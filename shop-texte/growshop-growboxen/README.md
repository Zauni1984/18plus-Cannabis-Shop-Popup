# Growboxen & Komplettsets — Hausstil- & SEO-Review

Review der Produkttexte und Yoast-SEO-Daten für die WooCommerce-Kategorien
**Growboxen** (531) und **Komplettsets** (831) auf hanfjack.de. Teil des
laufenden, kategorienweiten Content-Cleanups im Growshop/Growbedarf-Baum
(Headshop-Baum ist bereits abgeschlossen).

## Scope

- Beide Kategorien per `wp_wc_list_products(category="531"/"831",
  per_page=100, status="any")` gelistet (auch private/draft erfasst).
- **531 (Growboxen)**: 66 eindeutige Produkte.
- **831 (Komplettsets)**: 23 zusätzliche Produkte, die nicht bereits in
  531 vorkamen (mehrere Produkte liegen in beiden Kategorien).
- **Insgesamt 89 eindeutige Produkte geprüft** (Aufgabenschätzung war
  ca. 79 — die Differenz erklärt sich durch die Kategorie-Überlappung
  zwischen 531 und 831).
- Kein Produktname enthielt "HEMPER", "Goody Glass" oder "Smoke
  Friends" — der Ausschluss griff nicht.
- **Alle 89 Produkte gefixt oder als bereits sauber verifiziert.**

## Befund

Mehrere klar unterscheidbare Content-Generationen im Scope:

- **Spider-Farmer-Growboxen der ersten Generation** (14 Produkte, IDs
  16244–16257, z. B. "Grow Box 120x60x150", "Pro Grow Box 90x90x180",
  "Mini Grow Box 60x40x50"): durchgehend führendes `<h1
  data-path-to-node="…">Produktname</h1>` **plus** Hanfjack-CTA-Phrase
  ("Jetzt bei Hanfjack für Stealth-Grows kaufen!" u. ä.) in der
  Yoast-Meta-Description. Produkt 16249 war das vom Auftraggeber
  genannte Referenzbeispiel für genau dieses Muster.
