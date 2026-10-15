<?php
/**
 * Plugin Name: Hanfjack Zahlungsabgleich (N26 via Enable Banking)
 * Plugin URI:  https://hanfjack.de
 * Description: Liest Zahlungseingänge des Geschäftskontos über PSD2 (Enable Banking) und markiert passende Vorkasse-Bestellungen automatisch als bezahlt.
 * Version:     0.1.0
 * Author:      Hanfjack
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Requires Plugins: woocommerce
 * Text Domain: hanfjack-bank-sync
 */

defined( 'ABSPATH' ) || exit;

define( 'HJB_VERSION', '0.1.0' );
define( 'HJB_FILE', __FILE__ );
define( 'HJB_DIR', plugin_dir_path( __FILE__ ) );
define( 'HJB_OPTION', 'hjb_settings' );

require_once HJB_DIR . 'includes/class-hjb-exception.php';
require_once HJB_DIR . 'includes/class-hjb-settings.php';
require_once HJB_DIR . 'includes/class-hjb-store.php';
require_once HJB_DIR . 'includes/class-hjb-enablebanking.php';
require_once HJB_DIR . 'includes/class-hjb-matcher.php';
require_once HJB_DIR . 'includes/class-hjb-sync.php';
require_once HJB_DIR . 'includes/class-hjb-rest.php';
require_once HJB_DIR . 'includes/class-hjb-admin.php';

register_activation_hook( __FILE__, array( 'HJB_Store', 'install' ) );
register_deactivation_hook(
	__FILE__,
	static function () {
		wp_clear_scheduled_hook( 'hjb_sync' );
		wp_clear_scheduled_hook( 'hjb_consent_check' );
	}
);

add_action(
	'before_woocommerce_init',
	static function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

add_action(
	'plugins_loaded',
	static function () {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		HJB_Store::maybe_upgrade();
		HJB_Sync::init();
		HJB_Rest::init();
		if ( is_admin() ) {
			HJB_Admin::init();
		}
	},
	20
);
