# Pflegeprodukte – Stil-Fixes (Kategorie 56, Marke Palacio)

Mechanische Nachbesserung der 29 Produkte in Kategorie "Pflegeprodukte"
(Massagegele, Balsame, Seifen, Zahnpasta, Körperlotion, Haarpflege, 2 Bundles).
Anders als beim Aktivkohlefilter- und Kategorien-Projekt ging es hier **nicht**
um komplette Neufassung der Texte — die Inhalte waren bereits gut, EU-konform
(Kosmetikverordnung (EG) Nr. 1223/2009, keine Heilaussagen, CBD als
quantifizierter Inhaltsstoff ohne Wirkversprechen) und mussten unverändert
bleiben. Es ging um zwei systematische, projektweite Stil-Verstöße.

## Befund

Jedes der 29 Produkte hatte in `description` (post_content):

1. Ein öffnendes `<h1 data-path-to-node="10">…</h1>`-Tag am Anfang des Fließtexts.
   Der Produktname liefert bereits das Seiten-H1 — ein zweites H1 im Text ist
   die gleiche Stilregel, die schon beim Aktivkohlefilter-Projekt (93 Produkte)
   durchgesetzt wurde.
2. Einen abschließenden fett gedruckten Hanfjack-CTA-Absatz (z. B. "Bestelle
   … jetzt bei Hanfjack.", "Hol dir … bei Hanfjack", "Gönn deiner Haut …
   jetzt bei Hanfjack"), meist direkt nach einem `<hr>`. Standing Rule des
   Gesamtprojekts: keine Hanfjack-Marken-CTA-Phrasen in Produkttexten.

Zusätzlich enthielt bei jedem Produkt die Yoast `_yoast_wpseo_metadesc` eine
Kaufaufforderung mit Markennamen ("Jetzt bei Hanfjack … kaufen/bestellen!").
Die EAN-Angabe blieb, wo sinnvoll, erhalten; der Kauf-Satz wurde entfernt.

`short_description` (post_excerpt) war bei allen 29 Produkten bereits sauber
(reine Bullet-Listen ohne H1/CTA) und wurde nicht angefasst.

## Vorgehen

- Fix 1 + 2 via `wp_replace_in_post` (Regex-Modus) direkt auf `post_content`,
  NIE via `wp_wc_update_product`/`wp_wc_batch_update_products` — Letzteres hätte
  beim Schreiben von `description` ungewollt `_min_age` (Pflichtfeld "18")
  gelöscht, wenn es nicht explizit mitgesendet wird.
- Exakter String-Match über `wp_replace_in_post` schlug bei allen Produkten
  fehl (`replacements_count: 0`), vermutlich durch eine Encoding-Differenz
  beim Transport der Sonderzeichen/Anführungszeichen; funktioniert hat
  durchgehend der Regex-Modus (`regex: true`) mit toleranten Mustern
  (`<h1[^>]*>.*?</h1>\s*` bzw. `<hr .../>\s*<p...><b...>CTA-Anfang.*?</b></p>\s*`).
- Wo unmittelbar vor dem CTA-Absatz ein `<hr>` als reiner Abschnittstrenner
  stand, wurde er mit entfernt, damit kein verwaister `<hr>` am Textende
  zurückbleibt.
- Yoast-Fix via `wp_yoast_update_post_seo(meta_description=…)`; die CTA-Phrase
  wurde gestrichen, Kerninhalt und (wo vorhanden) EAN blieben erhalten.
  Bei Produkt 13783 zusätzlich `og_description` angepasst (dort stand die
  CTA-Phrase auch in einem separaten `_yoast_wpseo_opengraph-description`-Meta).
- Verifikation je Produkt: `wp_replace_in_post` mit `search == replace`
  (`<h1` bzw. "bei Hanfjack") als kostengünstiger "Grep ohne Änderung" —
  `replacements_count: 0` bestätigt, dass die Strings nicht mehr vorkommen.
  Zusätzlich `wp_yoast_get_post_seo` erneut gelesen und die bereinigte
  `meta_description` bestätigt.

## Sonderfälle

- **Bundles** (14712 Fanpack, 14708 Flexpack, Typ `woosb`): dieselbe
  H1/CTA-Struktur wie die Einzelprodukte, nur der Fließtext ist länger
  (Aufzählung der Bundle-Inhalte). Keine Bundle-spezifische Struktur verändert.
- **Variables Produkt 494** (Palacio Hanf Haarshampoo, outofstock): Elternartikel
  hatte dieselbe H1/CTA-Struktur, ebenso gefixt. Beide Variationen (541 „250ml“,
  542 „500ml“) haben ein leeres `description`-Feld — keine Änderung nötig,
  per `wp_wc_get_product_variation` bestätigt.
- **13789** (Tiger Balsam CBD): einziges Produkt mit einer zusätzlichen
  `<ul>` „Anwendungshinweise“ zwischen Inhaltsstoffen und CTA — der `<hr>` vor
  dem CTA lag hier auf `data-path-to-node="25"` statt `"24"`, dementsprechend
  angepasstes Suchmuster verwendet. Zusätzlich enthielt der Yoast-`seo_title`
  hier (als einziges der 29 Produkte) einen Markensuffix „| Hanfjack“
  („Palacio Tiger Balsam mit CBD | 15 g Dose | Hanfjack“) — zur Konsistenz mit
  allen anderen Titeln im Projekt entfernt, jetzt „Palacio Tiger Balsam mit
  CBD | 15 g Dose“.

## Vollständige Produktliste (29/29)

- [x] 20193 – PALACIO Hanf-Massagegel mit Pflaumen Brandy 600 ml
- [x] 20192 – Palacio Hanf-Badesalz 1200g
- [x] 20190 – Palacio HEMP & DENT Hanf-Zahnpasta 75 g
- [x] 19954 – Palacio CBD Fortis Massage Gel 200ml
- [x] 19839 – Palacio Gentle Soap 100g
- [x] 14712 – Palacio Fanpack Bundle (woosb)
- [x] 14708 – Palacio Flexpack Bundle (woosb)
- [x] 13789 – Palacio Tiger Balsam mit CBD 15g
- [x] 13786 – Palacio Tiger Massage Gel Forte 175ml
- [x] 13783 – Palacio Tiger Massage Gel 380ml
- [x] 1876 – Palacio Cannasex Massage Öl 150ml
- [x] 1879 – Palacio Forte Sport Gel kühlend 200ml
- [x] 1878 – Palacio Forte Sport Gel wärmend 200ml
- [x] 1877 – Palacio After Sun Körperlotion 200ml
- [x] 512 – Palacio Bio Öl Fußcreme regenerierend 125ml Tube
- [x] 511 – Palacio Bio Öl Creme regenerierend 125ml Tube (Handcreme)
- [x] 510 – Palacio Hanf Massagegel Bio Öl Forte 200ml Tube
- [x] 509 – Palacio Cannacool kühlendes Massagegel 200ml Tube
- [x] 508 – Palacio Cannahot wärmendes Massagegel 200ml Tube
- [x] 505 – Palacio Hanf Wasser Zerstäuber 100ml Sprayflasche
- [x] 504 – Palacio Hanfsalbe regenerierend 125ml Dose
- [x] 503 – Palacio Flexgel Massagegel wärmend 380ml Dose
- [x] 502 – Palacio Flexgel Massage kühlend 380ml Dose
- [x] 501 – Palacio Hanf Massagegel mit Bioöl regenerierend 600ml
- [x] 500 – Palacio Hanf Massagegel 380ml
- [x] 499 – Palacio Hanf Körperbutter 200ml
- [x] 497 – Palacio Hanf Haarbalsam 500ml
- [x] 494 – Palacio Hanf Haarshampoo (variables Produkt, 2 Variationen)

## Abschluss

Alle 29 Produkte der Kategorie Pflegeprodukte sind bereinigt und einzeln
verifiziert: kein `<h1` mehr in `description`, keine "bei Hanfjack"-CTA mehr
im Fließtext, Yoast `meta_description` ohne Kaufaufforderung (EAN wo sinnvoll
erhalten). `_min_age` und alle sonstigen Post-Meta-Felder blieben unangetastet,
da ausschließlich `wp_replace_in_post` (Feld-Scope `post_content`) und
`wp_yoast_update_post_seo` verwendet wurden.
