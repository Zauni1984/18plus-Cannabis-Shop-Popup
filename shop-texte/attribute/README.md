# Produktattribute der Samen auf hanfjack.de

Stand: 15.09.2026 · **1807 Samen** (ohne Entwürfe) in 51 Marken.

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

| Marke | Produkte | Quelle | Umfang |
| --- | --- | --- | --- |
| Royal Queen Seeds | 166 | royalqueenseeds.de, Datenblatt je Sorte | 14 Attribute |
| Dutch Passion | 103 | dutch-passion.com | Typ, Blütezeit, THC, CBD, Schwierigkeitsgrad |
| Barneys Farm | 90 | barneysfarm.com, Datenblatt je Sorte | 14 Attribute |
| Sensi Seeds | 63 | sensiseeds.com | Sativa/Indica, Typ, Klima, Schwierigkeitsgrad |
| Nirvana Seeds | 56 | Herstellerangaben aus der Textrecherche | Blütezeit, Genetik, Ertrag, Höhe, Erntemonat |
| **Variante, alle Marken** | **1126** | Produktname | Variante |

Alle Läufe ohne Fehler.

## Offene Punkte

- **Fast Buds (111 Produkte)**: 2fast4buds.com und die Produktseiten von fastbuds.com sind
  hinter Cloudflare, die Datenblätter sind nicht abrufbar.
- **Sensi Seeds**: veröffentlicht Ertrag, Höhe und Blütezeit nur qualitativ („Üppiger Ertrag",
  „Durchschnittliche Blütezeit"). Diese Felder bleiben dort leer. 53 der 116 Sorten stehen nicht
  in der Sitemap (reguläre Samen und Autos werden per JavaScript nachgeladen).
- **Barneys Farm**: 33 der 120 Sorten führt der Hersteller nicht mehr (Gelato, Cookies Kush,
  GMO, Zkittlez-Linie, Mendo Breath und weitere). Für sie gibt es keine Datenblätter mehr.
- **Taxonomie-Dubletten**: In den Attributen liegen Terms doppelt mit unterschiedlicher
  Schreibweise („erdig" neben „Erdig"). WooCommerce greift dann auf den bestehenden Term zu.
  Wird zum Schluss über alle Attribute in einem Durchgang bereinigt.
