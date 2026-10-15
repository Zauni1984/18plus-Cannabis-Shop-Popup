<?php
defined( 'ABSPATH' ) || exit;

/**
 * Settings storage (wp_options) with sane defaults.
 */
class HJ_TSE_Settings {

	public static function defaults(): array {
		return array(
			'enabled'       => 'yes',
			'api_key'       => '',
			'api_secret'    => '',
			'tss_id'        => '',
			'client_id'     => '',
			'client_serial' => 'HANFJACK-INGOLSTADT-KASSE-1',
			'admin_pin'     => '',
			'admin_puk'     => '',
			'timeout_tx'    => 5,
			'timeout_admin' => 40,
			'receipt_label' => 'Kassenbeleg',
			'print_fields'  => 'yes',
		);
	}

	public static function all(): array {
		$saved = get_option( HJ_TSE_OPTION, array() );
		if ( ! is_array( $saved ) ) {
			$saved = array();
		}
		return array_merge( self::defaults(), $saved );
	}

	public static function get( string $key, $default = null ) {
		$all = self::all();
		return array_key_exists( $key, $all ) ? $all[ $key ] : $default;
	}

	public static function update( array $values ): array {
		$allowed = array_keys( self::defaults() );
		$current = self::all();
		foreach ( $values as $k => $v ) {
			if ( ! in_array( $k, $allowed, true ) ) {
				continue;
			}
			if ( in_array( $k, array( 'timeout_tx', 'timeout_admin' ), true ) ) {
				$v = max( 1, (int) $v );
			} else {
				$v = is_string( $v ) ? trim( wp_unslash( $v ) ) : $v;
			}
			$current[ $k ] = $v;
		}
		update_option( HJ_TSE_OPTION, $current, false );
		delete_transient( 'hj_tse_token' );
		return $current;
	}

	public static function is_enabled(): bool {
		return 'yes' === self::get( 'enabled' );
	}

	public static function is_configured(): bool {
		$s = self::all();
		return '' !== $s['api_key'] && '' !== $s['api_secret'] && '' !== $s['tss_id'] && '' !== $s['client_id'];
	}

	public static function environment(): string {
		$key = (string) self::get( 'api_key' );
		if ( '' === $key ) {
			return 'unknown';
		}
		return str_starts_with( $key, 'test_' ) ? 'TEST' : 'LIVE';
	}

	/** Settings with secrets masked for API/status output. */
	public static function masked(): array {
		$s = self::all();
		foreach ( array( 'api_secret', 'admin_pin', 'admin_puk' ) as $k ) {
			$s[ $k ] = '' === $s[ $k ] ? '' : '••••' . substr( (string) $s[ $k ], -2 );
		}
		if ( '' !== $s['api_key'] ) {
			$s['api_key'] = substr( $s['api_key'], 0, 10 ) . '…';
		}
		$s['environment'] = self::environment();
		return $s;
	}
}
