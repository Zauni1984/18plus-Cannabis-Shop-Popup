<?php
defined( 'ABSPATH' ) || exit;

/**
 * Hooks the WooCommerce order lifecycle of POS orders into the TSE.
 *
 * Lifecycle: order created -> TSE transaction ACTIVE (rev 1)
 *            order paid/completed -> FINISHED with amounts (rev 2)
 *            order cancelled before payment -> FINISHED with receipt_type ABORT
 *            refund -> separate transaction, RECEIPT with negative amounts
 */
class HJ_TSE_Orders {

	const META_PREFIX = '_hj_tse_';

	public static function init(): void {
		// Priority 5 so the signature exists before WCPOS cloud-print triggers (priority 20).
		add_action( 'woocommerce_new_order', array( __CLASS__, 'on_new_order' ), 5, 1 );
		add_action( 'woocommerce_payment_complete', array( __CLASS__, 'on_paid' ), 5, 1 );
		add_action( 'woocommerce_order_status_completed', array( __CLASS__, 'on_paid' ), 5, 1 );
		add_action( 'woocommerce_order_status_processing', array( __CLASS__, 'on_paid' ), 5, 1 );
		add_action( 'woocommerce_order_status_cancelled', array( __CLASS__, 'on_cancelled' ), 5, 1 );
		add_action( 'woocommerce_order_refunded', array( __CLASS__, 'on_refunded' ), 5, 2 );
	}

	/* ---------- helpers ---------- */

	public static function is_pos_order( WC_Order $order ): bool {
		if ( 'woocommerce-pos' === $order->get_created_via() ) {
			return true;
		}
		if ( '' !== (string) $order->get_meta( '_woocommerce_pos_uuid' ) ) {
			return true;
		}
		if ( '' !== (string) $order->get_meta( '_pos' ) || '' !== (string) $order->get_meta( '_pos_user' ) ) {
			return true; // Online order that was taken over and paid at the POS.
		}
		if ( in_array( (string) $order->get_payment_method(), self::pos_gateways(), true ) ) {
			return true; // Cash collected in the store ("Bar (nur für Laden)") or POS gateways.
		}
		return (bool) apply_filters( 'hj_tse_is_pos_order', false, $order );
	}

	/** Gateways whose payment happens at the counter and therefore belongs into the cash register. */
	public static function pos_gateways(): array {
		return (array) apply_filters( 'hj_tse_pos_gateways', array( 'pos_cash', 'pos_card', 'sumup_terminal_for_woocommerce', 'invoice' ) );
	}

	public static function meta( WC_Abstract_Order $order, string $key, $default = '' ) {
		$v = $order->get_meta( self::META_PREFIX . $key );
		return ( '' === $v || null === $v ) ? $default : $v;
	}

	private static function set_meta( WC_Abstract_Order $order, array $values ): void {
		foreach ( $values as $k => $v ) {
			$order->update_meta_data( self::META_PREFIX . $k, $v );
		}
	}

	private static function ready(): bool {
		return HJ_TSE_Settings::is_enabled() && HJ_TSE_Settings::is_configured();
	}

	/* ---------- hooks ---------- */

	public static function on_new_order( $order_id ): void {
		$order = wc_get_order( $order_id );
		if ( ! $order instanceof WC_Order || ! self::is_pos_order( $order ) ) {
			return;
		}
		self::start( $order );
	}

	public static function on_paid( $order_id ): void {
		$order = wc_get_order( $order_id );
		if ( ! $order instanceof WC_Order || ! self::is_pos_order( $order ) ) {
			return;
		}
		self::finish( $order, 'RECEIPT' );
	}

	public static function on_cancelled( $order_id ): void {
		$order = wc_get_order( $order_id );
		if ( ! $order instanceof WC_Order || ! self::is_pos_order( $order ) ) {
			return;
		}
		if ( 'FINISHED' === self::meta( $order, 'state' ) ) {
			return; // Paid receipts are reversed via refunds, not aborted.
		}
		self::finish( $order, 'ABORT' );
	}

	public static function on_refunded( $order_id, $refund_id ): void {
		$order  = wc_get_order( $order_id );
		$refund = wc_get_order( $refund_id );
		if ( ! $order instanceof WC_Order || ! $refund instanceof WC_Order_Refund || ! self::is_pos_order( $order ) ) {
			return;
		}
		self::sign_refund( $order, $refund );
	}

	/* ---------- core operations ---------- */

