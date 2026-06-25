# WC Professional Catalog

Professionelles WooCommerce-Plugin für Produktkataloge mit Online-Flipbook,
PDF-Export, Druckansicht und voller Design-Steuerung. Entwickelt mit Fokus auf
B2C/B2B-Shops, die zusätzlich zum Online-Shop einen klassischen Katalog
brauchen (z. B. zum Verschicken, Ausdrucken oder am Counter).

Plugin-Code liegt unter `wc-professional-catalog/`.

## Features

- **Layouts**: Raster, Liste oder blätterbares Online-Flipbook
- **Filter**: Alle Produkte oder gefiltert nach Kategorie, Schlagwort oder Marke
- **Marken-Erkennung**: arbeitet mit `product_brand`, `pwb-brand` oder `pa_brand`
- **PDF-Export**: modernes Cover, Seitenkopf mit Logo, Seitenzahl, A4/A5/Letter
- **Druckansicht** mit Auto-Druck (`window.print()`)
- **Online-Flipbook** mit Tastatur, Buttons und Touch-Swipe
- **Brutto- und Nettopreise** nebeneinander (auch wenn Steuer in WC deaktiviert ist - Fallback-Satz)
- **Grundpreis** (z. B. „750 ml → 16,00 € / 1 L") automatisch aus Titel oder Produkt-Meta
- **Direkter Kauf-Link** unter jedem Produkt - der Katalog ist überall mit dem Shop verknüpft
- **Typografie pro Block**: Titel, Kurzbeschreibung und Preis bekommen jeweils eigene Schriftart, Größe, Gewicht und Stil
- **Farben & Logo** über Color-Picker und Media-Library
- **Shortcodes**: `[wcpc_catalog]`, `[wcpc_catalog_buttons]`

## Endpoints

| URL | Wirkung |
| --- | --- |
| `/wc-catalog/flipbook/` | Online-Katalog |
| `/wc-catalog/pdf/` | PDF / Print-Fallback |
| `/wc-catalog/print/` | Druckansicht |
| `/wc-catalog/pdf/brand/<slug>/` | PDF gefiltert nach Marke |
| `/wc-catalog/pdf/category/<slug>/` | PDF gefiltert nach Kategorie |
| `/wc-catalog/pdf/tag/<slug>/` | PDF gefiltert nach Schlagwort |

## Installation

1. Ordner `wc-professional-catalog/` nach `wp-content/plugins/` kopieren.
2. (Empfohlen) Im Plugin-Ordner `composer require dompdf/dompdf` ausführen für native PDFs. Ohne Dompdf läuft das Plugin in einem druckbaren HTML-Fallback mit Auto-Print.
3. In WordPress aktivieren.
4. **Wichtig:** unter *Einstellungen → Permalinks* einmal speichern, damit `/wc-catalog/...` greift.
5. Unter **Katalog** Design, Farben, Schriften und PDF-Layout einstellen.

## Shortcode-Beispiele

```text
[wcpc_catalog]
[wcpc_catalog layout="flipbook"]
[wcpc_catalog category="gin,spirituosen" columns="4"]
[wcpc_catalog brand="monkey-47" limit="20"]
[wcpc_catalog tag="bestseller"]
[wcpc_catalog ids="12,15,22"]
[wcpc_catalog_buttons]
```

## Architektur

```
wc-professional-catalog/
├── wc-professional-catalog.php   Plugin-Bootstrap
├── uninstall.php
├── composer.json                 Dompdf-Dependency
├── includes/
│   ├── class-wcpc-plugin.php     Settings, Endpoints, Hooks
│   ├── class-wcpc-catalog.php    Query, Marken-Erkennung, CSS-Vars
│   ├── class-wcpc-price.php      Brutto/Netto + Grundpreis
│   ├── class-wcpc-pdf.php        Dompdf-Adapter + Fallback
│   ├── class-wcpc-shortcode.php
│   ├── class-wcpc-assets.php
│   └── class-wcpc-admin.php      Tabbed Settings UI
├── admin/views/                  Settings-Tabs (General/Design/Typo/PDF/Shortcuts)
├── public/css/catalog.css        Themable Cards + Flipbook
├── public/css/print.css          @media print + Print-Page Layout
├── public/js/catalog-flip.js     Flipbook (Keyboard + Swipe)
└── templates/
    ├── product-card.php          Produktkarte (Front)
    ├── catalog-grid.php          Grid-Layout
    ├── catalog-flipbook.php      Flipbook
    ├── catalog-print.php         Druckseite
    └── catalog-pdf.php           PDF-Template (Dompdf-tauglich)
```

## Erweiterbarkeit

Alle Templates können in einem (Child-)Theme unter `wc-professional-catalog/<template>.php` überschrieben werden — geplant für 1.1.
