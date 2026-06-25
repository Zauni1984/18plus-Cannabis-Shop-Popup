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
			$cursor     = 1;
			$page_index = 0;
			$carry      = array();

			while ( $cursor <= 1000 ) {
				$batch = WCPC_Catalog::batch( $args, $cursor, $batch_size );

				if ( empty( $batch ) ) {
					// No more products: emit the trailing partial page if any.
					if ( ! empty( $carry ) ) {
						$page_products = $carry;
						$carry         = array();
						$page_index++;
						include WCPC_PLUGIN_DIR . 'templates/parts/flipbook-page.php';
						unset( $page_products );
					}
					break;
				}

				$combined = array_merge( $carry, $batch );
				$carry    = array();
				unset( $batch );

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
			?>

		</div>
	</div>
</section>
