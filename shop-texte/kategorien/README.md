# Kategorien: Beschreibungen, SEO und Kacheln

Stand: 2026-09-07

## Kachel-Generator

Die Kategoriekacheln werden **nicht** in Canva erstellt, sondern mit
`tools/category-tiles/tiles.py` (SVG -> HTML -> Headless-Chromium-Screenshot).
Design, Farben und Layout sind dort fest hinterlegt und duerfen nicht
veraendert werden: einheitliches Design, nur Text und Symbol wechseln.

Rendern siehe `tools/category-tiles/README.md`. Ergebnisse liegen als PNG in
`category-images/`, benannt nach `<term_id>_<slug>.png` (hanfjack.de) bzw.
`com<term_id>_<slug>.png` (nur auf hanfjack.com vorhandene Kategorien).

Upload in WordPress ueber `wp_upload_media_from_url` mit der Raw-URL aus
diesem Repository, danach `thumbnail_id` als Term-Meta setzen.

## hanfjack.de

- 14 neue Kategorien (IDs 16435-16527) mit Beschreibung, Yoast-Titel,
  Meta-Description und Kachel versorgt.
- 8 Nachzuegler ohne Kachel nachtraeglich versorgt
  (15521, 15523, 15550, 15557-15560, 16087; Medien 42501-42508).
- 2 Kacheln korrigiert: `539_luefter-und-filter`, `5423_feuerzeuge-und-zippo`.

## hanfjack.com

32 Kategorien hatten Luecken (Beschreibung, Kachel oder SEO). Alle 32 sind
jetzt vollstaendig:

- 21 Kacheln neu hochgeladen (Medien 38453-38473) und als `thumbnail_id`
  gesetzt.
- 31 Beschreibungen gesetzt: 20 wortgleich von der .de-Kategorie
  uebernommen (Abgleich ueber den normalisierten Namen, nicht ueber den
  Slug - die Slugs weichen zwischen den Shops ab), 11 neu geschrieben
  (Merch-Linie und Pflege & Reinigung).
- 32 x `_yoast_wpseo_title` und 32 x `_yoast_wpseo_metadesc` als Term-Meta
  gesetzt (Yoast liest auf dieser Installation Taxonomie-SEO aus Term-Meta).

Betroffene Term-IDs auf .com:
160, 4734, 5898-5908, 6033, 6147, 6198, 6205, 6724, 6727, 9610, 9687,
11771-11773, 13150, 13163, 13181, 13184, 13192, 13197, 13228, 13687.

## below_category_content

Der Textblock unter dem Kategorie-Archiv (`below_category_content` als
Term-Meta) ist jetzt auf beiden Shops nachgezogen (63 Bloecke). Stil wie bei den
bestehenden Kategorien: ein bis zwei `<h2>`-Abschnitte mit `<p>` oder `<ul>`,
sachlich, ohne Werbesprache und ohne Emoji.

- **hanfjack.de (17):** die 14 neuen Kategorien 16435-16458 und 16527, 13416
  (Pumpen), dessen Block leer war, sowie 15 und 6881 (siehe Altbestand unten).
- **hanfjack.com (46):** die 14 neuen Kategorien (14011-14085) und die
  32 Luecken-Kategorien.

Bei den .com-Kategorien wurde der .de-Text 1:1 uebernommen, wo er bereits im
aktuellen Stil vorlag (18 Faelle). Der Luftfilter-Block nennt auf .de "siehe
Unterkategorie Ersatzfilter"; auf .com ist Ersatzfilter keine Unterkategorie,
der Satz wurde entsprechend angepasst. Die 12 reinen .com-Kategorien
(Merch-Linie und Pflege & Reinigung) haben neu geschriebene Bloecke.

### Altbestand umgestellt

Zwei .de-Kategorien trugen noch alten Marketing-Text mit Emoji und
`data-start`-Attributen aus einem Copy-Paste. Beide sind jetzt auf den
aktuellen Stil umgestellt, auf .de und .com wortgleich:

- **Hanfprodukte** (.de 15 / .com 6033). Der alte Text beschrieb Hanfmode und
  Hanftextilien - in der Kategorie liegen aber zwei Hizen-Vaporizer und
  ein Samen-Adventskalender. Der Text war also nicht nur stilistisch alt,
  sondern inhaltlich falsch. Neu: ein Abschnitt zum Rohstoff Nutzhanf,
  passend zur Kategoriebeschreibung.
- **CBD Vapes** (.de 6881 / .com 6205). Neu: Bauarten (Disposable Pens vs.
  Cartridges) und ein Tipp zur Wahl zwischen beiden. Die Produktdetails
  (Freigeist, 4 % CBD, Aromen) stehen unveraendert in der
  Kategoriebeschreibung ueber den Produkten.

Alle gesetzten Texte liegen zum Nachvollziehen in
`shop-texte/kategorien/below-category-content.json`.

## Offen

- Die Kategoriebeschreibung von `CBD Vapes` auf .de (Term 6881) ist noch im
  alten Marketing-Stil. Nicht angefasst, weil sie inhaltlich korrekt ist.
- Ob weitere aeltere Kategorien alten Stil tragen, ist nicht flaechendeckend
  geprueft - dafuer waere ein Sweep ueber alle 110 .de-Kategorien noetig.
