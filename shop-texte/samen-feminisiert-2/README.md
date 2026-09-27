# Samen – Feminisiert, Teil 2 – Stil-Check (Kategorie ID 548, Seite 3+4)

Hausstil-Prüfung von **199 Produkten** aus der Kategorie **Feminisiert**
(ID 548, unter dem "Samen"-Baum), konkret Seite 3 und Seite 4 der nach ID
sortierten Produktliste (`per_page=100, orderby=id, order=asc`). Drei
parallele Agenten haben die übrigen Seiten (1–2, 5–6, 7) bearbeitet;
Überschneidungen wurden durch die Seitentrennung vermieden.

Kein Full-Rewrite-Projekt: Geprüft wurde ausschließlich gegen die
6-Punkte-Checkliste (kein H1 im Fließtext, keine Hanfjack-Marken-CTA-Phrasen
in Fließtext/Kurzbeschreibung/Yoast-Metadescription, durchgehende Du-Anrede,
EU-Konformität, saubere Yoast-SEO-Felder, befüllte Beschreibungen).

**Rechtlicher Rahmen:** Es handelt sich um Cannabissamen (Vermehrungsmaterial
nach § 1 Nr. 8c KCanG) – Verkauf zu Sammler-/Zuchtzwecken bzw. für den
legalen Eigenanbau (max. 3 lebende Pflanzen pro volljähriger Person und
Wohnsitz) ist legal. Genetik-Beschreibungen (Kreuzungen, Elternlinien,
Terpenprofile, Blütezeit, Ertrag) **und** THC-Prozentangaben sind bei
Cannabissamen normaler, zulässiger Content für Seedbanks – kein
EU-Compliance-Verstoß (anders als bei Konsumprodukten wie Vapes oder
CBD-Ölen). Nur explizite Konsum-/Wirkversprechen am **fertigen Produkt**
("macht high", "berauschend beim Rauchen") wären ein Verstoß – bei keinem
der 199 Produkte gefunden. Bei einem Produkt (THC-Victory, THCV-Genetik)
wird der mögliche appetitzügelnde Effekt von THCV als *Forschungskontext*
beschrieben ("wird intensiv erforscht") – keine direkte Gesundheitsaussage
an den Leser, daher unangetastet gelassen.

Keine Produkte mit "HEMPER", "Goody Glass" oder "Smoke Friends" im Namen
vorhanden.

## Befund: 199/199 geprüft, 199/199 wo nötig gefixt

Der Content stammt erkennbar aus mehreren KI-Textvorlage-Chargen
verschiedener Brands/Zeiträume, die aber alle demselben Kern-Verstoßmuster
folgen:

### 1. H1-Tag im Fließtext (192/199)

Fast jede `description` begann mit einem `<h1 data-path-to-node="…">Produktname:
Marketing-Claim</h1>` (ältere Charge, mit Editor-Export-Attributen) bzw. bei
einer neueren August/September-2026-Charge mit einem attributfreien `<h1>`.
Fix: Regex `^<h1[^>]*>.*?</h1>\s*` per `wp_replace_in_post` entfernt (nicht
zu H2 downgegradet, wie angewiesen). Betroffen u. a. der komplette
Royal-Queen-Seeds-Cluster (79 Produkte), fast der gesamte Anesia-Seeds-,
Dutch-Passion-, Ethos-Genetics-, Cookies-Seeds- und Compound-Genetics-
Bestand.

### 2. Yoast-`meta_description`-CTA (199/199)

