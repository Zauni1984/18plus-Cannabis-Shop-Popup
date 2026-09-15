# Düngerattribute auf hanfjack.de

Stand: 15.09.2026 · **487 veröffentlichte Dünger** in der Kategorie „Dünger" (Term 1132).

Ausgangslage: Von den 487 Produkten trugen **77 eine Angabe zum Inhalt, sonst nichts**.
Zehn der elf Attribute gab es im Shop überhaupt noch nicht.

## Schema

`duenger_schema.py` legt die Werteskalen fest. Zehn Attribute sind neu angelegt:

| Attribut | ID | Werte |
| --- | --- | --- |
| NPK-Verhältnis | 45 | `3-1-4`, Nachkommastellen mit Komma |
| Nährstoffe | 46 | Stickstoff (N) … Vitamine, 21 Werte |
| Düngertyp | 47 | Einnährstoffdünger, Mehrnährstoffdünger (Volldünger) |
| Düngerart | 48 | Mineralisch, Organisch, Organo-mineralisch, Pflanzenhilfsmittel |
| Wirkdauer | 49 | Sofortwirkung, Langzeitwirkung, Sofort- und Langzeitwirkung |
| Form | 50 | Flüssig, Pulver, Granulat, Tabletten, Stäbchen, Paste |
| Löslichkeit | 51 | vollständig / teilweise / nicht wasserlöslich |
| Anwendungsphase | 52 | Keimung und Stecklinge, Wachstum, Blüte, Wachstum und Blüte, Spülphase, Ganze Kultur |
| Substrat | 53 | Erde, Coco, Hydrokultur, Steinwolle, Alle Substrate |
| Anwendungsart | 54 | Gießen (Wurzel), Blattdüngung, Gießen und Blattdüngung, In das Substrat einarbeiten |

Der vorhandene `Inhalt` (ID 2) wird weiter genutzt.

## Herleitung

**Packungsgröße** kommt aus dem Produktnamen (`d_inhalt.py`). 406 der 487 Namen tragen sie
direkt, etwa „VitaLink Turbo+, 250 ml". Die übrigen 81 sind Variantenprodukte — dort steckt die
Größe in den Variationen und wird nicht angefasst.

**Alles Weitere** kommt aus Produktname und Beschreibungstext (`d_attr.py`). 484 der 487
Produkte haben einen Block „Auf einen Blick" mit den Eckdaten.

Zwei Schutzmechanismen, ohne die der Parser Unsinn schreibt:

- **Verneinungsschutz.** Vor jedem Treffer wird geprüft, ob davor „nicht", „kein", „ohne",
  „statt" oder „frei von" steht. „Nicht mit Enzymes+ kombinieren" zählt damit nicht als
  Enzymgehalt.
- **Enthaltensaussage bei Nährstoffen.** Das bloße Vorkommen eines Nährstoffnamens reicht nicht.
  „Um den Stickstoff zu senken" ist keine Inhaltsangabe. Nur wenn der Satz den Nährstoff als
  Bestandteil ausweist — „enthält", „mit", „versorgt mit", „Gehalt", „reich an", ein
  Prozentwert oder eine Aufzählung — wird er übernommen. Das hat die Trefferzahl von 367 auf
  267 gesenkt und dabei die Falschtreffer entfernt.

**Der Produktname schlägt den Fließtext.** Ein „Canna CoGr Flores" ist ein Blütedünger, auch
wenn der Text irgendwo die Wachstumsphase erwähnt. Erst wenn der Name nichts hergibt, entscheidet
der Text.

**Düngertyp folgt aus dem NPK**: Ist genau ein Hauptnährstoff größer null, ist es ein
Einnährstoffdünger, bei zweien oder dreien ein Volldünger.

## Abdeckung

| Attribut | befüllt | Abdeckung |
| --- | ---: | ---: |
| Inhalt | 466 | 95 % |
| Form | 456 | 93 % |
| Anwendungsphase | 370 | 75 % |
| Anwendungsart | 359 | 73 % |
| Substrat | 312 | 64 % |
| Nährstoffe | 267 | 54 % |
| Düngertyp | 182 | 37 % |
| NPK-Verhältnis | 117 | 24 % |
| Düngerart | 83 | 17 % |
| Löslichkeit | 15 | 3 % |
| Wirkdauer | 0 | 0 % |

## Herstellerdatenblätter

Für NPK, Düngerart und Löslichkeit wurden die Herstellerseiten ausgewertet (`d_web.py`,
`d_web_map.py`). Ergebnis nach Marke:

| Hersteller | Produkte im Shop | veröffentlicht NPK? |
| --- | ---: | --- |
| Canna | 71 | **nein** — weder auf den Produkt- noch auf den Linienseiten |
| Hesi | 58 | ja, als „NPK 4-2-4" direkt auf der Produktseite |
| Terra Aquatica (GHE) | 46 | nicht öffentlich auffindbar |
| Advanced Hydroponics | 36 | nein |
| Athena | 33 | nicht öffentlich auffindbar |
| Atami | 29 | nur als Rechenhinweis, ohne Werte |
| BioTabs | 25 | nein |
| Mills Nutrients | 24 | nicht öffentlich auffindbar |
| Plagron | 19 | ja, als „NPK-Dünger (2-2-4)" plus PDF-Datenblatt |

Aus Hesi und Plagron kamen 38 Zuordnungen, davon 14 mit neuen Werten — der Rest war aus den
Shoptexten schon belegt.

`pdftext.py` ist dabei entstanden: Die Umgebung hat kein `pdftotext`, und `pypdf` scheitert an
einer kaputten cryptography-Bindung. Das Skript packt die FlateDecode-Streams selbst aus und
sammelt die Text-Operatoren ein. Damit ist das Plagron-Datenblatt vollständig lesbar — Gesamt-
stickstoff, Nitrat- und Ammoniumanteil, P₂O₅, K₂O, MgO, SO₃ und sechs Spurenelemente mit
Prozentwerten, jeweils mit dem Vermerk „water soluble".

## Offen

**NPK bleibt bei 24 %, und das liegt an den Herstellern, nicht am Verfahren.** 369 der 487
Produkte sind potenzielle Basisdünger, die eine NPK-Deklaration tragen könnten. Der größte
Anbieter im Sortiment — Canna mit 71 Produkten — veröffentlicht sie im Netz überhaupt nicht.
Die übrigen großen Marken ebenso wenig. Die Werte stehen auf den Etiketten und in PDF-Daten-
blättern, die nicht frei verlinkt sind. Wer sie vollständig haben will, kommt um Etikettenfotos
oder einen Datenblattzugang beim Hersteller nicht herum. Geschätzt wird nichts.

**Löslichkeit (3 %) und Wirkdauer (0 %)** aus demselben Grund. Sofort- gegen Langzeitwirkung ist
bei Flüssigdüngern für Hydro- und Erdkultur praktisch nie ausgewiesen; die Angabe gehört eher zu
Gartenbaudüngern.

**Hygroskopizität** wurde bewusst nicht als Attribut angelegt: Kein Hersteller im Sortiment
veröffentlicht dazu Werte.

**Growbedarf** (1344 Produkte) steht noch aus — eigener Durchgang, wie besprochen.
