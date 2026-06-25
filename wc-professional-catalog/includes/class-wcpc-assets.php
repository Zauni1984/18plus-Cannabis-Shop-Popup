<?php
/**
 * Frontend / admin asset handling.
 *
 * @package WCProfessionalCatalog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCPC_Assets {

	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_frontend' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin' ) );
	}

	public static function register_frontend() {
		wp_register_style(
			'wcpc-catalog',
			WCPC_PLUGIN_URL . 'public/css/catalog.css',
			array(),
			WCPC_VERSION
		);
		wp_register_style(
			'wcpc-print',
			WCPC_PLUGIN_URL . 'public/css/print.css',
			array(),
			WCPC_VERSION,
			'print'
		);
		wp_register_script(
			'wcpc-flipbook',
			WCPC_PLUGIN_URL . 'public/js/catalog-flip.js',
			array(),
			WCPC_VERSION,
			true
		);
		wp_register_script(
			'wcpc-lightbox',
			WCPC_PLUGIN_URL . 'public/js/lightbox.js',
			array(),
			WCPC_VERSION,
			true
		);
	}

	public static function enqueue_frontend() {
		wp_enqueue_style( 'wcpc-catalog' );
		wp_enqueue_style( 'wcpc-print' );
		wp_enqueue_script( 'wcpc-flipbook' );

		$settings = WCPC_Plugin::get_settings();
		if ( ! empty( $settings['enable_lightbox'] ) ) {
			wp_enqueue_script( 'wcpc-lightbox' );
		}

		$inline_css = WCPC_Catalog::build_css_variables( $settings );
		wp_add_inline_style( 'wcpc-catalog', $inline_css );
	}

	public static function enqueue_admin( $hook ) {
		if ( false === strpos( (string) $hook, 'wcpc' ) ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style(
			'wcpc-admin',
			WCPC_PLUGIN_URL . 'admin/css/admin.css',
			array(),
			WCPC_VERSION
		);
		wp_enqueue_script(
			'wcpc-admin',
			WCPC_PLUGIN_URL . 'admin/js/admin.js',
			array( 'jquery', 'wp-color-picker' ),
			WCPC_VERSION,
			true
		);
		wp_enqueue_script(
			'wcpc-pdf-generator',
			WCPC_PLUGIN_URL . 'admin/js/pdf-generator.js',
			array( 'jquery' ),
			WCPC_VERSION,
			true
		);
	}
}
