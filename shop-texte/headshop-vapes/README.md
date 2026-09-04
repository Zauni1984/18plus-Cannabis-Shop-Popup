# Headshop-Vapes – Stil-Check (Kategorien Vapes & Pods, Vaporizer)

Hausstil-Prüfung der 75 Produkte in den zwei Headshop-Unterkategorien
**Vapes & Pods** (ID 15550, 40 Produkte) und **Vaporizer** (ID 6862,
42 Produkte, davon 7 HEMPER-Produkte ausgeschlossen → 35 geprüft). Kein
Full-Rewrite-Projekt: Geprüft wurde ausschließlich gegen die 6-Punkte-
Checkliste (kein H1 im Text, keine Hanfjack-Marken-CTA, durchgehende
Du-Anrede, EU-Konformität inkl. Vaporizer-spezifischem Verbot von
Cannabis-Wirkstoff-/Wirkungsaussagen, saubere Yoast-SEO-Felder, saubere
`short_description`).

## Marken-Ausschluss

7 Produkte in Vaporizer enthalten "HEMPER" im Namen (HEMPER Lit Vape
Battery 510-Akku, alle 6 Farbvarianten + Assorted: 37613, 37612, 37611,
37610, 37609, 37608, 37607) und wurden gemäß Anweisung übersprungen, nicht
geprüft und nicht verändert. "Goody Glass" und "Smoke Friends" kamen in
keiner der beiden Kategorien vor.

## Befund

### Vapes & Pods (40/40) — SHEESH-Serie, ein einziges systematisches Problem

Alle 40 Produkte sind die SHEESH-Marke (Hyper Series DNT-9, Superior Pro
Vape, Signature Pod — jeweils 10/10/20 Geschmacksvarianten). Der Content ist
maschinell aus derselben Vorlage generiert: **jedes einzelne Produkt** hatte
exakt denselben einen Verstoß — ein `<h1>Produktname</h1>` als erste Zeile
im Fließtext, gefolgt vom eigentlichen Absatztext. Sonst war bei allen 40
Produkten alles sauber: keine Hanfjack-CTA, durchgehende Du-Anrede, keine
unbelegten Gesundheits-/Wirkstoffaussagen (Formulierungen wie "Herstellerangabe:
enthält kein THC" sind Tatsachenwiedergabe der Verpackung, keine eigene
Behauptung), Yoast-Felder vollständig und mit sinnvoller Länge, `short_description`
sauber. Fix: `<h1>[^<]*</h1>\n?` per Regex entfernt (39/40 — bei einem
Produkt, 38016, griff zunächst der einfache String-Such-Ersatz aufgrund
eines abweichenden Halbgeviertstrich-Zeichens nicht, danach ebenfalls per
Regex behoben).

### Vaporizer (35/35 geprüft, 35/35 gefixt) — zwei unterschiedliche Content-Vorlagen, beide mit systematischen Fehlern

Die Kategorie mischt zwei völlig unterschiedliche Text-Generationen ohne
einen einzigen sauberen Fall:

**A) Storz & Bickel Volcano-Familie (13 Produkte, IDs 37907–37919):**
Marketing-H1 wie `<h1>Storz & Bickel Volcano Hybrid Silber: der vertraute
Look des Spitzenmodells</h1>` am Textanfang, danach `<h3>`-Struktur.
Inhaltlich sauber (nur Gerätefunktionen: Temperatur, Heiztechnik, Akku,
Material), Yoast-Felder gut. Einziger Fix: H1 entfernt (`<h1>[^<]*</h1>\s*`
per Regex, mit `\s*` statt `\n?` wegen Leerzeichen-Trennung statt Zeilenumbruch).

**B) "Next-Gen"-Marketing-Vorlage (22 Produkte, IDs 1693–27276 sowie die
4 Norddampf-Glasmundstücke):** Kein H1, aber zwei wiederkehrende
Verstoßmuster:

