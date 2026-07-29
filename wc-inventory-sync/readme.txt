=== WC Inventory Sync ===
Contributors: blocksocial
Tags: woocommerce, inventory, stock, sync, multishop
Requires at least: 5.8
Tested up to: 6.6
Requires PHP: 7.4
WC requires at least: 5.0
WC tested up to: 9.9
Stable tag: 1.7.0
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

= 1.7.0 =
* Steuer-Sync: Steuerstatus und Steuerklasse werden als eigenes Feld übertragen (getrennt vom Preis) – auch für Variationen. Neue „Steuerklassen-Zuordnung" (Empfängerseite) übersetzt abweichende Slugs (z. B. „reduzierter-preis" ⇒ „reduced-rate"); unbekannte, nicht zugeordnete Slugs werden übersprungen statt fälschlich auf Standard zu fallen.
* Produkte vom Hauptshop holen (Pull): Ein Neben-Shop kann die erste Produkt-Übernahme selbst starten und sich die Produkte des Hauptshops holen – ohne dass der Hauptshop an alle Shops verteilen muss. Chunk-basiert mit Fortschrittsbalken.

= 1.6.0 =
* Komplett überarbeitete, moderne Admin-Oberfläche: App-Layout mit sticky Topbar (Live-Status), seitlicher Tab-Navigation (kein langes Scrollen), Karten, Toggle-Switches, Chips, animierten Fortschrittsbalken und Dark-Mode (folgt dem System-Theme). Rein CSS/JS, keine zusätzlichen Bibliotheken.

= 1.5.0 =
* Kategorie-Ausschluss: ganze Produktkategorien lassen sich vom Sync ausschließen (harter Ausschluss, gilt aus- und eingehend).
* Vorschau „Umfang anzeigen": zeigt anhand der aktuellen (auch ungespeicherten) Auswahl, wie viele Produkte in den Sync-Umfang fallen – inkl. Beispielliste.

= 1.4.0 =
* Sync-Filter pro Shop: Auswahl, welche Produkte synchronisiert werden – nach Kategorie, nach Marke (automatische Erkennung gängiger Marken-Taxonomien) und per Einzelprodukt-Auswahl. Einzelne Produkte lassen sich hart ausschließen (gilt aus- und eingehend).
* Feld-Auswahl für den Produkt-Sync: frei wählbar, welche Felder (z. B. Preis, Beschreibung, Bilder, Status) übertragen werden – so kann jeder Shop z. B. eigene Preise behalten.
* Filter gilt für Bestands- und Produkt-Sync sowie für den Abgleich.

= 1.3.0 =
* Optionaler Produkt-Sync: neue Produkte werden 1:1 an alle Shops übertragen (einfache und variable Produkte), inklusive Veröffentlichungsstatus (veröffentlicht/privat/Entwurf). Zuordnung per SKU.
* Quelle wählbar (nur Hauptshop oder jeder Shop), Bilder optional mitübertragen, bestehende Produkte optional aktualisieren.
* „Alle Produkte jetzt übertragen"-Button mit Fortschrittsbalken (chunk-basiert, kein Timeout) zum Befüllen neuer Shops.
* Guaranteed Delivery über die Retry-Queue (nun mit Endpoint-Unterstützung); DB-Upgrade-Routine für bestehende Installationen.

= 1.2.0 =
* Automatischer Abgleich (Reconciliation): periodischer Konsistenz-Check (stündlich/6h/täglich), der Bestands-Abweichungen zwischen den Shops erkennt und Korrekturen nachreicht – ideal, wenn ein Shop kurz nicht erreichbar war.
* Konflikt-Strategie wählbar: „Niedrigster Bestand gewinnt" (schützt vor Überverkauf) oder „Hauptshop maßgeblich".
* Nicht erreichbare Shops beim Abgleich landen in der Retry-Queue und werden später automatisch nachgezogen.
* Manueller „Jetzt abgleichen"-Button und Statusanzeige des nächsten Abgleichs.

= 1.1.0 =
* Voll-Synchronisation als abbruchsicherer Hintergrund-Job mit Prozent-Fortschrittsbalken (Live-Anzeige, kein PHP-Timeout).
* Einstellbare Batchgröße (1–500) und HTTP-Timeout (5–60 s).
* Nicht erreichbare Shops werden beim Voll-Sync übersprungen (kein Blockieren).

= 1.0.0 =
* Erste Version.
