# Bewässerung (AutoPot & Co.) — Hausstil- & SEO-Review

Review der Produkttexte und Yoast-SEO-Daten für die WooCommerce-Kategorie
**Bewässerung** (1401, Elternkategorie) und ihre vier Unterkategorien
**AutoPot Komplettsysteme** (15557), **AutoPot Töpfe & Untersetzer**
(15558), **AutoPot Verrohrung & Ventile** (15559) und **AutoPot Tanks &
Zubehör** (15560). Teil des laufenden, kategorienweiten Content-Cleanups
des Hanfjack.de-Shops im Growshop/Growbedarf-Baum (Headshop-Baum ist
bereits abgeschlossen).

## Scope

- Alle Produkte wurden pro Kategorie per `wp_wc_list_products(status="any")`
  aufgelistet (auch private/draft erfasst).
- **1401 (Bewässerung, direkt zugeordnet, nicht in einer Unterkategorie)**:
  26 Produkte — AC Infinity Sprüher/Bewässerungsbasis, Aqua Master
  Dosierpumpen/-schläuche (C10/C20/C30), Autopot FlexiPot/easy2Go-Zubehör,
  zwei Tauchpumpen, 5 Spider-Farmer-Bewässerungssets und 8 private
  Terra-Aquatica-(GHE)-Hydroponiksysteme.
