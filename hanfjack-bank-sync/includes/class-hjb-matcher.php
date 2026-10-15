<?php
/**
 * Pure matching logic: bank credit -> open Vorkasse order. No WordPress dependencies (unit-testable).
 */
class HJB_Matcher {

	const MATCH  = 'matched';
	const REVIEW = 'review';
	const NONE   = 'unmatched';

	/**
	 * Normalise an Enable Banking transaction to the fields the matcher needs.
	 */
	public static function normalize( array $tx ): array {
		$amount = (float) ( $tx['transaction_amount']['amount'] ?? 0 );
		$ind    = (string) ( $tx['credit_debit_indicator'] ?? ( $amount < 0 ? 'DBIT' : 'CRDT' ) );
		$rem    = $tx['remittance_information'] ?? array();
		$rem    = is_array( $rem ) ? implode( ' ', array_map( 'strval', $rem ) ) : (string) $rem;
		foreach ( array( 'reference_number', 'note' ) as $k ) {
			if ( ! empty( $tx[ $k ] ) && is_string( $tx[ $k ] ) && false === strpos( $rem, $tx[ $k ] ) ) {
				$rem .= ' ' . $tx[ $k ];
			}
		}
		$debtor = (string) ( $tx['debtor']['name'] ?? '' );
		$date   = (string) ( $tx['booking_date'] ?? $tx['value_date'] ?? $tx['transaction_date'] ?? '' );
		$basis  = (string) ( $tx['entry_reference'] ?? $tx['transaction_id'] ?? '' );
		if ( '' === $basis ) {
			$basis = $date . '|' . number_format( abs( $amount ), 2, '.', '' ) . '|' . $debtor . '|' . $rem;
		}
		return array(
			'key'       => sha1( $basis ),
			'amount'    => round( abs( $amount ), 2 ),
			'currency'  => (string) ( $tx['transaction_amount']['currency'] ?? 'EUR' ),
			'credit'    => 'CRDT' === $ind,
			'booked'    => in_array( (string) ( $tx['status'] ?? 'BOOK' ), array( 'BOOK', '' ), true ),
			'remittance' => trim( preg_replace( '/\s+/', ' ', $rem ) ),
			'debtor'    => trim( $debtor ),
			'date'      => $date,
			'reference' => (string) ( $tx['entry_reference'] ?? $tx['transaction_id'] ?? '' ),
		);
	}

	public static function cents( float $v ): int {
		return (int) round( $v * 100 );
	}

	/** Lower-case, umlauts folded, only letters/digits/spaces. */
	public static function fold( string $s ): string {
		$s = mb_strtolower( $s, 'UTF-8' );
		$s = strtr( $s, array( 'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss', 'é' => 'e', 'è' => 'e', 'á' => 'a', 'à' => 'a' ) );
		return trim( preg_replace( '/[^a-z0-9 ]+/', ' ', $s ) );
	}

	/** True when the payer name plausibly belongs to the order's billing name. */
	public static function name_matches( string $debtor, string $billing_first, string $billing_last, string $company = '' ): bool {
		$d = ' ' . self::fold( $debtor ) . ' ';
		if ( '  ' === $d ) {
			return false;
		}
		$last = self::fold( $billing_last );
		if ( '' !== $last ) {
			foreach ( explode( ' ', $last ) as $part ) {
				if ( strlen( $part ) >= 3 && false !== strpos( $d, ' ' . $part . ' ' ) ) {
					return true;
				}
			}
		}
		$co = self::fold( $company );
		if ( strlen( $co ) >= 4 && false !== strpos( $d, $co ) ) {
			return true;
		}
		return false;
	}

	/** True when $number appears in the remittance as a standalone token. */
	public static function number_in_text( string $number, string $text ): bool {
		$number = trim( $number );
		if ( '' === $number ) {
			return false;
		}
		return (bool) preg_match( '/(?<![A-Za-z0-9])' . preg_quote( $number, '/' ) . '(?![A-Za-z0-9])/i', $text );
	}

