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

## Offen

- `below_category_content` ("Was du hier findest"-Block unter dem Archiv)
  fehlt bei den neuen Kategorien auf beiden Shops. 86 aeltere .de-Kategorien
  haben ihn. Noch nicht freigegeben.
