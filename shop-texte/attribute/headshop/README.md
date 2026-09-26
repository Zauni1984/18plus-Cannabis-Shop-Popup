# Headshop-Attribute auf hanfjack.de

Stand: 16.09.2026 · **768 Produkte** · davon tragen **706 (91 %)** mindestens ein Attribut.

Bongs 106, Dabbing 86, Pre Rolled Papers 80, Filter Extra Slim 75, Pipes 71, Rolling Trays 66,
Zippo 43, Vaporizer 42, Aufbewahrung 41, Papers 30, dazu ein Dutzend kleinerer Gruppen.

## Neue Attribute

| Attribut | ID | Beispielwerte |
| --- | --- | --- |
| Herkunft | 64 | Deutschland, China, Indien |
| Format | 65 | King Size, Medium, 1 1/4, 51 × 89 mm |
| Motiv | 66 | Big Face, Luxe Black Marble |
| Brennstoff | 67 | Zippo Premium-Feuerzeugbenzin, Butangas |
| Länge | 68 | 11,4 cm |
| Durchmesser | 69 | 61 mm |

## Das Problem mit diesen Texten

Die Datenzeilen im Headshop enthalten überwiegend Fließtext, nicht Attributwerte:

> Form: Ein Ende rund, ein Ende spitz zulaufend
> Länge: King Size, die längste Cone-Variante dieser Serie
> Gewicht: Rund 30 g für das komplette Set

Ein naiver Parser schreibt solche Sätze als Attributwert in den Filter. `hs_attr2.py` verwirft
sie über drei Regeln:

1. **Satzbau-Erkennung.** Enthält der Wert ein finites Verb oder eine Konjunktion — „ist", „hat",
   „sorgt", „damit", „sodass", „zulaufend" — ist es ein Satz und kein Attributwert.
2. **Längengrenze je Attribut.** Eine Farbe hat höchstens 26 Zeichen, ein Material 42.
3. **Einheitenprüfung.** Länge, Durchmesser, Gewicht und Maße müssen eine Maßeinheit tragen,
   sonst werden sie verworfen. Aus „Rund 30 g für das komplette Set" wird `30 g`.

Bei „Format" reicht das nicht, weil „Flache Bauform mit abgerundeten Kanten" alle drei Regeln
besteht. Dort wird zusätzlich gegen eine Liste echter Formate geprüft — King Size, Slim, Medium,
1 1/4, Cone, Rolls oder eine Maßangabe.

**Herkunft** wird auf das Land reduziert: Aus „China, Zolltarifnummer 7013.99.0000" und
„Hergestellt in China" wird beides Mal `China`. Die Zolltarifnummer gehört nicht in einen
Produktfilter.

## Ergebnis

| Attribut | befüllt |
| --- | ---: |
| Herkunft | 302 |
| Material | 266 |
| Abmessungen | 96 |
| Format | 82 |
| Inhalt | 80 |
| Motiv | 48 |
| Farbe | 38 |
| Brennstoff | 38 |
| Gewicht | 23 |
| Länge | 15 |
| Durchmesser | 10 |
