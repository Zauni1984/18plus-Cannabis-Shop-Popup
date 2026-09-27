# Samen – Regular – Stil-Check (WooCommerce-Kategorie 549)

Hausstil-Prüfung aller 71 Produkte in Kategorie **Regular** (ID 549, Status
`any`) — Cannabis-Vermehrungsmaterial nach § 1 Nr. 8c KCanG. Geprüft wurde
gegen die 6-Punkte-Checkliste (kein H1 im Fließtext, keine Hanfjack-Marken-
CTA, durchgehende Du-Anrede, EU-Compliance — Genetik/THC-Fachsprache
erlaubt, nur explizite Konsum-/Wirkungsversprechen auf das fertige Produkt
sind ein Verstoß —, saubere Yoast-SEO-Felder, keine leeren Beschreibungen).
Marken-Ausschluss (HEMPER, Goody Glass, Smoke Friends) kam in keinem der 71
Produkte vor. Zwei Text-Generationen: 33 neuere Produkte (Ethos Genetics,
The Cali Connection, James Loud Genetics, Grand Daddy Genetics, Ace Seeds,
Brothers Grimm) mit sachlichem Zuchtlinien-Stil, und 38 ältere Produkte
(Cipher Genetics, DNA Genetics, Terphogz, Doja, Barneys Farm, Dutch
Passion, weitere Ace Seeds) im werblicheren "Profi-Tipps"-Format.

## Befund

### H1 im Fließtext (69/71 betroffen, alle gefixt)

Durchgängiges Muster wie in den bereits abgeschlossenen Kategorien: entweder
einfaches `<h1>Titel</h1>` (die 4 Cipher-Genetics-Produkte ohne Attribute)
oder `<h1 data-path-to-node="…">…</h1>` bzw. `<h1 class="wp-block-heading">`
mit KI-Editor-Metadaten (alle übrigen). Fix durchgehend: `^<h1[^>]*>.*?</h1>\s*`
per Regex auf `post_content` entfernt (nicht zu H2 herabgestuft). Alle
Treffer mit genau 1 Ersetzung bestätigt. Nur 2 Produkte hatten von
vornherein kein H1 (15711 Doja Strawberry Zkillato BX1 nutzte H3, 15296
Doja Kerosene Kreme H2 als Titel-Heading — kein Verstoß, da nicht H1).

### Hanfjack-CTA in Yoast-`meta_description` (27/71 betroffen, gefixt)

Durchgängiges Muster bei einem Teil der älteren Produkte: "Jetzt bei
Hanfjack (online) kaufen/bestellen!" — betraf die komplette Terphogz-Serie
(12), alle 7 Barneys-Farm-Regular-Produkte, beide Dutch-Passion-Produkte,
Widow Remedy/Pineapple Haze, Afghan Hash Plant, Thai x Panama sowie Doja
Melon Melange/Neon Wormz. Alle betroffenen Metadescriptions wurden durch
eine CTA-freie Fassung mit denselben Fakten (Genetik, THC-%, Blütezeit,
Aroma) ersetzt, innerhalb 120–160 Zeichen. Die 8 Cipher-Genetics-/DNA-
Genetics-Produkte hatten zwar "Reguläre Samen online kaufen." in der
Metadescription, aber ohne Markennennung — kein Verstoß nach Checkliste
und daher unangetastet. Die 33 neueren Ethos-/Cali-Connection-/James-Loud-/
Grand-Daddy-/Ace-Seeds-/Brothers-Grimm-Produkte hatten bereits saubere,
CTA-freie Metadescriptions.

### Hanfjack-CTA im Fließtext (2/71 betroffen, gefixt)

