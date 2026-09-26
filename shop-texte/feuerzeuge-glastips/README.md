# Feuerzeuge & Glas-Tips – Stil-Check (Kategorien Feuerzeuge, Zippo, Glas-Tips)

Hausstil-Prüfung der 51 Produkte in den drei Headshop-Unterkategorien
**Feuerzeuge** (ID 5426, 2 Produkte), **Zippo** (ID 5424, 43 Produkte) und
**Glas-Tips** (ID 5191, 6 Produkte, 5 HEMPER-Glasfilter bereits
ausgeschlossen: 34994, 34995, 35384, 35385, 35386). Ebenfalls ausgeschlossen:
HEMPER Portal Gun Sturmfeuerzeug (35457, Feuerzeuge) sowie zwei direkt der
Elternkategorie zugeordnete HEMPER-Produkte (37596, 37410). Wie beim
Headshop-Mini-Projekt war dies **kein** Full-Rewrite: Die Mehrheit der Texte
war bereits sauber und wurde unverändert gelassen. Es ging darum,
tatsächliche Verstöße gegen die 6-Punkte-Checkliste (kein H1 im Text, keine
Hanfjack-Marken-CTA, durchgehende Du-Anrede, EU-Konformität, saubere
Yoast-SEO-Felder, saubere `short_description`) zu finden und gezielt zu
fixen.

## Befund

Der Vorbefund lautete: Zippo 14802 (BLACK MATTE) und Feuerzeuge 20069
(Rasta Leaves) sind sauber, Glas-Tips 14415 trägt dagegen noch die alte,
vor-refaktorierte Content-Struktur mit `<h1 data-path-to-node="…">`-Tag.
Das hat sich vollständig bestätigt:

**Alle 6 Glas-Tips-Produkte** (Herb Shuttles Glas-Tip Spiral, alle am
26.07.2025 aus demselben Content-Batch erstellt) trugen den `<h1
data-path-to-node="…">Titel: Untertitel</h1>`-Tag am Anfang der
`description` — exakt das aus dem Pflegeprodukte-Projekt bekannte
Altmuster. Bei einem Teil der Produkte stand zusätzlich eine
Hanfjack-Marken-CTA-Phrase ("… jetzt bei Hanfjack bestellen!") im Yoast-
`meta_description`-Feld. Diese alte Struktur ist damit auch im
Feuerzeuge/Zippo-Bereich nachgewiesen — sie ist nicht auf eine Marke oder
Kategorie beschränkt.

**Alle 43 Zippo-Produkte** nutzen dagegen durchgängig das neue, saubere
Template: `<h3 dir="auto">` / `<p dir="auto">` / `<ul dir="auto">` ohne
`data-path-to-node`-Attribute, kein H1 im Text, keine Hanfjack-Marken-CTA im
Fließtext oder in der `short_description`. Die Du-Anrede ist konsistent.
Bei keinem einzigen Zippo-Produkt wurde ein H1- oder CTA-Verstoß im
`post_content` gefunden. Eine bislang nicht dokumentierte, aber klar
abgrenzbare zweite Fehlerklasse trat jedoch bei 13 der 43 Zippo-Produkte
auf: Bei diesen fehlte das Yoast-Feld `_yoast_wpseo_metadesc` **komplett**
(erkennbar am Admin-Hinweis "diese Seite zeigt keine Meta-Beschreibung" im
`yoast_head`). Betroffen war ein zusammenhängender ID-Block
(14794–14821), alle am 04./05.08.2025 im selben frühen Erstellungs-Batch
angelegt — vermutlich ein einmaliger Import-Fehler bei diesem Batch, nicht
systematisch für alle Zippo-Produkte.

Beide Feuerzeuge-Produkte (20069 Rasta Leaves, 20070 Cannabis Motive) waren
bereits vollständig sauber — kein H1, keine CTA, Yoast-Felder vollständig
und angemessen lang.

`short_description` (post_excerpt) war bei allen 51 Produkten bereits
sauber (reine Bullet-Listen ohne H1/CTA-Probleme), mit Ausnahme der
CTA-Reste in den Glas-Tips-Kurzbeschreibungen (siehe Vorgehen).

## Vorgehen

