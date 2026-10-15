<?php
defined( 'ABSPATH' ) || exit;

/**
 * Connection flow, scheduled sync and applying matches to WooCommerce orders.
 */
class HJB_Sync {

	public static function init(): void {
		add_action( 'hjb_sync', array( __CLASS__, 'cron_sync' ) );
		add_action( 'hjb_consent_check', array( __CLASS__, 'consent_check' ) );
		add_action( 'init', array( __CLASS__, 'schedule' ) );
	}

	/* ---------- scheduling ---------- */

	/**
	 * PSD2 allows only ~4 unattended account reads per day, so we run at fixed local times.
	 */
	public static function schedule(): void {
		if ( ! wp_next_scheduled( 'hjb_consent_check' ) ) {
			wp_schedule_event( time() + 3600, 'daily', 'hjb_consent_check' );
		}
		$next = self::next_run();
		$have = wp_next_scheduled( 'hjb_sync' );
		if ( $next && ( ! $have || abs( $have - $next ) > 60 ) ) {
			wp_clear_scheduled_hook( 'hjb_sync' );
			wp_schedule_single_event( $next, 'hjb_sync' );
		}
	}

	public static function next_run(): int {
		$tz    = wp_timezone();
		$now   = new DateTimeImmutable( 'now', $tz );
		$times = array();
		foreach ( HJB_Settings::list( 'sync_times' ) as $t ) {
			if ( preg_match( '/^(\d{1,2}):(\d{2})$/', $t, $m ) ) {
				$times[] = sprintf( '%02d:%02d', min( 23, (int) $m[1] ), min( 59, (int) $m[2] ) );
			}
		}
		$times = array_slice( array_unique( $times ), 0, 4 );
		sort( $times );
		if ( ! $times ) {
			return 0;
		}
		foreach ( array( 0, 1 ) as $d ) {
			foreach ( $times as $t ) {
				$c = new DateTimeImmutable( $now->format( 'Y-m-d' ) . ' ' . $t, $tz );
				$c = $c->modify( '+' . $d . ' day' );
				if ( $c > $now ) {
					return $c->getTimestamp();
				}
			}
		}
		return 0;
	}

	public static function cron_sync(): void {
		try {
			self::run( array() );
		} catch ( HJB_Exception $e ) {
			self::log( 'Abruf fehlgeschlagen: ' . $e->getMessage() );
		}
		wp_clear_scheduled_hook( 'hjb_sync' );
		self::schedule();
	}

	/* ---------- connect ---------- */

	public static function callback_url(): string {
		return rest_url( 'hj-bank/v1/callback' );
	}

	/** Returns the bank's consent URL to redirect the account owner to. */
	public static function start_connect(): string {
		if ( ! HJB_Settings::is_configured() ) {
			throw new HJB_Exception( 'Bitte zuerst Application-ID und Private Key speichern.', 'E_CONFIG' );
		}
		$api     = new HJB_EnableBanking();
		$name    = (string) HJB_Settings::get( 'aspsp_name' );
		$country = (string) HJB_Settings::get( 'aspsp_country' );
		$aspsp   = $api->find_aspsp( $name, $country );
		if ( ! $aspsp ) {
			throw new HJB_Exception( sprintf( 'Bank „%s“ (%s) ist bei Enable Banking nicht verfügbar.', $name, $country ), 'E_ASPSP' );
		}
		$max   = (int) ( $aspsp['maximum_consent_validity'] ?? 0 );
		$secs  = $max > 0 ? min( $max, 180 * DAY_IN_SECONDS ) : 90 * DAY_IN_SECONDS;
		$until = gmdate( 'Y-m-d\TH:i:s\Z', time() + $secs - 300 );
		$psu   = (string) HJB_Settings::get( 'psu_type' );
		$types = (array) ( $aspsp['psu_types'] ?? array() );
		if ( $types && ! in_array( $psu, $types, true ) ) {
			$psu = (string) $types[0];
		}
		$state = wp_generate_password( 32, false, false );
		HJB_Settings::update( array( 'pending_state' => $state ) );
		set_transient( 'hjb_state_' . $state, get_current_user_id() ?: 1, HOUR_IN_SECONDS );
		$res = $api->start_auth( (string) $aspsp['name'], $country, $psu, self::callback_url(), $state, $until );
		if ( empty( $res['url'] ) ) {
			throw new HJB_Exception( 'Keine Weiterleitungsadresse von Enable Banking erhalten.', 'E_AUTH' );
		}
		return (string) $res['url'];
	}

