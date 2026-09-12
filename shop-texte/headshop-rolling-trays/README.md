# Rolling Trays – Stil-Check (Kategorie-ID 1974)

Hausstil-Prüfung aller 67 Produkte der Kategorie **Rolling Trays**. Wie beim
Headshop-Mini-Projekt war dies **kein** Full-Rewrite: Die Mehrheit der Texte
war bereits sauber und wurde unverändert gelassen. Es ging darum,
tatsächliche Verstöße gegen die 6-Punkte-Checkliste (kein H1 im Text, keine
Hanfjack-Marken-CTA, durchgehende Du-Anrede, EU-Konformität, saubere Yoast-
SEO-Felder, saubere `short_description`) zu finden und gezielt zu fixen.

Auf Anweisung ausgeschlossen (nicht bearbeitet): jedes Produkt mit "HEMPER"
oder "Goody Glass" im Namen — 29 von 67 Produkten (21 HEMPER-Varianten,
8 Goody-Glass-Varianten). "Smoke Friends" kam in dieser Kategorie nicht vor.
Geprüft wurden die verbleibenden **38 Produkte**.

## Befund

Bei keinem der 38 Produkte fand sich ein H1-Tag im Fließtext, eine
Hanfjack-Marken-CTA im `post_content`/`short_description` oder eine
Sie-Anrede — die neueren RAW-, OCB-, Purize- und G-Rollz-Texte folgen
durchgehend dem sauberen `<p dir="auto">`/`<h3 dir="auto">`-Template ohne
`data-path-to-node`-Reste. Alle gefundenen Verstöße betrafen ausschließlich
die Yoast-`meta_description` — reine SEO-Feld-Korrekturen ohne Eingriff in
`post_content`. **15 von 38 Produkten** wurden gefixt, in drei Mustern:

1. **Hanfjack-Marken-CTA in der Metadesc** (6 Produkte, alle G-Rollz): Die
   Metadescs enthielten Sätze wie *"…Perfekt zum Drehen, Servieren oder als
   stylisches Deko-Highlight. Jetzt bei Hanfjack bestellen! 🌸💗"* — ein
   klarer Verstoß gegen Checkliste-Punkt 2, dazu mit Emojis und weit über dem
   Soll-Bereich (280–322 statt 120–160 Zeichen). Alle sechs betroffen: die
   komplette G-Rollz-Hello-Kitty-Serie mit dieser Struktur (Red Kimono, Best
   Hits, Harajuku, Cheerleader, Avocado) plus Smokey Shroom. Auffällig: die
   *anderen* Hello-Kitty-Produkte (Kimono Pink, Retro Tourist, Cupido,
   Doctor) und die übrigen G-Rollz-Kunstmotiv-Trays hatten bereits saubere,
   korrekt lange Metadescs ohne CTA — der Fehler trat nicht systematisch bei
   allen Produkten eines Templates auf, sondern unregelmäßig verteilt.
2. **Abgebrochene Sätze in der Metadesc** (7 Produkte, alle G-Rollz Banksy/
   Cheech & Chong/Colossal Dream/Moth Lick/Smokey Sisters/Mushroom Lover):
   Muster wie *"…mit ikonischem Panda-Graffiti-Motiv **auf. EAN:** …"* oder
   *"…mit **psychedelischem. EAN:** …"* — ein Adjektiv oder eine Präposition
   ohne folgendes Substantiv, offensichtlich beim ursprünglichen Erstellen
   der Metadesc abgeschnitten. Jeweils durch ein passendes Substantiv ergänzt
   bzw. das hängende Wort entfernt.
3. **Fehlende oder fehlerhafte Metadesc** (2 Produkte): 30312 (G-Rollz Reggae
   Medium Tray) hatte gar keine `_yoast_wpseo_metadesc` gesetzt; 14768
   (Purize Tray Kit Blau) ebenso — dort füllte nur die automatisch aus
   `short_description` generierte `og:description` die Lücke. Beide durch
   passende Metadescs (117–132 Zeichen) ergänzt.

Zusätzlich ein reiner Grammatikfehler (23435, Amsterdam Magnet Cover):
"stylisches Abdeckung" (falsches Genus) → "stylische Abdeckung" korrigiert.

## Vorgehen

- Da `wp_wc_get_product` bei dieser Produktgruppe durchgängig 50–55 KB große
  Antworten lieferte (dominiert von irrelevanten Plugin-Metafeldern:
  `_woosea_*`, `ocean_*`, `crawlwp_*`), wurden bei Bedarf die vom Harness
  automatisch gespeicherten JSON-Dateien der Tool-Ergebnisse per Python-Skript
  (statt vollständigem Re-Read) auf H1-Tags, Sie-Anrede, Hanfjack-Erwähnung
  und Yoast-Felder durchsucht, um Tokens zu sparen.
- Alle Fixes ausschließlich über `wp_yoast_update_post_seo`
  (`meta_description`) — kein Fix erforderte einen Eingriff in
  `post_content` oder `short_description`, da keine H1/CTA-Verstöße im
  Fließtext gefunden wurden. `wp_wc_update_product`/
  `wp_wc_batch_update_products` wurden nicht verwendet (Risiko, `_min_age`
  zu löschen).
