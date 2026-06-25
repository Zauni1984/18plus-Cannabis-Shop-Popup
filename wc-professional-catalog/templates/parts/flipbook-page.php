<?php
/**
 * One flipbook page. Expects $page_products (array of WC_Product), $page_index (int),
 * $total_pages (int), $logo_url (string), $settings (array) in scope.
 *
 * @package WCProfessionalCatalog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="wcpc-flipbook__page" data-page="<?php echo (int) $page_index; ?>">
	<header class="wcpc-flipbook__pageheader">
		<?php if ( ! empty( $logo_url ) ) : ?>
			<img class="wcpc-flipbook__pagelogo" src="<?php echo esc_url( $logo_url ); ?>" alt="" />
		<?php endif; ?>
		<span class="wcpc-flipbook__pagenum">
			<?php
			printf(
				/* translators: 1: page number, 2: total pages */
				esc_html__( 'Seite %1$d / %2$d', 'wc-professional-catalog' ),
				(int) $page_index,
				(int) $total_pages
			);
			?>
		</span>
	</header>
	<div class="wcpc-grid" style="--wcpc-grid-cols: 2">
		<?php
		foreach ( $page_products as $product ) {
			if ( ! $product instanceof WC_Product ) {
				continue;
			}
			include WCPC_PLUGIN_DIR . 'templates/product-card.php';
		}
		?>
	</div>
	<?php if ( ! empty( $settings['footer_text'] ) ) : ?>
		<footer class="wcpc-flipbook__pagefooter">
			<?php echo esc_html( $settings['footer_text'] ); ?>
		</footer>
	<?php endif; ?>
</article>
