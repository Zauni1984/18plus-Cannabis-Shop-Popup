<?php
/**
 * Single product card. Receives $product (WC_Product) and $settings (array).
 *
 * @package WCProfessionalCatalog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** @var WC_Product $product */
/** @var array      $settings */

$prices     = WCPC_Price::get_prices( $product );
$unit_price = WCPC_Price::get_unit_price( $product );
$brand      = WCPC_Catalog::get_brand_label( $product );
$image      = $product->get_image( 'medium', array( 'class' => 'wcpc-card__image' ) );
$link       = get_permalink( $product->get_id() );
$cart_link  = add_query_arg(
	array(
		'add-to-cart' => $product->get_id(),
	),
	wc_get_checkout_url() ? wc_get_cart_url() : home_url( '/' )
);
?>
<article class="wcpc-card">
	<a class="wcpc-card__media" href="<?php echo esc_url( $link ); ?>">
		<?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WC HTML. ?>
		<?php if ( $product->is_on_sale() ) : ?>
			<span class="wcpc-card__badge"><?php esc_html_e( 'Aktion', 'wc-professional-catalog' ); ?></span>
		<?php endif; ?>
	</a>
	<div class="wcpc-card__body">
		<?php if ( $brand ) : ?>
			<div class="wcpc-card__brand"><?php echo esc_html( $brand ); ?></div>
		<?php endif; ?>
		<h3 class="wcpc-card__title">
			<a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
		</h3>
		<?php if ( ! empty( $settings['show_sku'] ) && $product->get_sku() ) : ?>
			<div class="wcpc-card__sku"><?php esc_html_e( 'Art.-Nr.', 'wc-professional-catalog' ); ?>: <?php echo esc_html( $product->get_sku() ); ?></div>
		<?php endif; ?>
		<?php if ( ! empty( $settings['show_short_desc'] ) ) :
			$short = wp_strip_all_tags( $product->get_short_description() );
			if ( '' !== $short ) : ?>
				<p class="wcpc-card__short"><?php echo esc_html( wp_trim_words( $short, 24, '…' ) ); ?></p>
		<?php endif; endif; ?>

		<div class="wcpc-card__prices">
			<?php if ( ! empty( $settings['show_price_gross'] ) ) : ?>
				<div class="wcpc-card__price wcpc-card__price--gross">
					<span class="wcpc-card__price-label"><?php esc_html_e( 'Brutto', 'wc-professional-catalog' ); ?></span>
					<?php echo $prices['gross']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wc_price. ?>
				</div>
			<?php endif; ?>
			<?php if ( ! empty( $settings['show_price_net'] ) ) : ?>
				<div class="wcpc-card__price wcpc-card__price--net">
					<span class="wcpc-card__price-label"><?php esc_html_e( 'Netto', 'wc-professional-catalog' ); ?></span>
					<?php echo $prices['net']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wc_price. ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $settings['show_unit_price'] ) && $unit_price ) : ?>
			<div class="wcpc-card__unit-price"><?php echo $unit_price; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wc_price. ?></div>
		<?php endif; ?>

		<?php if ( ! empty( $settings['enable_buy_link'] ) ) : ?>
			<a class="wcpc-card__buy" href="<?php echo esc_url( $link ); ?>">
				<?php echo esc_html( $settings['buy_button_text'] ); ?>
			</a>
		<?php endif; ?>
	</div>
</article>
