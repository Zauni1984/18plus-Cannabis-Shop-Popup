<?php
/**
 * Flipbook (online catalog) - rendered by shortcode and the /flipbook/ endpoint.
 *
 * @package WCProfessionalCatalog
 * @var WC_Product[][] $pages
 * @var array          $settings
 * @var int            $per_page
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$logo_url  = ! empty( $settings['logo_id'] ) ? wp_get_attachment_image_url( $settings['logo_id'], 'medium' ) : '';
$cover_url = ! empty( $settings['cover_image_id'] ) ? wp_get_attachment_image_url( $settings['cover_image_id'], 'large' ) : '';
?>
<section class="wcpc-catalog wcpc-flipbook" aria-label="<?php esc_attr_e( 'Online-Katalog', 'wc-professional-catalog' ); ?>">
	<div class="wcpc-flipbook__toolbar">
		<button type="button" class="wcpc-btn wcpc-btn-ghost" data-wcpc-prev>&laquo; <?php esc_html_e( 'Zurück', 'wc-professional-catalog' ); ?></button>
		<span class="wcpc-flipbook__pageinfo">
			<span data-wcpc-current>1</span> / <span data-wcpc-total><?php echo (int) ( count( $pages ) + 1 ); ?></span>
		</span>
		<button type="button" class="wcpc-btn wcpc-btn-ghost" data-wcpc-next><?php esc_html_e( 'Weiter', 'wc-professional-catalog' ); ?> &raquo;</button>
	</div>

	<div class="wcpc-flipbook__stage">
		<div class="wcpc-flipbook__pages">

			<article class="wcpc-flipbook__page wcpc-flipbook__page--cover is-active" data-page="0">
				<div class="wcpc-cover<?php echo $cover_url ? ' wcpc-cover--has-image' : ''; ?>"
					<?php if ( $cover_url ) : ?>
						style="background-image:url('<?php echo esc_url( $cover_url ); ?>');"
					<?php endif; ?>>
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

			<?php foreach ( $pages as $index => $page_products ) : ?>
				<article class="wcpc-flipbook__page" data-page="<?php echo (int) ( $index + 1 ); ?>">
					<header class="wcpc-flipbook__pageheader">
						<?php if ( $logo_url ) : ?>
							<img class="wcpc-flipbook__pagelogo" src="<?php echo esc_url( $logo_url ); ?>" alt="" />
						<?php endif; ?>
						<span class="wcpc-flipbook__pagenum">
							<?php
							printf(
								/* translators: 1: page number, 2: total pages */
								esc_html__( 'Seite %1$d / %2$d', 'wc-professional-catalog' ),
								(int) ( $index + 1 ),
								(int) count( $pages )
							);
							?>
						</span>
					</header>
					<div class="wcpc-grid" style="--wcpc-grid-cols: 2">
						<?php foreach ( $page_products as $product ) :
							if ( ! $product instanceof WC_Product ) {
								continue;
							}
							include WCPC_PLUGIN_DIR . 'templates/product-card.php';
						endforeach; ?>
					</div>
					<?php if ( ! empty( $settings['footer_text'] ) ) : ?>
						<footer class="wcpc-flipbook__pagefooter">
							<?php echo esc_html( $settings['footer_text'] ); ?>
						</footer>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>

		</div>
	</div>
</section>
