<?php
defined( 'ABSPATH' ) || exit;

/**
 * Kassenbuch: cash deposits and withdrawals, signed as TSE transfers (AVTransfer).
 */
class HJ_TSE_Cashbook {

	const REASONS = array(
		'Anfangsbestand' => 'Anfangsbestand / Wechselgeld zu Tagesbeginn',
		'Privateinlage'  => 'Einlage (privat)',
		'Privatentnahme' => 'Entnahme (privat)',
		'Geldtransit'    => 'Geldtransit (Bankeinzahlung / Bankabhebung)',
		'Auszahlung'     => 'Auszahlung (z. B. Barauslage)',
		'Einzahlung'     => 'Einzahlung (sonstige)',
	);

	/**
	 * Book a cash movement. Positive amount = money into the drawer, negative = out.
	 */
	public static function book( float $amount, string $reason, string $text, int $user_id = 0 ): array {
		if ( ! isset( self::REASONS[ $reason ] ) ) {
			throw new HJ_TSE_Exception( 'Unbekannter Buchungsgrund.', 'E_REASON' );
		}
		$amount = round( $amount, 2 );
		if ( 0.0 === $amount ) {
			throw new HJ_TSE_Exception( 'Betrag darf nicht 0 sein.', 'E_AMOUNT' );
		}
		if ( ! HJ_TSE_Settings::is_configured() ) {
			throw new HJ_TSE_Exception( 'TSE nicht konfiguriert.', 'E_CONFIG' );
		}
		$user      = $user_id ? get_userdata( $user_id ) : null;
		$user_name = $user ? $user->display_name : 'System';
		$receipt   = array(
			'receipt_type'             => 'TRANSFER',
			'amounts_per_vat_rate'     => array( array( 'vat_rate' => 'NULL', 'amount' => HJ_TSE_Amounts::money( $amount ) ) ),
			'amounts_per_payment_type' => array( array( 'payment_type' => 'CASH', 'amount' => HJ_TSE_Amounts::money( $amount ) ) ),
		);
		$tx_id  = wp_generate_uuid4();
		$client = new HJ_TSE_Client();
		$note   = wp_json_encode( array( 'reason' => $reason, 'text' => $text, 'user_id' => $user_id, 'user_name' => $user_name ) );
		try {
			$client->start_transaction( $tx_id );
			$res = $client->finish_transaction( $tx_id, 2, $receipt, 'FINISHED' );
		} catch ( HJ_TSE_Exception $e ) {
			HJ_TSE_Log::insert(
				array(
					'tx_id'  => $tx_id,
					'state'  => 'ERROR',
					'kind'   => $amount > 0 ? 'pay_in' : 'pay_out',
					'receipt_type' => 'TRANSFER',
					'total'  => $amount,
					'note'   => $note,
					'error'  => '[transfer] ' . $e->getMessage(),
				)
			);
			throw $e;
		}
		$sig = $res['signature'] ?? array();
		$id  = HJ_TSE_Log::insert(
			array(
				'tx_id'             => $tx_id,
				'tx_number'         => (int) ( $res['number'] ?? 0 ),
				'revision'          => 2,
				'state'             => 'FINISHED',
				'receipt_type'      => 'TRANSFER',
				'kind'              => $amount > 0 ? 'pay_in' : 'pay_out',
				'total'             => $amount,
				'vat_json'          => wp_json_encode( $receipt['amounts_per_vat_rate'] ),
				'payment_json'      => wp_json_encode( $receipt['amounts_per_payment_type'] ),
				'time_start'        => (int) ( $res['time_start'] ?? 0 ),
				'time_end'          => (int) ( $res['time_end'] ?? 0 ),
				'signature_counter' => (int) ( $sig['counter'] ?? 0 ),
				'signature'         => (string) ( $sig['value'] ?? '' ),
				'qr_code_data'      => (string) ( $res['qr_code_data'] ?? '' ),
				'client_serial'     => (string) ( $res['client_serial_number'] ?? '' ),
				'tss_serial'        => (string) ( $res['tss_serial_number'] ?? '' ),
				'note'              => $note,
			)
		);
		return array(
			'log_id'            => $id,
			'tx_number'         => (int) ( $res['number'] ?? 0 ),
			'signature_counter' => (int) ( $sig['counter'] ?? 0 ),
			'amount'            => $amount,
			'reason'            => $reason,
		);
	}
}
