# Headshop Kleinzubehör — Hausstil- & SEO-Review

Review der Produkttexte und Yoast-SEO-Daten für 8 Headshop-Unterkategorien
("Kleinzubehör"): Aschenbecher, Aufbewahrung, Kräutermühlen, Mundstücke,
Waagen, THC Test, Terpene, Tabakersatz. Teil des laufenden, kategorienweiten
Content-Cleanups des Hanfjack.de-Shops (vorangegangene Phasen: 93
Aktivkohlefilter-Produkte, alle Kategorietexte, 29 Pflegeprodukte, 64+51
Headshop-Vapes-Produkte).

## Befund

Über alle 8 Kategorien hinweg zog sich dasselbe Muster wie in früheren
Runden: ein älteres, vor einem Content-Refactoring erstelltes Text-Template
mit `<h1 data-path-to-node="…">Produktname: Marketing-Claim</h1>` am Anfang
der Beschreibung sowie eine Hanfjack-Marken-CTA-Phrase am Ende der
Kurzbeschreibung und/oder in der Yoast-Meta-Description (z. B. "Jetzt bei
Hanfjack sichern!", "Jetzt bei Hanfjack bestellen!", "Im Headshop Hanfjack
kaufen."). Neuere Produkte (erkennbar am `<p dir="auto">`/`<h2 dir="auto">`-
Template ohne `data-path-to-node`) sind praktisch durchgängig bereits
konform.

Die Konzentration dieses Musters war sehr unterschiedlich: In **Aschenbecher**
waren 100 % der geprüften Produkte betroffen (7 von 7). In **Aufbewahrung**
(der mit Abstand größten Kategorie, 39 geprüfte Produkte) betraf es gut
zwei Drittel — konzentriert bei den älteren Einzelprodukten (G-Rollz-,
Barneys-Farm-, Tightpac-, Miron-Einzelglas- und Olivenholz-Artikeln),
während die im August 2026 neu angelegten Miron-/Integra-Boost-Bundle-Sets
(Typ `woosb`) durchgängig sauber waren. In **Mundstücke, Terpene und
Tabakersatz** war kein einziges Produkt betroffen — hier waren alle
Beschreibungen bereits im neueren, CTA-freien Template verfasst.

Zusätzlich gefundene Einzelfälle:
- **Fehlende Yoast-Meta-Description**: mehrere Produkte (u. a. Grove-Bags-
  TerpLoc-Beutel, Miron-Weithalsdose 1L/200ml) hatten gar keine
  Meta-Description hinterlegt (nur der Yoast-Admin-Hinweiskommentar war im
  `yoast_head` sichtbar) — jeweils neu verfasst.
