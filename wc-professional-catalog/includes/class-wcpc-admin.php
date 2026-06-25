<?php
/**
 * Admin settings UI.
 *
 * @package WCProfessionalCatalog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCPC_Admin {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'admin_post_wcpc_save_settings', array( __CLASS__, 'handle_save' ) );
		add_filter( 'plugin_action_links_' . WCPC_PLUGIN_BASENAME, array( __CLASS__, 'plugin_action_links' ) );
	}

	public static function register_menu() {
		add_menu_page(
			__( 'Katalog', 'wc-professional-catalog' ),
			__( 'Katalog', 'wc-professional-catalog' ),
			'manage_woocommerce',
			'wcpc-settings',
			array( __CLASS__, 'render_settings_page' ),
			'dashicons-book-alt',
			58
		);
	}

	public static function register_settings() {
		register_setting( 'wcpc_settings_group', WCPC_OPTION_KEY, array( __CLASS__, 'sanitize_settings' ) );
	}

	public static function plugin_action_links( $links ) {
		$url = admin_url( 'admin.php?page=wcpc-settings' );
		array_unshift( $links, sprintf( '<a href="%s">%s</a>', esc_url( $url ), esc_html__( 'Einstellungen', 'wc-professional-catalog' ) ) );
		return $links;
	}

	public static function render_settings_page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}
		$settings = WCPC_Plugin::get_settings();
		$tabs     = array(
			'general'   => __( 'Allgemein', 'wc-professional-catalog' ),
			'design'    => __( 'Design & Farben', 'wc-professional-catalog' ),
			'images'    => __( 'Bilder', 'wc-professional-catalog' ),
			'typo'      => __( 'Typografie', 'wc-professional-catalog' ),
			'pdf'       => __( 'PDF / Druck', 'wc-professional-catalog' ),
			'shortcuts' => __( 'Shortcodes', 'wc-professional-catalog' ),
		);
		$active   = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'general'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $tabs[ $active ] ) ) {
			$active = 'general';
		}
		include WCPC_PLUGIN_DIR . 'admin/views/settings-page.php';
	}

	public static function handle_save() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'Keine Berechtigung.', 'wc-professional-catalog' ) );
		}
		check_admin_referer( 'wcpc_save_settings' );

		$input = isset( $_POST['wcpc'] ) ? wp_unslash( $_POST['wcpc'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$clean = self::sanitize_settings( $input );

		$existing = get_option( WCPC_OPTION_KEY, array() );
		if ( ! is_array( $existing ) ) {
			$existing = array();
		}
		update_option( WCPC_OPTION_KEY, array_merge( $existing, $clean ) );

		$tab = isset( $_POST['_wcpc_tab'] ) ? sanitize_key( wp_unslash( $_POST['_wcpc_tab'] ) ) : 'general';
		wp_safe_redirect( add_query_arg(
			array(
				'page'    => 'wcpc-settings',
				'tab'     => $tab,
				'updated' => 'true',
			),
			admin_url( 'admin.php' )
		) );
		exit;
	}

	public static function sanitize_settings( $input ) {
		$defaults = WCPC_Plugin::get_defaults();
		$out      = array();
		foreach ( $defaults as $key => $default ) {
			if ( ! isset( $input[ $key ] ) ) {
				// Checkboxes default to 0 when missing.
				if ( in_array( $key, self::checkbox_keys(), true ) ) {
					$out[ $key ] = 0;
				}
				continue;
			}
			$value = $input[ $key ];

			if ( in_array( $key, self::checkbox_keys(), true ) ) {
				$out[ $key ] = $value ? 1 : 0;
				continue;
			}

			if ( in_array( $key, self::color_keys(), true ) ) {
				$out[ $key ] = self::sanitize_color( $value );
				continue;
			}

			if ( in_array( $key, self::int_keys(), true ) ) {
				$out[ $key ] = (int) $value;
				continue;
			}

			$out[ $key ] = sanitize_text_field( $value );
		}
		return $out;
	}

	private static function checkbox_keys() {
		return array(
			'show_brand_filter',
			'show_category_filter',
			'show_tag_filter',
			'show_sku',
			'show_short_desc',
			'show_price_net',
			'show_price_gross',
			'show_unit_price',
			'enable_buy_link',
			'pdf_show_qr',
			'show_gallery',
			'enable_lightbox',
			'pdf_show_gallery',
		);
	}

	private static function color_keys() {
		return array(
			'color_primary',
			'color_secondary',
			'color_accent',
			'color_bg',
			'color_text',
			'color_muted',
			'price_color',
		);
	}

	private static function int_keys() {
		return array(
			'columns',
			'products_per_page',
			'max_products',
			'logo_id',
			'cover_image_id',
			'font_size_title',
			'font_size_body',
			'font_size_price',
			'pdf_columns',
			'pdf_per_page',
			'pdf_margin',
			'reference_volume_ml',
			'reference_weight_g',
			'tax_rate_fallback',
			'gallery_count',
			'pdf_gallery_count',
			'pdf_image_quality',
		);
	}

	private static function sanitize_color( $val ) {
		$val = trim( (string) $val );
		if ( preg_match( '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $val ) ) {
			return $val;
		}
		return '#000000';
	}
}
