<?php
/**
 * Sync-Filter: bestimmt pro Shop, welche Produkte synchronisiert werden
 * (Auswahl einzelner Produkte, nach Kategorie, nach Marke; plus Ausschlüsse)
 * und welche Felder (z. B. Preis) beim Produkt-Sync übertragen werden.
 *
 * Der Filter gilt für ausgehende Synchronisation (was dieser Shop sendet).
 * Ausgeschlossene Produkte werden zusätzlich auch eingehend nicht verändert.
 *
 * @package WC_Inventory_Sync
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter-Logik.
 */
class WCIS_Filter {

	/**
	 * Alle bekannten Feld-Schlüssel des Produkt-Syncs (für die Feld-Auswahl).
	 *
	 * @return array key => Label.
	 */
	public static function product_field_labels() {
		return array(
			'name'              => __( 'Name/Titel', 'wc-inventory-sync' ),
			'price'             => __( 'Preis (regulär & Angebot)', 'wc-inventory-sync' ),
			'description'       => __( 'Beschreibung', 'wc-inventory-sync' ),
			'short_description' => __( 'Kurzbeschreibung', 'wc-inventory-sync' ),
			'images'            => __( 'Bilder', 'wc-inventory-sync' ),
			'categories'        => __( 'Kategorien', 'wc-inventory-sync' ),
			'tags'              => __( 'Schlagwörter', 'wc-inventory-sync' ),
			'attributes'        => __( 'Attribute', 'wc-inventory-sync' ),
			'dimensions'        => __( 'Maße & Gewicht', 'wc-inventory-sync' ),
			'status'            => __( 'Veröffentlichungsstatus', 'wc-inventory-sync' ),
			'stock'             => __( 'Lagerbestand (bei Produktanlage)', 'wc-inventory-sync' ),
		);
	}

	/**
	 * Ist ein Produkt-Feld für die Übertragung freigegeben?
	 *
	 * @param string $key Feld-Schlüssel.
	 * @return bool
	 */
	public static function field_enabled( $key ) {
		$fields = WCIS_Settings::get( 'product_fields', null );
		if ( ! is_array( $fields ) ) {
			return true; // Standard: alle Felder.
		}
		if ( 'name' === $key ) {
			return true; // Name wird für die Zuordnung/Anlage immer benötigt.
		}
		return in_array( $key, $fields, true );
	}

	/**
	 * Ermittelt die aktive Marken-Taxonomie (falls vorhanden).
	 *
	 * @return string Taxonomie-Slug oder '' wenn keine gefunden.
	 */
	public static function brand_taxonomy() {
		$candidates = apply_filters(
			'wcis_brand_taxonomies',
			array( 'product_brand', 'pwb-brand', 'yith_product_brand', 'berocket_brand', 'pa_brand', 'product_brands' )
		);
		foreach ( (array) $candidates as $tax ) {
			if ( taxonomy_exists( $tax ) ) {
				return $tax;
			}
		}
		return '';
	}

	/**
	 * Normalisiert ein Produkt auf die relevante (Eltern-)ID.
	 *
	 * @param WC_Product|int $product Produkt oder ID.
	 * @return int
	 */
	protected static function base_id( $product ) {
		if ( is_numeric( $product ) ) {
			$product = wc_get_product( $product );
		}
		if ( ! $product instanceof WC_Product ) {
			return 0;
		}
		return $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
	}

	/**
	 * Soll dieses Produkt (ausgehend) synchronisiert werden?
	 *
	 * @param WC_Product|int $product Produkt.
	 * @return bool
	 */
	public static function should_sync( $product ) {
		$id = self::base_id( $product );
		if ( ! $id ) {
			return false;
		}

		// Harte Ausschlussliste hat immer Vorrang.
		if ( self::is_excluded( $id ) ) {
			return false;
		}

		$mode = WCIS_Settings::get( 'filter_mode', 'all' );
		if ( 'all' === $mode ) {
			return true;
		}

		// Modus "selected": mindestens ein Kriterium muss zutreffen.
		$include = array_map( 'intval', (array) WCIS_Settings::get( 'filter_include_ids', array() ) );
		if ( in_array( $id, $include, true ) ) {
			return true;
		}

		$cats = array_map( 'intval', (array) WCIS_Settings::get( 'filter_categories', array() ) );
		if ( ! empty( $cats ) && has_term( $cats, 'product_cat', $id ) ) {
			return true;
		}

		$brands = array_map( 'intval', (array) WCIS_Settings::get( 'filter_brands', array() ) );
		$btax   = self::brand_taxonomy();
		if ( ! empty( $brands ) && $btax && has_term( $brands, $btax, $id ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Ist dieses Produkt explizit ausgeschlossen (gilt auch eingehend)?
	 *
	 * @param WC_Product|int $product Produkt.
	 * @return bool
	 */
	public static function is_excluded( $product ) {
		$id = self::base_id( $product );
		if ( ! $id ) {
			return false;
		}
		$excluded = array_map( 'intval', (array) WCIS_Settings::get( 'filter_exclude_ids', array() ) );
		return in_array( $id, $excluded, true );
	}
}