	public static function finish_connect( string $code, string $state ): array {
		$expected = (string) HJB_Settings::get( 'pending_state' );
		if ( '' === $state || ! hash_equals( $expected, $state ) || ! get_transient( 'hjb_state_' . $state ) ) {
			throw new HJB_Exception( 'Ungültiger oder abgelaufener Rückruf (state).', 'E_STATE' );
		}
		delete_transient( 'hjb_state_' . $state );
		$api     = new HJB_EnableBanking();
		$session = $api->create_session( $code );
		$accts   = array();
		$raw     = (array) ( $session['accounts'] ?? array() );
		foreach ( $raw as $a ) {
			$uid  = is_array( $a ) ? (string) ( $a['uid'] ?? '' ) : (string) $a;
			$iban = is_array( $a ) ? (string) ( $a['account_id']['iban'] ?? '' ) : '';
			$name = is_array( $a ) ? (string) ( $a['name'] ?? $a['product'] ?? '' ) : '';
			if ( '' === $uid ) {
				continue;
			}
			if ( '' === $iban ) {
				try {
					$d    = $api->account_details( $uid );
					$iban = (string) ( $d['account_id']['iban'] ?? '' );
					$name = $name ?: (string) ( $d['name'] ?? $d['product'] ?? '' );
				} catch ( HJB_Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement
				}
			}
			$accts[] = array( 'uid' => $uid, 'iban' => $iban, 'name' => $name );
		}
		$filter = preg_replace( '/\s+/', '', strtoupper( (string) HJB_Settings::get( 'iban_filter' ) ) );
		if ( '' !== $filter ) {
			$accts = array_values( array_filter( $accts, static fn( $a ) => preg_replace( '/\s+/', '', strtoupper( $a['iban'] ) ) === $filter ) );
		}
		if ( ! $accts ) {
			throw new HJB_Exception( 'Die Bank hat kein (passendes) Konto freigegeben.', 'E_NO_ACCOUNT' );
		}
		HJB_Settings::update(
			array(
				'session_id'    => (string) ( $session['session_id'] ?? '' ),
				'accounts'      => $accts,
				'valid_until'   => (string) ( $session['access']['valid_until'] ?? '' ),
				'pending_state' => '',
			)
		);
		delete_option( 'hjb_consent_warned' );
		self::log( sprintf( 'Bankverbindung hergestellt: %d Konto/Konten, gültig bis %s.', count( $accts ), (string) ( $session['access']['valid_until'] ?? '?' ) ) );
		return $accts;
	}

	public static function disconnect(): void {
		$sid = (string) HJB_Settings::get( 'session_id' );
		if ( '' !== $sid ) {
			try {
				( new HJB_EnableBanking() )->delete_session( $sid );
			} catch ( HJB_Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement
			}
		}
		HJB_Settings::update( array( 'session_id' => '', 'accounts' => array(), 'valid_until' => '' ) );
	}

	/* ---------- sync ---------- */

