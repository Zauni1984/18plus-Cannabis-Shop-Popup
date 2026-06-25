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

if ( ! isset( $product ) || ! ( $product instanceof WC_Product ) ) {
	return;
}
if ( ! isset( $settings ) || ! is_array( $settings ) ) {
	$settings = WCPC_Plugin::get_settings();
}

$prices     = WCPC_Price::get_prices( $product );
$unit_price = WCPC_Price::get_unit_price( $product );
$brand      = WCPC_Catalog::get_brand_label( $product );
$link       = (string) get_permalink( $product->get_id() );

$img_size_setting = isset( $settings['image_size_online'] ) ? (string) $settings['image_size_online'] : 'medium';
$img_size         = '' !== $img_size_setting ? $img_size_setting : 'medium';

$main_img = (int) $product->get_image_id();
$full_url = $main_img ? (string) wp_get_attachment_image_url( $main_img, 'full' ) : '';

$image_attrs = array( 'class' => 'wcpc-card__image' );
$image       = (string) $product->get_image( $img_size, $image_attrs );

$aspect_raw = isset( $settings['image_aspect'] ) ? (string) $settings['image_aspect'] : '4-3';
$style_raw  = isset( $settings['image_style'] ) ? (string) $settings['image_style'] : 'flat';
$pos_raw    = isset( $settings['image_position'] ) ? (string) $settings['image_position'] : 'top';

$aspect_cls = 'wcpc-card--aspect-' . sanitize_html_class( $aspect_raw, '4-3' );
$style_cls  = 'wcpc-card--style-' . sanitize_html_class( $style_raw, 'flat' );
$pos_cls    = 'wcpc-card--pos-' . sanitize_html_class( $pos_raw, 'top' );

$gallery_ids = array();
if ( ! empty( $settings['show_gallery'] ) ) {
	$ids = $product->get_gallery_image_ids();
	if ( is_array( $ids ) && $ids ) {
		$count       = isset( $settings['gallery_count'] ) ? max( 1, (int) $settings['gallery_count'] ) : 3;
		$gallery_ids = array_slice( $ids, 0, $count );
	}
}

$lightbox = ! empty( $settings['enable_lightbox'] );
$group    = 'product-' . $product->get_id();

$show_sku      = ! empty( $settings['show_sku'] );
$show_short    = ! empty( $settings['show_short_desc'] );
$show_gross    = ! empty( $settings['show_price_gross'] );
$show_net      = ! empty( $settings['show_price_net'] );
$show_unit     = ! empty( $settings['show_unit_price'] );
$enable_buy    = ! empty( $settings['enable_buy_link'] );
$buy_label     = isset( $settings['buy_button_text'] ) ? (string) $settings['buy_button_text'] : __( 'Jetzt kaufen', 'wc-professional-catalog' );
?>
<article class="wcpc-card <?php echo esc_attr( $aspect_cls . ' ' . $style_cls . ' ' . $pos_cls ); ?>">
	<a class="wcpc-card__media" href="<?php echo esc_url( $lightbox && '' !== $full_url ? $full_url : $link ); ?>"<?php if ( $lightbox && '' !== $full_url ) : ?> data-wcpc-lightbox="1" data-wcpc-group="<?php echo esc_attr( $group ); ?>" data-wcpc-title="<?php echo esc_attr( $product->get_name() ); ?>"<?php endif; ?>>
		<?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WC HTML. ?>
		<?php if ( $product->is_on_sale() ) : ?>
			<span class="wcpc-card__badge"><?php esc_html_e( 'Aktion', 'wc-professional-catalog' ); ?></span>
		<?php endif; ?>
	</a>

	<?php if ( $gallery_ids ) : ?>
		<div class="wcpc-card__gallery">
			<?php foreach ( $gallery_ids as $gid ) :
				$thumb_url = (string) wp_get_attachment_image_url( (int) $gid, 'thumbnail' );
				$big_url   = (string) wp_get_attachment_image_url( (int) $gid, 'full' );
				if ( '' === $thumb_url ) {
					continue;
				}
				?>
				<a class="wcpc-card__gallery-thumb" href="<?php echo esc_url( $lightbox && '' !== $big_url ? $big_url : $link ); ?>"<?php if ( $lightbox && '' !== $big_url ) : ?> data-wcpc-lightbox="1" data-wcpc-group="<?php echo esc_attr( $group ); ?>"<?php endif; ?>>
					<img src="<?php echo esc_url( $thumb_url ); ?>" alt="" loading="lazy" />
				</a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<div class="wcpc-card__body">
		<?php if ( '' !== $brand ) : ?>
			<div class="wcpc-card__brand"><?php echo esc_html( $brand ); ?></div>
		<?php endif; ?>
		<h3 class="wcpc-card__title">
			<a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
		</h3>
		<?php if ( $show_sku && $product->get_sku() ) : ?>
			<div class="wcpc-card__sku"><?php esc_html_e( 'Art.-Nr.', 'wc-professional-catalog' ); ?>: <?php echo esc_html( $product->get_sku() ); ?></div>
		<?php endif; ?>
		<?php
		if ( $show_short ) :
			$short = wp_strip_all_tags( (string) $product->get_short_description() );
			if ( '' !== $short ) :
				?>
				<p class="wcpc-card__short"><?php echo esc_html( wp_trim_words( $short, 24, '…' ) ); ?></p>
				<?php
			endif;
		endif;
		?>

		<div class="wcpc-card__prices">
			<?php if ( $show_gross ) : ?>
				<div class="wcpc-card__price wcpc-card__price--gross">
					<span class="wcpc-card__price-label"><?php esc_html_e( 'Brutto', 'wc-professional-catalog' ); ?></span>
					<?php echo $prices['gross']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wc_price. ?>
				</div>
			<?php endif; ?>
			<?php if ( $show_net ) : ?>
				<div class="wcpc-card__price wcpc-card__price--net">
					<span class="wcpc-card__price-label"><?php esc_html_e( 'Netto', 'wc-professional-catalog' ); ?></span>
					<?php echo $prices['net']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wc_price. ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $show_unit && '' !== $unit_price ) : ?>
			<div class="wcpc-card__unit-price"><?php echo $unit_price; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wc_price. ?></div>
		<?php endif; ?>

		<?php if ( $enable_buy ) : ?>
			<a class="wcpc-card__buy" href="<?php echo esc_url( $link ); ?>">
				<?php echo esc_html( $buy_label ); ?>
			</a>
		<?php endif; ?>
	</div>
</article>
