<?php
/**
 * Einstellungen: Speicherung und Zugriffshelfer.
 *
 * @package WC_Inventory_Sync
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Verwaltet die Plugin-Optionen.
 *
 * Alle Shops im Netzwerk teilen sich dasselbe "Netzwerk-Secret" und dieselbe
 * Shop-Liste (Topologie). Ein Shop ist der Master (Hauptshop); dieser startet
 * die erste Voll-Synchronisation. Der Master ist jederzeit änderbar.
 */
class WCIS_Settings {

	/**
	 * Zwischenspeicher der Optionen.
	 *
	 * @var array|null
	 */
	protected static $cache = null;

	/**
	 * Standardwerte.
	 *
	 * @return array
	 */
	public static function defaults() {
		return array(
			'enabled'        => true,
			'network_secret' => '',
			'this_shop_name' => get_bloginfo( 'name' ),
			'this_shop_url'  => home_url(),
			'master_url'     => home_url(),
			// Liste aller Shops im Netzwerk: [ ['name'=>..,'url'=>..], ... ]
			// Enthält üblicherweise auch diesen Shop selbst.
			'shops'          => array(),
			// Bei fehlender SKU oder nicht vorhandenem Produkt still ignorieren.
			'sync_status'    => true,  // Auch Lagerstatus (in/out of stock) mitsenden.
			'log_level'      => 'info', // info | error
		);
	}

	/**
	 * Liefert alle Einstellungen (mit Defaults gemischt).
	 *
	 * @return array
	 */
	public static function all() {
		if ( null === self::$cache ) {
			$saved        = get_option( WCIS_OPT, array() );
			$saved        = is_array( $saved ) ? $saved : array();
			self::$cache  = wp_parse_args( $saved, self::defaults() );
		}
		return self::$cache;
	}

	/**
	 * Liefert einen einzelnen Wert.
	 *
	 * @param string $key     Schlüssel.
	 * @param mixed  $default Standard.
	 * @return mixed
	 */
	public static function get( $key, $default = null ) {
		$all = self::all();
		return array_key_exists( $key, $all ) ? $all[ $key ] : $default;
	}

	/**
	 * Speichert die Einstellungen.
	 *
	 * @param array $values Neue Werte (werden mit vorhandenen gemischt).
	 */
	public static function update( array $values ) {
		$merged = wp_parse_args( $values, self::all() );
		update_option( WCIS_OPT, $merged );
		self::$cache = $merged;
	}

	/**
	 * Ist die Synchronisation global aktiviert?
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		return (bool) self::get( 'enabled', true );
	}

	/**
	 * Normalisiert eine URL für Vergleiche (Schema + Host + Pfad, ohne Trailing-Slash).
	 *
	 * @param string $url URL.
	 * @return string
	 */
	public static function normalize_url( $url ) {
		$url = trim( (string) $url );
		if ( '' === $url ) {
			return '';
		}
		$url = preg_replace( '#^https?://#i', '', $url ); // Schema entfernen für Vergleich.
		$url = strtolower( $url );
		$url = preg_replace( '#^www\.#', '', $url );
		return untrailingslashit( $url );
	}

	/**
	 * URL dieses Shops.
	 *
	 * @return string
	 */
	public static function this_url() {
		$url = self::get( 'this_shop_url' );
		return $url ? $url : home_url();
	}

	/**
	 * Ist dieser Shop der Master (Hauptshop)?
	 *
	 * @return bool
	 */
	public static function is_master() {
		return self::normalize_url( self::this_url() ) === self::normalize_url( self::get( 'master_url' ) );
	}

	/**
	 * Liefert alle Peer-Shops (alle Shops außer diesem).
	 *
	 * @return array Liste von ['name'=>..,'url'=>..].
	 */
	public static function get_peers() {
		$self  = self::normalize_url( self::this_url() );
		$peers = array();
		foreach ( (array) self::get( 'shops', array() ) as $shop ) {
			if ( empty( $shop['url'] ) ) {
				continue;
			}
			if ( self::normalize_url( $shop['url'] ) === $self ) {
				continue; // sich selbst überspringen.
			}
			$peers[] = array(
				'name' => isset( $shop['name'] ) ? $shop['name'] : $shop['url'],
				'url'  => untrailingslashit( $shop['url'] ),
			);
		}
		return $peers;
	}

	/**
	 * Das Netzwerk-Secret.
	 *
	 * @return string
	 */
	public static function secret() {
		return (string) self::get( 'network_secret', '' );
	}

	/**
	 * Erzeugt ein neues zufälliges Secret.
	 *
	 * @return string
	 */
	public static function generate_secret() {
		return wp_generate_password( 48, false, false );
	}
}
