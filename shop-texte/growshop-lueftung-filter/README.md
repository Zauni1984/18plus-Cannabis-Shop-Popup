# Lüfter & Filter — Hausstil- & SEO-Review

Review der Produkttexte und Yoast-SEO-Daten für die WooCommerce-Kategorie
**Lüfter & Filter** (539, Elternkategorie) und ihre drei Unterkategorien
**Aktivkohlefilter** (4161, im Growshop-Baum — **nicht** zu verwechseln
mit der gleichnamigen, bereits abgeschlossenen Headshop-Kategorie 4550),
**Ventilatoren** (4159) und **Zu- und Abluft** (4160). Teil des
laufenden, kategorienweiten Content-Cleanups des Hanfjack.de-Shops im
Growshop/Growbedarf-Baum (Headshop-Baum ist bereits abgeschlossen).

## Scope

Alle Produkte wurden pro Kategorie per `wp_wc_list_products(category="<ID>",
per_page=100, status="any")` aufgelistet (auch private/draft erfasst):

- **539 (Lüfter & Filter, direkt zugeordnet)**: 96 Produkte.
- **4161 (Aktivkohlefilter)**: 41 Produkte.
- **4159 (Ventilatoren)**: 7 Produkte.
- **4160 (Zu- und Abluft)**: 90 Produkte.
- Starke Überlappung zwischen den Kategorien (z. B. sind alle Aktivkohle-
  filter sowohl in 4161 als auch direkt in 539 einsortiert) → **Union:
  118 eindeutige Produkte**, das entspricht der Aufgabenschätzung von
  ca. 118.
- Kein Produktname enthielt "HEMPER", "Goody Glass" oder "Smoke
  Friends" — der Ausschluss griff nicht (erwartungsgemäß, reine
  Grow-Equipment-Kategorien).
- **116 von 118 Produkten gefixt**, 2 waren bereits vollständig sauber
  (9764 Rhino Pro Aktivkohlefilter-Serie, 17634 Caluma Clip-Grip
  oszillierender Ventilator 20W).

## Befund

Zwei klar getrennte Content-Generationen, praktisch keine Überschneidung
mit anderen Verstoßarten:

**12 Spider-Farmer-Produkte** (der vom Auftraggeber explizit geflaggte
Schwerpunkt) — 4"/6"-Inline-Lüfter, -Kanalventilator-Kits,
-Aktivkohlefilter und Clip-Ventilatoren, IDs 16258–16267, 16281, 27739,
aus dem September-2025/Januar-2026-Batch:

- **Alle 12** hatten ein führendes `<h1 data-path-to-node="…">Titel</h1>`
  (teils mit zusätzlichen `data-index-in-node`-Attributen in den
  folgenden Absätzen) am Beginn der `description`.
- **Alle 12** hatten die CTA-Phrase "Jetzt bei Hanfjack kaufen!" bzw.
  "… bestellen!" in der Yoast-`meta_description` (und identisch im
  automatisch gespiegelten `og_description`).
- Kein Produkt hatte die CTA-Phrase im Fließtext selbst — dort kam
  ausschließlich "Profi-Tipp (von Hanfjack):" als akzeptierte
  Abschnittsüberschrift vor (nicht angetastet). Bei Produkt 27739 findet
  sich zusätzlich ein Fließtext-Satz "Hanfjack empfiehlt dieses Set
  besonders wegen der nahtlosen Fernsteuerung" — das ist eine reine
  Empfehlungs-Zuschreibung, keine Kauf-CTA im Sinne der Checkliste
  ("Jetzt … kaufen/bestellen"), daher unangetastet gelassen.
- Keines der 12 hatte eine leere `description`/`short_description` — die
  im Hintergrund erwähnten leeren Spider-Farmer-Entwürfe (Status
  private/draft, ID ab ca. 32600) kommen in diesem Scope nicht vor.