- **Meta-Description deutlich zu lang mit CTA + Emoji**: zwei G-Rollz-
  Aufbewahrungsboxen (Pets Rock, Banksy's Graffiti) sowie die Barneys-Farm-
  Metalldose hatten ca. 280–300 Zeichen lange Meta-Descriptions mit
  angehängter CTA-Phrase und Emojis (🎸🌿 / 🎨🌿 / 🖤🌿) — auf 120–160 Zeichen
  gekürzt und CTA/Emoji entfernt.
- **Meta-Description zu kurz**: RAW Black Tin Case Dose (96 Zeichen) — auf
  eine vollständige, informative Länge erweitert.
- **Formale "Sie"-Anrede**: die drei Olivenholz-Produkte (Schälchen rund,
  Müslischale, Dose mit Magnetschließsystem) verwendeten in den
  Pflegehinweisen durchgängig "Sie"/"Ihr" statt "du"/"dein" — pro Phrase
  einzeln auf "du"-Form umgestellt (nicht per Pauschal-Ersetzung, da
  deutsche Grammatik pro Satz die Konjugation ändert).
- **Yoast-Daten eines falschen Schwesterprodukts** (Kräutermühlen, Produkt
  32712, Spider Farmer Elektrische Kräutermühle): SEO-Title, Meta-
  Description und Focus-Keyword beschrieben tatsächlich ein anderes,
  unverwandtes Produkt (ein "Spider Farmer Growzelt"/Grow-Tent). Nur die
  Yoast-Felder wurden korrigiert (im Rahmen der erlaubten Tools); Gewicht/
  Maße und der leere Beschreibungstext blieben unangetastet, da eine
  Korrektur dort `wp_wc_update_product` oder einen vollständigen
  Content-Neuentwurf erfordert hätte — beides außerhalb des Aufgaben-Scopes.
- **HEMPER/Goody-Glass-Produkte**: wie angewiesen komplett übersprungen,
  weder gelesen noch angefasst.

Die Hausstil-Checkliste (kein H1 in Description, keine Hanfjack-CTA im
Fließtext, durchgängige "du"-Anrede, keine unbelegten Health-/Superlativ-
Claims, vollständige und sinnvolle Yoast-SEO-Felder, saubere
Kurzbeschreibung als Bulletliste) wurde auf jedes nicht ausgeschlossene
Produkt angewendet. Dies war **kein Full-Rewrite-Projekt** — nur tatsächlich
gefundene Verstöße wurden behoben, der Rest der Texte (Fließtext-Inhalt,
technische Daten, Anwendungstipps) blieb unverändert.

## Umfang

| Kategorie (Term-ID) | Gesamt | HEMPER/Goody-Glass ausgeschlossen | Geprüft | Konform | Behoben |
|---|---:|---:|---:|---:|---:|
| Aschenbecher (596) | 14 | 7 | 7 | 0 | 7 |
| Aufbewahrung (598) | 45 | 6 | 39 | 13 | 26 |
| Kräutermühlen (599) | 20 | 3 | 17 | 5 | 12 |
| Mundstücke (5181) | 4 | 0 | 4 | 4 | 0 |
| Waagen (7042) | 4 | 0 | 4 | 1 | 3 |
| THC Test (4127) | 2 | 0 | 2 | 1 | 1 |
| Terpene (4223) | 5 | 0 | 5 | 5 | 0 |
| Tabakersatz (7645) | 5 | 0 | 5 | 5 | 0 |
| **Gesamt** | **99** | **16** | **83** | **34** | **49** |

Alle Zählungen basieren auf `wp_wc_list_products` je Kategorie (inkl.
`status: private`-Produkten, die wie publizierte Produkte behandelt wurden).
Zwei HEMPER-Produkte (37624, 37623) sind sowohl Aufbewahrung als auch
Kräutermühlen zugeordnet und in beiden Zeilen mitgezählt.

## Kategorie-Checklisten

### Aschenbecher (596) — 7/7 behoben
- [x] 28634 RAW Ruby Red Glas Aschenbecher — H1 + CTA entfernt
- [x] 28629 RAW Dark Side Glas Aschenbecher — H1 + CTA entfernt
- [x] 18730 RAW Aschenbecher Metall Black – Regal Ashtray — H1 + CTA entfernt
- [x] 16203 RAW Rainbow Glas Aschenbecher — H1 + CTA entfernt
- [x] 1770 Aschenbecher mit Fliesenglas Muster (private) — H1 + CTA entfernt
- [x] 1769 Aschenbecher – farbige Streifen (private) — H1 + CTA entfernt
- [x] 1766 Arabesken-Aschenbecher aus Keramik (private) — H1 + CTA entfernt

Übersprungen (HEMPER): 37589, 37588, 37587, 37582, 37581, 37580, 37579.

### Aufbewahrung (598) — 39 geprüft, 26 behoben, 13 konform
Konform: 33980, 33979, 33978, 33977, 33976, 33975, 33974, 33973, 33972,
33970, 33969 (alle Miron-/RAW-Bundle-Sets, Typ `woosb`), 17649 (RAW
Einmachglas 475ml), 14776 (OCB Rolling Box).

Behoben:
- [x] 30300 G-Rollz Thug Life Mylar Bags (private) — H1, CTA in Kurzbeschr., CTA in Meta-Desc.
- [x] 30297 Amsterdam Mylar Bags — H1, CTA in Meta-Desc.
- [x] 28606 Tightpac Minivac 2,35L — H1, CTA in Kurzbeschr., CTA in Meta-Desc.
- [x] 28604 Tightpac Minivac 0,29L — H1, CTA in Kurzbeschr., CTA in Meta-Desc.
- [x] 28541 Tightpac Minivac 0,12L — H1, CTA in Kurzbeschr., CTA in Meta-Desc.
- [x] 28189 Miron Violettglas 100ml Saturn — H1, CTA in Kurzbeschr., CTA in Meta-Desc.
- [x] 28182 Miron Violettglas 5ml Ceres — H1, CTA in Kurzbeschr., CTA in Meta-Desc.
- [x] 27710 Royal Queen Seeds Einmachglas 400ml — H1, CTA in Kurzbeschr., CTA in Meta-Desc.
- [x] 22863 G-Rollz Small Aufbewahrungsbox — H1, CTA in Kurzbeschr., CTA in Meta-Desc.
- [x] 22854 G-Rollz Hello Kitty Aufbewahrungsbox — H1, CTA in Kurzbeschr., CTA in Meta-Desc.
- [x] 22851 G-Rollz Cheech & Chong Friends Box — H1, CTA in Kurzbeschr., CTA in Meta-Desc.
- [x] 22845 G-Rollz Cheech & Chong Greatest Hits Box — H1, CTA in Kurzbeschr., CTA in Meta-Desc.
- [x] 22836 G-Rollz Pets Rock Aufbewahrungsbox — Meta-Desc. zu lang (295 Zeichen) mit CTA + Emoji, gekürzt
- [x] 22831 G-Rollz Banksy's Graffiti Aufbewahrungsbox — Meta-Desc. zu lang (302 Zeichen) mit CTA + Emoji, gekürzt
- [x] 16294 Spider Farmer 2L Auto-Cure Smart Jar — H1, CTA in Meta-Desc.
- [x] 15746 Miron Violettglas 200ml Weithalsdose — Meta-Desc. fehlte komplett, neu verfasst
- [x] 14777 Miron Violettglas 1L Weithalsdose — Meta-Desc. fehlte komplett, neu verfasst
- [x] 14775 RAW Black Tin Case Dose — Meta-Desc. zu kurz (96 Zeichen), erweitert
- [x] 14384 Barneys Farm Einmachglas durchsichtig — CTA in Meta-Desc.
- [x] 14383 Barneys Farm Metalldose schwarz — Meta-Desc. zu lang (280 Zeichen) mit CTA + Emoji, gekürzt
- [x] 11939 Grove Bags TerpLoc 30g — Meta-Desc. fehlte komplett, neu verfasst
- [x] 11937 Grove Bags TerpLoc 15g — Meta-Desc. fehlte komplett, neu verfasst
- [x] 11935 Grove Bags TerpLoc 7g — Meta-Desc. fehlte komplett, neu verfasst
- [x] 1764 Schälchen rund Olivenholz (private) — Sie→du umgestellt, CTA in Meta-Desc.
- [x] 1762 Schale Müslischale Olivenholz (private) — Sie→du umgestellt, CTA in Meta-Desc.
- [x] 1759 Dose mit Magnetschließsystem Olivenholz (private) — Sie→du umgestellt, CTA in Meta-Desc.

Übersprungen (HEMPER): 37624, 37623, 35463, 35462, 35461, 35458.

### Kräutermühlen (599) — 17 geprüft, 12 behoben, 5 konform
Konform: 38387, 38386, 9036, 1760, 1714 (bereits sauberes Template bzw.
bereits durchgängige du-Anrede).

Behoben:
- [x] 32712 Spider Farmer Elektrische Kräutermühle (private) — Yoast-Block beschrieb ein anderes Produkt (Spider Farmer Growzelt); Title/Meta-Desc./Focus-Keyword korrigiert. Gewicht/Maße und leerer Body bewusst nicht angefasst (außerhalb des Tool-Scopes).
- [x] 32632 HASHY Grinder für Extrakte — H1 + CTA entfernt
- [x] 28643 Elements Aluminium Pink Large — H1 + CTA entfernt
- [x] 21807 Granny's Premium Milchkanne — H1 + CTA entfernt
- [x] 18976 Champ High Bling Bling Leaf — H1 + CTA entfernt
- [x] 16273 Spider Farmer Ø 76mm Grinder (private) — Meta-Desc. mitten im Wort abgebrochen, vollständig neu formuliert
- [x] 9052 Hammercraft x RAW Rasta — H1 + CTA entfernt
- [x] 9049 Hammercraft x RAW Black Aluminium (private) — H1 + CTA entfernt
- [x] 9045 Atomic Metal 4-teilig (private) — H1 + CTA entfernt
- [x] 1761 Mörser mit Stößel Olivenholz (private) — Sie→du umgestellt
- [x] 1713 Hizen XXL Aluminium 63mm (private) — Sie→du umgestellt
- [x] 526 Storz & Bickel Grinder Orange (private) — H1 (Variante ohne data-path-to-node, direkt anliegend) entfernt

Übersprungen (HEMPER/Goody Glass): 37746, 37624, 37623.

### Mundstücke (5181) — 4/4 konform
- [x] 14401 PURIZE Holzmundstück Vanilla — konform
- [x] 14400 PURIZE Holzmundstück Dark Fruit — konform
- [x] 14399 PURIZE Holzmundstück Apple — konform
- [x] 14398 PURIZE Holzmundstück Lemon — konform

Keine HEMPER-Produkte in dieser Kategorie.

### Waagen (7042) — 4 geprüft, 3 behoben, 1 konform
- [x] 18727 DIPSE Digitalwaage Dab Scale 100g — H1 + CTA entfernt
- [x] 18726 DIPSE Digitalwaage MTW-Serie 200g (private) — Meta-Desc. fehlte, neu verfasst
- [x] 18725 DIPSE Digitalwaage Taschenwaage Jungle 200g — H1 + CTA entfernt
- [x] 9376 RAW Rolling Tray Digitalwaage 1000g — Meta-Desc. fehlte, neu verfasst

Keine HEMPER-Produkte in dieser Kategorie.

### THC Test (4127) — 2 geprüft, 1 behoben, 1 konform
- [x] 27320 Purpl PRO THC Messgerät — konform
- [x] 12213 Alltest THC Speichel Schnelltest (private) — H1 + CTA entfernt

Keine HEMPER-Produkte in dieser Kategorie.

### Terpene (4223) — 5/5 konform
- [x] 12458 Integra Boost Terpene Humulene 62% 4g — konform
- [x] 12456 Integra Boost Terpene Terpinolene 62% 4g — konform
- [x] 12453 Integra Boost Terpene Linalool 62% 4g — konform
- [x] 12451 Integra Boost Terpene Pinene 62% 4g — konform
- [x] 12449 Integra Boost Terpene Myrcene 62% 4g — konform

Alle Texte bleiben faktisch bei "Aroma"/"Duft", keine cannabis-wirkungs-
suggerierenden Aussagen gefunden. Keine HEMPER-Produkte in dieser Kategorie.

### Tabakersatz (7645) — 5/5 konform
- [x] 28653 Bobby Green #01 — konform
- [x] 28648 Bobby Green #02 — konform
- [x] 20068 420z Leaf Blueberry 20g — konform
- [x] 20066 420z Leaf Lemon 20g — konform
- [x] 20067 420z Leaf Rainbow 20g — konform

Keine HEMPER-Produkte in dieser Kategorie.

## Sonderfälle & Präzedenzen (Fortführung aus headshop-mini)

- **`status: private`-Produkte**: identisch zu publizierten Produkten
  geprüft und ggf. korrigiert, keine Sonderbehandlung.
- **Zu kurze Meta-Description**: Schwelle bei < 100 Zeichen für "zu kurz
  und korrekturbedürftig"; Werte im Bereich 105–160 Zeichen wurden als
  akzeptabel belassen.
- **Bundle-Produkte (Typ `woosb`)**: eigene, unabhängig editierbare
  Description/Kurzbeschreibung/Yoast-Daten, nach denselben Kriterien
  geprüft wie einfache Produkte.
- **Falsche Yoast-Daten von Schwesterprodukt**: als Verstoß gegen
  Checklistenpunkt 5 behandelt und im Rahmen der erlaubten Tools (nur
  Yoast-Felder) behoben; darüber hinausgehende Korrekturen (Produktdaten,
  leerer Content) explizit außerhalb des Scopes belassen und hier
  dokumentiert statt automatisch mitgezogen.
- **CTA-Phrasen in der Yoast-Meta-Description** (nicht nur im Fließtext)
  wurden konsequent mitkorrigiert, in Übereinstimmung mit der in früheren
  Kategorien etablierten Praxis — eine reine "ProduktName | Hanfjack"-
  Suffix im SEO-Title bleibt dagegen unangetastet.

## Abschluss

Alle 8 zugewiesenen Kategorien (99 Produkte gesamt, 16 davon HEMPER/Goody-
Glass-ausgeschlossen, 83 geprüft) wurden vollständig nach der 6-Punkte-
Hausstil-Checkliste bearbeitet. 34 Produkte waren bereits konform, 49
Produkte wurden korrigiert (überwiegend H1-Entfernung + CTA-Bereinigung
nach dem bekannten Legacy-Template-Muster, ergänzt um mehrere fehlende/zu
lange/zu kurze Yoast-Meta-Descriptions und drei Sie→du-Umstellungen). Jede
Änderung wurde nach der Ausführung durch erneutes Auslesen des Produkts
verifiziert. Es wurden ausschließlich `wp_replace_in_post` (für
`post_content`/`post_excerpt`) und `wp_yoast_update_post_seo` (für die
Yoast-Felder) verwendet — nie `wp_wc_update_product` oder
`wp_wc_batch_update_products`, um das Pflichtfeld `_min_age` nicht zu
gefährden.
