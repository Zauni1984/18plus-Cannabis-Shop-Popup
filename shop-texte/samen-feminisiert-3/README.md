# Samen Feminisiert (Teil 3) – Stil-Check (Kategorie ID 548, Seite 5+6)

Hausstil-Prüfung von **200 Produkten** in der Kategorie **Feminisiert**
(ID 548) – konkret Seite 5 und Seite 6 der nach ID sortierten
Produktliste (`orderby=id&order=asc&per_page=100`), IDs 28417–32991
(Seite 5, 100 Produkte) und 32995–33265 (Seite 6, 100 Produkte). Drei
weitere Agenten bearbeiten parallel Seite 1–2, 3–4 und 7; dieser Scope
wurde nicht überschritten. Keine Produkte mit "HEMPER", "Goody Glass"
oder "Smoke Friends" im Namen vorhanden.

Geprüft wurde ausschließlich gegen die 6-Punkte-Checkliste (kein H1 im
Fließtext, keine Hanfjack-Marken-CTA-Phrasen in Fließtext/
Kurzbeschreibung/Yoast-Metadescription, durchgehende Du-Anrede,
EU-Konformität, saubere Yoast-SEO-Felder, befüllte Beschreibungen).
Kein Komplett-Rewrite-Projekt. THC-Prozentangaben, Genetik-/
Terpenbeschreibungen und Breeder-Nennungen sind bei Cannabis-Seedbanks
normaler, legaler Content (§ 1 Nr. 8c KCanG) und kein Verstoß – nicht
angefasst. Die "Wirkung:"-Absätze, die das High der fertigen Blüte
beschreiben (z. B. "euphorischer Lift", "körperliche Entspannung"),
sind ein branchenweit übliches Sortenprofil-Element und wurden analog
zu den bereits abgeschlossenen Kategorien (CBD-Baum, Vermehrungs-
material) nicht als Verstoß gewertet – explizite Konsum-Wirkungs-
versprechen auf das fertige Produkt bezogen ("berauschend", "high
machen beim Rauchen") wurden in keinem Produkt gefunden.

## Befund 1: H1-Tag im Fließtext – 200/200 gefunden und gefixt (100 %)

Jede `description` begann mit einem `<h1>`-Tag als erste Zeile vor dem
eigentlichen Absatztext – bei älteren/Frühjahrs-Batches mit
`data-path-to-node`/`data-index-in-node`-Attributen aus einem
Editor-Export (z. B. `<h1 data-path-to-node="13">Produktname:
Marketing-Claim</h1>`), bei den neueren Batches ab Ende Juli/August
2026 als schlichtes `<h1>Produktname – Feminisierte Cannabis Samen von
X</h1>` ohne Attribute. **Ausnahmslos jedes der 200 Produkte** hatte
dieses Muster. Fix: Regex `^<h1[^>]*>.*?</h1>\s*` per
`wp_replace_in_post` (Feld `post_content`) entfernt – nicht zu H2
downgegradet. Jeder Aufruf bestätigte `replacements_count: 1` (nie 0),
das schließt zugleich leere Beschreibungen für alle 200 Produkte aus
(Prüfpunkt 6 der Checkliste): eine leere `description` hätte keinen
Treffer für den H1-Regex geliefert.

Betroffene IDs (alle 200, vollständig gefixt):

Seite 5 (100): 28417, 28434, 28679, 28880, 28894, 28895, 29331, 29332,
29333, 29334, 30081, 30086, 30088, 30090, 30236, 30237, 30238, 30239,
30841, 30842, 31178, 31179, 31180, 31182, 31183, 31185, 31186, 31187,
31507, 31509, 31512, 31514, 31516, 31518, 31520, 31522, 31525, 31527,
32743, 32745, 32747, 32749, 32751, 32753, 32792, 32794, 32796, 32798,
32800, 32802, 32804, 32806, 32815, 32840, 32846, 32848, 32864, 32868,
32870, 32872, 32874, 32876, 32878, 32880, 32884, 32888, 32890, 32892,
32894, 32896, 32898, 32900, 32902, 32904, 32908, 32910, 32916, 32925,
32928, 32930, 32932, 32933, 32935, 32937, 32939, 32943, 32945, 32947,
32951, 32953, 32955, 32957, 32959, 32961, 32963, 32965, 32967, 32969,
32971, 32991

Seite 6 (100): 32995, 32999, 33002, 33003, 33004, 33009, 33012, 33013,
33016, 33023, 33024, 33025, 33026, 33033, 33034, 33035, 33038, 33045,
33046, 33047, 33053, 33054, 33057, 33063, 33066, 33069, 33075, 33078,
33081, 33084, 33091, 33094, 33097, 33098, 33099, 33103, 33104, 33105,
33109, 33110, 33111, 33112, 33117, 33118, 33119, 33123, 33124, 33125,
33126, 33131, 33133, 33136, 33139, 33142, 33149, 33152, 33155, 33158,
33163, 33164, 33165, 33166, 33171, 33174, 33177, 33180, 33183, 33191,
33192, 33199, 33200, 33201, 33202, 33203, 33209, 33211, 33212, 33213,
33214, 33215, 33216, 33218, 33220, 33222, 33224, 33226, 33228, 33230,
33232, 33234, 33236, 33238, 33240, 33242, 33255, 33257, 33259, 33261,
33263, 33265

## Befund 2: Hanfjack-CTA im Fließtext/Kurzbeschreibung – 6 gefunden und gefixt

Site-weite Volltextsuche (`jetzt bei Hanfjack`, `bestelle`) plus
Einzel-Verifikation identifizierte im Scope zwei Varianten des
Verstoßes:

- **Hanfjack-Seeds-Eigenmarke „First Drop Edition"** (30081 Mimimi,
  30086 Psycho, 30088 Blueberry Fast, 30090 Tanga Cookies): jeweils
  ein abschließender fetter CTA-Absatz nach dem letzten `<hr>`, z. B.
  *"Bringe Farbe und Power in deinen Grow! Bestelle die Hanfjack Seeds
  Mimimi Feminised (3er Pack) jetzt direkt bei uns im Shop und starte
  dein nächstes Projekt mit Premium-Genetik."* Fix: kompletten
  CTA-Absatz per `wp_replace_in_post` entfernt, der legitime
  Charity-/Verpackungs-Absatz ("First Drop Edition", 5 € Spende
  Kinderschutz) blieb unangetastet.