**106 weitere Produkte** (AC Infinity CLOUDLINE/CLOUDRAY-Ventilatoren
und -Aktivkohlefilter, Can-Fan/Can-Lite/Can-Original-Filter und
-Ventilatoren, Prima Klima EC/AC-Ventilatoren, Phonic Trap-Dämmschläuche,
Combiconnect/Aluflexschlauch-Rohrware) stammen aus einem einheitlichen,
sehr frischen Batch vom 30. August bis 3. September 2026:

- **104 von 106** hatten **ausschließlich** ein führendes
  `<h1>Produktname</h1>`-Tag (schlicht, ohne `data-path-to-node`-
  Attribute) — sonst war der Text durchgehend sauber: informelle
  Du-Anrede, keine CTA-Phrasen im Fließtext, Yoast-SEO vollständig und
  thematisch treffend (Meta-Description 120–160 Zeichen, kein
  CTA-Zusatz, Titel/Description passen zum jeweiligen Produkt). Reiner
  H1-Entfernungs-Batch.
- **2 von 106** (9764 Rhino Pro Aktivkohlefilter-Serie, Status
  "private", älterer Mai-2025-Content; 17634 Caluma Clip-Grip
  oszillierender Ventilator, September-2025-Content) hatten **kein**
  H1-Tag und **keine** CTA-Phrase — bereits sauber, nicht verändert.
- Stichprobenartige Yoast-Prüfung über alle Produktfamilien und
  Kategorien hinweg (AC Infinity Aktivkohlefilter, CLOUDLINE-Ventilator-
  Reihe, CLOUDRAY-Clip-Ventilatoren, private Lüftungsanschluss-Variante,
  AC Infinity Luftfilter-Sets, Can Original Filter, Can-Fan MAX Pro,
  ein generalüberholtes Prima-Klima-Gerät, Phonic Trap, Combiconnect)
  ergab durchweg saubere, produktspezifische Meta-Descriptions ohne
  CTA-Phrasen und ohne erkennbare Verwechslungen zwischen Produkten.

## Gefixte Verstöße nach Kategorie

| Fix | Anzahl Produkte |
|---|---|
| Führendes H1-Tag im Fließtext entfernt | 116 |
| Hanfjack-CTA-Phrase in Yoast-Meta-Description entfernt | 12 |
| Leere Produktbeschreibung neu geschrieben | 0 |
| **Produkte mit mindestens einem Fix** | **116 von 118** |

Alle Fixes erfolgten ausschließlich über `wp_replace_in_post` (Feld
`post_content`, Regex-Search-Replace `^<h1[^>]*>.*?</h1>\s*` — deckt
sowohl das schlichte `<h1>` als auch das `<h1 data-path-to-node="…">`
der Spider-Farmer-Produkte ab) und `wp_yoast_update_post_seo` (nur Feld
`meta_description`) — **nicht** über `wp_wc_update_product` oder
`wp_wc_batch_update_products`. Der in der Aufgabenstellung beschriebene
`_min_age`-Reset-Bug betrifft ausschließlich den `wp_wc_update_product`-
Schreibpfad; da dieser hier nie verwendet wurde (kein Produkt in diesem
Scope hatte eine leere Beschreibung, die eine `description`/
`short_description`-Neufassung erfordert hätte), war keine
`_min_age`-Nachkontrolle/-Wiederherstellung nötig. Stichprobenkontrolle
per `wp_wc_get_product` (Produkte 9764 unbearbeitet und 16258 bearbeitet)
bestätigte trotzdem, dass das Top-Level-Feld `min_age` bei beiden
unverändert `18` ist.

Jeder Fix wurde direkt danach per erneutem Read (`replacements_count` im
Tool-Ergebnis, stichprobenartig zusätzlich per `wp_yoast_get_post_seo`)
verifiziert.

## Nicht angetastet (kein Verstoß)

- "Profi-Tipp (von Hanfjack):" als Abschnittsüberschrift kam bei allen
  12 Spider-Farmer-Produkten vor — akzeptiertes Stilelement, nicht
  verändert.
