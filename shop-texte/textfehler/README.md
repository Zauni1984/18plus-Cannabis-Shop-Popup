# Textfehler in Produktbeschreibungen (17.09.2026)

## Anlass

Beim Anlegen des Smoking-Supreme-Einzelhefts fiel auf, dass die zugehoerige
24er-Box einen Kurztext hat, der mitten im Satz abbricht. Daraufhin wurde der
ganze Katalog auf dieselbe Fehlerklasse geprueft.

## Gesucht wurde nach

| Muster | gefunden |
|---|---:|
| Kurztext bricht ab (Komma am Ende, Adjektiv ohne Nomen, Bindewort am Ende) | 1 |
| Beschreibung besteht nur aus Stichpunkten, kein Fliesstext | 1 |
| Beschreibung vorhanden, Kurztext fehlt ganz | 46 |

Die ersten beiden sind behoben. Die 46 sind kein Fehler, sondern eine Luecke –
siehe unten.

## 18977 – Smoking Supreme King Size Slim 2in1 Box

**Vorher:** die gesamte Beschreibung war eine Stichpunktliste (403 Zeichen),
der Kurztext enthielt den Bruchstueck-Eintrag
`<p>Ultrafeine, transparente</p>` – der Satz endet nach dem Adjektiv.

**Nachher:** Fliesstext nach dem Muster der vier Schwesterboxen 18978 bis
18981 – zwei Absaetze, „Auf einen Blick", „Hinweise" (1143 Zeichen), dazu ein
vollstaendiger Kurztext.

**„transparent" wurde nicht uebernommen.** Die Verpackung sagt ultrafein
(ULTRAFINO-ULTRATHIN) und Ultra Smooth Touch; von Transparenz steht dort
nichts. Was sich nicht belegen laesst, kommt nicht in den Text – auch dann
nicht, wenn es im kaputten Original stand.

## 30312 – G-Rollz Pets Rock Reggae Medium Tray

**Vorher:** die vollstaendige Beschreibung lautete
`<ul><li>27,5x17,5cm</li></ul>` – 32 Zeichen, kein Kurztext, keine
SEO-Felder. Die neun Schwester-Trays haben alle einen ordentlichen Text.

**Nachher:** Text nach demselben Muster, dazu die fehlenden Attribute
(Format und Motiv), die uebliche Tag-Struktur der Reihe und Yoast-Felder.

Der Serien-Tag **„Pets Rock" (20721) fehlte** und wurde angelegt – jede
G-Rollz-Serie hat im Shop einen eigenen (Cheech & Chong, Banksy Graffiti,
Colossal Dream).

Der Hinweistext uebernimmt die Formulierung der Geschwister: zu Material und
weiteren Kennwerten veroeffentlicht G-Rollz nichts Belegbares, und geschaetzte
Werte werden nicht eingetragen.

## Was offen bleibt: 46 fehlende Kurzbeschreibungen

Keine Fehler, sondern nie geschrieben. Verteilung:

| Gruppe | Produkte |
|---|---:|
| Hanfjack-Merch (Shirts, Hoodies, Taschen) | 35 |
| PotKing Stofftoepfe und Toepfe | 9 |
| Messbecher, G-Rollz | 2 |

Der Kurztext steht auf der Produktseite ueber dem Umbruch – er fehlt dort
sichtbar. Die Texte liessen sich aus den vorhandenen Beschreibungen ableiten,
das ist aber ein eigener Lauf und keine Fehlerkorrektur.

## Dateien

- `fix18977.py`, `fix30312.py` – die beiden Korrekturen mit Begruendung im
  Kopfkommentar
