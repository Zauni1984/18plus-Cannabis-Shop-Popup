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
| Form | 455 | 93 % |
| Anwendungsphase | 370 | 75 % |
| Anwendungsart | 359 | 73 % |
| Substrat | 312 | 64 % |
| Nährstoffe | 267 | 54 % |
| Düngertyp | 182 | 37 % |
| NPK-Verhältnis | 110 | 22 % |
| Düngerart | 76 | 15 % |
| Löslichkeit | 15 | 3 % |
| Wirkdauer | 0 | 0 % |

## Offen

**NPK, Düngerart und Löslichkeit bleiben dünn**, weil die Shoptexte sie meist nicht nennen.
Sie stehen auf den Herstellerdatenblättern — Canna (71 Produkte), Hesi (58), Terra Aquatica (46),
Advanced Hydroponics (36), Athena (33), Atami (29), BioTabs (25), Mills (24). Das ist der
nächste Schritt.

**Wirkdauer steht bei 0 %.** Sofort- gegen Langzeitwirkung ist bei Flüssigdüngern für Hydro- und
Erdkultur praktisch nie ausgewiesen; die Angabe gehört eher zu Gartenbaudüngern. Geschätzt wird
dort nichts.

**Hygroskopizität** wurde bewusst nicht als Attribut angelegt: Kein Hersteller im Sortiment
veröffentlicht dazu Werte.
