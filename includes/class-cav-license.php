<?php
/**
 * License handling for Cannabis Age Verifier.
 *
 * Premium plugin – activation requires a license key, EXCEPT on the
 * vendor's own domain (blocksocial.eu) where it is always active.
 *
 * @package CannabisAgeVerifier
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class CAV_License {

	const STATUS_ACTIVE       = 'active';
	const STATUS_INACTIVE     = 'inactive';
	const STATUS_VENDOR       = 'vendor';
	const TRANSIENT_KEY       = 'cav_license_status';
	const TRANSIENT_TTL       = DAY_IN_SECONDS;

	public static function is_active() {
		if ( self::is_vendor_site() ) {
			return true;
		}

		$status = get_transient( self::TRANSIENT_KEY );

		if ( false === $status ) {
			$status = self::refresh();
		}

		return self::STATUS_ACTIVE === $status;
	}

	public static function status() {
		if ( self::is_vendor_site() ) {
			return self::STATUS_VENDOR;
		}

		$status = get_transient( self::TRANSIENT_KEY );

		if ( false === $status ) {
			$status = self::refresh();
		}

		return $status;
	}

	public static function refresh() {
		$license = get_option( CAV_LICENSE_KEY, '' );
		$status  = self::validate_key( $license ) ? self::STATUS_ACTIVE : self::STATUS_INACTIVE;

		set_transient( self::TRANSIENT_KEY, $status, self::TRANSIENT_TTL );

		return $status;
	}

	public static function save_key( $key ) {
		$key = sanitize_text_field( $key );
		$key = preg_replace( '/[^A-Za-z0-9\-]/', '', $key );
		$key = substr( $key, 0, 64 );

		update_option( CAV_LICENSE_KEY, $key, false );
		delete_transient( self::TRANSIENT_KEY );

		return $key;
	}

	public static function vendor_domains() {
		$domains = defined( 'CAV_VENDOR_DOMAINS' ) ? @unserialize( CAV_VENDOR_DOMAINS ) : array();

		if ( ! is_array( $domains ) ) {
			$domains = array( 'blocksocial.eu' );
		}

		return array_map( 'strtolower', $domains );
	}

	public static function is_vendor_site() {
		$home = wp_parse_url( home_url(), PHP_URL_HOST );

		if ( ! is_string( $home ) ) {
			return false;
		}

		$home = strtolower( preg_replace( '/^www\./', '', $home ) );

		foreach ( self::vendor_domains() as $domain ) {
			if ( $home === $domain || substr( $home, -( strlen( $domain ) + 1 ) ) === '.' . $domain ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Offline license-key validation.
	 *
	 * Keys are formatted CAV-XXXX-XXXX-XXXX-CHK where CHK is the first 4
	 * hex chars of sha256("BSEU-CAV|" . payload). This lets the plugin
	 * ship without a remote phone-home, which keeps load times fast and
	 * is GDPR-friendlier (no IP-Übermittlung an Drittserver).
	 */
	public static function validate_key( $key ) {
		$key = is_string( $key ) ? strtoupper( trim( $key ) ) : '';

		if ( ! preg_match( '/^CAV-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-F0-9]{4}$/', $key ) ) {
			return false;
		}

		$parts = explode( '-', $key );
		$payload = $parts[0] . '-' . $parts[1] . '-' . $parts[2] . '-' . $parts[3];
		$check   = substr( hash( 'sha256', 'BSEU-CAV|' . $payload ), 0, 4 );

		return hash_equals( strtoupper( $check ), $parts[4] );
	}
}
