<?php
/**
 * Plugin Name:       WC Inventory Sync
 * Plugin URI:        https://github.com/zauni1984/18plus-cannabis-shop-popup
 * Description:        Synchronisiert die Lagerbestände (Stock) mehrerer WooCommerce-Shops in nahezu Echtzeit. Zuordnung per SKU, ein wählbarer Hauptshop (Master) für die erste Voll-Synchronisation, jederzeit änderbar. Einfache und variable Produkte werden unterstützt; Produkte, die nur in einem Shop existieren, werden ignoriert.
 * Version:           1.2.0
 * Author:            Stefan Z
 * License:           MIT
 * Text Domain:       wc-inventory-sync
 * Domain Path:       /languages
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * WC requires at least: 5.0
 * WC tested up to:   9.9
 *
 * @package WC_Inventory_Sync
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direktzugriff verhindern.
}

// -----------------------------------------------------------------------------
// Konstanten
// -----------------------------------------------------------------------------
define( 'WCIS_VERSION', '1.2.0' );
define( 'WCIS_FILE', __FILE__ );
define( 'WCIS_PATH', plugin_dir_path( __FILE__ ) );
define( 'WCIS_URL', plugin_dir_url( __FILE__ ) );
define( 'WCIS_BASENAME', plugin_basename( __FILE__ ) );
define( 'WCIS_REST_NS', 'wc-inventory-sync/v1' );
define( 'WCIS_OPT', 'wcis_settings' );

// -----------------------------------------------------------------------------
// HPOS-Kompatibilität (High-Performance Order Storage) deklarieren.
// -----------------------------------------------------------------------------
add_action(
	'before_woocommerce_init',
	static function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WCIS_FILE, true );
		}
	}
);

// -----------------------------------------------------------------------------
// Autoloader für die Plugin-Klassen (includes/class-wcis-*.php).
// -----------------------------------------------------------------------------
spl_autoload_register(
	static function ( $class ) {
		if ( strpos( $class, 'WCIS_' ) !== 0 ) {
			return;
		}
		$file = 'class-' . strtolower( str_replace( '_', '-', $class ) ) . '.php';
		$path = WCIS_PATH . 'includes/' . $file;
		if ( is_readable( $path ) ) {
			require_once $path;
		}
	}
);

// -----------------------------------------------------------------------------
// Aktivierung / Deaktivierung
// -----------------------------------------------------------------------------
register_activation_hook( __FILE__, array( 'WCIS_Install', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WCIS_Install', 'deactivate' ) );

// -----------------------------------------------------------------------------
// Bootstrap – erst laden, wenn WooCommerce verfügbar ist.
// -----------------------------------------------------------------------------
add_action(
	'plugins_loaded',
	static function () {
		load_plugin_textdomain( 'wc-inventory-sync', false, dirname( WCIS_BASENAME ) . '/languages' );

		if ( ! class_exists( 'WooCommerce' ) ) {
			add_action(
				'admin_notices',
				static function () {
					echo '<div class="notice notice-error"><p>';
					echo esc_html__( 'WC Inventory Sync benötigt WooCommerce. Bitte WooCommerce installieren und aktivieren.', 'wc-inventory-sync' );
					echo '</p></div>';
				}
			);
			return;
		}

		WCIS_Plugin::instance()->init();
	}
);
