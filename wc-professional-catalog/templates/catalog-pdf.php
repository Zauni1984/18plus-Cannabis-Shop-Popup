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

if ( ! isset( $settings ) || ! is_array( $settings ) ) {
	$settings = WCPC_Plugin::get_settings();
}
if ( ! isset( $pages ) || ! is_array( $pages ) ) {
	$pages = array();
}
if ( ! isset( $columns ) || (int) $columns <= 0 ) {
	$columns = isset( $settings['pdf_columns'] ) ? max( 1, (int) $settings['pdf_columns'] ) : 3;
}
$logo_url      = ! empty( $settings['logo_id'] ) ? (string) wp_get_attachment_image_url( (int) $settings['logo_id'], 'large' ) : '';
$cover_url_pdf = ! empty( $settings['cover_image_id'] ) ? (string) wp_get_attachment_image_url( (int) $settings['cover_image_id'], 'large' ) : '';
$pdf_img_size  = isset( $settings['image_size_pdf'] ) && $settings['image_size_pdf'] ? (string) $settings['image_size_pdf'] : 'medium';
$margin        = isset( $settings['pdf_margin'] ) ? max( 0, (int) $settings['pdf_margin'] ) : 12;

$page_size = isset( $settings['pdf_page_size'] ) ? strtoupper( (string) $settings['pdf_page_size'] ) : 'A4';
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
.wcpc-pdf-cover { text-align: center; padding: 60px 0; page-break-after: always; position: relative; }
.wcpc-pdf-cover.has-bg { background-size: cover; background-position: center; color: #fff; padding: 120px 30px; min-height: 400px; }
.wcpc-pdf-cover.has-bg .scrim { position: absolute; inset: 0; background: rgba(0,0,0,.45); }
.wcpc-pdf-cover.has-bg .inner { position: relative; z-index: 1; }
.wcpc-pdf-cover.has-bg h1 { color: #fff !important; }
.wcpc-pdf-cover.has-bg .sub, .wcpc-pdf-cover.has-bg .date { color: rgba(255,255,255,.85) !important; }
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

<?php
// Scale the card font sizes down a bit for dense layouts (3+ columns) so 9
// products comfortably fit on A4 portrait.
$dense          = ( (int) $columns >= 3 );
$title_size_pdf = $dense ? max( 9, min( 13, (int) $settings['font_size_title'] - 4 ) ) : (int) $settings['font_size_title'];
$body_size_pdf  = $dense ? max( 8, min( 10, (int) $settings['font_size_body'] ) ) : (int) $settings['font_size_body'];
$price_size_pdf = $dense ? max( 10, min( 13, (int) $settings['font_size_price'] - 1 ) ) : (int) $settings['font_size_price'];
$img_max_h      = $dense ? 70 : 110;
$cell_pad       = $dense ? 5 : 8;
$cell_gap       = $dense ? 4 : 6;
?>
table.wcpc-pdf-grid { width: 100%; border-collapse: separate; border-spacing: <?php echo (int) $cell_gap; ?>px; }
table.wcpc-pdf-grid td {
	width: <?php echo (int) ( 100 / max( 1, (int) $columns ) ); ?>%;
	vertical-align: top;
	background: #fff;
	border: 1px solid #e8e8e8;
	padding: <?php echo (int) $cell_pad; ?>px;
}
.wcpc-pdf-item img { max-width: 100%; max-height: <?php echo (int) $img_max_h; ?>px; display: block; margin: 0 auto 4px; }
.wcpc-pdf-item .brand {
	color: <?php echo esc_html( $settings['color_accent'] ); ?>;
	font-size: 9px;
	letter-spacing: 1px;
	text-transform: uppercase;
}
.wcpc-pdf-item .title {
	font-family: <?php echo esc_html( $settings['font_family_title'] ); ?>, Helvetica, Arial, sans-serif;
	font-size: <?php echo (int) $title_size_pdf; ?>px;
	font-weight: <?php echo esc_html( $settings['font_weight_title'] ); ?>;
	font-style: <?php echo esc_html( $settings['font_style_title'] ); ?>;
	color: <?php echo esc_html( $settings['color_secondary'] ); ?>;
	margin: 2px 0 3px;
	line-height: 1.15;
}
.wcpc-pdf-item .sku { color: <?php echo esc_html( $settings['color_muted'] ); ?>; font-size: 9px; margin-bottom: 3px; }
.wcpc-pdf-item .desc { font-size: <?php echo (int) $body_size_pdf; ?>px; line-height: 1.3; margin: 2px 0 4px; }
.wcpc-pdf-item .price {
	font-family: <?php echo esc_html( $settings['font_family_price'] ); ?>, Helvetica, Arial, sans-serif;
	font-size: <?php echo (int) $price_size_pdf; ?>px;
	font-weight: <?php echo esc_html( $settings['font_weight_price'] ); ?>;
	color: <?php echo esc_html( $settings['price_color'] ); ?>;
	line-height: 1.2;
}
.wcpc-pdf-item .price small { color: <?php echo esc_html( $settings['color_muted'] ); ?>; font-weight: 400; font-size: 9px; }
.wcpc-pdf-item .unit { font-size: 9px; color: <?php echo esc_html( $settings['color_muted'] ); ?>; margin-top: 1px; }
.wcpc-pdf-item .buy { display: inline-block; margin-top: 4px; padding: 3px 6px; background: <?php echo esc_html( $settings['color_primary'] ); ?>; color: #fff; text-decoration: none; font-size: 9px; border-radius: 3px; }
.wcpc-pdf-item a.img-link, .wcpc-pdf-item .title a { text-decoration: none; color: inherit; }
.wcpc-pdf-item .pdf-gallery { margin-top: 3px; display: block; }
.wcpc-pdf-item .pdf-gallery img { display: inline-block; width: 22px; height: 22px; object-fit: cover; margin-right: 2px; border: 1px solid #e8e8e8; border-radius: 2px; }
</style>
</head>
<body class="wcpc-pdf">

<section class="wcpc-pdf-cover<?php echo $cover_url_pdf ? ' has-bg' : ''; ?>"
	<?php if ( $cover_url_pdf ) : ?>style="background-image:url('<?php echo esc_url( $cover_url_pdf ); ?>');"<?php endif; ?>>
	<?php if ( $cover_url_pdf ) : ?><div class="scrim"></div><?php endif; ?>
	<div class="inner">
		<?php if ( $logo_url ) : ?>
			<img src="<?php echo esc_url( $logo_url ); ?>" alt="" />
		<?php endif; ?>
		<h1><?php echo esc_html( $settings['cover_title'] ); ?></h1>
		<?php if ( ! empty( $settings['cover_subtitle'] ) ) : ?>
			<p class="sub"><?php echo esc_html( $settings['cover_subtitle'] ); ?></p>
		<?php endif; ?>
		<p class="date"><?php echo esc_html( date_i18n( get_option( 'date_format' ) ) ); ?></p>
	</div>
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
					$image      = wp_get_attachment_image_url( $product->get_image_id(), $pdf_img_size );
					$short      = wp_strip_all_tags( $product->get_short_description() );
					$link       = get_permalink( $product->get_id() );
					$pdf_gallery = ( ! empty( $settings['pdf_show_gallery'] ) ) ? $product->get_gallery_image_ids() : array();
					if ( $pdf_gallery ) {
						$pdf_gallery = array_slice( $pdf_gallery, 0, max( 1, (int) $settings['pdf_gallery_count'] ) );
					}
					?>
					<td>
						<div class="wcpc-pdf-item">
							<?php if ( $image ) : ?>
								<a class="img-link" href="<?php echo esc_url( $link ); ?>">
									<img src="<?php echo esc_url( $image ); ?>" alt="" />
								</a>
							<?php endif; ?>
							<?php if ( $brand ) : ?>
								<div class="brand"><?php echo esc_html( $brand ); ?></div>
							<?php endif; ?>
							<div class="title">
								<a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
							</div>
							<?php if ( $pdf_gallery ) : ?>
								<div class="pdf-gallery">
									<?php foreach ( $pdf_gallery as $gid ) :
										$thumb = wp_get_attachment_image_url( $gid, 'thumbnail' );
										if ( ! $thumb ) { continue; } ?>
										<img src="<?php echo esc_url( $thumb ); ?>" alt="" />
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
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
