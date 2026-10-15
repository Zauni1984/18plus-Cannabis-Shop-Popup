<?php
defined( 'ABSPATH' ) || exit;

/**
 * Minimal fiskaly SIGN DE (KassenSichV) v2 client.
 */
class HJ_TSE_Client {

	const BASE = 'https://kassensichv-middleware.fiskaly.com/api/v2';

	private array $settings;

	public function __construct( ?array $settings = null ) {
		$this->settings = $settings ?? HJ_TSE_Settings::all();
	}

	/* ---------- auth ---------- */

	private function token( bool $force = false ): string {
		if ( ! $force ) {
			$cached = get_transient( 'hj_tse_token' );
			if ( is_string( $cached ) && '' !== $cached ) {
				return $cached;
			}
		}
		if ( '' === $this->settings['api_key'] || '' === $this->settings['api_secret'] ) {
			throw new HJ_TSE_Exception( 'fiskaly API-Key oder Secret fehlt.', 'E_CONFIG' );
		}
		$res = $this->raw(
			'POST',
			'/auth',
			array(
				'api_key'    => $this->settings['api_key'],
				'api_secret' => $this->settings['api_secret'],
			),
			null,
			(int) $this->settings['timeout_admin']
		);
		if ( empty( $res['body']['access_token'] ) ) {
			throw new HJ_TSE_Exception( 'fiskaly Login fehlgeschlagen: ' . ( $res['body']['message'] ?? 'unbekannt' ), $res['body']['code'] ?? 'E_AUTH', $res['status'] );
		}
		$token = (string) $res['body']['access_token'];
		$ttl   = max( 300, (int) ( $res['body']['access_token_expires_in'] ?? 3600 ) - 300 );
		set_transient( 'hj_tse_token', $token, $ttl );
		return $token;
	}

	/* ---------- http ---------- */

	private function raw( string $method, string $path, ?array $body, ?string $token, int $timeout ): array {
		$args = array(
			'method'  => $method,
			'timeout' => $timeout,
			'headers' => array(
				'Content-Type' => 'application/json',
				'Accept'       => 'application/json',
			),
		);
		if ( null !== $token ) {
			$args['headers']['Authorization'] = 'Bearer ' . $token;
		}
		if ( null !== $body ) {
			$args['body'] = wp_json_encode( $body );
		}
		$response = wp_remote_request( self::BASE . $path, $args );
		if ( is_wp_error( $response ) ) {
			throw new HJ_TSE_Exception( 'fiskaly nicht erreichbar: ' . $response->get_error_message(), 'E_NETWORK' );
		}
		$status = (int) wp_remote_retrieve_response_code( $response );
		$json   = json_decode( (string) wp_remote_retrieve_body( $response ), true );
		return array(
			'status' => $status,
			'body'   => is_array( $json ) ? $json : array(),
		);
	}

	/**
	 * Authenticated request; retries once with a fresh token on 401.
	 */
	public function request( string $method, string $path, ?array $body = null, ?int $timeout = null ): array {
		$timeout = $timeout ?? (int) $this->settings['timeout_tx'];
		$res     = $this->raw( $method, $path, $body, $this->token(), $timeout );
		if ( 401 === $res['status'] ) {
			$res = $this->raw( $method, $path, $body, $this->token( true ), $timeout );
		}
		if ( $res['status'] < 200 || $res['status'] >= 300 ) {
			$msg = $res['body']['message'] ?? ( 'HTTP ' . $res['status'] );
			throw new HJ_TSE_Exception( 'fiskaly: ' . $msg, (string) ( $res['body']['code'] ?? 'E_HTTP' ), $res['status'] );
		}
		return $res['body'];
	}

	/* ---------- TSS / client ---------- */

	public function get_tss( string $tss_id ): array {
		return $this->request( 'GET', '/tss/' . rawurlencode( $tss_id ), null, (int) $this->settings['timeout_admin'] );
	}

	public function list_tss(): array {
		return $this->request( 'GET', '/tss?limit=100', null, (int) $this->settings['timeout_admin'] );
	}

	public function get_client( string $tss_id, string $client_id ): array {
		return $this->request( 'GET', '/tss/' . rawurlencode( $tss_id ) . '/client/' . rawurlencode( $client_id ), null, (int) $this->settings['timeout_admin'] );
	}

