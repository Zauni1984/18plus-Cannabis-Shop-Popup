<?php
defined( 'ABSPATH' ) || exit;

/**
 * REST API for remote administration (namespace hj-tse/v1). Requires manage_woocommerce.
 */
class HJ_TSE_Rest {

	public static function init(): void {
		add_action( 'rest_api_init', array( __CLASS__, 'routes' ) );
	}

	public static function permission(): bool {
		return current_user_can( 'manage_woocommerce' );
	}

	public static function routes(): void {
		$ns = 'hj-tse/v1';
		$p  = array( __CLASS__, 'permission' );
		register_rest_route( $ns, '/status', array( 'methods' => 'GET', 'callback' => array( __CLASS__, 'status' ), 'permission_callback' => $p ) );
		register_rest_route( $ns, '/settings', array( 'methods' => 'POST', 'callback' => array( __CLASS__, 'settings' ), 'permission_callback' => $p ) );
		register_rest_route( $ns, '/setup', array( 'methods' => 'POST', 'callback' => array( __CLASS__, 'setup' ), 'permission_callback' => $p ) );
		register_rest_route( $ns, '/sign/(?P<id>\d+)', array( 'methods' => 'POST', 'callback' => array( __CLASS__, 'sign' ), 'permission_callback' => $p ) );
		register_rest_route( $ns, '/order/(?P<id>\d+)', array( 'methods' => 'GET', 'callback' => array( __CLASS__, 'order' ), 'permission_callback' => $p ) );
		register_rest_route( $ns, '/log', array( 'methods' => 'GET', 'callback' => array( __CLASS__, 'log' ), 'permission_callback' => $p ) );
		register_rest_route( $ns, '/export', array( 'methods' => 'POST', 'callback' => array( __CLASS__, 'export' ), 'permission_callback' => $p ) );
		register_rest_route( $ns, '/export/(?P<id>[a-f0-9-]+)', array( 'methods' => 'GET', 'callback' => array( __CLASS__, 'export_status' ), 'permission_callback' => $p ) );
		register_rest_route( $ns, '/wcpos-template/(?P<id>\d+)/inject', array( 'methods' => 'POST', 'callback' => array( __CLASS__, 'inject_template' ), 'permission_callback' => $p ) );
		register_rest_route( $ns, '/cashbook', array( 'methods' => 'POST', 'callback' => array( __CLASS__, 'cashbook' ), 'permission_callback' => $p ) );
		register_rest_route( $ns, '/closings', array( 'methods' => 'GET', 'callback' => array( __CLASS__, 'closings' ), 'permission_callback' => $p ) );
		register_rest_route( $ns, '/closings', array( 'methods' => 'POST', 'callback' => array( __CLASS__, 'create_closing' ), 'permission_callback' => $p ) );
		register_rest_route( $ns, '/closings/(?P<id>\d+)', array( 'methods' => 'GET', 'callback' => array( __CLASS__, 'closing' ), 'permission_callback' => $p ) );
		register_rest_route( $ns, '/closings/(?P<id>\d+)/refresh', array( 'methods' => 'POST', 'callback' => array( __CLASS__, 'refresh_closing' ), 'permission_callback' => $p ) );
		register_rest_route( $ns, '/cash-register', array( 'methods' => 'POST', 'callback' => array( __CLASS__, 'cash_register' ), 'permission_callback' => $p ) );
		register_rest_route( $ns, '/dsfinvk-export', array( 'methods' => 'POST', 'callback' => array( __CLASS__, 'dsfinvk_export' ), 'permission_callback' => $p ) );
		register_rest_route( $ns, '/dsfinvk-export/(?P<id>[a-f0-9-]+)', array( 'methods' => 'GET', 'callback' => array( __CLASS__, 'dsfinvk_export_status' ), 'permission_callback' => $p ) );
	}

	public static function cashbook( WP_REST_Request $r ) {
		try {
			$res = HJ_TSE_Cashbook::book( (float) $r->get_param( 'amount' ), (string) $r->get_param( 'reason' ), (string) ( $r->get_param( 'text' ) ?? '' ), get_current_user_id() );
			return rest_ensure_response( $res );
		} catch ( HJ_TSE_Exception $e ) {
			return self::error( $e );
		}
	}