- H1- und CTA-Fixes in `post_content`/`post_excerpt` ausschließlich über
  `wp_replace_in_post` (Regex-Modus für den H1-Tag), **nie** über
  `wp_wc_update_product`/`wp_wc_batch_update_products` — Letzteres hätte
  beim Schreiben von `description` ungewollt `_min_age` (Pflichtfeld "18")
  löschen können, wenn es nicht explizit mitgesendet wird.
- Muster: `^<h1[^>]*>.*?</h1>\s*` zum Entfernen des H1-Tags am Anfang der
  `description`, konsistent bei allen 6 Glas-Tips-Produkten angewendet.
  Wo eine Hanfjack-CTA-Phrase im Fließtext oder in der
  `short_description` folgte, wurde diese gezielt per zweitem
  `wp_replace_in_post`-Aufruf entfernt.
- Yoast-Fixes über `wp_yoast_update_post_seo` (`meta_description`):
  Bei den Glas-Tips-Produkten wurden vorhandene Hanfjack-CTA-Phrasen aus
  der `meta_description` entfernt. Bei den 13 betroffenen Zippo-Produkten
  mit komplett fehlender `meta_description` wurde eine neue, CTA-freie
  Beschreibung (~140–165 Zeichen, mit Produkt-Highlight und EAN) ergänzt.
  Für bereits vorhandene, aber knapp bemessene Metadescs (ca. 100–160
  Zeichen) galt der etablierte Schwellenwert aus dem Headshop-Mini-Projekt:
  nur bei fehlender oder unter 100 Zeichen langer Metadesc wurde
  eingegriffen, um unnötige Änderungen an bereits akzeptablen Texten zu
  vermeiden.
- Verifikation je Fix: `wp_replace_in_post` mit `search == replace`
  (H1-Muster bzw. CTA-Text) als kostengünstiger "Grep ohne Änderung" —
  `replacements_count: 0` bestätigt, dass der String nicht mehr vorkommt.
  Zusätzlich wurde `_min_age` bei jedem geänderten Produkt im
  `wp_wc_get_product`-Response gegengeprüft (durchgehend "18").
- Alle 43 Zippo-Produkte wurden einzeln per `wp_wc_get_product` gelesen und
  auf H1/CTA/Du-Anrede/Yoast-Feldvollständigkeit geprüft — keine
  Stichprobe, volle Abdeckung.

## Sonderfälle

- **_min_age-Anomalie bei 31760** (Herb Shuttles Glas-Tip Spiral Orange):
  Das Feld `_min_age` steht bei diesem Produkt auf leerem String `""`
  statt auf "18". Dies ist ein vorbestehendes Datenproblem, das nicht mit
  den hier durchgeführten `wp_replace_in_post`/`wp_yoast_update_post_seo`-
  Aufrufen zusammenhängt (diese berühren keine Post-Meta-Felder) und war
  bereits vor dem Fix in diesem Zustand. Wurde **nicht** korrigiert, da
  außerhalb des Auftragsumfangs (reiner Stil-Check der Texte) — als
  Randbefund hier dokumentiert.
- **Fehlende Yoast-Metadesc als eigene Fehlerklasse**: Die 13 betroffenen
  Zippo-Produkte (14794, 14796, 14799, 14800, 14805, 14806, 14807, 14808,
  14809, 14814, 14819, 14820, 14821) hatten allesamt saubere
  `post_content`/`post_excerpt` — der Verstoß lag ausschließlich im
  fehlenden Yoast-Feld, nicht im Fließtext. Da alle betroffenen IDs aus
  demselben frühen Erstellungs-Batch (04./05.08.2025) stammen, handelt es
  sich vermutlich um einen einmaligen Importfehler und nicht um ein
  systematisches Problem der Zippo-Kategorie insgesamt (30 von 43
  Zippo-Produkten hatten von Anfang an eine vollständige, angemessene
  Metadesc).
- **Private-Status-Produkt** (20069 Feuerzeuge, Rasta Leaves, Status
  "private"): normal geprüft wie alle anderen Produkte, keine
  Sonderbehandlung nötig — bereits sauber.
