<?php
defined( 'ABSPATH' ) || exit;

/**
 * Persistent log of every TSE transaction (needed for DSFinV-K, outage lists and audits).
 */
class HJ_TSE_Log {

	const DB_VERSION = '1';

	public static function table(): string {
		global $wpdb;
		return $wpdb->prefix . 'hj_tse_log';
	}

	public static function install(): void {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$table   = self::table();
		$charset = $wpdb->get_charset_collate();
		$sql     = "CREATE TABLE {$table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			created_gmt DATETIME NOT NULL,
			order_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			refund_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			tx_id CHAR(36) NOT NULL DEFAULT '',
			tx_number BIGINT UNSIGNED NOT NULL DEFAULT 0,
			revision INT UNSIGNED NOT NULL DEFAULT 0,
			state VARCHAR(20) NOT NULL DEFAULT '',
			receipt_type VARCHAR(20) NOT NULL DEFAULT '',
			total DECIMAL(12,2) NOT NULL DEFAULT 0,
			vat_json TEXT NULL,
			payment_json TEXT NULL,
			time_start BIGINT UNSIGNED NOT NULL DEFAULT 0,
			time_end BIGINT UNSIGNED NOT NULL DEFAULT 0,
			signature_counter BIGINT UNSIGNED NOT NULL DEFAULT 0,
			signature TEXT NULL,
			qr_code_data TEXT NULL,
			client_serial VARCHAR(100) NOT NULL DEFAULT '',
			tss_serial VARCHAR(128) NOT NULL DEFAULT '',
			error TEXT NULL,
			PRIMARY KEY  (id),
			KEY order_id (order_id),
			KEY tx_number (tx_number),
			KEY state (state),
			KEY created_gmt (created_gmt)
		) {$charset};";
		dbDelta( $sql );
		update_option( 'hj_tse_db_version', self::DB_VERSION, false );
	}

	public static function maybe_upgrade(): void {
		if ( get_option( 'hj_tse_db_version' ) !== self::DB_VERSION ) {
			self::install();
		}
	}

	public static function insert( array $row ): int {
		global $wpdb;
		$defaults = array(
			'created_gmt' => gmdate( 'Y-m-d H:i:s' ),
			'order_id'    => 0,
			'refund_id'   => 0,
			'tx_id'       => '',
			'tx_number'   => 0,
			'revision'    => 0,
			'state'       => '',
			'receipt_type' => '',
			'total'       => 0,
			'vat_json'    => null,
			'payment_json' => null,
			'time_start'  => 0,
			'time_end'    => 0,
			'signature_counter' => 0,
			'signature'   => null,
			'qr_code_data' => null,
			'client_serial' => '',
			'tss_serial'  => '',
			'error'       => null,
		);
		$row = array_intersect_key( array_merge( $defaults, $row ), $defaults );
		$wpdb->insert( self::table(), $row );
		return (int) $wpdb->insert_id;
	}

	public static function recent( int $limit = 50 ): array {
		global $wpdb;
		$table = self::table();
		return (array) $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} ORDER BY id DESC LIMIT %d", $limit ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	public static function counts(): array {
		global $wpdb;
		$table = self::table();
		$rows  = (array) $wpdb->get_results( "SELECT state, COUNT(*) AS c FROM {$table} GROUP BY state", ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$out   = array();
		foreach ( $rows as $r ) {
			$out[ $r['state'] ] = (int) $r['c'];
		}
		return $out;
	}
}