- Metadesc-Längen wurden vor jedem Fix per Python (`len()`) geprüft und auf
  ca. 120–160 Zeichen gebracht; bei Kürzungen wurde EAN und Kerninhalt
  erhalten, Emojis und die Hanfjack-CTA-Phrase vollständig entfernt.
- Verifikation je Fix: `wp_yoast_get_post_seo` erneut gelesen und der
  bereinigte Wert bestätigt (siehe Liste unten, alle 15 Fixes bestätigt).

## Sonderfälle

- **Metadescs 100–119 Zeichen** (14773 RAW Black Cover Small: 107,
  14771 RAW Brazil Small: 104, 14770 RAW Summer Small: 113): knapp unter dem
  Soll-Bereich, aber in Übereinstimmung mit der Präzedenz aus dem
  Headshop-Mini-Projekt ("unter 100 Zeichen" als Schwelle für "zu kurz")
  **nicht** als Verstoß gewertet und unverändert gelassen.
- **Fehlender Punkt vor "EAN:"** in mehreren älteren Metadescs (z. B. 14769,
  14767, 27074, 23435 jeweils vor dem Fix): ein durchgängiges, katalogweites
  Stilmuster in vielen ansonsten korrekten Metadescs, kein Einzelfehler —
  nicht korrigiert, da außerhalb des engeren Scopes (kein Satzabbruch,
  reine Interpunktion).
- **Private-Status-Produkt** (22915, G-Rollz Mushroom Lover): normal geprüft
  wie alle anderen, hatte trotz `noindex`-Status denselben abgebrochenen-
  Satz-Fehler in der Metadesc wie die publizierten Geschwisterprodukte und
  wurde ebenfalls gefixt.
- **`data-start`/`data-end`-Reste in `short_description`** (u. a. 27074,
  23437, 22933, 22909): mehrere `short_description`-Felder enthalten
  `<p data-start="…" data-end="…">`-Wrapper um die Listeneinträge — Reste
  eines Copy-Paste-Vorgangs aus einem anderen Tool. Optisch identisch zur
  sauberen Bullet-Liste (keine sichtbaren Auswirkungen, kein H1/CTA-Bezug),
  daher außerhalb des engeren Scopes dieser Checkliste **nicht** korrigiert.
- **Leere `short_description`** (30312, 22891): kein Verstoß im engeren
  Sinn (Checkliste fordert nur, dass eine vorhandene Bullet-Liste sauber
  ist), daher unverändert gelassen.

## Vollständige Produktliste

### Ausgeschlossen (29/67 – HEMPER/Goody Glass, nicht bearbeitet)

- 37763, 37762, 37761, 37760 – Goody Glass Pattern Face Rolling Tray (4 Varianten)
- 37751, 37750, 37749, 37748 – Goody Glass Big Face Rolling Tray (4 Varianten)
- 37619, 37618, 37617 – HEMPER Luxe White/Gold Marble Rolling Tray (3 Größen)
- 37616, 37615, 37614 – HEMPER Luxe Black Marble Rolling Tray (3 Größen)
- 37605, 37604, 37603 – HEMPER It's Money Rolling Tray (3 Größen)
- 37602, 37601, 37600 – HEMPER It's Lit Light Green Camo Rolling Tray (3 Größen)
- 37599, 37598, 37597 – HEMPER It's Lit Black Camouflage Rolling Tray (3 Größen)
- 37595, 37594, 37593 – HEMPER Gaming Rolling Tray (3 Größen)
- 37592, 37591, 37590 – HEMPER Camouflage Rolling Tray (3 Größen)

### Geprüft (38/38)

