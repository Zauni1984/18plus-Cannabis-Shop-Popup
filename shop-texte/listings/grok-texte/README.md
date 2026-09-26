# Grok-Texte ersetzen – 847 Produkte und alle Marken auf hanfjack.de

Stand: 15.09.2026 · **847 Produkte** vollständig neu getextet (Beschreibung, Kurzbeschreibung, Yoast-Titel, Meta-Description, Focus-Keyword) und **auf hanfjack.de eingespielt**. Dazu **alle Marken** der Taxonomie `pwb-brand` neu beschrieben und deren Yoast-Felder erneuert; aus 241 Marken sind nach dem Aufräumen **233** geworden.

Die Texte liegen in diesem Ordner:

- `hanfjack-de-847-produkttexte.json` – Volltexte als HTML, Schlüssel ist die WooCommerce-Produkt-ID (Upload-Quelle)
- `hanfjack-de-847-produkttexte.csv` – dieselben Daten als Tabelle zur Durchsicht
- `hanfjack-de-markentexte.json` – die 233 Markenbeschreibungen samt Yoast-Feldern
- `hanfjack-de-markentexte.csv` – dieselben Markendaten als Tabelle
- `hanfjack-de-geloeschte-marken.json` – Sicherung der 8 entfernten Marken-Terms

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

## Markenbeschreibungen (241 → 233 Marken)

Der Grok-Marker steckte nicht nur in Produkttexten, sondern auch in der Taxonomie `pwb-brand`:
101 der 241 Marken trugen `dir="auto"` in ihrer Beschreibung. Diese Texte erscheinen im Tab
„Marke“ auf jeder Produktseite der Marke und auf der Marken-Archivseite – bei Royal Queen Seeds
etwa auf 178 Produktseiten, bei Barneys Farm auf 124.

Am 15.09.2026 abgearbeitet:

- **233 Marken** haben eine neue, zweiabsätzige Beschreibung sowie neuen Yoast-Titel, neue
  Meta-Description und ein neues Focus-Keyword. Keine Marke ist mehr ohne Text.
- Das **zweite Beschreibungsfeld** (`pwb_long_brand_desc`, Perfect WooCommerce Brands) wurde auf
  allen 102 betroffenen Marken geleert. Im Tab „Marke“ steht jetzt genau ein Textblock.
- **7 Marken ohne jedes Produkt** wurden gelöscht (siehe unten).
- Die Dublette **HY-PRO / Hy-Pro Fertilizers** wurde zusammengeführt.

24 Marken zeigten in der Taxonomie den Zähler 0. Ein Abgleich über alle Produktstatus hat
gezeigt, dass davon **17 weiterhin private Produkte oder Entwürfe** tragen – die bleiben stehen
und haben ebenfalls neue Texte bekommen. Nur die verbleibenden **7 ohne jedes Produkt** in
irgendeinem Status wurden entfernt.

101 der zweiten Beschreibungen waren Grok-Texte. Eine weitere (Atami) war ein eigener, sauberer
Text ohne Grok-Marker – nach der Vorgabe „nur noch eine Markenbeschreibung“ ebenfalls entfernt.
Gefunden wurde sie erst durch einen Vollscan aller 217 Marken-Produktseiten, weil
`pwb_long_brand_desc` über die REST-API nicht lesbar ist.

Die Texte liegen als `hanfjack-de-markentexte.json` und `hanfjack-de-markentexte.csv` in diesem
Ordner, die gelöschten Terms als `hanfjack-de-geloeschte-marken.json`.

### Technische Hinweise

- Markenbeschreibungen werden auf hanfjack.de per `wp_filter_kses` von HTML befreit. Die Texte
  sind deshalb reiner Text; eine **Leerzeile zwischen den Absätzen** erzeugt auf der Archivseite
  zwei `<p>`, ein einzelner Zeilenumbruch nur ein `<br>`.
- `pwb_long_brand_desc` ist nicht als REST-Meta registriert und musste einzeln je Term
  geschrieben werden.
- Beschreibung und Yoast-Felder liefen über `POST /wp/v2/pwb-brand/<id>` mit einem
  WordPress-Anwendungspasswort; der WooCommerce-Schlüssel reicht dafür nicht.

### Kontrolle nach dem Upload (alle 233 Marken über die REST-API gegengelesen)

| Prüfung | Ergebnis |
| --- | --- |
| `dir="auto"` in Markenbeschreibungen | 0 |
| HTML in Markenbeschreibungen | 0 |
| CTA im Beschreibungstext (gehört nur in Titel/Meta) | 0 |
| Absatztrennung durch Leerzeile vorhanden | 233 von 233 |
| Yoast-Titel ≤ 75 Zeichen | 233 von 233 |
| Meta-Description 100–160 Zeichen | 233 von 233 |
| Marken ohne Beschreibung | 0 |
| Focus-Keyword gesetzt und in Titel und Text enthalten | 233 von 233 |
| Zweiter Textblock im Tab „Marke“ (Vollscan über 217 Produktseiten) | 0 |
| `pwb_long_brand_desc` bei den 17 Marken ohne öffentliche Produkte | leer |

