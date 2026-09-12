# hanfjack.de – B2C-Preise Bloomtech-Sortiment

## Regel
Endkundenpreis = **Bloomtech-Listenpreis (brutto) − 0,10 €**.

Ausnahmen laut Vorgabe:
- **Bücher**: exakter gebundener Ladenpreis (keine 10 Cent Abzug) – separat erledigt, 12 Titel, Steuerklasse `reduzierter-preis` (7 %).
- **Billigartikel unter 10 €** (Listenpreis): unverändert gelassen – 94 Artikel.

## Umfang
| | Anzahl |
|---|---|
| Bloomtech-Artikel mit Listenpreis | 676 |
| davon Bücher (bereits erledigt) | 12 |
| davon unter 10 € (nicht angefasst) | 94 |
| **geändert** | **570** |

## Rechenweg
- hanfjack.de speichert **netto**. Alle 570 Artikel haben `tax_class = ""` (Standard 19 %) und `tax_status = taxable` – vor dem Schreiben einzeln per Batch-Read geprüft, keine Ausnahme.
- `netto = (Listenpreis_brutto − 0,10) / 1,19`, gespeichert mit 4 Nachkommastellen.
- Kontrolle: jeder Datensatz wurde nach dem Schreiben über das zurückgelieferte `price_html` gegen den Zielbruttopreis verifiziert.

## Ergebnis
- 569 von 570 treffen den Zielpreis **exakt**.
- 1 Ausnahme: SKU **17414** (AC Infinity Vorfiltertuch 200 mm). Ziel wäre 21,89 € gewesen; bei 19 % liegt dieser Wert zwischen zwei Netto-Cent-Werten (18,39 → 21,88 / 18,40 → 21,90). Gesetzt wurde 18,39 netto = **21,88 €**, also 1 Cent unter Ziel statt darüber.

## Preisrichtung
- **556 Artikel wurden günstiger** (lagen über der Bloomtech-Liste).
- **13 Artikel wurden teurer** (lagen unter der Liste), u. a.:
  - 14557 Can Original Filter 150BFT 2100 m³/h Ø315 mm: 349,90 → 499,80 €
  - 12227 Phonic Trap Ø315 mm 10 m: 99,90 → 139,80 €
  - 12705 Can-Fan RK Ø125/310 m³/h: 49,90 → 79,80 €
  - 12706 Can-Fan RK Ø250/830 m³/h: 79,90 → 109,80 €

Größte Senkungen:
  - 16855 Prima Klima PK300/315-EC: 1.319,90 → 999,80 €
  - 12711 Canna Boost 10 L: 654,35 → 409,80 €
  - 11332 Canna Boost 5 L: 341,15 → 219,80 €

## Offene Punkte
- **SKU 16930 (Birchmeier Rückenspritze PR3, ID 39999)** hat einen aktiven Sale-Preis von 119,00 € netto (141,61 € brutto). Der reguläre Preis steht jetzt korrekt auf 199,80 € brutto, der Aktionspreis liegt aber weiterhin **unter dem eigenen B2B-Preis**. Braucht eine Entscheidung.
- **SKU 12999 (Aptus Super PK 5 Liter, ID 39446)** war veröffentlicht, hatte aber **gar keinen Preis**. Ist jetzt auf 201,5126 € netto (239,80 € brutto) gesetzt.
- Rundungshinweis: hanfjack.de rundet den Nettopreis für die Anzeige auf 2 Nachkommastellen, bevor die Steuer addiert wird. Bei 19 % sind dadurch nicht alle Bruttopreise exakt darstellbar (siehe 17414).
