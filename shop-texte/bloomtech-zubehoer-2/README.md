# Bloomtech-Import: Zubehör (Teil 2)

Quelle: Google-Sheet des Lieferanten Bloomtech ("zubehoerhanfjack", 60 Zeilen, CSV-Export).
Auftrag: weitere neue Produkte inkl. Lagerbestand anlegen, ohne Bilder (analog zum ersten Bloomtech-Import "Anzucht & Zubehör" → `shop-texte/bloomtech-anzucht-zubehoer/README.md`, alle als `status: draft`).

## Ergebnis

- **57 Produkte neu angelegt** (WooCommerce-IDs 39955–40011, fortlaufend, `status: draft`).
- **3 Zeilen übersprungen** – Duplikate zu bereits bestehenden Grove-Bags-TerpLoc-Produkten (gleiche Füllmenge, praktisch identische Maße). Konsistent mit der vom Nutzer im ersten Bloomtech-Import getroffenen Entscheidung "Duplikate überspringen" behandelt.
- Alle 57 Produkte tragen: Name, Slug, SKU = Bloomtech-Artikelnummer (kompatibel mit dem bestehenden `bloomtech-stock-sync`-Plugin), Preis (inkl. Aktionspreis bei der Birchmeier Rückenspritze), bereinigte Lang-/Kurzbeschreibung (H1 und Hanfjack-CTA-Absatz entfernt), Kategorie, `manage_stock=true`, Lagerbestand + Lagerstatus, `min_age=18`, GTIN/EAN (soweit im Sheet vorhanden), sowie vollständige Yoast-SEO-Felder (SEO-Titel, Meta-Description, Fokus-Keyword).

## Übersprungene Duplikate (3)

| Bloomtech-Artikelnr. | Produkt (Sheet) | Bereits vorhanden als |
|---|---|---|
| 15195 | Grove Bags - TerpLoc 15g 20x13cm mit Fenster | ID 11937, SKU HJ-6743863 "Grove Bags TerpLoc 15g 21x13cm mit Fenster" |
| 15196 | Grove Bags - TerpLoc 30g 23x16cm mit Fenster | ID 11939, SKU HJ-3548887 "Grove Bags TerpLoc 30g 23x16cm mit Fenster" |
| 15244 | Grove Bags - TerpLoc 7g 17x10cm mit Fenster | ID 11935, SKU HJ-5471922 "Grove Bags TerpLoc 7g 17x10cm mit Fenster" |

## Kategorien

| ID | Name | Produkte | Status |
|---|---|---|---|
| 16451 | Veredeln & Extraktion | 7 | neu, Kind von Growshop (538) |
| 16452 | Curing & Lagerung | 8 | neu, Kind von Growshop (538) |
| 7042 | Waagen | 5 | bereits vorhanden (Kind von Headshop, 55) – wiederverwendet |
| 16453 | Bücher | 12 | neu, Top-Level-Kategorie |
| 16454 | Geruchsneutralisation | 12 | neu, Kind von Growshop (538) |
| 16455 | Pflanzzubehör | 13 | neu, Kind von Growshop (538) |

Summe: 7+8+5+12+12+13 = 57.

`Bücher` wurde bewusst als eigene Top-Level-Kategorie angelegt statt unter Growshop, da es sich um Fachliteratur statt Anbauzubehör handelt.

## Marken/Hersteller und weitere bekannte Grenzen

Wie im ersten Bloomtech-Import (`shop-texte/bloomtech-anzucht-zubehoer/README.md`) dokumentiert, sind Marken (`pwb-brand`) und Hersteller (`product_manufacturer`) über die verfügbare REST/MCP-Schnittstelle nicht auf Produkte schreibbar. Für diesen zweiten Import wurde deshalb bewusst **kein** neuer Versuch unternommen, Marken-/Hersteller-Terms anzulegen und zu verknüpfen – das ist eine bestätigte Plattformgrenze, keine Lücke dieses Imports. Ebenso bleibt `delivery_time` unbeschrieben (unverändertes, bereits dokumentiertes Problem).

`min_age` und `gtin` wurden wie beim ersten Import über die Top-Level-Felder in einem abschließenden, konsolidierenden `wp_wc_batch_update_products`-Update-Aufruf pro Produkt gesetzt (Readback an Produkt 39999 bestätigt: `min_age: "18"`, `gtin` korrekt übernommen).

## Vollständige Produktliste (57)

