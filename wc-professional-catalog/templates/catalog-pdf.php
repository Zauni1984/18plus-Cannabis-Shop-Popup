<?php
/**
 * PDF catalog template (also reused as the fallback printable HTML).
 *
 * Renders the catalog by streaming products in batches so peak memory
 * stays bounded by the batch size, even on shops with thousands of
 * products.
 *
 * Expects in scope:
 *   - $args     Filter args (forwarded to WCPC_Catalog::batch())
 *   - $settings (array)
 *   - $columns  (int)
 *   - $total    (int)        Pre-counted total products
 *
 * @package WCProfessionalCatalog
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
if ( ! isset( $columns ) || (int) $columns <= 0 ) {
	$columns = isset( $settings['pdf_columns'] ) ? max( 1, (int) $settings['pdf_columns'] ) : 3;
}
$per_pdf_page = max( 1, (int) ( $settings['pdf_per_page'] ?? 9 ) );
if ( ! isset( $total ) ) {
	$total = WCPC_Catalog::count( $args );
}
$total       = (int) $total;
$total_pages = max( 1, (int) ceil( $total / $per_pdf_page ) );

$logo_url      = ! empty( $settings['logo_id'] ) ? (string) wp_get_attachment_image_url( (int) $settings['logo_id'], 'large' ) : '';
$cover_url_pdf = ! empty( $settings['cover_image_id'] ) ? (string) wp_get_attachment_image_url( (int) $settings['cover_image_id'], 'large' ) : '';
$pdf_img_size  = isset( $settings['image_size_pdf'] ) && $settings['image_size_pdf'] ? (string) $settings['image_size_pdf'] : 'medium';
$margin        = isset( $settings['pdf_margin'] ) ? max( 0, (int) $settings['pdf_margin'] ) : 12;
$page_size     = isset( $settings['pdf_page_size'] ) ? strtoupper( (string) $settings['pdf_page_size'] ) : 'A4';

$dense          = ( (int) $columns >= 3 );
$title_size_pdf = $dense ? max( 9, min( 13, (int) $settings['font_size_title'] - 4 ) ) : (int) $settings['font_size_title'];
$body_size_pdf  = $dense ? max( 8, min( 10, (int) $settings['font_size_body'] ) ) : (int) $settings['font_size_body'];
$price_size_pdf = $dense ? max( 10, min( 13, (int) $settings['font_size_price'] - 1 ) ) : (int) $settings['font_size_price'];
$img_max_h      = $dense ? 70 : 110;
$cell_pad       = $dense ? 5 : 8;
$cell_gap       = $dense ? 4 : 6;
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

<?php
// ----- Batched render loop -----
// Same safety net as the flipbook: memory pressure check, duplicate-batch
// detection, hard iteration cap.
$batch_size = (int) apply_filters( 'wcpc_batch_size', max( $per_pdf_page * 2, 30 ) );
$cursor     = 1;
$page_index = 0;
$carry      = array();
$seen_ids   = array();
$truncated  = false;

while ( $cursor <= 1000 ) {
	if ( WCPC_Catalog::memory_pressure() ) {
		$truncated = true;
		break;
	}

	$batch = WCPC_Catalog::batch( $args, $cursor, $batch_size );

	if ( empty( $batch ) ) {
		if ( ! empty( $carry ) ) {
			$page_products = $carry;
			$carry         = array();
			$page_index++;
			include WCPC_PLUGIN_DIR . 'templates/parts/pdf-page.php';
			unset( $page_products );
		}
		break;
	}

	$batch_ids = array();
	foreach ( $batch as $p ) {
		if ( $p instanceof WC_Product ) {
			$batch_ids[] = (int) $p->get_id();
		}
	}
	$fresh_ids = array_diff( $batch_ids, $seen_ids );
	if ( empty( $fresh_ids ) ) {
		$truncated = true;
		if ( ! empty( $carry ) ) {
			$page_products = $carry;
			$carry         = array();
			$page_index++;
			include WCPC_PLUGIN_DIR . 'templates/parts/pdf-page.php';
			unset( $page_products );
		}
		break;
	}
	foreach ( $batch_ids as $id ) {
		$seen_ids[ $id ] = true;
	}

	$combined = array_merge( $carry, $batch );
	$carry    = array();
	unset( $batch, $batch_ids, $fresh_ids );

	while ( count( $combined ) >= $per_pdf_page ) {
		$page_products = array_splice( $combined, 0, $per_pdf_page );
		$page_index++;
		include WCPC_PLUGIN_DIR . 'templates/parts/pdf-page.php';
		unset( $page_products );
	}

	$carry = $combined;
	unset( $combined );

	$cursor++;
	if ( $cursor % 3 === 0 ) {
		WCPC_Catalog::free_batch_memory();
	}
}

if ( $truncated ) {
	$rendered = count( $seen_ids );
	?>
	<section class="wcpc-pdf-page">
		<div class="wcpc-pdf-header">
			<div class="l"></div>
			<div class="r"><?php esc_html_e( 'Katalog gekürzt', 'wc-professional-catalog' ); ?></div>
		</div>
		<p style="text-align:center;padding:40px;color:#777;font-size:11px;">
			<?php
			printf(
				/* translators: 1: rendered count, 2: total */
				esc_html__( '%1$d von %2$d Produkten enthalten.', 'wc-professional-catalog' ),
				(int) $rendered,
				(int) $total
			);
			?>
		</p>
	</section>
	<?php
}
?>

<?php if ( ! empty( $settings['footer_text'] ) ) : ?>
	<div class="wcpc-pdf-footer"><?php echo esc_html( $settings['footer_text'] ); ?></div>
<?php endif; ?>

</body>
</html>
