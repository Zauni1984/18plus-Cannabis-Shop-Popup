<?php
/**
 * Cannabis Age Verifier – Uninstall
 *
 * Removes all plugin data when WordPress fires the uninstall hook.
 *
 * @package CannabisAgeVerifier
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'cav_settings' );
delete_option( 'cav_license' );
delete_option( 'cav_hmac_secret' );
delete_transient( 'cav_license_status' );

// Best-effort cleanup of rate-limit transients.
global $wpdb;
$like = $wpdb->esc_like( '_transient_cav_rl_' ) . '%';
$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $like ) );
$like = $wpdb->esc_like( '_transient_timeout_cav_rl_' ) . '%';
$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $like ) );
