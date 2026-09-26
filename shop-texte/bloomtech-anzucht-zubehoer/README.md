# Bloomtech-Import: Anzucht & Zubehör

Quelle: Google-Sheet des Lieferanten Bloomtech ("Anzucht und Zubehör", 49 Zeilen, CSV-Export).
Auftrag: 49 neue Produkte inkl. Lagerbestand anlegen, ohne Bilder ("Bilder kommen später" → alle als `status: draft`).

## Ergebnis

- **44 Produkte neu angelegt** (WooCommerce-IDs 39911–39954, fortlaufend, `status: draft`).
- **5 Zeilen übersprungen** – echte Duplikate zu bereits bestehenden Eigenbestand-Produkten (HJ-SKU). Vom Nutzer per Rückfrage explizit bestätigt: "Duplikate überspringen".
- Alle 44 Produkte tragen: Name, Slug, SKU = Bloomtech-Artikelnummer (kompatibel mit dem bestehenden `bloomtech-stock-sync`-Plugin), Preis, bereinigte Lang-/Kurzbeschreibung (H1 und Hanfjack-CTA-Absatz entfernt), Kategorie, `manage_stock=true`, Lagerbestand + Lagerstatus, `min_age=18`, GTIN/EAN (soweit im Sheet vorhanden), sowie vollständige Yoast-SEO-Felder (SEO-Titel, Meta-Description, Fokus-Keyword).

## Übersprungene Duplikate (5)

| Bloomtech-Artikelnr. | Produkt (Sheet) | Bereits vorhanden als |
|---|---|---|
| 12264 | Eazy Plug 150er Tray | ID 30830, SKU HJ-9999635 |
| 12265 | Eazy Plug 24er Tray | ID 30829, SKU HJ-9277713 |
| 10804 | Plagron Perlite 10 Liter | ID 32135, SKU HJ-9999732 |
| 15337 | Clonex Mist 300ml | ID 30824, SKU HJ-5466467 |
| 17086 | Hubey Bio Rooting Gel 30 ml | ID 30821, SKU HJ-6254876 |

## Neue Kategorien (alle Kind von Growshop, ID 538)

| ID | Name | Produkte |
|---|---|---|
| 16435 | Anzuchtmedien | 13 |
| 16436 | Zimmergewächshäuser | 2 |
| 16437 | Stecklingszubehör | 7 |
| 16438 | Anzuchtbeleuchtung | 16 |
| 16439 | Hydrokultur-Anzucht | 3 |

Zusätzlich wurde die bestehende Kategorie **Anzucht** (ID 12713) für 3 Sets ("Anzuchtset Warme Füße groß/klein", "Anzuchtset klein & eco") mitgenutzt. Summe: 13+2+7+16+3+3 = 44.

## Marken- und Hersteller-Terms angelegt

Diese Terms existieren jetzt in den Taxonomien `pwb-brand` (Marken) bzw. `product_manufacturer` (Hersteller), sind aber **nicht** mit den 44 Produkten verknüpft – siehe Limitation unten.

**Neue Marken-Terms (`pwb-brand`):**
Ferna Trade (16440), Grodan (16441), Speedgrow (16442), Romberg (16443), Hermann Meyer KG (16444), Terra Exotica (16445), Nutriculture (16446), Eazy Plug (16447)

**Wiederverwendete Marken-Terms:** Plagron (1953), Jiffy (12726), Growth Technology (12716), ROOT!T (12720), hubey GmbH (12712), Sanlight (1891)

**Neue Hersteller-Terms (`product_manufacturer`):** Grow In AG (16448), HPS (16449), HGA International B.V. (16450)

## Technische Erkenntnisse / bekannte Lücken

1. **`min_age` und `gtin` nur über Top-Level-Feld schreibbar.** `meta_data`-Einträge für `_min_age`/`_ts_gtin` werden bei CREATE und UPDATE stillschweigend ignoriert. Nur die unpräfigierten Top-Level-Felder `min_age` und `gtin` in `wp_wc_batch_update_products` persistieren – und werden von jedem nachfolgenden Schreibvorgang, der das Feld auslässt, wieder auf leer zurückgesetzt. Deshalb wurden beide Felder im letzten, konsolidierenden Update-Call pro Produkt mitgeschickt.

