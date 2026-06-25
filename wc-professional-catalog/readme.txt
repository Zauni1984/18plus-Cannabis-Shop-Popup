=== WC Professional Catalog ===
Contributors: zaunidigital
Tags: woocommerce, catalog, pdf, flipbook, print, brands
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later

Professioneller Produktkatalog für WooCommerce: Online-Flipbook, PDF-Export, Druckansicht, Marken-/Kategorie-/Tag-Filter, brutto/netto Preise und Grundpreis-Berechnung.

== Description ==

* Katalogansicht aller Produkte oder gefiltert nach Kategorie, Schlagwort oder Marke
* Online-Katalog mit Blätter-Effekt (Flipbook), mobil mit Swipe
* PDF-Export mit modernem Design, Cover-Seite, Logo, Farbsteuerung
* Druckansicht für Print-Versionen mit Auto-Druck
* Alle Produkte sind im Katalog mit dem Shop verlinkt - direkter Weg zum Kauf
* Brutto- und Netto-Preise nebeneinander, Grundpreis automatisch (z.B. 750 ml -> €/L)
* Schriftart, -größe, -gewicht und -stil getrennt für Titel, Kurzbeschreibung und Preis
* Logo, Cover-Titel, Untertitel und Fußzeile einstellbar
* Shortcodes für volle Flexibilität in Seiten und Beiträgen

== Installation ==

1. Plugin-Ordner nach `/wp-content/plugins/` hochladen.
2. (Optional, empfohlen) Im Plugin-Ordner ausführen: `composer require dompdf/dompdf` für native PDF-Erzeugung. Ohne Dompdf liefert das Plugin eine druckbare HTML-Seite, die direkt das Druckdialog öffnet.
3. Plugin in WordPress aktivieren.
4. Unter Katalog -> Einstellungen Design, Farben, Schriften und PDF-Optionen anpassen.
5. Auf einer Seite `[wcpc_catalog]` oder `[wcpc_catalog_buttons]` einfügen.

== Shortcodes ==

* `[wcpc_catalog]` - gesamter Katalog im Standard-Layout
* `[wcpc_catalog layout="flipbook"]` - Online-Flipbook
* `[wcpc_catalog category="gin,spirituosen"]` - Kategorie-Filter
* `[wcpc_catalog brand="monkey-47"]` - Marken-Filter
* `[wcpc_catalog tag="bestseller" columns="4" limit="24"]` - Schlagwort + Layout
* `[wcpc_catalog_buttons]` - PDF / Druck / Online-Katalog Buttons

== Endpoints ==

* `/wc-catalog/flipbook/` - Online-Katalog
* `/wc-catalog/pdf/` - PDF-Download / Anzeige
* `/wc-catalog/print/` - Druckansicht mit Auto-Druck
* `/wc-catalog/pdf/brand/<slug>/` - PDF gefiltert nach Marke
* `/wc-catalog/pdf/category/<slug>/` - PDF gefiltert nach Kategorie
* `/wc-catalog/pdf/tag/<slug>/` - PDF gefiltert nach Schlagwort

== Changelog ==

= 1.0.0 =
* Erste Version: Online-Katalog, PDF, Print, Marken-/Kategorie-/Tag-Filter, brutto/netto, Grundpreis.
