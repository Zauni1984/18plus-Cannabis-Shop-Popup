<?php
/**
 * Catalog query + rendering.
 *
 * @package WCProfessionalCatalog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCPC_Catalog {

	/**
	 * Query products honoring filter args.
	 *
	 * @param array $args {
	 *     @type string $category Comma list of category slugs or IDs.
	 *     @type string $tag      Comma list of tag slugs or IDs.
	 *     @type string $brand    Comma list of brand slugs or IDs.
	 *     @type string $ids      Comma list of product IDs.
	 *     @type int    $limit    Optional max amount (-1 = all).
	 *     @type string $orderby  Sort field.
	 *     @type string $order    ASC | DESC.
	 * }
	 * @return WC_Product[]
	 */
	/**
	 * Default query args.
	 *
	 * @return array
	 */
	private static function query_defaults() {
		return array(
			'category' => '',
			'tag'      => '',
			'brand'    => '',
			'ids'      => '',
			'limit'    => -1,
			'orderby'  => 'menu_order',
			'order'    => 'ASC',
			'status'   => 'publish',
		);
	}

	/**
	 * Build the wc_get_products() argument array, EXCLUDING limit/page/return,
	 * so the caller can pick between a one-shot query and a batched query.
	 *
	 * @param array $args Filter args.
	 * @return array
	 */
	private static function build_query_args( $args ) {
		$query_args = array(
			'status'  => $args['status'],
			'orderby' => $args['orderby'],
			'order'   => $args['order'],
		);
		if ( ! empty( $args['ids'] ) ) {
			$query_args['include'] = wp_parse_id_list( $args['ids'] );
		}
		if ( ! empty( $args['category'] ) ) {
			$query_args['category'] = self::split_terms( $args['category'] );
		}
		if ( ! empty( $args['tag'] ) ) {
			$query_args['tag'] = self::split_terms( $args['tag'] );
		}
		if ( ! empty( $args['brand'] ) ) {
			$query_args['tax_query'] = self::build_brand_tax_query( $args['brand'] );
		}
		return $query_args;
	}

	/**
	 * Light-weight count of matching products. Uses wc_get_products with
	 * paginate=true + return=ids so no full WC_Product objects are loaded.
	 *
	 * @param array $args Filter args.
	 * @return int
	 */
	public static function count( $args = array() ) {
		$args       = wp_parse_args( $args, self::query_defaults() );
		$query_args = self::build_query_args( $args );
		$query_args['limit']    = 1;
		$query_args['page']     = 1;
		$query_args['return']   = 'ids';
		$query_args['paginate'] = true;

		$result = wc_get_products( $query_args );
		return ( is_object( $result ) && isset( $result->total ) ) ? (int) $result->total : 0;
	}

	/**
	 * Fetch a single batch of products. Used by the batched render loop so
	 * peak memory stays bounded by $per_page, not by the total catalog size.
	 *
	 * @param array $args     Filter args.
	 * @param int   $page     1-based page index.
	 * @param int   $per_page Products per batch.
	 * @return WC_Product[]
	 */
	public static function batch( $args = array(), $page = 1, $per_page = 30 ) {
		$args       = wp_parse_args( $args, self::query_defaults() );
		$query_args = self::build_query_args( $args );
		$query_args['limit']  = max( 1, (int) $per_page );
		$query_args['page']   = max( 1, (int) $page );
		$query_args['return'] = 'objects';

		$products = wc_get_products( $query_args );
		return is_array( $products ) ? $products : array();
	}

	/**
	 * Build the ordered "section plan" for the catalog: a flat list of sections
	 * (one per leaf product category), each with the product IDs already sorted
	 * by Brand A-Z, then Product Name A-Z.
	 *
	 * Order at the top level: main categories A-Z. Inside a main category:
	 *  1. The main category's "own" products (those NOT in any subcategory).
	 *  2. Each subcategory A-Z, with its products.
	 *
	 * Returns an array of:
	 *   [ 'main' => string, 'sub' => string, 'cat_id' => int, 'cat_slug' => string,
	 *     'ids' => int[] ]
	 *
	 * @return array
	 */
	public static function collect_sections() {
		$sections = array();

		$default_cat_id = (int) get_option( 'default_product_cat' );
		$main_cats      = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'parent'     => 0,
				'orderby'    => 'name',
				'order'      => 'ASC',
				'hide_empty' => true,
				'exclude'    => $default_cat_id ? array( $default_cat_id ) : array(),
			)
		);

		if ( is_wp_error( $main_cats ) || ! is_array( $main_cats ) ) {
			return array();
		}

		foreach ( $main_cats as $main ) {
			$subs = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'parent'     => (int) $main->term_id,
					'orderby'    => 'name',
					'order'      => 'ASC',
					'hide_empty' => true,
				)
			);
			if ( is_wp_error( $subs ) ) {
				$subs = array();
			}

			// (1) Main-category-only products (skip those that also live in a subcat).
			$direct_ids = self::products_in_category_ordered( (int) $main->term_id, ! empty( $subs ) );
			if ( $direct_ids ) {
				$sections[] = array(
					'main'     => (string) $main->name,
					'sub'      => '',
					'cat_id'   => (int) $main->term_id,
					'cat_slug' => (string) $main->slug,
					'ids'      => $direct_ids,
				);
			}

			// (2) Each subcategory's products.
			foreach ( $subs as $sub ) {
				$sub_ids = self::products_in_category_ordered( (int) $sub->term_id, false );
				if ( $sub_ids ) {
					$sections[] = array(
						'main'     => (string) $main->name,
						'sub'      => (string) $sub->name,
						'cat_id'   => (int) $sub->term_id,
						'cat_slug' => (string) $sub->slug,
						'ids'      => $sub_ids,
					);
				}
			}
		}

		return $sections;
	}

	/**
	 * Return the product IDs in a category, sorted by Brand A-Z then Name A-Z.
	 *
	 * One SQL hop per category — joins to the brand taxonomy when available
	 * (product_brand / pwb-brand / pa_brand). Products without a brand sort
	 * to the top of the alphabet ('').
	 *
	 * @param int  $cat_id          Term ID of the product category.
	 * @param bool $exclude_subcats When true, products that are also in any
	 *                              child of $cat_id are excluded (used so a
	 *                              product never appears twice — once under
	 *                              its main category and once under its
	 *                              subcategory).
	 * @return int[]
	 */
	private static function products_in_category_ordered( $cat_id, $exclude_subcats ) {
		global $wpdb;

		$brand_tax = '';
		foreach ( array( 'product_brand', 'pwb-brand', 'pa_brand' ) as $t ) {
			if ( taxonomy_exists( $t ) ) {
				$brand_tax = $t;
				break;
			}
		}

		$exclude_sql = '';
		if ( $exclude_subcats ) {
			$sub_ids = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'parent'     => (int) $cat_id,
					'fields'     => 'ids',
					'hide_empty' => false,
				)
			);
			if ( ! is_wp_error( $sub_ids ) && is_array( $sub_ids ) && ! empty( $sub_ids ) ) {
				$sub_ids      = array_map( 'intval', $sub_ids );
				$placeholders = implode( ',', array_fill( 0, count( $sub_ids ), '%d' ) );
				// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
				$exclude_sql = $wpdb->prepare(
					"AND p.ID NOT IN (
						SELECT tr2.object_id FROM {$wpdb->term_relationships} tr2
						INNER JOIN {$wpdb->term_taxonomy} tt2 ON tt2.term_taxonomy_id = tr2.term_taxonomy_id
						WHERE tt2.taxonomy = 'product_cat' AND tt2.term_id IN ( $placeholders )
					)",
					$sub_ids
				);
			}
		}

		if ( $brand_tax ) {
			$sql = $wpdb->prepare(
				"SELECT p.ID, MIN(COALESCE(bt.name, '')) AS brand_name
				FROM {$wpdb->posts} p
				INNER JOIN {$wpdb->term_relationships} tr ON tr.object_id = p.ID
				INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
				LEFT JOIN {$wpdb->term_relationships} btr ON btr.object_id = p.ID
				LEFT JOIN {$wpdb->term_taxonomy} btt ON btt.term_taxonomy_id = btr.term_taxonomy_id AND btt.taxonomy = %s
				LEFT JOIN {$wpdb->terms} bt ON bt.term_id = btt.term_id
				WHERE p.post_type = 'product'
				AND p.post_status = 'publish'
				AND tt.taxonomy = 'product_cat'
				AND tt.term_id = %d
				$exclude_sql
				GROUP BY p.ID, p.post_title
				ORDER BY brand_name ASC, p.post_title ASC",
				$brand_tax,
				(int) $cat_id
			);
		} else {
			$sql = $wpdb->prepare(
				"SELECT p.ID
				FROM {$wpdb->posts} p
				INNER JOIN {$wpdb->term_relationships} tr ON tr.object_id = p.ID
				INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
				WHERE p.post_type = 'product'
				AND p.post_status = 'publish'
				AND tt.taxonomy = 'product_cat'
				AND tt.term_id = %d
				$exclude_sql
				GROUP BY p.ID, p.post_title
				ORDER BY p.post_title ASC",
				(int) $cat_id
			);
		}

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery
		$rows = $wpdb->get_results( $sql );
		if ( ! is_array( $rows ) || empty( $rows ) ) {
			return array();
		}

		$ids = array();
		foreach ( $rows as $r ) {
			$ids[] = (int) $r->ID;
		}
		return $ids;
	}

	/**
	 * Fetch WC_Product objects for a fixed-order ID slice. Used by the
	 * section-batched renderer so the brand/name ordering is preserved.
	 *
	 * @param int[] $ids    Ordered list of IDs.
	 * @param int   $offset Starting offset.
	 * @param int   $limit  Max number to fetch.
	 * @return WC_Product[]
	 */
	public static function batch_by_ids( $ids, $offset = 0, $limit = 30 ) {
		$slice = array_slice( $ids, (int) $offset, (int) $limit );
		if ( empty( $slice ) ) {
			return array();
		}
		$products = wc_get_products(
			array(
				'include' => $slice,
				'orderby' => 'include',
				'limit'   => count( $slice ),
				'return'  => 'objects',
			)
		);
		if ( ! is_array( $products ) ) {
			return array();
		}
		// wc_get_products with orderby=include usually preserves order, but some
		// hosts have seen it lost in custom data stores - re-index defensively.
		$by_id = array();
		foreach ( $products as $p ) {
			if ( $p instanceof WC_Product ) {
				$by_id[ (int) $p->get_id() ] = $p;
			}
		}
		$ordered = array();
		foreach ( $slice as $id ) {
			if ( isset( $by_id[ (int) $id ] ) ) {
				$ordered[] = $by_id[ (int) $id ];
			}
		}
		return $ordered;
	}

	/**
	 * Free per-batch caches between iterations to keep memory bounded.
	 */
	public static function free_batch_memory() {
		if ( function_exists( 'gc_collect_cycles' ) ) {
			gc_collect_cycles();
		}
		// wp_cache_flush_runtime_cache() can be aggressive with some object cache
		// drop-ins (Redis, Memcached), so we only call it on the default cache
		// to stay on the safe side.
		if ( function_exists( 'wp_cache_flush_runtime_cache' ) && ! wp_using_ext_object_cache() ) {
			wp_cache_flush_runtime_cache();
		}
	}

	/**
	 * Resolve php.ini's memory_limit as bytes (-1/empty -> 0 meaning "unlimited").
	 *
	 * @return int
	 */
	public static function memory_limit_bytes() {
		$raw = ini_get( 'memory_limit' );
		if ( false === $raw || '' === $raw || '-1' === (string) $raw ) {
			return 0;
		}
		$raw = trim( (string) $raw );
		$num = (int) $raw;
		$unit = strtoupper( substr( $raw, -1 ) );
		switch ( $unit ) {
			case 'G':
				return $num * 1024 * 1024 * 1024;
			case 'M':
				return $num * 1024 * 1024;
			case 'K':
				return $num * 1024;
		}
		return $num;
	}

	/**
	 * Has the current request consumed more memory than is safe to keep rendering?
	 *
	 * @return bool
	 */
	public static function memory_pressure() {
		$limit = self::memory_limit_bytes();
		if ( 0 === $limit ) {
			return false;
		}
		return memory_get_usage( true ) > (int) ( $limit * 0.75 );
	}

	/**
	 * Legacy one-shot query - still used by the grid template for small
	 * catalogs and by direct shortcode callers that pass an explicit limit.
	 * Falls back to a batched fetch when the result would exceed the
	 * per-batch threshold, to avoid loading thousands of objects at once.
	 *
	 * @param array $args See query_defaults().
	 * @return WC_Product[]
	 */
	public static function query( $args = array() ) {
		$args = wp_parse_args( $args, self::query_defaults() );

		$requested_limit = (int) $args['limit'];
		$max_setting     = (int) WCPC_Plugin::get_setting( 'max_products', 0 );
		// max_products = 0 means "no cap" - we still page internally so memory
		// never blows up regardless of catalog size.
		$cap = $max_setting > 0 ? $max_setting : PHP_INT_MAX;
		if ( $requested_limit <= 0 || $requested_limit > $cap ) {
			$requested_limit = $cap;
		}

		$batch_size = (int) apply_filters( 'wcpc_batch_size', 30 );
		$out        = array();
		$page       = 1;
		while ( count( $out ) < $requested_limit ) {
			$remaining = $requested_limit - count( $out );
			$take      = min( $batch_size, $remaining );
			$batch     = self::batch( $args, $page, $take );
			if ( empty( $batch ) ) {
				break;
			}
			foreach ( $batch as $p ) {
				$out[] = $p;
			}
			$page++;
			if ( $page % 3 === 0 ) {
				self::free_batch_memory();
			}
		}
		return $out;
	}

	/**
	 * Render catalog grid HTML (used by shortcode + frontend).
	 *
	 * @param array $args Same as query() + render options.
	 * @return string
	 */
	public static function render_grid( $args = array() ) {
		$products = self::query( $args );
		$settings = WCPC_Plugin::get_settings();
		$columns  = isset( $args['columns'] ) && (int) $args['columns'] > 0
			? (int) $args['columns']
			: (int) $settings['columns'];

		ob_start();
		include WCPC_PLUGIN_DIR . 'templates/catalog-grid.php';
		return ob_get_clean();
	}

	/**
	 * Render flipbook HTML (online catalog with page turn).
	 *
	 * @param array $args Render args.
	 * @return string
	 */
	public static function render_flipbook( $args = array() ) {
		$settings = WCPC_Plugin::get_settings();
		$per_page = isset( $args['per_page'] ) && (int) $args['per_page'] > 0
			? (int) $args['per_page']
			: (int) $settings['products_per_page'];

		ob_start();
		include WCPC_PLUGIN_DIR . 'templates/catalog-flipbook.php';
		return ob_get_clean();
	}

	/**
	 * Render a standalone flipbook page (own HTML doc) for the endpoint.
	 *
	 * @param array $args Query args.
	 */
	public static function render_flipbook_page( $args = array() ) {
		// Force a 200 OK + no caching so the theme does not treat this as a 404
		// page (rewrite resolves to no real post, which would otherwise be 404).
		status_header( 200 );
		nocache_headers();
		global $wp_query;
		if ( $wp_query ) {
			$wp_query->is_404 = false;
		}

		self::install_fatal_logger( 'flipbook' );

		add_action( 'wp_enqueue_scripts', array( 'WCPC_Assets', 'enqueue_frontend' ), 20 );

		try {
			$content = self::render_flipbook( $args );
		} catch ( \Throwable $e ) {
			self::log_throwable( 'flipbook', $e );
			$content = self::error_html( $e );
		}

		get_header();
		echo '<div class="wcpc-endpoint-wrap">';
		echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- pre-escaped template / error_html is self-escaping.
		echo '</div>';
		get_footer();
	}

	/**
	 * Catch PHP fatals (memory exhausted, etc.) that bubble past WP's handler
	 * and write a tagged line to debug.log so the cause is discoverable. The
	 * cost is one register_shutdown_function per request - cheap.
	 *
	 * @param string $context Surface tag (flipbook / pdf / print).
	 */
	public static function install_fatal_logger( $context ) {
		static $installed = array();
		if ( isset( $installed[ $context ] ) ) {
			return;
		}
		$installed[ $context ] = true;

		register_shutdown_function(
			function () use ( $context ) {
				$e = error_get_last();
				if ( ! $e ) {
					return;
				}
				$fatal_types = array( E_ERROR, E_PARSE, E_COMPILE_ERROR, E_CORE_ERROR, E_RECOVERABLE_ERROR );
				if ( ! in_array( (int) $e['type'], $fatal_types, true ) ) {
					return;
				}
				error_log(
					sprintf(
						'[WCPC %s] FATAL: %s in %s:%d (peak %s MB / limit %s)',
						$context,
						$e['message'],
						$e['file'],
						$e['line'],
						round( memory_get_peak_usage( true ) / 1024 / 1024, 1 ),
						ini_get( 'memory_limit' )
					)
				);
			}
		);
	}

	/**
	 * Log a Throwable with the WCPC tag.
	 *
	 * @param string     $context Surface tag.
	 * @param \Throwable $e       Throwable to log.
	 */
	public static function log_throwable( $context, \Throwable $e ) {
		error_log(
			sprintf(
				"[WCPC %s] %s: %s in %s:%d\n%s",
				$context,
				get_class( $e ),
				$e->getMessage(),
				$e->getFile(),
				$e->getLine(),
				$e->getTraceAsString()
			)
		);
	}

	/**
	 * Friendly fallback HTML when rendering throws. Shows the exception details
	 * only when WP_DEBUG is on.
	 *
	 * @param \Throwable $e Throwable.
	 * @return string
	 */
	public static function error_html( \Throwable $e ) {
		$debug = ( defined( 'WP_DEBUG' ) && WP_DEBUG );
		ob_start();
		?>
		<div class="wcpc-error" style="padding:24px;background:#fff3f3;border:1px solid #f0baba;border-radius:8px;color:#7d1f1f;max-width:760px;margin:30px auto;">
			<h2 style="margin-top:0;color:#7d1f1f;"><?php esc_html_e( 'Der Katalog konnte gerade nicht geladen werden.', 'wc-professional-catalog' ); ?></h2>
			<p><?php esc_html_e( 'Bitte versuche es in ein paar Sekunden noch einmal. Der Administrator wurde benachrichtigt.', 'wc-professional-catalog' ); ?></p>
			<?php if ( $debug ) : ?>
				<pre style="white-space:pre-wrap;font-size:12px;background:#fff;padding:12px;border:1px solid #ddd;color:#333;"><?php echo esc_html( get_class( $e ) . ': ' . $e->getMessage() . "\n" . $e->getTraceAsString() ); ?></pre>
			<?php endif; ?>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Render the print-ready catalog as its own minimal HTML page.
	 *
	 * @param array $args Query args.
	 */
	public static function render_print_page( $args = array() ) {
		$settings = WCPC_Plugin::get_settings();
		$columns  = isset( $args['columns'] ) && (int) $args['columns'] > 0
			? (int) $args['columns']
			: max( 1, (int) ( $settings['pdf_columns'] ?? 3 ) );

		status_header( 200 );
		nocache_headers();
		header( 'Content-Type: text/html; charset=' . get_bloginfo( 'charset' ) );

		self::install_fatal_logger( 'print' );

		try {
			include WCPC_PLUGIN_DIR . 'templates/catalog-print.php';
		} catch ( \Throwable $e ) {
			self::log_throwable( 'print', $e );
			echo self::error_html( $e ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- self-escaping.
		}
	}

	/**
	 * Helper - split a CSV term list, accept IDs or slugs.
	 *
	 * @param string $value CSV input.
	 * @return array
	 */
	private static function split_terms( $value ) {
		$items = array_filter( array_map( 'trim', explode( ',', (string) $value ) ) );
		$out   = array();
		foreach ( $items as $item ) {
			$out[] = is_numeric( $item ) ? (int) $item : sanitize_title( $item );
		}
		return $out;
	}

	/**
	 * Build a tax_query for brand taxonomies. We try the most common brand
	 * taxonomies in order until we find one that exists.
	 *
	 * @param string $value CSV brand IDs/slugs.
	 * @return array
	 */
	private static function build_brand_tax_query( $value ) {
		$brands     = self::split_terms( $value );
		$candidates = array(
			'product_brand',   // YITH / Perfect Brands.
			'pwb-brand',       // Perfect WooCommerce Brands legacy.
			'pa_brand',        // Custom product attribute.
		);

		$tax = '';
		foreach ( $candidates as $c ) {
			if ( taxonomy_exists( $c ) ) {
				$tax = $c;
				break;
			}
		}

		if ( ! $tax ) {
			return array();
		}

		$field = '';
		foreach ( $brands as $b ) {
			if ( is_string( $b ) ) {
				$field = 'slug';
				break;
			}
		}
		if ( ! $field ) {
			$field = 'term_id';
		}

		return array(
			array(
				'taxonomy' => $tax,
				'field'    => $field,
				'terms'    => $brands,
				'operator' => 'IN',
			),
		);
	}

	/**
	 * Resolve a brand label for a product (first matching taxonomy term).
	 *
	 * @param WC_Product $product Product.
	 * @return string
	 */
	public static function get_brand_label( $product ) {
		$candidates = array( 'product_brand', 'pwb-brand', 'pa_brand' );
		foreach ( $candidates as $tax ) {
			if ( ! taxonomy_exists( $tax ) ) {
				continue;
			}
			$terms = get_the_terms( $product->get_id(), $tax );
			if ( is_array( $terms ) && ! empty( $terms ) ) {
				return (string) $terms[0]->name;
			}
		}
		return '';
	}

	/**
	 * Build inline CSS variables based on settings, for live theming.
	 *
	 * @param array $settings Plugin settings.
	 * @return string
	 */
	public static function build_css_variables( $settings ) {
		$vars = array(
			'--wcpc-color-primary'   => self::safe_color( $settings['color_primary'] ),
			'--wcpc-color-secondary' => self::safe_color( $settings['color_secondary'] ),
			'--wcpc-color-accent'    => self::safe_color( $settings['color_accent'] ),
			'--wcpc-color-bg'        => self::safe_color( $settings['color_bg'] ),
			'--wcpc-color-text'      => self::safe_color( $settings['color_text'] ),
			'--wcpc-color-muted'     => self::safe_color( $settings['color_muted'] ),
			'--wcpc-price-color'     => self::safe_color( $settings['price_color'] ),

			'--wcpc-font-title'      => self::safe_css_word( $settings['font_family_title'] ),
			'--wcpc-size-title'      => (int) $settings['font_size_title'] . 'px',
			'--wcpc-weight-title'    => self::safe_css_word( $settings['font_weight_title'] ),
			'--wcpc-style-title'     => self::safe_css_word( $settings['font_style_title'] ),

			'--wcpc-font-body'       => self::safe_css_word( $settings['font_family_body'] ),
			'--wcpc-size-body'       => (int) $settings['font_size_body'] . 'px',
			'--wcpc-weight-body'     => self::safe_css_word( $settings['font_weight_body'] ),
			'--wcpc-style-body'      => self::safe_css_word( $settings['font_style_body'] ),

			'--wcpc-font-price'      => self::safe_css_word( $settings['font_family_price'] ),
			'--wcpc-size-price'      => (int) $settings['font_size_price'] . 'px',
			'--wcpc-weight-price'    => self::safe_css_word( $settings['font_weight_price'] ),
			'--wcpc-style-price'     => self::safe_css_word( $settings['font_style_price'] ),
		);

		$css = ':root, .wcpc-catalog, .wcpc-pdf{';
		foreach ( $vars as $k => $v ) {
			$css .= $k . ':' . $v . ';';
		}
		$css .= '}';
		return $css;
	}

	/**
	 * Restrict a value to characters legal in a hex color (defense-in-depth on top of
	 * the admin sanitizer in WCPC_Admin::sanitize_color()).
	 *
	 * @param string $val Stored color value.
	 * @return string
	 */
	private static function safe_color( $val ) {
		return preg_match( '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', (string) $val ) ? $val : '#000000';
	}

	/**
	 * Strip a value down to characters that are legal in a CSS identifier / font name.
	 * Used only as a fallback - the admin form ships these via <select> with a
	 * whitelist - but we never want stored text to leak control characters into the
	 * stylesheet (no semicolons, braces, angle brackets, quotes).
	 *
	 * @param string $val Stored value.
	 * @return string
	 */
	private static function safe_css_word( $val ) {
		$clean = preg_replace( '/[^A-Za-z0-9 _\-]/', '', (string) $val );
		return '' === $clean ? 'Helvetica' : $clean;
	}
}
