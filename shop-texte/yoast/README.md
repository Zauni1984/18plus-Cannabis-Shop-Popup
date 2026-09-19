# Yoast-Titel und Meta-Descriptions

## Ausgangslage

Gemeldet war, dass bei einigen Produkten Yoast SEO fehlt. Die Pruefung
aller 4441 Produkte fand mehr als nur Luecken:

| Befund | Produkte |
|---|---|
| kein Yoast-Titel | 304 |
| keine Meta-Description | 385 |
| Titel laenger als 60 Zeichen | 385 |
| Meta-Description laenger als 156 Zeichen | 131 |
| identischer Titel wie ein anderes Produkt | 63 |

Titel ueber 60 Zeichen schneidet Google ab, Meta-Descriptions ueber 156
ebenso. Identische Titel sind fuer die Suchmaschine nicht unterscheidbar.

Die 63 Dubletten waren aufschlussreich: es sind dieselben Sorten von
verschiedenen Zuechtern. "Northern Lights Auto Hanfsamen kaufen |
Hanfjack" stand gleichzeitig auf einem Seedsman-, einem Sensi-Seeds- und
einem Hanfjack-Produkt. Der Zuechtername steht im Produktnamen, war aber
aus dem Titel gefallen.

## Der Stil

Im Shop liefen zwei Titelstile nebeneinander, in jeder Kategorie
gemischt: 2109 mit dem Zusatz "... kaufen | Hanfjack" und 1718 rein
beschreibend. Entschieden wurde fuer **beschreibend mit Merkmal**:

```
Twister T6 Filter Bag 200 Mesh / 70 Mikron, weiss
Master Trimmer MT DRY 500 – 180 W
Sweedbar Zkittlez Auto 3er Packung – hoher THC-Gehalt
```

Die 60 Zeichen tragen damit Unterscheidungsmerkmale statt des Shopnamens,
der bei langen Produktnamen ohnehin als Erstes abgeschnitten wird.

**Bestehende Titel wurden nicht umgeschrieben, nur gekuerzt.** Bei den
385 zu langen faellt zuerst " | Hanfjack", dann " kaufen", dann
angehaengte Segmente hinter "|". Reicht das nicht, wird aus dem
Produktnamen neu gebaut. Ein Titel, der schon passte, blieb unberuehrt –
auch wenn er den alten Stil hat.

## Woher die Angaben kommen

Nichts ist erfunden. Jeder Baustein stammt aus einem gepflegten Attribut
oder aus dem Beschreibungstext des Produkts selbst. `yoast_bau.py`
uebersetzt Attributwerte in lesbares Deutsch:

| Attribut | Wert | Baustein |
|---|---|---|
| `pa_thc-gehalt` | `Hoch` | hoher THC-Gehalt |
| `pa_thc-gehalt` | `25 % und mehr` | ueber 25 % THC |
| `pa_form` | `Fluessig` | in fluessiger Form |
| `pa_anwendungsphase` | `Bluete` | fuer die Bluetephase |
| `pa_genetik` | `OG Kush und Thin Mint GSC` | aus OG Kush x Thin Mint GSC |

Der Satzbau ist bewusst eine Aufzaehlung nach Doppelpunkt
(`Name: Angabe, Angabe und Angabe.`). Das braucht kein Verb und kann
deshalb grammatisch nicht danebengehen – anders als ein Satzschema, in
das Attributwerte eingesetzt werden.

Reichen die Attribute nicht fuer 120 Zeichen, wird mit einem Satz aus der
Produktbeschreibung aufgefuellt, bevorzugt aus den ersten beiden
Absaetzen. Weiter hinten stehen Pflegetipps und Pflichtangaben; ein
Snippet mit "Nicht ueber den Hausmuell entsorgen." nuetzt niemandem,
deshalb sortiert `BOILERPLATE` solche Saetze aus.

## Ergebnis

| | vorher | nachher |
|---|---|---|
| ohne Titel | 304 | 0 |
| ohne Meta-Description | 385 | 4 |
| Titel ueber 60 Zeichen | 385 | 0 |
| Meta-Description ueber 156 | 131 | 0 |
| doppelte Titel | 63 | 5 |

923 Produkte geschrieben, kein Fehler.

## Was offen bleibt

**4 Produkte ohne Meta-Description**: Propagator Set S, M, L und XL
(15365, 15367, 15369, 15371). Sie haben weder Beschreibungstext noch
Attribute – die Beschreibung besteht aus einem YouTube-Einbett-Link. Aus
dem Produktnamen allein liesse sich nur ein 16-Zeichen-Snippet bauen, und
das ist schlechter als keines: Google schreibt dann lieber selbst eines
aus dem Seiteninhalt. Hier fehlt Text, nicht SEO.

**5 Produkte mit doppeltem Titel** – kein SEO-Problem, sondern
wahrscheinlich doppelt angelegte Produkte:

| IDs | Name |
|---|---|
| 14791, 14796, 14804 | Zippo Benzinfeuerzeug Cannabis Design |
| 22906, 22912 | G-Rollz Colossal Dream Medium Tray 17,5 × 27,5 cm |

Identischer Name, identische Attribute, identischer Text, verschiedene
SKUs. Kein Titel kann sie unterscheiden, weil sich nichts unterscheidet.
Das gehoert im Katalog geklaert.

**994 Meta-Descriptions unter 120 Zeichen** waren schon vorher da und
blieben unangetastet. Sie sind nicht falsch, verschenken aber Platz im
Suchergebnis.

## Zwei Fallen beim Schreiben

`<1 %` wird beim Speichern zu `&lt;1 %` und wird damit fuenf Zeichen
laenger als geplant – genug, um einen 57-Zeichen-Titel ueber die Grenze
zu heben. `_gehalt()` schreibt deshalb "unter 1 %" aus.

Ein Satzende ist schoener als ein Wortschnitt, aber nicht um jeden Preis:
`kappe()` opfert einen ganzen Satz nur dann, wenn danach noch 80 % der
Laenge stehen. Sonst wuerde ein einziges Zeichen zu viel 60 Zeichen Text
kosten.

## Dateien

- `hol_yoast.py` – holt den Ist-Stand aller Produkte (Yoast-Felder liegen
  im `meta_data` der WooCommerce-Schnittstelle)
- `yoast_bau.py` – baut Titel und Beschreibung aus Produktdaten
- `yoast_plan.py` – entscheidet je Produkt, was geschrieben wird, und
  loest Dubletten auf
- `schreib_yoast.py` – schreibt zurueck, Bloecke von 20, zwei Laeufe
  parallel (`alle 2 0` und `alle 2 1`)