- Der Empfehlungssatz "Hanfjack empfiehlt dieses Set …" im Fließtext von
  Produkt 27739 ist keine Kauf-CTA im Sinne der Checkliste und wurde
  belassen.
- Fachbegriffe wie Geruchsneutralisierung, Luftaustausch, CFM, statischer
  Luftdruck etc. sind bei Lüftungs-/Filter-Equipment unproblematisch und
  wurden nicht angerührt.
- SEO-Titel im Format "Produkt | Ersatzteil" bzw. mit technischen
  Kenndaten (z. B. "AC Infinity CLOUDLINE Pro S12 Rohrventilator 300 mm")
  sind Standard-Title-Tag-Konventionen, keine CTA-Phrase.
- `noindex` bei privatem Produkt 38460 (AC Infinity Lüftungsanschluss
  200 mm) ist korrekt gesetzt und wurde nicht verändert.

## Auffälligkeiten außerhalb des Scopes

- Der Yoast-/Content-Schreibzugriff (`mcp__Hanfjack__*`-Tools,
  insbesondere `wp_replace_in_post` und `wp_yoast_update_post_seo`) war
  während dieser Session wiederholt und unvorhersehbar durch ein
  serverseitiges Rate-Limit blockiert (vermutlich durch parallel
  laufende Agenten auf demselben Shop — mehrere andere Kategorien im
  `shop-texte`-Verzeichnis wurden zeitgleich bearbeitet). Alle 118
  Produkte im Scope wurden trotzdem vollständig abgearbeitet, die
  Bearbeitung hat dadurch aber deutlich länger gedauert als für die
  Produktzahl zu erwarten wäre.
- Keine Yoast-Block-Verwechslungen zwischen Produkten (z. B. Meta-
  Description einer falschen Größenvariante) im Stichprobenumfang
  gefunden — anders als in der parallel geprüften Bewässerungs-Kategorie
  (siehe `shop-texte/growshop-bewaesserung/README.md`), wo genau dieses
  Muster mehrfach auftrat. Da die 106 Nicht-Spider-Farmer-Produkte hier
  aus einem einzigen, sehr aktuellen Bulk-Batch (30.08.–03.09.2026)
  stammen, ist das Risiko geringer als bei älteren, gemischten
  Content-Generationen — eine vollständige Yoast-Einzelprüfung aller 106
  Produkte (statt der durchgeführten Stichprobe über alle Produkt-
  familien) wäre aber nötig, um das mit letzter Sicherheit
  auszuschließen.

## Vorgehen

1. Produktlisten per `wp_wc_list_products(category="<ID>", per_page=100,
   status="any")` für alle vier Kategorien geholt und die 118
   eindeutigen Produkt-IDs (Union über 539/4161/4159/4160) ermittelt.
2. Pro Produkt: Content per `wp_get_cpt_item` (leichtgewichtig) gelesen.
   Bei den 12 Spider-Farmer-Produkten und einer breiten Stichprobe der
   übrigen 106 zusätzlich Yoast-SEO per `wp_yoast_get_post_seo` geprüft.
3. Nur bei echten Verstößen (H1-Tag, CTA-Phrase in Fließtext oder
   Meta-Description) gezielt gefixt — kein Komplett-Rewrite.
4. Jeder Fix direkt danach per `replacements_count`
   (`wp_replace_in_post`) bzw. `updated_fields` (`wp_yoast_update_post_seo`)
   verifiziert; H1-Fixes zusätzlich stichprobenartig per `wp_wc_get_product`
   gegengelesen.
5. Kein Produkt in diesem Scope hatte eine leere `description`/
   `short_description` — die `_min_age`-Prüfung nach
   `wp_wc_update_product`-Writes aus der Aufgabenstellung war daher nicht
   anzuwenden; Stichprobenkontrolle des Top-Level-Felds `min_age`
   bestätigte dennoch, dass es unverändert bei `18` liegt.