	/**
	 * Start the TSE transaction (state ACTIVE). Idempotent.
	 */
	public static function start( WC_Order $order ): bool {
		if ( ! self::ready() ) {
			return false;
		}
		if ( '' !== self::meta( $order, 'tx_id' ) ) {
			return true;
		}
		$tx_id = wp_generate_uuid4();
		try {
			$client = new HJ_TSE_Client();
			$res    = $client->start_transaction( $tx_id );
			self::set_meta(
				$order,
				array(
					'tx_id'      => $tx_id,
					'revision'   => 1,
					'state'      => 'ACTIVE',
					'tx_number'  => (int) ( $res['number'] ?? 0 ),
					'time_start' => (int) ( $res['time_start'] ?? 0 ),
					'error'      => '',
				)
			);
			$order->save();
			HJ_TSE_Log::insert(
				array(
					'order_id'   => $order->get_id(),
					'tx_id'      => $tx_id,
					'tx_number'  => (int) ( $res['number'] ?? 0 ),
					'revision'   => 1,
					'state'      => 'ACTIVE',
					'time_start' => (int) ( $res['time_start'] ?? 0 ),
					'client_serial' => (string) ( $res['client_serial_number'] ?? HJ_TSE_Settings::get( 'client_serial' ) ),
					'tss_serial' => (string) ( $res['tss_serial_number'] ?? '' ),
				)
			);
			return true;
		} catch ( HJ_TSE_Exception $e ) {
			self::record_error( $order, 'start', $e );
			return false;
		}
	}

	/**
	 * Finish the TSE transaction with the order amounts. Idempotent.
	 */
	public static function finish( WC_Order $order, string $receipt_type = 'RECEIPT' ): bool {
		if ( ! self::ready() ) {
			return false;
		}
		$state = self::meta( $order, 'state' );
		if ( in_array( $state, array( 'FINISHED', 'CANCELLED' ), true ) ) {
			return true;
		}
		// Start was never done (e.g. outage at creation): start now, then finish.
		if ( '' === self::meta( $order, 'tx_id' ) ) {
			if ( ! self::start( $order ) ) {
				return false;
			}
		}
		$tx_id    = (string) self::meta( $order, 'tx_id' );
		$revision = (int) self::meta( $order, 'revision', 1 ) + 1;
		$receipt  = HJ_TSE_Amounts::build( $order, $receipt_type, (string) $order->get_payment_method(), 1 );
		try {
			$client = new HJ_TSE_Client();
			$res    = $client->finish_transaction( $tx_id, $revision, $receipt, 'FINISHED' );
			self::store_signature( $order, $res, $revision, $receipt_type, $receipt, (float) $order->get_total() );
			$order->add_order_note(
				sprintf(
					'TSE signiert (%s): Transaktion %d, Signaturzähler %d, %s.',
					$receipt_type,
					(int) ( $res['number'] ?? 0 ),
					(int) ( $res['signature']['counter'] ?? 0 ),
					HJ_TSE_Settings::environment()
				)
			);
			return true;
		} catch ( HJ_TSE_Exception $e ) {
			self::record_error( $order, 'finish', $e );
			return false;
		}
	}

	/**
	 * Refund: own transaction (start + finish) with negative amounts, signature stored on the refund.
	 */
	public static function sign_refund( WC_Order $order, WC_Order_Refund $refund ): bool {
		if ( ! self::ready() ) {
			return false;
		}
		if ( 'FINISHED' === self::meta( $refund, 'state' ) ) {
			return true;
		}
		$tx_id   = wp_generate_uuid4();
		$receipt = HJ_TSE_Amounts::build( $refund, 'RECEIPT', (string) $order->get_payment_method(), -1 );
		try {
			$client = new HJ_TSE_Client();
			$client->start_transaction( $tx_id );
			$res = $client->finish_transaction( $tx_id, 2, $receipt, 'FINISHED' );
			self::set_meta( $refund, array( 'tx_id' => $tx_id, 'parent_tx_number' => self::meta( $order, 'tx_number' ) ) );
			self::store_signature( $refund, $res, 2, 'RECEIPT', $receipt, -abs( (float) $refund->get_total() ), $order->get_id(), $refund->get_id() );
			$order->add_order_note(
				sprintf(
					'TSE Storno signiert: Rückerstattung #%d, Transaktion %d, Signaturzähler %d.',
					$refund->get_id(),
					(int) ( $res['number'] ?? 0 ),
					(int) ( $res['signature']['counter'] ?? 0 )
				)
			);
			return true;
		} catch ( HJ_TSE_Exception $e ) {
			self::record_error( $refund, 'refund', $e, $order );
			return false;
		}
	}

