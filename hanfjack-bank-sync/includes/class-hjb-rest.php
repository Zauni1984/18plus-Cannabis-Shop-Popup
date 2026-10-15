<?php
defined( 'ABSPATH' ) || exit;

class HJB_Rest {

	public static function init(): void {
		add_action( 'rest_api_init', array( __CLASS__, 'routes' ) );
	}

	public static function can(): bool {
		return current_user_can( 'manage_woocommerce' );
	}

	public static function routes(): void {
		$ns = 'hj-bank/v1';
		// Public: the bank redirects the account owner here after consent. Protected by the one-time state value.
		register_rest_route( $ns, '/callback', array( 'methods' => 'GET', 'callback' => array( __CLASS__, 'callback' ), 'permission_callback' => '__return_true' ) );
		register_rest_route( $ns, '/status', array( 'methods' => 'GET', 'callback' => array( __CLASS__, 'status' ), 'permission_callback' => array( __CLASS__, 'can' ) ) );
		register_rest_route( $ns, '/settings', array( 'methods' => 'POST', 'callback' => array( __CLASS__, 'settings' ), 'permission_callback' => array( __CLASS__, 'can' ) ) );
		register_rest_route( $ns, '/aspsps', array( 'methods' => 'GET', 'callback' => array( __CLASS__, 'aspsps' ), 'permission_callback' => array( __CLASS__, 'can' ) ) );
		register_rest_route( $ns, '/sync', array( 'methods' => 'POST', 'callback' => array( __CLASS__, 'sync' ), 'permission_callback' => array( __CLASS__, 'can' ) ) );
		register_rest_route( $ns, '/transactions', array( 'methods' => 'GET', 'callback' => array( __CLASS__, 'transactions' ), 'permission_callback' => array( __CLASS__, 'can' ) ) );
		register_rest_route( $ns, '/assign', array( 'methods' => 'POST', 'callback' => array( __CLASS__, 'assign' ), 'permission_callback' => array( __CLASS__, 'can' ) ) );
		register_rest_route( $ns, '/test-match', array( 'methods' => 'POST', 'callback' => array( __CLASS__, 'test_match' ), 'permission_callback' => array( __CLASS__, 'can' ) ) );
	}

	public static function callback( WP_REST_Request $r ) {
		$target = admin_url( 'admin.php?page=hjb' );
		$error  = (string) $r->get_param( 'error' );
		if ( '' !== $error ) {
			wp_safe_redirect( add_query_arg( array( 'hjb_msg' => rawurlencode( 'Bank hat die Freigabe abgelehnt: ' . $error ), 'hjb_type' => 'error' ), $target ) );
			exit;
		}
		try {
			$accts = HJB_Sync::finish_connect( (string) $r->get_param( 'code' ), (string) $r->get_param( 'state' ) );
			$msg   = 'Konto verbunden: ' . implode( ', ', array_map( static fn( $a ) => HJB_Settings::mask_iban( $a['iban'] ), $accts ) );
			wp_safe_redirect( add_query_arg( array( 'hjb_msg' => rawurlencode( $msg ), 'hjb_type' => 'updated' ), $target ) );
		} catch ( HJB_Exception $e ) {
			wp_safe_redirect( add_query_arg( array( 'hjb_msg' => rawurlencode( 'Verbindung fehlgeschlagen: ' . $e->getMessage() ), 'hjb_type' => 'error' ), $target ) );
		}
		exit;
	}

	public static function status() {
		return rest_ensure_response(
			array(
				'settings'     => HJB_Settings::masked(),
				'configured'   => HJB_Settings::is_configured(),
				'connected'    => HJB_Settings::is_connected(),
				'callback_url' => HJB_Sync::callback_url(),
				'next_run'     => wp_next_scheduled( 'hjb_sync' ) ? wp_date( 'd.m.Y H:i', wp_next_scheduled( 'hjb_sync' ) ) : null,
				'counts'       => HJB_Store::counts(),
				'open_orders'  => count( HJB_Sync::open_orders() ),
			)
		);
	}

	public static function settings( WP_REST_Request $r ) {
		$p = $r->get_json_params();
		$p = is_array( $p ) ? $p : array();
		// Credentials and runtime state are never set over REST.
		unset( $p['private_key'], $p['session_id'], $p['accounts'], $p['valid_until'], $p['pending_state'], $p['last_sync'] );
		HJB_Settings::update( $p );
		HJB_Sync::schedule();
		return rest_ensure_response( HJB_Settings::masked() );
	}

	public static function aspsps( WP_REST_Request $r ) {
		try {
			$list = ( new HJB_EnableBanking() )->aspsps( (string) ( $r->get_param( 'country' ) ?: 'DE' ) );
			$q    = mb_strtolower( (string) $r->get_param( 'q' ) );
			$out  = array();
			foreach ( $list as $a ) {
				if ( '' === $q || false !== strpos( mb_strtolower( (string) $a['name'] ), $q ) ) {
					$out[] = array( 'name' => $a['name'] ?? '', 'psu_types' => $a['psu_types'] ?? array(), 'maximum_consent_validity_days' => isset( $a['maximum_consent_validity'] ) ? round( $a['maximum_consent_validity'] / 86400 ) : null, 'beta' => $a['beta'] ?? null );
				}
			}
			return rest_ensure_response( $out );
		} catch ( HJB_Exception $e ) {
			return new WP_Error( 'hjb_api', $e->getMessage(), array( 'status' => 502 ) );
		}
	}

	public static function sync( WP_REST_Request $r ) {
		try {
			return rest_ensure_response( HJB_Sync::run( array(), (int) $r->get_param( 'days' ) ) );
		} catch ( HJB_Exception $e ) {
			return new WP_Error( 'hjb_sync', $e->getMessage(), array( 'status' => 502 ) );
		}
	}

	public static function transactions( WP_REST_Request $r ) {
		$st = array_filter( explode( ',', (string) ( $r->get_param( 'status' ) ?: 'review,unmatched,matched' ) ) );
		return rest_ensure_response( HJB_Store::by_status( $st, min( 500, (int) ( $r->get_param( 'limit' ) ?: 100 ) ) ) );
	}

	public static function assign( WP_REST_Request $r ) {
		try {
			HJB_Sync::assign( (int) $r->get_param( 'row_id' ), (int) $r->get_param( 'order_id' ) );
			return rest_ensure_response( array( 'ok' => true ) );
		} catch ( HJB_Exception $e ) {
			return new WP_Error( 'hjb_assign', $e->getMessage(), array( 'status' => 400 ) );
		}
	}

	/** Dry run of the matcher against current open orders (no changes). */
	public static function test_match( WP_REST_Request $r ) {
		$tx = HJB_Matcher::normalize(
			array(
				'transaction_amount'     => array( 'amount' => (string) $r->get_param( 'amount' ), 'currency' => 'EUR' ),
				'credit_debit_indicator' => 'CRDT',
				'status'                 => 'BOOK',
				'remittance_information' => array( (string) $r->get_param( 'remittance' ) ),
				'debtor'                 => array( 'name' => (string) $r->get_param( 'debtor' ) ),
				'booking_date'           => gmdate( 'Y-m-d' ),
			)
		);
		return rest_ensure_response( array( 'tx' => $tx, 'result' => HJB_Matcher::match( $tx, HJB_Sync::open_orders() ) ) );
	}
}
