<?php
/**
 * Frontend rendering for Cannabis Age Verifier.
 *
 * @package CannabisAgeVerifier
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class CAV_Frontend {

	const REST_NAMESPACE = 'cav/v1';
	const TRANSIENT_RL   = 'cav_rl_';

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'wp_footer', array( $this, 'render_popup' ), 5 );
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		add_action( 'wp_head', array( $this, 'preload_critical' ), 1 );
	}

	/**
	 * Should the popup HTML be present on this page?
	 *
	 * Intentionally cache-safe: the cookie is NOT consulted here. Whether
	 * the popup is *visible* is decided client-side from a JS-readable
	 * companion cookie. This way a page can be fully cached and still
	 * gate every fresh visitor on their first page load.
	 */
	public function should_show() {
		if ( ! CAV_License::is_active() ) {
			return false;
		}

		$opts = CAV_Settings::get_all();

		if ( empty( $opts['enabled'] ) ) {
			return false;
		}

		if ( is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			return false;
		}

		// Don't block the redirect target itself (prevents infinite loops if redirect_url
		// points to an internal info page).
		if ( ! empty( $opts['redirect_url'] ) ) {
			$current = wp_parse_url( home_url( add_query_arg( null, null ) ) );
			$target  = wp_parse_url( $opts['redirect_url'] );
			if (
				is_array( $current ) && is_array( $target )
				&& ! empty( $current['host'] ) && ! empty( $target['host'] )
				&& strtolower( $current['host'] ) === strtolower( $target['host'] )
				&& isset( $current['path'], $target['path'] )
				&& untrailingslashit( $current['path'] ) === untrailingslashit( $target['path'] )
			) {
				return false;
			}
		}

		if ( 'shop' === $opts['scope'] ) {
			if ( ! function_exists( 'is_woocommerce' ) ) {
				return false;
			}
			if ( ! ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) ) {
				return false;
			}
		}

		return true;
	}

	public function preload_critical() {
		if ( ! $this->should_show() ) {
			return;
		}

		// Inline critical CSS + cookie-aware bootstrap.
		// - Hides #cav-root by default so verified visitors never see a flash.
		// - .cav-active reveals the popup; .cav-locked freezes body scroll.
		// - The IIFE reads the *JS-readable* companion cookie set after a
		//   successful adult verification. On '1' the popup stays hidden;
		//   on '0' (minor) the visitor is redirected synchronously, even
		//   from a fully cached page.
		$redirect = esc_url( CAV_Settings::get( 'redirect_url' ) );
		$flag     = CAV_COOKIE_FLAG;

		echo "<style id=\"cav-anti-flash\">"
			. "#cav-root{display:none}"
			. "html.cav-active #cav-root{display:grid}"
			. "html.cav-locked,html.cav-locked body{overflow:hidden!important;height:100%!important}"
			. "</style>\n";

		echo "<script id=\"cav-anti-flash-js\">(function(){var m=document.cookie.match(/(?:^|;\\s*)" . esc_js( $flag ) . "=([^;]+)/);var v=m?m[1]:'';if(v==='1'){return;}if(v==='0'){window.location.replace('" . esc_js( $redirect ) . "');return;}document.documentElement.classList.add('cav-active','cav-locked');})();</script>\n";
	}

	public function enqueue() {
		if ( ! $this->should_show() ) {
			return;
		}

		wp_enqueue_style(
			'cav-popup',
			CAV_PLUGIN_URL . 'assets/css/cav-popup.css',
			array(),
			CAV_VERSION
		);

		wp_enqueue_script(
			'cav-popup',
			CAV_PLUGIN_URL . 'assets/js/cav-popup.js',
			array(),
			CAV_VERSION,
			true
		);

		$opts = CAV_Settings::get_all();

		wp_localize_script(
			'cav-popup',
			'CAV_DATA',
			array(
				'restUrl'     => esc_url_raw( rest_url( self::REST_NAMESPACE . '/verify' ) ),
				'nonce'       => wp_create_nonce( 'wp_rest' ),
				'redirectUrl' => esc_url_raw( $opts['redirect_url'] ),
				'minAge'      => (int) $opts['min_age'],
				'mode'        => $opts['verification_mode'],
				'blockScroll' => (bool) $opts['block_scroll'],
				'animations'  => (bool) $opts['enable_animations'],
				'flagCookie'  => CAV_COOKIE_FLAG,
				'i18n'        => array(
					'invalidDate'  => __( 'Bitte ein gültiges Geburtsdatum eingeben.', 'cannabis-age-verifier' ),
					'tooYoung'     => __( 'Du musst mindestens %d Jahre alt sein, um diesen Shop zu besuchen.', 'cannabis-age-verifier' ),
					'networkError' => __( 'Verbindungsfehler. Bitte erneut versuchen.', 'cannabis-age-verifier' ),
					'rateLimited'  => __( 'Zu viele Versuche. Bitte später erneut versuchen.', 'cannabis-age-verifier' ),
					'verifying'    => __( 'Prüfe …', 'cannabis-age-verifier' ),
				),
			)
		);
	}

	public function register_routes() {
		register_rest_route(
			self::REST_NAMESPACE,
			'/verify',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'rest_verify' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'mode'    => array(
							'required'          => true,
							'sanitize_callback' => 'sanitize_key',
						),
						'year'    => array( 'sanitize_callback' => 'absint' ),
						'month'   => array( 'sanitize_callback' => 'absint' ),
						'day'     => array( 'sanitize_callback' => 'absint' ),
						'confirm' => array( 'sanitize_callback' => 'sanitize_key' ),
					),
				),
			)
		);
	}

	public function rest_verify( WP_REST_Request $request ) {
		$nonce = $request->get_header( 'X-WP-Nonce' );
		if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new WP_Error( 'cav_bad_nonce', __( 'Ungültiger Sicherheitsschlüssel.', 'cannabis-age-verifier' ), array( 'status' => 403 ) );
		}

		if ( ! $this->check_rate_limit() ) {
			return new WP_Error( 'cav_rate_limited', __( 'Zu viele Versuche.', 'cannabis-age-verifier' ), array( 'status' => 429 ) );
		}

		$opts     = CAV_Settings::get_all();
		$min_age  = (int) $opts['min_age'];
		$mode     = $request->get_param( 'mode' );
		$is_adult = false;

		if ( 'dob' === $mode ) {
			$age = CAV_Security::calc_age_from_dob(
				(int) $request->get_param( 'year' ),
				(int) $request->get_param( 'month' ),
				(int) $request->get_param( 'day' )
			);
			if ( $age < 0 ) {
				return new WP_Error( 'cav_invalid_date', __( 'Ungültiges Datum.', 'cannabis-age-verifier' ), array( 'status' => 400 ) );
			}
			$is_adult = ( $age >= $min_age );
		} elseif ( 'confirm' === $mode ) {
			$is_adult = ( 'yes' === $request->get_param( 'confirm' ) );
		} else {
			return new WP_Error( 'cav_invalid_mode', __( 'Ungültiger Modus.', 'cannabis-age-verifier' ), array( 'status' => 400 ) );
		}

		$band     = $is_adult ? 'adult' : 'minor';
		$issued   = time();
		$token    = CAV_Security::generate_cookie_token( $band, $issued );
		$lifetime = $is_adult
			? (int) $opts['cookie_lifetime_days'] * DAY_IN_SECONDS
			: (int) $opts['remember_minor_hours'] * HOUR_IN_SECONDS;

		$this->set_cookies( $token, $issued + $lifetime, $is_adult );

		return rest_ensure_response(
			array(
				'verified' => $is_adult,
				'redirect' => $is_adult ? '' : $opts['redirect_url'],
			)
		);
	}

	private function set_cookies( $token, $expires, $is_adult ) {
		$secure = is_ssl();
		$path   = COOKIEPATH ? COOKIEPATH : '/';
		$domain = COOKIE_DOMAIN ? COOKIE_DOMAIN : '';
		$flag   = $is_adult ? '1' : '0';

		if ( PHP_VERSION_ID >= 70300 ) {
			$base = array(
				'expires'  => $expires,
				'path'     => $path,
				'domain'   => $domain,
				'secure'   => $secure,
				'samesite' => 'Lax',
			);

			// Signed, server-trusted cookie (HttpOnly).
			setcookie( CAV_COOKIE_NAME, $token, array_merge( $base, array( 'httponly' => true ) ) );

			// JS-readable companion flag – no sensitive data, just '1' or '0'.
			setcookie( CAV_COOKIE_FLAG, $flag, array_merge( $base, array( 'httponly' => false ) ) );
		} else {
			setcookie( CAV_COOKIE_NAME, $token, $expires, $path . '; samesite=Lax', $domain, $secure, true );
			setcookie( CAV_COOKIE_FLAG, $flag, $expires, $path . '; samesite=Lax', $domain, $secure, false );
		}
	}

	private function check_rate_limit() {
		$key   = self::TRANSIENT_RL . CAV_Security::get_client_ip_hash();
		$count = (int) get_transient( $key );

		if ( $count >= 12 ) {
			return false;
		}

		set_transient( $key, $count + 1, MINUTE_IN_SECONDS * 5 );

		return true;
	}

	public function render_popup() {
		if ( ! $this->should_show() ) {
			return;
		}

		$opts    = CAV_Settings::get_all();
		$bg      = max( 30, min( 100, (int) $opts['background_opacity'] ) ) / 100;
		$accent  = CAV_Security::sanitize_hex_color( $opts['accent_color'], '#1aa654' );
		$accent2 = CAV_Security::sanitize_hex_color( $opts['accent_color_2'], '#0a7d3a' );
		$tpl     = CAV_PLUGIN_DIR . 'templates/popup.php';

		if ( file_exists( $tpl ) ) {
			include $tpl;
		}
	}
}
