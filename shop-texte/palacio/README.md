# Palacio: Inhaltsstoffe

## Der Befund

Keiner der 32 Palacio-Datensaetze auf hanfjack.de fuehrte eine
Inhaltsstoffliste. 28 davon trugen stattdessen diesen Satz:

> Eine vollstaendige Inhaltsstoffliste veroeffentlicht Palacio nicht in einer
> Form, die wir hier belegen koennten.

Der Satz ist falsch. palacio.cz ist eine SPA; die Daten kommen aus
`https://cml.palacio.cz/api/products/<id>?expand=1`, und dort liefert jeder
Artikel ein Feld `ingredients` mit der vollstaendigen INCI-Liste. Der Katalog
hat 521 Positionen und wird ueber einen Range-Header geblaettert
(`Range: x-items=0-99`), nicht ueber `page`.

## Die Zuordnung

Zuerst ueber die **EAN**, sonst ueber die **Katalognummer PALxxxx**, die im
Shop als MPN steht. `valid=1` blendet ausgelistete Artikel aus – ohne den
Filter stieg die Trefferquote von 24 auf 27 von 32.

Offen bleiben nur **14710 Tigerpack** und **14712 Fanpack**: Was genau drin
ist, sagt keine Quelle. Die drei anderen Bundles benennen ihren Inhalt im
eigenen Text, ihre Listen sind aus den Komponenten zusammengesetzt.

## Das Format

Wie bei Produkt 26719, dem einzigen Artikel im Shop, der es vorher schon
richtig machte:

```html
<h3 style="margin-top:1.8em">Inhaltsstoffe</h3>
<p>Wasser, Calciumcarbonat, Glycerin, Maisstaerke, ...</p>
<p><strong>INCI:</strong><br />Aqua, Calcium Carbonate, Glycerin, ...</p>
```

Deutsch und kommagetrennt fuer den Kunden, INCI unveraendert daneben – das
ist die Form, auf die sich die Kosmetikverordnung bezieht, und sie
uebersetzt zu wollen waere falsch.

`pal_inci.py` haelt das Woerterbuch: **195 INCI-Begriffe**, alle abgedeckt.
Unbekannte Begriffe wuerden unveraendert durchlaufen und beim Lauf gemeldet,
damit nichts stillschweigend verschwindet. Farbindizes werden zu
„Farbstoff CI 19140".

## Zwei Fallen in den Rohdaten

- **Der Extension-Block.** Manche `ingredients`-Felder haengen hinter
  „Extension" oder „Extension of the EU Cosmetics Regulation 1223/2009" eine
  zweite Liste an. Bei 499 (Hanf-Koerperbutter) ist sie **inhaltlich anders**
  – andere Farbstoffe, andere Oele. Das ist eine zweite Variante, keine
  Fortsetzung. `zerlegen()` schneidet ab „Extension" ab.
- **Doppelte Begriffe.** 13783 nennt Mentha Piperita Oil und Cinnamomum
  Cassia Leaf Oil je zweimal. Wiederholungen fallen raus.

## Was noch geaendert wurde

- Der Falschsatz ist in allen 28 Faellen entfernt.
- Bei **20190** stand „Ob die Zahnpasta Fluorid enthaelt, gibt die
  Produktbezeichnung nicht an". Jetzt steht die Liste im Text, also sagt der
  Hinweis, was sie hergibt: keine Fluoridverbindung.

Stand vor dem Lauf: `palacio_desc_vorher.json`.

---

# Drei neue Zahnpasten (17.09.2026)

**45365 / 45366 / 45367**, alle **veroeffentlicht**.

| | Refreshing | Sensitive | Whitening |
|---|---|---|---|
| Palacio-ID | 731 | 732 | 733 |
| MPN | PAL1432 | PAL1433 | PAL1431 |
| GTIN | 8595641303839 | 8595641303846 | 8595641303853 |
| SKU | HJ-6720902 | HJ-7854149 | HJ-4575145 |
| Bilder | 3 | 4 | 4 |
| Preis | 10,08 € netto = 12,00 € brutto | dito | dito |

Gemeinsam: 75 g, 0,095 kg, 13,2 × 3,5 × 3,5 cm, Bestand 5, kein
Lieferrueckstand, 1–3 Tage, Paket Standard, Kategorie Pflegeprodukte, Marke
Palacio, PALACIO CZ s.r.o.

**Preis:** 10,08 € netto = **12,00 € brutto**, angesagt und damit gesetzt;
alle drei sind veroeffentlicht. Der zuerst abgeleitete Preis war falsch: Der
Schwesterartikel 20190 (HEMP & DENT) steht bei 6,00 € brutto, die neuen
Pasten haben aber einen EK von ueber 6 €. Bei der Hausregel brutto = 2 x EK
kommt man auf 12 €. Ein Schwesterpreis traegt also nur, solange die Ware
auch im Einkauf vergleichbar ist – bei einer neuen, teureren Linie derselben
Marke nicht.

**Eine Herstellerangabe ist nicht uebernommen:** Der Whitening-Text behauptet,
Bambus-Aktivkohle remineralisiere den Zahnschmelz. Aktivkohle tut das nicht.
Alle anderen wertenden Angaben stehen als Herstellerangaben im Text
(„Palacio gibt an, dass …").

Die Bilder kamen ueber die Palacio-API und wurden nach WordPress
hochgeladen; das Hauptbild zeigt den Airless-Spender mit lesbarem Aufdruck
(„FLUORIDE FREE", „Beta-carotene & herb oil") und bestaetigt den Text.