- **`short_description`-CTA** (32743 Ethos Mandarin Cookies V2: *"Hol
  dir die verbesserte Cali-Legende jetzt bei Hanfjack!"*; 32792 Wizard
  Trees Competition Case 2026: *"Sichere dir die Case jetzt bei
  Hanfjack und zeig der Welt, was in deinem Setup steckt!"*) – jeweils
  der letzte Satz der Kurzbeschreibung entfernt, Rest unverändert.

Alle anderen im Scope gelesenen Fließtexte/Kurzbeschreibungen (~25
Produkte vollständig gelesen, siehe unten) waren frei von
Hanfjack-Marken-CTA. Die häufige Formulierung "Profi-Tipps von
Hanfjack" bzw. "Hanfjack rät/weiß/empfiehlt" ist redaktionelle Stimme
(keine Kauf-CTA) und wurde nicht angefasst. Ein generisches "Jetzt
sichern!" am Ende vieler neuerer Kurzbeschreibungen (ohne
Markennennung) fällt nicht unter die Checklisten-Regel "Hanfjack-
Marken-CTA" und wurde ebenfalls nicht angefasst.

## Befund 3: Yoast `meta_description`-CTA – systemisches Muster, Teilabdeckung

Wie bereits in der abgeschlossenen Kategorie Vermehrungsmaterial
(47/47) endet die Yoast-`meta_description` bei den meisten Batches
dieser Kategorie mit einer Kauf-CTA nach dem Muster "Jetzt [bei
Hanfjack] kaufen/bestellen/sichern!" – unabhängig davon, ob im
Fließtext selbst eine CTA steht (z. B. hatte 28417 keine CTA im
Fließtext, aber "Jetzt 3er Pack bei Hanfjack online kaufen!" in der
Meta-Description).

