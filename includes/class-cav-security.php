<?php
/**
 * Security helpers for Cannabis Age Verifier.
 *
 * @package CannabisAgeVerifier
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class CAV_Security {

	const NONCE_ACTION = 'cav_verify_age';
	const NONCE_NAME   = 'cav_nonce';
	const SECRET_OPT   = 'cav_hmac_secret';

	public static function get_secret() {
		$secret = get_option( self::SECRET_OPT );

		if ( ! is_string( $secret ) || strlen( $secret ) < 32 ) {
			$secret = wp_generate_password( 64, true, true );
			update_option( self::SECRET_OPT, $secret, false );
		}

		return $secret;
	}

	public static function sign( $payload ) {
		return hash_hmac( 'sha256', (string) $payload, self::get_secret() );
	}

	public static function verify_signature( $payload, $signature ) {
		if ( ! is_string( $signature ) || '' === $signature ) {
			return false;
		}

		return hash_equals( self::sign( $payload ), $signature );
	}

	public static function generate_cookie_token( $age_band, $issued_at ) {
		$age_band  = self::sanitize_age_band( $age_band );
		$issued_at = absint( $issued_at );
		$payload   = $age_band . '|' . $issued_at;

		return $payload . '|' . self::sign( $payload );
	}

	public static function parse_cookie_token( $token ) {
		if ( ! is_string( $token ) || strlen( $token ) > 256 ) {
			return false;
		}

		$parts = explode( '|', $token );

		if ( 3 !== count( $parts ) ) {
			return false;
		}

		list( $age_band, $issued_at, $signature ) = $parts;

		$payload = $age_band . '|' . $issued_at;

		if ( ! self::verify_signature( $payload, $signature ) ) {
			return false;
		}

		return array(
			'age_band'  => self::sanitize_age_band( $age_band ),
			'issued_at' => absint( $issued_at ),
		);
	}

	public static function sanitize_age_band( $value ) {
		$value = is_string( $value ) ? strtolower( $value ) : '';

		return in_array( $value, array( 'adult', 'minor' ), true ) ? $value : '';
	}

	public static function sanitize_url( $url ) {
		$clean = esc_url_raw( trim( (string) $url ) );

		if ( '' === $clean ) {
			return '';
		}

		$parsed = wp_parse_url( $clean );

		if ( empty( $parsed['scheme'] ) || ! in_array( $parsed['scheme'], array( 'http', 'https' ), true ) ) {
			return '';
		}

		return $clean;
	}

	public static function sanitize_hex_color( $color, $fallback = '#1aa654' ) {
		$color = is_string( $color ) ? trim( $color ) : '';

		if ( preg_match( '/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $color ) ) {
			return $color;
		}

		return $fallback;
	}

	public static function get_client_ip_hash() {
		$ip = '';

		if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = filter_var( wp_unslash( $_SERVER['REMOTE_ADDR'] ), FILTER_VALIDATE_IP );
		}

		if ( ! $ip ) {
			$ip = '0.0.0.0';
		}

		return substr( hash_hmac( 'sha256', $ip, self::get_secret() ), 0, 16 );
	}

	public static function calc_age_from_dob( $year, $month, $day ) {
		$year  = absint( $year );
		$month = absint( $month );
		$day   = absint( $day );

		if ( ! checkdate( $month, $day, $year ) ) {
			return -1;
		}

		$current_year = (int) gmdate( 'Y' );
		if ( $year < 1900 || $year > $current_year ) {
			return -1;
		}

		try {
			$dob   = new DateTimeImmutable( sprintf( '%04d-%02d-%02d', $year, $month, $day ), new DateTimeZone( 'UTC' ) );
			$today = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
		} catch ( Exception $e ) {
			return -1;
		}

		if ( $dob > $today ) {
			return -1;
		}

		return (int) $today->diff( $dob )->y;
	}
}
