<?php
/**
 * Plugin bootstrap.
 *
 * @package CannabisAgeVerifier
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class CAV_Plugin {

	private static $instance = null;
	public $admin;
	public $frontend;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		load_plugin_textdomain( 'cannabis-age-verifier', false, dirname( CAV_PLUGIN_BASENAME ) . '/languages' );

		if ( is_admin() ) {
			$this->admin = new CAV_Admin();
		}

		$this->frontend = new CAV_Frontend();
	}

	public static function activate() {
		if ( false === get_option( CAV_OPTION_KEY ) ) {
			add_option( CAV_OPTION_KEY, CAV_Settings::defaults(), '', false );
		}

		// Ensure HMAC secret exists from day one so cookies are signed.
		CAV_Security::get_secret();
	}

	public static function deactivate() {
		delete_transient( CAV_License::TRANSIENT_KEY );
	}
}
