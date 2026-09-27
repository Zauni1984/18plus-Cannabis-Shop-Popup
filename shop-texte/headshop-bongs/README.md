# Headshop-Bongs – Stil-Check (Kategorie Bongs, ID 3852)

Hausstil-Prüfung der Kategorie **Bongs** (ID 3852, 120 Produkte gesamt).
Wie beim Headshop-Mini-Projekt war dies **kein** Full-Rewrite: Es ging
darum, tatsächliche Verstöße gegen die 6-Punkte-Checkliste (kein H1 im
Text, keine Hanfjack-Marken-CTA, durchgehende Du-Anrede, EU-Konformität,
saubere Yoast-SEO-Felder, saubere `short_description`) zu finden und
gezielt zu fixen — nicht mehr.

## Marken-Ausschluss

Wie angewiesen wurden **101 von 120 Produkten** ausgeschlossen, weil ihr
Name "HEMPER" enthält (100 auf Seite 1 der Auflistung plus "HEMPER Cereal
Box Wasserfilter-Glas XL" auf Seite 2) — diese wurden nicht gelesen, nicht
geöffnet und nicht angefasst. "Goody Glass"- oder "Smoke Friends"-Produkte
kamen in dieser Kategorie nicht vor, es gab also nichts zusätzlich
auszuschließen. Bearbeitet/geprüft wurden die verbleibenden **19
Produkte**: 2× Amsterdam, 3× Thug Life OG Series V2, 14× Atomic/Knistermann
(überwiegend Status `private`, aus einem älteren Produktimport).

## Befund

Zwei unterschiedliche Verstoßmuster traten auf, jeweils klar auf eine
Produktgruppe begrenzt:

**1. H1-Tag + Hanfjack-Marken-CTA im Fließtext** — betraf ausschließlich
die drei **Thug Life OG Series V2**-Produkte (22822 Green, 22819 Black,
22817 Umber). Alle drei trugen noch die alte, vor-refaktorierte
Content-Struktur mit `<h1 data-path-to-node="…">Produktname: Marketing-
Claim</h1>` am Anfang sowie einen abschließenden CTA-Absatz nach Schema
"Bestelle die Thug Life OG Series V2 … jetzt bei Hanfjack – Deinem
Experten für …!", direkt vor dem ein verwaister `<hr>` stand. Die beiden
**Amsterdam**-Produkte (22828, 22825), die laut Auftrag in einer früheren
Runde bereits gefixt wurden, wurden verifiziert — sie sind sauber
(kein H1, kein CTA-Absatz, durchgehende Du-Anrede), keine erneute
Bearbeitung nötig.

**2. Fehlende Yoast-`meta_description`** — betraf 5 der 14
Atomic-Produkte (14829 Glasbong Cup, 14830 Acrylbong Flower 30cm, 14831
Acrylbong Bubble 40cm, 14832 Acrylbong Bubble 30cm, 14833 Glasbong 9mm
H38cm). Bei diesen fünf war das Feld `_yoast_wpseo_metadesc` komplett
leer (Yoast zeigte im Head-Kommentar "diese Seite zeigt keine
Meta-Beschreibung"), `seo_title` und `focus_keyword` waren dagegen
gesetzt. Der Fließtext dieser fünf Produkte selbst war bereits sauber
(kein H1, keine CTA, durchgehende Du-Anrede) — reine SEO-Feld-Lücke, kein
Content-Verstoß.

Die übrigen 11 Produkte (9 weitere Atomic/Knistermann-Produkte sowie die
beiden bereits verifizierten Amsterdam-Produkte) waren bei allen sechs
Checklistenpunkten bereits konform und wurden **nicht** verändert. Auffällig:
Viele der älteren Atomic-Texte nutzen durchgehend die informelle
Kleinschreibung "du" statt "Du" — das ist grammatikalisch korrektes
Deutsch für die Du-Anrede und wurde nicht als Verstoß gewertet, da die
Checkliste nur "durchgehende Du-Anrede" (nicht "Sie") verlangt, keine
bestimmte Groß-/Kleinschreibung.

Die meisten der 14 Atomic-Produkte tragen den Post-Status `private`
(vermutlich ein älterer, deaktivierter Import) mit `noindex, follow` im
Yoast-Robots-Feld. Sie wurden trotzdem regulär geprüft — laut Auftrag gibt
es keine Sonderbehandlung nach Status, und eine fehlende Metadescription
ist unabhängig vom Index-Status ein Hausstil-/Hygiene-Mangel.

## Vorgehen

- H1- und CTA-Fixes in `post_content` ausschließlich über
  `wp_replace_in_post` (exakter String-Modus, kein Regex nötig), **nie**
  über `wp_wc_update_product`/`wp_wc_batch_update_products` — Letzteres
  hätte beim Schreiben von `description` ungewollt `_min_age`
  (Pflichtfeld "18") löschen können.
- Bei allen drei Thug-Life-Produkten identisches Muster: (1) H1-Tag exakt
  entfernt, (2) CTA-Absatz-Anfang bis vor den Halbgeviertstrich "–" durch
  einen Platzhalter ersetzt (Encoding des "–"-Zeichens im Suchstring
  führte sonst zu 0 Treffern), (3) Rest des CTA-Absatzes samt Platzhalter
  entfernt, (4) der dadurch verwaiste `<hr data-path-to-node="22" />`
  vor dem (jetzt leeren) Absatzende separat entfernt. Jeder Schritt wurde
  per erneutem `wp_wc_get_product`-Read verifiziert.
- Fehlende Metadescriptions über `wp_yoast_update_post_seo`
  (`meta_description`) ergänzt — Kerninhalt (Maße, Material, Ausstattung,
  EAN) aus dem jeweiligen Fließtext/Kurztext übernommen, Zielgröße
  120–160 Zeichen, Stil an die bereits vorhandenen Metadescs der
  Schwesterprodukte angeglichen.
- `seo_title` und `focus_keyword` waren bei allen 19 Produkten bereits
  sinnvoll gesetzt und wurden nicht angefasst.

## Vollständige Produktliste (19/19 bearbeitet/geprüft)

- [x] 22828 – Amsterdam Limited Edition Special Shape Glaspfeife (26cm) — verifiziert, bereits konform (früherer Fix bestätigt)
- [x] 22825 – Amsterdam Limited Edition Öl-Glaspfeife (24cm) — verifiziert, bereits konform (früherer Fix bestätigt)
- [x] 22822 – Thug Life OG Series V2 Green — FIXED (H1 + Hanfjack-CTA-Absatz + verwaister `<hr>` entfernt)
- [x] 22819 – Thug Life OG Series V2 Black — FIXED (H1 + Hanfjack-CTA-Absatz + verwaister `<hr>` entfernt)
- [x] 22817 – Thug Life OG Series V2 Umber — FIXED (H1 + Hanfjack-CTA-Absatz + verwaister `<hr>` entfernt)
- [x] 14835 – Atomic Glasbong H 39cm – Ø40mm – Schl. 18,8mm (Status: private) — OK
- [x] 14834 – Atomic Glasbong H 46cm – Ø50mm – Schl. 18,8mm (Status: private) — OK
- [x] 14833 – Atomic Glasbong 9mm dickes Glas H 38cm (Status: private) — FIXED (Yoast metadesc fehlte komplett → ergänzt)
- [x] 14832 – Atomic Acrylbong Bubble Design Höhe 30cm (Status: private) — FIXED (Yoast metadesc fehlte komplett → ergänzt)
- [x] 14831 – Atomic Acrylbong Bubble Design Höhe 40cm (Status: private) — FIXED (Yoast metadesc fehlte komplett → ergänzt)
- [x] 14830 – Atomic Acrylbong Höhe 30cm Flower Design (Status: private) — FIXED (Yoast metadesc fehlte komplett → ergänzt)
- [x] 14829 – Atomic Glasbong Cup (Status: private) — FIXED (Yoast metadesc fehlte komplett → ergänzt)
- [x] 14828 – Atomic Glas Eisbong Candy 4mm dickes Glas 45cm (Status: private) — OK
- [x] 14827 – Atomic Glasbong 9mm dickes Glas Höhe 30cm (Status: private) — OK
- [x] 14825 – Atomic Glasbong Rainbow 7mm dickes Glas 33cm Höhe (Status: private) — OK
- [x] 14824 – Atomic Glasbong H36cm – Ø50mm – Schl. 18,8mm (Status: private) — OK
- [x] 14419 – Glasbong Flying Dutchman 22cm ⌀65mm (Status: private) — OK
- [x] 12322 – Atomic Glasbong Ø40mm Höhe 36cm (Status: private) — OK
- [x] 12319 – Atomic Smoke Blower Rasta + Tragetasche (Status: private) — OK

## Abschluss

Von 120 Produkten in der Kategorie Bongs waren 101 HEMPER-Produkte laut
Anweisung ausgeschlossen. Die verbleibenden 19 Produkte wurden geprüft:
**8 Produkte** hatten tatsächliche Hausstil- oder Yoast-Hygiene-Verstöße
und wurden gezielt gefixt (3× H1+CTA-Entfernung bei Thug Life OG Series
V2, 5× fehlende Yoast-Metadescription bei Atomic-Produkten); **11
Produkte** waren bereits konform und wurden nicht verändert, darunter die
beiden Amsterdam-Produkte aus der früheren Runde. `_min_age` und alle
sonstigen Post-Meta-Felder blieben unangetastet, da ausschließlich
`wp_replace_in_post` (Feld-Scope `post_content`) und
`wp_yoast_update_post_seo` verwendet wurden.