	/* ---------- persistence ---------- */

	private static function store_signature( WC_Abstract_Order $obj, array $res, int $revision, string $receipt_type, array $receipt, float $total, int $order_id = 0, int $refund_id = 0 ): void {
		$kind = $refund_id ? 'refund' : ( 'ABORT' === $receipt_type ? 'abort' : 'sale' );
		$sig = $res['signature'] ?? array();
		self::set_meta(
			$obj,
			array(
				'revision'          => $revision,
				'state'             => (string) ( $res['state'] ?? 'FINISHED' ),
				'receipt_type'      => $receipt_type,
				'tx_number'         => (int) ( $res['number'] ?? 0 ),
				'time_start'        => (int) ( $res['time_start'] ?? 0 ),
				'time_end'          => (int) ( $res['time_end'] ?? 0 ),
				'signature'         => (string) ( $sig['value'] ?? '' ),
				'signature_counter' => (int) ( $sig['counter'] ?? 0 ),
				'signature_algorithm' => (string) ( $sig['algorithm'] ?? '' ),
				'public_key'        => (string) ( $sig['public_key'] ?? '' ),
				'qr_code_data'      => (string) ( $res['qr_code_data'] ?? '' ),
				'tss_serial'        => (string) ( $res['tss_serial_number'] ?? '' ),
				'client_serial'     => (string) ( $res['client_serial_number'] ?? '' ),
				'timestamp_format'  => (string) ( $res['log']['timestamp_format'] ?? 'unixTime' ),
				'certificate_serial' => (string) ( $res['certificate_serial'] ?? '' ),
				'environment'       => HJ_TSE_Settings::environment(),
				'error'             => '',
			)
		);
		$obj->save();
		HJ_TSE_Log::insert(
			array(
				'order_id'          => $order_id ?: $obj->get_id(),
				'refund_id'         => $refund_id,
				'tx_id'             => (string) self::meta( $obj, 'tx_id' ),
				'tx_number'         => (int) ( $res['number'] ?? 0 ),
				'revision'          => $revision,
				'state'             => (string) ( $res['state'] ?? 'FINISHED' ),
				'receipt_type'      => $receipt_type,
				'total'             => $total,
				'vat_json'          => wp_json_encode( $receipt['amounts_per_vat_rate'] ),
				'payment_json'      => wp_json_encode( $receipt['amounts_per_payment_type'] ),
				'time_start'        => (int) ( $res['time_start'] ?? 0 ),
				'time_end'          => (int) ( $res['time_end'] ?? 0 ),
				'signature_counter' => (int) ( $sig['counter'] ?? 0 ),
				'signature'         => (string) ( $sig['value'] ?? '' ),
				'qr_code_data'      => (string) ( $res['qr_code_data'] ?? '' ),
				'client_serial'     => (string) ( $res['client_serial_number'] ?? '' ),
				'tss_serial'        => (string) ( $res['tss_serial_number'] ?? '' ),
				'kind'              => $kind,
			)
		);
	}

	private static function record_error( WC_Abstract_Order $obj, string $phase, HJ_TSE_Exception $e, ?WC_Order $parent = null ): void {
		$msg = sprintf( '[%s] %s (%s)', $phase, $e->getMessage(), $e->api_code );
		self::set_meta(
			$obj,
			array(
				'state' => 'ERROR',
				'error' => $msg,
				'error_time' => time(),
			)
		);
		$obj->save();
		HJ_TSE_Log::insert(
			array(
				'order_id'  => $parent ? $parent->get_id() : $obj->get_id(),
				'refund_id' => $parent ? $obj->get_id() : 0,
				'tx_id'     => (string) self::meta( $obj, 'tx_id' ),
				'state'     => 'ERROR',
				'error'     => $msg,
				'total'     => (float) $obj->get_total(),
				'kind'      => 'finish' === $phase ? 'sale' : ( 'refund' === $phase ? 'refund' : '' ),
			)
		);
		$note_target = $parent ?? ( $obj instanceof WC_Order ? $obj : null );
		if ( $note_target ) {
			$note_target->add_order_note( 'TSE-Ausfall: ' . $msg );
		}
		if ( function_exists( 'wc_get_logger' ) ) {
			wc_get_logger()->error( $msg, array( 'source' => 'hanfjack-tse' ) );
		}
	}
}