	/**
	 * @param array $psu PSU headers when triggered interactively (lifts the 4/day background limit).
	 * @return array Summary counts.
	 */
	public static function run( array $psu = array(), int $days_back = 0 ): array {
		if ( ! HJB_Settings::is_connected() ) {
			throw new HJB_Exception( 'Keine aktive Bankverbindung. Bitte unter WooCommerce → Zahlungsabgleich verbinden.', 'E_NOT_CONNECTED' );
		}
		if ( 'yes' !== HJB_Settings::get( 'enabled' ) ) {
			return array( 'skipped' => 'deaktiviert' );
		}
		$last = (int) HJB_Settings::get( 'last_sync' );
		if ( $days_back <= 0 ) {
			$days_back = $last ? max( 3, (int) ceil( ( time() - $last ) / DAY_IN_SECONDS ) + 2 ) : 14;
		}
		$from    = gmdate( 'Y-m-d', time() - $days_back * DAY_IN_SECONDS );
		$api     = new HJB_EnableBanking();
		$summary = array( 'fetched' => 0, 'new_credits' => 0, 'matched' => 0, 'review' => 0, 'unmatched' => 0, 'errors' => array() );
		$orders  = self::open_orders();
		$review_ids = array();
		foreach ( (array) HJB_Settings::get( 'accounts' ) as $acct ) {
			try {
				$txs = $api->transactions( (string) $acct['uid'], $from, $psu );
			} catch ( HJB_Exception $e ) {
				$summary['errors'][] = $e->getMessage();
				if ( in_array( $e->api_code, array( 'EXPIRED_SESSION', 'REVOKED_SESSION', 'CLOSED_SESSION' ), true ) ) {
					HJB_Settings::update( array( 'session_id' => '', 'accounts' => array() ) );
					self::notify( 'Bankverbindung abgelaufen', "Die Freigabe für das Geschäftskonto ist abgelaufen oder wurde widerrufen.\nBitte unter WooCommerce → Zahlungsabgleich neu verbinden.\n\n" . admin_url( 'admin.php?page=hjb' ) );
				}
				continue;
			}
			$summary['fetched'] += count( $txs );
			foreach ( $txs as $raw ) {
				$tx = HJB_Matcher::normalize( (array) $raw );
				if ( ! $tx['credit'] || ! $tx['booked'] || HJB_Store::exists( $tx['key'] ) ) {
					continue;
				}
				$summary['new_credits']++;
				$res = HJB_Matcher::match( $tx, $orders, array( 'auto_by_number' => 'yes' === HJB_Settings::get( 'auto_by_number' ), 'auto_by_name' => 'yes' === HJB_Settings::get( 'auto_by_name' ) ) );
				$id  = HJB_Store::insert(
					array(
						'tx_key'       => $tx['key'],
						'account_iban' => HJB_Settings::mask_iban( (string) $acct['iban'] ),
						'booking_date' => $tx['date'] ?: null,
						'amount'       => $tx['amount'],
						'currency'     => $tx['currency'],
						'debtor_name'  => mb_substr( $tx['debtor'], 0, 190 ),
						'remittance'   => mb_substr( $tx['remittance'], 0, 1000 ),
						'status'       => $res['status'],
						'reason'       => mb_substr( $res['reason'], 0, 255 ),
						'suggestions'  => implode( ',', $res['suggestions'] ),
					)
				);
				if ( HJB_Matcher::MATCH === $res['status'] ) {
					if ( self::apply( $id, (int) $res['order_id'], $tx, $res['reason'] ) ) {
						$summary['matched']++;
						$orders = array_values( array_filter( $orders, static fn( $o ) => (int) $o['id'] !== (int) $res['order_id'] ) );
					}
				} elseif ( HJB_Matcher::REVIEW === $res['status'] ) {
					$summary['review']++;
					$review_ids[] = $id;
				} else {
					$summary['unmatched']++;
				}
			}
		}
		HJB_Settings::update( array( 'last_sync' => time(), 'last_sync_result' => wp_json_encode( $summary, JSON_UNESCAPED_UNICODE ) ) );
		if ( $review_ids ) {
			$lines = array();
			foreach ( $review_ids as $rid ) {
				$r = HJB_Store::get( $rid );
				if ( $r ) {
					$lines[] = sprintf( '- %s € von %s (%s): %s', number_format( (float) $r['amount'], 2, ',', '.' ), $r['debtor_name'] ?: 'unbekannt', $r['remittance'], $r['reason'] );
				}
			}
			self::notify( count( $review_ids ) . ' Zahlungseingang/-eingänge zur Prüfung', "Diese Zahlungen konnten nicht eindeutig zugeordnet werden:\n\n" . implode( "\n", $lines ) . "\n\nZuordnen: " . admin_url( 'admin.php?page=hjb' ) );
		}
		return $summary;
	}

	/**
	 * Open orders eligible for matching.
	 */
	public static function open_orders(): array {
		$statuses = HJB_Settings::list( 'statuses' );
		$gateways = HJB_Settings::list( 'gateways' );
		$days     = (int) HJB_Settings::get( 'order_days', 60 );
		$orders   = wc_get_orders(
			array(
				'status'         => $statuses ?: array( 'on-hold' ),
				'payment_method' => $gateways ?: array( 'bacs' ),
				'date_created'   => '>' . ( time() - $days * DAY_IN_SECONDS ),
				'limit'          => 500,
				'type'           => 'shop_order',
			)
		);
		$out = array();
		foreach ( $orders as $o ) {
			$out[] = array(
				'id'      => $o->get_id(),
				'number'  => (string) $o->get_order_number(),
				'total'   => (float) $o->get_total(),
				'first'   => (string) $o->get_billing_first_name(),
				'last'    => (string) $o->get_billing_last_name(),
				'company' => (string) $o->get_billing_company(),
			);
		}
		return $out;
	}

