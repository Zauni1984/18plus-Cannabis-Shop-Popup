<?php
/**
 * Grid catalog template - rendered by shortcode.
 *
 * @package WCProfessionalCatalog
 * @var WC_Product[] $products
 * @var array        $settings
 * @var int          $columns
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="wcpc-catalog wcpc-catalog--grid" data-columns="<?php echo (int) $columns; ?>">
	<?php if ( empty( $products ) ) : ?>
		<p class="wcpc-empty"><?php esc_html_e( 'Keine Produkte gefunden.', 'wc-professional-catalog' ); ?></p>
	<?php else : ?>
		<div class="wcpc-grid" style="--wcpc-grid-cols: <?php echo (int) $columns; ?>">
			<?php foreach ( $products as $product ) :
				if ( ! $product instanceof WC_Product ) {
					continue;
				}
				include WCPC_PLUGIN_DIR . 'templates/product-card.php';
			endforeach; ?>
		</div>
	<?php endif; ?>
</section>
