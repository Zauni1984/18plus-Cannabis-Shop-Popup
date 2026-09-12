# Lebensmittel (kompletter Kategoriebaum) — Hausstil- & SEO-Review

Review der Produkttexte und Yoast-SEO-Daten für den kompletten
Lebensmittel-Kategoriebaum (Kategorie-ID 51 mit allen Unterkategorien)
auf hanfjack.de. Teil des laufenden, shopweiten Content-Cleanups
(Headshop- und Growshop-Baum sind bereits fertig).

## Scope

- Elternkategorie **Lebensmittel** (ID 51) plus 9 Unterkategorien:
  Getränke (4146), Gewürze (4149), Hanfsamen (4152), Hanftee (4147),
  Hanföl (4150), Knabberhanf (4144), Mehl (4151), Rohkost (4148),
  Süßigkeiten und Snacks (4153).
- `wp_wc_list_products(category="<ID>", per_page=100, status="any")` pro
  Kategorie, dedupliziert über Produkt-ID.
- **30 eindeutige Produkte** im gesamten Baum (nach Dedupe). Die
  Elternkategorie 51 listet praktisch alle Produkte direkt mit — jede
  Unterkategorie-Zuordnung war eine Teilmenge davon, keine
  Unterkategorie enthielt ein Produkt, das nicht auch in Kategorie 51
  auftauchte.
- Kein Produktname enthielt "HEMPER", "Goody Glass" oder "Smoke
  Friends" — der Ausschluss griff nicht.
- **30 von 30 Produkten geprüft (100 % des Scopes)**, inkl. Content
  (`wp_wc_get_product` bzw. `wp_get_cpt_item`) und Yoast-SEO
  (`wp_yoast_get_post_seo` / eingebettetes `yoast_head_json`).

### Kategorie 4153 (Süßigkeiten und Snacks) — Prüfung "0 Produkte"

Die Kategorie zeigt im Storefront **0 sichtbare Produkte**, ist in der
Datenbank aber **nicht leer**: Sie enthält 5 Fremdmarken-Snacks (Twix
Wafer Rolls, Twix Salted Caramel, Ferrero Nutella Biscuit, Lion Black &
White, Cravingz Spongiez Cream), die alle Status **`private`** haben und
daher storefront-seitig unsichtbar sind. Alle 5 wurden trotzdem inhaltlich
geprüft (siehe unten).

## Befund

Zwei klar unterscheidbare Content-Herkünfte im Scope:

1. **"data-path-to-node"-Batch** (2 Produkte: Chillo Cannabis Ice Tea,
   CannaVita Bio Hanfsamenöl) — **exakt** das im Auftrag beschriebene
   Verstoßmuster: führendes `<h1 data-path-to-node="…">…</h1>` im
   Fließtext **und** eine abschließende Hanfjack-Bestell-CTA im Fließtext,
   **und** dieselbe CTA-Phrase am Ende der Yoast-Meta-Description. Das im
   Auftrag genannte Beispielprodukt **CannaVita Bio Hanfsamenöl (ID
   31109)** — Fund und Fix exakt wie beschrieben; zusätzlich fiel im
   selben Batch **Chillo Cannabis Ice Tea 250 ml (ID 31114)** mit
   identischem Muster auf.
2. **Sauberer Fließtext-Batch** (28 Produkte: alle 7 Sweedbar-Hanftees,
   alle 5 Sweedbar-Knabberhanf-Sorten, beide Sweedbar-Hanfsamen-Varianten,
   Sweedbar Hanf Protein Mehl, beide Hanföle von Alge, alle 4
   Alge-Rohkost-Produkte, beide Alge-Gewürze, PALACIO CannaSecco sowie die
   5 privaten Fremdmarken-Snacks) — kein H1, keine Hanfjack-CTA-Phrase,
   durchgehende Du-Anrede, Yoast bereits vollständig (Titel, Fokus-
   Keyword, Meta-Description ohne CTA). Einige Produkte nutzen ein `<h2>`
   bzw. `<h3>` am Anfang mit Produktnamen — laut Vorgabe unproblematisch
   (nur H1 ist der Verstoß, H2/H3 werden nicht angetastet).
