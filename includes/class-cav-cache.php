<?php
/**
 * Cache integration for Cannabis Age Verifier.
 *
 * Page caches snapshot HTML and serve it to every visitor – including
 * snapshots that were created before this plugin was installed/enabled.
 * To keep the popup behaviour predictable we proactively flush the most
 * common WordPress page caches whenever settings change.
 *
 * @package CannabisAgeVerifier
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class CAV_Cache {

	public static function init() {
		add_action( 'update_option_' . CAV_OPTION_KEY, array( __CLASS__, 'flush' ), 10, 0 );
		add_action( 'update_option_' . CAV_LICENSE_KEY, array( __CLASS__, 'flush' ), 10, 0 );
		add_action( 'cav_after_reset', array( __CLASS__, 'flush' ) );
	}

	/**
	 * Flush every page-cache plugin we recognise. All calls are guarded by
	 * function_exists/class_exists so missing plugins are no-ops.
	 */
	public static function flush() {
		// WP Rocket.
		if ( function_exists( 'rocket_clean_domain' ) ) {
			rocket_clean_domain();
		}
		if ( function_exists( 'rocket_clean_minify' ) ) {
			rocket_clean_minify();
		}
		if ( function_exists( 'rocket_clean_cache_busting' ) ) {
			rocket_clean_cache_busting();
		}

		// W3 Total Cache.
		if ( function_exists( 'w3tc_flush_all' ) ) {
			w3tc_flush_all();
		}

		// WP Super Cache.
		if ( function_exists( 'wp_cache_clear_cache' ) ) {
			wp_cache_clear_cache();
		} elseif ( function_exists( 'wp_cache_clean_cache' ) ) {
			global $file_prefix;
			wp_cache_clean_cache( $file_prefix );
		}

		// LiteSpeed Cache.
		do_action( 'litespeed_purge_all' );

		// SG Optimizer (SiteGround).
		if ( function_exists( 'sg_cachepress_purge_cache' ) ) {
			sg_cachepress_purge_cache();
		}

		// Cache Enabler (KeyCDN).
		if ( class_exists( 'Cache_Enabler' ) && is_callable( array( 'Cache_Enabler', 'clear_complete_cache' ) ) ) {
			Cache_Enabler::clear_complete_cache();
		} elseif ( class_exists( 'Cache_Enabler' ) && is_callable( array( 'Cache_Enabler', 'clear_total_cache' ) ) ) {
			Cache_Enabler::clear_total_cache();
		}

		// Autoptimize.
		do_action( 'autoptimize_action_cachepurged' );
		if ( class_exists( 'autoptimizeCache' ) && is_callable( array( 'autoptimizeCache', 'clearall' ) ) ) {
			autoptimizeCache::clearall();
		}

		// Breeze (Cloudways).
		if ( function_exists( 'breeze_clear_all_cache' ) ) {
			breeze_clear_all_cache();
		}

		// WP Fastest Cache.
		if ( class_exists( 'WpFastestCache' ) ) {
			$wpfc = new WpFastestCache();
			if ( is_callable( array( $wpfc, 'deleteCache' ) ) ) {
				$wpfc->deleteCache( true );
			}
		}

		// Hummingbird.
		do_action( 'wphb_clear_page_cache' );

		// Swift Performance.
		if ( class_exists( 'Swift_Performance_Cache' ) && is_callable( array( 'Swift_Performance_Cache', 'clear_all_cache' ) ) ) {
			Swift_Performance_Cache::clear_all_cache();
		}

		// Pantheon / generic.
		if ( function_exists( 'pantheon_wp_clear_edge_paths' ) ) {
			pantheon_wp_clear_edge_paths( array( '/' ) );
		}

		// Core object cache (covers Memcached/Redis-backed setups too).
		if ( function_exists( 'wp_cache_flush' ) ) {
			wp_cache_flush();
		}

		// Cloudflare via Cloudflare WP plugin.
		if ( class_exists( '\CF\WordPress\Hooks' ) ) {
			do_action( 'cloudflare_purge_everything' );
		}

		/**
		 * Fires after Cannabis Age Verifier has flushed known caches.
		 * Use this to add custom integrations (e.g. Varnish, Cloudfront).
		 */
		do_action( 'cav_caches_flushed' );
	}
}
