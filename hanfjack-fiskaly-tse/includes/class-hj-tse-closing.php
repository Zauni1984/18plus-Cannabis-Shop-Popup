<?php
defined( 'ABSPATH' ) || exit;

/**
 * Daily cash point closing (Kassenabschluss) and DSFinV-K submission via fiskaly.
 */
class HJ_TSE_Closing {

	/** fiskaly vat_rate key -> DSFinV-K vat_definition_export_id. */
	const VAT_IDS = array(
		'NORMAL'         => 1,
		'REDUCED_1'      => 2,
		'SPECIAL_RATE_1' => 3,
		'SPECIAL_RATE_2' => 4,
		'NULL'           => 5,
	);

	/** WC tax percent -> vat_definition_export_id. */
	const PERCENT_IDS = array(
		'19.0000' => 1,
		'7.0000'  => 2,
		'10.7000' => 3,
		'5.5000'  => 4,
		'0.0000'  => 5,
		'16.0000' => 7,
		'5.0000'  => 8,
	);

	public static function init(): void {
		add_action( 'hj_tse_daily_closing', array( __CLASS__, 'run_daily' ) );
		add_action( 'hj_tse_poll_closings', array( __CLASS__, 'poll_pending' ) );
		add_action( 'init', array( __CLASS__, 'schedule' ) );
	}

	public static function schedule(): void {
		if ( ! wp_next_scheduled( 'hj_tse_daily_closing' ) ) {
			$tz   = wp_timezone();
			$next = new DateTimeImmutable( 'today 23:58', $tz );
			if ( $next->getTimestamp() <= time() ) {
				$next = $next->modify( '+1 day' );
			}
			wp_schedule_event( $next->getTimestamp(), 'daily', 'hj_tse_daily_closing' );
		}
		if ( ! wp_next_scheduled( 'hj_tse_poll_closings' ) ) {
			wp_schedule_event( time() + 600, 'hourly', 'hj_tse_poll_closings' );
		}
	}

	public static function today(): string {
		return ( new DateTimeImmutable( 'now', wp_timezone() ) )->format( 'Y-m-d' );
	}

	/** Cron: close every open day up to and including today. */
	public static function run_daily(): void {
		if ( ! HJ_TSE_Settings::is_configured() ) {
			return;
		}
		$today = self::today();
		$days  = HJ_TSE_Log::open_days( $today );
		$days[] = $today;
		foreach ( array_unique( $days ) as $day ) {
			try {
				self::create_for_date( $day );
			} catch ( HJ_TSE_Exception $e ) {
				if ( function_exists( 'wc_get_logger' ) ) {
					wc_get_logger()->error( 'Kassenabschluss ' . $day . ': ' . $e->getMessage(), array( 'source' => 'hanfjack-tse' ) );
				}
			}
		}
		self::poll_pending();
	}

