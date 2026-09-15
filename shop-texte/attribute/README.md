# Produktattribute der Samen auf hanfjack.de

Stand: 15.09.2026 · **1604 veröffentlichte Samen** in 51 Marken (1890 inkl. Entwürfe und privater Artikel).

Ziel: 16 Attribute je Sorte, einheitlich befüllt und filterbar.

## Schema

`attr_schema.py` legt die Werteskalen fest, `vokabular.py` übersetzt die englischen
Herstellerangaben (Aroma, Geschmack, Effekte) nach Deutsch.

| Attribut | Werte |
| --- | --- |
| THC-Gehalt, CBD-Gehalt | feste Stufen: 0–1 %, 1–5 %, 5–10 %, 10–15 %, 15–20 %, 20–25 %, 25 % und mehr, Unbekannt |
| Sativa %, Indica %, Ruderalis % | Prozent in 5er-Schritten oder „Unbekannt" |
| Typ | Sativa-dominant, Indica-dominant, Hybrid (ausgewogen) |
| Variante | Feminisiert, Autoflowering, Regulär, CBD, CBG |
| Blütezeit (Tage) | Bereich in Tagen, z. B. „56-63" |
| Wuchshöhe | „80-140 cm (Indoor)", „175-210 cm (Outdoor)" |
| Ertrag | „600-650 g/m² (Indoor)", „650-700 g/Pflanze (Outdoor)" |
| Anbauumgebung | Indoor, Outdoor, Gewächshaus |
| Erntemonat | Monatsname, bei Autos „Ganzjährig (Auto)" |
| Klima | „mediterran, lange Sommer", „gemäßigt, kontinental", „kühl, kurze Sommer" |
| Schwierigkeitsgrad | Anfänger, Fortgeschrittene, Profis |
| Aroma, Geschmack, Effekte, Genetik, Terpene | Wortlisten |

Bei „bis zu X %" wird X als **Obergrenze** gewertet: „bis zu 20 %" ergibt die Stufe 15–20 %,
nicht 20–25 %.

**Terpene** werden nur gesetzt, wo der Hersteller sie ausdrücklich nennt. Aus dem Aroma
abgeleitet wird nichts.

## Neu angelegte Attribute

Klima, Wuchshöhe, Anbauumgebung, Erntemonat, Ertrag und Schwierigkeitsgrad gab es vorher nicht.
Aroma, THC, CBD, Sativa %, Indica %, Terpene, Variante, Blütezeit, Effekte und Genetik waren
bereits vorhanden, aber lückenhaft und uneinheitlich befüllt.

## Fortschritt

**945 von 1807 Samen** bearbeitet, zehn Markenläufe, alle ohne Fehler.
Dazu 772 Produkte aus den eigenen Produktbeschreibungen und 293 aus einem Händlerkatalog.

| Marke | Produkte | Quelle | Umfang |
| --- | --- | --- | --- |
| Royal Queen Seeds | 166 | royalqueenseeds.de | 14 Attribute je Sorte |
| Dutch Passion | 103 | dutch-passion.com | Typ, Blütezeit, THC, CBD, Schwierigkeitsgrad |
| Barneys Farm | 90 | barneysfarm.com | 14 Attribute je Sorte |
| Sweet Seeds | 71 | sweetseeds.com | THC, CBD, Genanteile, Blütezeit, Ertrag, Höhe, Effekte, Aroma |
| Sensi Seeds | 63 | sensiseeds.com | Genanteile, Typ, Klima, Schwierigkeitsgrad |
| Pyramid Seeds | 60 | pyramidseeds.com | 12 Attribute inkl. Terpene und Schwierigkeitsgrad |
| Nirvana Seeds | 56 | Herstellerangaben aus der Textrecherche | Blütezeit, Genetik, Ertrag, Höhe, Erntemonat |
| Anesia Seeds | 43 | anesiaseeds.com | THC, Genanteile, Blütezeit, Höhe, Ertrag, Erntemonat, Aroma |
| Paradise Seeds | 38 | paradise-seeds.com | THC, Genanteile, Blütezeit, Höhe, Ertrag, Effekte, Aroma |
| **Variante, alle Marken** | **1126** | Produktname | Variante |
| **Alle Marken** | **772** | „Auf einen Blick" aus der eigenen Produktbeschreibung | Blütezeit, Samentyp, Genanteile, THC, CBD, Genetik, Ertrag, Wuchshöhe |
| **21 Marken** | **293** | house-of-seeds.de (Händlerkatalog), 1. Durchgang | Aroma, Effekte, Terpene, Genetik |
| **Alle Marken** | **446** | house-of-seeds.de, 2. Durchgang über den Gesamtbestand | dieselben vier Felder, nur wo noch leer |
| **Alle Marken** | **1361** | Beschreibungen mit abweichenden Labeln | Blütezeit, Variante, Typ, Genanteile, THC, CBD, Genetik, Ertrag, Wuchshöhe |
| **Alle Marken** | **471** | Sortentyp autoflowering | Erntemonat „Ganzjährig (Auto)" |

