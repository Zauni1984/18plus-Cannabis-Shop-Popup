# Growin-Sortiment – Preisvergleich gegen die B2B-Preisliste

Quelle: `growin-preisliste-b2b-de.csv` (WS6100 PriceList B2B DE, 8.289 Positionen, alle 19 % MwSt).
Spalte R = Endkundenpreis (brutto), Spalte S = Händlerpreis / EK (netto).

Growin-Artikelnummer = Shop-SKU (6-stellig). Keine Überschneidung mit Bloomtech (5-stellig).

## Umfang
| | Anzahl |
|---|---|
| Growin-Artikel auf hanfjack.de | 303 |
| davon in der Preisliste gefunden | 303 (100 %) |
| Steuerklasse | durchgängig Standard 19 % |
| aktive Sale-Preise | keine |

## 1. B2C-Preise (hanfjack.de) gegen Spalte R
**Alle 285 Artikel mit Listen-Endkundenpreis stimmen exakt überein** – die größte Abweichung
beträgt 1 Cent und ist reine Rundung (der Shop rechnet netto × 1,19).

Anders als beim Bloomtech-Sortiment stehen die Growin-Preise also **auf dem Listenpreis, nicht
auf Listenpreis − 10 Cent**. Wenn die gleiche Regel gelten soll, wären das 285 Anpassungen.

Die restlichen 18 Artikel haben in der Liste **keinen Endkundenpreis**, weil Growin sie mit
„Nicht für Endkunden = Ja" führt. Der Shop hat sie mit EK × 1,6 kalkuliert. Sieben davon sind
auf hanfjack.de **veröffentlicht** (Twister T6 Vakuum-Bypass, CenturionPro DBT0, Master Trimmer
MT Dry 100 LiTE, MT Tumbler 200 LiTE, Twister T6 Spare Tumbler, Qnubu Zip-Beutel 7 g und 1 g,
MT DRY 100 LiTE Ersatzklinge) – im Namen steht zwar „nur für Gewerbe", verkaufbar sind sie
im B2C-Shop trotzdem. Die elf Biobizz-Juju-Royal-Artikel stehen korrekt auf `draft`.

## 2. Marge gegen Spalte S (EK)
Faktor Shop-Netto / EK: Median **1,61**, Minimum 0,17, Maximum 9,54.

**12 Artikel werden unter dem eigenen Einkaufspreis verkauft** (Details in `growin-unter-ek.json`).
Wichtig: Der Shop ist dabei **nicht falsch eingepflegt** – er steht exakt auf Growins eigenem
Endkundenpreis. In diesen zwölf Fällen liegt Growins empfohlener Endkundenpreis selbst unter
dem Händlerpreis, den sie einem berechnen.

Die größten Posten:

| SKU | Artikel | Shop netto | EK | Verlust je Stück |
|---|---|---|---|---|
| 111164 | Twister Tandem T2 Trimming System mit Leaf Collectors | 25.966,39 | 31.500,00 | −5.533,61 |
| 111166 | Twister Tandem T4 Trimming System mit Leaf Collectors | 18.901,75 | 20.546,55 | −1.644,80 |
| 112144 | VitaLink Buddy, 10 L | 97,47 | 571,00 | −473,53 |
| 112347 | MX ICE 200 LITE | 3.151,26 | 3.600,00 | −448,74 |
| 109650 | Twister T2 Wet Tumbler (Ersatzteil) | 875,63 | 1.300,00 | −424,37 |
| 111165 | Twister T4 Rails, Triple | 1.424,37 | 1.596,00 | −171,63 |
| 111338 | Twister T4 CHEVRON, Antriebsrad | 26,89 | 40,50 | −13,61 |
| 109646 | Twister T2 Doppelschalter 2er-Pack | 111,76 | 124,08 | −12,32 |
| 109655 | Twister T4 Filter Bag 300 Micron | 86,55 | 92,00 | −5,45 |
| 109645 | Twister T2 Tumbler V-Belt schwarz | 49,58 | 54,56 | −4,98 |
| 112141 | VitaLink Buddy, 250 ml | 6,54 | 8,00 | −1,46 |
| 112142 | VitaLink Buddy, 1 L | 14,28 | 15,00 | −0,72 |

Bei **VitaLink Buddy 10 L** ist der EK von 571,00 € auffällig: 250 ml kosten 8,00 €, 1 L 15,00 €,
5 L 29,00 € – 571,00 € für 10 L passt nicht in die Reihe und sieht nach einem Fehler in Growins
Liste aus. Bitte beim Lieferanten nachfragen.

Weitere 9 Artikel liegen unter 5 % Marge (u. a. Master Trimmer LIO 40 Freeze Dryer mit exakt
0 % – Shop-Netto 7.600,00 € = EK 7.600,00 €).

## 3. B2B-Preise auf hanfjack.com
Für das Growin-Sortiment sind **keine B2B-Preise gesetzt**. Stichprobe SKU 103662 (Trimbox,
hanfjack.com ID 37093): `wwpro_wholesale_prices` liefert für beide Rollen `source: none`,
also greift der normale Endkundenpreis. Die 10-%- / 30-%-Aufschläge, die für Bloomtech und
Aqua Master gesetzt wurden, fehlen hier komplett.

## 4. Aktionspreise in der Liste
78 Positionen haben einen **Händler-Sonderpreis unter dem regulären EK**, teils deutlich
(VitaLink Turbo+ 5 L: 114,00 → 41,46 €; Advanced Hydroponics Amino 1 L: 31,95 → 23,15 €).
Ein Endkunden-Sonderpreis ist dabei nirgends hinterlegt. Falls die B2B-Preise auf Basis von
EK + 10 % / + 30 % gesetzt werden, ist zu entscheiden, ob der reguläre EK oder der Aktions-EK
die Grundlage ist.

## Offene Entscheidungen
1. Sollen die 285 B2C-Preise wie bei Bloomtech auf **Listenpreis − 10 Cent** gesenkt werden?
2. Was passiert mit den 12 Artikeln unter EK – Preis anheben oder aus dem Verkauf nehmen?
3. Sollen die 7 veröffentlichten „nur für Gewerbe"-Artikel auf hanfjack.de sichtbar bleiben?
4. B2B-Preise für Growin auf hanfjack.com setzen (EK + 10 % / EK + 30 %) – auf Basis
   regulärer EK oder Aktions-EK?