- **Yoast-`meta_description`-Hygiene (fast durchgehend):** Storz & Bickel-
  Zubehörprodukte hatten die EAN roh und ohne Satzzeichen ans Ende der
  Metadesc geklebt (z. B. "... für intensives Aromadampfen EAN:
  4260248823434."); Norddampf-Produkte hatten stattdessen eine Kauf-CTA-
  Phrase angehängt ("Jetzt bei Hanfjack bestellen!", "Jetzt kaufen &
  besser daben!", "jetzt zuschlagen!"). Beides wurde durch eine saubere,
  120–160-Zeichen-Metadescription ohne CTA/EAN-Artefakt ersetzt.
- **Cannabis-Wirkstoff-/Wirkungsaussagen im Fließtext (7 Produkte):** Bei
  Vaporizern verboten Formulierungen wie "effiziente Extraktion der
  Wirkstoffe", "Wirkstoffe und Aromen … extrahiert", "Wirkstoffe verloren
  gehen", "Terpene und Cannabinoide optimal freisetzt/zur Geltung bringt"
  und "voller Geschmack und Wirkung" — betroffen: Mighty+ (27256), Plenty
  (27232), Veazy (21704), Crafty Plus (21701), Dab Pen Mini Atomizer
  (19466, gleich 3 Stellen im Text + 1 in der `short_description`), Voity
  Vaporizer (17763), RELICT Bubbler Wasserfilter (1702). Jeweils durch
  gerätebezogene Formulierungen ersetzt (Erwärmung, Aroma, Terpene ohne
  "Wirkstoffe"/"Cannabinoide"/"Wirkung").
- **Zusätzlich bei den 4 Norddampf-Glasmundstücken** (28666, 28662, 28658 —
  nicht bei 28664 Rosa): die Behauptung "ein … Dampferlebnis, das einfach
  süchtig macht" — als unbelegte, im Suchtkontext heikle Behauptung durch
  "das einfach überzeugt" ersetzt.

Ergebnis: **0 von 35** geprüften Vaporizer-Produkten war bereits vollständig
konform — jedes hatte mindestens einen Yoast-Metadesc-Fehler (EAN-Artefakt
oder CTA), 7 zusätzlich eine Cannabis-Wirkstoffaussage im Fließtext, 3
zusätzlich die Suchtformulierung.

`short_description` (post_excerpt) war in beiden Kategorien bis auf die eine
Wirkstoff-Stelle bei 19466 bereits sauber und wurde sonst nicht angefasst.

## Vorgehen

- H1- und Textfixes ausschließlich über `wp_replace_in_post`
  (`post_content`/`post_excerpt`), **nie** über `wp_wc_update_product` /
  `wp_wc_batch_update_products` — Letzteres hätte `_min_age` (Pflichtfeld
  "18") löschen können.
- H1-Entfernung per Regex `<h1>[^<]*</h1>\n?` bzw. `<h1>[^<]*</h1>\s*`
  (je nach Trennzeichen nach dem Tag), da der reine String-Suchmodus bei
  Halbgeviertstrichen im Produktnamen vereinzelt nicht traf.
- Wirkstoff-/Wirkungs-/Cannabinoid-Formulierungen per exaktem String-Ersatz
  gezielt umformuliert, nicht pauschal gelöscht — Satzstruktur blieb erhalten.
- Yoast-Fixes über `wp_yoast_update_post_seo` (`meta_description`): EAN-
  Artefakte und CTA-Phrasen entfernt, neue Metadescription mit echtem Inhalt
  auf ca. 120–160 Zeichen verfasst.
- Für die Lektüre der SHEESH- und Storz-&-Bickel-/Norddampf-Produkte wurde
  überwiegend `wp_get_cpt_item(rest_base="product", …)` statt
  `wp_wc_get_product` verwendet (liefert nur id/title/content/excerpt/status
  statt des vollen ~15–20 KB WooCommerce-Objekts mit Google-Search-Console-
  Metadaten) — spart massiv Tokens bei identischem Prüfergebnis. Yoast-Felder
  separat über `wp_yoast_get_post_seo` gelesen.
- Jeder Fix wurde durch erneutes Lesen des betroffenen Felds verifiziert.

## Produktliste

### Vapes & Pods (40/40, Kategorie 15550) — alle FIXED (H1 entfernt)

**Hyper Series DNT-9 (10):** 38016 Zkittles, 38015 Watermelon Gelato, 38014
Super Lemon Haze, 38013 Purple Haze, 38012 Pretty Strawberry, 38011
Pineapple Express, 38010 Mango Miami, 38009 Gorilla Glue, 38008 Durban
Poison, 38007 Blue Kush

**Superior Pro Vape (10):** 38005 Zkittlez, 38004 Super Lemon Haze, 38003
Sour Patch, 38002 Guava Haze, 38001 Glossy Gelato, 38000 Gazzy Grape, 37999
Forbidden Fruit, 37998 Dirty Sprite, 37997 Blue Zushi, 37996 Baddie Runtz

**Signature Pod (20):** 37995 Zkittlez, 37994 Super Lemon Haze, 37993
Wildberry Lilly, 37992 White Widow, 37991 Strawberry Kiss, 37990 Purple
Punch, 37989 Love 61, 37988 La Chaya, 37987 Himbeere Raspberry, 37986 Peach
Ice, 37985 Gelato Berry, 37984 Frozen Berries, 37983 Exotic, 37982 Coconut
Blueberry, 37981 Cherry Fuel, 37980 Melon Runtz, 37979 Blueberry, 37978
Blackberry Ize, 37977 BBL Kush, 37976 Amnesia Splash

### Vaporizer (35/35 geprüft, Kategorie 6862)

**HEMPER — übersprungen (7):** 37613, 37612, 37611, 37610, 37609, 37608,
37607 (Lit Vape Battery 510-Akku, alle Farben)

**Storz & Bickel Volcano-Familie — alle FIXED (H1 entfernt):** 37919
Volcano Hybrid Silber, 37918 Volcano Hybrid Onyx, 37917 Volcano Classic
Silber, 37916 Volcano Classic Onyx, 37915 Volcano Classic Green, 37914
Normalsiebe 6 Stk, 37913 Füllkissen 4 Stk, 37912 Füllkammerwerkzeuge 5 Stk,
37911 Dosierkapseln 40 Stk, 37910 Dosierkapsel Magazin mit Füllkissen,
37909 Capsule Caddy mit Füllkissen, 37908 Ballonschlauch 3m, 37907 Ballon
mit Adapter

**Norddampf Relict Glasmundstück (4) — FIXED:** 28666 Weiß (Suchtformulierung
+ Yoast-CTA), 28664 Rosa (nur Yoast-EAN-Artefakt), 28662 Grün
(Suchtformulierung + Yoast-CTA), 28658 Seegrün (Suchtformulierung +
Yoast-CTA)

**Storz & Bickel Geräte/Zubehör — FIXED:**
- 27276 Venty Vaporizer — Yoast-EAN-Artefakt
- 27268 Capsule Caddy — Yoast-EAN-Artefakt
- 27256 Mighty+ Vaporizer — Wirkstoff-Aussage im Text + Yoast-EAN-Artefakt
- 27244 Dosierkapsel Magazin — Yoast-EAN-Artefakt
- 27232 Plenty Vaporizer — Wirkstoff-Aussage im Text + Yoast-EAN-Artefakt
- 27222 Füllhilfe für Vaporizer — Yoast-EAN-Artefakt
- 21704 Veazy Vaporizer — Wirkstoff-Aussage im Text
- 21701 Crafty Plus Vaporizer — Wirkstoff-Aussage im Text + Yoast-EAN-Artefakt

**Norddampf-Produkte — FIXED:**
- 23424 Relict Dosierkapseln Edelstahl mit Tropfkissen, 4 Stk —
  Yoast-EAN-Artefakt
- 19466 Dab Pen Mini Atomizer — 3× Wirkstoff-/Cannabinoid-Aussage im Text
  + 1× in `short_description` + Yoast-CTA
- 18663 HAMMAH Bubbler — Yoast-CTA
- 18658 Relict Dosierkapseln Edelstahl, 4 Stk — Yoast-CTA
- 17802 Relict Capsule Caddy — Yoast-CTA
- 1693 RELICT Vaporizer & Zubehör (variabel, Elternprodukt geprüft) —
  Yoast-CTA
- 17777 Hammah Vaporizer (variabel, Elternprodukt geprüft) — Yoast-CTA
- 17763 Voity Vaporizer — "Wirkung"-Aussage im Text + Yoast-CTA
- 1706 RELICT Bong Adapter 14/18mm — Yoast-EAN-Artefakt
- 1702 RELICT Bubbler Wasserfilter — Cannabinoid-Aussage im Text +
  Yoast-CTA

## Abschluss

75 Produkte geprüft (40 Vapes & Pods + 35 Vaporizer), 7 HEMPER-Produkte
programmgemäß übersprungen. **Alle 75 geprüften Produkte hatten mindestens
einen Verstoß und wurden gefixt** — 0 Produkte waren bereits vollständig
konform. Das ist ein deutlicher Unterschied zu früheren Headshop-Stil-Checks
(z. B. Headshop-Mini: nur 14/64 mit Verstoß): Diese beiden Kategorien wurden
offenbar komplett neu bzw. mit einer noch nicht auf den Hausstil
abgestimmten Vorlage befüllt. Kernprobleme: (1) systematisches `<h1>` am
Fließtext-Anfang bei allen 53 H1-Fällen (40 SHEESH + 13 Storz & Bickel
Volcano-Familie), (2) durchgehend fehlerhafte Yoast-`meta_description`
bei der zweiten Vaporizer-Vorlage (EAN-Artefakt oder Kauf-CTA statt
sauberem Fließtext), (3) 7 Fälle von expliziten Cannabis-Wirkstoff-/
Cannabinoid-/Wirkungsaussagen im Fließtext von Vaporizer-Produkten, die
gegen die vaporizer-spezifische EU-Konformitätsregel verstießen, und (4) 3
Fälle einer unbelegten Suchtformulierung bei den Glasmundstücken.
`_min_age` und alle sonstigen Post-Meta-Felder blieben unangetastet, da
ausschließlich `wp_replace_in_post` (Feld-Scope `post_content`/`post_excerpt`)
und `wp_yoast_update_post_seo` verwendet wurden.
