<?php
/**
 * AJAX endpoints for the PDF Generator.
 *
 * Endpoints (all admin-ajax, capability: manage_woocommerce, nonce: wcpc_pdf_nonce):
 *   wcpc_pdf_start    POST  scope_type, scope_value, scope_label  -> job record
 *   wcpc_pdf_tick     POST  id                                    -> job record
 *   wcpc_pdf_status   POST  id                                    -> job record
 *   wcpc_pdf_cancel   POST  id                                    -> success bool
 *   wcpc_pdf_delete   POST  id                                    -> success bool
 *
 * @package WCProfessionalCatalog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCPC_PDF_Ajax {

	public static function init() {
		add_action( 'wp_ajax_wcpc_pdf_start', array( __CLASS__, 'start' ) );
		add_action( 'wp_ajax_wcpc_pdf_tick', array( __CLASS__, 'tick' ) );
		add_action( 'wp_ajax_wcpc_pdf_status', array( __CLASS__, 'status' ) );
		add_action( 'wp_ajax_wcpc_pdf_cancel', array( __CLASS__, 'cancel' ) );
		add_action( 'wp_ajax_wcpc_pdf_delete', array( __CLASS__, 'delete' ) );
	}

	private static function gate() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( array( 'error' => 'forbidden' ), 403 );
		}
		check_ajax_referer( 'wcpc_pdf_nonce', '_wpnonce' );
	}

	public static function start() {
		self::gate();

		$type  = isset( $_POST['scope_type'] ) ? sanitize_key( wp_unslash( $_POST['scope_type'] ) ) : 'all';
		$value = isset( $_POST['scope_value'] ) ? sanitize_text_field( wp_unslash( $_POST['scope_value'] ) ) : '';
		$label = isset( $_POST['scope_label'] ) ? sanitize_text_field( wp_unslash( $_POST['scope_label'] ) ) : '';

		if ( ! in_array( $type, array( 'all', 'main_cat', 'sub_cat' ), true ) ) {
			$type = 'all';
		}

		$record = WCPC_PDF_Job::create(
			array(
				'type'  => $type,
				'value' => $value,
				'label' => $label ?: ucfirst( $type ),
			)
		);

		wp_send_json_success( self::trim_record( $record ) );
	}

	public static function tick() {
		self::gate();
		$id     = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
		$record = WCPC_PDF_Job::tick( $id );
		wp_send_json_success( self::trim_record( $record ) );
	}

	public static function status() {
		self::gate();
		$id     = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
		$record = WCPC_PDF_Job::get( $id );
		if ( ! $record ) {
			wp_send_json_error( array( 'error' => 'not_found' ), 404 );
		}
		wp_send_json_success( self::trim_record( $record ) );
	}

	public static function cancel() {
		self::gate();
		$id = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
		wp_send_json_success( array( 'ok' => (bool) WCPC_PDF_Job::cancel( $id ) ) );
	}

	public static function delete() {
		self::gate();
		$id = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
		wp_send_json_success( array( 'ok' => (bool) WCPC_PDF_Job::delete( $id ) ) );
	}

	/**
	 * Strip the heavy product_ids array from the payload returned to JS - the
	 * UI never needs it and we cut the response from KBs to bytes.
	 *
	 * @param array $record
	 * @return array
	 */
	private static function trim_record( $record ) {
		if ( isset( $record['product_ids'] ) ) {
			unset( $record['product_ids'] );
		}
		return $record;
	}
}