| ID | SKU | Produkt | Kategorie | VK brutto | Lagerbestand | GTIN/EAN |
|---|---|---|---|---|---|---|
| 39955 | 14017 | Ersatztrommel 100 mesh (grob) für Heisenberg Pollenmaschine 150 G | Veredeln & Extraktion | 65.9 € | 3 | 4056159078700 |
| 39956 | 13673 | Ersatztrommel 150 mesh (standard) für Heisenberg Pollenmaschine 150 G | Veredeln & Extraktion | 65.9 € | 0 | 4056159065427 |
| 39957 | 14094 | Ersatztrommel 200 mesh (fein) für Heisenberg Pollenmaschine 150 G | Veredeln & Extraktion | 65.9 € | 0 | 4056159078717 |
| 39958 | 12242 | Heisenberg Pollenmaschine 150 G | Veredeln & Extraktion | 159.9 € | 39 | 4260617970912 |
| 39959 | 10261 | Pollenshaker | Veredeln & Extraktion | 34.9 € | 18 | 4260617971711 |
| 39960 | 16931 | Pollenshaker XL | Veredeln & Extraktion | 44.9 € | 11 | 3870000391944 |
| 39961 | 16932 | Extraktorbeutel 20 Liter 5 Siebe | Veredeln & Extraktion | 79.9 € | 6 | 3870000159018 |
| 39962 | 13951 | Boveda Hygro-Pack 58% 320g | Curing & Lagerung | 29.9 € | 4 | 852715006262 |
| 39963 | 13470 | Boveda Hygro-Pack 62% 320g | Curing & Lagerung | 29.9 € | 1 | 852715006187 |
| 39964 | 15246 | Grove Bags - TerpLoc 1000g 51x46cm OHNE Fenster | Curing & Lagerung | 14.9 € | 0 | 4260617973760 |
| 39965 | 15245 | Grove Bags - TerpLoc 100g 32x24cm mit Fenster | Curing & Lagerung | 4.9 € | 169 | 4260617973753 |
| 39966 | 15197 | Grove Bags - TerpLoc 250g 38x30cm mit Fenster | Curing & Lagerung | 5.9 € | 93 | 4260617973715 |
| 39967 | 15243 | Grove Bags - TerpLoc 3,5g 13x10cm mit Fenster | Curing & Lagerung | 0.9 € | 465 | 4260617973739 |
| 39968 | 15198 | Grove Bags - TerpLoc 500g 47x35cm mit Fenster | Curing & Lagerung | 12.9 € | 34 | 4260617973722 |
| 39969 | 16267 | Grove Bags - TerpLoc SafeVac 2 Stck. 4,8m x 28cm | Curing & Lagerung | 59.9 € | 5 | 4260617974866 |
| 39970 | 15711 | Nohlex Babywaage NXB-168 | Waagen | 49.9 € | 1 | 4260360060267 |
| 39971 | 15709 | Nohlex Paketwaage NSF890 | Waagen | 49.9 € | 9 | 4260360060175 |
| 39972 | 15710 | Nohlex Paketwaage NX200 | Waagen | 66.9 € | 5 | 4260360060243 |
| 39973 | 15705 | Nohlex Tischwaage NTP2K | Waagen | 29.9 € | 4 | 4260360060236 |
| 39974 | 15706 | Nohlex Tischwaage NTP500X | Waagen | 32.9 € | 4 | 4260360061240 |
| 39975 | 13757 | Hydroponik leicht gemacht | Bücher | 38.9 € | 2 | – |
| 39976 | 16167 | Cannabis Extraktion | Bücher | 24.8 € | 0 | – |
| 39977 | 16131 | Cannabis Innen Anbau 3.0 | Bücher | 28.0 € | 39 | 9788090714397 |
| 39978 | 16003 | Cannabis-Anbau mit LED | Bücher | 19.8 € | 24 | – |
| 39979 | 13074 | Deine eigenen Stecklinge | Bücher | 12.8 € | 7 | 9783037881644 |
| 39980 | 16166 | Die Behandlung mit Cannabis | Bücher | 25.8 € | 10 | – |
| 39981 | 16168 | Enzyklopädie der Cannabiszucht | Bücher | 45.0 € | 14 | – |
| 39982 | 13072 | Marihuana Anbaugrundlagen | Bücher | 21.95 € | 48 | – |
| 39983 | 13071 | Marihuana drinnen | Bücher | 34.0 € | 3 | – |
| 39984 | 12396 | Marijuana Growers Handbuch | Bücher | 52.5 € | 6 | – |
| 39985 | 15702 | Outdoor -Anbau | Bücher | 18.0 € | 0 | – |
| 39986 | 16165 | Therapeutisches Cannabis | Bücher | 16.0 € | 6 | – |
| 39987 | 15309 | Biodor Control CNB Gel 200g Neutral | Geruchsneutralisation | 39.9 € | 0 | 8437023254141 |
| 39988 | 15308 | Biodor Control CNB Gel 33g Eukalyptus | Geruchsneutralisation | 14.99 € | 6 | 8437023254165 |
| 39989 | 15307 | Biodor Control CNB Gel 33g Neutral | Geruchsneutralisation | 14.99 € | 0 | 8437023254134 |
| 39990 | 12116 | Ona Block Fresh Linen 170g | Geruchsneutralisation | 12.9 € | 27 | 624493941321 |
| 39991 | 12105 | Ona Block Pro 170g | Geruchsneutralisation | 12.9 € | 30 | 624493941345 |
| 39992 | 12131 | Ona Gel Fresh Linen 400g | Geruchsneutralisation | 13.9 € | 5 | 624493920623 |
| 39993 | 12106 | Ona Gel Fresh Linen 732g | Geruchsneutralisation | 19.9 € | 5 | 624493922023 |
| 39994 | 12161 | Ona Gel Pro 400g | Geruchsneutralisation | 13.9 € | 4 | 624493920647 |
| 39995 | 12152 | Ona Gel Pro 732g | Geruchsneutralisation | 19.9 € | 4 | 624493922047 |
| 39996 | 12365 | Ona Liquid Fresh Linen 922ml | Geruchsneutralisation | 29.9 € | 3 | 624493912024 |
| 39997 | 12108 | Ona Spray Fresh Linen 250ml | Geruchsneutralisation | 9.9 € | 0 | 624493911423 |
| 39998 | 12113 | Ona Spray Pro 250ml | Geruchsneutralisation | 9.9 € | 8 | 624493911447 |
| 39999 | 16930 | Birchmeier Rückenspritze PR3 Profi-Star 5 Liter | Pflanzzubehör | ~~199.9~~ 119.0 € | 2 | 7611034013916 |
| 40000 | 14182 | Stahl-Bindedraht 1,8mm 50m Rolle | Pflanzzubehör | 2.9 € | 23 | 4260617970127 |
| 40001 | 13031 | Bambusstock Ø5-10mm 120cm | Pflanzzubehör | 0.5 € | 52 | 4260617970110 |
| 40002 | 16929 | Plant Bends, 50 St je Pckg | Pflanzzubehör | 7.9 € | 22 | 5034517150410 |
| 40003 | 11084 | Bambusstock Ø7mm 80cm | Pflanzzubehör | 0.45 € | 2540 | 4260617971124 |
| 40004 | 13402 | Stabklammer aus Kunststoff | Pflanzzubehör | 0.1 € | 1177 | 4260617971858 |
| 40005 | 16972 | Stütznetz 1 x 1000 Meter | Pflanzzubehör | 199.9 € | 1 | 4260617972428 |
| 40006 | 17293 | Stütznetz 1,36 x 100 Meter | Pflanzzubehör | 39.9 € | 10 | 8002929008763 |
| 40007 | 16970 | Stütznetz 1,7 x 500 Meter | Pflanzzubehör | 199.0 € | 3 | 8413246650373 |
| 40008 | 16860 | Stütznetz 2 x 10 Meter | Pflanzzubehör | 13.9 € | 62 | 8435405310232 |
| 40009 | 12901 | Stütznetz 2 x 5 Meter | Pflanzzubehör | 8.9 € | 53 | 8711338304303 |
| 40010 | 16928 | PLANT!T YoYo, 8 St. | Pflanzzubehör | 8.9 € | 0 | 5034517150397 |
| 40011 | 16079 | Pflanzenbindedraht 5mm 5m, weich ummantelt | Pflanzzubehör | 7.9 € | 21 | 8435405307775 |
## Nächste Schritte

- Bilder ergänzen und Status auf `publish` setzen, sobald verfügbar.
- Marken-/Hersteller-Zuordnung ggf. manuell im WP-Admin nachpflegen.
- Lieferzeit-Feld ggf. manuell im WP-Admin setzen.
