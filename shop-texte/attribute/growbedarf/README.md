# Growbedarf-Attribute auf hanfjack.de

Stand: 16.09.2026 · **877 Produkte** in der Kategorie „Growshop" ohne die 467 Dünger,
die einen eigenen Durchgang hatten.

**673 der 877 tragen jetzt mindestens ein technisches Attribut.**

## Warum kein einheitliches Schema

Der Growbedarf ist keine Warengruppe, sondern ein Dutzend. Eine LED-Leuchte hat keinen
Luftdurchsatz, ein Pflanztopf kein Lichtspektrum, ein Extraktionsbeutel weder das eine noch das
andere. Deshalb gibt es kein gemeinsames Attributset, sondern je Gruppe die Kennwerte, die dort
zählen. Eine Gesamtabdeckung über alle 877 Produkte wäre eine unsinnige Zahl — sie stünde für
jedes Produkt im Nenner, dem das Attribut gar nicht zusteht.

## Neue Attribute

| Attribut | ID | Einheit |
| --- | --- | --- |
| Luftdurchsatz | 55 | m³/h |
| Anschluss Ø | 56 | mm |
| Abmessungen | 57 | cm, B × T × H |
| Material | 58 | Werkstoff |
| Gewicht | 59 | kg |
| Schutzart | 60 | IP-Code |
| Ausgelegte Fläche | 61 | cm bzw. m² |
| Maschenweite | 62 | µm |
| Presskraft | 63 | t |

Weitergenutzt werden die vorhandenen: Leistungsaufnahme, Spannung, Frequenz, PPF, PPE,
Lichtspektrum, Lumen, Geräuschpegel, Inhalt, Größe, Farbe, WEEE-Nummer.

## Herleitung

Die Beschreibungstexte führen die Werte als „Label: Wert"-Zeilen — `Anschlussdurchmesser: 315mm`,
`Gesamt-Photonen-Wirkungsgrad: 3,45 µmol/J`, `Kapazität Praktisch: 3000 m³/h`. `gb_attr.py`
liest diese Zeilen, dazu Werte, die frei im Produktnamen stehen (`Can Lite Filter 3000m³/h
Ø315mm`) und Stichpunkte ohne Doppelpunkt (`• Außenmaß 90 x 90 x 200 cm`).

**Jeder Wert muss die Einheit seines Attributs tragen.** Ohne diese Prüfung landet
`Anschluss: USB, Anzeige am Computerbildschirm` — aus einem Mikroskop — im Feld für den
Anschlussdurchmesser. Genau das passierte im ersten Lauf bei 101 Produkten.

Beim Material wird zusätzlich auf Verneinung geprüft, und `rostfreiem Stahl` zählt als Edelstahl,
nicht als Stahl.

Angehängte Zweitangaben werden abgetrennt: aus `215 lbs / 97,5 kg, Transportgewicht mit
Blattauffang: 190 kg` wird `215 lbs / 97,5 kg`.

## Abdeckung je Gruppe

| Gruppe | Produkte | Kennwert | Abdeckung |
| --- | ---: | --- | ---: |
| LED Growlampen | 75 | Leistungsaufnahme | 89 % |
| | | Abmessungen | 68 % |
| | | PPF | 60 % |
| | | Lichtspektrum | 60 % |
| | | PPE | 45 % |
| Zu- und Abluft | 64 | Anschluss Ø | 84 % |
| | | Leistungsaufnahme | 51 % |
| | | Luftdurchsatz | 46 % |
| Aktivkohlefilter | 40 | Luftdurchsatz | 87 % |
| | | Anschluss Ø | 77 % |
| | | Gewicht | 57 % |
| Erde & Substrate | 40 | Inhalt | 75 % |
| Pflanzentöpfe | 32 | Material | 65 % |
| | | Inhalt | 40 % |
| Growboxen | 71 | Material | 53 % |
| | | Abmessungen | 49 % |
| Extraktion & Pressen | 44 | Material | 38 % |
| | | Maschenweite | 31 % |
| | | Presskraft | 22 % |
| Trimmer & Erntehelfer | 144 | Material | 30 % |
| | | Abmessungen | 19 % |
| | | Leistungsaufnahme | 15 % |

## Offen

**Trimmer & Erntehelfer (144 Produkte) ist die größte Gruppe mit der dünnsten Abdeckung.**
Der Grund steht in den Texten: Ein großer Teil sind Zubehörteile — Ersatzklingen, Gummiwalzen,
Auffangschalen —, für die es keine technischen Kennwerte gibt. Bei den Maschinen selbst nennen
die Hersteller Durchsatz in kg/h, den der Shop bisher nicht als Attribut führt. Das wäre der
nächste sinnvolle Schritt für diese Gruppe.

**Gewicht** steht bei den Growboxen nur bei 16 %, weil die Texte dort meist das Artikelgewicht
der Verpackung nennen statt des Produktgewichts. Das eine als das andere einzutragen wäre falsch.