- **Spider-Farmer-Komplettsets/Growzelt-Sets** (die komplette
  SF1000/SF2000/SF4000/SE3000–SE7000/G3000–G8600/G1000W-Familie plus die
  zwei SF-G1500-Smart-Bundles mit CTA, insgesamt 18 Produkte in
  Kategorie 831):
  gleiches Muster — führendes `<h1 data-path-to-node="…">`-Tag und bei
  den G1500-Bundles zusätzlich eine CTA-Phrase in der
  Meta-Description ("Jetzt Profi-Set bei Hanfjack bestellen!", "Jetzt
  bei Hanfjack!"). Der übrige Fließtext folgt bei dieser Familie
  durchweg einem sauberen, festen Aufbau (Du-Anrede, Tabelle
  "Technische Details", "Profi-Tipp von Hanfjack:"-Abschlussabschnitt
  als akzeptiertes Stilelement) — reiner H1-/CTA-Entfernungs-Fix, kein
  Rewrite nötig.
- **Spider-Farmer-Zubehör** (Pflanzenständer, Anzuchtschalen, iCOVER-
  Growzelte; 7 Produkte): dasselbe H1+CTA-Muster. Bei Produkt 16290
  stand zusätzlich eine formelle "Sie"-Anrede im Excerpt
  (Lager-/Warteliste-Hinweis) — auf "du"-Stil vereinheitlicht.
- **AC Infinity** (20 Produkte: Zeltstangen, Gitternetze, CLOUDLAB-
  Zelte, Advance-Grow-Zelt-Systeme): bereits vollständig sauber —
  offenbar aus einer neueren, bereits bereinigten Content-Generation.
- **HOMEbox-Familie** (20 Produkte, Ambient Q/R-Serie): Fließtext
  durchgehend sauber (kein H1, keine CTA). Bei 5 Produkten fehlte
  jedoch die Yoast-Meta-Description komplett (Yoast fiel auf eine
  automatisch verkettete, interpunktionslose Excerpt-Zusammenfassung
  zurück) — ergänzt. Bei den übrigen 15 war eine kurze, aber
  vollständige und CTA-freie Meta-Description bereits vorhanden und
  wurde nicht angetastet (kein echter Verstoß, kein Grund für ein
  Rewrite).
- **Spider-Farmer-Großboxen SF-Serie** (2 Produkte, IDs 32709/32710,
  private): 32710 war sauber. Bei 32709 fehlte die komplette
  Produktbeschreibung, und die vorhandene Yoast-Meta-Description war
  **wörtlich von einem anderen Produkt kopiert** (bezog sich auf
  "120x60x150 cm" und "Stealth-Grows", während 32709 tatsächlich
  100x100x200 cm misst) — Beschreibung neu geschrieben, Yoast korrigiert.
- **PROPAGATOR-Kleinzelte** (2 Produkte, private): sauberer Fließtext,
  aber Yoast-Meta-Description komplett leer — ergänzt.
- **AC10-Bundle & SE1000W-Set** (32617, 16307): leere/fehlende
  Beschreibung bzw. Yoast-Daten — vollständig im Kategorie-Hausstil neu
  geschrieben.
- **SF-G3000-Smart-Bundles** (27489, 27493, 27496, alle private/noindex):
  bereits vollständig sauber, keine Änderung nötig.

## Gefixte Verstöße

| Fix | Anzahl Produkte |
|---|---|
| Führendes H1-Tag im Fließtext entfernt | 38 |
| Hanfjack-CTA-Phrase in Yoast-Meta-Description entfernt | 23 |
| Formelle "Sie"-Anrede auf "du" vereinheitlicht | 1 |
| Fehlende Yoast-Meta-Description ergänzt | 7 |
| Leere Produktbeschreibung/Kurzbeschreibung neu geschrieben | 4 |
| Yoast-Titel/Meta-Description/Fokus-Keyword inhaltlich falsch (kopiert von anderem Produkt) korrigiert | 1 |
| **Produkte mit mindestens einem Fix** | **49 von 89** |
| **Bereits sauber, kein Fix nötig** | **40 von 89** |

Alle H1-/CTA-Fixes im Fließtext erfolgten über `wp_replace_in_post`
(Feld `post_content`, Regex `^<h1[^>]*>.*?</h1>\r?\n?`, verankert am
Textanfang, `max_replacements=1`). Yoast-Fixes ausschließlich über
`wp_yoast_update_post_seo` (Feld `meta_description`, bei 32709 zusätzlich
`seo_title` und `focus_keyword`). Leere Beschreibungen wurden über
`wp_wc_update_product` (Felder `description`/`short_description`)
nachgetragen. Jeder Fix wurde direkt danach per erneutem Read
(`wp_get_cpt_item` bzw. `wp_yoast_get_post_seo`) verifiziert.

**`min_age`-Sicherheitscheck**: Nach jedem `wp_wc_update_product`-Write
(Produkte 32709, 15391, 16307, 32617) wurde per `wp_wc_get_product` das
Top-Level-Feld `min_age` erneut geprüft — es blieb in allen Fällen
durchgehend `"18"`. Das rohe `meta_data`-Array zeigt bei einigen dieser
Produkte einen veralteten `_min_age: ""`-Eintrag, der laut Tool-Doku
nicht das autoritative Feld ist; das Top-Level-Feld war in jedem Fall
korrekt und musste nie wiederhergestellt werden.

## Nicht angetastet (kein Verstoß)

- "Profi-Tipp von Hanfjack:" als Abschnittsüberschrift (bei praktisch
  allen Spider-Farmer-Komplettsets) — akzeptiertes Stilelement laut
  Aufgabenstellung, nicht verändert. Bei Produkt 16306 taucht eine
  Variante "Profi-Tipp für den Anbau:" auf (ohne "von Hanfjack") —
  ebenfalls unproblematisch, da keine Kauf-CTA.
- Du/du-Anrede-Mischung (mal groß-, mal kleingeschrieben) — laut
  Vorgabe im gesamten Katalog akzeptiert, nicht vereinheitlicht.
- Technisches Grow-Vokabular (µmol/J, PPFD, VPD, Samsung/Bridgelux-
  Dioden-Bezeichnungen, "explosives Wachstum", "massiver Harzbesatz"
  u. ä.) sind Ausrüstungs-/Leistungsangaben für Growequipment, keine
  Cannabis-Wirkstoff-/Cannabinoid-Heilsversprechen — laut Vorgabe bei
  Growbedarf unproblematisch, nicht angetastet.
- Kurze, aber vollständige und CTA-freie Yoast-Meta-Descriptions in der
  HOMEbox-Familie (15 Produkte) wurden nicht umformuliert — kein
  echter Verstoß, nur "knapp".

## Auffälligkeiten außerhalb des Scopes (nur geflaggt, nicht gefixt)

- **Produkt 15391** (HOMEbox Ambient Q40, private): Produkt-Metadaten
  zeigen Hersteller `"SANlight GmbH"` sowie SANlight-spezifische
  `safety_instructions` — thematisch offensichtlich falsch für ein
  HOMEbox-Zelt. Sieht nach einem Datenrest aus einer falschen
  Import-Vorlage aus. Liegt außerhalb des Text-/SEO-Scopes dieser
  Aufgabe (betrifft Hersteller-Taxonomie/Sicherheitsdaten, nicht
  Beschreibung oder Yoast) und wurde daher nur geflaggt, nicht
  korrigiert.
- **Produkt 16307** (Spider Farmer SE1000W-Set, private): `weight` und
  `dimensions` sind leer. Reines Logistik-/Versanddatenfeld außerhalb
  des Text-/SEO-Scopes, nur zur Kenntnis notiert.
- Der Yoast-/WooCommerce-API-Zugriff (`mcp__Hanfjack__*`-Tools) war
  während dieser Session wiederholt durch ein serverseitiges Rate-Limit
  blockiert (vermutlich durch mehrere parallel laufende
  Cleanup-Agenten auf demselben Shop). Alle 89 Produkte wurden trotzdem
  vollständig abgearbeitet, die Bearbeitung hat dadurch aber deutlich
  länger gedauert als für die Produktzahl zu erwarten wäre.

## Vorgehen

1. Produktlisten per `wp_wc_list_products(category="531"/"831",
   per_page=100, status="any")` geholt und die 89 eindeutigen
   Produkt-IDs ermittelt (66 in 531, davon 21 zusätzlich in 831
   überlappend, plus 23 nur in 831).
2. Pro Produkt: Content per `wp_get_cpt_item` (leichtgewichtig)
   gelesen, Yoast-SEO per `wp_yoast_get_post_seo` geprüft.
3. Nur bei echten Verstößen (H1-Tag, CTA-Phrase im Fließtext oder in
   der Meta-Description, formelle Anrede, fehlende/inhaltlich falsche
   Meta-Description, leere Produktbeschreibung) gezielt gefixt — kein
   Komplett-Rewrite bei bereits sauberen Texten.
4. Jeder Fix direkt danach per erneutem Read verifiziert; bei
   `wp_wc_update_product`-Writes zusätzlich das Top-Level-Feld
   `min_age` re-verifiziert.

## Vollständige Produktliste

### Kategorie 531 (Growboxen) — 66 Produkte

| ID | Produkt | Ergebnis |
|---|---|---|
| 16249 | Spider Farmer Grow Box 120x60x150 | Gefixt: H1 + CTA entfernt (Referenzbeispiel) |
| 16244 | Spider Farmer Grow Box 70x70x160 | Gefixt: H1 + CTA entfernt |
| 16245 | Spider Farmer Grow Box 70x140x200 | Gefixt: H1 + CTA entfernt |
| 16246 | Spider Farmer Grow Box 90x90x180 | Gefixt: H1 + CTA entfernt |
| 16247 | Spider Farmer Grow Box 120x120x200 | Gefixt: H1 + CTA entfernt |
| 16248 | Spider Farmer Grow Box 150x150x200 | Gefixt: H1 + CTA entfernt |
| 16250 | Spider Farmer Grow Box 120x240x200 | Gefixt: H1 + CTA entfernt |
| 16251 | Spider Farmer Grow Box 240x240x200 | Gefixt: H1 + CTA entfernt |
| 16252 | Spider Farmer Grow Box 300x150x200 | Gefixt: H1 + CTA entfernt |
| 16253 | Spider Farmer Mini Grow Box 60x40x50 | Gefixt: H1 + CTA entfernt |
| 16254 | Spider Farmer Mini Grow Box 60x60x80 | Gefixt: H1 + CTA entfernt |
| 16255 | Spider Farmer Pro Grow Box 60x60x180 | Gefixt: H1 + CTA entfernt |
| 16256 | Spider Farmer Pro Grow Box 90x90x180 | Gefixt: H1 + CTA entfernt |
| 16257 | Spider Farmer Pro Grow Box 70x140x200 | Gefixt: H1 + CTA entfernt |
| 38449 | AC Infinity Zeltstangen 90x90 | Sauber, kein Fix nötig |
| 38448 | AC Infinity Zeltstangen 60x60 | Sauber, kein Fix nötig |
| 38447 | AC Infinity Zeltstangen 60x120 | Sauber, kein Fix nötig |
| 38446 | AC Infinity Zeltstangen 120x120 | Sauber, kein Fix nötig |
| 38424 | AC Infinity Grow Zelt Gitternetz 90x90 | Sauber, kein Fix nötig |
| 38423 | AC Infinity Grow Zelt Gitternetz 60x60 | Sauber, kein Fix nötig |
| 38422 | AC Infinity Grow Zelt Gitternetz 60x120 | Sauber, kein Fix nötig |
| 38421 | AC Infinity Grow Zelt Gitternetz 120x120 | Sauber, kein Fix nötig |
| 38407 | AC Infinity CLOUDLAB 894 | Sauber, kein Fix nötig |
| 38406 | AC Infinity CLOUDLAB 866 | Sauber, kein Fix nötig |
| 38405 | AC Infinity CLOUDLAB 844 | Sauber, kein Fix nötig |
| 38404 | AC Infinity CLOUDLAB 733 | Sauber, kein Fix nötig |
| 38403 | AC Infinity CLOUDLAB 722 | Sauber, kein Fix nötig |
| 38402 | AC Infinity CLOUDLAB 642 | Sauber, kein Fix nötig |
| 38401 | AC Infinity CLOUDLAB 632 | Sauber, kein Fix nötig |
| 38392 | AC Infinity Advance Grow Zelt-System PRO 120x120x200 | Sauber, kein Fix nötig |
| 38391 | AC Infinity Advance Grow Zelt-System 90x90x180 | Sauber, kein Fix nötig |
| 38390 | AC Infinity Advance Grow Zelt-System 60x60x180 | Sauber, kein Fix nötig |
| 38389 | AC Infinity Advance Grow Zelt-System 60x120x180 | Sauber, kein Fix nötig |
| 38388 | AC Infinity Advance Grow Zelt-System 120x120x200 (private) | Sauber, kein Fix nötig |
| 32710 | Spider Farmer Grow Box SF Serie 240x120x200 (private) | Sauber, kein Fix nötig |
| 32709 | Spider Farmer Grow Box SF Serie 100x100x200 (private) | Gefixt: leere Beschreibung neu geschrieben; Yoast-Titel/Metadesc/Fokus-Keyword waren von Produkt 16249 kopiert — korrigiert |
| 30552 | Athena VPDome Set (private) | Sauber, kein Fix nötig |
| 27718 | Spider Farmer iCOVER 140x70x200 | Gefixt: H1 + CTA entfernt |
| 27717 | Spider Farmer iCOVER 90x90x180 | Gefixt: H1 + CTA entfernt |
| 27716 | Spider Farmer iCOVER 120x120x200 | Gefixt: H1 + CTA entfernt |
| 16290 | Spider Farmer 6-stufiger Pflanzenständer | Gefixt: H1 + CTA entfernt; Excerpt "Sie" → "du" |
| 16289 | Spider Farmer 4-stufiger Pflanzenständer | Sauber im Fließtext; Yoast-CTA entfernt |
| 16288 | Spider Farmer 3-stufiger Pflanzenständer | Gefixt: H1 + CTA entfernt |
| 16272 | Spider Farmer 4er-Pack Anzuchtschalen | Gefixt: H1 + CTA entfernt |
| 15389 | HOMEbox Vista Medium Growbox (private) | Gefixt: fehlende Yoast-Meta-Description ergänzt |
| 15388 | HOMEbox Ambient R240+ | Gefixt: fehlende Yoast-Meta-Description ergänzt |
| 15387 | HOMEbox Ambient R240 | Sauber, kein Fix nötig |
| 15386 | HOMEbox Ambient R120S | Sauber, kein Fix nötig |
| 15385 | HOMEbox Ambient R120 | Sauber, kein Fix nötig |
| 15384 | HOMEbox Ambient R80S | Gefixt: fehlende Yoast-Meta-Description ergänzt |
| 15383 | HOMEbox Ambient R150 | Sauber, kein Fix nötig |
| 15382 | HOMEbox Ambient R300+ | Gefixt: fehlende Yoast-Meta-Description ergänzt |
| 15381 | HOMEbox Ambient Q300+ | Sauber, kein Fix nötig |
| 15380 | HOMEbox Ambient Q240+ | Sauber, kein Fix nötig |
| 15379 | HOMEbox Ambient Q200+ | Sauber, kein Fix nötig |
| 15378 | HOMEbox Ambient Q150+ | Sauber, kein Fix nötig |
| 15377 | HOMEbox Ambient Q120+ | Sauber, kein Fix nötig |
| 15376 | HOMEbox Ambient Q120 | Sauber, kein Fix nötig |
| 8208 | HOMEbox Ambient Q100+ | Sauber, kein Fix nötig |
| 8194 | HOMEbox Ambient Q100 | Sauber, kein Fix nötig |
| 8180 | HOMEbox Ambient Q80+ | Sauber, kein Fix nötig |
| 15375 | HOMEbox Ambient Q60+ | Sauber, kein Fix nötig |
| 15390 | HOMEbox Ambient Q30 (private) | Sauber, kein Fix nötig |
| 15391 | HOMEbox Ambient Q40 (private) | Gefixt: leere Beschreibung + fehlende Yoast-Meta-Description ergänzt (Anomalie Hersteller/Sicherheitsdaten geflaggt, siehe oben) |
| 7987 | PROPAGATOR M (private) | Gefixt: fehlende Yoast-Meta-Description ergänzt |
| 7978 | PROPAGATOR S (private) | Gefixt: fehlende Yoast-Meta-Description ergänzt |

### Kategorie 831 (Komplettsets) — 23 zusätzliche Produkte (nicht bereits in 531)

| ID | Produkt | Ergebnis |
|---|---|---|
| 32617 | Spider Farmer AC10 mit Clip-Ventilator (draft, woosb) | Gefixt: Beschreibung/Kurzbeschreibung neu geschrieben |
| 27485 | Spider Farmer SF-G1500 Smart Tropfbewässerung Bundle (woosb) | Gefixt: H1 entfernt |
| 27480 | Spider Farmer SF-G1500 Smart Bundle (woosb) | Gefixt: H1 entfernt, Yoast-CTA entfernt |
| 27482 | Spider Farmer SF-G1500 Smart Hydroponik Bundle (woosb) | Gefixt: H1 entfernt, Yoast-CTA entfernt |
| 27496 | Spider Farmer SF-G3000 Smart Tropfbewässerung Bundle (private, woosb) | Sauber, kein Fix nötig |
| 27493 | Spider Farmer SF-G3000 Smart Hydroponik Bundle (private, woosb) | Sauber, kein Fix nötig |
| 27489 | Spider Farmer SF-G3000 Smart Bundle (private, woosb) | Sauber, kein Fix nötig |
| 16236 | Spider Farmer SF1000 Growzelt-Set 70x70x160 | Gefixt: H1 entfernt |
| 16237 | Spider Farmer SF1000 CP Growzelt-Set 70x70x160 | Gefixt: H1 entfernt |
| 16238 | Spider Farmer SF2000 Growzelt-Set 120x60x180 | Gefixt: H1 entfernt |
| 16239 | Spider Farmer SF2000 CP Growzelt-Set 120x60x180 Smart | Gefixt: H1 entfernt |
| 16240 | Spider Farmer SF4000 Growzelt-Set 120x120x200 | Gefixt: H1 entfernt |
| 16241 | Spider Farmer G3000 smartes Growzelt-Set 90x90x180 | Gefixt: H1 entfernt |
| 16299 | Spider Farmer SE3000 Growzelt-Kit 90x90x180 | Gefixt: H1 entfernt |
| 16300 | Spider Farmer G3000 Growzelt-Set Smart 90x90x180 | Gefixt: H1 entfernt |
| 16301 | Spider Farmer SE4500 Growzelt-Set 70x140x200 | Gefixt: H1 entfernt |
| 16302 | Spider Farmer G4500 Growbox-Set 140x70x200 | Gefixt: H1 entfernt |
| 16303 | Spider Farmer SE5000 Growzelt-Set 120x120x200 | Gefixt: H1 entfernt |
| 16304 | Spider Farmer G5000 Growbox-Komplettset 120x120x200 | Gefixt: H1 entfernt |
| 16305 | Spider Farmer SE7000 Komplett-Set 150x150x200 | Gefixt: H1 entfernt |
| 16306 | Spider Farmer G8600 Growbox-Komplettset 150x150x200 | Gefixt: H1 entfernt |
| 16307 | Spider Farmer 150x150 Growzelt-Set SE1000W (private) | Gefixt: leere Beschreibung/Kurzbeschreibung + Yoast neu geschrieben (weight/dimensions weiterhin leer, siehe Anomalien) |
| 16308 | Spider Farmer G1000W Growbox-Komplettset 150x150x200 | Gefixt: H1 entfernt |

*Produkte, die sowohl in 531 als auch in 831 einsortiert sind (z. B. die
Spider-Farmer-Grow-Box-Familie 16244–16257), erscheinen nur in der
531-Tabelle, um Doppelzählung zu vermeiden.*
