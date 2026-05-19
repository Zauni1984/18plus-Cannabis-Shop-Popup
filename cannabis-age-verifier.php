<?php
/**
 * Plugin Name:       Cannabis Age Verifier – 18+ Popup für WooCommerce
 * Plugin URI:        https://blocksocial.eu/plugins/cannabis-age-verifier
 * Description:       Professionelles, DSGVO-konformes 18+ Altersverifikations-Popup für WooCommerce-Cannabis-Shops. Blockiert Minderjährige vollständig und leitet zur Aufklärungsseite des Bundesgesundheitsministeriums weiter.
 * Version:           1.0.3
 * Requires at least: 6.0
 * Tested up to:      6.7
 * Requires PHP:      7.4
 * WC requires at least: 7.0
 * WC tested up to:   9.4
 * Author:            BlockSocial UG (haftungsbeschränkt)
 * Author URI:        https://blocksocial.eu
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       cannabis-age-verifier
 * Domain Path:       /languages
 *
 * @package CannabisAgeVerifier
 * @copyright 2026 BlockSocial UG (haftungsbeschränkt), Kratzmühlstraße 14, 92339 Beilngries, Deutschland
 *
 * Cannabis Age Verifier is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CAV_VERSION', '1.0.3' );
define( 'CAV_PLUGIN_FILE', __FILE__ );
define( 'CAV_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CAV_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CAV_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'CAV_COOKIE_NAME', 'cav_age_verified' );
define( 'CAV_COOKIE_FLAG', 'cav_age_verified_js' );
define( 'CAV_OPTION_KEY', 'cav_settings' );
define( 'CAV_LICENSE_KEY', 'cav_license' );
if ( ! defined( 'CAV_VENDOR_DOMAINS' ) ) {
	define(
		'CAV_VENDOR_DOMAINS',
		serialize(
			array(
				'blocksocial.eu',
				'hanfjack.de',
				'moinkiffers.de',
				'growgarage.de',
			)
		)
	);
}

require_once CAV_PLUGIN_DIR . 'includes/class-cav-security.php';
require_once CAV_PLUGIN_DIR . 'includes/class-cav-license.php';
require_once CAV_PLUGIN_DIR . 'includes/class-cav-settings.php';
require_once CAV_PLUGIN_DIR . 'includes/class-cav-cache.php';
require_once CAV_PLUGIN_DIR . 'includes/class-cav-admin.php';
require_once CAV_PLUGIN_DIR . 'includes/class-cav-frontend.php';
require_once CAV_PLUGIN_DIR . 'includes/class-cav-plugin.php';

register_activation_hook( __FILE__, array( 'CAV_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'CAV_Plugin', 'deactivate' ) );

add_action( 'before_woocommerce_init', function () {
	if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
			'custom_order_tables',
			__FILE__,
			true
		);
	}
} );

add_action( 'plugins_loaded', array( 'CAV_Plugin', 'instance' ) );
