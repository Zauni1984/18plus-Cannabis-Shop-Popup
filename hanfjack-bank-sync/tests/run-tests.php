<?php
/**
 * Standalone tests: php tests/run-tests.php
 */
define( 'ABSPATH', __DIR__ );
function wp_json_encode( $d, $f = 0 ) { return json_encode( $d, $f ); }
require __DIR__ . '/../includes/class-hjb-exception.php';
require __DIR__ . '/../includes/class-hjb-matcher.php';
require __DIR__ . '/../includes/class-hjb-enablebanking.php';

$fail = 0; $n = 0;
function check( $label, $cond ) { global $fail, $n; $n++; if ( ! $cond ) { $fail++; echo "FAIL: $label\n"; } else { echo "ok   $label\n"; } }
function tx( $amount, $rem, $debtor = '', $ind = 'CRDT', $status = 'BOOK', $ref = null ) {
	$t = array( 'transaction_amount' => array( 'amount' => (string) $amount, 'currency' => 'EUR' ), 'credit_debit_indicator' => $ind, 'status' => $status, 'remittance_information' => (array) $rem, 'debtor' => array( 'name' => $debtor ), 'booking_date' => '2026-10-15' );
	if ( $ref ) { $t['entry_reference'] = $ref; }
	return HJB_Matcher::normalize( $t );
}
$orders = array(
	array( 'id' => 49157, 'number' => '49157', 'total' => 36.89, 'first' => 'Max', 'last' => 'Rix', 'company' => '' ),
	array( 'id' => 49052, 'number' => '49052', 'total' => 23.85, 'first' => 'Anna', 'last' => 'Müller', 'company' => '' ),
	array( 'id' => 48985, 'number' => '48985', 'total' => 22.68, 'first' => 'Jörg', 'last' => 'Weiß', 'company' => 'Grow GmbH' ),
	array( 'id' => 48990, 'number' => '48990', 'total' => 22.68, 'first' => 'Tom', 'last' => 'Bauer', 'company' => '' ),
	array( 'id' => 49095, 'number' => 'TT576966996315970414', 'total' => 29.90, 'first' => 'S', 'last' => 'Löwel', 'company' => '' ),
);
$r = HJB_Matcher::match( tx( '36.89', 'Bestellung 49157' , 'MAX RIX' ), $orders );
check( 'number + amount => matched 49157', 'matched' === $r['status'] && 49157 === $r['order_id'] );
$r = HJB_Matcher::match( tx( '36.89', 'Hanfjack Best.-Nr.:49157 danke', 'Someone Else' ), $orders );
check( 'number with punctuation, other payer => matched', 'matched' === $r['status'] && 49157 === $r['order_id'] );
$r = HJB_Matcher::match( tx( '30.00', '49157', 'Max Rix' ), $orders );
check( 'number found, amount differs => review with suggestion', 'review' === $r['status'] && array( 49157 ) === $r['suggestions'] && false !== strpos( $r['reason'], 'Teilzahlung' ) );
$r = HJB_Matcher::match( tx( '23.85', 'Online Einkauf', 'Anna Mueller' ), $orders );
check( 'no number, amount + folded umlaut name => matched 49052', 'matched' === $r['status'] && 49052 === $r['order_id'] );
$r = HJB_Matcher::match( tx( '23.85', 'Online Einkauf', 'Peter Schmidt' ), $orders );
check( 'no number, amount ok, name differs => review', 'review' === $r['status'] && array( 49052 ) === $r['suggestions'] );
$r = HJB_Matcher::match( tx( '22.68', 'Rechnung', 'Unbekannt' ), $orders );
check( 'two orders same amount, no name => review with 2 suggestions', 'review' === $r['status'] && 2 === count( $r['suggestions'] ) );
$r = HJB_Matcher::match( tx( '22.68', 'Rechnung', 'GROW GMBH' ), $orders );
check( 'two orders same amount, company name => matched 48985', 'matched' === $r['status'] && 48985 === $r['order_id'] );
$r = HJB_Matcher::match( tx( '29.90', 'TT576966996315970414', 'X' ), $orders );
check( 'non-numeric order number => matched', 'matched' === $r['status'] && 49095 === $r['order_id'] );
$r = HJB_Matcher::match( tx( '36.89', '149157 Bestellung', 'X' ), $orders );
check( 'number embedded in longer number is not a hit (amount-only, name differs) => review', 'review' === $r['status'] );
$r = HJB_Matcher::match( tx( '-36.89', '49157', 'Max Rix', 'DBIT' ), $orders );
check( 'debit is skipped', 'skip' === $r['status'] );
$r = HJB_Matcher::match( tx( '99.99', 'Gehalt', 'Firma' ), $orders );
check( 'unrelated credit => unmatched', 'unmatched' === $r['status'] );
$r = HJB_Matcher::match( tx( '36.89', 'Bestellung 49157', 'Max Rix' ), $orders, array( 'auto_by_number' => false ) );
check( 'auto_by_number off => review with suggestion', 'review' === $r['status'] && array( 49157 ) === $r['suggestions'] );
$a = tx( '36.89', 'x', 'y', 'CRDT', 'BOOK', 'REF-1' ); $b = tx( '36.89', 'x', 'y', 'CRDT', 'BOOK', 'REF-1' );
check( 'stable key from entry_reference', $a['key'] === $b['key'] );
$p = tx( '36.89', 'x', 'y', 'CRDT', 'PDNG' );
check( 'pending transaction flagged not booked', false === $p['booked'] );

// JWT RS256 roundtrip.
$res = openssl_pkey_new( array( 'private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA ) );
openssl_pkey_export( $res, $pem ); $pub = openssl_pkey_get_details( $res )['key'];
$api = new HJB_EnableBanking( 'app-123', $pem );
$jwt = $api->jwt();
list( $h, $p2, $s ) = explode( '.', $jwt );
$dec = static fn( $x ) => base64_decode( strtr( $x, '-_', '+/' ) . str_repeat( '=', ( 4 - strlen( $x ) % 4 ) % 4 ) );
$hdr = json_decode( $dec( $h ), true ); $pl = json_decode( $dec( $p2 ), true );
check( 'jwt header kid/alg', 'app-123' === $hdr['kid'] && 'RS256' === $hdr['alg'] );
check( 'jwt payload iss/aud', 'enablebanking.com' === $pl['iss'] && 'api.enablebanking.com' === $pl['aud'] && $pl['exp'] > $pl['iat'] );
check( 'jwt signature verifies', 1 === openssl_verify( $h . '.' . $p2, $dec( $s ), $pub, OPENSSL_ALGO_SHA256 ) );

echo "\n" . ( $n - $fail ) . "/$n passed\n";
exit( $fail ? 1 : 0 );