	/**
	 * Build and submit the closing for one business day. Returns the closing row or null when nothing to close.
	 */
	public static function create_for_date( string $business_date ): ?array {
		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $business_date ) ) {
			throw new HJ_TSE_Exception( 'Ungültiges Datum.', 'E_DATE' );
		}
		$rows = HJ_TSE_Log::rows_for_day( $business_date );
		if ( empty( $rows ) ) {
			return null;
		}
		self::ensure_cash_register();
		$export_id = (int) get_option( 'hj_tse_closing_counter', 0 ) + 1;
		$build     = self::build( $business_date, $rows, $export_id );
		$uuid      = wp_generate_uuid4();
		$id        = HJ_TSE_Log::insert_closing(
			array(
				'business_date' => $business_date,
				'closing_uuid'  => $uuid,
				'export_id'     => $export_id,
				'client_id'     => (string) HJ_TSE_Settings::get( 'client_id' ),
				'state'         => 'CREATING',
				'tx_count'      => count( $build['body']['transactions'] ),
				'totals_json'   => wp_json_encode( $build['totals'] ),
				'body_json'     => wp_json_encode( $build['body'] ),
			)
		);
		update_option( 'hj_tse_closing_counter', $export_id, false );
		try {
			$res = ( new HJ_TSE_Dsfinvk() )->put_closing( $uuid, $build['body'] );
		} catch ( HJ_TSE_Exception $e ) {
			HJ_TSE_Log::update_closing( $id, array( 'state' => 'ERROR', 'error' => $e->getMessage() ) );
			throw $e;
		}
		HJ_TSE_Log::update_closing( $id, array( 'state' => (string) ( $res['state'] ?? 'PENDING' ) ) );
		HJ_TSE_Log::assign_closing( $build['row_ids'], $id );
		// Short synchronous poll so the admin sees the result immediately.
		for ( $i = 0; $i < 4; $i++ ) {
			sleep( 3 );
			$state = self::refresh( $id );
			if ( in_array( $state, array( 'COMPLETED', 'ERROR' ), true ) ) {
				break;
			}
		}
		return HJ_TSE_Log::get_closing( $id );
	}

	public static function refresh( int $id ): string {
		$row = HJ_TSE_Log::get_closing( $id );
		if ( ! $row || '' === $row['closing_uuid'] ) {
			return '';
		}
		try {
			$res   = ( new HJ_TSE_Dsfinvk() )->get_closing( $row['closing_uuid'] );
			$state = (string) ( $res['state'] ?? $row['state'] );
			$upd   = array( 'state' => $state );
			if ( 'ERROR' === $state ) {
				$upd['error'] = wp_json_encode( $res['error'] ?? 'unbekannt', JSON_UNESCAPED_UNICODE );
				// Release the rows so the day can be closed again after a fix.
				global $wpdb;
				$table = HJ_TSE_Log::table();
				$wpdb->query( $wpdb->prepare( "UPDATE {$table} SET closing_id = 0 WHERE closing_id = %d", $id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			}
			if ( 'COMPLETED' === $state ) {
				$upd['completed_gmt'] = gmdate( 'Y-m-d H:i:s' );
			}
			HJ_TSE_Log::update_closing( $id, $upd );
			return $state;
		} catch ( HJ_TSE_Exception $e ) {
			return (string) $row['state'];
		}
	}

	public static function poll_pending(): void {
		foreach ( HJ_TSE_Log::pending_closings() as $row ) {
			self::refresh( (int) $row['id'] );
		}
	}

	public static function ensure_cash_register(): void {
		$client_id = (string) HJ_TSE_Settings::get( 'client_id' );
		if ( get_option( 'hj_tse_cash_register_registered' ) === $client_id . '|' . HJ_TSE_VERSION ) {
			return;
		}
		( new HJ_TSE_Dsfinvk() )->register_cash_register();
		update_option( 'hj_tse_cash_register_registered', $client_id . '|' . HJ_TSE_VERSION, false );
	}

	/* ---------- builder ---------- */

	public static function vat_id_for_percent( float $percent ): int {
		$k = number_format( $percent, 4, '.', '' );
		return self::PERCENT_IDS[ $k ] ?? 1;
	}

	private static function vat_entry( int $vat_id, float $incl, float $excl, float $vat ): array {
		return array(
			'vat_definition_export_id' => $vat_id,
			'incl_vat'                 => round( $incl, 2 ),
			'excl_vat'                 => round( $excl, 2 ),
			'vat'                      => round( $vat, 2 ),
		);
	}

	private static function add_vat( array &$agg, int $vat_id, float $incl, float $excl, float $vat ): void {
		if ( ! isset( $agg[ $vat_id ] ) ) {
			$agg[ $vat_id ] = array( 0.0, 0.0, 0.0 );
		}
		$agg[ $vat_id ][0] += $incl;
		$agg[ $vat_id ][1] += $excl;
		$agg[ $vat_id ][2] += $vat;
	}

	private static function vat_list( array $agg ): array {
		ksort( $agg );
		$out = array();
		foreach ( $agg as $id => $v ) {
			$out[] = self::vat_entry( (int) $id, $v[0], $v[1], $v[2] );
		}
		return $out;
	}

	public static function payment_type_for( string $gateway ): array {
		if ( in_array( $gateway, HJ_TSE_Amounts::CASH_GATEWAYS, true ) ) {
			return array( 'Bar', 'Bar' );
		}
		if ( str_contains( $gateway, 'sumup' ) ) {
			return array( 'Unbar', 'SumUp Terminal' );
		}
		if ( 'pos_card' === $gateway ) {
			return array( 'Unbar', 'Kartenzahlung' );
		}
		return array( 'Unbar', $gateway );
	}

	private static function user_for( ?WC_Order $order, array $row ): array {
		$uid = 0;
		if ( $order ) {
			$uid = (int) $order->get_meta( '_pos_user' );
		}
		if ( ! $uid && ! empty( $row['note'] ) ) {
			$n   = json_decode( (string) $row['note'], true );
			$uid = (int) ( $n['user_id'] ?? 0 );
		}
		$u = $uid ? get_userdata( $uid ) : null;
		return array( 'user_export_id' => (string) ( $uid ?: '0' ), 'name' => $u ? $u->display_name : 'Kasse' );
	}

	private static function buyer_for( ?WC_Order $order ): array {
		if ( $order && $order->get_customer_id() ) {
			$name = trim( $order->get_formatted_billing_full_name() );
			return array( 'type' => 'Kunde', 'buyer_export_id' => (string) $order->get_customer_id(), 'name' => '' !== $name ? $name : 'Kunde' );
		}
		return array( 'type' => 'Kunde', 'buyer_export_id' => '0', 'name' => 'Kunde' );
	}

	/**
	 * Lines + aggregated VAT for an order or refund. $sign = -1 for refunds.
	 */
	private static function lines_for( WC_Abstract_Order $obj, int $sign ): array {
		$lines = array();
		$agg   = array();
		$n     = 0;
		$items = array_merge( $obj->get_items( 'line_item' ), $obj->get_items( 'fee' ), $obj->get_items( 'shipping' ) );
		foreach ( $items as $item ) {
			$net = abs( (float) $item->get_total() ) * $sign;
			$tax = abs( (float) $item->get_total_tax() ) * $sign;
			$qty = abs( (float) $item->get_quantity() ) * $sign;
			if ( 0.0 === $qty ) {
				$qty = 1.0 * $sign;
			}
			$percent = 0.0;
			$taxes   = $item->get_taxes();
			if ( ! empty( $taxes['total'] ) ) {
				foreach ( $taxes['total'] as $rate_id => $amt ) {
					if ( '' !== $amt && null !== $amt ) {
						$percent = (float) WC_Tax::get_rate_percent_value( (int) $rate_id );
						break;
					}
				}
			}
			$vat_id = self::vat_id_for_percent( $percent );
			$incl   = round( $net + $tax, 2 );
			$n++;
			$product = $item instanceof WC_Order_Item_Product ? $item->get_product() : null;
			$number  = $product && $product->get_sku() ? $product->get_sku() : ( $product ? (string) $product->get_id() : $item->get_type() );
			$line    = array(
				'business_case'      => array( 'type' => 'Umsatz', 'amounts_per_vat_id' => array( self::vat_entry( $vat_id, $incl, $net, $tax ) ) ),
				'lineitem_export_id' => (string) $n,
				'storno'             => false,
				'text'               => mb_substr( wp_strip_all_tags( $item->get_name() ), 0, 255 ),
				'item'               => array(
					'number'         => mb_substr( $number, 0, 50 ),
					'quantity'       => round( $qty, 3 ),
					'price_per_unit' => round( abs( $qty ) > 0 ? $incl / $qty : $incl, 5 ),
				),
			);
			if ( $product && method_exists( $product, 'get_global_unique_id' ) && $product->get_global_unique_id() ) {
				$line['item']['gtin'] = (string) $product->get_global_unique_id();
			}
			$lines[] = $line;
			self::add_vat( $agg, $vat_id, $incl, $net, $tax );
		}
		return array( $lines, $agg );
	}

	/**
	 * @return array{body:array,totals:array,row_ids:array}
	 */
	public static function build( string $business_date, array $rows, int $export_id ): array {
		$client_id    = (string) HJ_TSE_Settings::get( 'client_id' );
		$transactions = array();
		$bc_agg       = array(); // type => vat_id => sums
		$pay_agg      = array(); // "type|name" => amount
		$cash_amount  = 0.0;
		$full_amount  = 0.0;
		$totals       = array( 'sales' => 0, 'refunds' => 0, 'aborts' => 0, 'transfers' => 0, 'unsigned' => 0, 'revenue' => 0.0, 'cash_in' => 0.0, 'cash_out' => 0.0, 'by_payment' => array(), 'by_vat' => array() );
		$row_ids      = array();
		$numbers      = array();

		foreach ( $rows as $row ) {
			$kind  = (string) $row['kind'];
			$order = $row['order_id'] ? wc_get_order( (int) $row['order_id'] ) : null;
			if ( in_array( $kind, array( 'sale', 'abort' ), true ) && ! $order instanceof WC_Order ) {
				continue;
			}
			$number = (int) $row['tx_number'];
			if ( $number <= 0 ) {
				$number = 900000000 + (int) $row['id']; // unsigned rows get a synthetic unique number
			}
			$numbers[] = $number;
			$row_ids[] = (int) $row['id'];
			$ts_start  = (int) $row['time_start'] ?: strtotime( $row['created_gmt'] . ' UTC' );
			$ts_end    = (int) $row['time_end'] ?: $ts_start;
			$security  = 'FINISHED' === $row['state'] && '' !== $row['tx_id'] ? array( 'tss_tx_id' => $row['tx_id'] ) : array( 'error_message' => mb_substr( (string) $row['error'], 0, 255 ) ?: 'TSE nicht verfuegbar' );
			if ( 'ERROR' === $row['state'] ) {
				$totals['unsigned']++;
			}
			$head = array(
				'tx_id'                 => '' !== $row['tx_id'] ? $row['tx_id'] : wp_generate_uuid4(),
				'transaction_export_id' => (string) $number,
				'closing_client_id'     => $client_id,
				'storno'                => false,
				'number'                => $number,
				'timestamp_start'       => $ts_start,
				'timestamp_end'         => $ts_end,
				'user'                  => self::user_for( $order instanceof WC_Order ? $order : null, $row ),
				'buyer'                 => self::buyer_for( $order instanceof WC_Order ? $order : null ),
			);

			if ( 'sale' === $kind || 'abort' === $kind || 'refund' === $kind ) {
				$obj  = 'refund' === $kind ? wc_get_order( (int) $row['refund_id'] ) : $order;
				$sign = 'refund' === $kind ? -1 : 1;
				if ( ! $obj instanceof WC_Abstract_Order ) {
					continue;
				}
				list( $lines, $agg ) = self::lines_for( $obj, $sign );
				$full = 0.0;
				foreach ( $agg as $v ) {
					$full += $v[0];
				}
				$full = round( $full, 2 );
				list( $ptype, $pname ) = self::payment_type_for( (string) $order->get_payment_method() );
				$head['type'] = 'abort' === $kind ? 'AVBelegabbruch' : 'Beleg';
				$transactions[] = array(
					'head'     => $head,
					'data'     => array(
						'full_amount_incl_vat' => $full,
						'payment_types'        => array( array( 'type' => $ptype, 'name' => $pname, 'currency_code' => 'EUR', 'amount' => $full ) ),
						'amounts_per_vat_id'   => self::vat_list( $agg ),
						'lines'                => $lines,
					),
					'security' => $security,
				);
				if ( 'abort' === $kind ) {
					$totals['aborts']++;
					continue;
				}
				$totals[ 'refund' === $kind ? 'refunds' : 'sales' ]++;
				$totals['revenue'] += $full;
				$full_amount       += $full;
				foreach ( $agg as $vid => $v ) {
					self::add_vat( $bc_agg['Umsatz'], (int) $vid, $v[0], $v[1], $v[2] );
					$totals['by_vat'][ $vid ] = round( ( $totals['by_vat'][ $vid ] ?? 0 ) + $v[0], 2 );
				}
				$pk = $ptype . '|' . $pname;
				$pay_agg[ $pk ] = ( $pay_agg[ $pk ] ?? 0 ) + $full;
				$totals['by_payment'][ $pname ] = round( ( $totals['by_payment'][ $pname ] ?? 0 ) + $full, 2 );
				if ( 'Bar' === $ptype ) {
					$cash_amount += $full;
				}
				continue;
			}

			if ( 'pay_in' === $kind || 'pay_out' === $kind ) {
				$note   = json_decode( (string) $row['note'], true ) ?: array();
				$reason = (string) ( $note['reason'] ?? ( 'pay_in' === $kind ? 'Privateinlage' : 'Privatentnahme' ) );
				$amount = round( (float) $row['total'], 2 );
				$head['type'] = 'AVTransfer';
				$transactions[] = array(
					'head'     => $head,
					'data'     => array(
						'full_amount_incl_vat' => $amount,
						'payment_types'        => array( array( 'type' => 'Bar', 'name' => 'Bar', 'currency_code' => 'EUR', 'amount' => $amount ) ),
						'amounts_per_vat_id'   => array( self::vat_entry( 5, $amount, $amount, 0.0 ) ),
						'lines'                => array(
							array(
								'business_case'      => array( 'type' => $reason, 'amounts_per_vat_id' => array( self::vat_entry( 5, $amount, $amount, 0.0 ) ) ),
								'lineitem_export_id' => '1',
								'storno'             => false,
								'text'               => mb_substr( (string) ( $note['text'] ?? $reason ), 0, 255 ),
							),
						),
					),
					'security' => $security,
				);
				$totals['transfers']++;
				if ( $amount > 0 ) {
					$totals['cash_in'] += $amount;
				} else {
					$totals['cash_out'] += abs( $amount );
				}
				self::add_vat( $bc_agg[ $reason ], 5, $amount, $amount, 0.0 );
				$pay_agg['Bar|Bar'] = ( $pay_agg['Bar|Bar'] ?? 0 ) + $amount;
				$cash_amount       += $amount;
				$full_amount       += $amount;
			}
		}

		$business_cases = array();
		foreach ( $bc_agg as $type => $agg ) {
			$business_cases[] = array( 'type' => $type, 'amounts_per_vat_id' => self::vat_list( $agg ) );
		}
		$payment_types = array();
		foreach ( $pay_agg as $k => $amt ) {
			list( $t, $n ) = explode( '|', $k, 2 );
			$payment_types[] = array( 'type' => $t, 'name' => $n, 'currency_code' => 'EUR', 'amount' => round( $amt, 2 ) );
		}
		sort( $numbers );
		$body = array(
			'client_id'                    => $client_id,
			'cash_point_closing_export_id' => $export_id,
			'head'                         => array(
				'business_date'               => $business_date,
				'first_transaction_export_id' => (string) ( $numbers[0] ?? '0' ),
				'last_transaction_export_id'  => (string) ( $numbers ? $numbers[ count( $numbers ) - 1 ] : '0' ),
				'export_creation_date'        => time(),
			),
			'cash_statement'               => array(
				'business_cases' => $business_cases,
				'payment'        => array(
					'full_amount'              => round( $full_amount, 2 ),
					'cash_amount'              => round( $cash_amount, 2 ),
					'cash_amounts_by_currency' => array( array( 'currency_code' => 'EUR', 'amount' => round( $cash_amount, 2 ) ) ),
					'payment_types'            => $payment_types,
				),
			),
			'transactions'                 => $transactions,
			'metadata'                     => array( 'site' => home_url(), 'plugin' => HJ_TSE_VERSION ),
		);
		$totals['revenue']  = round( $totals['revenue'], 2 );
		$totals['cash_in']  = round( $totals['cash_in'], 2 );
		$totals['cash_out'] = round( $totals['cash_out'], 2 );
		$totals['cash_net'] = round( $cash_amount, 2 );
		$totals['tx_count'] = count( $transactions );
		return array( 'body' => $body, 'totals' => $totals, 'row_ids' => $row_ids );
	}

	/* ---------- export ---------- */

	public static function trigger_export( string $start_date, string $end_date ): array {
		$tz   = wp_timezone();
		$body = array(
			'client_id' => (string) HJ_TSE_Settings::get( 'client_id' ),
			'format'    => 'zip',
		);
		if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $start_date ) && preg_match( '/^\d{4}-\d{2}-\d{2}$/', $end_date ) ) {
			$body['business_date_start'] = $start_date;
			$body['business_date_end']   = $end_date;
		} else {
			$body['start_date'] = 0;
			$body['end_date']   = time() + 60;
		}
		$id  = wp_generate_uuid4();
		$res = ( new HJ_TSE_Dsfinvk() )->trigger_export( $id, $body );
		update_option( 'hj_tse_last_dsfinvk_export', array( 'id' => $id, 'time' => time(), 'body' => $body ), false );
		return array( 'export_id' => $id, 'state' => (string) ( $res['state'] ?? '' ) );
	}

	public static function export_status( string $export_id, bool $download ): array {
		$client = new HJ_TSE_Dsfinvk();
		$res    = $client->get_export( $export_id );
		$out    = array(
			'state'               => (string) ( $res['state'] ?? '' ),
			'error'               => $res['error'] ?? null,
			'cash_point_closings' => $res['cash_point_closings'] ?? array(),
			'time_expiration'     => $res['time_expiration'] ?? null,
		);
		if ( $download && 'COMPLETED' === $out['state'] ) {
			$zip = $client->download_export( $export_id );
			$dir = wp_upload_dir();
			$sub = trailingslashit( $dir['basedir'] ) . 'hj-tse-exports';
			wp_mkdir_p( $sub );
			if ( ! file_exists( $sub . '/.htaccess' ) ) {
				file_put_contents( $sub . '/.htaccess', "Deny from all\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions
			}
			$file = $sub . '/dsfinvk-' . gmdate( 'Ymd-His' ) . '-' . substr( $export_id, 0, 8 ) . '.zip';
			file_put_contents( $file, $zip ); // phpcs:ignore WordPress.WP.AlternativeFunctions
			$out['file']  = $file;
			$out['bytes'] = strlen( $zip );
		}
		return $out;
	}
}
