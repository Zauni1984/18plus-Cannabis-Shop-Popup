# Headshop-Mini – Stil-Check (Kategorien Blunts, Dabbing, Feuchtigkeitsregler)

Hausstil-Prüfung der 64 Produkte in den drei Headshop-Unterkategorien
**Blunts** (ID 4224, 10 Produkte), **Dabbing** (ID 6864, 47 Produkte,
HEMPER/Goody-Glass bereits ausgeschlossen) und **Feuchtigkeitsregler**
(ID 4220, 7 Produkte). Anders als beim Aktivkohlefilter- und
Pflegeprodukte-Projekt war dies **kein** Full-Rewrite: Die Mehrheit der
Texte war bereits sauber und wurde unverändert gelassen. Es ging darum,
tatsächliche Verstöße gegen die 6-Punkte-Checkliste (kein H1 im Text, keine
Hanfjack-Marken-CTA, durchgehende Du-Anrede, EU-Konformität, saubere Yoast-
SEO-Felder, saubere `short_description`) zu finden und gezielt zu fixen.

## Befund

Der Vorbefund für dieses Projekt lautete: Eine Stichprobe von 8 Produkten
zeigte keine H1/CTA-Verstöße, daher wurde erwartet, dass die meisten der 64
Produkte keine Änderung brauchen. Das hat sich **größtenteils bestätigt**,
aber mit einer wichtigen Einschränkung:

**3 von 64 Produkten trugen noch die alte, vor-refaktorierte Content-Struktur**
aus dem Pflegeprodukte-Projekt — ein `<h1 data-path-to-node="…">`-Tag am
Anfang des Fließtexts plus eine Hanfjack-Marken-CTA-Phrase in der Yoast
`meta_description` ("… jetzt bei Hanfjack kaufen/bestellen!"):

- **22828** – Amsterdam Limited Edition Special Shape Glaspfeife (Dabbing)
- **22825** – Amsterdam Limited Edition Öl-Glaspfeife (Dabbing)
- **27737** – Spider Farmer Feuchtigkeitsregler 62 % 10er-Pack (Feuchtigkeitsregler)

Diese alte Struktur ist also **nicht auf eine Marke oder Kategorie
beschränkt** — sie taucht vereinzelt quer über den gesamten Headshop-Bereich
auf und sollte bei künftigen Stil-Checks weiterhin mitgeprüft werden.

Die überwiegende Mehrheit der neueren Texte (King Palm, Juicy Jays, Cyclones,
G-Rollz, GRAVEDA Graspresso-Serie, Norddampf, Black Leaf, DIPSE, Integra
Boost) nutzt bereits das neue, saubere Template: `<p dir="auto">` /
`<h3 dir="auto">` / `<ul dir="auto">` ohne `data-path-to-node`-Attribute,
kein H1 im Text, keine Hanfjack-Marken-CTA im Fließtext oder in der
`short_description`. Bei diesen Produkten waren nur vereinzelt die
Yoast-`meta_description`-Felder zu lang, zu kurz, fehlend oder mit einer
(nicht markenbezogenen) Kauf-CTA versehen — reine SEO-Feld-Korrekturen ohne
Eingriff in `post_content`.

Ein Sonderfall (**12441**, Integra Boost Feuchtigkeitsregler 62 % 4g): Der
komplette Yoast-Block (`seo_title`, `focus_keyword`, `meta_description`) war
fälschlich vom 8g-Geschwisterprodukt (12443) kopiert worden — er nannte
"8g"/"28 g Reichweite" statt "4g"/"14 g". Body-Text war korrekt, nur die
SEO-Felder mussten korrigiert werden.

`short_description` (post_excerpt) war bei allen 64 Produkten bereits sauber
(reine Bullet-Listen ohne H1/CTA-Probleme) und wurde nicht angefasst.

## Vorgehen

