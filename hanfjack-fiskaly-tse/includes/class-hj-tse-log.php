<?php
defined( 'ABSPATH' ) || exit;

/**
 * Persistent log of every TSE transaction (needed for DSFinV-K, outage lists and audits).
 */
class HJ_TSE_Log {

	const DB_VERSION = '3';

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
			kind VARCHAR(20) NOT NULL DEFAULT '',
			note TEXT NULL,
			closing_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			PRIMARY KEY  (id),
			KEY order_id (order_id),
			KEY tx_number (tx_number),
			KEY state (state),
			KEY created_gmt (created_gmt),
			KEY closing_id (closing_id)
		) {$charset};";
		dbDelta( $sql );
		$closings = $wpdb->prefix . 'hj_tse_closings';
		$sql2 = "CREATE TABLE {$closings} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			created_gmt DATETIME NOT NULL,
			business_date DATE NOT NULL,
			closing_uuid CHAR(36) NOT NULL DEFAULT '',
			export_id INT UNSIGNED NOT NULL DEFAULT 0,
			client_id CHAR(36) NOT NULL DEFAULT '',
			state VARCHAR(20) NOT NULL DEFAULT '',
			error TEXT NULL,
			tx_count INT UNSIGNED NOT NULL DEFAULT 0,
			totals_json TEXT NULL,
			body_json LONGTEXT NULL,
			completed_gmt DATETIME NULL,
			PRIMARY KEY  (id),
			KEY business_date (business_date),
			KEY state (state)
		) {$charset};";
		dbDelta( $sql2 );
		// Backfill the kind column for rows written by plugin versions before 0.2.0.
		$wpdb->query( "UPDATE {$table} SET kind = 'refund' WHERE kind = '' AND refund_id > 0 AND state IN ('FINISHED','ERROR')" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query( "UPDATE {$table} SET kind = 'abort' WHERE kind = '' AND receipt_type = 'ABORT' AND state = 'FINISHED'" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query( "UPDATE {$table} SET kind = 'sale' WHERE kind = '' AND refund_id = 0 AND order_id > 0 AND receipt_type = 'RECEIPT' AND state = 'FINISHED'" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
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
			'kind'        => '',
			'note'        => null,
			'closing_id'  => 0,
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

	public static function closings_table(): string {
		global $wpdb;
		return $wpdb->prefix . 'hj_tse_closings';
	}

	/** Rows of a business day (local timezone) that are not yet part of a closing. */
	public static function rows_for_day( string $business_date ): array {
		global $wpdb;
		$tz    = wp_timezone();
		$start = ( new DateTimeImmutable( $business_date . ' 00:00:00', $tz ) )->getTimestamp();
		$end   = $start + 86400;
		$table = self::table();
		return (array) $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$table} WHERE closing_id = 0 AND state IN ('FINISHED','ERROR') AND kind <> '' AND ( ( time_end >= %d AND time_end < %d ) OR ( time_end = 0 AND UNIX_TIMESTAMP(created_gmt) >= %d AND UNIX_TIMESTAMP(created_gmt) < %d ) ) ORDER BY tx_number ASC, id ASC", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$start,
				$end,
				$start,
				$end
			),
			ARRAY_A
		);
	}

	/** Distinct business dates (local) with open rows older than today. */
	public static function open_days( string $before_date ): array {
		global $wpdb;
		$tz    = wp_timezone();
		$table = self::table();
		$rows  = (array) $wpdb->get_col( "SELECT DISTINCT IF(time_end>0,time_end,UNIX_TIMESTAMP(created_gmt)) AS t FROM {$table} WHERE closing_id = 0 AND state IN ('FINISHED','ERROR') AND kind <> ''" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$days  = array();
		foreach ( $rows as $t ) {
			$d = ( new DateTimeImmutable( '@' . (int) $t ) )->setTimezone( $tz )->format( 'Y-m-d' );
			if ( $d < $before_date ) {
				$days[ $d ] = true;
			}
		}
		$days = array_keys( $days );
		sort( $days );
		return $days;
	}

	public static function assign_closing( array $row_ids, int $closing_id ): void {
		global $wpdb;
		if ( empty( $row_ids ) ) {
			return;
		}
		$ids   = implode( ',', array_map( 'intval', $row_ids ) );
		$table = self::table();
		$wpdb->query( $wpdb->prepare( "UPDATE {$table} SET closing_id = %d WHERE id IN ({$ids})", $closing_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	public static function insert_closing( array $row ): int {
		global $wpdb;
		$wpdb->insert( self::closings_table(), array_merge( array( 'created_gmt' => gmdate( 'Y-m-d H:i:s' ) ), $row ) );
		return (int) $wpdb->insert_id;
	}

	public static function update_closing( int $id, array $row ): void {
		global $wpdb;
		$wpdb->update( self::closings_table(), $row, array( 'id' => $id ) );
	}

	public static function get_closing( int $id ): ?array {
		global $wpdb;
		$table = self::closings_table();
		$row   = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $row ?: null;
	}

	public static function closings( int $limit = 60 ): array {
		global $wpdb;
		$table = self::closings_table();
		return (array) $wpdb->get_results( $wpdb->prepare( "SELECT id, created_gmt, business_date, closing_uuid, export_id, state, error, tx_count, totals_json, completed_gmt FROM {$table} ORDER BY business_date DESC, id DESC LIMIT %d", $limit ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	public static function pending_closings(): array {
		global $wpdb;
		$table = self::closings_table();
		return (array) $wpdb->get_results( "SELECT * FROM {$table} WHERE state IN ('PENDING','WORKING') ORDER BY id ASC", ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	public static function closing_for_date( string $business_date ): ?array {
		global $wpdb;
		$table = self::closings_table();
		$row   = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE business_date = %s AND state <> 'ERROR' ORDER BY id DESC LIMIT 1", $business_date ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $row ?: null;
	}
}
