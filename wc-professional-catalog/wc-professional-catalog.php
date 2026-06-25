<?php
/**
 * Plugin Name: WC Professional Catalog
 * Plugin URI:  https://example.com/wc-professional-catalog
 * Description: Professioneller Produktkatalog für WooCommerce mit Online-Flipbook, PDF-Export, Druckansicht, Marken-/Kategorie-/Tag-Filter, brutto/netto Preisen, Grundpreis-Berechnung und voller Design-Steuerung.
 * Version:     1.0.2
 * Author:      Zauni Digital
 * Text Domain: wc-professional-catalog
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires at least: 6.0
 * WC requires at least: 7.0
 * WC tested up to: 9.4
 *
 * @package WCProfessionalCatalog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WCPC_VERSION', '1.0.2' );
define( 'WCPC_PLUGIN_FILE', __FILE__ );
define( 'WCPC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WCPC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WCPC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'WCPC_OPTION_KEY', 'wcpc_settings' );

require_once WCPC_PLUGIN_DIR . 'includes/class-wcpc-plugin.php';
require_once WCPC_PLUGIN_DIR . 'includes/class-wcpc-price.php';
require_once WCPC_PLUGIN_DIR . 'includes/class-wcpc-catalog.php';
require_once WCPC_PLUGIN_DIR . 'includes/class-wcpc-shortcode.php';
require_once WCPC_PLUGIN_DIR . 'includes/class-wcpc-assets.php';
require_once WCPC_PLUGIN_DIR . 'includes/class-wcpc-pdf.php';
require_once WCPC_PLUGIN_DIR . 'includes/class-wcpc-admin.php';

register_activation_hook( __FILE__, array( 'WCPC_Plugin', 'on_activate' ) );
register_deactivation_hook( __FILE__, array( 'WCPC_Plugin', 'on_deactivate' ) );

add_action( 'plugins_loaded', array( 'WCPC_Plugin', 'instance' ) );

// Declare HPOS (High-Performance Order Storage) compatibility.
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( '\\Automattic\\WooCommerce\\Utilities\\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);
