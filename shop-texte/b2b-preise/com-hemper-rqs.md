# Rollenpreise hanfjack.com – HEMPER und Royal Queen Seeds (17.09.2026)

## Aufschlaege

Je Lieferant gilt ein eigener Anbauverein-Satz. Der B2B-Satz ist ueberall
gleich.

| Lieferant | B2B Kunde | Anbauverein |
|---|---|---|
| Fast Buds | EK × 1,10 | EK × 1,40 |
| Tiger One | EK × 1,10 | EK × 1,30 |
| **HEMPER** | EK × 1,10 | **EK × 1,35** |
| **Royal Queen Seeds** | EK × 1,10 | **EK × 1,30** |

Kaufmaennisch auf zwei Nachkommastellen, netto. Meta-Keys
`_wwpro_price_b2b_customer` und `_wwpro_price_anbauverein`.

---

## HEMPER und Nebenmarken

**Quelle:** Google-Mappe mit elf Reitern, rund 525 Zeilen.

### Die Falle: eine Zeile, drei Preise

Jede Sheet-Zeile nennt bis zu drei Wholesale-Preise – pro Stueck, pro Display
und pro Karton. Der Shop verkauft **beides unter aehnlichen SKUs**: die Box
als `HMP-FT-GLASS-10MM-DISPLAY`, das Einzelstueck als
`HMP-FT-GLASS-10MM-EINZEL`.

Wer nur die SKU vergleicht, schreibt den Stueckpreis auf die Box:

| | EK laut Sheet | richtig? |
|---|---|---|
| Stueckpreis | 1,50 € | nein – das ist ein Glasfilter |
| Displaypreis | 15,00 € | ja – die Box kostet im Shop 25,13 € |

Bei 1,50 € EK stuende auf einer 25-Euro-Box ein B2B-Preis von 1,65 €.

### Warum die Spaltennamen nicht reichen

In den vier grossen Reitern stehen Unit und Display in H und I. Vier weitere
Reiter haben nur **eine** Wholesale-Spalte, und die bedeutet nicht ueberall
dasselbe: in `1601411394` ist es der Stueckpreis, in `956249339` der
Displaypreis. Die Zuordnung laeuft deshalb ueber Arithmetik – Kartonpreis
geteilt durch Displays je Karton – und faellt nur dort auf die SKU zurueck,
wo kein Kartonpreis steht.

Gegenprobe an einem Fall, den die Mappe doppelt fuehrt: `DISPLAY-HMP-WICK`
nennt nur 50,00 €, `DISPLAY-HT-HEMPWICK` dieselbe Ware mit 1,00 € je Stueck
und 50,00 € je Display. Die aus 50,00 € / 50 Stueck hergeleiteten 1,00 €
treffen den ausgewiesenen Wert exakt.

### Ergebnis

| | Positionen |
|---|---:|
| im Sheet zugeordnet | 300 |
| davon Displaypreis | 125 |
| davon Stueckpreis | 175 |
| **geschrieben** | **296** |
| zurueckgestellt | 4 |
| Fehler | 0 |

50 einfache Produkte und 246 Variationen. Jede Position wurde
zurueckgelesen: `own_price` stimmt, `price == own_price`, `source` steht auf
`product` bzw. `variation`.

**Zurueckgestellt** (`com-hemper-offen.json`): die vier Geruchsneutralisierer.
Bei 30,00 € EK und 40,24 € VK ergaebe der Anbauvereinpreis 40,50 € – mehr als
der Laden-VK. Das Plugin wuerde auf 40,24 € deckeln, der Anbauverein zahlte
also den vollen Ladenpreis. Entweder steigt der VK oder der Satz passt fuer
diese Ware nicht.

---

## Royal Queen Seeds

**Quelle:** `RQS_Pricelist_2026_-_Seeds_Wholesale.xlsx`, Blatt „Seeds WS".
619 SKUs mit EK. Gegenprobe: der ausgewiesene Verbraucherpreis ist bei
**allen** 619 exakt das Doppelte des EK, und alle 597 Werte, die sich mit der
im Repo abgelegten Liste ueberschneiden, stimmen zeichengenau.

Das Blatt „Bulk WS" fuehrt 151 Mengenstaffel-SKUs; keine davon existiert im
Shop.

### Die RQS-Nummern haengen nicht am Produkt

Im Shop tragen die RQS-Artikel `HJ-`-Nummern. Die Zuordnung Produkt → Sorte
stammt aus `rqs-plan.json`, die Packungsgroesse aus dem Attribut
„Menge waehlen".

### Zwei EK-Quellen, getrennt gefuehrt

Von 166 offenen Positionen liessen sich nur **3** direkt aus der Liste
bepreisen. Der Grund ist kein Datenfehler: **RQS fuehrt die uebrigen
Packungsgroessen nicht mehr.** „Alien OG" gibt es nur noch als 3er, „Biscotti"
bis 10er – der Shop hat aber 5er, 10er und 25er im Sortiment.

Fuer diese 153 Positionen wurde der EK aus dem Laden-VK zurueckgerechnet. Die
Grundlage ist die im Katalog durchgaengig verwendete Hausregel
**brutto = 2 × EK, netto = brutto / 1,07**. Sie trifft 431 der 461 Positionen
mit bekanntem EK; die 30 Ausnahmen sind F1-Hybride, die der Shop bewusst
guenstiger anbietet.

Drei Belege, dass die Rueckrechnung den echten EK trifft:

1. **153 von 154** Ergebnissen liegen auf dem Viertel-Euro genau.
2. Die Preiskurve pro Samen faellt wie in der Liste: Alien OG ergibt 4,50 /
   4,00 / 3,75 € je Samen fuer 3er / 5er / 10er – dieselbe Form wie Biscotti
   mit echten Listenpreisen (5,08 / 4,65 / 4,25 €).
3. Wo Liste und Rueckrechnung sich ueberschneiden, stimmen sie ueberein.

Der eine krumme Fall (Royal Cheese Fast, 10er, ergaebe 27,55 €) bleibt liegen.

**Die 153 hergeleiteten Positionen sind in
`com-rqs-rollenpreise-2026.json` mit `"herkunft": "herleitung"` markiert** und
lassen sich damit jederzeit gezielt zuruecknehmen.

### Ergebnis

| | Positionen |
|---|---:|
| bereits bepreist | 460 |
| aus der Preisliste geschrieben | 3 |
| aus dem VK hergeleitet geschrieben | 153 |
| **geschrieben** | **156** |
| zurueckgestellt | 10 |
| Fehler | 0 |

Alle 156 wurden zurueckgelesen, keine Abweichung.

**Zurueckgestellt** (`com-rqs-offen.json`): neun Variationen ohne
Mengen-Attribut – acht davon am Orion F1-Hybrid, die alle dieselbe SKU
`HJ-5628761` tragen und sich dadurch nicht unterscheiden lassen; dazu
Medusa `RO-0526-01`. Das ist ein kaputter Variationsaufbau, kein Preisproblem.

---

## Dateien

- `com-hemper-rollenpreise.json`, `com-hemper-offen.json`
- `com-rqs-rollenpreise-2026.json`, `com-rqs-offen.json`
- `xlsx.py` – minimaler xlsx-Leser (openpyxl fehlt in der Umgebung)
- `hemper_map.py` – Spalten- und Gebinde-Erkennung, der heikle Teil
- `hemper_plan.py`, `schreib_hemper.py`
- `rqs_ek.py`, `rqs_plan.py`, `schreib_rqs.py`, `pruef_rqs.py`
