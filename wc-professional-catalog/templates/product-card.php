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
$link       = get_permalink( $product->get_id() );

$img_size   = $settings['image_size_online'] ?: 'medium';
$main_img   = $product->get_image_id();
$full_url   = $main_img ? wp_get_attachment_image_url( $main_img, 'full' ) : '';
$image      = $product->get_image( $img_size, array( 'class' => 'wcpc-card__image', 'loading' => 'lazy' ) );

$aspect_cls = 'wcpc-card--aspect-' . sanitize_html_class( $settings['image_aspect'] );
$style_cls  = 'wcpc-card--style-' . sanitize_html_class( $settings['image_style'] );
$pos_cls    = 'wcpc-card--pos-' . sanitize_html_class( $settings['image_position'] );

$gallery_ids = ! empty( $settings['show_gallery'] ) ? $product->get_gallery_image_ids() : array();
if ( $gallery_ids ) {
	$gallery_ids = array_slice( $gallery_ids, 0, max( 1, (int) $settings['gallery_count'] ) );
}

$lightbox = ! empty( $settings['enable_lightbox'] );
$group    = 'product-' . $product->get_id();
?>
<article class="wcpc-card <?php echo esc_attr( $aspect_cls . ' ' . $style_cls . ' ' . $pos_cls ); ?>">
	<a class="wcpc-card__media"
		href="<?php echo esc_url( $lightbox && $full_url ? $full_url : $link ); ?>"
		<?php if ( $lightbox && $full_url ) : ?>
			data-wcpc-lightbox="1" data-wcpc-group="<?php echo esc_attr( $group ); ?>" data-wcpc-title="<?php echo esc_attr( $product->get_name() ); ?>"
		<?php endif; ?>>
		<?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WC HTML. ?>
		<?php if ( $product->is_on_sale() ) : ?>
			<span class="wcpc-card__badge"><?php esc_html_e( 'Aktion', 'wc-professional-catalog' ); ?></span>
		<?php endif; ?>
	</a>

	<?php if ( $gallery_ids ) : ?>
		<div class="wcpc-card__gallery">
			<?php foreach ( $gallery_ids as $gid ) :
				$thumb_url = wp_get_attachment_image_url( $gid, 'thumbnail' );
				$big_url   = wp_get_attachment_image_url( $gid, 'full' );
				if ( ! $thumb_url ) { continue; } ?>
				<a class="wcpc-card__gallery-thumb"
					href="<?php echo esc_url( $lightbox && $big_url ? $big_url : $link ); ?>"
					<?php if ( $lightbox && $big_url ) : ?>
						data-wcpc-lightbox="1" data-wcpc-group="<?php echo esc_attr( $group ); ?>"
					<?php endif; ?>>
					<img src="<?php echo esc_url( $thumb_url ); ?>" alt="" loading="lazy" />
				</a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

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
