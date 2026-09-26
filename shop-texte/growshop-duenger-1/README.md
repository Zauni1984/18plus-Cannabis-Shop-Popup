# Dünger (Seite 1–2) — Hausstil- & SEO-Review

Review der Produkttexte und Yoast-SEO-Daten für Seite 1 und 2 der
WooCommerce-Kategorie **Dünger** (Kategorie-ID 1132) auf hanfjack.de. Teil
des laufenden, kategorienweiten Content-Cleanups im Growshop/Growbedarf-
Baum; zwei parallele Agenten haben zeitgleich Seite 3–4 bzw. Seite 5+ und
die Kategorie Dünger Sets bearbeitet.

## Scope

- `wp_wc_list_products(category="1132", per_page=100, status="any", orderby="id", order="asc")`,
  `page=1` und `page=2`.
- **200 Produkte**, IDs 8321–38629 (je 100 pro Seite, keine Lücken in der
  Kategorie-Zuordnung).
- Kein Produktname enthielt "HEMPER", "Goody Glass" oder "Smoke Friends" —
  der Ausschluss griff nicht.
- Mix aus `simple`- und `variable`-Produkten sowie 6 Produkten mit Status
  `private` (alte Terra-Aquatica-Starter-Kits).
- **200 von 200 Produkten geprüft** (100 % des Scopes).

## Befund

Anders als im Nachbar-Scope (Seite 3–4) war dieser Bereich **nicht**
einheitlich. Da `orderby=id/order=asc` die niedrigsten IDs zuerst liefert,
lagen hier überwiegend die älteren Produkte — und genau dort trat das im
Auftrag beschriebene Muster in voller Ausprägung auf. Drei klar
unterscheidbare Content-Generationen:

1. **Alte Terra-Aquatica-Starter-Kits (IDs 8321–8337, 6 Produkte, Status
   `private`)** — bereits sauber, offenbar in einem früheren Durchlauf
   bereinigt (kein H1, keine CTA, korrekte Du-Anrede).
2. **"data-path-to-node"-Batch** (u. a. Plagron, BioBizz-Try-Packs,
   BioTabs, HY-PRO, The Happy Cannabis, 420flow, BioTabs-Großgebinde) —
   **das** Verstoßmuster aus der Aufgabenstellung: führendes
   `<h1 data-path-to-node="…">…</h1>` **und** ein abschließender
   `<p><b>…jetzt bei Hanfjack…</b></p>`-Absatz im Fließtext, **und** exakt
   dieselbe CTA-Phrase ("Jetzt bei Hanfjack bestellen/kaufen! EAN: …") am
   Ende der Yoast-Meta-Description. Betraf u. a. das im Auftrag genannte
   Beispielprodukt **Plagron Hydro Roots (ID 14191)** – Fund und Fix exakt
   wie beschrieben.
3. **Frischer Content-Batch ohne `data-path`-Attribute** (Athena,
   Atami, Terra Aquatica Einzelflaschen/-Gebinde, Canna Aqua) — hier fehlte
   höchstens ein einfaches `<h1>Produktname</h1>` am Anfang, sonst
   durchgehend sauber (Du-Anrede, keine CTA, Yoast bereits gut). Die
   34-teilige Athena-Serie hatte **überhaupt kein** H1 und keine
   CTA-Phrase — sie stammt aus einem noch neueren, komplett CTA-freien
   Template (Abschluss "Falls du … Fragen hast, melde dich gerne bei uns"
   ist eine Service-Einladung, keine Kauf-CTA, daher kein Verstoß).
- **EU-Compliance:** keine Auffälligkeiten. Gärtnerische Fachbegriffe
  (Wurzelwachstum, Nährstoffaufnahme, Phosphor/Kalium-Wirkung, Zellstruktur
  etc.) sind bei Düngemitteln unproblematisch und wurden nicht angerührt.
- **Du-Anrede:** durchgehend korrekt (informell, Groß- oder Kleinschreibung
  gemischt je nach Produkt-Generation — beides laut Vorgabe in Ordnung).
- **"Profi-Tipp von Hanfjack:"** kam als Abschnittsüberschrift nicht vor.
- **Leere Produktbeschreibungen:** keine gefunden — auch die 6 privaten
  Terra-Aquatica-Produkte hatten vollständigen Content.
- **Ein Sonderfall:** Produkt 11972 (BioTabs Guerrilla Tabs 20 Stück) hatte
  einen sauberen Fließtext ohne H1/CTA, aber **keine Yoast
  meta_description überhaupt** (Feld war leer) — ergänzt.

## Gefixte Verstöße nach Kategorie

| Fix | Anzahl Produkte |
|---|---|
| Führendes H1-Tag im Fließtext entfernt | 142 |
| … davon zusätzlich Hanfjack-CTA-Absatz im Fließtext entfernt | 58 |
| Yoast-Meta-Description gefixt (CTA-Phrase entfernt oder fehlendes Feld ergänzt) | 20 |
| Leere Produktbeschreibung neu geschrieben | 0 |
| **Produkte ganz ohne Content-Verstoß** | 58 |

Die 58 CTA-Fixes sind eine Teilmenge der 142 H1-Fixes (Batch 2 aus dem
Befund oben). Die restlichen 84 H1-Fixes (Batch 3: Atami, Terra Aquatica,
Canna Aqua) hatten nur das isolierte H1-Tag, sonst keinen Verstoß — genau
das im Auftrag beschriebene Muster für "neuere Produkte".

