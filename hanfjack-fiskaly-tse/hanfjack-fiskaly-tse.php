<?php
/**
 * Plugin Name: Hanfjack TSE (fiskaly) für WCPOS
 * Plugin URI:  https://hanfjack.de
 * Description: KassenSichV-konforme Signatur von WCPOS-Kassenbelegen über die fiskaly Cloud-TSE (SIGN DE v2): Transaktionen starten/abschließen, Stornos, Belegdaten für den Bon, TSE-Export.
 * Version:     0.2.0
 * Author:      Hanfjack
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Requires Plugins: woocommerce
 * Text Domain: hanfjack-fiskaly-tse
 */

defined( 'ABSPATH' ) || exit;

define( 'HJ_TSE_VERSION', '0.2.0' );
define( 'HJ_TSE_FILE', __FILE__ );
define( 'HJ_TSE_DIR', plugin_dir_path( __FILE__ ) );
define( 'HJ_TSE_OPTION', 'hj_tse_settings' );

require_once HJ_TSE_DIR . 'includes/class-hj-tse-exception.php';
require_once HJ_TSE_DIR . 'includes/class-hj-tse-settings.php';
require_once HJ_TSE_DIR . 'includes/class-hj-tse-log.php';
require_once HJ_TSE_DIR . 'includes/class-hj-tse-client.php';
require_once HJ_TSE_DIR . 'includes/class-hj-tse-amounts.php';
require_once HJ_TSE_DIR . 'includes/class-hj-tse-orders.php';
require_once HJ_TSE_DIR . 'includes/class-hj-tse-receipt.php';
require_once HJ_TSE_DIR . 'includes/class-hj-tse-dsfinvk.php';
require_once HJ_TSE_DIR . 'includes/class-hj-tse-cashbook.php';
require_once HJ_TSE_DIR . 'includes/class-hj-tse-closing.php';
require_once HJ_TSE_DIR . 'includes/class-hj-tse-rest.php';
require_once HJ_TSE_DIR . 'includes/class-hj-tse-admin.php';

register_activation_hook( __FILE__, array( 'HJ_TSE_Log', 'install' ) );
register_deactivation_hook(
	__FILE__,
	static function () {
		wp_clear_scheduled_hook( 'hj_tse_daily_closing' );
		wp_clear_scheduled_hook( 'hj_tse_poll_closings' );
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
		HJ_TSE_Log::maybe_upgrade();
		HJ_TSE_Orders::init();
		HJ_TSE_Receipt::init();
		HJ_TSE_Closing::init();
		HJ_TSE_Rest::init();
		if ( is_admin() ) {
			HJ_TSE_Admin::init();
		}
	},
	20
);