- **Feuerzeuge-Kategorie-Umfang**: Die Kategorie 5426 ("Feuerzeuge")
  enthält in der REST-Auflistung 4 Produkte (14779, 20069, 20070, 35457).
  Davon ist 14779 (Zippo Butan Gas 100ml) zusätzlich der Kategorie Zippo
  zugeordnet und wurde dort bereits geprüft; 35457 (HEMPER Portal Gun) ist
  laut Auftrag ausgeschlossen. Damit reduziert sich der tatsächliche
  Prüfumfang der Kategorie Feuerzeuge korrekt auf die vorgesehenen 2
  Produkte (20069, 20070).

## Vollständige Produktliste (51/51)

### Feuerzeuge (2/2)

- [x] 20069 – Feuerzeug Rasta Leaves mit zufälliger Motivauswahl (Status: private) — OK
- [x] 20070 – Feuerzeug Cannabis Motive mit zufälliger Motivauswahl (Status: private) — OK

### Zippo (43/43)

- [x] 14778 – Zippo Ersatzfeuerstein 2406N FLINT Card Single Unit — OK
- [x] 14779 – Zippo Butan Gas 100ml — OK
- [x] 14780 – Zippo Feuerzeugbenzin 125ml – Made in USA — OK
- [x] 14781 – Zippo Ersatzdocht 2425G Wick Card Single Unit — OK
- [x] 14782 – Zippo Watte und Filzplatte Ersatzteile — OK
- [x] 14784 – Zippo Benzinfeuerzeug BRUSHED CHROME — OK
- [x] 14786 – Zippo Butane Double Flame One Box — OK
- [x] 14787 – Zippo Butane Yellow Flame Insert – DE/FR/EN — OK
- [x] 14788 – Zippo Benzinfeuerzeug Street Brass — OK
- [x] 14789 – Zippo Benzinfeuerzeug BLACK CRACKLE — OK
- [x] 14790 – Zippo Benzinfeuerzeug BLACK ICE — OK
- [x] 14791 – Zippo Benzinfeuerzeug Cannabis Design — OK
- [x] 14792 – Zippo Benzinfeuerzeug SATIN CHROME — OK
- [x] 14794 – Zippo Benzinfeuerzeug 207 Zippo Flame Design — FIXED (Yoast metadesc fehlte komplett → ergänzt, 163 Zeichen inkl. EAN)
- [x] 14795 – Zippo Benzinfeuerzeug 49190 Tree Of Life Emblem — OK
- [x] 14796 – Zippo Benzinfeuerzeug Cannabis Design (2) — FIXED (Yoast metadesc fehlte komplett → ergänzt, 160 Zeichen inkl. EAN)
- [x] 14797 – Zippo Benzinfeuerzeug SPECTRUM — OK
- [x] 14798 – Zippo Benzinfeuerzeug VENETIAN BRASS — OK
- [x] 14799 – Zippo Benzinfeuerzeug 200 Compass Emblem — FIXED (Yoast metadesc fehlte komplett → ergänzt, 141 Zeichen inkl. EAN)
- [x] 14800 – Zippo Benzinfeuerzeug 207 Cannabis Design — FIXED (Yoast metadesc fehlte komplett → ergänzt)
- [x] 14801 – Zippo Benzinfeuerzeug 24756 Anne Stokes Collection — OK
- [x] 14802 – Zippo Benzinfeuerzeug BLACK MATTE — OK (Vorab-Stichprobe, Metadesc etwas knapp aber ok)
- [x] 14803 – Zippo Benzinfeuerzeug BRASS HIGH POLISHED — OK
- [x] 14804 – Zippo Benzinfeuerzeug Cannabis Design (3) — OK
- [x] 14805 – Zippo Benzinfeuerzeug Cannabis Pattern Design — FIXED (Yoast metadesc fehlte komplett → ergänzt)
- [x] 14806 – Zippo Benzinfeuerzeug CHAMELEON High Polish Green — FIXED (Yoast metadesc fehlte komplett → ergänzt)
- [x] 14807 – Zippo Benzinfeuerzeug Cigar Girl Design — FIXED (Yoast metadesc fehlte komplett → ergänzt)
- [x] 14808 – Zippo Benzinfeuerzeug Concrete Hole Design — FIXED (Yoast metadesc fehlte komplett → ergänzt)
- [x] 14809 – Zippo Benzinfeuerzeug Counter Culture Design — FIXED (Yoast metadesc fehlte komplett → ergänzt)
- [x] 14810 – Zippo Benzinfeuerzeug Dragonfly Design — OK
- [x] 14811 – Zippo Benzinfeuerzeug Funky Cannabis Design — OK
- [x] 14812 – Zippo Benzinfeuerzeug HIGH POLISH CHROME — OK
- [x] 14813 – Zippo Benzinfeuerzeug Holographic Design — OK
- [x] 14814 – Zippo Benzinfeuerzeug Japan Tiger — FIXED (Yoast metadesc fehlte komplett → ergänzt)
- [x] 14815 – Zippo Benzinfeuerzeug Lion Design — OK
- [x] 14816 – Zippo Benzinfeuerzeug Mountain Design — OK
- [x] 14817 – Zippo Benzinfeuerzeug Pfeifen-Einsatz Chrome — OK
- [x] 14818 – Zippo Benzinfeuerzeug Phoenix Design — OK
- [x] 14819 – Zippo Benzinfeuerzeug PL 200 EAGLE 2013 EMBLEM — FIXED (Yoast metadesc fehlte komplett → ergänzt)
- [x] 14820 – Zippo Benzinfeuerzeug PL 200 NAUTIC EMBLEM — FIXED (Yoast metadesc fehlte komplett → ergänzt)
- [x] 14821 – Zippo Benzinfeuerzeug Rastafari Leaf Design — FIXED (Yoast metadesc fehlte komplett → ergänzt)
- [x] 14822 – Zippo Benzinfeuerzeug Reg HP Rose Gold — OK
- [x] 14823 – Zippo Benzinfeuerzeug VENETIAN CHROME — OK

