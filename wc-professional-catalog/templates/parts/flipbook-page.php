<?php
/**
 * One flipbook page. Expects in scope:
 *   - $page_products   (WC_Product[])
 *   - $page_index      (int, 1-based)
 *   - $total_pages     (int)
 *   - $logo_url        (string)
 *   - $settings        (array)
 *   - $section_main    (string, optional)
 *   - $section_sub     (string, optional)
 *   - $show_banner     (bool, optional - true on the first page of a section)
 *
 * @package WCProfessionalCatalog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$section_main = isset( $section_main ) ? (string) $section_main : '';
$section_sub  = isset( $section_sub ) ? (string) $section_sub : '';
$show_banner  = isset( $show_banner ) ? (bool) $show_banner : false;
$section_path = $section_main;
if ( '' !== $section_sub ) {
	$section_path = '' === $section_main ? $section_sub : $section_main . ' / ' . $section_sub;
}
?>
<article class="wcpc-flipbook__page" data-page="<?php echo (int) $page_index; ?>">
	<header class="wcpc-flipbook__pageheader">
		<?php if ( ! empty( $logo_url ) ) : ?>
			<img class="wcpc-flipbook__pagelogo" src="<?php echo esc_url( $logo_url ); ?>" alt="" />
		<?php endif; ?>
		<?php if ( '' !== $section_path ) : ?>
			<span class="wcpc-flipbook__pagesection"><?php echo esc_html( $section_path ); ?></span>
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

	<?php if ( $show_banner && '' !== $section_path ) : ?>
		<div class="wcpc-section-banner">
			<?php if ( '' !== $section_main ) : ?>
				<div class="wcpc-section-banner__eyebrow"><?php echo esc_html( $section_main ); ?></div>
			<?php endif; ?>
			<?php if ( '' !== $section_sub ) : ?>
				<h2 class="wcpc-section-banner__title"><?php echo esc_html( $section_sub ); ?></h2>
			<?php elseif ( '' !== $section_main ) : ?>
				<h2 class="wcpc-section-banner__title"><?php echo esc_html( $section_main ); ?></h2>
			<?php endif; ?>
		</div>
	<?php endif; ?>

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
