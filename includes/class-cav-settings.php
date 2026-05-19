<?php
/**
 * Settings + defaults for Cannabis Age Verifier.
 *
 * @package CannabisAgeVerifier
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class CAV_Settings {

	public static function defaults() {
		return array(
			'enabled'              => 1,
			'age_preset'           => '18', // '18' | '21' | 'custom'
			'min_age'              => 18,
			'verification_mode'    => 'dob', // 'dob' | 'confirm'
			'cookie_lifetime_days' => 30,
			'scope'                => 'site', // 'shop' | 'site' (default: site, damit Popup auf der allerersten Seite erscheint)
			'redirect_url'         => 'https://www.bundesgesundheitsministerium.de/service/gesetze-und-verordnungen/detail/cannabisgesetz',
			'remember_minor_hours' => 24,
			'headline'             => __( 'Bist Du mindestens 18 Jahre alt?', 'cannabis-age-verifier' ),
			'subline'              => __( 'Dieser Shop verkauft Cannabis-Produkte gemäß Cannabisgesetz (CanG). Der Zugang ist Personen unter 18 Jahren untersagt.', 'cannabis-age-verifier' ),
			'legal_note'           => __( 'Mit der Bestätigung Deines Geburtsdatums erklärst Du Dich mit der Speicherung eines technisch notwendigen Cookies (Art. 6 Abs. 1 lit. f DSGVO) einverstanden. Dein Geburtsdatum wird nicht gespeichert.', 'cannabis-age-verifier' ),
			'privacy_url'          => '',
			'imprint_url'          => '',
			'accent_color'         => '#1aa654',
			'accent_color_2'       => '#0a7d3a',
			'background_opacity'   => 78,
			'logo_url'             => '',
			'enable_animations'    => 1,
			'block_scroll'         => 1,
			'show_on_pages'        => array(), // page IDs explicitly disabled (none by default)
		);
	}

	public static function get_all() {
		$opts = get_option( CAV_OPTION_KEY, array() );

		if ( ! is_array( $opts ) ) {
			$opts = array();
		}

		return wp_parse_args( $opts, self::defaults() );
	}

	public static function get( $key ) {
		$all = self::get_all();
		return isset( $all[ $key ] ) ? $all[ $key ] : null;
	}

	/**
	 * Per-tab field map. Fields outside the submitted tab must NOT be touched,
	 * otherwise text inputs from inactive tabs would be reset to defaults.
	 */
	public static function tab_fields() {
		return array(
			'general' => array(
				'enabled',
				'age_preset',
				'min_age',
				'verification_mode',
				'cookie_lifetime_days',
				'scope',
				'redirect_url',
				'remember_minor_hours',
			),
			'design'  => array(
				'headline',
				'subline',
				'logo_url',
				'accent_color',
				'accent_color_2',
				'background_opacity',
				'enable_animations',
				'block_scroll',
			),
			'legal'   => array(
				'legal_note',
				'privacy_url',
				'imprint_url',
			),
		);
	}

	public static function sanitize( $input ) {
		// Start from the currently-saved values so untouched tabs survive.
		$current = get_option( CAV_OPTION_KEY, array() );
		if ( ! is_array( $current ) ) {
			$current = array();
		}
		$out = wp_parse_args( $current, self::defaults() );

		if ( ! is_array( $input ) ) {
			return $out;
		}

		$tab    = isset( $input['_tab'] ) ? sanitize_key( $input['_tab'] ) : '';
		$map    = self::tab_fields();
		$active = isset( $map[ $tab ] ) ? $map[ $tab ] : null; // null = no marker → allow all (e.g. programmatic update).

		$allow = static function ( $key ) use ( $active ) {
			return null === $active || in_array( $key, $active, true );
		};

		if ( $allow( 'enabled' ) ) {
			$out['enabled'] = ! empty( $input['enabled'] ) ? 1 : 0;
		}
		if ( $allow( 'enable_animations' ) ) {
			$out['enable_animations'] = ! empty( $input['enable_animations'] ) ? 1 : 0;
		}
		if ( $allow( 'block_scroll' ) ) {
			$out['block_scroll'] = ! empty( $input['block_scroll'] ) ? 1 : 0;
		}

		if ( $allow( 'age_preset' ) || $allow( 'min_age' ) ) {
			$preset = isset( $input['age_preset'] ) ? sanitize_key( $input['age_preset'] ) : $out['age_preset'];
			$preset = in_array( $preset, array( '18', '21', 'custom' ), true ) ? $preset : '18';
			$out['age_preset'] = $preset;

			if ( '18' === $preset ) {
				$out['min_age'] = 18;
			} elseif ( '21' === $preset ) {
				$out['min_age'] = 21;
			} else {
				// Custom – aber niemals unter 18 (gesetzlich Cannabisgesetz).
				$out['min_age'] = max( 18, min( 99, absint( $input['min_age'] ?? $out['min_age'] ) ) );
			}
		}

		if ( $allow( 'verification_mode' ) && isset( $input['verification_mode'] ) ) {
			$out['verification_mode'] = in_array( $input['verification_mode'], array( 'dob', 'confirm' ), true ) ? $input['verification_mode'] : 'dob';
		}
		if ( $allow( 'cookie_lifetime_days' ) && isset( $input['cookie_lifetime_days'] ) ) {
			$out['cookie_lifetime_days'] = max( 1, min( 365, absint( $input['cookie_lifetime_days'] ) ) );
		}
		if ( $allow( 'scope' ) && isset( $input['scope'] ) ) {
			$out['scope'] = in_array( $input['scope'], array( 'shop', 'site' ), true ) ? $input['scope'] : 'shop';
		}
		if ( $allow( 'redirect_url' ) && isset( $input['redirect_url'] ) ) {
			$clean = CAV_Security::sanitize_url( $input['redirect_url'] );
			$out['redirect_url'] = ( '' === $clean )
				? 'https://www.bundesgesundheitsministerium.de/service/gesetze-und-verordnungen/detail/cannabisgesetz'
				: $clean;
		}
		if ( $allow( 'remember_minor_hours' ) && isset( $input['remember_minor_hours'] ) ) {
			$out['remember_minor_hours'] = max( 1, min( 720, absint( $input['remember_minor_hours'] ) ) );
		}
		if ( $allow( 'headline' ) && isset( $input['headline'] ) ) {
			$out['headline'] = sanitize_text_field( $input['headline'] );
		}
		if ( $allow( 'subline' ) && isset( $input['subline'] ) ) {
			$out['subline'] = wp_kses_post( $input['subline'] );
		}
		if ( $allow( 'legal_note' ) && isset( $input['legal_note'] ) ) {
			$out['legal_note'] = wp_kses_post( $input['legal_note'] );
		}
		if ( $allow( 'privacy_url' ) && isset( $input['privacy_url'] ) ) {
			$out['privacy_url'] = CAV_Security::sanitize_url( $input['privacy_url'] );
		}
		if ( $allow( 'imprint_url' ) && isset( $input['imprint_url'] ) ) {
			$out['imprint_url'] = CAV_Security::sanitize_url( $input['imprint_url'] );
		}
		if ( $allow( 'accent_color' ) && isset( $input['accent_color'] ) ) {
			$out['accent_color'] = CAV_Security::sanitize_hex_color( $input['accent_color'], '#1aa654' );
		}
		if ( $allow( 'accent_color_2' ) && isset( $input['accent_color_2'] ) ) {
			$out['accent_color_2'] = CAV_Security::sanitize_hex_color( $input['accent_color_2'], '#0a7d3a' );
		}
		if ( $allow( 'background_opacity' ) && isset( $input['background_opacity'] ) ) {
			$out['background_opacity'] = max( 30, min( 100, absint( $input['background_opacity'] ) ) );
		}
		if ( $allow( 'logo_url' ) && isset( $input['logo_url'] ) ) {
			$out['logo_url'] = CAV_Security::sanitize_url( $input['logo_url'] );
		}

		// Never persist the tab marker itself.
		unset( $out['_tab'] );

		return $out;
	}
}
