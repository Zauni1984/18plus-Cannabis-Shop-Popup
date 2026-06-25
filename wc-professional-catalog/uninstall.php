<?php
/**
 * Uninstall handler for WC Professional Catalog.
 *
 * @package WCProfessionalCatalog
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'wcpc_settings' );

// Clear rewrite rules.
flush_rewrite_rules();
