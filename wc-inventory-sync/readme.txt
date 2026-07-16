=== WC Inventory Sync ===
Contributors: stefanz
Tags: woocommerce, inventory, stock, sync, multishop
Requires at least: 5.8
Tested up to: 6.6
Requires PHP: 7.4
WC requires at least: 5.0
WC tested up to: 9.9
Stable tag: 1.0.0
License: MIT
License URI: https://opensource.org/licenses/MIT

Synchronisiert Lagerbestände zwischen mehreren WooCommerce-Shops in nahezu Echtzeit. Zuordnung per SKU, wählbarer und änderbarer Hauptshop.

== Description ==

WC Inventory Sync verbindet drei oder mehr WooCommerce-Shops zu einem Bestands-Verbund.
Verkauft ein Shop einen Artikel, wird der neue Lagerbestand sofort an alle übrigen
Shops übertragen (Zuordnung per SKU).

**Funktionen**

* Kopplung mehrerer Shops (3+) über die REST-API mit HMAC-signierten Anfragen.
* Ein wählbarer Hauptshop (Master) für die erste Voll-Synchronisation – jederzeit änderbar.
* Nahezu Echtzeit-Push bei jeder Bestandsänderung (Bestellung, Storno, manuelle Änderung).
* Zuordnung per SKU – einfache und variable Produkte (über Variations-SKUs) werden unterstützt.
* Produkte, die nur in einem Shop existieren, werden automatisch ignoriert.
* Automatische Wiederholung fehlgeschlagener Zustellungen (Retry-Queue via Cron).
* Verbindungstest, Protokoll und Status-Übersicht im Backend.

== Installation ==

1. Ordner `wc-inventory-sync` nach `wp-content/plugins/` hochladen (auf jedem Shop).
2. Plugin in jedem Shop aktivieren.
3. Im Hauptshop unter *WooCommerce → Lagerbestand-Sync* ein Netzwerk-Secret erzeugen.
4. In jedem Shop dasselbe Secret eintragen, alle Shop-URLs hinterlegen und den Hauptshop wählen.
5. Verbindung testen und anschließend im Hauptshop die erste Voll-Synchronisation starten.

== Changelog ==

= 1.0.0 =
* Erste Version.