2. **Marken (`pwb-brand`) und Hersteller (`product_manufacturer`) sind über die verfügbare REST/MCP-Schnittstelle nicht auf Produkte schreibbar.** Getestet und fehlgeschlagen:
   - `brands: [{"id": X}]` beim Anlegen (CREATE) – Readback zeigt leeres `brands: []`.
   - `pwb-brand: [X]` (Array von Term-IDs) bei UPDATE – keine Wirkung.
   - `manufacturer: X` (Top-Level-Int) bei UPDATE – erzeugt einen inerten, generischen Postmeta-Key namens "manufacturer", verknüpft aber **nicht** die echte Taxonomie-Beziehung.

   Konsequenz: Alle Marken-/Hersteller-Terms oben sind angelegt, aber mit `count: 0` – sie hängen an keinem Produkt. Eine Zuordnung ist nur manuell über das WP-Admin-Backend möglich.

3. **`delivery_time` (Lieferzeit) konnte nicht gesetzt werden.** Weder ein reiner String-Slug (`"2-7-tage"`) noch ein Array-Objekt-Format (`[{"id": 4236}]`) persistierte. Für alle 44 Produkte bleibt das Feld leer.

4. Produkt 39930 (Clonex Mist 750ml, Hersteller "Drehandel GmbH" laut Sheet) hat keinen auffindbaren `product_manufacturer`-Term – es wurde kein Hersteller-Term dafür angelegt, da keine passende ID ermittelt werden konnte.

## Vollständige Produktliste (44)