Zwei Produktseiten zeigen zwei Textblöcke im Tab „Marke“, weil dem Produkt zwei Marken zugeordnet
sind: „Erntebundle Small Black“ (Meditrade und TRAFIKA) und „Stahl-Bindedraht 1,8 mm“ (Easy Grow
und Bloomtech). Das ist eine Zuordnungsfrage im Produkt, keine zweite Markenbeschreibung – bei
allen vier Marken ist `pwb_long_brand_desc` leer.

### Gelöschte Marken

Sieben Marken hatten in **keinem** Produktstatus (publish, draft, pending, private, future, trash)
ein einziges Produkt und wurden gelöscht:

Clipper (7649), exotic-seeds (14125), Ferna Trade (16440), Hermann Meyer KG (16444),
hortiOne (1884), In House Genetics (7560), Terra Exotica (16445).

Dazu kommt Hy-Pro Fertilizers (16340) als zusammengeführte Dublette – siehe unten.

ID, Name, Slug und Meta aller acht Terms liegen vor dem Löschen gesichert in
`hanfjack-de-geloeschte-marken.json`. Produkte, Medien und Kategorien wurden nicht angefasst;
die einzige Produktänderung ist die Markenzuordnung von „Hy-Pro Terra 20 Liter".

### Marken mit privaten Produkten – behalten und neu betextet

Diese 17 Marken standen in der Taxonomie mit 0 Produkten, tragen aber weiterhin private Artikel
oder Entwürfe. Sie sind erhalten geblieben und haben neue Beschreibungen und Yoast-Felder bekommen:

| Marke | private Produkte |
| --- | --- |
| SHEESH | 40 |
| The Bulldog Seeds | 14 |
| Dope Seeds | 10 |
| Ona | 9 |
| Preferred Gardens | 9 |
| French Connection | 8 |
| Medina Mood | 6 |
| Hy-Pro Fertilizers (vormals HY-PRO) | 5 |
| Jumi | 5 |
| Biodor | 3 |
| Grounded Genetics | 3 |
| Atlas Seed | 2 |
| Alltest | 1 |
| Knistermann | 1 |
| Prof | 1 |
| Rhino | 1 |
| Hydro Garden | 1 (Entwurf) |

### Zusammengeführte Dublette: HY-PRO / Hy-Pro Fertilizers

HY-PRO (2169) und Hy-Pro Fertilizers (16340) waren dieselbe Marke. Zusammengeführt wurde auf
**Term 2169**, weil dort das Markenlogo hängt und alle fünf Produkte der anderen Seite
`HJ-`-SKUs tragen und laut Vorgabe nicht angefasst werden dürfen. Umgehängt wurde deshalb nur
das eine Produkt ohne `HJ-`-SKU:

- „Hy-Pro Terra 20 Liter" (ID 39489, SKU 13002) von Term 16340 auf Term 2169
- Term 2169 umbenannt in **Hy-Pro Fertilizers**, Slug auf `hy-pro-fertilizers` gesetzt, damit die
  bereits von Google indexierte Archiv-URL erhalten bleibt
- Term 16340 gelöscht, nachdem er in keinem Produktstatus mehr etwas trug
- Beschreibung und Yoast-Felder von 2169 decken jetzt beide Sortimente ab

Die alte, produktlose URL `/marke/hy-pro/` liefert dadurch 404. Sie hatte kein einziges
öffentliches Produkt; ein Redirect auf `/marke/hy-pro-fertilizers/` wäre trotzdem sauber.

### Offene Befunde aus der Markenarbeit

- **Eazy Plug (16447) und eazyplug (6531)** sind dieselbe Marke und liegen weiterhin doppelt in
  der Taxonomie: 16447 trägt vier Produkte mit regulären SKUs, 6531 drei Produkte mit
  `HJ-`-SKUs. Nach demselben Muster wie bei Hy-Pro ließe sich auf 6531 zusammenführen, ohne ein
  `HJ-`-Produkt anzufassen. Nicht angefasst.
- **„Hy-Pro Terra 20 Liter" (39489) trägt `min_age` 18**, obwohl es ein Dünger ist. Das bricht
  den Google-Merchant-Center-Feed. Nicht angefasst, weil auf diesen Shop grundsätzlich kein
  `min_age` geschrieben wird.
- Produkt 39489 beschreibt sich selbst als Kanister, liegt aber in „Erde & Substrate" und trug
  in der alten Marken-Meta die Bezeichnung „20-Liter-Sack". Die Gebindeform gehört geprüft.

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

