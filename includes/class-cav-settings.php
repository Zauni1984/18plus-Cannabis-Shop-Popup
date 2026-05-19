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
			'scope'                => 'shop', // 'shop' | 'site'
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

	public static function sanitize( $input ) {
		$out = self::defaults();

		if ( ! is_array( $input ) ) {
			return $out;
		}

		$out['enabled']              = ! empty( $input['enabled'] ) ? 1 : 0;
		$out['enable_animations']    = ! empty( $input['enable_animations'] ) ? 1 : 0;
		$out['block_scroll']         = ! empty( $input['block_scroll'] ) ? 1 : 0;

		$preset = isset( $input['age_preset'] ) ? sanitize_key( $input['age_preset'] ) : '18';
		$preset = in_array( $preset, array( '18', '21', 'custom' ), true ) ? $preset : '18';
		$out['age_preset'] = $preset;

		if ( '18' === $preset ) {
			$out['min_age'] = 18;
		} elseif ( '21' === $preset ) {
			$out['min_age'] = 21;
		} else {
			// Custom – aber niemals unter 18 (gesetzlich Cannabisgesetz).
			$out['min_age'] = max( 18, min( 99, absint( $input['min_age'] ?? 18 ) ) );
		}
		$out['verification_mode']    = in_array( ( $input['verification_mode'] ?? '' ), array( 'dob', 'confirm' ), true ) ? $input['verification_mode'] : 'dob';
		$out['cookie_lifetime_days'] = max( 1, min( 365, absint( $input['cookie_lifetime_days'] ?? 30 ) ) );
		$out['scope']                = in_array( ( $input['scope'] ?? '' ), array( 'shop', 'site' ), true ) ? $input['scope'] : 'shop';
		$out['redirect_url']         = CAV_Security::sanitize_url( $input['redirect_url'] ?? '' );
		$out['remember_minor_hours'] = max( 1, min( 720, absint( $input['remember_minor_hours'] ?? 24 ) ) );
		$out['headline']             = sanitize_text_field( $input['headline'] ?? '' );
		$out['subline']              = wp_kses_post( $input['subline'] ?? '' );
		$out['legal_note']           = wp_kses_post( $input['legal_note'] ?? '' );
		$out['privacy_url']          = CAV_Security::sanitize_url( $input['privacy_url'] ?? '' );
		$out['imprint_url']          = CAV_Security::sanitize_url( $input['imprint_url'] ?? '' );
		$out['accent_color']         = CAV_Security::sanitize_hex_color( $input['accent_color'] ?? '', '#1aa654' );
		$out['accent_color_2']       = CAV_Security::sanitize_hex_color( $input['accent_color_2'] ?? '', '#0a7d3a' );
		$out['background_opacity']   = max( 30, min( 100, absint( $input['background_opacity'] ?? 78 ) ) );
		$out['logo_url']             = CAV_Security::sanitize_url( $input['logo_url'] ?? '' );

		if ( '' === $out['redirect_url'] ) {
			$out['redirect_url'] = 'https://www.bundesgesundheitsministerium.de/service/gesetze-und-verordnungen/detail/cannabisgesetz';
		}

		return $out;
	}
}
