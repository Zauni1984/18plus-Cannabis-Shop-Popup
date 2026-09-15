# Grok-Texte ersetzen – 847 Produkte und 241 Marken auf hanfjack.de

Stand: 15.09.2026 · **847 Produkte** vollständig neu getextet (Beschreibung, Kurzbeschreibung, Yoast-Titel, Meta-Description, Focus-Keyword) und **auf hanfjack.de eingespielt**. Dazu **alle 241 Marken** der Taxonomie `pwb-brand` neu beschrieben und deren Yoast-Felder erneuert.

Die Texte liegen in diesem Ordner:

- `hanfjack-de-847-produkttexte.json` – Volltexte als HTML, Schlüssel ist die WooCommerce-Produkt-ID (Upload-Quelle)
- `hanfjack-de-847-produkttexte.csv` – dieselben Daten als Tabelle zur Durchsicht
- `hanfjack-de-markentexte.json` – die 217 Markenbeschreibungen samt Yoast-Feldern
- `hanfjack-de-markentexte.csv` – dieselben Markendaten als Tabelle

## Upload

Am 15.09.2026 über die WooCommerce-REST-API (`POST /wc/v3/products/batch`, 25 Produkte je Anfrage)
eingespielt. Geschrieben wurden je Produkt `description`, `short_description` sowie die drei
Yoast-Postmeta-Felder `_yoast_wpseo_title`, `_yoast_wpseo_metadesc` und `_yoast_wpseo_focuskw`.
Preise, Bestand, Attribute, Kategorien und Varianten wurden nicht angefasst.

Kontrolle nach dem Upload gegen den Live-Shop (Store-API-Vollscan über 3919 sichtbare Produkte
plus REST-Abgleich aller 847):

| Prüfung | Ergebnis |
| --- | --- |
| Produkte im Shop auffindbar | 847 von 847 |
| `dir="auto"` / `data-start` in Produkttexten | 0 |
| Beschreibung und Kurzbeschreibung stimmen mit der Quelle überein | 847 von 847 |
| Yoast-Titel, Meta-Description und Focus-Keyword gesetzt und korrekt | 847 von 847 |
| Produkte mit gesetztem `min_age` | 0 – Dünger bleiben ohne Altersfreigabe |

Fünf Produkte (510, 1878, 1879, 22211, 22243) unterscheiden sich im Zeichenvergleich nur durch
WordPress' automatische Typografie (`"` → `"`, `'` → `'`). Das ist gewollt und inhaltlich identisch.

## Markenbeschreibungen (241 Marken)

Der Grok-Marker steckte nicht nur in Produkttexten, sondern auch in der Taxonomie `pwb-brand`:
101 der 241 Marken trugen `dir="auto"` in ihrer Beschreibung. Diese Texte erscheinen im Tab
„Marke“ auf jeder Produktseite der Marke und auf der Marken-Archivseite – bei Royal Queen Seeds
etwa auf 178 Produktseiten, bei Barneys Farm auf 124.

Am 15.09.2026 abgearbeitet:

- **217 Marken mit Produkten** haben eine neue, zweiabsätzige Beschreibung sowie neuen
  Yoast-Titel, neue Meta-Description und ein neues Focus-Keyword.
- Das **zweite Beschreibungsfeld** (`pwb_long_brand_desc`, Perfect WooCommerce Brands) wurde auf
  allen 102 betroffenen Marken geleert. Im Tab „Marke“ steht jetzt genau ein Textblock.
- **24 Marken ohne Produkte** (Karteileichen) wurden von Beschreibung und eigenen Yoast-Werten
  befreit, damit Yoast auf sein Template zurückfällt.

101 der zweiten Beschreibungen waren Grok-Texte. Eine weitere (Atami) war ein eigener, sauberer
Text ohne Grok-Marker – nach der Vorgabe „nur noch eine Markenbeschreibung“ ebenfalls entfernt.
Gefunden wurde sie erst durch einen Vollscan aller 217 Marken-Produktseiten, weil
`pwb_long_brand_desc` über die REST-API nicht lesbar ist.

Die Texte liegen als `hanfjack-de-markentexte.json` und `hanfjack-de-markentexte.csv` in diesem
Ordner.

### Technische Hinweise

- Markenbeschreibungen werden auf hanfjack.de per `wp_filter_kses` von HTML befreit. Die Texte
  sind deshalb reiner Text; eine **Leerzeile zwischen den Absätzen** erzeugt auf der Archivseite
  zwei `<p>`, ein einzelner Zeilenumbruch nur ein `<br>`.
- `pwb_long_brand_desc` ist nicht als REST-Meta registriert und musste einzeln je Term
  geschrieben werden.
- Beschreibung und Yoast-Felder liefen über `POST /wp/v2/pwb-brand/<id>` mit einem
  WordPress-Anwendungspasswort; der WooCommerce-Schlüssel reicht dafür nicht.

### Kontrolle nach dem Upload (alle 241 Marken über die REST-API gegengelesen)

| Prüfung | Ergebnis |
| --- | --- |
| `dir="auto"` in Markenbeschreibungen | 0 |
| HTML in Markenbeschreibungen | 0 |
| CTA im Beschreibungstext (gehört nur in Titel/Meta) | 0 |
| Absatztrennung durch Leerzeile vorhanden | 217 von 217 |
| Yoast-Titel ≤ 75 Zeichen | 217 von 217 |
| Meta-Description 100–160 Zeichen | 217 von 217 |
| Marken ohne Beschreibung | 24 – ausschließlich Marken mit 0 Produkten |
| Focus-Keyword gesetzt und in Titel und Text enthalten | 217 von 217 |
| Zweiter Textblock im Tab „Marke“ (Vollscan über 217 Produktseiten) | 0 |

