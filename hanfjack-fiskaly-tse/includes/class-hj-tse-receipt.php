<?php
defined( 'ABSPATH' ) || exit;

/**
 * Injects the TSE data into WCPOS receipt payloads (fiscal.* section).
 */
class HJ_TSE_Receipt {

	public static function init(): void {
		add_filter( 'woocommerce_pos_receipt_data', array( __CLASS__, 'filter_receipt_data' ), 20, 3 );
	}

	/**
	 * @param array  $data  Receipt payload.
	 * @param object $order WC_Order (or empty order in preview mode).
	 * @param string $mode  live|fiscal|preview.
	 */
	public static function filter_receipt_data( $data, $order, $mode ) {
		if ( ! is_array( $data ) ) {
			return $data;
		}
		if ( 'preview' === $mode || ! $order instanceof WC_Order || ! $order->get_id() ) {
			return $data;
		}
		if ( ! HJ_TSE_Orders::is_pos_order( $order ) ) {
			return $data;
		}
		$fields = self::fields_for( $order );
		if ( empty( $fields ) ) {
			return $data;
		}
		$data['fiscal'] = is_array( $data['fiscal'] ?? null ) ? $data['fiscal'] : array();
		$state          = (string) HJ_TSE_Orders::meta( $order, 'state' );
		if ( 'FINISHED' === $state ) {
			$label = (string) HJ_TSE_Settings::get( 'receipt_label', 'Kassenbeleg' );
			if ( '' === (string) ( $data['fiscal']['document_label'] ?? '' ) ) {
				$data['fiscal']['document_label'] = $label;
			}
			$data['fiscal']['qr_payload']        = (string) HJ_TSE_Orders::meta( $order, 'qr_code_data' );
			$data['fiscal']['signed_at']         = gmdate( 'Y-m-d\TH:i:s\Z', (int) HJ_TSE_Orders::meta( $order, 'time_end', 0 ) );
			$data['fiscal']['signature_excerpt'] = substr( (string) HJ_TSE_Orders::meta( $order, 'signature' ), 0, 8 );
			$data['fiscal']['hash']              = (string) HJ_TSE_Orders::meta( $order, 'signature' );
			$data['fiscal']['sequence']          = (string) HJ_TSE_Orders::meta( $order, 'tx_number' );
		}
		$existing = is_array( $data['fiscal']['extra_fields'] ?? null ) ? $data['fiscal']['extra_fields'] : array();
		$data['fiscal']['extra_fields'] = array_merge( $existing, $fields );
		return $data;
	}

	/**
	 * Plain-text TSE fields for the receipt (label/value pairs).
	 */
	public static function fields_for( WC_Order $order ): array {
		$state = (string) HJ_TSE_Orders::meta( $order, 'state' );
		if ( 'FINISHED' === $state ) {
			$tz  = wp_timezone();
			$fmt = static function ( $ts ) use ( $tz ) {
				$ts = (int) $ts;
				if ( $ts <= 0 ) {
					return '';
				}
				return ( new DateTimeImmutable( '@' . $ts ) )->setTimezone( $tz )->format( 'd.m.Y H:i:s' );
			};
			$fields = array(
				array( 'label' => 'Kassen-ID', 'value' => (string) HJ_TSE_Orders::meta( $order, 'client_serial' ) ),
				array( 'label' => 'TSE-Seriennr.', 'value' => (string) HJ_TSE_Orders::meta( $order, 'tss_serial' ) ),
				array( 'label' => 'TSE-Transaktion', 'value' => (string) HJ_TSE_Orders::meta( $order, 'tx_number' ) ),
				array( 'label' => 'TSE-Start', 'value' => $fmt( HJ_TSE_Orders::meta( $order, 'time_start' ) ) ),
				array( 'label' => 'TSE-Ende', 'value' => $fmt( HJ_TSE_Orders::meta( $order, 'time_end' ) ) ),
				array( 'label' => 'TSE-Signaturzähler', 'value' => (string) HJ_TSE_Orders::meta( $order, 'signature_counter' ) ),
			);
			if ( 'yes' === HJ_TSE_Settings::get( 'print_fields' ) ) {
				$fields[] = array( 'label' => 'TSE-Zeitformat', 'value' => (string) HJ_TSE_Orders::meta( $order, 'timestamp_format', 'unixTime' ) );
				$fields[] = array( 'label' => 'TSE-Hashalgorithmus', 'value' => (string) HJ_TSE_Orders::meta( $order, 'signature_algorithm' ) );
				$fields[] = array( 'label' => 'TSE-Signatur', 'value' => (string) HJ_TSE_Orders::meta( $order, 'signature' ) );
			}
			if ( 'TEST' === (string) HJ_TSE_Orders::meta( $order, 'environment' ) ) {
				$fields[] = array( 'label' => 'TSE-Umgebung', 'value' => 'TEST (kein gültiger Kassenbeleg)' );
			}
			return $fields;
		}
		if ( 'ERROR' === $state ) {
			return array(
				array( 'label' => 'TSE', 'value' => 'TSE ausgefallen, Beleg ohne Signatur' ),
				array( 'label' => 'Kassen-ID', 'value' => (string) HJ_TSE_Settings::get( 'client_serial' ) ),
				array( 'label' => 'TSE-Fehler', 'value' => wp_strip_all_tags( (string) HJ_TSE_Orders::meta( $order, 'error' ) ) ),
			);
		}
		return array();
	}
}
