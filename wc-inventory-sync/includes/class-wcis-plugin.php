<?php
/**
 * Zentrale Plugin-Klasse (Singleton) – verbindet alle Komponenten.
 *
 * @package WC_Inventory_Sync
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bootstrap-Klasse.
 */
class WCIS_Plugin {

	/**
	 * Singleton-Instanz.
	 *
	 * @var WCIS_Plugin|null
	 */
	protected static $instance = null;

	/**
	 * Liefert die Singleton-Instanz.
	 *
	 * @return WCIS_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Initialisiert Hooks und Komponenten.
	 */
	public function init() {
		// DB-Schema bei Versionswechsel aktualisieren.
		WCIS_Install::maybe_upgrade();

		// Eigenes Cron-Intervall (jede Minute) registrieren.
		add_filter( 'cron_schedules', array( $this, 'add_cron_schedule' ) );

		// Sync-Engine (Hooks für Bestandsänderungen + Cron-Queue).
		WCIS_Sync_Engine::init();

		// Periodischer Abgleich (Reconciliation).
		WCIS_Reconcile::init();

		// Optionaler Produkt-Sync (neue Produkte 1:1 übertragen).
		WCIS_Product_Sync::init();

		// REST-Routen.
		add_action( 'rest_api_init', array( 'WCIS_REST_Controller', 'register_routes' ) );

		// Admin-Oberfläche.
		if ( is_admin() ) {
			WCIS_Admin::instance()->init();
		}

		// "Einstellungen"-Link auf der Plugin-Seite.
		add_filter( 'plugin_action_links_' . WCIS_BASENAME, array( $this, 'action_links' ) );
	}

	/**
	 * Fügt ein 1-Minuten-Cron-Intervall hinzu.
	 *
	 * @param array $schedules Vorhandene Intervalle.
	 * @return array
	 */
	public function add_cron_schedule( $schedules ) {
		$schedules['wcis_every_minute'] = array(
			'interval' => 60,
			'display'  => __( 'Jede Minute (WC Inventory Sync)', 'wc-inventory-sync' ),
		);
		if ( ! isset( $schedules['wcis_six_hours'] ) ) {
			$schedules['wcis_six_hours'] = array(
				'interval' => 6 * HOUR_IN_SECONDS,
				'display'  => __( 'Alle 6 Stunden (WC Inventory Sync)', 'wc-inventory-sync' ),
			);
		}
		return $schedules;
	}

	/**
	 * Fügt einen Einstellungen-Link hinzu.
	 *
	 * @param array $links Links.
	 * @return array
	 */
	public function action_links( $links ) {
		$url  = admin_url( 'admin.php?page=wc-inventory-sync' );
		$link = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Einstellungen', 'wc-inventory-sync' ) . '</a>';
		array_unshift( $links, $link );
		return $links;
	}
}