- **15557 (AutoPot Komplettsysteme)**: 26 Produkte (alle "Autopot SYSTEM
  1Pot/2Pot …", IDs 38109–38134).
- **15558 (AutoPot Töpfe & Untersetzer)**: 8 Produkte.
- **15559 (AutoPot Verrohrung & Ventile)**: 21 Produkte.
- **15560 (AutoPot Tanks & Zubehör)**: 10 Produkte.
- **Insgesamt 91 Produkte geprüft** (Aufgabenschätzung war ca. 83 — die
  Differenz erklärt sich durch die zusätzlichen, nur der Elternkategorie
  zugeordneten Produkte wie AC Infinity, Aqua Master, Tauchpumpen und die
  Terra-Aquatica-Systeme, die die Aufgabenstellung explizit als
  prüfungspflichtig markiert hatte).
- Kein Produktname enthielt "HEMPER", "Goody Glass" oder "Smoke Friends" —
  der Ausschluss griff nicht.
- **89 von 91 Produkten gefixt**, 2 waren bereits vollständig sauber
  (8286 HydroCloner Spinner 72, 8267 Terra Aquatica Ebb&Grow).

## Befund

Zwei klar getrennte Content-Generationen:

**Die 65 AutoPot-Produkte aus den vier Unterkategorien** (Komplettsysteme,
Töpfe & Untersetzer, Verrohrung & Ventile, Tanks & Zubehör) stammen aus
einem einheitlichen, sehr frischen Batch von Ende August/Anfang September
2026. Alle 65 hatten **ausschließlich** ein führendes
`<h1>Produktname</h1>`-Tag (schlicht, ohne `data-path-to-node`-Attribute)
— sonst war der Text durchgehend sauber: informelle Du-Anrede, keine
CTA-Phrasen im Fließtext, Yoast-SEO vollständig und thematisch treffend
(Fokus-Keyword implizit über Titel/Description, Meta-Description 120–160
Zeichen, kein CTA-Zusatz). Reiner H1-Entfernungs-Batch.

**Die 26 nur der Elternkategorie 1401 zugeordneten Produkte** sind
gemischter, weil sie aus mehreren älteren Content-Generationen stammen:

- **AC Infinity** (2 Produkte, Wassersprüher/Bewässerungsbasis, Ende
  August 2026): nur das schlichte `<h1>`-Tag, sonst bereits sauber.
- **Aqua Master C10/C20/C30-Pumpen + Dosierschläuche** (5 Produkte, Mai
  2025): klassisches `data-path-to-node`-Template mit "Profi-Tipps von
  Hanfjack …"-Abschnitt (akzeptierter Stil, nicht angetastet) — aber bei
  **allen 5** stand die CTA-Phrase "Jetzt bei Hanfjack kaufen!" in der
  Yoast-Meta-Description. Zusätzlich hatte der 10-m-Dosierschlauch (31583)
  eine Meta-Description, die **wörtlich vom 5-m-Schlauch kopiert** war
  ("Hochresistenter **5m** PTFE-Schlauch" auf der 10-m-Produktseite) —
  offensichtlicher Copy-Paste-Fehler zwischen zwei Größenvarianten.
- **Autopot FlexiPot/easy2Go-Zubehör** (4 Produkte, Mai 2025): gleiches
  Template, aber mit CTA **sowohl im Fließtext** (abschließender
  Absatz "Bestelle … jetzt bei Hanfjack …") **als auch** in der
  Meta-Description.
- **Tauchpumpen** (2 Produkte, April 2026): kein H1, keine CTA im
  Fließtext — aber beide Yoast-Meta-Descriptions waren mit 165–176
  Zeichen zu lang und endeten auf ein bloßes "Jetzt kaufen!" (keine
  exakte Hanfjack-CTA-Phrase, aber unnötiger Kaufappell plus
  Längenverstoß) — gekürzt und neutral umformuliert.
- **Spider-Farmer-Bewässerungssets** (5 Produkte, Sep 2025–Jan 2026, der
  vom Auftraggeber geflaggte Schwerpunkt): durchgehend `<h1
  data-path-to-node="…">` plus CTA-Phrase in der Meta-Description
  ("Jetzt bei Hanfjack kaufen!"), bei zwei Produkten zusätzlich eine
  nackte EAN-Nummer als Anhängsel der Description. Kein Produkt in
  diesem Cluster hatte eine leere Beschreibung — die im Hintergrund
  erwähnten leeren Spider-Farmer-Produkte (Status private/draft, ID
  ab ~32600) kommen in der Bewässerungs-Kategorie nicht vor.
- **Terra Aquatica (GHE) Hydroponiksysteme** (8 Produkte, Status
  "private", Feb 2025): kein H1, keine CTA im Fließtext, generell sauberes
  Text-Template — aber bei **4 von 8** (HydroCloner 27, CultiMate S 15L,
  CultiMate L 45L, CultiMate 4-pack) fehlte die Yoast-Meta-Description
  komplett. Bei einem weiteren (CultiMate L **Solar** 45L) behauptete die
  Meta-Description fälschlich ein "Solar-betriebenes" System — im
  Fließtext ist "Solar" nur der Produktlinien-Name, das System läuft
  stromlos per Schwerkraft wie die anderen CultiMate-Modelle. Bei
  CultiMate Aero war die Description zwar vorhanden, aber zu allgemein/
  leicht am Produkt vorbei formuliert ("effiziente Stecklingsanzucht" für
  ein Produkt, das laut Fließtext für Kräuter/Zierpflanzen/Gemüse gedacht
  ist) und trug eine nackte EAN als Anhängsel — neu formuliert.

## Gefixte Verstöße nach Kategorie

| Fix | Anzahl Produkte |
|---|---|
| Führendes H1-Tag im Fließtext entfernt | 81 |
| Hanfjack-CTA-Phrase im Fließtext entfernt | 4 |
| Hanfjack-CTA-Phrase in Yoast-Meta-Description entfernt/neutralisiert | 14 |
| Fehlende Yoast-Meta-Description ergänzt | 4 |
| Yoast-Meta-Description inhaltlich falsch/nicht passend korrigiert | 2 |
| Yoast-Meta-Description zu lang (>160 Zeichen) gekürzt | 2 |
| **Produkte mit mindestens einem Fix** | **89 von 91** |

Alle Fixes erfolgten ausschließlich über `wp_replace_in_post` (Feld
`post_content`, gezieltes Regex-Search-Replace: `^<h1[^>]*>.*?</h1>\s*` für
das H1-Tag, sowie gezielte String-Matches für die CTA-Absätze) und
`wp_yoast_update_post_seo` (nur Feld `meta_description`) — **nicht** über
`wp_wc_update_product`. Der in der Aufgabenstellung beschriebene
`_min_age`-Reset-Bug betrifft ausschließlich den `wp_wc_update_product`-
Schreibpfad; da dieser hier nie verwendet wurde (kein Produkt in diesem
Scope hatte eine leere Beschreibung, die eine `description`/
`short_description`-Neufassung per `wp_wc_update_product` erfordert
hätte), war keine `_min_age`-Nachkontrolle oder -Wiederherstellung nötig.

Jeder Fix wurde direkt danach per erneutem Read (`wp_get_cpt_item` bzw.
`wp_yoast_get_post_seo`) verifiziert.

## Nicht angetastet (kein Verstoß)

- "Profi-Tipp(s) von Hanfjack:" bzw. "Profi-Tipps von Hanfjack für …:" als
  Abschnittsüberschrift kam bei mehreren Aqua-Master- und Spider-Farmer-
  Produkten vor — akzeptiertes Stilelement, nicht verändert. Ebenso
  wurden einzelne inline Sätze wie "Hanfjack rät:", "Hanfjack weiß:",
  "Hanfjack empfiehlt:" innerhalb dieser Profi-Tipp-Abschnitte belassen,
  da es sich um reine Ratschlags-Zuschreibung und keine Kauf-CTA handelt.
- SEO-Titel im Format "Produktname kaufen | Hanfjack" (z. B. bei Tauch-
  pumpen und Terra-Aquatica-Produkten) wurden **nicht** verändert — das
  ist eine Standard-Title-Tag-Konvention ("Produkt | Shopname"), keine
  CTA-Phrase im Sinne der Checkliste (die explizit "Jetzt bei Hanfjack
  kaufen/bestellen"-Sätze meint).
- Marketing-Superlative bei den Spider-Farmer-Produkten (z. B. "bis zu
  30 % mehr Ertrag") sind Ausrüstungs-/Leistungsangaben, keine
  Cannabis-Wirkstoff-/Cannabinoid-Heilsversprechen — laut Vorgabe bei
  Growbedarf unproblematisch, nicht angetastet.
- Fachbegriffe wie Wurzelwachstum, Nährstoffaufnahme, Terpenprofil,
  Harzproduktion etc. sind bei Bewässerungs-/Hydroponik-Equipment
  unproblematisch und wurden nicht angerührt.
- Die interne Widersprüchlichkeit bei Produkt 8267 (Ebb&Grow Solar:
  "6,5 Watt (tatsächlich weniger als 2 Watt im Betrieb)") ist keine
  Checklisten-Verstoß (kein H1, keine CTA, keine falsche Meta-Description)
  und wurde daher stehen gelassen.

## Auffälligkeiten außerhalb des Scopes

- **Produkt 31583** (Aqua Master Dosierschlauch 10m): Die
  Meta-Description war wörtlich von der 5-m-Variante (31581) übernommen
  worden ("Hochresistenter 5m PTFE-Schlauch …" auf der 10-m-Seite) — ein
  klarer Copy-Paste-Fehler zwischen Größenvarianten, wie ihn die
  Aufgabenstellung als "SEO-Block versehentlich falschem Produkt
  zugeordnet" beschreibt. Wurde hier korrigiert, da beide Produkte in
  meinem Scope lagen; bei ähnlichen Größenvarianten-Familien in anderen
  Kategorien lohnt eine gezielte Stichprobe.
- **Produkt 8278** (Terra Aquatica CultiMate L Solar 45L): Die
  Meta-Description behauptete "Solar-betriebenes hydroponisches
  Anbausystem" — das Produkt läuft aber wie alle anderen CultiMate-
  Modelle rein über Schwerkraft/Pumpe mit unter 2 Watt Verbrauch; "Solar"
  ist nur der Produktlinien-Name (Terra Aquatica CultiMate L **Solar**
  vs. CultiMate L ohne Solar-Zusatz). Korrigiert; könnte ein Hinweis
  sein, dass der Solar-Zusatz im Produktnamen selbst bei einem Massen-
  import ohne Rücksicht auf die tatsächliche Funktionsweise vergeben
  wurde — falls dieselbe Namenskonvention bei weiteren "Solar"-Produkten
  in anderen Kategorien auftaucht, lohnt ein Blick auf deren
  Meta-Description.
- Bei Produkt 38071 (Absperrhahn PE 2x Ø16mm) trägt das Produktbild in
  Yoast noch die automatisch generierte Kamera-Bildunterschrift "KONICA
  MINOLTA DIGITAL CAMERA" als `caption` — ein Metadaten-Restbestand aus
  dem Original-Foto-Upload, kein Text-/SEO-Feld im Sinne der Checkliste
  und daher nicht verändert, aber als Aufräum-Kandidat erwähnenswert.
- Der Yoast-SEO-API-Zugriff (`mcp__Hanfjack__*`-Tools) war während dieser
  Session wiederholt durch ein serverseitiges Rate-Limit blockiert
  (vermutlich durch parallel laufende Agenten auf demselben Shop). Alle
  91 Produkte im Scope wurden trotzdem vollständig abgearbeitet, die
  Bearbeitung hat dadurch aber deutlich länger gedauert als für die
  Produktzahl zu erwarten wäre.

## Vorgehen

1. Produktlisten per `wp_wc_list_products(category="<ID>", per_page=100,
   status="any")` für alle fünf Kategorien geholt und die 91 eindeutigen
   Produkt-IDs (26 nur in 1401 direkt + 65 in den vier Unterkategorien)
   ermittelt.
2. Pro Produkt: Content per `wp_get_cpt_item` (leichtgewichtig) gelesen,
   Yoast-SEO per `wp_yoast_get_post_seo` geprüft.
3. Nur bei echten Verstößen (H1-Tag, CTA-Phrase im Fließtext oder in der
   Meta-Description, fehlende oder inhaltlich falsche Meta-Description,
   Längenverstoß) gezielt gefixt — kein Komplett-Rewrite.
4. Jeder Fix direkt danach per erneutem Read verifiziert.
5. Kein Produkt in diesem Scope hatte eine leere `description`/
   `short_description` — die `_min_age`-Prüfung nach
   `wp_wc_update_product`-Writes aus der Aufgabenstellung war daher
   nicht anzuwenden.
