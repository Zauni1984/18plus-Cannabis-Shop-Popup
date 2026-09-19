# VK-Prüfung Bloomtech-Sortiment (hanfjack.com, Stand 09.09.2026)

Abgleich Shop-VK (brutto, aus regular_price × MwSt) gegen `bloomtech-vk-liste.csv`
Spalte "Std. VK Brutto". 673 Produkte abgleichbar.

**Achtung: B2C-Preise werden auf hanfjack.de gepflegt, .com übernimmt sie per Sync-Plugin.
Korrekturen also auf .de, nicht auf .com.**

## Ergebnis
| | Anzahl |
|---|---|
| VK stimmt mit Liste überein | 451 |
| VK im Shop **über** Liste | 186 |
| VK im Shop **unter** Liste | 36 |

## Die 107 gedeckelten Produkte
87 davon haben den korrekten Listenpreis – dort ist bereits Bloomtechs eigene Kalkulation dünn
(Median Faktor Listen-VK/EK = 1,48 gegenüber 1,79 im Gesamtsortiment). Kein Pflegefehler,
sondern Sortimentsrealität bei Markenhardware (AC Infinity, Can-Fan, Dimlux) und Büchern.
20 der 107 haben einen abweichenden Shop-VK, siehe `bloomtech-vk-abweichungen.json`.

## Handlungsbedarf
1. **12 Bücher unter dem gebundenen Ladenpreis** – Verstoß gegen die Buchpreisbindung,
   vorrangig zu korrigieren.
2. **10 weitere Produkte deutlich unter Liste** mit dünner Marge, u. a.
   Can Original Filter 150BFT (349,90 statt 499,90), Can-Fan RK Ø125 (49,90 statt 79,90),
   Can-Fan RK Ø250 (79,90 statt 109,90), Phonic Trap Ø315 10 m (99,90 statt 139,90).
3. **186 Produkte über Listenpreis** – kein Fehler, aber Wettbewerbsnachteil gegenüber
   bloomtech.de. Größte Ausreißer: Prima Klima PK300/315-EC (1.319,90 statt 999,90),
   Canna Boost 10 L (654,35 statt 409,90), Canna Boost 5 L (341,15 statt 219,90).

Vollständige Liste aller 222 Abweichungen: `bloomtech-vk-abweichungen.json`.