- H1- und CTA-Fixes in `post_content` ausschließlich über `wp_replace_in_post`
  (Regex-Modus), **nie** über `wp_wc_update_product`/`wp_wc_batch_update_products`
  — Letzteres hätte beim Schreiben von `description` ungewollt `_min_age`
  (Pflichtfeld "18") löschen können, wenn es nicht explizit mitgesendet wird.
- Muster: `<h1[^>]*>.*?</h1>\s*` zum Entfernen des H1-Tags. Bei 22828/22825
  zusätzlich ein zweites Muster für den CTA-Absatz samt vorangehendem `<hr>`
  (`<hr[^>]*/>\s*<p[^>]*><b[^>]*>CTA-Anfang.*?</b></p>\s*`); bei 27737 stand
  kein CTA-Absatz im Fließtext (nur im Yoast-Metadesc), daher genügte dort
  der reine H1-Fix im Content.
- Yoast-Fixes über `wp_yoast_update_post_seo` (`meta_description`,
  vereinzelt `focus_keyword`/`seo_title`): Marken-CTA-Phrasen entfernt,
  zu lange Metadescs (bis 246 Zeichen) auf ca. 120–160 Zeichen gekürzt,
  fehlende Metadescs ergänzt, zu kurze (unter 100 Zeichen) auf sinnvolle
  Länge erweitert — Kerninhalt und EAN wo vorhanden erhalten.
- Verifikation je Fix: `wp_replace_in_post` mit `search == replace`
  (`<h1` bzw. Such-String der CTA) als kostengünstiger "Grep ohne Änderung" —
  `replacements_count: 0` bestätigt, dass der String nicht mehr vorkommt.
  Zusätzlich `wp_yoast_get_post_seo` erneut gelesen und der bereinigte Wert
  bestätigt.
- Große `wp_wc_get_product`-Antworten (bis 55 KB, dominiert von irrelevanten
  Plugin-Metafeldern) wurden bei Bedarf über die vom Harness gespeicherte
  Datei per `grep`/Python statt vollständigem Re-Read geprüft, um Tokens zu
  sparen.

## Sonderfälle

- **Private-Status-Produkte** (20946 GRAVEDA Edelstahlschaufel/Stopfer,
  17773 Norddampf Terp Pen Atomizer, 27737 Spider Farmer Feuchtigkeitsregler):
  normal geprüft wie alle anderen, keine Sonderbehandlung nötig. 27737 hatte
  trotz `noindex`-Status denselben H1/CTA-Verstoß wie die publizierten
  Amsterdam-Produkte und wurde ebenfalls gefixt.
- **Variable Produkte** (17768 Norddampf DAB Pen Mini, 30327 Doktorfreezy
  CleanUp-Set): jeweils nur das Elternprodukt geprüft (Beschreibung liegt
  dort), Variationen nicht einzeln durchgesehen. Bei 17768 war die Yoast-
  Metadesc mit 212 Zeichen deutlich zu lang und enthielt eine (nicht
  markenbezogene) Kauf-CTA — gekürzt auf 121 Zeichen.
- **Nicht-Hanfjack-CTA in Yoast-Metadescs** (17773, 17772, 17768): Diese
  drei Norddampf-Produkte hatten generische Kauf-CTA-Phrasen wie "Jetzt
  kaufen & mobil genießen!" im Metadesc — ohne Markennennung "Hanfjack",
  also formal kein Verstoß gegen Checkliste-Punkt 2 (der explizit nur
  Hanfjack-Marken-CTA verbietet). Da die Metadescs dadurch aber auch die
  Zeichenlänge sprengten (bis 212 Zeichen), wurden sie im Zuge der
  Längenkorrektur mitentfernt.
- **12441** (siehe Befund): einziger Fall von komplett falsch zugeordneten
  Yoast-Feldern (Copy-Paste vom Geschwisterprodukt) statt eines
  Stil-Verstoßes im engeren Sinn — dennoch klar unter Checkliste-Punkt 5
  ("keine offensichtlichen Fehler") zu fassen und gefixt.
