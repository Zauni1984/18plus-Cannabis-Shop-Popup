# Growzubehör — Hausstil- & SEO-Review

Review der Produkttexte und Yoast-SEO-Daten für die WooCommerce-
Elternkategorie **Growzubehör** (6523) und ihre neun Unterkategorien
**Controller** (6243), **Erntescheren** (4225), **Handschuhe** (5831),
**Lupen & Mikroskope** (16087), **Messgeräte** (13408),
**Pflanzentöpfe** (855), **Pumpen** (13416), **Trimmer** (6240) und
**ph-Wert** (13023). Teil des laufenden, kategorienweiten
Content-Cleanups des Hanfjack.de-Shops im Growshop/Growbedarf-Baum.

## Scope

Alle Produkte wurden pro Kategorie per `wp_wc_list_products(category="<ID>",
per_page=100, status="any")` aufgelistet (auch private/draft erfasst,
Kategorie 6523 benötigte wegen der Produktmenge zwei Seiten). Nach
Deduplizierung über alle zehn Kategorien (viele Produkte sind sowohl
direkt in 6523 als auch in einer Unterkategorie einsortiert):

- **146 eindeutige Produkte** im Scope (Schätzung war ca. 134 — die
  höhere Zahl erklärt sich durch Seite 2 von Kategorie 6523, die auch
  Produkte enthält, die nur direkt der Elternkategorie zugeordnet sind).
- Kein Produktname enthielt "HEMPER", "Goody Glass" oder "Smoke
  Friends" — der Ausschluss griff nicht (erwartungsgemäß, reine
  Grow-Equipment-Kategorien).
- **Alle 146 Produkte wurden vollständig geprüft** (Content-Pass:
  H1/CTA im Fließtext; Yoast-Pass: SEO-Titel/Meta-Description/CTA).

## Befund

Drei klar unterscheidbare Content-Generationen:

### 18 Spider-Farmer-Produkte (Auftraggeber-Priorität)

Erntescheren- und Messgeräte-Zubehör aus dem Januar/Februar-2026-Batch,
IDs 16270, 16271, 16274, 16275, 16276, 16284, 16285, 16293, 16295,
16296, 16297, 16298, 21453, 21465, 27641, 27714, 27715, 27719.

- **Alle 18** hatten ein führendes `<h1 data-path-to-node="…">Titel</h1>`
  am Beginn der `description` — entfernt (nicht auf H2 herabgestuft).
- **Alle 18** hatten die CTA-Phrase "Jetzt bei Hanfjack kaufen/bestellen/
  sichern!" (bzw. Varianten wie "Jetzt Ernte sichern bei Hanfjack!" oder
  ein am Satzende angehängtes "… bei Hanfjack!") in der Yoast-
  `meta_description` — entfernt und durch produktspezifischen,
  120–160 Zeichen langen Text ersetzt.
- "Profi-Tipp von Hanfjack:" kam als akzeptierte Abschnittsüberschrift
  vor und wurde nicht angetastet.
- **100 %-Trefferquote** für beide Verstoßarten in dieser Gruppe —
  bestätigt die Auftraggeber-Einschätzung, dass hier besondere Sorgfalt
  nötig war.

### Aqua Master Tools / BloomStar (Mai 2026, ~30 Produkte)

pH-/EC-Messgeräte, Controller, Dosierpumpen, Eichflüssigkeiten,
Testkits und Erntescheren aus einem einheitlichen Mai-2026-Batch
(IDs 31414–31583).

- Bei einigen Produkten (v. a. der "Eichflüssigkeit"/"Testkit"-Familie,
  z. B. 31414) fand sich zusätzlich ein Fließtext-Absatz mit CTA
  ("Bestelle … jetzt bei Hanfjack") am Textende, abgetrennt durch ein
  `<hr>` — per Regex entfernt.
- **Rund die Hälfte** der Gruppe hatte die CTA-Phrase "Jetzt bei
  Hanfjack (online) kaufen/bestellen!" in der Meta-Description — entfernt.
- Die andere Hälfte (v. a. die Pumpen-/Dosierschlauch-Serie C10/C20/C30
  und die PTFE-Dosierschläuche) war bereits sauber: keine CTA, Länge
  im 120–160-Zeichen-Fenster, thematisch treffend.

### AC Infinity / Can / Carson / PK-DGTL (30.–31. August 2026, ~50 Produkte)

Astscheren, Trimmer, Trockengestelle, Klimasensoren, Controller,
Stofftöpfe, Lupen und USB-Mikroskope aus einem sehr frischen,
einheitlichen Bulk-Batch.

- **Durchgehend keine CTA-Phrasen**, weder im Fließtext noch in der
  Meta-Description — reiner, sauberer Content ohne Verstöße in dieser
  Gruppe.
- Titel folgen einem schlankeren Schema ohne "| Hanfjack"-Suffix
  (z. B. "AC Infinity Astschere Edelstahl mit Reinigungsset") — das ist
  eine Stilvariante, keine Regelverletzung, und wurde nicht angeglichen.

### Ältere Bestandsprodukte (Root Pouch, PROPOT, Topf-*, Meditrade, Messbecher u. a.)

