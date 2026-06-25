<?php
/**
 * Main settings page wrapper.
 *
 * @package WCProfessionalCatalog
 * @var array  $settings
 * @var array  $tabs
 * @var string $active
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap wcpc-wrap">
	<h1><?php esc_html_e( 'WC Professional Catalog', 'wc-professional-catalog' ); ?></h1>

	<?php if ( isset( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Einstellungen gespeichert.', 'wc-professional-catalog' ); ?></p></div>
	<?php endif; ?>

	<h2 class="nav-tab-wrapper">
		<?php foreach ( $tabs as $slug => $label ) : ?>
			<a class="nav-tab <?php echo $active === $slug ? 'nav-tab-active' : ''; ?>"
			   href="<?php echo esc_url( admin_url( 'admin.php?page=wcpc-settings&tab=' . $slug ) ); ?>">
				<?php echo esc_html( $label ); ?>
			</a>
		<?php endforeach; ?>
	</h2>

	<?php if ( 'shortcuts' === $active ) :
		include WCPC_PLUGIN_DIR . 'admin/views/settings-shortcuts.php';
	elseif ( 'diag' === $active ) :
		include WCPC_PLUGIN_DIR . 'admin/views/settings-diagnostic.php';
	elseif ( 'generator' === $active ) :
		include WCPC_PLUGIN_DIR . 'admin/views/settings-pdf-generator.php';
	else : ?>
		<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" class="wcpc-form">
			<?php wp_nonce_field( 'wcpc_save_settings' ); ?>
			<input type="hidden" name="action" value="wcpc_save_settings" />
			<input type="hidden" name="_wcpc_tab" value="<?php echo esc_attr( $active ); ?>" />

			<?php
			switch ( $active ) {
				case 'design':
					include WCPC_PLUGIN_DIR . 'admin/views/settings-design.php';
					break;
				case 'images':
					include WCPC_PLUGIN_DIR . 'admin/views/settings-images.php';
					break;
				case 'typo':
					include WCPC_PLUGIN_DIR . 'admin/views/settings-typography.php';
					break;
				case 'pdf':
					include WCPC_PLUGIN_DIR . 'admin/views/settings-pdf.php';
					break;
				case 'general':
				default:
					include WCPC_PLUGIN_DIR . 'admin/views/settings-general.php';
					break;
			}
			?>

			<?php submit_button( __( 'Einstellungen speichern', 'wc-professional-catalog' ) ); ?>
		</form>
	<?php endif; ?>
</div>
