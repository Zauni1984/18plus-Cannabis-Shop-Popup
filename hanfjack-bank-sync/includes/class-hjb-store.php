<?php
defined( 'ABSPATH' ) || exit;

/**
 * Seen bank transactions (credits only) and their matching outcome.
 */
class HJB_Store {

	const DB_VERSION = '1';

	public static function table(): string {
		global $wpdb;
		return $wpdb->prefix . 'hjb_transactions';
	}

	public static function install(): void {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$table   = self::table();
		$charset = $wpdb->get_charset_collate();
		dbDelta(
			"CREATE TABLE {$table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			tx_key CHAR(40) NOT NULL,
			created_gmt DATETIME NOT NULL,
			account_iban VARCHAR(40) NOT NULL DEFAULT '',
			booking_date DATE NULL,
			amount DECIMAL(12,2) NOT NULL DEFAULT 0,
			currency CHAR(3) NOT NULL DEFAULT 'EUR',
			debtor_name VARCHAR(190) NOT NULL DEFAULT '',
			remittance TEXT NULL,
			status VARCHAR(20) NOT NULL DEFAULT 'new',
			order_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			reason VARCHAR(255) NOT NULL DEFAULT '',
			suggestions VARCHAR(255) NOT NULL DEFAULT '',
			PRIMARY KEY  (id),
			UNIQUE KEY tx_key (tx_key),
			KEY status (status),
			KEY order_id (order_id)
		) {$charset};"
		);
		update_option( 'hjb_db_version', self::DB_VERSION, false );
	}

	public static function maybe_upgrade(): void {
		if ( get_option( 'hjb_db_version' ) !== self::DB_VERSION ) {
			self::install();
		}
	}

	public static function exists( string $key ): bool {
		global $wpdb;
		$table = self::table();
		return (bool) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE tx_key = %s", $key ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	public static function insert( array $row ): int {
		global $wpdb;
		$row['created_gmt'] = gmdate( 'Y-m-d H:i:s' );
		$wpdb->insert( self::table(), $row );
		return (int) $wpdb->insert_id;
	}

	public static function update( int $id, array $row ): void {
		global $wpdb;
		$wpdb->update( self::table(), $row, array( 'id' => $id ) );
	}

	public static function get( int $id ): ?array {
		global $wpdb;
		$table = self::table();
		$row   = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $row ?: null;
	}

	public static function by_status( array $statuses, int $limit = 100 ): array {
		global $wpdb;
		$table = self::table();
		$in    = implode( ',', array_fill( 0, count( $statuses ), '%s' ) );
		$args  = array_merge( $statuses, array( $limit ) );
		return (array) $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE status IN ({$in}) ORDER BY booking_date DESC, id DESC LIMIT %d", $args ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL
	}

	public static function counts(): array {
		global $wpdb;
		$table = self::table();
		$out   = array();
		foreach ( (array) $wpdb->get_results( "SELECT status, COUNT(*) c FROM {$table} GROUP BY status", ARRAY_A ) as $r ) { // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$out[ $r['status'] ] = (int) $r['c'];
		}
		return $out;
	}
}
