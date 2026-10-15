<?php
defined( 'ABSPATH' ) || exit;

/**
 * Enable Banking API client (PSD2 account information).
 *
 * Auth: RS256-signed JWT, header kid = application id, iss enablebanking.com, aud api.enablebanking.com.
 */
class HJB_EnableBanking {

	const BASE = 'https://api.enablebanking.com';

	private string $app_id;
	private string $key;

	public function __construct( ?string $app_id = null, ?string $key = null ) {
		$this->app_id = $app_id ?? (string) HJB_Settings::get( 'app_id' );
		$this->key    = $key ?? HJB_Settings::private_key();
	}

	public static function b64url( string $data ): string {
		return rtrim( strtr( base64_encode( $data ), '+/', '-_' ), '=' );
	}

	public function jwt( int $ttl = 3600 ): string {
		if ( '' === $this->app_id || '' === $this->key ) {
			throw new HJB_Exception( 'Enable Banking Application-ID oder Private Key fehlt.', 'E_CONFIG' );
		}
		$now     = time();
		$header  = self::b64url( wp_json_encode( array( 'typ' => 'JWT', 'alg' => 'RS256', 'kid' => $this->app_id ) ) );
		$payload = self::b64url( wp_json_encode( array( 'iss' => 'enablebanking.com', 'aud' => 'api.enablebanking.com', 'iat' => $now, 'exp' => $now + $ttl ) ) );
		$pkey    = openssl_pkey_get_private( $this->key );
		if ( false === $pkey ) {
			throw new HJB_Exception( 'Private Key konnte nicht gelesen werden (PEM-Format erwartet).', 'E_KEY' );
		}
		$sig = '';
		if ( ! openssl_sign( $header . '.' . $payload, $sig, $pkey, OPENSSL_ALGO_SHA256 ) ) {
			throw new HJB_Exception( 'JWT-Signatur fehlgeschlagen.', 'E_KEY' );
		}
		return $header . '.' . $payload . '.' . self::b64url( $sig );
	}

	/**
	 * @param array $psu Optional PSU headers (ip, ua) when the account owner triggers the call interactively.
	 */
	public function request( string $method, string $path, ?array $body = null, array $psu = array(), int $timeout = 45 ): array {
		$headers = array(
			'Authorization' => 'Bearer ' . $this->jwt(),
			'Content-Type'  => 'application/json',
			'Accept'        => 'application/json',
		);
		if ( ! empty( $psu['ip'] ) ) {
			$headers['Psu-Ip-Address'] = $psu['ip'];
		}
		if ( ! empty( $psu['ua'] ) ) {
			$headers['Psu-User-Agent'] = $psu['ua'];
		}
		$args = array( 'method' => $method, 'timeout' => $timeout, 'headers' => $headers );
		if ( null !== $body ) {
			$args['body'] = wp_json_encode( $body );
		}
		$res = wp_remote_request( self::BASE . $path, $args );
		if ( is_wp_error( $res ) ) {
			throw new HJB_Exception( 'Enable Banking nicht erreichbar: ' . $res->get_error_message(), 'E_NETWORK' );
		}
		$status = (int) wp_remote_retrieve_response_code( $res );
		$json   = json_decode( (string) wp_remote_retrieve_body( $res ), true );
		$json   = is_array( $json ) ? $json : array();
		if ( $status < 200 || $status >= 300 ) {
			$code = (string) ( $json['error'] ?? $json['code'] ?? 'E_HTTP' );
			$msg  = (string) ( $json['message'] ?? $json['detail'] ?? ( 'HTTP ' . $status ) );
			throw new HJB_Exception( 'Enable Banking: ' . $msg . ' (' . $code . ')', $code, $status );
		}
		return $json;
	}

	public function application(): array {
		return $this->request( 'GET', '/application' );
	}

	public function aspsps( string $country ): array {
		$res = $this->request( 'GET', '/aspsps?country=' . rawurlencode( $country ) );
		return (array) ( $res['aspsps'] ?? array() );
	}

	public function find_aspsp( string $name, string $country ): ?array {
		foreach ( $this->aspsps( $country ) as $a ) {
			if ( 0 === strcasecmp( (string) ( $a['name'] ?? '' ), $name ) ) {
				return $a;
			}
		}
		return null;
	}

	public function start_auth( string $name, string $country, string $psu_type, string $redirect_url, string $state, string $valid_until ): array {
		return $this->request(
			'POST',
			'/auth',
			array(
				'access'       => array( 'valid_until' => $valid_until ),
				'aspsp'        => array( 'name' => $name, 'country' => $country ),
				'state'        => $state,
				'redirect_url' => $redirect_url,
				'psu_type'     => $psu_type,
				'language'     => 'de',
			)
		);
	}

	public function create_session( string $code ): array {
		return $this->request( 'POST', '/sessions', array( 'code' => $code ) );
	}

	public function get_session( string $session_id ): array {
		return $this->request( 'GET', '/sessions/' . rawurlencode( $session_id ) );
	}

	public function delete_session( string $session_id ): void {
		$this->request( 'DELETE', '/sessions/' . rawurlencode( $session_id ) );
	}

	public function account_details( string $uid ): array {
		return $this->request( 'GET', '/accounts/' . rawurlencode( $uid ) . '/details' );
	}

	/**
	 * All transactions since $date_from (YYYY-MM-DD), following continuation keys.
	 */
	public function transactions( string $uid, string $date_from, array $psu = array() ): array {
		$out  = array();
		$key  = '';
		$page = 0;
		do {
			$q = 'date_from=' . rawurlencode( $date_from );
			if ( '' !== $key ) {
				$q .= '&continuation_key=' . rawurlencode( $key );
			}
			$res  = $this->request( 'GET', '/accounts/' . rawurlencode( $uid ) . '/transactions?' . $q, null, $psu, 60 );
			$out  = array_merge( $out, (array) ( $res['transactions'] ?? array() ) );
			$key  = (string) ( $res['continuation_key'] ?? '' );
			$page++;
		} while ( '' !== $key && $page < 50 );
		return $out;
	}
}