	/**
	 * Create + initialise a TSS and register the cash register client.
	 *
	 * @return array{tss_id:string,client_id:string,admin_pin:string,admin_puk:string,tss_serial:string}
	 */
	public function setup( string $client_serial, array $metadata = array() ): array {
		$t      = (int) $this->settings['timeout_admin'];
		$tss_id = wp_generate_uuid4();
		$tss    = $this->request( 'PUT', '/tss/' . $tss_id, array( 'metadata' => $metadata ), $t );
		$puk    = (string) ( $tss['admin_puk'] ?? '' );
		$pin    = (string) wp_rand( 100000, 999999 );

		$this->request( 'PATCH', '/tss/' . $tss_id, array( 'state' => 'UNINITIALIZED' ), $t );
		$this->request( 'PATCH', '/tss/' . $tss_id . '/admin', array( 'admin_puk' => $puk, 'new_admin_pin' => $pin ), $t );
		$this->request( 'POST', '/tss/' . $tss_id . '/admin/auth', array( 'admin_pin' => $pin ), $t );
		$init = $this->request( 'PATCH', '/tss/' . $tss_id, array( 'state' => 'INITIALIZED' ), $t );

		$client_id = wp_generate_uuid4();
		$this->request( 'PUT', '/tss/' . $tss_id . '/client/' . $client_id, array( 'serial_number' => $client_serial, 'metadata' => $metadata ), $t );
		try {
			$this->request( 'POST', '/tss/' . $tss_id . '/admin/logout', array(), $t );
		} catch ( HJ_TSE_Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement
			// Logout is best effort.
		}
		return array(
			'tss_id'     => $tss_id,
			'client_id'  => $client_id,
			'admin_pin'  => $pin,
			'admin_puk'  => $puk,
			'tss_serial' => (string) ( $init['serial_number'] ?? $tss['serial_number'] ?? '' ),
		);
	}

	/* ---------- transactions ---------- */

	public function start_transaction( string $tx_id ): array {
		return $this->request(
			'PUT',
			'/tss/' . rawurlencode( $this->settings['tss_id'] ) . '/tx/' . $tx_id . '?tx_revision=1',
			array(
				'state'     => 'ACTIVE',
				'client_id' => $this->settings['client_id'],
			)
		);
	}

	/**
	 * Finish (or cancel) a transaction with a standard_v1 receipt.
	 */
	public function finish_transaction( string $tx_id, int $revision, array $receipt, string $state = 'FINISHED' ): array {
		return $this->request(
			'PUT',
			'/tss/' . rawurlencode( $this->settings['tss_id'] ) . '/tx/' . $tx_id . '?tx_revision=' . $revision,
			array(
				'state'     => $state,
				'client_id' => $this->settings['client_id'],
				'schema'    => array( 'standard_v1' => array( 'receipt' => $receipt ) ),
			)
		);
	}

	public function get_transaction( string $tx_id ): array {
		return $this->request( 'GET', '/tss/' . rawurlencode( $this->settings['tss_id'] ) . '/tx/' . $tx_id );
	}

	/* ---------- export ---------- */

	public function trigger_export( string $export_id, array $query = array() ): array {
		$qs = $query ? '?' . http_build_query( $query ) : '';
		return $this->request( 'PUT', '/tss/' . rawurlencode( $this->settings['tss_id'] ) . '/export/' . $export_id . $qs, array(), (int) $this->settings['timeout_admin'] );
	}

	public function get_export( string $export_id ): array {
		return $this->request( 'GET', '/tss/' . rawurlencode( $this->settings['tss_id'] ) . '/export/' . $export_id, null, (int) $this->settings['timeout_admin'] );
	}

	public function download_export( string $export_id ): string {
		$response = wp_remote_get(
			self::BASE . '/tss/' . rawurlencode( $this->settings['tss_id'] ) . '/export/' . $export_id . '/file',
			array(
				'timeout' => (int) $this->settings['timeout_admin'],
				'headers' => array( 'Authorization' => 'Bearer ' . $this->token() ),
			)
		);
		if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			throw new HJ_TSE_Exception( 'Export-Download fehlgeschlagen.', 'E_EXPORT' );
		}
		return (string) wp_remote_retrieve_body( $response );
	}
}