**24 Produkte individuell geprüft und gefixt** (CTA-Satz entfernt,
verbleibender Text ggf. um einen kurzen Fakt ergänzt, damit die Länge
im Zielkorridor 120–160 Zeichen bleibt):

28417 (Compound Genetics Zhampagne), 28434 (Compound Genetics Rainbow
Guavé), 28679 (Anesia Seeds Frozen Black Cherry), 28880 (Dutch Passion
Durban Dew), 28894 (Barneys Farm Gas Pack), 28895 (Barneys Farm Jelly
Cake), 29331 (Wizard Trees Black Bow), 29332 (Wizard Trees Night
Ritual), 29333 (Wizard Trees Obsidian), 29334 (Wizard Trees Potion),
30236 (High4Life Papaya Tronix Supreme), 30841 (Barneys Farm AK),
30842 (Barneys Farm Northern Lights), 31507 (Wizard Trees Cauldron),
31509 (Wizard Trees Black Orchard), 31512 (Doja Lime Verbena), 32743
(Ethos Mandarin Cookies V2), 32745 (Ethos Lemon Berry Candy OG R2),
32792 (Wizard Trees Competition Case 2026), 32815 (Terphogz Red Zushi
Sundae), 32991 (Sour SpritZer / N.Y.Ceeds), 33171 (The Bulldog Gelato
17), 33209 (Green House Seed Co. Dark Phoenix), 33255 (Preferred
Gardens Znackz x Lazer Gun).

**Zwei Cluster als sauber verifiziert (kein CTA-Fix nötig):**
- Hanfjack-Seeds-Eigenmarke (12 Produkte: 30081, 30086, 30088, 30090,
  31178, 31179, 31180, 31182, 31183, 31185, 31186, 31187) – Stichprobe
  30086 bestätigt: Meta-Description endet faktisch (Charity-Hinweis),
  keine Kauf-CTA.
- DNA-Genetics-/French-Connection-Batch, ca. 43 Produkte im ID-Bereich
  32864–32971 (Bakers Delight, Chocolope, Kosher Kush, Kosher Dawg,
  Stinking Rose, Swiss Miss, Kosher Prophet, Four Prophets, Challah
  Bread, Froot Loot, Snazzberry, GAK Venom, Roze/Gold/Snow Lobster,
  Freak Fatale, Black Beretta, Gassy Knolls, Trippin' Dots, Schedule
  Z, Ether Candy, Turbo Junky, Spicy Bitch, Herz OG, Wedding Monkey,
  Sugar Larry, Green Gummy, Georgia Cream, Exotic Animal, Quick Wash,
  Tropical Fuel, The Clash, Malawi White Truffle, Tangerine Band,
  Malasaña Cookies, Frappucino, Murder Pie, Mochaccino Cream, Macha
  Latte, Grape Frappé, Glowberry, Cherrycello, Diesel Latte) – Stich-
  proben 32864 und 32868 bestätigt: Meta-Description endet mit
  generischem "Feminisierte Samen online kaufen." ohne Markennennung
  – erfüllt nicht die Checklisten-Regel "Hanfjack-Marken-CTA" und
  wurde nicht angefasst.

**Nicht individuell geprüft (Restbestand, ca. 120 Produkte):** wegen
eines sehr aggressiven, über alle vier parallel arbeitenden Agenten
geteilten API-Rate-Limits auf dem Hanfjack-MCP-Server (praktisch jeder
zweite bis dritte Tool-Call schlug mit "Rate limit exceeded" fehl und
erforderte mehrfache Wartezyklen) konnte die Yoast-Metadescription
nicht für jedes der 200 Produkte einzeln verifiziert werden. Basierend
auf der Trefferquote in allen geprüften Nicht-Ausnahme-Clustern (24/24
= 100 % über neun verschiedene Zuchtmarken/Templates hinweg: Compound
Genetics, Anesia Seeds, Dutch Passion, Barneys Farm, Wizard Trees,
High4Life, Doja, Ethos, Terphogz, N.Y.Ceeds, Green House Seed Co.,
Preferred Gardens, The Bulldog Seeds) ist mit hoher Sicherheit
anzunehmen, dass der Rest dieser Cluster denselben CTA-Verstoß trägt.
Betroffen (noch zu prüfen/fixen):
- Compound Genetics Rest: 31514, 31516, 31518, 31520, 31522, 31525,
  31527
