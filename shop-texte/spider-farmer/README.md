# Spider Farmer: Samsung raus, Bridgelux rein (24.09.2026)

Spider Farmer hat die Dioden der Lampen umgestellt. Wichtig: **nicht bei allen
Modellen.** Stand der Herstellerseiten heute:

| Modell | Dioden 2026 | Belegstelle |
|---|---|---|
| SF1000 | Bridgelux, PPE 2,5 µmol/J, PPF 249,2 µmol/s | `products/sf-1000-led-grow-light/` – „upgraded to Premium Bridgelux" |
| SF1000D | Bridgelux | Vergleichstabelle der SF-Seiten |
| SF2000 | Bridgelux 3030, PPE 2,7 | `products/sf-2000-led-grow-light/` – „Advanced Bridgelux 3030" |
| SF2000Pro | Bridgelux | `products/spider-farmer-sf2000pro-led-grow-light/` – Seitentitel „2026 … Bridgelux" |
| SF4000 | Bridgelux, PPE 2,7 | `products/sf-4000-led-grow-light/` |
| SE1000W, SE1500, SE3000, SE4500, SE5000, SE7000 | Bridgelux, bis 2,9 µmol/J | Sammelseite `se-series-led-grow-light` |
| **SF7000** | **weiter Samsung LM301B** | `products/sf7000-foldable-led-grow-light/` |

Die Vergleichstabelle auf den SF-Produktseiten ist an einer Stelle veraltet:
sie fuehrt SF2000Pro noch mit Samsung LM301H EVO, waehrend die Produktseite des
Pro selbst schon Bridgelux nennt. Deshalb zaehlt die jeweilige Produktseite.

## Was im Shop geaendert wurde

| ID | SKU | Aenderung |
|---|---|---|
| 16213 | SF-1000 | Text, Kurztext, SEO, Name (+2026), Tag – das einzige Produkt mit Samsung im Fliesstext |
| 16214 | SF-1000-D | Samsung-Restangabe „3,14 µmol/j single diode" entfernt, Version 2026 |
| 16215 | SF-2000 | LED-Chips auf Bridgelux 3030 benannt, Name (+2026), SEO-Titel, Tag |
| 16216 | SF-2000PRO | Version 2026, Tag |
| 16218 | SF-4000 | **Samsung stand im Produkttitel** – Name neu, Hinweis eindeutig, SEO, Tag |
| 16219 | SF-7000 | Hinweis richtiggestellt: bleibt Samsung LM301B, Diode jetzt benannt |
| 16227, 16229, 16231 | SE-3000, SE-5000, SE-1000W | Tag (Texte standen schon auf Bridgelux) |
| 16238, 16239, 16240 | SF-Zelt-Sets | Tag „Samsung 301H" getauscht, Jahresangabe im Namen |

Tags: `Samsung LM301H EVO` (6271) und `Samsung 301H` (6555) sind von diesen
Produkten entfernt, `Bridgelux LEDs` (6284) ist gesetzt, bei der SF2000
zusaetzlich `Bridgelux 3030` (11400). Die beiden Samsung-Begriffe bleiben als
leere Terms stehen – Loeschen ist nicht rueckholbar, und ohne Produkte richten
sie keinen Schaden an. Aufraeumen kann man sie im Backend.

**Offen:** Im Attributblock der SF-1000 stehen weiterhin die alten, nach
Spannung aufgeschluesselten Messwerte (PPF 245,6–246,3 µmol/s, 16654 lm). Der
Hersteller nennt fuer die Version 2026 pauschal 249,2 µmol/s, was jetzt im Text
steht. Wer beides gleichziehen will, muss die Attribute nachfuehren.

## Spider-Farmer-Tags sind ein Zoo

Fuer dieselbe Sache existieren vier Terms: `Bridgelux LEDs` (6284),
`Bridgelux LED` (7107), `Bridgelux 3030` (11400), `Bridgelux 3030 LED` (11427).
Zusammenfuehren waere sinnvoll, ist aber nicht Teil dieses Auftrags.

## Kontrolle nach dem Durchlauf

Alle zwoelf Produkte geschrieben, danach geprueft: kein Samsung-Rest mehr ausser
den drei beabsichtigten Stellen – SF-1000 und SF-4000 nennen die frueheren
Samsung LM301H EVO im historischen Nebensatz, die SF-7000 nennt ihre Samsung
LM301B als aktuelle Bestueckung.

Shopweite Suche nach „Samsung" und „LM301" liefert 20 Produkte. Die 17 anderen
sind **AC Infinity** (IONFRAME EVO3/4/6/8, IONBEAM S11/S16, Advance-Grow-Sets,
Stecklings-Gewaechshaeuser), **BloomStar** (FLUXshield-Reihe, Wingcommander) und
eine **Solux**-Leuchte. Die tragen tatsaechlich Samsung-Dioden und sind von der
Spider-Farmer-Umstellung nicht betroffen – dort bleibt alles stehen.

## Nacharbeit: Attribute und Schlagworte (24.09.2026)

**Attribute** der beiden alten Datensaetze auf den Aufbau der Schwesterprodukte
gebracht – neun Merkmale, Einzelwerte statt nach Spannung aufgeschluesselter
Reihen:

| | SF-1000 (16213) | SF-1000-D (16214) |
|---|---|---|
| Leistungsaufnahme | `100W±5%@AC120V-277V` → `100 W ± 5 %` | `100W ±5% @AC110-130V` → `100 W ± 5 %` |
| PPF | `245,6 / 245,7 / 246,3 umol/S` → `249,2 µmol/s` | `199 / 211 μmol/S` → `211 µmol/s` |
| Spannung | `100-277V AC` → `100–277 V (Wechselstrom)` | `110-130V / 220-240V` → `100–277 V (Wechselstrom)` |
| Ausgelegte Flaeche | neu: `3 × 3 ft` | `3 × 3 ft` → `2 × 2 ft (rund 60 × 60 cm)` |
| Abmessungen, Gewicht | neu: `32,5 × 29,0 × 5,9 cm`, `1,92 kg` | unveraendert |
| Lumen, Amp, Frequenz | entfernt | entfernt |

Lumen, Amp und Frequenz sind bewusst weg: bei der SF-1000 stammten die Werte
aus dem Samsung-Datenblatt, fuer die SF-1000-D gibt es keine belegten neuen,
und die Schwesterprodukte fuehren diese Merkmale ohnehin nicht. Fuer eine
Pflanzenlampe zaehlt PPF, nicht Lumen.

Beim Spektrum bleibt es bei **660–665 nm**. Das aktuelle SF-1000-Datenblatt
nennt dort 650–665 nm, waehrend SF2000 und SF4000 bei ansonsten identischer
Tabelle 660–665 nm fuehren – das sieht nach Tippfehler auf Herstellerseite aus,
und ein Ausreisser ist kein Grund, einen plausiblen Wert zu ueberschreiben.

**Schlagworte** zusammengefuehrt: `Bridgelux LED` (7107), `Bridgelux 3030`
(11400) und `Bridgelux 3030 LED` (11427) laufen auf `Bridgelux LEDs` (6284),
elf Produkte umgehaengt, die drei Begriffe geloescht. `Samsung 301H` (6555) war
leer und ist ebenfalls geloescht. Stand danach: `Bridgelux LEDs` 23 Produkte,
`Samsung LM301H EVO` 4 Produkte – die vier AC-Infinity-IONFRAME mit echten
Samsung-Dioden. Der Term bleibt deshalb bestehen.

Die 3030-Angabe steht weiterhin im Text der SF-2000, wo der Hersteller sie
ausdruecklich nennt.