- Häufigstes Muster: Meta-Description **unter 120 Zeichen**, oft mit
  einer nackten EAN-Nummer am Ende ohne abschließenden Satz — wirkt wie
  ein automatisch generierter Produktfeed-Text. In allen Fällen zu
  einem vollständigen, thematisch passenden Satz auf 120–160 Zeichen
  erweitert.
- Wiederkehrendes CTA-Muster **"Im Growshop Hanfjack kaufen."** bzw.
  **"Bei Hanfjack kaufen."** als knapper Meta-Description-Schluss bei
  mehreren älteren Topf-/Pflanzentopf-Produkten (u. a. 7428, 7429, 7431,
  7432, 7433, 11910, 11911, 11912, 17485, 30219) — entfernt.
- **Zwei Meta-Descriptions fehlten komplett** (kein `description`-Feld
  in `yoast_head_json`: 11909 PROPOT Stofftopf 7L, 11921 Root Pouch
  Anzuchttöpfe 1L, 17631 Caluma 3-in-1 pH-EC-Temp Messgerät) — neu
  geschrieben.

## Anomalie außerhalb der Standard-Checkliste: formelle "Sie"-Anrede

Bei vier Produkten der "Kleiner/Mittelgroßer Topf"-Serie (30225 Topf
0,5L, 30227 Topf 1L, 30230 Topf 7L, 30233 Topf 11L) verwendete die
Meta-Description durchgängig **"Entwickeln Sie gesunde Pflanzen…"** —
also die formelle Sie-Anrede statt der im Hausstil verlangten
informellen Du-Anrede (Regel 3). Zusätzlich war der Text bei drei der
vier Produkte **wortidentisch** (generische Vorlage ohne Bezug zur
jeweiligen Topfgröße). Beides wurde behoben: informelle Anrede,
größenspezifischer Text. Dies ist der einzige Treffer dieser Art im
gesamten Scope — alle anderen geprüften Produkte verwendeten
durchgehend die informelle Anrede oder eine anredefreie, sachliche
Formulierung.

## Nicht angetastet (kein Verstoß)

- "Profi-Tipp von Hanfjack:" als Abschnittsüberschrift — akzeptiertes
  Stilelement, nicht verändert.
- Fachbegriffe (pH-Wert, EC-Wert, VPD, IP67, Kalibrierung etc.) sind bei
  Grow-Equipment unproblematisch, die strenge Health-Claim-Regel gilt
  hier nicht.
- Keines der 146 Produkte hatte eine leere `description`/
  `short_description` — anders als im Hintergrund vermutet, liegen die
  bekannten leeren Entwürfe (Status private/draft, ID ab ca. 32000)
  außerhalb dieses Scopes (Beleuchtung/Growbox-Kategorien). Der
  `_min_age`-Schreibpfad über `wp_wc_update_product` musste daher nicht
  verwendet werden.
- Keine erkennbar falsch zugeordneten Yoast-Blöcke (Meta-Description
  einer anderen Produktvariante) im vollständig geprüften Scope
  gefunden.

## Vorgehen

1. Produktlisten per `wp_wc_list_products(category="<ID>", per_page=100,
   status="any")` für alle zehn Kategorien geholt (6523 inkl. Seite 2)
   und die 146 eindeutigen Produkt-IDs per Deduplizierung ermittelt.
2. Pro Produkt: Content per `wp_get_cpt_item` gelesen, führendes
   `<h1>`-Tag per Regex (`wp_replace_in_post`, `regex=true`) entfernt,
   CTA-Blöcke im Fließtext (Aqua-Master-Familie) ebenfalls per Regex
   entfernt.
3. Anschließend vollständiger zweiter Durchgang: Yoast-SEO für **alle**
   146 Produkte per `wp_yoast_get_post_seo` geprüft (SEO-Titel, Meta-
   Description auf CTA-Phrasen, Länge 120–160 Zeichen, thematische
   Passgenauigkeit, Anrede). Verstöße per `wp_yoast_update_post_seo`
   (Feld `meta_description`) korrigiert.
4. Jeder Fix direkt danach verifiziert: `replacements_count` bei
   `wp_replace_in_post`, `updated_fields` bei `wp_yoast_update_post_seo`.
   Ein Produkt (31414) wurde im ersten Yoast-Durchlauf übersehen und
   beim abschließenden Ist/Soll-Abgleich der 146 IDs nachgeholt.
5. Kein Produkt in diesem Scope hatte eine leere Beschreibung — die im
   Auftrag beschriebene `_min_age`-Nachprüfung nach
   `wp_wc_update_product`-Writes war daher nicht anzuwenden.

## Ergebnis in Zahlen

| Kategorie | Wert |
|---|---|
| Produkte im Scope | 146 |
| Davon Spider-Farmer-Produkte | 18 (alle mit H1- und CTA-Verstoß) |
| H1-Tag entfernt | 86 |
| CTA-Phrase im Fließtext entfernt | 10 |
| Yoast-Meta-Description korrigiert (CTA, Länge, fehlend, Anrede) | 80 |
| Yoast-Meta-Description bereits sauber | 66 |