### Abdeckung im Katalog

1604 veröffentlichte Samen, Stand nach allen Läufen:

| Attribut | befüllt | Abdeckung |
| --- | ---: | ---: |
| Variante | 1603 | 99 % |
| Genetik | 1520 | 94 % |
| THC-Gehalt | 1203 | 75 % |
| Aroma | 1196 | 74 % |
| Effekte | 1102 | 68 % |
| Blütezeit | 1005 | 62 % |
| Typ | 909 | 56 % |
| Terpene | 887 | 55 % |
| Indica % | 667 | 41 % |
| Sativa % | 660 | 41 % |
| Erntemonat | 550 | 34 % |
| CBD-Gehalt | 344 | 21 % |
| Wuchshöhe | 340 | 21 % |
| Anbauumgebung | 321 | 20 % |
| Ertrag | 285 | 17 % |
| Geschmack | 174 | 10 % |
| Schwierigkeitsgrad | 132 | 8 % |
| Klima | 58 | 3 % |
| Ruderalis % | 0 | 0 % |

Zum Vergleich der Ausgangsstand vor dieser Runde: Typ 0, Anbauumgebung 0, Ertrag 0,
Wuchshöhe 0, Schwierigkeitsgrad 0, Erntemonat 0, Klima 0 — diese sieben Attribute gab es
vorher gar nicht. Terpene standen bei 40 %, Effekte bei 54 %, Blütezeit bei 51 %.

**Klima (3 %) und Schwierigkeitsgrad (8 %) bleiben dünn, und das bleibt auch so.** Beide
Angaben veröffentlichen nur wenige Hersteller (Royal Queen Seeds, Sensi, Pyramid). Weder der
Händlerkatalog noch die Produktbeschreibungen enthalten sie. Sie zu schätzen hieße, Werte zu
erfinden — deshalb bleiben die Felder leer.

**Ruderalis % steht bei 0 %**, weil die Genanteile in den Quellen durchgängig als Sativa/Indica
angegeben werden, auch bei Autos. Der Ruderalis-Anteil wird nirgends beziffert.

Die Zuwächse bei Aroma, Genetik und THC fallen kleiner aus als die Zahl der bearbeiteten
Produkte, weil diese Felder vielfach schon belegt waren. Dort liegt der Gewinn in der
Vereinheitlichung der Werte, nicht in der Zahl.

## Quellen

Erste Wahl sind immer die Datenblätter des Herstellers. Wo die fehlen oder gesperrt sind,
kommen zwei weitere Quellen zum Einsatz:

**Eigene Produktbeschreibungen.** 772 Samen tragen im Beschreibungstext einen Block
„Auf einen Blick" mit Blütezeit, Samentyp, Genanteilen, THC, CBD, Kreuzung, Ertrag und
Wuchshöhe. `desc_attr.py` liest ihn aus. Das deckt genau die Marken ab, deren Herstellerseiten
blockieren (Fast Buds, Ethos, Seedsman, Wizard Trees, Teile von Sensi).

**house-of-seeds.de.** Ein Händlerkatalog, kein Herstellerdatenblatt. Über die offene
Shopify-Schnittstelle `/products.json` stehen 1427 Produkte mit den Feldern `Geschmack`,
`Wirkung`, `3 Hauptterpene` und `Breeder & Kreuzung` bereit. `hs_attr.py` liest sie aus,
`hs_match.py` ordnet sie über Marke und normalisierten Sortennamen zu.

Weil es sich um Händlerdaten handelt, gilt dort eine Einschränkung: **die Werte füllen nur
leere Felder.** Was aus einem Herstellerdatenblatt oder aus der eigenen Produktbeschreibung
stammt, wird nie überschrieben.

Alle neuen Werte werden vor dem Schreiben gegen die vorhandenen Taxonomie-Terme abgeglichen
(`shop_terms.json`, Vergleich ohne Groß-/Kleinschreibung). So entsteht kein zweiter Term
„Erdig" neben dem bestehenden „erdig".

## Offene Punkte