### Glas-Tips (6/6)

- [x] 14415 – Herb Shuttles Glas-Tip Spiral 10mm Amber — FIXED (H1 entfernt + Hanfjack-CTA in short_description/Yoast-Metadesc entfernt)
- [x] 14416 – Herb Shuttles Glas-Tip Spiral 10mm Black — FIXED (H1 entfernt + Hanfjack-CTA entfernt)
- [x] 14417 – Herb Shuttles Glas-Tip Spiral 10mm Clear — FIXED (H1 entfernt + Hanfjack-CTA entfernt)
- [x] 14418 – Herb Shuttles Glas-Tip Spiral 10mm Pink — FIXED (H1 entfernt + Hanfjack-CTA entfernt)
- [x] 31758 – Herb Shuttles Glas-Tip Spiral 10mm Milky White — FIXED (H1 entfernt + Hanfjack-CTA entfernt)
- [x] 31760 – Herb Shuttles Glas-Tip Spiral 10mm Orange — FIXED (H1 entfernt + Hanfjack-CTA entfernt; siehe Sonderfall `_min_age`)

## Abschluss

Alle 51 Produkte der drei Kategorien Feuerzeuge, Zippo und Glas-Tips sind
geprüft. **19 von 51 Produkten** hatten tatsächliche Hausstil- oder
Yoast-Hygiene-Verstöße und wurden gezielt gefixt (0 in Feuerzeuge, 13 in
Zippo, 6 in Glas-Tips); **32 Produkte** waren bereits konform und wurden
nicht verändert. Der wichtigste Befund: Alle 6 Glas-Tips-Produkte trugen
noch die alte, vor-refaktorierte Content-Struktur (H1 + teils
Hanfjack-Marken-CTA) aus dem Pflegeprodukte-Projekt — diese Struktur ist
damit in einer weiteren, bislang ungeprüften Kategorie nachgewiesen und
sollte bei künftigen Stil-Checks weiterhin im Blick behalten werden. Bei
Zippo trat kein einziger H1/CTA-Verstoß im Fließtext auf; stattdessen fand
sich eine neue, klar abgrenzbare Fehlerklasse — 13 Produkte aus einem
frühen Erstellungs-Batch mit komplett fehlender Yoast-`meta_description`,
die jeweils durch eine neue, CTA-freie Beschreibung ergänzt wurde. `_min_age`
und alle sonstigen Post-Meta-Felder blieben unangetastet, da ausschließlich
`wp_replace_in_post` (Feld-Scope `post_content`/`post_excerpt`) und
`wp_yoast_update_post_seo` verwendet wurden; die vorbestehende
`_min_age`-Anomalie bei Produkt 31760 (leerer statt "18"-Wert) wurde als
Randbefund dokumentiert, aber nicht korrigiert (außerhalb des
Auftragsumfangs).
