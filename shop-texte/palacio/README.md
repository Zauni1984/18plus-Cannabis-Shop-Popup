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

---

# Drei Geschenksets KON004, KON008, KON009 (17.09.2026)

| | KON009 | KON004 | KON008 |
|---|---|---|---|
| Shop-ID | 45379 | 45384 | 45385 |
| SKU | HJ-2635225 | HJ-9768207 | HJ-2876138 |
| GTIN | 8595641303990 | 8595641304409 | 8595641304485 |
| VK brutto | 25,00 € | 30,00 € | 30,00 € |
| netto | 21,01 € | 25,21 € | 25,21 € |
| Bestand | 12 | 12 | 10 |
| Gewicht | 0,73 kg | 1,61 kg | 0,65 kg |
| Bilder | 4 | 4 | 4 |

Alle drei heissen beim Hersteller „PALACIO Soothe Beauty Set"; der Zusatz im
Shop-Namen trennt sie.

**Status: `private`** – die Lieferung ist noch nicht da. Gilt auch fuer die
drei Zahnpasten 45365–45367.

## Die Inhaltsstoffe kommen aus den Komponenten

Kein Set fuehrt ein eigenes `ingredients`-Feld. Die Komponenten stehen aber
im Katalog und sind auf den Kartonbildern lesbar:

| Set | Komponenten |
|---|---|
| KON009 | PAL1198 Cannacool 200 ml · PAL1122 Konopny Gel Forte 200 ml · PAL1197 Cannahot 200 ml |
| KON004 | PAL0355 Haarshampoo 500 ml · PAL1192 Koerperbutter 200 ml · PAL1224 Cremeduschgel 500 ml |
| KON008 | PAL0441 Gesichtscreme 50 ml · PAL1229 Nachtcreme 50 ml · PAL1209 Reinigungsschaum 150 ml |

Bei KON009 stehen alle drei Komponenten auch selbst im Shop (509, 510, 508).

**Das Bild hat die Zuordnung entschieden.** Der Katalog fuehrt vier
Hanf-Shampoos zu 500 ml und acht Duschgele; die Kurzbeschreibung sagt nur
„Vlasovy sampon 500ml" und „Sprchovy gel 500 ml". Auf dem Karton steht
„KREMOVY SPRCHOVY … s konopnym olejem" – das ist PAL1224. Beim Shampoo bleibt
eine Unschaerfe, die aber folgenlos ist: **die INCI-Listen der vier
500-ml-Varianten sind wortgleich.**

## Ein Tippfehler in den Herstellerdaten

PAL0441 und PAL1229 fuehren **„Triethenolamine"**. Die Nummer gibt es nicht;
gemeint ist Triethanolamine. Beleg: 511 und 512 tragen dieselbe Rezeptur und
dort steht sie richtig. `pal_inci.TIPPFEHLER` korrigiert das, statt den
Fehler in den Shop zu tragen.

Das Woerterbuch steht damit bei **201 Begriffen**.

## Nachtrag zum Preis

Bei KON009 war zuerst kein VK genannt. Die Hausregel fuer Bundles laesst sich
am Bestand ablesen: Sportpack und Cannapack stehen auf exakt der Summe ihrer
Komponenten (2 × 6,30 € netto = 12,60 €), Flexpack ebenso (2 × 10,08 € =
20,16 €). Fuer KON009 waeren das 3 × 6,30 € = 18,90 € netto. Angesagt wurden
dann 25,00 € brutto – die Rechnung war also nur ein Vorschlag, kein Ergebnis.

## Noch nicht gelistet

KON005, KON006 und KON007 – die drei uebrigen Sets der Reihe, alle mit EAN im
Katalog.

---

# Fehlende Herstellerbilder nachgetragen (17.09.2026)

**17 Produkte, 33 Bilder.** Der Bestand ging von 50 auf 83 Bilder.

| ID | vorher | nachher | | ID | vorher | nachher |
|---|---|---|---|---|---|---|
| 497 | 1 | 2 | | 512 | 1 | 3 |
| 499 | 2 | 4 | | 1877 | 1 | 4 |
| 500 | 1 | 2 | | 1878 | 1 | 3 |
| 501 | 1 | 3 | | 1879 | 1 | 6 |
| 502 | 1 | 2 | | 13783 | 2 | 4 |
| 503 | 1 | 2 | | 13786 | 2 | 4 |
| 504 | 1 | 4 | | 20192 | 3 | 4 |
| 508 | 4 | 6 | | 510 | 2 | 4 |
| 511 | 4 | 5 | | | | |

