<?php
defined( 'ABSPATH' ) || exit;

class HJB_Settings {

	public static function defaults(): array {
		return array(
			'enabled'          => 'yes',
			'app_id'           => '',
			'private_key'      => '',
			'aspsp_name'       => 'N26',
			'aspsp_country'    => 'DE',
			'psu_type'         => 'business',
			'gateways'         => 'bacs',
			'statuses'         => 'on-hold,pending',
			'order_days'       => 60,
			'sync_times'       => '07:30,11:30,15:30,19:30',
			'auto_by_number'   => 'yes',
			'auto_by_name'     => 'yes',
			'notify_email'     => '',
			'iban_filter'      => '',
			// Runtime state.
			'session_id'       => '',
			'accounts'         => array(),
			'valid_until'      => '',
			'last_sync'        => 0,
			'last_sync_result' => '',
			'pending_state'    => '',
		);
	}

	public static function all(): array {
		$saved = get_option( HJB_OPTION, array() );
		return array_merge( self::defaults(), is_array( $saved ) ? $saved : array() );
	}

	public static function get( string $key, $default = null ) {
		$all = self::all();
		return array_key_exists( $key, $all ) ? $all[ $key ] : $default;
	}

	public static function update( array $values ): array {
		$current = self::all();
		foreach ( $values as $k => $v ) {
			if ( ! array_key_exists( $k, self::defaults() ) ) {
				continue;
			}
			if ( 'order_days' === $k ) {
				$v = max( 1, min( 365, (int) $v ) );
			} elseif ( is_string( $v ) && 'private_key' !== $k ) {
				$v = trim( $v );
			}
			$current[ $k ] = $v;
		}
		update_option( HJB_OPTION, $current, false );
		return $current;
	}

	/** Private key: wp-config constant HJB_PRIVATE_KEY_PATH wins over the stored value. */
	public static function private_key(): string {
		if ( defined( 'HJB_PRIVATE_KEY_PATH' ) && is_readable( HJB_PRIVATE_KEY_PATH ) ) {
			return (string) file_get_contents( HJB_PRIVATE_KEY_PATH ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		}
		return (string) self::get( 'private_key', '' );
	}

	public static function is_configured(): bool {
		return '' !== (string) self::get( 'app_id' ) && '' !== self::private_key();
	}

	public static function is_connected(): bool {
		$until = (string) self::get( 'valid_until' );
		return '' !== (string) self::get( 'session_id' ) && ! empty( self::get( 'accounts' ) ) && ( '' === $until || strtotime( $until ) > time() );
	}

	public static function list( string $key ): array {
		return array_values( array_filter( array_map( 'trim', explode( ',', (string) self::get( $key, '' ) ) ) ) );
	}

	public static function masked(): array {
		$s = self::all();
		$s['private_key'] = '' === self::private_key() ? '' : 'gespeichert';
		$s['session_id']  = '' === $s['session_id'] ? '' : substr( $s['session_id'], 0, 8 ) . '…';
		$s['pending_state'] = '';
		$accounts = array();
		foreach ( (array) $s['accounts'] as $a ) {
			$accounts[] = array( 'uid' => substr( (string) ( $a['uid'] ?? '' ), 0, 8 ) . '…', 'iban' => self::mask_iban( (string) ( $a['iban'] ?? '' ) ), 'name' => $a['name'] ?? '' );
		}
		$s['accounts'] = $accounts;
		return $s;
	}

	public static function mask_iban( string $iban ): string {
		$iban = preg_replace( '/\s+/', '', $iban );
		return strlen( $iban ) > 8 ? substr( $iban, 0, 4 ) . ' •••• ' . substr( $iban, -4 ) : $iban;
	}
}