Alle Content-Fixes erfolgten über `wp_replace_in_post` (Feld
`post_content`, Regex `^<h1[^>]*>.*?</h1>\s*` bzw. für den CTA-Absatz
`<p[^>]*><b[^>]*>[^<]*jetzt bei hanfjack[^<]*</b></p>\s*`, case-insensitiv)
— **nicht** über `wp_wc_update_product`. Der beschriebene
`_min_age`-Reset-Bug betrifft ausschließlich den `wp_wc_update_product`-
Schreibpfad; da dieser für keinen einzigen Fix in diesem Scope verwendet
wurde (auch die 20 Yoast-Fixes liefen über `wp_yoast_update_post_seo`,
das nur Yoast-Postmeta schreibt), war keine systematische
`_min_age`-Nachkontrolle nötig. Stichprobenartig per `wp_wc_get_product`
geprüft (u. a. Produkt 14191, Plagron Hydro Roots): `min_age` weiterhin
`"18"` — unauffällig.

Jeder Fix wurde direkt über den Rückgabewert von `wp_replace_in_post`
bzw. `wp_yoast_update_post_seo` verifiziert (`replacements_count: 1` /
`updated_fields`). Der komplette Athena-Batch (33 Produkte, IDs
30518–30551) wurde zusätzlich mechanisch mit derselben H1/CTA-Regex
durchlaufen (nicht nur stichprobenartig gelesen), um Konsistenz mit dem
Rest des Scopes sicherzustellen — Ergebnis überall `replacements_count: 0`,
also bestätigt sauber.

## Nicht angetastet (kein Verstoß)

- Fachbegriffe wie Wurzelwachstum, Nährstoffaufnahme, Phosphor-/
  Kalium-Gehalt, Zellstruktur, Enzymwirkung etc. sind bei Düngemitteln
  unproblematisch.
- "Falls du Fragen hast, melde dich gerne bei uns" (Athena-Serie) ist eine
  Support-Einladung, keine Kauf-CTA im Sinne der Checkliste — nicht
  verändert.
- SEO-Titel-Zusätze wie "… kaufen | Hanfjack" wurden nicht angerührt —
  Standard-Keyword-Ergänzung im Title-Tag, keine CTA-Phrase im Fließtext
  oder in der Meta-Description.

## Auffälligkeiten außerhalb des Scopes

- **Massive API-Rate-Limits während der Bearbeitung**, vermutlich durch
  die drei parallel auf derselben hanfjack.de-Instanz arbeitenden Agenten
  gemeinsam verursacht (nicht kategoriespezifisch, kein Text-/SEO-Befund,
  aber erwähnenswert für den Gesamtauftrag: einzelne Schreib-Calls
  benötigten wiederholt 20–90 s Wartezeit, bevor sie durchgingen).
- Kein inhaltlicher Befund außerhalb des Text-/SEO-Scopes (keine leeren
  Preisfelder, keine kaputten Kategorien o. ä. aufgefallen).

## Vorgehen

1. Produktlisten für Seite 1 und 2 der Kategorie 1132 abgerufen
   (`orderby=id`, `order=asc` für stabile, nicht überlappende Grenzen).
2. Pro Produkt(-familie) Content per `wp_get_cpt_item` oder
   `wp_wc_get_product` gelesen, um das jeweilige Template-Muster zu
   identifizieren (3 Generationen, siehe Befund).
3. H1-Tag und CTA-Absatz per `wp_replace_in_post` (Regex,
   `max_replacements` implizit 1 durch spezifisches Pattern) auf jedes der
   200 Produkte einzeln angewendet, jeweils über `replacements_count`
   verifiziert — bei 0 Treffern kein Verstoß vorhanden, keine Änderung.
4. Für jedes Produkt mit gefundenem Fließtext-CTA zusätzlich
   `wp_yoast_get_post_seo` geprüft und die Meta-Description ohne
   CTA-Phrase neu formuliert (120–160 Zeichen) über
   `wp_yoast_update_post_seo`.
5. Bei fehlender Yoast-Meta-Description (Produkt 11972) neue
   Beschreibung ergänzt statt nur CTA zu entfernen.
6. Stichproben über alle Produktfamilien (inkl. vollständiger
   Athena-Batch) zur Bestätigung, dass die "sauberen" Bereiche wirklich
   keinen Verstoß enthalten.
7. `min_age` bei einem bearbeiteten Produkt stichprobenartig verifiziert
   (weiterhin `"18"`).
8. README geschrieben, lokaler Git-Commit (kein Push).

## Melde-Zusammenfassung

- **200 Produkte geprüft** (IDs 8321–38629, Seite 1+2 der Kategorie 1132),
  0 ausgeschlossen (kein HEMPER/Goody Glass/Smoke Friends-Treffer).
- **142 Produkte gefixt** (H1-Tag entfernt), davon **58** zusätzlich mit
  Hanfjack-CTA-Phrase im Fließtext, **20** mit Yoast-Meta-Description-Fix
  (CTA entfernt oder fehlendes Feld ergänzt). 58 Produkte waren bereits
  vollständig sauber.
- Auffälligkeit außerhalb des Scopes: durchgehend hohe API-Rate-Limits
  während der gesamten Bearbeitung (vermutlich durch die 3 parallel
  arbeitenden Agenten verursacht), kein inhaltlicher Befund.