	public static function closings( WP_REST_Request $r ) {
		return rest_ensure_response( array( 'closings' => HJ_TSE_Log::closings( 100 ), 'open_days' => HJ_TSE_Log::open_days( HJ_TSE_Closing::today() ), 'today' => HJ_TSE_Closing::today() ) );
	}

	public static function create_closing( WP_REST_Request $r ) {
		$date = (string) ( $r->get_param( 'business_date' ) ?: HJ_TSE_Closing::today() );
		try {
			$row = HJ_TSE_Closing::create_for_date( $date );
			if ( null === $row ) {
				return rest_ensure_response( array( 'created' => false, 'message' => 'Keine offenen Vorgänge für ' . $date . '.' ) );
			}
			unset( $row['body_json'] );
			return rest_ensure_response( array( 'created' => true, 'closing' => $row ) );
		} catch ( HJ_TSE_Exception $e ) {
			return self::error( $e );
		}
	}

	public static function closing( WP_REST_Request $r ) {
		$row = HJ_TSE_Log::get_closing( (int) $r['id'] );
		if ( ! $row ) {
			return new WP_Error( 'hj_tse_closing', 'Abschluss nicht gefunden.', array( 'status' => 404 ) );
		}
		if ( 'yes' !== (string) $r->get_param( 'body' ) ) {
			unset( $row['body_json'] );
		}
		return rest_ensure_response( $row );
	}

	public static function refresh_closing( WP_REST_Request $r ) {
		return rest_ensure_response( array( 'state' => HJ_TSE_Closing::refresh( (int) $r['id'] ) ) );
	}

	public static function cash_register( WP_REST_Request $r ) {
		try {
			delete_option( 'hj_tse_cash_register_registered' );
			HJ_TSE_Closing::ensure_cash_register();
			return rest_ensure_response( ( new HJ_TSE_Dsfinvk() )->get_cash_register() );
		} catch ( HJ_TSE_Exception $e ) {
			return self::error( $e );
		}
	}

	public static function dsfinvk_export( WP_REST_Request $r ) {
		try {
			return rest_ensure_response( HJ_TSE_Closing::trigger_export( (string) $r->get_param( 'start_date' ), (string) $r->get_param( 'end_date' ) ) );
		} catch ( HJ_TSE_Exception $e ) {
			return self::error( $e );
		}
	}

	public static function dsfinvk_export_status( WP_REST_Request $r ) {
		try {
			return rest_ensure_response( HJ_TSE_Closing::export_status( (string) $r['id'], 'yes' === (string) $r->get_param( 'download' ) ) );
		} catch ( HJ_TSE_Exception $e ) {
			return self::error( $e );
		}
	}

