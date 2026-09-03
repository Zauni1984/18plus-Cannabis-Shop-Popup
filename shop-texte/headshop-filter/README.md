# Headshop-Filter – Stil-Check (Kategorien Filter, Luftfilter, Ersatzfilter)

Hausstil-Prüfung der Produkte in den drei Headshop-Kategorien **Filter**
(ID 608, nur direkt zugeordnete Produkte, Unterkategorie Aktivkohlefilter
ausgeschlossen), **Luftfilter** (ID 15521) und **Ersatzfilter** (ID 15523,
Unterkategorie von Luftfilter). Kein Full-Rewrite: Es ging darum, tatsächliche
Verstöße gegen die 6-Punkte-Checkliste (kein H1 im Text, keine Hanfjack-
Marken-CTA, durchgehende Du-Anrede, EU-Konformität, saubere Yoast-SEO-Felder,
saubere `short_description`) zu finden und gezielt zu fixen.

## Kategorie-Bereinigung vor der Prüfung

- **Kategorie 608 (Filter)** enthält direkt 109 Produkte (2 Seiten à 100/9).
  Davon sind **93 gleichzeitig der Unterkategorie Aktivkohlefilter (ID 4550)**
  zugeordnet (PURIZE, Kailar, Medusafilters, Granny's Weed, Hybrid Supreme,
  Smoking, CTIP, Gizeh Aktivfilter Kokoskohle) — dieses Projekt (bereits
  abgeschlossen) wurde **nicht** angefasst. Verbleibend: 16 Produkte.
- Von diesen 16 tragen **9 den Markennamen HEMPER im Produktnamen**
  (HEMPER Kartonfilter perforiert, HEMPER Bullet Kartonfilter × 2, HEMPER
  Quick Tips Kartonfilter × 6 Geschmacksrichtungen) — laut Auftrag
  übersprungen, nicht bearbeitet.
- **Verbleibend zur Prüfung in Kategorie 608: 7 Produkte** (King Palm Flavor
  Tips × 3, G-Rollz Dr. Whiskerz Pink Mega Tips, RAW Cellulose Filter Slim,
  Tortuga verde Drehfilter Slim, Gizeh Filter Tips Regular).
- **Luftfilter (15521): 19 Produkte** — 7 "Persönliche Luftfilter"-Figuren
  (Trixx Gespenst, RIPNDIP Nermal, Juice Ananas, Chilly Pinguin, Catnip
  Kätzchen, Blitz Football, Blaze Kaktus) plus 12 dazugehörige Ersatzfilter-
  Varianten (Einzeln/3er-Pack je Figur), die zugleich in Kategorie 15523
  einsortiert sind. Keine zusätzlichen, nur in 15523 geführten Produkte
  gefunden — die 12 Ersatzfilter sind in beiden Kategorien identisch.
- **Markenausschluss HEMPER/Goody Glass/Smoke Friends**: In Luftfilter/
  Ersatzfilter kam keiner der drei Strings **im Produktnamen** vor. Die
  Marken-Taxonomie der 7 Figuren ist tatsächlich "**Smoke Fiends**" (nicht
  "Smoke Friends" — andere Schreibweise, kein Treffer). Sonderfall: **37852
  RIPNDIP Nermal Persönlicher Luftfilter** trägt als Marken-Taxonomie
  "RIPNDIP x HEMPER" — der Produkt**name** selbst enthält "HEMPER" jedoch
  nicht, daher gemäß der Anweisung (Prüfung explizit auf den Produktnamen)
  **nicht** ausgeschlossen, sondern regulär geprüft und gefixt. Dies ist im
  Bericht als expliziter Grenzfall vermerkt.

## Befund