Bestehende Bilder blieben unveraendert und behielten ihre Reihenfolge, die
neuen kommen dahinter. Kein Hauptbild hat gewechselt.

## Zwei Verfahren waren Sackgassen

1. **Byte-Hash.** Der Shop speichert WebP, Palacio liefert PNG und JPEG.
   Dieselbe Aufnahme hat nie denselben Hash – das Ergebnis waere gewesen:
   „alle 71 Bilder sind neu".
2. **dHash.** Naheliegend, traegt aber auch nicht: Die Shop-Bilder sind enger
   beschnitten als die Herstellerbilder. Der Versatz allein bringt identische
   Aufnahmen auf 35 von 256 Bit Abstand – ueber jeder brauchbaren Schwelle.
   Wieder waeren alle 71 als neu gezaehlt worden.

## Was traegt: erst den Weissrand weg

Alle Aufnahmen sind Freisteller auf Weiss. Nach dem Zuschnitt auf die
Bounding Box der nicht-weissen Pixel, Skalierung auf 48 × 48 Graustufen und
Autokontrast trennen die Werte sauber:

- gleiche Aufnahme: **2,6 bis 7,5** RMSE
- anderes Motiv: **76 bis 155**

Die Schwelle liegt bei 30 und damit weit von beiden Gruppen entfernt. Von den
71 Kandidaten blieben so **33** uebrig; 38 waren dieselben Aufnahmen in
anderer Kodierung.

Pillow war im Container nicht installiert (`pip install pillow`).

## Zwei Produkte haben eine neue Verpackung

Der Sichtvergleich der Grenzfaelle zeigte mehr als nur neue Perspektiven:

- **512 Bio Oel Fusscreme** – der Shop fuehrte die alte, schlichte Tube; der
  Hersteller zeigt eine neu gestaltete („CANNABIS FOOT CREAM with bio hemp
  oil").
- **1877 After Sun Koerperlotion** – im Shop das alte „NATURE'S BEST"-Design,
  beim Hersteller das neue „Herbal Therapy"-Layout.

Beide alten Aufnahmen stehen weiterhin an erster Stelle. Ob das Hauptbild
umgehaengt werden soll, haengt daran, welche Ware tatsaechlich im Lager liegt
– das entscheidet der Blick ins Regal, nicht der Katalog.

## Ein abgebrochener Lauf

Der erste Durchgang starb nach 11 von 17 Produkten an einem Connection Reset
– Uploads von mehreren MB brechen gelegentlich ab. Das Bild 45413 war da
schon hochgeladen, aber noch keinem Produkt zugeordnet. Der zweite Lauf hat
es wiederverwendet statt neu hochzuladen; `hoch()` hat jetzt eine
Wiederholung mit wachsender Wartezeit.

Kontrolle danach: 0 fehlende Bilder, keine Dubletten in den 27 Galerien.

## PAL1270 nachgelistet (22.09.2026)

| | |
|---|---|
| Produkt | PALACIO Hanf-Massagegel mit Panthenol 200 ml |
| ID / SKU | 45596 / HJ-7763975 |
| MPN / EAN | PAL1270 / 8595641302825 |
| Preis | 6,30 EUR brutto, Grundpreis 37,49 EUR je Liter |
| Bestand | 25, Lieferzeit 1-3 Tage, Paket Standard |
| Quelle | `cml.palacio.cz/api/products/396?expand=1` |

Der Preis ist **gesetzt wie bei allen anderen 200-ml-Gelen von Palacio im
Shop** (Cannacool 509, Cannahot 508, Forte Sport Gel 1878/1879 stehen alle auf
6,30 EUR). Eine Preisvorgabe lag nicht vor - falls der EK dagegen spricht,
ist das die Stelle zum Nachbessern.

Die Inhaltsstoffe stehen vollstaendig auf Deutsch und kommagetrennt im Text,
darunter die INCI-Liste im Original: 43 Bestandteile, davon 18 Pflanzenauszuege.
Die beiden Farbstoffe CI 19140 und CI 42051 stehen als "Farbstoff CI ..." -
mehr gibt die Herstellerliste nicht her.

Die beiden Herstellerbilder sind angehaengt (Medien 45597, 45598). Verknuepft
ist es nach dem Muster der internen Verlinkung: Upsells 509, 508, 1879, 19954
(die uebrigen Palacio-Gele), Cross-Sells 20192 Hanf-Badesalz und 504 Hanfsalbe.