- **12465** (Juicy Jays Hemp Wraps Eldorado, Blunts): Auffälligkeit außerhalb
  des Hausstil-Scopes entdeckt — der letzte Absatz des Fließtexts nennt
  fälschlich "King Palm Slim Rolls" statt der tatsächlichen Juicy Jays Wraps
  (vermutlich Copy-Paste-Fehler aus einer anderen Produktvorlage). Da es
  sich um einen Fakteninhalt-Fehler und keinen Stilverstoß handelt, wurde er
  hier **nicht** korrigiert (außerhalb des Auftragsumfangs), sondern separat
  als Vorschlag markiert.
- **Ungewöhnlich langer Yoast-`focus_keyword`** (27737 Spider Farmer):
  enthielt einen ganzen Satz statt eines kurzen Schlüsselwortausdrucks
  ("Spider Farmer 2-Wege-Feuchtigkeitsregler 62 % im 10er-Pack zur optimalen
  Lagerung und Schimmelprävention") — zur Konsistenz mit allen anderen
  Produkten auf eine normale Kurzform gekürzt.

## Vollständige Produktliste (64/64)

### Blunts (10/10)

- [x] 19896 – King Palm Watermelon Slim Rolls — OK
- [x] 19895 – King Palm Magic Mint Slim Rolls — OK
- [x] 19894 – King Palm Margarita Slim Rolls — OK
- [x] 19893 – King Palm Berry Terps Slim Rolls — OK
- [x] 19892 – King Palm Banana Cream Slim Rolls — OK
- [x] 12465 – Juicy Jays Hemp Wraps Eldorado — FIXED (Yoast metadesc 202→155 Zeichen)
- [x] 12463 – Juicy Jays Hemp Wraps Purple — FIXED (Yoast metadesc 194→147)
- [x] 12460 – Juicy Jays Hemp Wraps Tropical — FIXED (Yoast metadesc 210→152)
- [x] 9454 – G-Rollz Banksy's Graffiti Orange Bud Blunt Cones — FIXED (Yoast metadesc 246→154)
- [x] 9033 – Cyclones Hemp Cones Cane XTRA Slo — OK

### Dabbing (47/47)

- [x] 30658, 30657, 30656, 30655, 30654, 30653, 30652, 30651, 30650 – GRAVEDA Rosin Bags Serie (versch. Mikron/Größen) — alle OK
- [x] 30327 – Doktorfreezy CleanUp-Set (variables Produkt, Elternprodukt geprüft) — FIXED (Yoast metadesc 224→148)
- [x] 30306 – High Society Stahl 20mm 5er — FIXED (Yoast metadesc fehlte komplett → ergänzt, 99 Zeichen)
- [x] 28627 – Black Leaf Dabmatte klein — OK
- [x] 28607 – Graveda Tweezer Pinzette — OK
- [x] 28539 – Graveda Dabbingtool-Set — OK
- [x] 27415, 27412, 27408, 11933 – weitere GRAVEDA Rosin Bags 51×89mm — alle OK
- [x] 27401 – GRAVEDA Pergamentpapier für Extraktion 30cm — OK
- [x] 22828 – Amsterdam Limited Edition Special Shape Glaspfeife (26cm) — FIXED (H1 + Hanfjack-CTA-Absatz + Yoast-CTA entfernt)
- [x] 22825 – Amsterdam Limited Edition Öl-Glaspfeife (24cm) — FIXED (H1 + Hanfjack-CTA-Absatz + Yoast-CTA entfernt)
- [x] 21035, 20963, 20954, 21030, 21023, 21018, 21012, 21004 – GRAVEDA Graspresso Hydraulik/Pneumatik Pressen (versch. Größen) — alle OK
- [x] 20998, 20994, 20988, 20982 – weitere GRAVEDA Graspresso Pressen — alle OK
- [x] 20977 – GRAVEDA Graspresso Rosin Hydraulik Heißpresse 10T — OK
- [x] 20973 – GRAVEDA Magnete für Rosinpressen (2 Stück) — OK
- [x] 20968 – GRAVEDA Graspresso Rosin Hydraulik Heißpresse 3T — OK
- [x] 20959 – GRAVEDA Waschmaschine Eis/Kaltwasser Extraktion — OK
- [x] 20950 – GRAVEDA Pergamentpapier für Extraktion 80g, 20 Stück — OK
- [x] 20946 – GRAVEDA Edelstahlschaufel/Stopfer 0,35g (Status: private) — OK
- [x] 20942 – GRAVEDA BottleTech PrePress — FIXED (Yoast metadesc 99→156, war zu kurz)
- [x] 20939 – GRAVEDA Cooling Plate — OK
- [x] 18986 – Black Leaf Electric Hot Knife Black — OK
- [x] 18727 – DIPSE Digitalwaage Dab Scale 100g/0,01g — OK
- [x] 17773 – Norddampf Terp Pen Atomizer (Status: private) — FIXED (Yoast metadesc 172→140, generische Kauf-CTA gekürzt)
- [x] 17772 – Norddampf Hot Knife für DAB Pen Mini — FIXED (Yoast metadesc 168→138, gleiche Kürzung)
- [x] 17768 – Norddampf DAB Pen Mini (variables Produkt, Elternprodukt geprüft) — FIXED (Yoast metadesc 212→121, deutlich zu lang + generische CTA entfernt)
- [x] 17764 – Norddampf Terp Pen — OK

### Feuchtigkeitsregler (7/7)

- [x] 27737 – Spider Farmer Feuchtigkeitsregler 62% 8g, 10 Stück (Status: private) — FIXED (H1 entfernt, Yoast-CTA "jetzt bei Hanfjack bestellen!" entfernt, Yoast metadesc 156→127, überlanger focus_keyword gekürzt)
- [x] 18724 – Integra Boost Feuchtigkeitsregler 55% 67g — OK
- [x] 18723 – Integra Boost Feuchtigkeitsregler 55% 8g — OK
- [x] 18722 – Integra Boost Feuchtigkeitsregler 55% 4g — OK
- [x] 12445 – Integra Boost Feuchtigkeitsregler 62% 67g — OK
- [x] 12443 – Integra Boost Feuchtigkeitsregler 62% 8g — OK
- [x] 12441 – Integra Boost Feuchtigkeitsregler 62% 4g — FIXED (kompletter Yoast-Block seo_title/focus_keyword/metadesc war vom 8g-Geschwisterprodukt 12443 kopiert, auf korrekte 4g-Werte korrigiert)

## Abschluss

Alle 64 Produkte der drei Kategorien Blunts, Dabbing und Feuchtigkeitsregler
sind geprüft. **14 von 64 Produkten** hatten tatsächliche Hausstil- oder
Yoast-Hygiene-Verstöße und wurden gezielt gefixt (4 in Blunts, 8 in Dabbing,
2 in Feuchtigkeitsregler); **50 Produkte** waren bereits konform und wurden
nicht verändert. Der wichtigste Befund: 3 Produkte trugen noch die alte,
vor-refaktorierte Content-Struktur (H1 + Hanfjack-Marken-CTA) aus dem
Pflegeprodukte-Projekt — diese ist also nicht auf eine einzelne Marke oder
Kategorie beschränkt und sollte bei künftigen Stil-Checks im Headshop-Bereich
weiter im Blick behalten werden. `_min_age` und alle sonstigen Post-Meta-
Felder blieben unangetastet, da ausschließlich `wp_replace_in_post`
(Feld-Scope `post_content`) und `wp_yoast_update_post_seo` verwendet wurden.