- **EU-Compliance:** ein Fund. Produkt **Bio Hanf Tee Sweed Harmony (ID
  23183)**, ein Frauentee, enthielt die Formulierung "Schafgarbe ist
  traditionell dafür bekannt, zur Regulierung der Hormonaktivität
  beizutragen" — eine Funktionsaussage zur Hormonregulation, die bei
  einem Lebensmittel als nicht zugelassener Health Claim nach EU-VO
  1924/2006 zu werten ist. Auf eine neutrale Formulierung ohne
  Wirkversprechen umgestellt ("Schafgarbe ist eine traditionelle Zutat in
  Kräutertees für Frauen …"), restlicher Text (Ritual-/Wellness-Sprache
  ohne konkrete Wirkaussage) unverändert gelassen. Alle übrigen
  Nährwert- und Geschmacksangaben (Omega-3/6, Vitamine, "mehr Vitamin C
  als Orangen", Koffein-Warnhinweise etc.) sind normale, unproblematische
  Lebensmittelaussagen und wurden nicht angerührt.
- **Du-Anrede:** durchgehend korrekt (informell, groß/klein gemischt je
  nach Produkt — laut Vorgabe beides in Ordnung).
- **Leere Produktbeschreibungen:** keine gefunden, auch nicht bei den 5
  privaten Snack-Produkten.
- **Fehlende Yoast-Meta-Description:** bei den 5 privaten Fremdmarken-
  Snacks (Twix, Ferrero, Lion, Cravingz) ist keine Meta-Description
  gesetzt, wodurch Yoast automatisch `noindex` setzt. Da diese Produkte
  (a) `status: private` sind, also ohnehin nicht öffentlich erreichbar,
  und (b) reine Fremdmarken-Süßwaren ohne Hanf-Bezug sind, wurde dies
  nicht als Fix-Fall behandelt (kein "echter Verstoß" an einem live
  sichtbaren Produkt) — der Rest von Content und SEO-Titel war sauber.

## Gefixte Verstöße

| Fix | Anzahl Produkte |
|---|---|
| Führendes `<h1 data-path-to-node>`-Tag im Fließtext entfernt | 2 |
| … davon zusätzlich Hanfjack-CTA-Absatz im Fließtext entfernt | 2 |
| Yoast-Meta-Description gefixt (CTA-Phrase entfernt) | 2 |
| EU-Compliance: Health-Claim-Formulierung neutralisiert | 1 |
| Leere Produktbeschreibung neu geschrieben | 0 |
| **Produkte ganz ohne Verstoß** | 27 |

Betroffene Produkte im Detail:

- **CannaVita Bio Hanfsamenöl kaltgepresst, 250 ml (ID 31109)** —
  H1 entfernt, CTA-Absatz "Bestelle das CannaVita Bio Hanfsamenöl 250 ml
  jetzt bei Hanfjack." entfernt, Yoast-Meta-Description ("… Jetzt bei
  Hanfjack kaufen!") auf CTA-freie Formulierung umgestellt.
- **Chillo Cannabis Ice Tea 250 ml (ID 31114)** — H1 entfernt, CTA-Absatz
  "Bereit für den Chill-Moment? Bestelle den Chillo Cannabis Ice Tea
  jetzt online bei Hanfjack oder schau in unserem Shop in Beilngries
  vorbei!" entfernt, Yoast-Meta-Description ("… Jetzt bei Hanfjack
  bestellen oder in Beilngries kaufen!") auf CTA-freie Formulierung
  umgestellt. Der separate Hinweis auf Abholung im Laden in Beilngries
  (Absatz "Hanfjack-Special: Du bist in der Nähe? …") ist keine
  Kauf-CTA im Sinne der Vorgabe, sondern eine sachliche
  Click-&-Collect-Information, und wurde stehen gelassen.
- **Bio Hanf Tee Sweed Harmony 30g (ID 23183)** — Health-Claim-Satz zur
  Hormonregulation neutralisiert (siehe EU-Compliance oben).

Alle Content-Fixes erfolgten über `wp_replace_in_post` (Feld
`post_content`, Regex `^<h1[^>]*>.*?</h1>\s*` für das H1-Tag; exakte
bzw. regex-basierte Suche für die CTA-Absätze — der Live-Content nutzte
`\r\n`-Zeilenumbrüche, weshalb die Chillo-CTA-Entfernung eine
Regex-Variante mit `\s*` am Ende brauchte, statt einer reinen
Text-Suche). Die Yoast-Fixes liefen über `wp_yoast_update_post_seo`
(Feld `meta_description`) — **nicht** über `wp_wc_update_product`.

## `_min_age`-Kontrolle

Der beschriebene `_min_age`-Reset-Bug betrifft ausschließlich den
`wp_wc_update_product`-Schreibpfad. Da dieser für keinen einzigen Fix in
diesem Scope verwendet wurde (nur `wp_replace_in_post` und
`wp_yoast_update_post_seo`, die post_content bzw. Yoast-Postmeta
schreiben, aber nicht die WooCommerce-Produkt-Metadaten), war keine
systematische Nachkorrektur nötig. Trotzdem wurden alle 3 geänderten
Produkte per `wp_wc_get_product` nach dem Fix kontrolliert:

| Produkt-ID | `_min_age` vor dem Fix | `_min_age` nach dem Fix |
|---|---|---|
| 31109 (CannaVita Bio Hanfsamenöl) | `"18"` | `"18"` (unverändert) |
| 31114 (Chillo Cannabis Ice Tea) | `"12"` | `"12"` (unverändert) |
| 23183 (Sweed Harmony Tee) | `"18"` | `"18"` (unverändert) |

Der Wert `"12"` bei Chillo Cannabis Ice Tea war bereits vor diesem
Durchlauf so gesetzt (kein THC-Produkt, Altersfreigabe niedriger als bei
den meisten anderen Produkten) und wurde nicht verändert — nur
wiederhergestellt hätte werden müssen, was ein eigener Write
zurückgesetzt hätte, was hier nicht der Fall war.

## Nicht angetastet (kein Verstoß)

- Nährwert- und Ernährungsangaben (Omega-3/6, Ballaststoffe, Eiweiß,
  Vitamine, Mineralstoffe, glykämischer Index, "mehr Vitamin C als
  Orangen" etc.) sind bei Lebensmitteln unproblematisch und wurden nicht
  angerührt.
- Koffein-Warnhinweise ("Enthält Koffein – nicht für Kinder geeignet")
  und THC-Klarstellungen ("0,0 % THC", "EU-Nutzhanf mit weniger als 0,2 %
  THC") sind sachliche Produktinformationen, keine Health Claims.
- `<h2>`/`<h3>`-Überschriften mit Produktnamen am Textanfang (mehrere
  Rohkost- und Hanfsamen-Produkte) wurden laut Vorgabe nicht zu einer
  anderen Ebene geändert und auch nicht entfernt — nur `<h1>` ist der
  Verstoß.
- Fehlende Yoast-Meta-Description bei den 5 privaten Fremdmarken-Snacks
  (Twix, Ferrero, Lion, Cravingz) in Kategorie 4153 — siehe Begründung
  oben unter "Befund".