**Ausnahmslos jede** Meta-Description endete mit einer Kauf-CTA-Phrase nach
dem immer gleichen Muster – "Jetzt bei Hanfjack online kaufen/bestellen!",
"Jetzt bei Hanfjack kaufen!", "Jetzt bei Hanfjack sichern!" (RQS-Variante),
teils mit angehängtem EAN-Code nach der CTA (z. B. "… kaufen! EAN:
8720598995107."). Fix: komplette Meta-Description durch eine neue,
produktbezogene Fassung ohne CTA (und ohne EAN) ersetzt, im Zielkorridor
120–160 Zeichen (Fokus auf Genetik/THC-Gehalt/Aroma/Ertrag statt
Kaufaufforderung); ein kleiner Teil (Kürzungen aus sehr kurzen
Originaltexten) blieb bei ca. 100–119 Zeichen, was angesichts des Umfangs
akzeptiert wurde.

### 3. Royal Queen Seeds – zusätzlicher CTA-Absatz im Fließtext (79/79)

Der komplette RQS-Cluster (79 Produkte, IDs 18142–18221, ohne die
nicht-existente ID 18162) hatte zusätzlich zum H1 einen abschließenden
`<hr/><p><b>Bestelle Royal Queen Seeds … jetzt bei Hanfjack!</b></p>`-Block
am Ende des Fließtexts. Fix: kombinierte Regex in einem Aufruf
(`(^<h1[^>]*>.*?</h1>\s*)|(<hr[^>]*/>\s*<p[^>]*><b[^>]*>[^<]*Bestelle Royal
Queen Seeds[^<]*</b></p>\s*$)`), jeweils mit `replacements_count: 2`
bestätigt.

## Sonderfälle / Ausnahmen vom Muster

- **21117 (Anesia Seeds Purple Thai):** Kein H1 vorhanden – Content beginnt
  direkt mit `<p data-path-to-node="9">`. Einziges Anesia-Produkt ohne den
  sonst durchgehenden H1. Nur Yoast-CTA gefixt, Content unverändert (kein
  Fix nötig/anwendbar).
- **28406 (Compound Genetics Honeycomb Pavé), 28409 (Medellin x Apples n
  Bananas), 28410 (Mellowz), 28411 (Mora Azul):** Vier Produkte aus einer
  neueren, anderen Content-Vorlage (durchgängiger Fließtext mit `<p><strong>`
  statt Editor-Template mit H1). Kein H1 vorhanden – nur die Yoast-CTA
  "Jetzt Samen bestellen!" wurde entfernt, Content blieb unangetastet.
  28410 und 28411 sind zusätzlich als `private` markiert (Sichtbarkeit
  nicht verändert).
- **20136 (The Cali Connection Fruit Tree), 20150 (Lovin In Her Eyes Depth
  Charge)** u. a. aus dem Block 20136–20150: gemischte Markenvielfalt
  zwischen dem Barneys-Farm- und dem zweiten Anesia-Block, unterschiedliche
  Content-Herkunft (teils ganz ohne H1, teils mit), jeweils einzeln geprüft
  und wie oben beschrieben gefixt.

## "Hanfjack rät/weiß/empfiehlt/sagt" & "Profi-Tipps von Hanfjack" – keine CTA

Wie bereits in den Bäumen `cbd-baum` und `vermehrungsmaterial` festgestellt,
ist die Formulierung "Hanfjack rät:", "Hanfjack weiß:", "Profi-Tipps von
Hanfjack für …" eine redaktionelle Stimme (Anbau-Tipps), **keine**
Kauf-CTA (kein "jetzt kaufen/bestellen"). Diese Passagen sind in praktisch
jedem der 199 Produkte im Abschnitt "Anbau-Daten & Sortenprofil" vorhanden
und wurden bewusst nicht angefasst. Ebenso unangetastet: der "| Hanfjack"-
Suffix in vielen Yoast-SEO-Titeln (kein CTA, sondern Markenkennzeichnung).

## Bearbeitete Cluster (Übersicht)

| Cluster / Marke | Anzahl | Muster |
|---|---|---|
| Royal Queen Seeds | 79 | H1 + Fließtext-CTA-Absatz + Yoast-CTA |
| Dutch Passion | 52 | H1 + Yoast-CTA (teils + EAN) |
| Anesia Seeds (zwei Teilchargen) | 19 | H1 + Yoast-CTA (Ausnahme: 21117 ohne H1) |
| Compound Genetics | 15 | H1 + Yoast-CTA (4x Ausnahme ohne H1) |
| Barneys Farm | 5 | H1 + Yoast-CTA |
| Humboldt Seed Co. | 4 | H1 + Yoast-CTA (teils + EAN) |
| Ethos Genetics (inkl. "10th Planet") | 4 | H1 + Yoast-CTA |
| Gemischte Brands (Cali Connection, Lovin In Her Eyes, Brothers Grimm, Pyramid Seeds u. a., IDs 20130–20150) | 11 | H1 (teils) + Yoast-CTA |
| Cookies Seeds | 1 | H1 + Yoast-CTA (+ EAN) |
| **Gesamt** | **199 (Seite 3: ca. 118, Seite 4: ca. 71)** | |

### Vollständige ID-Listen (große Cluster)

**Royal Queen Seeds (79):** 18142–18221 (in 6er-Schritten, ohne 18162)

**Dutch Passion (52):** 21971, 22167, 22175, 22179, 22183, 22187, 22191,
22223, 22231, 22235, 22239, 22247, 22251, 22255, 22259, 22263, 22267,
22271, 22275, 22279, 22283, 22287, 22291, 22295, 22299, 22303, 22307,
22311, 22315, 22319, 22323, 22331, 22335, 22339, 22343, 22347, 22351,
22355, 22363, 22367, 22371, 22375, 22379, 22383, 22387, 22391, 22395,
22399, 22403, 22407, 22411, 22415

**Anesia Seeds (19, zwei Chargen):** 18959, 18962, 18965, 18968, 18970 ff.
(erste Charge, 12 Produkte im Bereich 18959–18970) sowie 21114, 21117,
21120, 21126, 21129, 21144, 21147 (zweite/dritte Charge, 21117 = Ausnahme
ohne H1)

**Ethos Genetics (4):** 20127 ("10th Planet"), 27385, 27390, 27394

**Compound Genetics (15):** 28402–28416 durchgehend (28406, 28409, 28410,
28411 = Ausnahme ohne H1; 28410 und 28411 zusätzlich `private`)

**Cookies Seeds (1):** 27439

**Gemischte Brands / restliche Seite 3 (Barneys Farm, Humboldt Seed Co.,
Brothers Grimm, Pyramid Seeds, Cali Connection, Lovin In Her Eyes u. a.):**
19187, 19190, 19696, 19699, 19965, 19969, 19973, 19977, 19981, 20130,
20132, 20134, 20136, 20138, 20140, 20142, 20144, 20148, 20150

## Verifikation

- Jeder `wp_replace_in_post`-Aufruf lieferte `replacements_count: 1` (bzw.
  `2` beim RQS-Kombi-Fix) als Bestätigung des Treffers.
- Da ausschließlich `wp_replace_in_post` (Content) und
  `wp_yoast_update_post_seo` (Metadaten) verwendet wurden – nie
  `wp_wc_update_product` –, bestand kein Risiko eines `_min_age`-Resets
  über den WooCommerce-REST-Write-Pfad; eine gesonderte Prüfung war daher
  nicht erforderlich (analog zur Feststellung im `vermehrungsmaterial`-
  Baum).

## Nicht angefasst (bewusst)

- THC-%-Angaben, Genetik-/Terpen-/Breeder-Fachsprache, Blütezeit- und
  Ertragsangaben – normaler, zulässiger Content für Cannabissamen.
- "Hanfjack rät/weiß/empfiehlt/sagt:" und "Profi-Tipps von Hanfjack" in den
  Anbau-Tipps-Abschnitten – redaktionelle Stimme, keine CTA.
- "| Hanfjack"-Suffix in Yoast-SEO-Titeln – Markenkennzeichnung, keine CTA.
- Sichtbarkeits-/Robots-Einstellungen (u. a. `private`-Status bei 28410,
  28411) – unverändert gelassen.
- Variable Produkte: Haupttext liegt auf dem Elternprodukt
  (description/excerpt) – ausreichend für die Prüfung; Variationen wurden
  nicht einzeln geprüft.