**15295 (Doja Melon Melange)** und **15713 (Doja Neon Wormz)** endeten mit
einem expliziten Kauf-Absatz ("Bestelle … jetzt bei Hanfjack — Deinem
Experten für exklusive Genetik!"). Absatz inkl. vorangehendem `<hr>` per
Regex entfernt.

### EU-Compliance — explizite Konsum-/Wirkungsversprechen (7/71 betroffen, gefixt)

Der wichtigste inhaltliche Befund. Anders als reine Genetik-/THC-Angaben
(erlaubt) fanden sich bei 7 Produkten eigene "Wirkung"-Abschnitte oder
Formulierungen, die explizit den psychoaktiven Konsum-Effekt des fertigen
Produkts bewarben ("High", "Euphorie", "berauschend" u. ä.) — ein
EU-Compliance-Verstoß analog zu Konsumprodukten wie Vaporizern:

- **30834 (Barneys Farm G13 Haze Regular):** eigene Sektion "Wirkung:
  Kreativer Auftrieb und mentale Klarheit" ("liefert ein High …", "schenkt
  dir einen sofortigen mentalen Auftrieb", "Euphorie ohne Trägheit") komplett
  entfernt.
- **30833 (Barneys Farm Acapulco Gold Regular):** Sektion "Wirkung: Mentale
  Klarheit und euphorische Energie" ("klassische Sativa-High: zerebral,
  motivierend und absolut euphorisch") entfernt; zusätzlich die
  `short_description`-Zeile "Klares, euphorisches und energetisierendes
  cerebrales High" durch eine neutrale Genetik-Aussage ersetzt.
- **28687 (Ace Seeds Thai x Panama Regular):** am stärksten betroffenes
  Produkt — "psychedelisches High" stand im Fließtext-Intro, in einem
  Bullet-Punkt ("Visionäres High: … euphorisches, psychedelisches
  Erlebnis"), in einer eigenen Wirkung-Sektion, in der `short_description`
  und sogar im Yoast-SEO-Titel ("… | Psychedelisch") sowie im
  Fokus-Keyword. Alle sechs Fundstellen neutralisiert (Fokus auf
  Terpenprofil/Genetik statt Konsumeffekt); SEO-Titel auf "… | 100 % Sativa
  F1-Hybrid" geändert.
- **11786 (Barneys Farm Pineapple Haze Regular):** Sektion "Wirkung: Pure
  Energie und mentale Klarheit" sowie ein Bullet ("Brutale Sativa-Power:
  Liefert stundenlange Euphorie …") entfernt bzw. neutralisiert; zwei
  Effekt-Formulierungen in der `short_description` ("Euphorisch, kreativ &
  energetisch"; "Klassisches Haze-High") ebenfalls ersetzt.
- **15296 (Doja Kerosene Kreme, privat/Draft):** Sektion "Wirkung"
  ("Euphorisch: Stimmungsaufhellend & gesellig", "Entspannend …",
  "Glücklich …") entfernt, dazugehörige Zeile im Sortenprofil-Table sowie
  Bullet in der `short_description` ebenfalls entfernt.
- **15711 (Doja Strawberry Zkillato BX1, privat/Draft):** Sektion "Wirkung"
  ("körperlich entspannend und beruhigend", "leichte Euphorie") entfernt,
  dazu ein abschließender Effekt-Satz ("Eine Sorte, die den Körper
  entspannt …").
- **15715 (Doja Zkittlez x Coffin Candy, privat/Draft):** Sektion "Wirkung"
  ("entspannende Indica-Basis … euphorischer Sativa-Kick") entfernt.

Reine Genetik-Fachsprache (THC-%, Terpene, Blütezeit, Ertrag, Aroma) blieb
in allen Produkten unangetastet — das betrifft auch die übrigen Barneys-
Farm-Regular-Produkte (White Widow, Skunk #1, Northern Lights, Master Kush,
Hindu Kush), die einen neutralen "Sortencharakter"-Abschnitt statt einer
"Wirkung"-Sektion verwenden und keinen Fix benötigten.

### Fehlende Yoast-`meta_description` / Fokus-Keyword (3/71)

Die drei privaten/Draft-Doja-Produkte (15715, 15711, 15296) hatten
überhaupt keine `meta_description` gesetzt und einen CTA-artigen SEO-Titel
("… Samen kaufen | Hanfjack"). Alle drei erhielten einen neuen SEO-Titel im
Hausstil ("Produktname | Regular Samen"), eine neue Metadescription
(120–160 Zeichen, faktenbasiert) und ein Fokus-Keyword (Produktname).

### Leere Produktbeschreibungen

Keine gefunden — alle 71 Produkte hatten bereits substanziellen Fließtext.

## Nicht behoben / offene Punkte

- **Varianten** der `type: "variable"`-Produkte (33 der neueren Produkte)
  wurden laut Vorgabe nicht einzeln geprüft — nur der Elternprodukttext.
- `min_age` wurde stichprobenartig nach Abschluss aller Edits bei 3
  Produkten unterschiedlichen Fix-Typs verifiziert (34929: nur H1-Fix,
  30834: Wirkung-Sektion entfernt, 28687: umfangreichster Fix inkl.
  Fokus-Keyword) und blieb in allen Fällen korrekt auf `"18"`. Da
  ausschließlich `wp_replace_in_post` (Text-/Excerpt-Felder) und
  `wp_yoast_update_post_seo` (Yoast-Postmeta) verwendet wurden — nie
  `wp_wc_update_product` oder `wp_wc_batch_update_products` —, besteht
  strukturell kein Risiko einer `min_age`-Rücksetzung.
- Bei 2 der 3 privaten Doja-Produkte wurde je ein zusätzlicher, weniger
  eindeutiger "Raucherlebnis"-Satz (15711, ohne explizites Wirkversprechen
  wie "High"/"Euphorie") bewusst NICHT entfernt, da er sich auf Aroma-
  Intensität statt auf einen psychoaktiven Effekt bezieht.

## Zusammenfassung

| Fix-Typ | Betroffene Produkte |
|---|---|
| H1 im Fließtext entfernt | 69 / 71 |
| Hanfjack-CTA in Yoast-`meta_description` entfernt | 27 / 71 |
| Hanfjack-CTA im Fließtext entfernt | 2 / 71 |
| Explizite Konsum-/Wirkungsversprechen entfernt (EU-Compliance) | 7 / 71 |
| Fehlende Yoast-Felder ergänzt (Titel/Description/Fokus-Keyword) | 3 / 71 |
| Leere Produktbeschreibung neu geschrieben | 0 / 71 |

Alle 71 Produkte wurden gemäß Aufgabenstellung gegen die vollständige
Checkliste geprüft. Produkte, die oben nicht erwähnt sind (u. a. die
gesamte 8-teilige Terphogz-Serie ex Blooberry/Z3, Dutch-Passion-Serie),
hatten außer dem H1-/CTA-Standardmuster keine weiteren Verstöße.