	/**
	 * Insert (or remove) the TSE block in a WCPOS thermal template (post type wcpos_template).
	 */
	public static function inject_template( WP_REST_Request $r ) {
		$post_id = (int) $r['id'];
		$post    = get_post( $post_id );
		if ( ! $post || 'wcpos_template' !== $post->post_type ) {
			return new WP_Error( 'hj_tse_template', 'Keine WCPOS-Vorlage.', array( 'status' => 404 ) );
		}
		$block   = file_get_contents( HJ_TSE_DIR . 'templates/wcpos-thermal-tse-block.xml' ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		$content = (string) $post->post_content;
		$has     = str_contains( $content, 'TSE-Daten' );
		if ( 'yes' === (string) $r->get_param( 'remove' ) ) {
			if ( ! $has ) {
				return rest_ensure_response( array( 'changed' => false, 'present' => false ) );
			}
			$new = str_replace( $block . "\n", '', $content );
			$new = str_replace( $block, '', $new );
		} else {
			if ( $has ) {
				return rest_ensure_response( array( 'changed' => false, 'present' => true ) );
			}
			$marker = (string) ( $r->get_param( 'marker' ) ?: '<!-- ── Order barcode' );
			$pos    = strpos( $content, $marker );
			if ( false === $pos ) {
				$marker = '<feed lines="3" />';
				$pos    = strrpos( $content, $marker );
			}
			if ( false === $pos ) {
				return new WP_Error( 'hj_tse_marker', 'Einfügemarke nicht gefunden.', array( 'status' => 422 ) );
			}
			$line_start = strrpos( substr( $content, 0, $pos ), "\n" );
			$line_start = false === $line_start ? 0 : $line_start + 1;
			$new        = substr( $content, 0, $line_start ) . $block . "\n" . substr( $content, $line_start );
		}
		$saved = false;
		if ( class_exists( '\WCPOS\WooCommercePOS\Templates' ) && method_exists( '\WCPOS\WooCommercePOS\Templates', 'save_raw_post_content' ) ) {
			$saved = (bool) \WCPOS\WooCommercePOS\Templates::save_raw_post_content( $post_id, $new );
		}
		if ( ! $saved ) {
			$saved = ! is_wp_error( wp_update_post( array( 'ID' => $post_id, 'post_content' => $new ), true ) );
		}
		clean_post_cache( $post_id );
		return rest_ensure_response( array( 'changed' => $saved, 'present' => str_contains( $new, 'TSE-Daten' ), 'length' => strlen( $new ) ) );
	}

	private static function error( HJ_TSE_Exception $e ): WP_Error {
		return new WP_Error( 'hj_tse_' . strtolower( $e->api_code ?: 'error' ), $e->getMessage(), array( 'status' => 502 ) );
	}

	public static function status( WP_REST_Request $r ) {
		$out = array(
			'settings'   => HJ_TSE_Settings::masked(),
			'configured' => HJ_TSE_Settings::is_configured(),
			'log_counts' => HJ_TSE_Log::counts(),
		);
		if ( HJ_TSE_Settings::is_configured() && 'yes' === (string) $r->get_param( 'remote' ) ) {
			try {
				$client        = new HJ_TSE_Client();
				$tss           = $client->get_tss( HJ_TSE_Settings::get( 'tss_id' ) );
				$cl            = $client->get_client( HJ_TSE_Settings::get( 'tss_id' ), HJ_TSE_Settings::get( 'client_id' ) );
				$out['tss']    = array( 'state' => $tss['state'] ?? '', 'serial_number' => $tss['serial_number'] ?? '', 'env' => $tss['_env'] ?? '' );
				$out['client'] = array( 'state' => $cl['state'] ?? '', 'serial_number' => $cl['serial_number'] ?? '' );
			} catch ( HJ_TSE_Exception $e ) {
				$out['remote_error'] = $e->getMessage();
			}
		}
		return rest_ensure_response( $out );
	}

	public static function settings( WP_REST_Request $r ) {
		$params = $r->get_json_params();
		if ( ! is_array( $params ) ) {
			$params = $r->get_params();
		}
		HJ_TSE_Settings::update( $params );
		return rest_ensure_response( array( 'settings' => HJ_TSE_Settings::masked() ) );
	}

	/** Create + initialise TSS and register the client; stores ids in settings. */
	public static function setup( WP_REST_Request $r ) {
		$serial = (string) ( $r->get_param( 'client_serial' ) ?: HJ_TSE_Settings::get( 'client_serial' ) );
		if ( '' !== HJ_TSE_Settings::get( 'tss_id' ) && 'yes' !== (string) $r->get_param( 'force' ) ) {
			return new WP_Error( 'hj_tse_exists', 'Es ist bereits eine TSS konfiguriert. Mit force=yes eine neue anlegen.', array( 'status' => 409 ) );
		}
		try {
			$client = new HJ_TSE_Client();
			$res    = $client->setup( $serial, array( 'shop' => get_bloginfo( 'name' ), 'site' => home_url(), 'pos' => 'WCPOS' ) );
			HJ_TSE_Settings::update(
				array(
					'tss_id'        => $res['tss_id'],
					'client_id'     => $res['client_id'],
					'client_serial' => $serial,
					'admin_pin'     => $res['admin_pin'],
					'admin_puk'     => $res['admin_puk'],
				)
			);
			return rest_ensure_response(
				array(
					'tss_id'     => $res['tss_id'],
					'client_id'  => $res['client_id'],
					'tss_serial' => $res['tss_serial'],
					'environment' => HJ_TSE_Settings::environment(),
				)
			);
		} catch ( HJ_TSE_Exception $e ) {
			return self::error( $e );
		}
	}

	/** Manually (re)sign an order, e.g. after an outage. */
	public static function sign( WP_REST_Request $r ) {
		$order = wc_get_order( (int) $r['id'] );
		if ( ! $order instanceof WC_Order ) {
			return new WP_Error( 'hj_tse_order', 'Bestellung nicht gefunden.', array( 'status' => 404 ) );
		}
		$type = (string) ( $r->get_param( 'receipt_type' ) ?: 'RECEIPT' );
		$ok   = HJ_TSE_Orders::finish( $order, $type );
		return rest_ensure_response( array( 'ok' => $ok, 'tse' => self::order_meta( $order ) ) );
	}

	public static function order( WP_REST_Request $r ) {
		$order = wc_get_order( (int) $r['id'] );
		if ( ! $order instanceof WC_Order ) {
			return new WP_Error( 'hj_tse_order', 'Bestellung nicht gefunden.', array( 'status' => 404 ) );
		}
		return rest_ensure_response( array( 'tse' => self::order_meta( $order ), 'receipt_fields' => HJ_TSE_Receipt::fields_for( $order ) ) );
	}

	private static function order_meta( WC_Abstract_Order $order ): array {
		$out = array();
		foreach ( $order->get_meta_data() as $m ) {
			$d = $m->get_data();
			if ( str_starts_with( (string) $d['key'], HJ_TSE_Orders::META_PREFIX ) ) {
				$out[ substr( $d['key'], strlen( HJ_TSE_Orders::META_PREFIX ) ) ] = $d['value'];
			}
		}
		return $out;
	}

	public static function log( WP_REST_Request $r ) {
		return rest_ensure_response( array( 'rows' => HJ_TSE_Log::recent( min( 500, max( 1, (int) ( $r->get_param( 'limit' ) ?: 50 ) ) ) ) ) );
	}

	public static function export( WP_REST_Request $r ) {
		$query = array();
		foreach ( array( 'start_date', 'end_date', 'start_transaction_number', 'end_transaction_number', 'client_id' ) as $k ) {
			$v = $r->get_param( $k );
			if ( null !== $v && '' !== $v ) {
				$query[ $k ] = $v;
			}
		}
		try {
			$id  = wp_generate_uuid4();
			$res = ( new HJ_TSE_Client() )->trigger_export( $id, $query );
			return rest_ensure_response( array( 'export_id' => $id, 'state' => $res['state'] ?? '', 'query' => $query ) );
		} catch ( HJ_TSE_Exception $e ) {
			return self::error( $e );
		}
	}

	public static function export_status( WP_REST_Request $r ) {
		try {
			$client = new HJ_TSE_Client();
			$res    = $client->get_export( (string) $r['id'] );
			$out    = array( 'state' => $res['state'] ?? '', 'time_expiration' => $res['time_expiration'] ?? null, 'error' => $res['error'] ?? null );
			if ( 'COMPLETED' === ( $res['state'] ?? '' ) && 'yes' === (string) $r->get_param( 'download' ) ) {
				$tar = $client->download_export( (string) $r['id'] );
				$dir = wp_upload_dir();
				$sub = trailingslashit( $dir['basedir'] ) . 'hj-tse-exports';
				wp_mkdir_p( $sub );
				if ( ! file_exists( $sub . '/.htaccess' ) ) {
					file_put_contents( $sub . '/.htaccess', "Deny from all\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions
				}
				$file = $sub . '/tse-export-' . gmdate( 'Ymd-His' ) . '-' . substr( (string) $r['id'], 0, 8 ) . '.tar';
				file_put_contents( $file, $tar ); // phpcs:ignore WordPress.WP.AlternativeFunctions
				$out['file']  = $file;
				$out['bytes'] = strlen( $tar );
			}
			return rest_ensure_response( $out );
		} catch ( HJ_TSE_Exception $e ) {
			return self::error( $e );
		}
	}
}
