<?php
defined( 'ABSPATH' ) || exit;

/**
 * fiskaly DSFINVK DE API v1 client (cash register, cash point closings, exports).
 */
class HJ_TSE_Dsfinvk {

	const BASE = 'https://dsfinvk.fiskaly.com/api/v1';

	private array $settings;

	public function __construct( ?array $settings = null ) {
		$this->settings = $settings ?? HJ_TSE_Settings::all();
	}

	private function token( bool $force = false ): string {
		if ( ! $force ) {
			$cached = get_transient( 'hj_tse_dsfinvk_token' );
			if ( is_string( $cached ) && '' !== $cached ) {
				return $cached;
			}
		}
		$res = $this->raw( 'POST', '/auth', array( 'api_key' => $this->settings['api_key'], 'api_secret' => $this->settings['api_secret'] ), null, 40 );
		if ( empty( $res['body']['access_token'] ) ) {
			throw new HJ_TSE_Exception( 'DSFinV-K Login fehlgeschlagen: ' . ( $res['body']['message'] ?? 'unbekannt' ), (string) ( $res['body']['code'] ?? 'E_AUTH' ), $res['status'] );
		}
		$token = (string) $res['body']['access_token'];
		set_transient( 'hj_tse_dsfinvk_token', $token, max( 300, (int) ( $res['body']['access_token_expires_in'] ?? 3600 ) - 300 ) );
		return $token;
	}

	private function raw( string $method, string $path, ?array $body, ?string $token, int $timeout, bool $binary = false ): array {
		$args = array(
			'method'  => $method,
			'timeout' => $timeout,
			'headers' => array( 'Content-Type' => 'application/json', 'Accept' => $binary ? '*/*' : 'application/json' ),
		);
		if ( null !== $token ) {
			$args['headers']['Authorization'] = 'Bearer ' . $token;
		}
		if ( null !== $body ) {
			$args['body'] = wp_json_encode( $body );
		}
		$response = wp_remote_request( self::BASE . $path, $args );
		if ( is_wp_error( $response ) ) {
			throw new HJ_TSE_Exception( 'DSFinV-K nicht erreichbar: ' . $response->get_error_message(), 'E_NETWORK' );
		}
		$status = (int) wp_remote_retrieve_response_code( $response );
		$raw    = (string) wp_remote_retrieve_body( $response );
		if ( $binary ) {
			return array( 'status' => $status, 'body' => array(), 'raw' => $raw );
		}
		$json = json_decode( $raw, true );
		return array( 'status' => $status, 'body' => is_array( $json ) ? $json : array(), 'raw' => $raw );
	}

	public function request( string $method, string $path, ?array $body = null, int $timeout = 60 ): array {
		$res = $this->raw( $method, $path, $body, $this->token(), $timeout );
		if ( 401 === $res['status'] ) {
			$res = $this->raw( $method, $path, $body, $this->token( true ), $timeout );
		}
		if ( $res['status'] < 200 || $res['status'] >= 300 ) {
			$msg = $res['body']['message'] ?? ( 'HTTP ' . $res['status'] );
			throw new HJ_TSE_Exception( 'DSFinV-K: ' . $msg, (string) ( $res['body']['code'] ?? 'E_HTTP' ), $res['status'] );
		}
		return $res['body'];
	}

	/** Register (or update) the cash register that mirrors the SIGN DE client. */
	public function register_cash_register(): array {
		$client_id = (string) $this->settings['client_id'];
		return $this->request(
			'PUT',
			'/cash_registers/' . rawurlencode( $client_id ),
			array(
				'cash_register_type' => array( 'type' => 'MASTER', 'tss_id' => $this->settings['tss_id'] ),
				'brand'              => 'WCPOS',
				'model'              => 'WCPOS Pro / WooCommerce',
				'software'           => array( 'brand' => 'Hanfjack TSE (fiskaly) fuer WCPOS', 'version' => HJ_TSE_VERSION ),
				'base_currency_code' => 'EUR',
				'processing_flags'   => array( 'UmsatzsteuerNichtErmittelbar' => false ),
				'metadata'           => array( 'site' => home_url(), 'client_serial' => (string) $this->settings['client_serial'] ),
			)
		);
	}

	public function get_cash_register(): array {
		return $this->request( 'GET', '/cash_registers/' . rawurlencode( (string) $this->settings['client_id'] ) );
	}

	public function put_closing( string $closing_id, array $body ): array {
		return $this->request( 'PUT', '/cash_point_closings/' . $closing_id, $body, 120 );
	}

	public function get_closing( string $closing_id ): array {
		return $this->request( 'GET', '/cash_point_closings/' . $closing_id );
	}

	public function trigger_export( string $export_id, array $body ): array {
		return $this->request( 'PUT', '/exports/' . $export_id, $body, 120 );
	}

	public function get_export( string $export_id ): array {
		return $this->request( 'GET', '/exports/' . $export_id );
	}

	public function download_export( string $export_id ): string {
		$res = $this->raw( 'GET', '/exports/' . $export_id . '/download', null, $this->token(), 300, true );
		if ( 200 !== $res['status'] ) {
			throw new HJ_TSE_Exception( 'DSFinV-K Export-Download fehlgeschlagen (HTTP ' . $res['status'] . ').', 'E_EXPORT' );
		}
		return $res['raw'];
	}
}
