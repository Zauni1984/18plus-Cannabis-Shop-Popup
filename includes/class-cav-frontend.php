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
		add_action( 'wp_head', array( $this, 'inline_popup_css' ), 999 );
		add_action( 'init', array( $this, 'maybe_clear_cookies' ), 1 );
		add_action( 'admin_bar_menu', array( $this, 'admin_bar' ), 80 );
		add_filter( 'language_attributes', array( $this, 'html_class' ), 10, 1 );
		add_filter( 'script_loader_tag', array( $this, 'mark_script_no_optimize' ), 10, 2 );
		add_filter( 'style_loader_tag', array( $this, 'mark_style_no_optimize' ), 10, 2 );
		add_filter( 'rocket_delay_js_exclusions', array( $this, 'rocket_exclude' ) );
		add_filter( 'rocket_exclude_js', array( $this, 'rocket_exclude' ) );
		add_filter( 'rocket_exclude_css', array( $this, 'rocket_exclude' ) );
		add_filter( 'rocket_rucss_inline_content_exclusions', array( $this, 'rocket_exclude' ) );
		add_filter( 'rocket_rucss_safelist', array( $this, 'rucss_safelist' ) );
		add_filter( 'autoptimize_filter_js_exclude', array( $this, 'autoptimize_exclude' ), 10, 1 );
		add_filter( 'autoptimize_filter_css_exclude', array( $this, 'autoptimize_exclude' ), 10, 1 );
		add_filter( 'wpfc_js_minify_excluded_files', array( $this, 'wpfc_exclude' ) );
		add_filter( 'litespeed_optm_js_defer_exc', array( $this, 'rocket_exclude' ) );
		add_filter( 'litespeed_ucss_exc', array( $this, 'rucss_safelist' ) );
		add_filter( 'perfmatters_rucss_excluded_selectors', array( $this, 'rucss_safelist' ) );
	}

	public function mark_style_no_optimize( $tag, $handle ) {
		if ( 'cav-popup' !== $handle ) {
			return $tag;
		}
		if ( false !== strpos( $tag, 'data-cfasync' ) ) {
			return $tag;
		}
		return str_replace(
			'<link ',
			'<link data-cfasync="false" data-no-optimize="1" data-no-minify="1" ',
			$tag
		);
	}

	public function rucss_safelist( $list ) {
		$list   = is_array( $list ) ? $list : array();
		$list[] = '#cav-root';
		$list[] = '.cav-root';
		$list[] = '.cav-card';
		$list[] = '.cav-backdrop';
		$list[] = '.cav-orb';
		$list[] = '.cav-headline';
		$list[] = '.cav-subline';
		$list[] = '.cav-dob';
		$list[] = '.cav-field';
		$list[] = '.cav-btn';
		$list[] = '.cav-legal';
		$list[] = '.cav-meta';
		$list[] = '.cav-badge';
		$list[] = '.cav-spinner';
		$list[] = '.cav-error';
		$list[] = '.cav-anim';
		$list[] = '.cav-form';
		$list[] = '.cav-logo';
		$list[] = '.cav-popup-on';
		$list[] = '.cav-verified';
		return $list;
	}

	/**
	 * Stamp our main popup script with attributes that every major
	 * optimizer/cache plugin recognises as "do not touch".
	 */
	public function mark_script_no_optimize( $tag, $handle ) {
		if ( 'cav-popup' !== $handle ) {
			return $tag;
		}
		if ( false !== strpos( $tag, 'data-cfasync' ) ) {
			return $tag;
		}
		return str_replace(
			'<script ',
			'<script data-cfasync="false" data-no-optimize="1" data-no-defer="1" data-no-minify="1" data-no-async="1" data-wpfc-render="false" ',
			$tag
		);
	}

	public function rocket_exclude( $exclusions ) {
		$exclusions   = is_array( $exclusions ) ? $exclusions : array();
		$exclusions[] = 'cav-popup';
		$exclusions[] = '/cannabis-age-verifier/';
		return $exclusions;
	}

	public function autoptimize_exclude( $exclude ) {
		$additions = 'cav-popup, cannabis-age-verifier';
		return ( '' === (string) $exclude ) ? $additions : $exclude . ', ' . $additions;
	}

	public function wpfc_exclude( $exclusions ) {
		$exclusions   = is_array( $exclusions ) ? $exclusions : array();
		$exclusions[] = 'cav-popup';
		return $exclusions;
	}

	/**
	 * Pre-stamp the <html> element with cav-popup-on so the popup is
	 * gated even if the inline anti-flash JS is stripped/delayed by an
	 * aggressive optimization plugin. The companion JS removes the class
	 * for verified visitors.
	 */
	public function html_class( $attrs ) {
		if ( ! $this->should_show() ) {
			return $attrs;
		}
		if ( preg_match( '/\sclass="([^"]*)"/', $attrs ) ) {
			return preg_replace( '/(\sclass=")([^"]*)(")/', '$1$2 cav-popup-on$3', $attrs, 1 );
		}
		return $attrs . ' class="cav-popup-on"';
	}

	/**
	 * Admin-only convenience: append ?cav-reset-cookie=1 (with nonce) to
	 * delete the verification cookies and re-trigger the popup. Useful
	 * for testing on a domain where 30-day cookies make iterating hard.
	 */
	public function maybe_clear_cookies() {
		if ( empty( $_GET['cav-reset-cookie'] ) || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'cav_clear_cookies' ) ) {
			return;
		}

		$secure = is_ssl();
		$path   = COOKIEPATH ? COOKIEPATH : '/';
		$domain = COOKIE_DOMAIN ? COOKIE_DOMAIN : '';
		$past   = time() - HOUR_IN_SECONDS;

		if ( PHP_VERSION_ID >= 70300 ) {
			$base = array( 'expires' => $past, 'path' => $path, 'domain' => $domain, 'secure' => $secure, 'samesite' => 'Lax' );
			setcookie( CAV_COOKIE_NAME, '', array_merge( $base, array( 'httponly' => true ) ) );
			setcookie( CAV_COOKIE_FLAG, '', array_merge( $base, array( 'httponly' => false ) ) );
		} else {
			setcookie( CAV_COOKIE_NAME, '', $past, $path . '; samesite=Lax', $domain, $secure, true );
			setcookie( CAV_COOKIE_FLAG, '', $past, $path . '; samesite=Lax', $domain, $secure, false );
		}

		// Also clear from current request so should_show() doesn't see them.
		unset( $_COOKIE[ CAV_COOKIE_NAME ], $_COOKIE[ CAV_COOKIE_FLAG ] );

		wp_safe_redirect( remove_query_arg( array( 'cav-reset-cookie', '_wpnonce' ) ) );
		exit;
	}

	public function admin_bar( $bar ) {
		if ( ! current_user_can( 'manage_options' ) || is_admin() || ! is_admin_bar_showing() ) {
			return;
		}

		$bar->add_node(
			array(
				'id'    => 'cav-clear-cookies',
				'title' => '<span class="ab-icon dashicons dashicons-shield-alt" style="top:2px"></span>' . esc_html__( 'Age-Popup zurücksetzen', 'cannabis-age-verifier' ),
				'href'  => wp_nonce_url( add_query_arg( 'cav-reset-cookie', '1' ), 'cav_clear_cookies' ),
				'meta'  => array(
					'title' => __( 'Löscht den Altersverifikations-Cookie, damit das Popup auf dieser Seite erneut erscheint.', 'cannabis-age-verifier' ),
				),
			)
		);
	}

	/**
	 * Should the popup HTML be present on this page?
	 *
	 * Intentionally cache-safe: the cookie is NOT consulted here. Whether
	 * the popup is *visible* is decided client-side from a JS-readable
	 * companion cookie. This way a page can be fully cached and still
	 * gate every fresh visitor on their first page load.
	 *
	 * Skips: admin, AJAX/REST, feeds, robots.txt, sitemaps, 404, known
	 * search-engine and social/preview bots, affiliate feed readers. This
	 * keeps SEO indexing intact and lets partners pull feeds.
	 */
	public function should_show() {
		if ( ! CAV_License::is_active() ) {
			return false;
		}

		$opts = CAV_Settings::get_all();

		if ( empty( $opts['enabled'] ) ) {
			return false;
		}

		if ( is_admin()
			|| wp_doing_ajax()
			|| ( defined( 'REST_REQUEST' ) && REST_REQUEST )
			|| ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST )
			|| ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			return false;
		}

		// XML feeds, robots.txt, sitemaps – never inject the popup there.
		if ( is_feed() || is_robots() || $this->is_sitemap_request() ) {
			return false;
		}

		// Search-engine crawlers and feed readers / preview bots.
		if ( $this->is_bot() ) {
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

		/**
		 * Allow third parties (affiliate plugins, custom integrations, …)
		 * to whitelist specific requests from the age popup.
		 *
		 * @param bool  $show True to render the popup, false to suppress.
		 * @param array $opts Current plugin settings.
		 */
		return (bool) apply_filters( 'cav_should_show_popup', true, $opts );
	}

	private function is_sitemap_request() {
		if ( ! empty( $_GET['sitemap'] ) || ! empty( $_GET['sitemap-stylesheet'] ) ) {
			return true;
		}

		$uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
		if ( '' === $uri ) {
			return false;
		}

		// Core WP sitemaps + Yoast/Rank Math/AIOSEO conventions.
		return (bool) preg_match( '#(/wp-sitemap[^/]*\.xml|/sitemap[_-]?index?\.xml|/[a-z0-9_-]+-sitemap\.xml|/sitemap\.xsl|/sitemap\.xml)$#i', $uri );
	}

	private function is_bot() {
		if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			// No UA. Real browsers always send one; this is almost
			// certainly a script. Return false so we err on the side of
			// SHOWING the popup to anything claiming to be human.
			return false;
		}

		$ua = strtolower( wp_unslash( (string) $_SERVER['HTTP_USER_AGENT'] ) );

		// Substring matches (no generic 'bot'/'spider' etc. – those would
		// false-positive on real consumer devices, e.g. Cubot phones whose
		// UA contains "CUBOT KingKong …").
		$patterns = array(
			// HTTP clients used for server-to-server crawling.
			'curl/', 'wget/', 'python-requests', 'go-http-client', 'libwww-perl',
			'guzzlehttp/', 'axios/', 'okhttp/', 'apache-httpclient', 'java-http-client',
			// Search engines.
			'googlebot', 'bingbot', 'duckduckbot', 'baiduspider', 'yandex',
			'sogou', 'exabot', 'facebot', 'ia_archiver', 'applebot', 'mojeekbot',
			'petalbot', 'seznambot', 'qwantify', 'msnbot', 'adsbot-google',
			'mediapartners-google', 'google-inspectiontool',
			// SEO/monitoring.
			'ahrefsbot', 'semrushbot', 'mj12bot', 'dotbot', 'rogerbot', 'screaming frog',
			'pingdom', 'uptimerobot', 'gtmetrix', 'lighthouse', 'pagespeed', 'chrome-lighthouse',
			'siteimprove', 'serpstatbot', 'sitebulb', 'detectify', 'qualys', 'datadog',
			// Social previews.
			'facebookexternalhit', 'twitterbot', 'linkedinbot', 'whatsapp', 'telegrambot',
			'discordbot', 'slackbot', 'pinterest', 'redditbot', 'embedly', 'iframely', 'skypeuripreview',
			'tumblr', 'vkshare',
			// Feed readers / affiliate aggregators.
			'feedly', 'feedfetcher', 'feedburner', 'feedreader', 'inoreader', 'newsblur',
			'theoldreader', 'flipboard', 'apple-pubsub', 'feedwrangler', 'feedbin',
			'awin', 'tradedoubler', 'commissionjunction', 'cj-affiliate', 'rakutenmarketing',
			'shopstyle', 'skimlinks', 'webgains', 'partnerize', 'admitad',
		);

		foreach ( $patterns as $p ) {
			if ( false !== strpos( $ua, $p ) ) {
				return true;
			}
		}

		// Word-boundary check for the broad terms so e.g. "CUBOT" is NOT
		// flagged as a bot but "compatible; SomeBot/1.0" is.
		if ( preg_match( '/(^|[\s;\/\(])(bot|crawler|spider|crawling)[\s\/;\)]/', $ua ) ) {
			return true;
		}

		/**
		 * Allow site owners to flag additional user-agents as bot.
		 *
		 * @param bool   $is_bot
		 * @param string $ua
		 */
		return (bool) apply_filters( 'cav_is_bot', false, $ua );
	}

	public function preload_critical() {
		if ( ! $this->should_show() ) {
			return;
		}

		$redirect = esc_url( CAV_Settings::get( 'redirect_url' ) );
		$flag     = CAV_COOKIE_FLAG;

		echo "<style id=\"cav-anti-flash\">"
			. "html.cav-popup-on,html.cav-popup-on body{overflow:hidden!important;height:100%!important}"
			. "html.cav-verified,html.cav-verified body{overflow:auto!important;height:auto!important}"
			. "</style>\n";

		echo "<script id=\"cav-anti-flash-js\" data-cfasync=\"false\" data-no-optimize=\"1\" data-no-defer=\"1\" data-no-minify=\"1\">"
			. "(function(){var h=document.documentElement;h.classList.add('cav-popup-on');"
			. "var m=document.cookie.match(/(?:^|;\\s*)" . esc_js( $flag ) . "=([^;]+)/);"
			. "var v=m?m[1]:'';"
			. "if(v==='1'){h.classList.add('cav-verified');h.classList.remove('cav-popup-on');return;}"
			. "if(v==='0'){window.location.replace('" . esc_js( $redirect ) . "');return;}"
			. "function open(){var n=document.getElementById('cav-root');if(!n)return;if(n.parentNode!==document.body){document.body.appendChild(n);}"
			. "if(typeof n.showModal==='function'){if(!n.open){try{n.showModal();}catch(e){n.setAttribute('open','');}}"
			. "n.addEventListener('cancel',function(e){e.preventDefault();},{once:false});}"
			. "else{n.setAttribute('open','');}}"
			. "if(document.readyState!=='loading'){open();}else{document.addEventListener('DOMContentLoaded',open,{once:true});}"
			. "document.addEventListener('submit',function(e){if(e.target&&e.target.id==='cav-form'){e.preventDefault();}},true);"
			. "})();</script>\n";
	}

	/**
	 * Late wp_head output: full popup CSS inline so it wins the cascade
	 * over any theme/plugin rule and so RUCSS-style "Remove Unused CSS"
	 * optimizers cannot strip our design.
	 */
	public function inline_popup_css() {
		if ( ! $this->should_show() ) {
			return;
		}
		$popup_css = $this->get_popup_css();
		if ( '' === $popup_css ) {
			return;
		}
		echo "<style id=\"cav-popup-css\" data-cfasync=\"false\" data-no-optimize=\"1\" data-no-minify=\"1\">"
			. $popup_css // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static plugin asset, no user input.
			. "</style>\n";
	}

	/**
	 * Read + lightly-minify the popup stylesheet for inline output.
	 * Result is cached per-request via static variable.
	 */
	private function get_popup_css() {
		static $cache = null;
		if ( null !== $cache ) {
			return $cache;
		}

		$file = CAV_PLUGIN_DIR . 'assets/css/cav-popup.css';
		if ( ! is_readable( $file ) ) {
			$cache = '';
			return $cache;
		}

		$css = @file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( false === $css ) {
			$cache = '';
			return $cache;
		}

		// Strip /* */ comments.
		$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
		// Collapse whitespace.
		$css = preg_replace( '/\s+/', ' ', $css );
		// Tighten around CSS punctuation.
		$css = preg_replace( '/\s*([{}:;,>~])\s*/', '$1', $css );
		$css = preg_replace( '/;\}/', '}', $css );

		$cache = trim( (string) $css );
		return $cache;
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
		// Note: no wp_verify_nonce() check here. The endpoint is intentionally
		// public — it accepts a self-declared DOB and only sets a soft cookie.
		// A stale nonce (e.g. from a fully cached page after the 12 h tick)
		// would otherwise lock out legitimate visitors. Abuse protection comes
		// from the per-IP rate-limit below + SameSite=Lax cookie + HMAC sig.

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