	/**
	 * Mark the order as paid and link it to the bank transaction.
	 */
	public static function apply( int $row_id, int $order_id, array $tx, string $reason, bool $manual = false ): bool {
		$order = wc_get_order( $order_id );
		if ( ! $order instanceof WC_Order ) {
			return false;
		}
		if ( $order->is_paid() ) {
			HJB_Store::update( $row_id, array( 'status' => 'review', 'reason' => 'Bestellung war bereits bezahlt' ) );
			return false;
		}
		$order->update_meta_data( '_hjb_bank_tx', $tx['key'] );
		$order->add_order_note(
			sprintf(
				'Zahlungseingang erkannt%s: %s € am %s von %s. Verwendungszweck: „%s“. Zuordnung: %s.',
				$manual ? ' (manuell zugeordnet)' : '',
				number_format( (float) $tx['amount'], 2, ',', '.' ),
				$tx['date'] ? wp_date( 'd.m.Y', strtotime( $tx['date'] ) ) : '?',
				$tx['debtor'] ?: 'unbekannt',
				$tx['remittance'],
				$reason
			)
		);
		$order->save();
		$order->payment_complete( $tx['reference'] ?: $tx['key'] );
		HJB_Store::update( $row_id, array( 'status' => 'matched', 'order_id' => $order_id, 'reason' => mb_substr( ( $manual ? 'manuell: ' : '' ) . $reason, 0, 255 ) ) );
		return true;
	}

	/** Manual assignment from the admin review list. */
	public static function assign( int $row_id, int $order_id ): void {
		$row = HJB_Store::get( $row_id );
		if ( ! $row || 'matched' === $row['status'] ) {
			throw new HJB_Exception( 'Buchung nicht gefunden oder bereits zugeordnet.', 'E_ROW' );
		}
		$tx = array(
			'key'        => $row['tx_key'],
			'amount'     => (float) $row['amount'],
			'date'       => (string) $row['booking_date'],
			'debtor'     => (string) $row['debtor_name'],
			'remittance' => (string) $row['remittance'],
			'reference'  => '',
		);
		$order = wc_get_order( $order_id );
		if ( ! $order instanceof WC_Order ) {
			throw new HJB_Exception( 'Bestellung #' . $order_id . ' nicht gefunden.', 'E_ORDER' );
		}
		$diff = HJB_Matcher::cents( (float) $row['amount'] ) - HJB_Matcher::cents( (float) $order->get_total() );
		$note = 0 === $diff ? 'Betrag stimmt' : sprintf( 'Betrag weicht um %s € ab', number_format( $diff / 100, 2, ',', '.' ) );
		if ( ! self::apply( $row_id, $order_id, $tx, $note, true ) ) {
			throw new HJB_Exception( 'Bestellung konnte nicht als bezahlt markiert werden (bereits bezahlt?).', 'E_APPLY' );
		}
	}

	/* ---------- consent expiry ---------- */

	public static function consent_check(): void {
		$until = strtotime( (string) HJB_Settings::get( 'valid_until' ) );
		if ( ! $until || ! HJB_Settings::get( 'session_id' ) ) {
			return;
		}
		$days   = (int) floor( ( $until - time() ) / DAY_IN_SECONDS );
		$warned = (array) get_option( 'hjb_consent_warned', array() );
		foreach ( array( 14, 3 ) as $th ) {
			if ( $days <= $th && ! in_array( $th, $warned, true ) ) {
				self::notify( 'Bankfreigabe läuft in ' . max( 0, $days ) . ' Tagen ab', "Die PSD2-Freigabe für den automatischen Zahlungsabgleich endet am " . wp_date( 'd.m.Y', $until ) . ".\nBitte rechtzeitig unter WooCommerce → Zahlungsabgleich auf „Neu verbinden“ klicken und in der N26-App bestätigen.\n\n" . admin_url( 'admin.php?page=hjb' ) );
				$warned[] = $th;
			}
		}
		update_option( 'hjb_consent_warned', $warned, false );
	}

	/* ---------- helpers ---------- */

	public static function notify( string $subject, string $body ): void {
		$to = (string) HJB_Settings::get( 'notify_email' ) ?: (string) get_option( 'admin_email' );
		wp_mail( $to, '[Hanfjack Zahlungsabgleich] ' . $subject, $body );
	}

	public static function log( string $msg ): void {
		if ( function_exists( 'wc_get_logger' ) ) {
			wc_get_logger()->info( $msg, array( 'source' => 'hanfjack-bank-sync' ) );
		}
	}

	public static function psu_from_request(): array {
		$ip = isset( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) : ( isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '' );
		$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
		return array( 'ip' => $ip, 'ua' => $ua );
	}
}
