<?php
/**
 * Main plugin class.
 *
 * @package WCProfessionalCatalog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCPC_Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var WCPC_Plugin
	 */
	private static $instance = null;

	/**
	 * Default settings.
	 *
	 * @var array
	 */
	private static $defaults = array(
		// Layout / general.
		'layout'              => 'grid',          // grid | flipbook | list.
		'columns'             => 3,
		'products_per_page'   => 12,
		'show_brand_filter'   => 1,
		'show_category_filter'=> 1,
		'show_tag_filter'     => 1,
		'show_sku'            => 1,
		'show_short_desc'     => 1,
		'show_price_net'      => 1,
		'show_price_gross'    => 1,
		'show_unit_price'     => 1,
		'enable_buy_link'     => 1,
		'buy_button_text'     => 'Jetzt kaufen',

		// Branding.
		'logo_id'             => 0,
		'cover_title'         => 'Produktkatalog',
		'cover_subtitle'      => '',
		'footer_text'         => '',

		// Colors.
		'color_primary'       => '#1f7a3a',
		'color_secondary'     => '#0c2d18',
		'color_accent'        => '#d4af37',
		'color_bg'            => '#ffffff',
		'color_text'          => '#222222',
		'color_muted'         => '#777777',

		// Typography - title.
		'font_family_title'   => 'Helvetica',
		'font_size_title'     => 18,
		'font_weight_title'   => '700',
		'font_style_title'    => 'normal',

		// Typography - body / short description.
		'font_family_body'    => 'Helvetica',
		'font_size_body'      => 11,
		'font_weight_body'    => '400',
		'font_style_body'     => 'normal',

		// Typography - price.
		'font_family_price'   => 'Helvetica',
		'font_size_price'     => 14,
		'font_weight_price'   => '700',
		'font_style_price'    => 'normal',
		'price_color'         => '#1f7a3a',

		// PDF.
		'pdf_page_size'       => 'A4',
		'pdf_orientation'     => 'portrait',
		'pdf_columns'         => 3,
		'pdf_per_page'        => 9,
		'pdf_margin'          => 12,
		'pdf_show_qr'         => 1,

		// Unit / base price.
		'reference_volume_ml' => 1000, // 1 L reference.
		'reference_weight_g'  => 1000, // 1 kg reference.
		'tax_rate_fallback'   => 19,
	);

	/**
	 * Instance accessor.
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'register_rewrites' ) );
		add_filter( 'query_vars', array( $this, 'register_query_vars' ) );
		add_action( 'template_redirect', array( $this, 'maybe_handle_endpoint' ) );

		add_action( 'admin_notices', array( $this, 'maybe_show_dependency_notice' ) );

		// Boot subsystems.
		WCPC_Assets::init();
		WCPC_Shortcode::init();
		WCPC_Admin::init();
	}

	/**
	 * Returns plugin settings merged with defaults.
	 *
	 * @return array
	 */
	public static function get_settings() {
		$saved = get_option( WCPC_OPTION_KEY, array() );
		if ( ! is_array( $saved ) ) {
			$saved = array();
		}
		return array_merge( self::$defaults, $saved );
	}

	/**
	 * Returns a single setting value.
	 *
	 * @param string $key Setting key.
	 * @param mixed  $fallback Optional fallback if missing.
	 * @return mixed
	 */
	public static function get_setting( $key, $fallback = null ) {
		$settings = self::get_settings();
		if ( array_key_exists( $key, $settings ) ) {
			return $settings[ $key ];
		}
		return $fallback;
	}

	/**
	 * Returns plugin defaults.
	 *
	 * @return array
	 */
	public static function get_defaults() {
		return self::$defaults;
	}

	/**
	 * Activation hook.
	 */
	public static function on_activate() {
		if ( false === get_option( WCPC_OPTION_KEY ) ) {
			add_option( WCPC_OPTION_KEY, self::$defaults );
		}
		// Register endpoint and flush.
		self::register_rewrites_static();
		flush_rewrite_rules();
	}

	/**
	 * Deactivation hook.
	 */
	public static function on_deactivate() {
		flush_rewrite_rules();
	}

	/**
	 * Load translations.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'wc-professional-catalog', false, dirname( WCPC_PLUGIN_BASENAME ) . '/languages' );
	}

	/**
	 * Register pretty rewrites for /catalog/pdf, /catalog/print etc.
	 */
	public function register_rewrites() {
		self::register_rewrites_static();
	}

	/**
	 * Static version usable from activation hook.
	 */
	private static function register_rewrites_static() {
		add_rewrite_rule(
			'^wc-catalog/(pdf|print|flipbook)/?$',
			'index.php?wcpc_view=$matches[1]',
			'top'
		);
		add_rewrite_rule(
			'^wc-catalog/(pdf|print|flipbook)/(brand|category|tag)/([^/]+)/?$',
			'index.php?wcpc_view=$matches[1]&wcpc_filter=$matches[2]&wcpc_term=$matches[3]',
			'top'
		);
	}

	/**
	 * Add query vars used by the plugin endpoints.
	 *
	 * @param array $vars Existing query vars.
	 * @return array
	 */
	public function register_query_vars( $vars ) {
		$vars[] = 'wcpc_view';
		$vars[] = 'wcpc_filter';
		$vars[] = 'wcpc_term';
		return $vars;
	}

	/**
	 * Handle /wc-catalog/(pdf|print|flipbook) endpoints.
	 */
	public function maybe_handle_endpoint() {
		$view = get_query_var( 'wcpc_view' );
		if ( ! $view ) {
			return;
		}

		$filter = get_query_var( 'wcpc_filter' );
		$term   = get_query_var( 'wcpc_term' );

		$args            = array();
		$allowed_filters = array( 'brand', 'category', 'tag' );
		if ( $filter && $term && in_array( $filter, $allowed_filters, true ) ) {
			$args[ $filter ] = sanitize_title( $term );
		}

		$allowed_views = array( 'pdf', 'print', 'flipbook' );
		if ( ! in_array( $view, $allowed_views, true ) ) {
			return;
		}

		switch ( $view ) {
			case 'pdf':
				WCPC_PDF::stream_catalog( $args );
				exit;
			case 'print':
				WCPC_Catalog::render_print_page( $args );
				exit;
			case 'flipbook':
				WCPC_Catalog::render_flipbook_page( $args );
				exit;
		}
	}

	/**
	 * Notify the admin if WooCommerce is missing or the PDF dependency cannot be found.
	 */
	public function maybe_show_dependency_notice() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}
		if ( ! class_exists( 'WooCommerce' ) ) {
			echo '<div class="notice notice-error"><p><strong>WC Professional Catalog:</strong> ';
			esc_html_e( 'WooCommerce ist nicht aktiv. Bitte WooCommerce installieren und aktivieren.', 'wc-professional-catalog' );
			echo '</p></div>';
		}
		if ( ! WCPC_PDF::is_dompdf_available() ) {
			echo '<div class="notice notice-warning is-dismissible"><p><strong>WC Professional Catalog:</strong> ';
			printf(
				/* translators: %s: composer command */
				esc_html__( 'PDF-Export läuft im Fallback-Modus (HTML zum Drucken). Für native PDFs bitte im Plugin-Ordner %s ausführen.', 'wc-professional-catalog' ),
				'<code>composer require dompdf/dompdf</code>'
			);
			echo '</p></div>';
		}
	}
}