| ID | SKU | Produkt | Kategorie | VK brutto | Lagerbestand | GTIN/EAN |
|---|---|---|---|---|---|---|
| 39911 | 12575 | Anzuchtset Warme Füße, groß | Anzucht | 99.9 € | 18 | – |
| 39912 | 12574 | Anzuchtset Warme Füße, klein | Anzucht | 69.9 € | 8 | – |
| 39913 | 11142 | Anzuchtset klein & eco | Anzucht | 12.9 € | 8 | – |
| 39914 | 10238 | Steinwollwürfel im Tray 4x4cm 77 Stück | Anzuchtmedien | 14.9 € | 19 | 4260617973685 |
| 39915 | 11094 | Steinwollblock 10cm - 4cm Loch | Anzuchtmedien | 0.9 € | 0 | 4260617973623 |
| 39916 | 10241 | Steinwollblock 7,5cm - 4cm Loch | Anzuchtmedien | 0.5 € | 687 | 4260617973661 |
| 39917 | 11091 | Steinwollwürfel 4x4cm mit Ummantelung | Anzuchtmedien | 0.2 € | 2423 | 5702315080054 |
| 39918 | 13407 | Eazy Block 7,5cm | Anzuchtmedien | 1.2 € | 365 | 642070781993 |
| 39919 | 15359 | Eazy Plug 12er Tray | Anzuchtmedien | 4.9 € | 9 | 8717662001963 |
| 39920 | 12263 | Eazy Plug 77er Tray | Anzuchtmedien | 11.9 € | 84 | 8717662000843 |
| 39921 | 13559 | Eazy Plug rund 104C Tray | Anzuchtmedien | 13.9 € | 0 | 8717662002298 |
| 39922 | 10630 | Jiffy Quelltöpfe Ø41mm | Anzuchtmedien | 0.12 € | 1820 | 4260617971520 |
| 39923 | 17310 | NextGen Growplug 84er Tray – Anzuchtplugs für Stecklinge & Samen \| Plagron | Anzuchtmedien | 19.9 € | 36 | 8720188273097 |
| 39924 | 14155 | Plagron Perlite 60 Liter | Anzuchtmedien | 27.9 € | 63 | 8718104121416 |
| 39925 | 12613 | Speedgrow Green 126er Tray Ø28mm / 40mm | Anzuchtmedien | 13.9 € | 31 | 4260617971827 |
| 39926 | 12399 | Speedgrow Green 84er Tray Ø38mm / 40mm | Anzuchtmedien | 12.9 € | 0 | 4260617971834 |
| 39927 | 11877 | Zimmergewächshaus groß 58x38cm | Zimmergewächshäuser | 29.9 € | 19 | 8720256759607 |
| 39928 | 10984 | Zimmergewächshaus klein 38x24cm | Zimmergewächshäuser | 6.9 € | 8 | 4260617972060 |
| 39929 | 17266 | Steckling Versandhülle, 65 St. | Stecklingszubehör | 34.9 € | 3 | 4260520752247 |
| 39930 | 15174 | Clonex Mist 750ml | Stecklingszubehör | 24.9 € | 15 | 5025644919339 |
| 39931 | 11382 | Stecketikett | Stecklingszubehör | 0.1 € | 3085 | – |
| 39932 | 11778 | Stecketiketten, 500 Stück | Stecklingszubehör | 29.9 € | 5 | – |
| 39933 | 14831 | ROOT!T Rooting Gel 150ml | Stecklingszubehör | 9.95 € | 129 | 5034517300648 |
| 39934 | 11408 | Pumpsprüher 2 Liter | Stecklingszubehör | 9.9 € | 9 | 5034517504701 |
| 39935 | 17087 | Hubey Bio Rooting Gel 70 ml | Stecklingszubehör | 19.95 € | 22 | 4024102311310 |
| 39936 | 17144 | ROOT!T LED-Leiste 26 Watt | Anzuchtbeleuchtung | 44.9 € | 0 | 5034517300099 |
| 39937 | 17145 | ROOT!T LED-Leiste 42 Watt | Anzuchtbeleuchtung | 59.9 € | 41 | 5034517300112 |
| 39938 | 14972 | SANLight FLEX II 2-Stecker Verteilkabel für 150 / 240 / 320 Watt Netzteil | Anzuchtbeleuchtung | 9.9 € | 3 | 9120069231632 |
| 39939 | 14970 | SANLight FLEX II 2-Stecker Verteilkabel für 25 + 60 Watt Netzteil | Anzuchtbeleuchtung | 9.9 € | 32 | 9126900231618 |
| 39940 | 14973 | SANLight FLEX II 4-Stecker Verteilkabel für 150 / 240 / 320 Watt Netzteil | Anzuchtbeleuchtung | 15.9 € | 7 | 9126900231649 |
| 39941 | 14971 | SANLight FLEX II 4-Stecker Verteilkabel für 25 + 60 Watt Netzteil | Anzuchtbeleuchtung | 11.9 € | 30 | 9120069231625 |
| 39942 | 14976 | SANLight FLEX II Netzteil 150 Watt | Anzuchtbeleuchtung | 56.9 € | 1 | 9120069231571 |
| 39943 | 14977 | SANLight FLEX II Netzteil 240 Watt | Anzuchtbeleuchtung | 69.9 € | 1 | 9120069231588 |
| 39944 | 14974 | SANLight FLEX II Netzteil 25 Watt | Anzuchtbeleuchtung | 22.9 € | 4 | 9120069231526 |
| 39945 | 14978 | SANLight FLEX II Netzteil 320 Watt | Anzuchtbeleuchtung | 89.9 € | 1 | 9120069231595 |
| 39946 | 14975 | SANLight FLEX II Netzteil 60 Watt | Anzuchtbeleuchtung | 35.9 € | 21 | 9120069231533 |
| 39947 | 14969 | SANLight FLEX II T-Kabel | Anzuchtbeleuchtung | 5.9 € | 43 | 9120069231601 |
| 39948 | 14968 | SANLight FLEX II Verlängerungskabel 100 cm | Anzuchtbeleuchtung | 2.5 € | 33 | 9120069231663 |
| 39949 | 14967 | SANLight FLEX II Verlängerungskabel 60 cm | Anzuchtbeleuchtung | 1.9 € | 54 | 9120069231656 |
| 39950 | 14964 | SANLight FLEX II-10 Watt | Anzuchtbeleuchtung | 42.9 € | 7 | 9120069231489 |
| 39951 | 14965 | SANLight FLEX II-20 Watt | Anzuchtbeleuchtung | 64.9 € | 10 | 9120069231496 |
| 39952 | 11807 | Neoprendisk Ø5cm schwarz | Hydrokultur-Anzucht | 0.6 € | 1888 | 5060193562995 |
| 39953 | 11849 | Nutriculture X-Stream für 20 Pflanzen | Hydrokultur-Anzucht | 119.9 € | 3 | 5060193558318 |
| 39954 | 11848 | Nutriculture X-Stream für 40 Pflanzen | Hydrokultur-Anzucht | 129.9 € | 0 | 5060193558400 |
## Nächste Schritte

- Bilder ergänzen und Status auf `publish` setzen, sobald verfügbar.
- Marken-/Hersteller-Zuordnung ggf. manuell im WP-Admin nachpflegen (siehe Limitation oben).
- Lieferzeit-Feld ggf. manuell im WP-Admin setzen.
