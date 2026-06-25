<?php
/**
 * Background PDF generation job.
 *
 * A job is a long-running PDF render split into many small batches:
 *   1. start()    - resolves the product ID list for the scope, persists job state
 *   2. tick()     - renders ONE small batch of cards into a temp HTML file, then
 *                   updates progress; called repeatedly via AJAX (and optionally
 *                   re-fired via Action Scheduler so it survives a closed browser)
 *   3. finalize() - wraps the temp HTML with the PDF doc head + cover + footer,
 *                   runs Dompdf, saves the final PDF into wp-content/uploads
 *
 * Each tick stays well under the PHP memory_limit because only $batch_size
 * WC_Product objects are loaded at a time. The PDF generator is therefore
 * memory-bounded by batch_size, not by total product count.
 *
 * Concurrency: at most one job is "active" per record. Multiple records can
 * exist (= multiple finished PDFs in the uploads folder).
 *
 * @package WCProfessionalCatalog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCPC_PDF_Job {

	const OPT_PREFIX   = 'wcpc_pdf_job_';
	const INDEX_OPT    = 'wcpc_pdf_jobs';
	const DIR_FINAL    = 'wcpc-catalogs';
	const DIR_TEMP     = 'wcpc-tmp';
	const CRON_ACTION  = 'wcpc_pdf_process_job';
	const JOB_TTL      = 7 * DAY_IN_SECONDS;

	/**
	 * Register the action scheduler hook so jobs can keep advancing without
	 * the admin tab being open.
	 */
	public static function init() {
		add_action( self::CRON_ACTION, array( __CLASS__, 'cron_tick' ), 10, 1 );
	}

	/**
	 * Create a new job for the given scope. Resolves the ordered product ID
	 * list up front so progress reporting can show "X / Y".
	 *
	 * @param array $scope { type:'all'|'main_cat'|'sub_cat'|'brand', value:string|int, label:string }
	 * @return array Job record.
	 */
	public static function create( $scope ) {
		$id       = wp_generate_uuid4();
		$sections = self::resolve_sections( $scope );
		$total    = 0;
		foreach ( $sections as $s ) {
			$total += count( $s['ids'] );
		}
		$now = time();

		$record = array(
			'id'                       => $id,
			'scope'                    => $scope,
			'sections'                 => $sections,
			'total_products'           => $total,
			'processed'                => 0,
			'page_index'               => 0,
			'current_section_index'    => 0,
			'current_section_offset'   => 0,
			'status'                   => 'queued',
			'current_section'          => '',
			'error'                    => '',
			'file_url'                 => '',
			'file_path'                => '',
			'file_size'                => 0,
			'created'                  => $now,
			'updated'                  => $now,
			'finalized'                => 0,
		);

		self::save( $record );
		self::index_add( $id );

		// Schedule a cron tick as a safety net (browser may close).
		if ( function_exists( 'as_schedule_single_action' ) ) {
			as_schedule_single_action( time() + 5, self::CRON_ACTION, array( $id ), 'wcpc-pdf' );
		} elseif ( function_exists( 'wp_schedule_single_event' ) ) {
			wp_schedule_single_event( time() + 5, self::CRON_ACTION, array( $id ) );
		}

		return $record;
	}

	/**
	 * Process the next batch of this job. Safe to call multiple times - the
	 * 'processed' counter advances atomically.
	 *
	 * @param string $id Job ID.
	 * @return array Updated job record.
	 */
	public static function tick( $id ) {
		$record = self::get( $id );
		if ( ! $record ) {
			return array( 'status' => 'error', 'error' => 'Job not found' );
		}
		if ( in_array( $record['status'], array( 'done', 'error', 'cancelled' ), true ) ) {
			return $record;
		}

		$record['status']  = 'processing';
		$record['updated'] = time();

		$batch_size = (int) apply_filters( 'wcpc_pdf_job_batch_size', 15 );

		try {
			$sections = isset( $record['sections'] ) && is_array( $record['sections'] ) ? $record['sections'] : array();
			$sec_idx  = (int) $record['current_section_index'];
			$sec_off  = (int) $record['current_section_offset'];

			$consumed = 0;
			while ( $consumed < $batch_size && $sec_idx < count( $sections ) ) {
				$section   = $sections[ $sec_idx ];
				$sec_size  = count( $section['ids'] );
				$remaining = $sec_size - $sec_off;
				if ( $remaining <= 0 ) {
					$sec_idx++;
					$sec_off = 0;
					continue;
				}
				$take = min( $batch_size - $consumed, $remaining );

				$products = WCPC_Catalog::batch_by_ids( $section['ids'], $sec_off, $take );
				if ( ! empty( $products ) ) {
					$is_first_page_of_section = ( 0 === $sec_off );
					$html = self::render_section_batch_html( $products, $section, $record, $is_first_page_of_section );
					self::append_temp( $record['id'], $html );

					$consumed             += count( $products );
					$record['processed']  += count( $products );
					$sec_off              += count( $products );
				} else {
					// Empty batch from a section - skip the section to stay live.
					$sec_idx++;
					$sec_off = 0;
				}

				$record['current_section'] = $section['main'] . ( '' !== $section['sub'] ? ' / ' . $section['sub'] : '' );

				if ( $sec_off >= $sec_size ) {
					$sec_idx++;
					$sec_off = 0;
				}

				unset( $products );
			}

			$record['current_section_index']  = $sec_idx;
			$record['current_section_offset'] = $sec_off;

			WCPC_Catalog::free_batch_memory();

			if ( $record['processed'] >= (int) $record['total_products'] || $sec_idx >= count( $sections ) ) {
				self::save( $record );
				return self::finalize( $id );
			}
		} catch ( \Throwable $e ) {
			$record['status'] = 'error';
			$record['error']  = $e->getMessage();
			WCPC_Catalog::log_throwable( 'pdf-job', $e );
		}

		self::save( $record );

		// Re-schedule the next cron tick if processing.
		if ( 'processing' === $record['status'] && function_exists( 'as_schedule_single_action' ) ) {
			if ( ! as_next_scheduled_action( self::CRON_ACTION, array( $id ), 'wcpc-pdf' ) ) {
				as_schedule_single_action( time() + 5, self::CRON_ACTION, array( $id ), 'wcpc-pdf' );
			}
		}

		return $record;
	}

	/**
	 * Cron entry point - same as tick().
	 *
	 * @param string $id Job id.
	 */
	public static function cron_tick( $id ) {
		self::tick( $id );
	}

	/**
	 * Wrap the temp body with the PDF doc head + cover + foot, run Dompdf,
	 * save the PDF, mark the job done.
	 *
	 * @param string $id Job ID.
	 * @return array Updated job record.
	 */
	public static function finalize( $id ) {
		$record = self::get( $id );
		if ( ! $record ) {
			return array( 'status' => 'error', 'error' => 'Job not found' );
		}
		if ( $record['finalized'] ) {
			return $record;
		}

		$record['status']  = 'finalizing';
		$record['updated'] = time();
		self::save( $record );

		$temp_path = self::temp_path( $id );
		if ( ! file_exists( $temp_path ) ) {
			$record['status'] = 'error';
			$record['error']  = 'Temp file missing';
			self::save( $record );
			return $record;
		}

		try {
			$settings = WCPC_Plugin::get_settings();
			$head     = self::render_doc_head( $settings );
			$cover    = self::render_cover( $settings );
			$body     = (string) file_get_contents( $temp_path );
			$foot     = self::render_doc_foot( $settings );
			$html     = $head . $cover . $body . $foot;
			unset( $body );

			$pdf_path = self::final_pdf_path( $id, $record['scope'] );
			wp_mkdir_p( dirname( $pdf_path ) );

			// Give the final Dompdf step plenty of headroom - it's the only
			// memory-heavy step now that batches are streamed.
			$current_limit = ini_get( 'memory_limit' );
			if ( WCPC_Catalog::memory_limit_bytes() < 512 * 1024 * 1024 ) {
				@ini_set( 'memory_limit', '512M' );
			}
			@set_time_limit( 300 );

			if ( ! WCPC_PDF::is_dompdf_available() ) {
				throw new \RuntimeException( 'Dompdf nicht verfügbar' );
			}

			$dompdf = new \Dompdf\Dompdf(
				array(
					'isHtml5ParserEnabled' => true,
					'isRemoteEnabled'      => true,
					'defaultFont'          => $settings['font_family_body'],
					'chroot'               => array( WP_CONTENT_DIR, ABSPATH ),
				)
			);
			$dompdf->loadHtml( $html, 'UTF-8' );
			unset( $html );

			$paper = in_array( $settings['pdf_page_size'], array( 'A4', 'A5', 'Letter' ), true )
				? $settings['pdf_page_size']
				: 'A4';
			$dompdf->setPaper( $paper, ( 'landscape' === $settings['pdf_orientation'] ) ? 'landscape' : 'portrait' );
			$dompdf->render();
			file_put_contents( $pdf_path, $dompdf->output() );
			unset( $dompdf );

			@ini_set( 'memory_limit', $current_limit );

			$record['file_path'] = $pdf_path;
			$record['file_url']  = self::pdf_url( $pdf_path );
			$record['file_size'] = (int) filesize( $pdf_path );
			$record['status']    = 'done';
			$record['finalized'] = 1;
			$record['error']     = '';

			@unlink( $temp_path );
			@rmdir( dirname( $temp_path ) );
		} catch ( \Throwable $e ) {
			$record['status'] = 'error';
			$record['error']  = $e->getMessage();
			WCPC_Catalog::log_throwable( 'pdf-finalize', $e );
		}

		self::save( $record );
		return $record;
	}

	public static function cancel( $id ) {
		$record = self::get( $id );
		if ( ! $record ) {
			return false;
		}
		$record['status']  = 'cancelled';
		$record['updated'] = time();
		self::save( $record );

		$temp_path = self::temp_path( $id );
		@unlink( $temp_path );
		@rmdir( dirname( $temp_path ) );
		return true;
	}

	public static function delete( $id ) {
		$record = self::get( $id );
		if ( ! $record ) {
			return false;
		}
		if ( ! empty( $record['file_path'] ) && file_exists( $record['file_path'] ) ) {
			@unlink( $record['file_path'] );
		}
		$temp_path = self::temp_path( $id );
		@unlink( $temp_path );
		@rmdir( dirname( $temp_path ) );
		delete_option( self::OPT_PREFIX . $id );
		self::index_remove( $id );
		return true;
	}

	public static function get( $id ) {
		if ( ! self::valid_id( $id ) ) {
			return null;
		}
		$rec = get_option( self::OPT_PREFIX . $id, null );
		return is_array( $rec ) ? $rec : null;
	}

	public static function save( $record ) {
		if ( ! is_array( $record ) || empty( $record['id'] ) ) {
			return;
		}
		update_option( self::OPT_PREFIX . $record['id'], $record, false );
	}

	public static function all() {
		$ids = get_option( self::INDEX_OPT, array() );
		if ( ! is_array( $ids ) ) {
			$ids = array();
		}
		$out = array();
		foreach ( $ids as $id ) {
			$rec = self::get( $id );
			if ( $rec ) {
				$out[] = $rec;
			}
		}
		usort(
			$out,
			function ( $a, $b ) {
				return ( (int) $b['created'] ) <=> ( (int) $a['created'] );
			}
		);
		return $out;
	}

	private static function index_add( $id ) {
		$ids = get_option( self::INDEX_OPT, array() );
		if ( ! is_array( $ids ) ) {
			$ids = array();
		}
		if ( ! in_array( $id, $ids, true ) ) {
			array_unshift( $ids, $id );
		}
		update_option( self::INDEX_OPT, array_slice( $ids, 0, 50 ), false );
	}

	private static function index_remove( $id ) {
		$ids = get_option( self::INDEX_OPT, array() );
		if ( ! is_array( $ids ) ) {
			return;
		}
		$ids = array_values( array_diff( $ids, array( $id ) ) );
		update_option( self::INDEX_OPT, $ids, false );
	}

	/**
	 * Resolve the ordered sections for a scope. Reuses
	 * WCPC_Catalog::collect_sections() (Hauptkat -> Subkat -> Brand -> Name).
	 *
	 * @param array $scope
	 * @return array
	 */
	private static function resolve_sections( $scope ) {
		$type  = isset( $scope['type'] ) ? (string) $scope['type'] : 'all';
		$value = isset( $scope['value'] ) ? $scope['value'] : '';

		$all = WCPC_Catalog::collect_sections();
		if ( 'all' === $type ) {
			return $all;
		}

		$out = array();
		foreach ( $all as $section ) {
			if ( 'main_cat' === $type ) {
				if ( (string) $section['main'] === (string) $value || (int) $section['cat_id'] === (int) $value ) {
					$out[] = $section;
				}
			} elseif ( 'sub_cat' === $type ) {
				if ( (int) $section['cat_id'] === (int) $value ) {
					$out[] = $section;
				}
			}
		}
		return $out;
	}

	/**
	 * Render the PDF body HTML for a single batch within a section. Emits a
	 * full section banner on the first batch of the section, then continues
	 * with regular product pages.
	 *
	 * @param WC_Product[] $products
	 * @param array        $section
	 * @param array        $record
	 * @param bool         $first_of_section
	 * @return string
	 */
	private static function render_section_batch_html( $products, $section, &$record, $first_of_section ) {
		$settings     = WCPC_Plugin::get_settings();
		$columns      = max( 1, (int) ( $settings['pdf_columns'] ?? 3 ) );
		$per_pdf_page = max( 1, (int) ( $settings['pdf_per_page'] ?? 9 ) );
		$logo_url     = ! empty( $settings['logo_id'] ) ? (string) wp_get_attachment_image_url( (int) $settings['logo_id'], 'large' ) : '';
		$pdf_img_size = $settings['image_size_pdf'] ?? 'medium';
		$total_pages  = (int) ceil( (int) $record['total_products'] / $per_pdf_page );

		ob_start();
		$chunks = array_chunk( $products, $per_pdf_page );
		foreach ( $chunks as $i => $page_products ) {
			$record['page_index'] = (int) $record['page_index'] + 1;
			$page_index   = (int) $record['page_index'];
			$section_main = (string) $section['main'];
			$section_sub  = (string) $section['sub'];
			$show_banner  = ( $first_of_section && 0 === $i );
			include WCPC_PLUGIN_DIR . 'templates/parts/pdf-page.php';
		}
		return (string) ob_get_clean();
	}

	private static function render_doc_head( $settings ) {
		$columns        = max( 1, (int) ( $settings['pdf_columns'] ?? 3 ) );
		$dense          = ( $columns >= 3 );
		$title_size_pdf = $dense ? max( 9, min( 13, (int) $settings['font_size_title'] - 4 ) ) : (int) $settings['font_size_title'];
		$body_size_pdf  = $dense ? max( 8, min( 10, (int) $settings['font_size_body'] ) ) : (int) $settings['font_size_body'];
		$price_size_pdf = $dense ? max( 10, min( 13, (int) $settings['font_size_price'] - 1 ) ) : (int) $settings['font_size_price'];
		$img_max_h      = $dense ? 70 : 110;
		$cell_pad       = $dense ? 5 : 8;
		$cell_gap       = $dense ? 4 : 6;
		$margin         = max( 0, (int) ( $settings['pdf_margin'] ?? 12 ) );
		$page_size      = strtoupper( (string) ( $settings['pdf_page_size'] ?? 'A4' ) );

		ob_start();
		?>
<!doctype html><html><head><meta charset="UTF-8"><title><?php echo esc_html( $settings['cover_title'] ); ?></title>
<style>
@page { size: <?php echo esc_attr( $page_size ); ?> <?php echo esc_attr( $settings['pdf_orientation'] ); ?>; margin: <?php echo (int) $margin; ?>mm; }
* { box-sizing: border-box; }
body { font-family: <?php echo esc_html( $settings['font_family_body'] ); ?>, Helvetica, Arial, sans-serif; font-size: <?php echo (int) $settings['font_size_body']; ?>px; color: <?php echo esc_html( $settings['color_text'] ); ?>; background: <?php echo esc_html( $settings['color_bg'] ); ?>; margin: 0; }
.wcpc-pdf-cover { text-align: center; padding: 60px 0; page-break-after: always; }
.wcpc-pdf-cover img { max-width: 240px; max-height: 140px; margin-bottom: 30px; }
.wcpc-pdf-cover h1 { font-family: <?php echo esc_html( $settings['font_family_title'] ); ?>; font-size: 42px; color: <?php echo esc_html( $settings['color_primary'] ); ?>; margin: 0 0 10px; }
.wcpc-pdf-cover .sub { color: <?php echo esc_html( $settings['color_muted'] ); ?>; font-size: 14px; }
.wcpc-pdf-cover .date { color: <?php echo esc_html( $settings['color_muted'] ); ?>; font-size: 12px; margin-top: 30px; }
.wcpc-pdf-page { page-break-after: always; }
.wcpc-pdf-page:last-child { page-break-after: auto; }
.wcpc-pdf-header { border-bottom: 2px solid <?php echo esc_html( $settings['color_primary'] ); ?>; padding-bottom: 6px; margin-bottom: 14px; display: table; width: 100%; }
.wcpc-pdf-header .l { display: table-cell; vertical-align: middle; }
.wcpc-pdf-header .r { display: table-cell; vertical-align: middle; text-align: right; color: <?php echo esc_html( $settings['color_muted'] ); ?>; font-size: 10px; }
.wcpc-pdf-header img { max-height: 28px; }
table.wcpc-pdf-grid { width: 100%; border-collapse: separate; border-spacing: <?php echo (int) $cell_gap; ?>px; }
table.wcpc-pdf-grid td { width: <?php echo (int) ( 100 / max( 1, $columns ) ); ?>%; vertical-align: top; background: #fff; border: 1px solid #e8e8e8; padding: <?php echo (int) $cell_pad; ?>px; }
.wcpc-pdf-item img { max-width: 100%; max-height: <?php echo (int) $img_max_h; ?>px; display: block; margin: 0 auto 4px; }
.wcpc-pdf-item .brand { color: <?php echo esc_html( $settings['color_accent'] ); ?>; font-size: 9px; letter-spacing: 1px; text-transform: uppercase; }
.wcpc-pdf-item .title { font-family: <?php echo esc_html( $settings['font_family_title'] ); ?>; font-size: <?php echo (int) $title_size_pdf; ?>px; font-weight: <?php echo esc_html( $settings['font_weight_title'] ); ?>; color: <?php echo esc_html( $settings['color_secondary'] ); ?>; margin: 2px 0 3px; line-height: 1.15; }
.wcpc-pdf-item .sku { color: <?php echo esc_html( $settings['color_muted'] ); ?>; font-size: 9px; margin-bottom: 3px; }
.wcpc-pdf-item .desc { font-size: <?php echo (int) $body_size_pdf; ?>px; line-height: 1.3; margin: 2px 0 4px; }
.wcpc-pdf-item .price { font-family: <?php echo esc_html( $settings['font_family_price'] ); ?>; font-size: <?php echo (int) $price_size_pdf; ?>px; font-weight: <?php echo esc_html( $settings['font_weight_price'] ); ?>; color: <?php echo esc_html( $settings['price_color'] ); ?>; line-height: 1.2; }
.wcpc-pdf-item .price small { color: <?php echo esc_html( $settings['color_muted'] ); ?>; font-weight: 400; font-size: 9px; }
.wcpc-pdf-item .unit { font-size: 9px; color: <?php echo esc_html( $settings['color_muted'] ); ?>; margin-top: 1px; }
.wcpc-pdf-item .buy { display: inline-block; margin-top: 4px; padding: 3px 6px; background: <?php echo esc_html( $settings['color_primary'] ); ?>; color: #fff; text-decoration: none; font-size: 9px; border-radius: 3px; }
.wcpc-pdf-item a.img-link, .wcpc-pdf-item .title a { text-decoration: none; color: inherit; }
.wcpc-pdf-section-banner { text-align: center; padding: 10px 0 14px; margin-bottom: 12px; border-bottom: 2px solid <?php echo esc_html( $settings['color_primary'] ); ?>; }
.wcpc-pdf-section-banner .eyebrow { font-size: 10px; letter-spacing: 2px; text-transform: uppercase; color: <?php echo esc_html( $settings['color_accent'] ); ?>; margin-bottom: 3px; }
.wcpc-pdf-section-banner .title { font-family: <?php echo esc_html( $settings['font_family_title'] ); ?>; font-size: 18px; color: <?php echo esc_html( $settings['color_secondary'] ); ?>; margin: 0; }
</style></head><body class="wcpc-pdf">
		<?php
		return (string) ob_get_clean();
	}

	private static function render_cover( $settings ) {
		$logo_url = ! empty( $settings['logo_id'] ) ? (string) wp_get_attachment_image_url( (int) $settings['logo_id'], 'large' ) : '';
		ob_start();
		?>
		<section class="wcpc-pdf-cover">
			<?php if ( $logo_url ) : ?><img src="<?php echo esc_url( $logo_url ); ?>" alt="" /><?php endif; ?>
			<h1><?php echo esc_html( $settings['cover_title'] ); ?></h1>
			<?php if ( ! empty( $settings['cover_subtitle'] ) ) : ?><p class="sub"><?php echo esc_html( $settings['cover_subtitle'] ); ?></p><?php endif; ?>
			<p class="date"><?php echo esc_html( date_i18n( get_option( 'date_format' ) ) ); ?></p>
		</section>
		<?php
		return (string) ob_get_clean();
	}

	private static function render_doc_foot( $settings ) {
		ob_start();
		if ( ! empty( $settings['footer_text'] ) ) :
			?>
			<div class="wcpc-pdf-footer" style="text-align:center;color:#777;font-size:9px;"><?php echo esc_html( $settings['footer_text'] ); ?></div>
			<?php
		endif;
		echo '</body></html>';
		return (string) ob_get_clean();
	}

	private static function append_temp( $id, $html ) {
		$path = self::temp_path( $id );
		wp_mkdir_p( dirname( $path ) );
		file_put_contents( $path, $html, FILE_APPEND );
	}

	private static function temp_path( $id ) {
		$up = wp_upload_dir();
		return trailingslashit( $up['basedir'] ) . self::DIR_TEMP . '/' . sanitize_file_name( $id ) . '/body.html';
	}

	private static function final_pdf_path( $id, $scope ) {
		$up    = wp_upload_dir();
		$slug  = isset( $scope['label'] ) ? sanitize_title( (string) $scope['label'] ) : 'gesamt';
		$short = substr( sanitize_file_name( $id ), 0, 8 );
		$name  = sprintf( 'katalog-%s-%s-%s.pdf', gmdate( 'Y-m-d' ), $slug ?: 'gesamt', $short );
		return trailingslashit( $up['basedir'] ) . self::DIR_FINAL . '/' . $name;
	}

	private static function pdf_url( $path ) {
		$up = wp_upload_dir();
		return str_replace( $up['basedir'], $up['baseurl'], $path );
	}

	private static function valid_id( $id ) {
		return is_string( $id ) && preg_match( '/^[a-f0-9-]{36}$/i', $id );
	}
}