- [x] 30312 – G-Rollz Pets Rock Reggae Medium Tray — FIXED (Yoast metadesc fehlte komplett → ergänzt, 132 Zeichen)
- [x] 28677 – RAW Emerald Green 20th Anniversary Rolling Tray — OK
- [x] 28675 – RAW Murdered Rolling Tray Medium Rot — OK
- [x] 28672 – RAW Murdered Rolling Tray Medium Schwarz — OK
- [x] 28670 – RAW Metal Rolling Tray Metallic Medium — OK
- [x] 28668 – OCB Rolling Tray Premium Schwarz — OK
- [x] 27074 – RAW Wooden Rolling Tray Spout — OK
- [x] 23435 – Amsterdam Magnet Rolling Tray Cover Stoned — FIXED (Genus-Fehler "stylisches"→"stylische Abdeckung")
- [x] 23437 – G-Rollz Reggae Magnet Cover für Medium Trays — OK
- [x] 22933 – G-Rollz Banksy's Graffiti Livin' the Dream Medium Tray — OK
- [x] 22930 – G-Rollz Banksy's Graffiti Panda Gunnin Medium Tray — FIXED (abgebrochener Satz "…Motiv auf. EAN:" → "…Motiv. EAN:")
- [x] 22927 – G-Rollz Banksy's Graffiti Thug for Life Medium Tray — OK
- [x] 22924 – G-Rollz Cheech & Chong Trippy Medium Tray — OK
- [x] 22921 – G-Rollz Cheech & Chong Greatest Hits Medium Tray — FIXED (abgebrochener Satz "…Design im. EAN:" → "…Retro-Design. EAN:")
- [x] 22918 – G-Rollz Cheech & Chong Sofa Medium Tray — OK
- [x] 22915 – G-Rollz Mushroom Lover Medium Tray (Status: private) — FIXED (abgebrochener Satz "…psychedelischem. EAN:" → "…psychedelischem Kunstmotiv. EAN:")
- [x] 22912 – G-Rollz Colossal Dream Medium Tray (helle Variante) — OK
- [x] 22909 – G-Rollz Smokey Sisters Medium Tray — FIXED (abgebrochener Satz "…psychedelischem. EAN:" → "…psychedelischem Smokey-Sisters-Design. EAN:")
- [x] 22906 – G-Rollz Colossal Dream Medium Tray (dunkle Variante) — FIXED (abgebrochener Satz "…mit intensivem. EAN:" → "…intensivem Colossal-Dream-Design. EAN:")
- [x] 22903 – G-Rollz Moth Lick Medium Tray — FIXED (abgebrochener Satz "…psychedelischem. EAN:" → "…psychedelischem Moth-Lick-Design. EAN:")
- [x] 22900 – G-Rollz Smokey Shroom Medium Tray — FIXED (Hanfjack-CTA + Emojis, 301→145 Zeichen)
- [x] 22897 – G-Rollz Hello Kitty(TM) Red Kimono Medium Küchentablett — FIXED (Hanfjack-CTA + Emojis, 307→147 Zeichen)
- [x] 22894 – G-Rollz Hello Kitty(TM) Best Hits Medium Küchentablett — FIXED (Hanfjack-CTA + Emojis, 280→134 Zeichen)
- [x] 22891 – G-Rollz Hello Kitty(TM) Kimono Pink Small Küchentablett — OK
- [x] 22888 – G-Rollz Hello Kitty(TM) Retro Tourist Small Küchentablett — OK
- [x] 22885 – G-Rollz Hello Kitty(TM) Cupido Small Küchentablett — OK
- [x] 22882 – G-Rollz Hello Kitty(TM) Harajuku Small Küchentablett — FIXED (Hanfjack-CTA + Emojis, 314→130 Zeichen)
- [x] 22879 – G-Rollz Hello Kitty(TM) Doctor Small Küchentablett — OK
- [x] 22876 – G-Rollz Hello Kitty(TM) Cheerleader Medium Küchentablett — FIXED (Hanfjack-CTA + Emojis, 322→141 Zeichen)
- [x] 22873 – G-Rollz Hello Kitty(TM) Avocado Medium Küchentablett — FIXED (Hanfjack-CTA + Emojis, 292→133 Zeichen)
- [x] 14776 – OCB Rolling Box inkl. Tray — OK
- [x] 14774 – RAW Classic Beige Rolling Tray Cover Small — OK
- [x] 14773 – RAW Black Rolling Tray Cover Small — OK
- [x] 14771 – RAW Brazil Rolling Tray Small — OK
- [x] 14770 – RAW Summer Rolling Tray Small — OK
- [x] 14769 – Purize Tray Kit Grün Fiber Composite — OK
- [x] 14768 – Purize Tray Kit Blau Fiber Composite — FIXED (Yoast metadesc fehlte komplett → ergänzt, 117 Zeichen)
- [x] 14767 – Purize Tray Kit Rot Fiber Composite — OK

## Abschluss

Alle 67 Produkte der Kategorie Rolling Trays wurden erfasst; 29 (HEMPER/
Goody Glass) wurden auf Anweisung übersprungen. Von den verbleibenden 38
Produkten hatten **15 (≈ 39 %)** tatsächliche Yoast-Hygiene-Verstöße und
wurden gezielt gefixt: 6× Hanfjack-Marken-CTA in der Metadesc (mit Emojis
und massiver Überlänge), 7× abgebrochener Satz in der Metadesc, 2× komplett
fehlende Metadesc. 1 weiterer Fix betraf einen reinen Genus-Fehler
("stylisches" → "stylische"). **23 Produkte** waren bereits konform und
wurden nicht verändert. Kein Produkt dieser Kategorie hatte ein H1-Tag im
Fließtext, eine Hanfjack-CTA im `post_content`/`short_description` oder eine
Sie-Anrede — anders als im Headshop-Mini-Projekt trat die "alte,
vor-refaktorierte Content-Struktur" (H1 + Content-CTA) hier nicht auf; die
gefundenen Verstöße lagen ausschließlich in den Yoast-`meta_description`-
Feldern. `_min_age` und alle sonstigen Post-Meta-Felder blieben unangetastet,
da ausschließlich `wp_yoast_update_post_seo` verwendet wurde.
