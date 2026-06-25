<?php
/**
 * Flipbook (online catalog) - rendered by shortcode and the /flipbook/ endpoint.
 *
 * Streams products in batches so memory stays bounded by the per-batch size
 * regardless of catalog size.
 *
 * @package WCProfessionalCatalog
 * @var array $args      Original filter args (carries over to batch()).
 * @var int   $per_page  Products per flipbook page.
 * @var int   $total     Total matching products (already counted).
 * @var array $settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! isset( $settings ) || ! is_array( $settings ) ) {
	$settings = WCPC_Plugin::get_settings();
}
if ( ! isset( $args ) || ! is_array( $args ) ) {
	$args = array();
}
if ( ! isset( $per_page ) || (int) $per_page <= 0 ) {
	$per_page = max( 1, (int) ( $settings['products_per_page'] ?? 12 ) );
}
if ( ! isset( $total ) ) {
	$total = WCPC_Catalog::count( $args );
}
$total       = (int) $total;
$total_pages = max( 1, (int) ceil( $total / max( 1, $per_page ) ) );

$logo_url  = ! empty( $settings['logo_id'] ) ? (string) wp_get_attachment_image_url( (int) $settings['logo_id'], 'medium' ) : '';
$cover_url = ! empty( $settings['cover_image_id'] ) ? (string) wp_get_attachment_image_url( (int) $settings['cover_image_id'], 'large' ) : '';

$batch_size = (int) apply_filters( 'wcpc_batch_size', max( $per_page * 2, 30 ) );
?>
<section class="wcpc-catalog wcpc-flipbook" aria-label="<?php esc_attr_e( 'Online-Katalog', 'wc-professional-catalog' ); ?>">
	<div class="wcpc-flipbook__toolbar">
		<button type="button" class="wcpc-btn wcpc-btn-ghost" data-wcpc-prev>&laquo; <?php esc_html_e( 'Zurück', 'wc-professional-catalog' ); ?></button>
		<span class="wcpc-flipbook__pageinfo">
			<span data-wcpc-current>1</span> / <span data-wcpc-total><?php echo (int) ( $total_pages + 1 ); ?></span>
		</span>
		<button type="button" class="wcpc-btn wcpc-btn-ghost" data-wcpc-next><?php esc_html_e( 'Weiter', 'wc-professional-catalog' ); ?> &raquo;</button>
	</div>

	<div class="wcpc-flipbook__stage">
		<div class="wcpc-flipbook__pages">

			<article class="wcpc-flipbook__page wcpc-flipbook__page--cover is-active" data-page="0">
				<div class="wcpc-cover<?php echo $cover_url ? ' wcpc-cover--has-image' : ''; ?>"
					<?php if ( $cover_url ) : ?>style="background-image:url('<?php echo esc_url( $cover_url ); ?>');"<?php endif; ?>>
					<?php if ( $logo_url ) : ?>
						<img class="wcpc-cover__logo" src="<?php echo esc_url( $logo_url ); ?>" alt="" />
					<?php endif; ?>
					<h1 class="wcpc-cover__title"><?php echo esc_html( $settings['cover_title'] ); ?></h1>
					<?php if ( ! empty( $settings['cover_subtitle'] ) ) : ?>
						<p class="wcpc-cover__subtitle"><?php echo esc_html( $settings['cover_subtitle'] ); ?></p>
					<?php endif; ?>
					<p class="wcpc-cover__date"><?php echo esc_html( date_i18n( get_option( 'date_format' ) ) ); ?></p>
				</div>
			</article>

			<?php
			// ----- Batched render loop -----
			// Peak memory stays bounded by $batch_size WC_Product objects regardless
			// of catalog size. Three safety checks keep this loop from ever wedging:
			//   (a) duplicate batch detection - if wc_get_products' pagination breaks
			//       on this install, we stop instead of looping forever.
			//   (b) memory pressure - we stop at 75% of memory_limit and emit a notice.
			//   (c) hard 1000-iteration cap as a last resort.
			$cursor       = 1;
			$page_index   = 0;
			$carry        = array();
			$seen_ids     = array();
			$truncated    = false;
			$truncated_by = '';

			while ( $cursor <= 1000 ) {
				if ( WCPC_Catalog::memory_pressure() ) {
					$truncated    = true;
					$truncated_by = 'memory';
					break;
				}

				$batch = WCPC_Catalog::batch( $args, $cursor, $batch_size );

				if ( empty( $batch ) ) {
					if ( ! empty( $carry ) ) {
						$page_products = $carry;
						$carry         = array();
						$page_index++;
						include WCPC_PLUGIN_DIR . 'templates/parts/flipbook-page.php';
						unset( $page_products );
					}
					break;
				}

				// (a) Duplicate detection: if every product in this batch was already
				// rendered, pagination is wedged and continuing would be an infinite
				// loop. Bail out and flush whatever carry we have.
				$batch_ids = array();
				foreach ( $batch as $p ) {
					if ( $p instanceof WC_Product ) {
						$batch_ids[] = (int) $p->get_id();
					}
				}
				$fresh_ids = array_diff( $batch_ids, $seen_ids );
				if ( empty( $fresh_ids ) ) {
					$truncated    = true;
					$truncated_by = 'duplicate-batch';
					if ( ! empty( $carry ) ) {
						$page_products = $carry;
						$carry         = array();
						$page_index++;
						include WCPC_PLUGIN_DIR . 'templates/parts/flipbook-page.php';
						unset( $page_products );
					}
					break;
				}
				foreach ( $batch_ids as $id ) {
					$seen_ids[ $id ] = true;
				}

				$combined = array_merge( $carry, $batch );
				$carry    = array();
				unset( $batch, $batch_ids, $fresh_ids );

				while ( count( $combined ) >= $per_page ) {
					$page_products = array_splice( $combined, 0, $per_page );
					$page_index++;
					include WCPC_PLUGIN_DIR . 'templates/parts/flipbook-page.php';
					unset( $page_products );
				}

				$carry = $combined;
				unset( $combined );

				$cursor++;
				if ( $cursor % 3 === 0 ) {
					WCPC_Catalog::free_batch_memory();
				}
			}

			if ( $truncated ) {
				$rendered = count( $seen_ids );
				?>
				<article class="wcpc-flipbook__page" data-page="<?php echo (int) ( $page_index + 1 ); ?>">
					<div class="wcpc-truncated" style="text-align:center;padding:60px 20px;color:var(--wcpc-color-muted,#777);">
						<h2 style="margin:0 0 10px;color:var(--wcpc-color-primary,#1f7a3a);"><?php esc_html_e( 'Katalog gekürzt', 'wc-professional-catalog' ); ?></h2>
						<p>
							<?php
							printf(
								/* translators: 1: rendered count, 2: expected total */
								esc_html__( '%1$d von %2$d Produkten wurden gerendert. Bitte einen kleineren Filter wählen oder den Server-Speicher erhöhen.', 'wc-professional-catalog' ),
								(int) $rendered,
								(int) $total
							);
							?>
						</p>
						<?php if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) : ?>
							<p style="font-size:11px;opacity:.6"><?php echo esc_html( 'Abbruchgrund: ' . $truncated_by ); ?></p>
						<?php endif; ?>
					</div>
				</article>
				<?php
			}
			?>

		</div>
	</div>
</section>