	/**
	 * @param array $tx     Normalised transaction.
	 * @param array $orders Open orders: [id, number, total, first, last, company].
	 * @param array $opt    auto_by_number, auto_by_name (bool).
	 * @return array{status:string, order_id:int, reason:string, suggestions:int[]}
	 */
	public static function match( array $tx, array $orders, array $opt = array() ): array {
		$opt = array_merge( array( 'auto_by_number' => true, 'auto_by_name' => true ), $opt );
		if ( ! $tx['credit'] || 'EUR' !== strtoupper( $tx['currency'] ) || $tx['amount'] <= 0 ) {
			return array( 'status' => 'skip', 'order_id' => 0, 'reason' => 'keine Gutschrift in EUR', 'suggestions' => array() );
		}
		$amount   = self::cents( $tx['amount'] );
		$by_num   = array();
		$by_amt   = array();
		foreach ( $orders as $o ) {
			$total = self::cents( (float) $o['total'] );
			if ( self::number_in_text( (string) $o['number'], $tx['remittance'] ) || ( (string) $o['id'] !== (string) $o['number'] && self::number_in_text( (string) $o['id'], $tx['remittance'] ) ) ) {
				$by_num[] = $o;
			}
			if ( $total === $amount ) {
				$by_amt[] = $o;
			}
		}

		// 1) Order number in remittance.
		if ( $by_num ) {
			$exact = array_values( array_filter( $by_num, static fn( $o ) => self::cents( (float) $o['total'] ) === $amount ) );
			if ( 1 === count( $exact ) ) {
				$o = $exact[0];
				return $opt['auto_by_number']
					? array( 'status' => self::MATCH, 'order_id' => (int) $o['id'], 'reason' => 'Bestellnummer und Betrag stimmen', 'suggestions' => array() )
					: array( 'status' => self::REVIEW, 'order_id' => 0, 'reason' => 'Bestellnummer und Betrag stimmen (manuelle Freigabe eingestellt)', 'suggestions' => array( (int) $o['id'] ) );
			}
			if ( 1 === count( $by_num ) ) {
				$o    = $by_num[0];
				$diff = ( $amount - self::cents( (float) $o['total'] ) ) / 100;
				return array( 'status' => self::REVIEW, 'order_id' => 0, 'reason' => sprintf( 'Bestellnummer gefunden, Betrag weicht um %s € ab (%s)', number_format( $diff, 2, ',', '.' ), $diff > 0 ? 'Überzahlung' : 'Teilzahlung' ), 'suggestions' => array( (int) $o['id'] ) );
			}
			return array( 'status' => self::REVIEW, 'order_id' => 0, 'reason' => 'mehrere Bestellnummern im Verwendungszweck', 'suggestions' => array_map( static fn( $o ) => (int) $o['id'], array_slice( $by_num, 0, 5 ) ) );
		}

		// 2) No number: amount + payer name.
		if ( $by_amt ) {
			$named = array_values( array_filter( $by_amt, static fn( $o ) => self::name_matches( $tx['debtor'], (string) $o['first'], (string) $o['last'], (string) ( $o['company'] ?? '' ) ) ) );
			if ( 1 === count( $named ) && $opt['auto_by_name'] ) {
				return array( 'status' => self::MATCH, 'order_id' => (int) $named[0]['id'], 'reason' => 'Betrag und Name stimmen (ohne Bestellnummer)', 'suggestions' => array() );
			}
			if ( 1 === count( $named ) ) {
				return array( 'status' => self::REVIEW, 'order_id' => 0, 'reason' => 'Betrag und Name stimmen, Bestellnummer fehlt', 'suggestions' => array( (int) $named[0]['id'] ) );
			}
			$cands = $named ?: $by_amt;
			return array( 'status' => self::REVIEW, 'order_id' => 0, 'reason' => count( $cands ) > 1 ? 'mehrere offene Bestellungen mit gleichem Betrag' : 'Betrag passt, Name weicht ab', 'suggestions' => array_map( static fn( $o ) => (int) $o['id'], array_slice( $cands, 0, 5 ) ) );
		}

		return array( 'status' => self::NONE, 'order_id' => 0, 'reason' => 'keine passende offene Bestellung', 'suggestions' => array() );
	}
}
