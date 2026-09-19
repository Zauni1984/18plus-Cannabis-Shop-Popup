# Dünger (Seite 5+) & Dünger Sets — Hausstil- & SEO-Review

Review der Produkttexte und Yoast-SEO-Daten für den Rest der Kategorie
**Dünger** (WooCommerce-Kategorie 1132, Seite 5 der Listung — die Produkte
mit den höchsten IDs) sowie die komplette Kategorie **Dünger Sets**
(WooCommerce-Kategorie 4157). Teil des laufenden, kategorienweiten
Content-Cleanups des Hanfjack.de-Shops im Growshop/Growbedarf-Baum; zwei
parallele Agenten haben zeitgleich die restlichen Dünger-Seiten (1–2 bzw.
3–4) bearbeitet.

## Scope

- **Kategorie 1132 (Dünger), Seite 5**: 26 Produkte, IDs 39474–39500
  (39489 existiert nicht in der Kategorie). Seite 6 war leer — Seite 5 war
  damit bereits der komplette Rest der Kategorie.
- **Kategorie 4157 (Dünger Sets)**: 32 Produkte, IDs 8321–39372.
- Kein Produktname enthielt "HEMPER", "Goody Glass" oder "Smoke Friends" —
  der Ausschluss griff nicht.
- Insgesamt **58 Produkte geprüft**, **53 davon gefixt**, 5 waren bereits
  sauber.

## Befund

Zwei klar getrennte Content-Generationen, wie im Hintergrund erwartet:

**Seite 5 von Dünger** (IDs 39474–39500) stammt komplett aus dem sehr
frischen Batch von Anfang September 2026 (GrowsArtig Alfa Boost/Betta
Roots, House & Garden, Humintech, Hydroponic Research, New Millenium,
SUPERthrive, Rock Nutrients). Alle 26 Produkte hatten **ausschließlich**
ein führendes `<h1>Produktname</h1>`-Tag (ohne `data-path-to-node`-
Attribute) — sonst war der Text bereits sauber: durchgehende Du-Anrede,
keine CTA-Phrasen, Yoast-SEO durchgehend vollständig und plausibel
(Fokus-Keyword, Titel, Meta-Description 120–160 Zeichen). Reiner
H1-Entfernungs-Batch.

**Dünger Sets** war gemischter, weil die Kategorie Produkte aus mehreren
Content-Generationen enthält:

