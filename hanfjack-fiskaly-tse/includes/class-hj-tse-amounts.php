<?php
defined( 'ABSPATH' ) || exit;

/**
 * Maps a WooCommerce order (or refund) to the fiskaly standard_v1 receipt amounts.
 *
 * Amounts are gross (incl. VAT) per VAT rate and per payment type, as strings with two decimals.
 */
class HJ_TSE_Amounts {

	/** Map of German VAT percentages to fiskaly vat_rate keys. */
	const RATE_MAP = array(
		'19.0000' => 'NORMAL',
		'7.0000'  => 'REDUCED_1',
		'10.7000' => 'SPECIAL_RATE_1',
		'5.5000'  => 'SPECIAL_RATE_2',
		'0.0000'  => 'NULL',
		'16.0000' => 'NORMAL',
		'5.0000'  => 'REDUCED_1',
	);

	/** Cash gateways; everything else counts as NON_CASH. */
	const CASH_GATEWAYS = array( 'pos_cash', 'cod', 'invoice' );

	/**
	 * Build the standard_v1 receipt body.
	 *
	 * @param WC_Abstract_Order $order        Order or refund.
	 * @param string            $receipt_type RECEIPT, ABORT, CANCELLATION ...
	 * @param string            $payment_method Gateway id of the parent order.
	 * @param int               $sign         1 for sales, -1 for refunds.
	 */
	public static function build( WC_Abstract_Order $order, string $receipt_type, string $payment_method, int $sign = 1 ): array {
		$per_rate = self::amounts_per_vat_rate( $order );
		$total    = 0.0;
		$vat_list = array();
		foreach ( $per_rate as $rate_key => $amount ) {
			$amount = round( $amount * $sign, 2 );
			$total += $amount;
			$vat_list[] = array(
				'vat_rate' => $rate_key,
				'amount'   => self::money( $amount ),
			);
		}
		if ( empty( $vat_list ) ) {
			$vat_list[] = array(
				'vat_rate' => 'NULL',
				'amount'   => '0.00',
			);
		}
		$payment_type = in_array( $payment_method, self::CASH_GATEWAYS, true ) ? 'CASH' : 'NON_CASH';
		return array(
			'receipt_type'             => $receipt_type,
			'amounts_per_vat_rate'     => $vat_list,
			'amounts_per_payment_type' => array(
				array(
					'payment_type' => $payment_type,
					'amount'       => self::money( round( $total, 2 ) ),
				),
			),
		);
	}

	/**
	 * Gross amounts grouped by fiskaly vat_rate key. Rounding differences against the
	 * order total are pushed into the largest bucket so the receipt total matches.
	 *
	 * @return array<string,float>
	 */
	public static function amounts_per_vat_rate( WC_Abstract_Order $order ): array {
		$buckets = array();
		$items   = array_merge(
			$order->get_items( 'line_item' ),
			$order->get_items( 'fee' ),
			$order->get_items( 'shipping' )
		);
		foreach ( $items as $item ) {
			$net   = (float) $item->get_total();
			$tax   = (float) $item->get_total_tax();
			$taxes = $item->get_taxes();
			$rates = array();
			if ( ! empty( $taxes['total'] ) ) {
				foreach ( $taxes['total'] as $rate_id => $tax_amount ) {
					if ( '' === $tax_amount || null === $tax_amount ) {
						continue;
					}
					$rates[ (string) $rate_id ] = (float) $tax_amount;
				}
			}
			if ( empty( $rates ) ) {
				$key = self::rate_key( 0.0 );
				$buckets[ $key ] = ( $buckets[ $key ] ?? 0.0 ) + $net + $tax;
				continue;
			}
			// Normally one rate per line; if several, split net proportionally by tax share.
			$tax_sum = array_sum( array_map( 'abs', $rates ) );
			foreach ( $rates as $rate_id => $tax_amount ) {
				$percent = (float) WC_Tax::get_rate_percent_value( (int) $rate_id );
				$key     = self::rate_key( $percent );
				$share   = $tax_sum > 0 ? abs( $tax_amount ) / $tax_sum : 1 / count( $rates );
				$buckets[ $key ] = ( $buckets[ $key ] ?? 0.0 ) + $net * $share + $tax_amount;
			}
		}
		foreach ( $buckets as $k => $v ) {
			$buckets[ $k ] = round( $v, 2 );
		}
		$order_total = abs( (float) $order->get_total() );
		$diff        = round( $order_total - array_sum( $buckets ), 2 );
		if ( 0.0 !== $diff && ! empty( $buckets ) ) {
			arsort( $buckets );
			$first = array_key_first( $buckets );
			$buckets[ $first ] = round( $buckets[ $first ] + $diff, 2 );
		}
		return $buckets;
	}

	public static function rate_key( float $percent ): string {
		$k = number_format( $percent, 4, '.', '' );
		if ( isset( self::RATE_MAP[ $k ] ) ) {
			return self::RATE_MAP[ $k ];
		}
		// Unknown rate: let fiskaly validate the numeric value (e.g. "19").
		return rtrim( rtrim( $k, '0' ), '.' );
	}

	public static function money( float $amount ): string {
		return number_format( $amount, 2, '.', '' );
	}
}