- **Fast Buds (111 Produkte)**: 2fast4buds.com und die Produktseiten von fastbuds.com sind
  hinter Cloudflare, die Datenblätter sind nicht abrufbar. Abgedeckt über die eigenen
  Beschreibungen und den Händlerkatalog.
- **78 Samen ohne jede belastbare Quelle**: Bei ihnen steht im Beschreibungstext ausdrücklich,
  dass der Züchter keine Daten veröffentlicht (Beispiel Solfire Gardens Rubicite: „Zu Rubicite
  ist bislang kein Beschreibungstext des Züchters hinterlegt"). Dort bleiben die Felder leer.
- **Sensi Seeds**: veröffentlicht Ertrag, Höhe und Blütezeit nur qualitativ („Üppiger Ertrag",
  „Durchschnittliche Blütezeit"). Diese Felder bleiben dort leer. 53 der 116 Sorten stehen nicht
  in der Sitemap (reguläre Samen und Autos werden per JavaScript nachgeladen).
- **Barneys Farm**: 33 der 120 Sorten führt der Hersteller nicht mehr (Gelato, Cookies Kush,
  GMO, Zkittlez-Linie, Mendo Breath und weitere). Für sie gibt es keine Datenblätter mehr.
- **Taxonomie-Dubletten**: Über alle 19 Attribute liegen **135 Dublettengruppen** vor,
  `dedup_scan.py` findet sie, `dedup_plan.py` legt je Gruppe den Kanon-Term fest.
  Die größten Blöcke:

  | Attribut | Gruppen | betroffene Produktzuordnungen | Ursache |
  | --- | --- | --- | --- |
  | Sativa % / Indica % | 39 | 342 | „50 %" neben „50%" |
  | Genetik | 56 | 36 | Klammerreste aus einem früheren Kreuzungs-Split („OG Kush)", „(Wedding Cake") |
  | Aroma / Geschmack | 18 | 25 | Groß-/Kleinschreibung, Singular/Plural |
  | THC / CBD | 16 | 7 | Bindestrich statt Halbgeviertstrich, fehlendes Leerzeichen vor „%" |
  | Ertrag | 3 | 16 | „400-450" neben „400 - 450" |
  | Terpene | 1 | 5 | „β-Caryophyllen" neben „Caryophyllen" |

  Zwei Gruppen bleiben bewusst ungetrennt stehen, weil es echte Unterschiede sind und keine
  Schreibweisen: **Critical + ≠ Critical** und **Criminal+ ≠ Criminal**. Ebenso bleibt bei der
  Blütezeit „70+" neben „70" bestehen — das Plus ist eine Angabe, keine Tippvariante.

  **Erledigt:** `dedup_apply.py` hat **721 Produktzuordnungen** auf den jeweiligen Kanon-Term
  umgehängt, `leere_terme.py` anschließend **149 leer gewordene Terme gelöscht**. Gelöscht wird
  nur, was der Shop selbst mit Zähler 0 meldet — jeder Term wird einzeln abgefragt und vorher
  in `leere_terme_backup.json` gesichert (Name, Slug, Beschreibung, Ziel-Term).

  Von 137 Dublettengruppen sind **4 übrig, und die bleiben**, weil es echte Wertunterschiede
  sind und keine Schreibweisen:

  | Attribut | bleibt getrennt | Grund |
  | --- | --- | --- |
  | Genetik | Critical + ≠ Critical | eigenständige Sorte |
  | Genetik | Critical + Auto ≠ Critical Auto | eigenständige Sorte |
  | Genetik | Criminal+ ≠ Criminal | eigenständige Sorte |
  | Blütezeit | 70+ ≠ 70 | das Plus ist eine Angabe |

  `dedup_plan.py` erkennt das an einer eigenen Regel: Sobald sich zwei Namen nur durch ein
  „+" unterscheiden, wird die Gruppe übersprungen — unabhängig davon, wo im Namen das Plus
  steht.

  Beim Schreiben selbst sind über alle vier Wortattribute nur **vier neue Terme** entstanden —
  „Kuchen", „Harz", „Säuerlich", „nostalgisch". Keine einzige Schreibweisen-Dublette, weil jeder
  Wert vor dem Schreiben gegen die bestehende Taxonomie abgeglichen wird.

- **Humboldt Seed Company**: humboldtseedcompany.com veröffentlicht nur 53 Sortenseiten mit
  Datenblock, davon passen 5 auf den Shop-Bestand. Der restliche Katalog liegt auf den
  Länderseiten (.es) und auf californiahempseeds.com.