- **Terra Aquatica-Sechserpack** (Starter Kits TriPart/ProOrganic/
  DualPart/NovaMax, IDs 8321–8337, Status "private", Erstellung Feb 2025):
  kein H1, keine CTA-Phrase im Fließtext — aber bei **allen 6** fehlte die
  Yoast-Meta-Description komplett (Yoast-Admin-Hinweis: "diese Seite zeigt
  keine Meta-Beschreibung"). Ergänzt.
- **Älteres data-path-to-node-Template mit CTA** (BioBizz TRY PACK
  Indoor/Outdoor/Stimulant/Hydro, Plagron Seedbox & Top Grow Box Terra,
  Biobizz Starters Pack, BioBizz All Pack Indoor, Hesi Pack, 420flow
  Starter Set/pH-Set/Bloom+Booster, Schumann Naturdünger — 13 Produkte,
  Erstellung Juli 2025–Feb 2026): klassisches Muster wie in den
  Headshop-Reviews — `<h1 data-path-to-node="…">` am Anfang **und** eine
  Hanfjack-CTA-Phrase am Ende des Fließtexts ("Bestelle … jetzt bei
  Hanfjack …"), bei allen 13 zusätzlich dieselbe CTA-Phrase 1:1 in der
  Yoast-Meta-Description ("… Jetzt bei Hanfjack online bestellen!" /
  "… kaufen!"). Alle drei Stellen gefixt: H1 entfernt, Body-CTA-Absatz
  gestrichen, Meta-Description neutral umformuliert.
- **Neuere/mittlere Batches ohne CTA** (Canna Bio Home Grow Kit, Canna Bio
  EasyBox, Hesi Starterset Erde, Atami Bloombastic Box, Atami VGN 4-Pack,
  Canna Terra Starterkit, Green House Powder Feeding, Mills Starter Pack —
  8 Produkte, Mai–Aug 2026): nur das schlichte `<h1>Produktname</h1>` ohne
  `data-path-to-node`, sonst bereits sauber (Yoast vollständig, keine
  CTA). Reiner H1-Entfernungs-Batch, wie Seite 5 von Dünger.
- **Bereits vollständig sauber** (kein Fix nötig): Plagron Top Grow Box
  natural, Plagron Easy Pack natural/Terra, BioTabs Starterpack, Canna
  Biocanna Starterkit — 5 Produkte ohne H1, ohne CTA, mit vollständigem
  Yoast.

## Gefixte Verstöße nach Kategorie

| Fix | Anzahl Produkte |
|---|---|
| Führendes H1-Tag entfernt (Fließtext) | 47 |
| Hanfjack-CTA-Phrase im Fließtext entfernt | 13 |
| Hanfjack-CTA-Phrase in Yoast-Meta-Description neutralisiert | 13 |
| Fehlende Yoast-Meta-Description ergänzt | 6 |
| **Produkte mit mindestens einem Fix** | **53 von 58** |

Alle Fixes erfolgten ausschließlich über `wp_replace_in_post` (Feld
`post_content`, gezieltes Regex-/String-Search-Replace) und
`wp_yoast_update_post_seo` (nur Feld `meta_description`) — **nicht** über
`wp_wc_update_product`. Der in der Aufgabenstellung beschriebene
`_min_age`-Reset-Bug betrifft ausschließlich den `wp_wc_update_product`-
Schreibpfad; da dieser hier nicht verwendet wurde, war keine
`_min_age`-Nachkontrolle/-Wiederherstellung nötig. Stichprobenartig wurde
bei mehreren Produkten trotzdem per `wp_wc_get_product` geprüft, dass
`min_age` weiterhin "18" ist — unauffällig.

Jeder Fix wurde direkt danach per erneutem Read (`wp_get_cpt_item` bzw.
`wp_yoast_get_post_seo`) verifiziert.

## Nicht angetastet (kein Verstoß)

- "Profi-Tipp von Hanfjack:" als Abschnittsüberschrift kam in diesem Scope
  nicht vor.
- Fachbegriffe wie Wurzelwachstum, Nährstoffaufnahme, Terpenprofil,
  Harzproduktion etc. sind bei Düngemitteln unproblematisch und wurden
  nicht angerührt.
- SEO-Titel im Format "Produktname kaufen | Hanfjack" (z. B. bei den
  Terra-Aquatica- und Atami-Produkten) wurden **nicht** verändert — das
  ist eine Standard-Title-Tag-Konvention ("Produkt | Shopname"), keine
  CTA-Phrase im Sinne der Checkliste (die explizit "Jetzt bei Hanfjack
  kaufen/bestellen"-Sätze meint).

## Auffälligkeiten außerhalb des Scopes

- **Produkt 8321** (Terra Aquatica Starter Kit TriPart hartes Wasser):
  im `meta_data`-Array von `wp_wc_get_product` steht `_min_age` als
  leerer String (`""`), während das berechnete Top-Level-Feld `min_age`
  im selben Response "18" zeigt. Da dieses Produkt in diesem Durchgang
  nicht per `wp_wc_update_product` beschrieben wurde, ist unklar, ob das
  ein Altbestand-Datenproblem ist oder ein Anzeige-Artefakt der
  REST-API. Empfehlung: gesondert prüfen, ob die Altersfreigabe auf der
  Live-Seite korrekt greift.
- **Produkt 23570** (420flow Das Set Bloom + Booster): am Ende des
  Fließtexts stand vor dem Fix ein zweiter, isolierter Absatz nach der
  eigentlichen CTA-Zeile: *"Brauchst du noch Unterstützung bei der Wahl
  des passenden Substrats für dieses Set?"* — eine rhetorische Frage ohne
  Antwort/Link, wirkt wie ein abgebrochener Auto-Content-Baustein. Ist
  keine Checklisten-Verstoß (kein H1, keine CTA, kein leerer Inhalt) und
  wurde daher stehen gelassen; könnte aber in einem separaten Redaktions-
  Durchgang aufgeräumt werden.
- Mehrere Produkte auf Seite 5 von Dünger sowie ein Teil der
  Dünger-Sets-Produkte hatten beim ersten Lesen ein `date_modified`, das
  während dieser Session lag, obwohl noch kein eigener Schreibzugriff
  erfolgt war — vermutlich ein Yoast-SEO-Content-Score-Rebuild oder eine
  parallele Aktivität eines anderen Prozesses/Agenten außerhalb dieses
  Scopes. Kein Einfluss auf die hier vorgenommenen Fixes.
- Der Yoast-SEO-API-Zugriff (`mcp__Hanfjack__*`-Tools) war während dieser
  Session wiederholt und über längere Phasen (mehrfach 15–40 Minuten am
  Stück) durch ein serverseitiges Rate-Limit blockiert, vermutlich durch
  die kombinierte Last der drei parallel arbeitenden Agenten auf
  demselben Shop. Alle 58 Produkte im Scope wurden trotzdem vollständig
  abgearbeitet, die Bearbeitung hat dadurch aber deutlich länger gedauert
  als für die Produktzahl zu erwarten wäre.

## Vorgehen

1. Produktlisten per `wp_wc_list_products` (Kategorie 1132 Seite 5+6,
   Kategorie 4157 komplett) geholt.
2. Pro Produkt: Content per `wp_get_cpt_item` (leichtgewichtig) gelesen,
   Yoast-SEO per `wp_yoast_get_post_seo` geprüft; bei den 6 Terra-
   Aquatica-Produkten zusätzlich `wp_wc_get_product` für die vollen
   Meta-Daten.
3. Nur bei echten Verstößen (H1-Tag, CTA-Phrase im Fließtext oder in der
   Meta-Description, fehlende Meta-Description) gezielt gefixt — kein
   Komplett-Rewrite.
4. Jeder Fix direkt danach erneut gelesen und verifiziert.
