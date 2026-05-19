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

		if ( $this->has_valid_cookie() ) {
			return false;
		}

		return true;
	}

	public function has_valid_cookie() {
		if ( empty( $_COOKIE[ CAV_COOKIE_NAME ] ) ) {
			return false;
		}

		$raw  = wp_unslash( $_COOKIE[ CAV_COOKIE_NAME ] );
		$data = CAV_Security::parse_cookie_token( $raw );

		if ( ! $data || 'adult' !== $data['age_band'] ) {
			return false;
		}

		$opts        = CAV_Settings::get_all();
		$max_age_sec = (int) $opts['cookie_lifetime_days'] * DAY_IN_SECONDS;

		if ( $data['issued_at'] + $max_age_sec < time() ) {
			return false;
		}

		return true;
	}

	public function has_minor_cookie() {
		if ( empty( $_COOKIE[ CAV_COOKIE_NAME ] ) ) {
			return false;
		}

		$data = CAV_Security::parse_cookie_token( wp_unslash( $_COOKIE[ CAV_COOKIE_NAME ] ) );

		if ( ! $data || 'minor' !== $data['age_band'] ) {
			return false;
		}

		$opts      = CAV_Settings::get_all();
		$max_age_s = (int) $opts['remember_minor_hours'] * HOUR_IN_SECONDS;

		return $data['issued_at'] + $max_age_s >= time();
	}

	public function preload_critical() {
		if ( ! $this->should_show() ) {
			return;
		}
		// Inline a tiny anti-flash style so the page never renders before the popup mounts.
		echo "<style id=\"cav-anti-flash\">html.cav-loading body{overflow:hidden!important}html.cav-loading body::before{content:'';position:fixed;inset:0;background:#0c1611;z-index:2147483646;opacity:.98}</style>\n";
		echo "<script id=\"cav-anti-flash-js\">document.documentElement.classList.add('cav-loading');</script>\n";
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
				'restUrl'        => esc_url_raw( rest_url( self::REST_NAMESPACE . '/verify' ) ),
				'nonce'          => wp_create_nonce( 'wp_rest' ),
				'redirectUrl'    => esc_url_raw( $opts['redirect_url'] ),
				'minAge'         => (int) $opts['min_age'],
				'mode'           => $opts['verification_mode'],
				'blockScroll'    => (bool) $opts['block_scroll'],
				'animations'     => (bool) $opts['enable_animations'],
				'i18n'           => array(
					'invalidDate'      => __( 'Bitte ein gültiges Geburtsdatum eingeben.', 'cannabis-age-verifier' ),
					'tooYoung'         => __( 'Du musst mindestens %d Jahre alt sein, um diesen Shop zu besuchen.', 'cannabis-age-verifier' ),
					'networkError'     => __( 'Verbindungsfehler. Bitte erneut versuchen.', 'cannabis-age-verifier' ),
					'rateLimited'      => __( 'Zu viele Versuche. Bitte später erneut versuchen.', 'cannabis-age-verifier' ),
					'verifying'        => __( 'Prüfe …', 'cannabis-age-verifier' ),
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
						'mode' => array(
							'required'          => true,
							'sanitize_callback' => 'sanitize_key',
						),
						'year' => array(
							'sanitize_callback' => 'absint',
						),
						'month' => array(
							'sanitize_callback' => 'absint',
						),
						'day' => array(
							'sanitize_callback' => 'absint',
						),
						'confirm' => array(
							'sanitize_callback' => 'sanitize_key',
						),
					),
				),
			)
		);
	}

	public function rest_verify( WP_REST_Request $request ) {
		// Nonce check via REST cookie auth.
		$nonce = $request->get_header( 'X-WP-Nonce' );
		if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new WP_Error( 'cav_bad_nonce', __( 'Ungültiger Sicherheitsschlüssel.', 'cannabis-age-verifier' ), array( 'status' => 403 ) );
		}

		if ( ! $this->check_rate_limit() ) {
			return new WP_Error( 'cav_rate_limited', __( 'Zu viele Versuche.', 'cannabis-age-verifier' ), array( 'status' => 429 ) );
		}

		$opts    = CAV_Settings::get_all();
		$min_age = (int) $opts['min_age'];
		$mode    = $request->get_param( 'mode' );
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
			$confirm  = $request->get_param( 'confirm' );
			$is_adult = ( 'yes' === $confirm );
		} else {
			return new WP_Error( 'cav_invalid_mode', __( 'Ungültiger Modus.', 'cannabis-age-verifier' ), array( 'status' => 400 ) );
		}

		$band     = $is_adult ? 'adult' : 'minor';
		$issued   = time();
		$token    = CAV_Security::generate_cookie_token( $band, $issued );
		$lifetime = $is_adult
			? (int) $opts['cookie_lifetime_days'] * DAY_IN_SECONDS
			: (int) $opts['remember_minor_hours'] * HOUR_IN_SECONDS;

		$this->set_cookie( $token, $issued + $lifetime );

		$response = array(
			'verified' => $is_adult,
			'redirect' => $is_adult ? '' : $opts['redirect_url'],
		);

		return rest_ensure_response( $response );
	}

	private function set_cookie( $value, $expires ) {
		$secure   = is_ssl();
		$samesite = 'Lax';
		$path     = COOKIEPATH ? COOKIEPATH : '/';
		$domain   = COOKIE_DOMAIN ? COOKIE_DOMAIN : '';

		if ( PHP_VERSION_ID >= 70300 ) {
			setcookie(
				CAV_COOKIE_NAME,
				$value,
				array(
					'expires'  => $expires,
					'path'     => $path,
					'domain'   => $domain,
					'secure'   => $secure,
					'httponly' => true,
					'samesite' => $samesite,
				)
			);
		} else {
			setcookie(
				CAV_COOKIE_NAME,
				$value,
				$expires,
				$path . '; samesite=' . $samesite,
				$domain,
				$secure,
				true
			);
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

		$opts        = CAV_Settings::get_all();
		$bg          = max( 30, min( 100, (int) $opts['background_opacity'] ) ) / 100;
		$accent      = CAV_Security::sanitize_hex_color( $opts['accent_color'], '#1aa654' );
		$accent2     = CAV_Security::sanitize_hex_color( $opts['accent_color_2'], '#0a7d3a' );
		$is_minor    = $this->has_minor_cookie();
		$tpl         = CAV_PLUGIN_DIR . 'templates/popup.php';

		if ( file_exists( $tpl ) ) {
			include $tpl;
		}
	}
}