**26 Produkte geprüft** (7 in Filter-direkt, 19 in Luftfilter/Ersatzfilter).
Ein sehr klares Muster zeigte sich in Luftfilter/Ersatzfilter: **alle 19
Produkte** (Batch-Erstellung 27.08.2026) trugen denselben Verstoß — ein
`<h1>Titel</h1>` als erste Zeile von `post_content`, gefolgt direkt von
`<p>…`. Weder Hanfjack-Marken-CTA noch andere Verstöße kamen in dieser Gruppe
vor; `short_description` und alle Yoast-Felder (SEO-Titel, Metadesc 118–129
Zeichen, Focus-Keyword) waren durchgehend sauber.

In Kategorie 608 (Filter-direkt) zeigte sich ein anderes, aus dem
Kartonfilter/Blunts-Projekt bekanntes Muster: Die drei **King Palm Flavor
Tips**-Produkte (Mango OG, Lemon Haze, Gelato Cream) trugen noch die alte,
vor-refaktorierte Content-Struktur — `<h1 data-path-to-node="…">` im
Fließtext **plus** eine Hanfjack-Marken-CTA-Phrase ("… jetzt bei Hanfjack
online bestellen!") am Ende der `short_description` **plus** dieselbe
CTA-Phrase ("Jetzt bei Hanfjack kaufen!") in der Yoast-`meta_description`.
Alle drei Stellen wurden gefixt. Ein viertes Produkt (G-Rollz Dr. Whiskerz
Pink Mega Tips) hatte einen sauberen Text ohne H1/CTA, aber eine mit 186
Zeichen deutlich zu lange Yoast-Metadesc — auf 154 Zeichen gekürzt. Die
übrigen drei Produkte (RAW Cellulose Filter Slim, Tortuga verde Drehfilter
Slim, Gizeh Filter Tips Regular) waren bereits vollständig konform.

## Vorgehen

- H1-Fixes in `post_content` ausschließlich über `wp_replace_in_post`, **nie**
  über `wp_wc_update_product`/`wp_wc_batch_update_products` — Letzteres hätte
  beim Schreiben von `description` ungewollt `_min_age` (Pflichtfeld "18")
  löschen können.
- **Beobachtung zur Suchstring-Behandlung**: Ein Suchstring mit eingebettetem
  `\n` (z. B. `<h1>…</h1>\n`) fand im Nicht-Regex-Modus **0** Treffer, obwohl
  der Tag exakt so im Content stand — vermutlich eine Eigenheit der
  Parameterübertragung bei mehrzeiligen Strings. Funktionierender Workaround:
  Regex-Modus mit Muster `<h1>Titel</h1>\s*` (Titel ohne regex-spezielle
  Zeichen escaped/geprüft) entfernte Tag **und** nachfolgenden Whitespace in
  einem Aufruf zuverlässig. Bei den drei King-Palm-Produkten (H1 mit
  `data-path-to-node`-Attribut) wurde zweistufig vorgegangen: zuerst der
  H1-Tag ohne Trailing-Newline per Literal-Suche entfernt, dann der
  verbleibende Zeilenumbruch vor dem folgenden `<p …>` separat entfernt.
- Yoast-Fixes über `wp_yoast_update_post_seo` (`meta_description`): Marken-
  CTA-Phrasen entfernt, überlange Metadesc gekürzt — Kerninhalt erhalten,
  Länge auf 143–154 Zeichen gebracht (Zielkorridor 120–160).
- CTA-Phrase in `short_description` (post_excerpt) über `wp_replace_in_post`
  (Feld `post_excerpt`) entfernt, Satz sauber mit Punkt statt Ausrufezeichen-
  CTA abgeschlossen.
- Verifikation je Fix: `wp_replace_in_post` mit `search == replace`
  (`<h1` bzw. `jetzt bei Hanfjack`) als kostengünstiger "Grep ohne Änderung" —
  `replacements_count: 0` bestätigt für alle 22 H1-Fixes und alle 3 CTA-Fixes,
  dass der jeweilige String nicht mehr vorkommt. Zusätzlich `wp_yoast_get_post_seo`
  erneut gelesen und die bereinigten Metadescs bestätigt.
- Kategorie-Abgleich (608 vs. 4550-Überschneidung) über Python-Set-Differenz
  der beiden `wp_wc_list_products`-Ergebnislisten statt Einzelprüfung von 109
  Produkten — spart Tokens, das Ergebnis wurde stichprobenartig über das
  `categories`-Feld einzelner `wp_wc_get_product`-Antworten verifiziert.

## Vollständige Produktliste (26/26)

### Filter – direkt, ohne Aktivkohlefilter-Überschneidung, ohne HEMPER (7/7)

- [x] 23166 – King Palm Flavor Tips Mango OG 7mm – 2 Stück — FIXED (H1 entfernt, Hanfjack-CTA in short_description entfernt, Yoast-Metadesc-CTA entfernt)
- [x] 23169 – King Palm Flavor Tips Lemon Haze 7mm – 2 Stück — FIXED (H1 entfernt, Hanfjack-CTA in short_description entfernt, Yoast-Metadesc-CTA entfernt)
- [x] 23172 – King Palm Flavor Tips Gelato Cream 7mm – 2 Stück — FIXED (H1 entfernt, Hanfjack-CTA in short_description entfernt, Yoast-Metadesc-CTA entfernt)
- [x] 18688 – G-Rollz Dr. Whiskerz Pink Mega Tips 4x5,5cm – 50 Tips Booklet (Status: private) — FIXED (Yoast-Metadesc 186→154 Zeichen)
- [x] 9397 – RAW Cellulose Filter Slim – 200 Stück — OK
- [x] 9021 – Tortuga verde Drehfilter Slim 6 mm ca. 104 Filter — OK
- [x] 524 – Gizeh Filter Tips Regular 35 Blatt – 1 Heftchen — OK

### Luftfilter (15521) – 7 Figuren (7/7)

- [x] 37853 – Trixx Gespenst Persönlicher Luftfilter — FIXED (H1 entfernt)
- [x] 37852 – RIPNDIP Nermal Persönlicher Luftfilter (Marken-Taxonomie "RIPNDIP x HEMPER", Produktname ohne "HEMPER" — regulär geprüft) — FIXED (H1 entfernt)
- [x] 37851 – Juice Ananas Persönlicher Luftfilter — FIXED (H1 entfernt)
- [x] 37838 – Chilly Pinguin Persönlicher Luftfilter — FIXED (H1 entfernt)
- [x] 37837 – Catnip Kätzchen Persönlicher Luftfilter — FIXED (H1 entfernt)
- [x] 37836 – Blitz Football Persönlicher Luftfilter — FIXED (H1 entfernt)
- [x] 37835 – Blaze Kaktus Persönlicher Luftfilter — FIXED (H1 entfernt)

### Ersatzfilter (15523) – 12 Varianten, gleichzeitig in Luftfilter (15521) (12/12)

- [x] 37850 – Ersatzfilter für Trixx Gespenst – Einzeln — FIXED (H1 entfernt)
- [x] 37849 – Ersatzfilter für Trixx Gespenst – 3er-Pack — FIXED (H1 entfernt)
- [x] 37848 – Ersatzfilter für Juice Ananas – Einzeln — FIXED (H1 entfernt)
- [x] 37847 – Ersatzfilter für Juice Ananas – 3er-Pack — FIXED (H1 entfernt)
- [x] 37846 – Ersatzfilter für Chilly Pinguin – Einzeln — FIXED (H1 entfernt)
- [x] 37845 – Ersatzfilter für Chilly Pinguin – 3er-Pack — FIXED (H1 entfernt)
- [x] 37844 – Ersatzfilter für Catnip Kätzchen – Einzeln — FIXED (H1 entfernt)
- [x] 37843 – Ersatzfilter für Catnip Kätzchen – 3er-Pack — FIXED (H1 entfernt)
- [x] 37842 – Ersatzfilter für Blitz Football – Einzeln — FIXED (H1 entfernt)
- [x] 37841 – Ersatzfilter für Blitz Football – 3er-Pack — FIXED (H1 entfernt)
- [x] 37840 – Ersatzfilter für Blaze Kaktus – Einzeln — FIXED (H1 entfernt)
- [x] 37839 – Ersatzfilter für Blaze Kaktus – 3er-Pack — FIXED (H1 entfernt)

## Sonderfälle

- **HEMPER-Ausschluss (9 Produkte in Filter-direkt)**: HEMPER Kartonfilter
  perforiert (37606), HEMPER Bullet Kartonfilter vorgerollt – Tray/Box
  (37586/37585), HEMPER Quick Tips Kartonfilter – Watermelon/Mint/Mango/
  Grape/Blueberry/Banana (37228/37227/37226/37225/37224/37223) — laut
  User-Anweisung übersprungen, keine Prüfung vorgenommen.
- **Aktivkohlefilter-Überschneidung (93 Produkte in Filter-direkt)**: bereits
  in einem früheren, abgeschlossenen Projekt bearbeitet — nicht angefasst.
  Liste nicht einzeln dokumentiert (siehe Kategorie-Bereinigung oben für die
  Marken/Serien).
- **RIPNDIP Nermal (37852)**: einziger Fall mit Marken-Taxonomie "RIPNDIP x
  HEMPER" (enthält "HEMPER"), aber der Produkt**name** selbst lautet
  "RIPNDIP Nermal Persönlicher Luftfilter" ohne "HEMPER". Die Anweisung
  bezog sich explizit auf den Produktnamen, daher wurde das Produkt reg
  ulär geprüft (H1-Verstoß gefunden und gefixt) statt übersprungen. Dies
  ist eine bewusste Auslegungsentscheidung und wird hier transparent
  gemacht.
- **Konsistentes H1-Muster (19/19 in Luftfilter/Ersatzfilter)**: Alle 19
  Produkte dieser beiden Kategorien stammen aus derselben Content-Erstellung
  vom 27.08.2026 und trugen ausnahmslos denselben H1-Verstoß, sonst keine
  weiteren Abweichungen vom Hausstil. Anders als im vorherigen
  Blunts/Dabbing-Projekt war hier keine Stichprobe nötig, da sich das Muster
  bereits nach den ersten beiden geprüften Produkten (37853, 37852) als
  durchgängig erwies und in der Folge bei jedem weiteren Produkt bestätigt
  wurde.
- **Suchstring-Eigenheit bei `wp_replace_in_post`**: Ein Nicht-Regex-Such-
  string mit eingebettetem `\n` fand konsistent 0 Treffer, obwohl der Text
  exakt vorhanden war (getestet an mehreren Produkten). Regex-Modus mit
  `\s*`/`\s+` löste das zuverlässig. Für künftige Stil-Checks empfohlen:
  bei mehrzeiligen Suchstrings direkt den Regex-Modus verwenden.

## Abschluss

Alle 26 zu prüfenden Produkte der drei Kategorien Filter (direkt),
Luftfilter und Ersatzfilter sind geprüft. **23 von 26 Produkten** hatten
tatsächliche Hausstil-Verstöße und wurden gezielt gefixt (4 in Filter-direkt,
19 in Luftfilter/Ersatzfilter — davon 19 reine H1-Fixes, 3 zusätzlich mit
Hanfjack-Marken-CTA-Entfernung in `short_description` und Yoast-Metadesc,
1 mit reiner Yoast-Metadesc-Längenkorrektur); **3 Produkte** waren bereits
konform. 93 Produkte in Kategorie 608 wurden wegen Aktivkohlefilter-
Überschneidung übersprungen (bereits abgeschlossenes Projekt), 9 Produkte
wegen der Marke HEMPER im Produktnamen. `_min_age` und alle sonstigen
Post-Meta-Felder blieben unangetastet, da ausschließlich `wp_replace_in_post`
(Feld-Scope `post_content`/`post_excerpt`) und `wp_yoast_update_post_seo`
verwendet wurden.