Zwei Produktseiten zeigen zwei Textblöcke im Tab „Marke“, weil dem Produkt zwei Marken zugeordnet
sind: „Erntebundle Small Black“ (Meditrade und TRAFIKA) und „Stahl-Bindedraht 1,8 mm“ (Easy Grow
und Bloomtech). Das ist eine Zuordnungsfrage im Produkt, keine zweite Markenbeschreibung – bei
allen vier Marken ist `pwb_long_brand_desc` leer.

### Empfehlung

Die 24 produktlosen Marken sind Karteileichen und sollten gelöscht werden. Gelöscht wurde in
diesem Durchgang nichts:

Alltest, Atlas Seed, Biodor, Clipper, Dope Seeds, exotic-seeds, Ferna Trade, French Connection,
Grounded Genetics, Hermann Meyer KG, hortiOne, HY-PRO, Hydro Garden, In House Genetics, Jumi,
Knistermann, Medina Mood, Ona, Preferred Gardens, Prof, Rhino, SHEESH, Terra Exotica,
The Bulldog Seeds.

## Prüfungen der Texte (alle bestanden)

- kein `dir="auto"` und keine `data-start`/`data-end`-Reste mehr
- Meta-Description 100–160 Zeichen, Yoast-Titel ≤ 75 Zeichen
- kein CTA in Beschreibung oder Kurzbeschreibung (nur in Titel/Meta)
- Rechtshinweis zur Keimung bei allen Samenprodukten vorhanden
- keine Heilaussagen bei CBD-, Tier-, Kosmetik- und Lebensmittelprodukten
- kein „Lorem ipsum“ und keine übernommenen Platzhaltertexte

## Produkte je Marke

- (sonstige): 156
- Barneys Farm: 113
- Spider Farmer: 110
- Anesia Seeds: 44
- Zippo: 43
- GRAVEDA: 35
- Athena: 33
- Dutch Passion: 33
- G-Rollz: 33
- Palacio: 30
- Wizard Trees: 22
- RAW: 20
- SANlight: 19
- HOMEbox: 17
- Humboldt Seed: 16
- Royal Queen Seeds: 16
- Plagron: 14
- Integra Boost: 11
- Calitamex: 9
- Purize: 8
- Storz & Bickel: 8
- Smoking: 7
- Juicy Jay: 6
- Elements: 5
- Green House: 5
- King Palm: 5
- Ethos Genetics: 4
- Exotic Seed: 3
- Fast Buds: 3
- Grove Bags: 3
- OCB: 3
- Sensi Seeds: 3
- Black Leaf: 2
- DIPSE: 2
- Eazy Plug: 2
- Miron: 2
- PotKing: 2

## Quellen

- Produktdaten Shop: 258
- spider-farmer.com: 110
- barneysfarm.com: 98
- Produktdaten Shop und Herstellerangaben: 50
- anesiaseeds.com: 44
- zippo.de: 43
- graveda.de: 35
- athenaag.com: 33
- dutch-passion.com: 33
- sanlight.com: 19
- humboldtseedcompany.com: 16
- royalqueenseeds.de: 16
- plagron.com: 14
- Shop-Produktdaten (Barneys Farm fuehrt die Sorte nicht mehr): 11
- integra-products.com: 11
- calitamex.com: 9
- purize-filters.com und Produktdaten Shop: 8
- storz-bickel.com: 8
- smokingpaper.com: 7
- Produktdaten Shop und juicyjays.com: 6
- shop.greenhouseseeds.nl: 5
- Produktdaten Shop (ethosgenetics.com liefert keine statisch abrufbaren Sortendaten): 4
- Produktdaten Shop (2fast4buds.com ist nicht abrufbar): 3
- Produktdaten Shop und ocb.net: 3
- sensiseeds.com: 3

## Offene Befunde aus der Recherche

- **SANlight EVO 6-120**: Shop-Name sagt 265 W, Herstellerdatenblatt 400 W.
- **G-Rollz 22906 und 22912**: identischer Artikel (Colossal Dream Medium Tray, gleicher Preis) – Dublette.
- **Norddampf Relict**: alte Meta nannte 2300 mAh, Hersteller 2600 mAh.
- **Spider Farmer**: alte Texte verkauften 3,14 µmol/J als Leuchten-Effizienz; laut Datenblatt ist das ein Einzeldioden-Spitzenwert (Leuchten: 2,5–2,85).
- **Spider Farmer SF-4000**: Shop-Titel nennt Samsung lm301H EVO, Datenblatt nennt Bridgelux.
- **Athena**: veröffentlicht keine NPK-Analysen; die PK-Produktseite enthält wörtliche „Lorem ipsum“-Platzhalter.
- **Calitamex**: Herstellerseite wirbt mit Heilaussagen („Bei Zahnschmerzen“, „entzündungshemmend“). Nicht übernommen.
- **Smoking Kukuxumusu**: Herstellerseite nennt im Fließtext 108 × 37 mm, im Datenblatt 108 × 44 mm. Datenblattwert verwendet.
- **King Palm Slim Rolls**: Fassungsvermögen 1,5 g stammt aus den Shop-Daten, beim Hersteller nicht gegengeprüft (Produktseiten nicht abrufbar).
- **Fast Buds, Ethos Genetics**: Herstellerseiten nicht abrufbar (Cloudflare bzw. JS-Rendering) – Texte aus den Shop-Produktdaten.

