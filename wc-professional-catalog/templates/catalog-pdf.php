<?php
/**
 * PDF catalog template (also reused as the fallback printable HTML).
 *
 * Kept lean and table/CSS-friendly so Dompdf renders it cleanly.
 *
 * @package WCProfessionalCatalog
 * @var WC_Product[][] $pages
 * @var array          $settings
 * @var int            $columns
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$logo_url = ! empty( $settings['logo_id'] ) ? wp_get_attachment_image_url( $settings['logo_id'], 'large' ) : '';
$margin   = (int) $settings['pdf_margin'];

$page_size = strtoupper( $settings['pdf_page_size'] );
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8" />
<title><?php echo esc_html( $settings['cover_title'] ); ?></title>
<style>
@page { size: <?php echo esc_attr( $page_size ); ?> <?php echo esc_attr( $settings['pdf_orientation'] ); ?>; margin: <?php echo (int) $margin; ?>mm; }
* { box-sizing: border-box; }
body {
	font-family: <?php echo esc_html( $settings['font_family_body'] ); ?>, Helvetica, Arial, sans-serif;
	font-size: <?php echo (int) $settings['font_size_body']; ?>px;
	font-weight: <?php echo esc_html( $settings['font_weight_body'] ); ?>;
	font-style: <?php echo esc_html( $settings['font_style_body'] ); ?>;
	color: <?php echo esc_html( $settings['color_text'] ); ?>;
	background: <?php echo esc_html( $settings['color_bg'] ); ?>;
	margin: 0;
}
.wcpc-pdf-cover { text-align: center; padding: 60px 0; page-break-after: always; }
.wcpc-pdf-cover img { max-width: 240px; max-height: 140px; margin-bottom: 30px; }
.wcpc-pdf-cover h1 {
	font-family: <?php echo esc_html( $settings['font_family_title'] ); ?>, Helvetica, Arial, sans-serif;
	font-size: 42px;
	font-weight: <?php echo esc_html( $settings['font_weight_title'] ); ?>;
	color: <?php echo esc_html( $settings['color_primary'] ); ?>;
	margin: 0 0 10px;
}
.wcpc-pdf-cover .sub { color: <?php echo esc_html( $settings['color_muted'] ); ?>; font-size: 14px; margin: 0; }
.wcpc-pdf-cover .date { color: <?php echo esc_html( $settings['color_muted'] ); ?>; font-size: 12px; margin-top: 30px; }

.wcpc-pdf-page { page-break-after: always; }
.wcpc-pdf-page:last-child { page-break-after: auto; }
.wcpc-pdf-header { border-bottom: 2px solid <?php echo esc_html( $settings['color_primary'] ); ?>; padding-bottom: 6px; margin-bottom: 14px; display: table; width: 100%; }
.wcpc-pdf-header .l { display: table-cell; vertical-align: middle; }
.wcpc-pdf-header .r { display: table-cell; vertical-align: middle; text-align: right; color: <?php echo esc_html( $settings['color_muted'] ); ?>; font-size: 10px; }
.wcpc-pdf-header img { max-height: 28px; }
.wcpc-pdf-footer { position: fixed; bottom: -8mm; left: 0; right: 0; text-align: center; color: <?php echo esc_html( $settings['color_muted'] ); ?>; font-size: 9px; }

table.wcpc-pdf-grid { width: 100%; border-collapse: separate; border-spacing: 6px; }
table.wcpc-pdf-grid td {
	width: <?php echo (int) ( 100 / max( 1, (int) $columns ) ); ?>%;
	vertical-align: top;
	background: #fff;
	border: 1px solid #e8e8e8;
	padding: 8px;
}
.wcpc-pdf-item img { max-width: 100%; max-height: 110px; display: block; margin: 0 auto 6px; }
.wcpc-pdf-item .brand {
	color: <?php echo esc_html( $settings['color_accent'] ); ?>;
	font-size: 10px;
	letter-spacing: 1px;
	text-transform: uppercase;
}
.wcpc-pdf-item .title {
	font-family: <?php echo esc_html( $settings['font_family_title'] ); ?>, Helvetica, Arial, sans-serif;
	font-size: <?php echo (int) $settings['font_size_title']; ?>px;
	font-weight: <?php echo esc_html( $settings['font_weight_title'] ); ?>;
	font-style: <?php echo esc_html( $settings['font_style_title'] ); ?>;
	color: <?php echo esc_html( $settings['color_secondary'] ); ?>;
	margin: 4px 0 4px;
}
.wcpc-pdf-item .sku { color: <?php echo esc_html( $settings['color_muted'] ); ?>; font-size: 10px; margin-bottom: 4px; }
.wcpc-pdf-item .desc { font-size: <?php echo (int) $settings['font_size_body']; ?>px; line-height: 1.35; margin: 4px 0 8px; }
.wcpc-pdf-item .price {
	font-family: <?php echo esc_html( $settings['font_family_price'] ); ?>, Helvetica, Arial, sans-serif;
	font-size: <?php echo (int) $settings['font_size_price']; ?>px;
	font-weight: <?php echo esc_html( $settings['font_weight_price'] ); ?>;
	color: <?php echo esc_html( $settings['price_color'] ); ?>;
}
.wcpc-pdf-item .price small { color: <?php echo esc_html( $settings['color_muted'] ); ?>; font-weight: 400; }
.wcpc-pdf-item .unit { font-size: 10px; color: <?php echo esc_html( $settings['color_muted'] ); ?>; margin-top: 2px; }
.wcpc-pdf-item .buy { display: inline-block; margin-top: 6px; padding: 4px 8px; background: <?php echo esc_html( $settings['color_primary'] ); ?>; color: #fff; text-decoration: none; font-size: 10px; border-radius: 3px; }
</style>
</head>
<body class="wcpc-pdf">

<section class="wcpc-pdf-cover">
	<?php if ( $logo_url ) : ?>
		<img src="<?php echo esc_url( $logo_url ); ?>" alt="" />
	<?php endif; ?>
	<h1><?php echo esc_html( $settings['cover_title'] ); ?></h1>
	<?php if ( ! empty( $settings['cover_subtitle'] ) ) : ?>
		<p class="sub"><?php echo esc_html( $settings['cover_subtitle'] ); ?></p>
	<?php endif; ?>
	<p class="date"><?php echo esc_html( date_i18n( get_option( 'date_format' ) ) ); ?></p>
</section>

<?php $page_count = count( $pages ); ?>
<?php foreach ( $pages as $i => $page_products ) : ?>
	<section class="wcpc-pdf-page">
		<div class="wcpc-pdf-header">
			<div class="l">
				<?php if ( $logo_url ) : ?>
					<img src="<?php echo esc_url( $logo_url ); ?>" alt="" />
				<?php endif; ?>
			</div>
			<div class="r">
				<?php
				printf(
					/* translators: 1: current page, 2: total pages */
					esc_html__( 'Seite %1$d von %2$d', 'wc-professional-catalog' ),
					(int) ( $i + 1 ),
					(int) $page_count
				);
				?>
			</div>
		</div>
		<table class="wcpc-pdf-grid">
			<?php
			$chunked = array_chunk( $page_products, max( 1, (int) $columns ) );
			foreach ( $chunked as $row ) : ?>
				<tr>
				<?php
				foreach ( $row as $product ) :
					if ( ! $product instanceof WC_Product ) { continue; }
					$prices     = WCPC_Price::get_prices( $product );
					$unit_price = WCPC_Price::get_unit_price( $product );
					$brand      = WCPC_Catalog::get_brand_label( $product );
					$image      = wp_get_attachment_image_url( $product->get_image_id(), 'medium' );
					$short      = wp_strip_all_tags( $product->get_short_description() );
					$link       = get_permalink( $product->get_id() );
					?>
					<td>
						<div class="wcpc-pdf-item">
							<?php if ( $image ) : ?>
								<img src="<?php echo esc_url( $image ); ?>" alt="" />
							<?php endif; ?>
							<?php if ( $brand ) : ?>
								<div class="brand"><?php echo esc_html( $brand ); ?></div>
							<?php endif; ?>
							<div class="title"><?php echo esc_html( $product->get_name() ); ?></div>
							<?php if ( ! empty( $settings['show_sku'] ) && $product->get_sku() ) : ?>
								<div class="sku">Art.-Nr.: <?php echo esc_html( $product->get_sku() ); ?></div>
							<?php endif; ?>
							<?php if ( ! empty( $settings['show_short_desc'] ) && '' !== $short ) : ?>
								<div class="desc"><?php echo esc_html( wp_trim_words( $short, 22, '…' ) ); ?></div>
							<?php endif; ?>
							<?php if ( ! empty( $settings['show_price_gross'] ) ) : ?>
								<div class="price"><?php echo $prices['gross']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wc_price. ?> <small><?php esc_html_e( 'inkl. MwSt.', 'wc-professional-catalog' ); ?></small></div>
							<?php endif; ?>
							<?php if ( ! empty( $settings['show_price_net'] ) ) : ?>
								<div class="price"><small><?php echo $prices['net']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wc_price. ?> <?php esc_html_e( 'netto', 'wc-professional-catalog' ); ?></small></div>
							<?php endif; ?>
							<?php if ( ! empty( $settings['show_unit_price'] ) && $unit_price ) : ?>
								<div class="unit"><?php echo $unit_price; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wc_price. ?></div>
							<?php endif; ?>
							<?php if ( ! empty( $settings['enable_buy_link'] ) ) : ?>
								<a class="buy" href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $settings['buy_button_text'] ); ?></a>
							<?php endif; ?>
						</div>
					</td>
				<?php endforeach; ?>
				<?php
				// Pad remaining columns to keep the grid aligned.
				$pad = (int) $columns - count( $row );
				for ( $p = 0; $p < $pad; $p++ ) {
					echo '<td></td>';
				}
				?>
				</tr>
			<?php endforeach; ?>
		</table>
	</section>
<?php endforeach; ?>

<?php if ( ! empty( $settings['footer_text'] ) ) : ?>
	<div class="wcpc-pdf-footer"><?php echo esc_html( $settings['footer_text'] ); ?></div>
<?php endif; ?>

</body>
</html>
