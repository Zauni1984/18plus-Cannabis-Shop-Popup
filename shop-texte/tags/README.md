# Produkt-Tags auf hanfjack.de

Stand: 16.09.2026 · **3976 Produkte** · **3296 aktive Tags**, 235 leer geworden.

## Benennungslogik

Deutsche Rechtschreibung: **Substantive groß, Adjektive und Partikeln klein**, Abkürzungen
groß, Markennamen in ihrer Eigenschreibweise.

    feminisiert · autoflowering · photoperiodisch · regulär · hoher Ertrag
    Hanfsamen · Indoor Grow · Aktivkohlefilter · Indica-dominant
    THC · CBD · LED · RQS → Royal Queen Seeds
    HEMPER · PURIZE · BioTabs · Spider Farmer · CAN-Filters

Erlaubt sind Buchstaben, Ziffern und Bindestriche. Kein Komma, Dezimaltrennzeichen ist der
Punkt. Die Raute bleibt nur vor einer Ziffer stehen, weil sie dort zum Sortennamen gehört
(`Skunk #1`, `NL#5`); der Doppelpunkt nur im Verhältnis (`1:1 THC CBD`).

## Was gemacht wurde

| Schritt | Umfang |
| --- | ---: |
| Tags umbenannt (Schreibweise) | 885 |
| Tags zusammengeführt | 61 |
| Tags entfernt (Wirkstoff- und Maßangaben) | 182 |
| Produkte umgehängt | 2395 |
| Produkte neu betaggt (hatten keine Tags) | 417 |
| Doppelte Tag-Namen aufgelöst | 9 |

Alle Läufe fehlerfrei.

### Wirkstoff- und Maßangaben entfernt

`16-24 % THC`, `1 Liter`, `10.2 cm`, `High THC` und 178 weitere sind weg. Diese Werte stehen
seit dem Attribut-Durchgang in eigenen Produktattributen und sind dort filterbar — als Tag waren
sie doppelt gepflegt und brachten die Kommazahlen mit.

**Nicht entfernt wurden Produktarten**: `CBD Samen` bleibt, denn das ist eine Warengruppe und
keine Messgröße. Die erste Fassung der Regel hätte sie mitgenommen.

### Inhaltliche Zusammenführungen

| bleibt | eingeschmolzen |
| --- | --- |
| feminisiert (1299) | feminisierte Samen, feminisierte Hanfsamen, feminisierte Seeds |
| Hanfsamen (1067) | Cannabis Samen, Cannabis Samen kaufen, Zuchtsamen, Cannabis Seeds |
| Indoor Grow (479) | Indoor, Indoor Growing, Indoor Anbau |
| autoflowering (451) | autoflower, autoflower Samen, autoflowering Hanfsamen |
| Outdoor Grow (347) | Outdoor, Freiland, Outdoor Growing |
| hoher Ertrag (226) | XL-Ertrag, ertragreich, hohe Erträge |
| Royal Queen Seeds (180) | RQS |
| USA Genetik (124) | US-Genetik |
| Cali Weed (103) | Cali, Cali Genetik, Kalifornische Genetik |

### Neu betaggt

417 Produkte hatten überhaupt keine Tags — überwiegend Trimmer, Dünger und Pressen. Sie haben
jetzt 2 bis 6 Tags, gespeist **ausschließlich aus vorhandenen Strukturdaten**: Marke, Kategorie
und die Produktattribute (Düngerart, Anwendungsphase, Form, Substrat, Material, Variante, Typ).
Erfunden wurde nichts. 60 Produkte bleiben ohne Tags, weil bei ihnen zu wenig Strukturdaten
vorliegen.

Produkte ohne Tags: **425 → 60**. Durchschnitt 6.9 Tags je Produkt.

## Beilngries

**`Beilngries` markiert den eigenen Lagerbestand** — 697 Produkte, die der Shop selbst vorrätig
hat. Der Tag ist damit einer der wichtigsten im System und wurde zusammen mit `Hanfjack`
ausdrücklich von jeder Umbenennung, Zusammenführung und Entfernung ausgenommen.

Ohne diesen Hinweis wäre er beinahe als bedeutungsloser Ortsname entfernt worden.

## Ein Fehler im Ablauf

Beim Zusammenführen wurden die Produkte korrekt auf **einen** Term konsolidiert — der trug
danach aber noch den alten Namen: `Cannabis Samen` statt `Hanfsamen`, `Indoor` statt
`Indoor Grow`, `Freiland` statt `Outdoor Grow`. Ursache war, dass Umbenennen und Umhängen
unabhängig voneinander entschieden, welcher der beiden Terme der Kanon ist.

Betroffen waren 9 Terme. `tag_dubfix.py` hat sie korrigiert, die dabei entstandenen
Namensdubletten aufgelöst (133 Produkte umgehängt, 9 Zwillinge gelöscht) und den überlebenden
Termen den sauberen Slug gegeben — `cannabis-samen` → `hanfsamen`, `freiland` → `outdoor-grow`,
`top%c2%b7max` → `top-max`.

## Offen

Die **235 leer gewordenen Tags** stehen noch im System; du wolltest sie selbst löschen.

Der lange Schwanz bleibt bestehen: rund 1900 Tags hängen an 1 bis 3 Produkten. Das sind
überwiegend Sortennamen, die du ausdrücklich behalten wolltest, dazu Motive und Modellbezeichnungen.