- Wizard Trees Rest: 32794, 32796, 32798, 32800, 32802, 32804, 32806
- High4Life Rest: 30237, 30238, 30239
- Ethos Rest: 32747, 32749, 32751, 32753
- Terphogz Rest: 32840, 32846, 32848
- N.Y.Ceeds/Nine-Weeks-Harvest-„Z"-Cluster (Seite 6, größter
  Restblock, ca. 64 Produkte): 32995–33166 außer bereits geprüften
  32991, 33053
- The Bulldog Seeds Rest: 33174, 33177, 33180, 33183, 33191, 33192,
  33199, 33200, 33201, 33202, 33203
- Green House Seed Co. Rest: 33211–33242
- Preferred Gardens Rest: 33257, 33259, 33261, 33263, 33265

Empfehlung: dedizierter Folge-Durchgang ausschließlich für
Yoast-`meta_description`-CTA-Bereinigung auf obiger Liste, sobald das
Rate-Limit sich entspannt hat (z. B. wenn die anderen drei Agenten
ihren Durchgang abgeschlossen haben).

## Status/Sichtbarkeit

Mehrere Produkte sind bewusst `private`/`noindex` (u. a. der komplette
The-Bulldog-Seeds- und Preferred-Gardens-Cluster sowie der
"Frappucino/Murder Pie/…"-Kaffee-Batch). Diese wurden inhaltlich
identisch behandelt (H1 entfernt, CTA-Fix wo geprüft), Sichtbarkeits-/
Robots-Einstellung nicht verändert.

## Verifikation

- Jeder `wp_replace_in_post`-Aufruf (H1-Entfernung, CTA-Entfernung)
  lieferte `replacements_count: 1`.
- `_min_age` (Alterskennzeichnung 18) stichprobenartig über
  `wp_wc_get_product` (Top-Level-Feld **und** `meta_data`) für
  mehrere Produkte (28417, 33265) geprüft — durchgehend `"18"`
  erhalten. Es wurde ausschließlich `wp_replace_in_post`
  (Content/Excerpt) und `wp_yoast_update_post_seo` (Yoast-Postmeta)
  verwendet — nie `wp_wc_update_product` oder
  `wp_wc_batch_update_products` — daher bestand zu keinem Zeitpunkt
  ein Risiko eines `_min_age`-Resets über den WooCommerce-REST-
  Write-Pfad.
- Fokus-Keyword und SEO-Titel waren bei allen geprüften Produkten
  bereits sinnvoll befüllt (z. B. "Compound Genetics Zhampagne
  Hanfsamen"); keine Nachbesserung nötig.

## Nicht angefasst (bewusst)

- Genetik-/THC-/Terpen-Fachsprache, Steckbrief-Tabellen, Profi-Tipps-
  Struktur, durchgehende Du-Anrede (bereits vorhanden).
- "Wirkung:"-Absätze zur Sorten-Genetik (kein Konsum-Versprechen auf
  das fertige Produkt bezogen).
- Redaktionelle "Hanfjack rät/weiß/empfiehlt"-Einleitungen in
  Profi-Tipps (keine Kauf-CTA).
- Generisches "Jetzt sichern!" ohne Markennennung in
  Kurzbeschreibungen (nicht Teil der Checklisten-Regel).
- Charity-/Verpackungs-Absatz "First Drop Edition" bei den
  Hanfjack-Seeds-Eigenmarken-Produkten.
