<?php
/**
 * Print-ready catalog page.
 *
 * @package WCProfessionalCatalog
 * @var WC_Product[] $products
 * @var array        $settings
 * @var int          $columns
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! isset( $settings ) || ! is_array( $settings ) ) {
	$settings = WCPC_Plugin::get_settings();
}
if ( ! isset( $products ) || ! is_array( $products ) ) {
	$products = array();
}
if ( ! isset( $columns ) || (int) $columns <= 0 ) {
	$columns = isset( $settings['pdf_columns'] ) ? max( 1, (int) $settings['pdf_columns'] ) : 3;
}
$logo_url  = ! empty( $settings['logo_id'] ) ? (string) wp_get_attachment_image_url( (int) $settings['logo_id'], 'medium' ) : '';
$cover_url = ! empty( $settings['cover_image_id'] ) ? (string) wp_get_attachment_image_url( (int) $settings['cover_image_id'], 'large' ) : '';
$css_vars  = WCPC_Catalog::build_css_variables( $settings );
?>
<!doctype html>
<html lang="<?php echo esc_attr( get_bloginfo( 'language' ) ); ?>">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php echo esc_html( $settings['cover_title'] ); ?></title>
<link rel="stylesheet" href="<?php echo esc_url( WCPC_PLUGIN_URL . 'public/css/catalog.css?v=' . WCPC_VERSION ); ?>" />
<link rel="stylesheet" href="<?php echo esc_url( WCPC_PLUGIN_URL . 'public/css/print.css?v=' . WCPC_VERSION ); ?>" />
<style><?php echo $css_vars; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- generated. ?></style>
</head>
<body class="wcpc-print-body">

<header class="wcpc-print-cover<?php echo $cover_url ? ' wcpc-print-cover--has-image' : ''; ?>"
	<?php if ( $cover_url ) : ?>style="background-image:url('<?php echo esc_url( $cover_url ); ?>');"<?php endif; ?>>
	<?php if ( $cover_url ) : ?><div class="wcpc-print-cover__scrim"></div><?php endif; ?>
	<div class="wcpc-print-cover__inner">
		<?php if ( $logo_url ) : ?>
			<img class="wcpc-print-cover__logo" src="<?php echo esc_url( $logo_url ); ?>" alt="" />
		<?php endif; ?>
		<h1 class="wcpc-print-cover__title"><?php echo esc_html( $settings['cover_title'] ); ?></h1>
		<?php if ( ! empty( $settings['cover_subtitle'] ) ) : ?>
			<p class="wcpc-print-cover__subtitle"><?php echo esc_html( $settings['cover_subtitle'] ); ?></p>
		<?php endif; ?>
		<p class="wcpc-print-cover__date"><?php echo esc_html( date_i18n( get_option( 'date_format' ) ) ); ?></p>
	</div>
</header>

<main class="wcpc-catalog wcpc-catalog--print">
	<div class="wcpc-grid" style="--wcpc-grid-cols: <?php echo (int) $columns; ?>">
		<?php foreach ( $products as $product ) :
			if ( ! $product instanceof WC_Product ) {
				continue;
			}
			include WCPC_PLUGIN_DIR . 'templates/product-card.php';
		endforeach; ?>
	</div>
</main>

<?php if ( ! empty( $settings['footer_text'] ) ) : ?>
	<footer class="wcpc-print-footer"><?php echo esc_html( $settings['footer_text'] ); ?></footer>
<?php endif; ?>

<script>window.addEventListener('load',function(){setTimeout(function(){window.print();},400);});</script>
</body>
</html>
