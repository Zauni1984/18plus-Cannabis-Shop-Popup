# Rollenpreise hanfjack.com – Lauf vom 16.09.2026

## Regel

Quelle ist die Tiger-One-Preisliste, **Spalte F (`price`) = EK netto**.

| Rolle | Meta-Key | Formel |
|---|---|---|
| B2B Kunde | `_wwpro_price_b2b_customer` | EK × 1,10 |
| Anbauverein | `_wwpro_price_anbauverein` | EK × 1,30 |

Kaufmaennisch auf zwei Nachkommastellen. Alle Werte netto, in derselben Basis
wie `regular_price` der Variationen (`tax_class: reduced-rate`).

**Achtung, es gibt zwei Regeln im Haus.** Fast Buds rechnet den Anbauverein mit
**1,40** (siehe `README.md`), Tiger One mit **1,30**. Dieser Lauf betrifft
ausschliesslich Tiger-One-Ware; die Fast-Buds-Variationen tragen `HJ-`-SKUs,
stehen nicht in der Tiger-One-Liste und wurden deshalb nicht angefasst.

## Ergebnis

| | Variationen |
|---|---:|
| auf hanfjack.com insgesamt | 4269 |
| mit EK in der Tiger-One-Liste | 1333 |
| davon bereits korrekt bepreist | 201 |
| **in diesem Lauf geschrieben** | **1132** |
| Fehler | 0 |

480 Elternprodukte betroffen, beginnend bei Silent Seeds `SIL-ACJ-FEM`.

### Nach Marke

| Marke | Variationen |
|---|---:|
| Seedsman | 344 |
| Sweet Seeds | 183 |
| Pyramid Seeds | 142 |
| Silent Seeds | 96 |
| Sensi Seeds | 93 |
| Amsterdam Genetics | 85 |
| Buddha Seeds | 73 |
| Ripper Seeds | 49 |
| Serious Seeds | 35 |
| TerpyZ Mutant Genetics | 19 |
| Sensi Seeds Research | 8 |
| Purple City Genetics | 5 |
| **Summe** | **1132** |

### Kontrolle

Jede der 1132 Variationen wurde nach dem Schreiben einzeln zurueckgelesen:
`own_price` stimmt mit dem berechneten Wert ueberein, `source` steht auf
`variation`, und `price == own_price` – Woo Wholesale Pro deckelt also nirgends.
Vor dem Schreiben wurde zusaetzlich geprueft, dass kein Anbauvereinpreis ueber
dem reguaeren VK liegt; das traf auf keine Variation zu.

Verkaufspreise (B2C), `min_age` und alle uebrigen Felder blieben unberuehrt –
geschickt wurde ausschliesslich `meta_data` mit den beiden Schluesseln.

## Zwei Fallen

**Der Filter ueber die Eltern-SKU greift zu kurz.** Der erste Anlauf suchte auf
.com nach Produkten, deren *Eltern*-SKU in der Liste steht. Das fand 503
Produkte – aber keine der 201 bereits bepreisten Variationen. Grund: bei
diesen haengt die Tiger-One-SKU an der *Variation*, waehrend das Elternprodukt
seine `HJ-`-SKU behalten hat. Richtig ist, die Variationen aller 1663 variablen
Produkte zu lesen und ueber die Variations-SKU zu matchen.

**Der Shop speichert `49.5`, gerechnet wird `49.50`.** Ein Textvergleich
meldete deshalb 140 vermeintliche Korrekturen, die in Wirklichkeit denselben
Preis trugen – nachgerechnet steckte in allen 140 bereits Faktor 1,10 bzw.
1,30. Der Vergleich laeuft jetzt ueber `Decimal`, sonst waeren 140 sinnlose
Schreibvorgaenge gelaufen.

## Was offen bleibt

1802 Variationen auf .com haben weiterhin keine Rollenpreise. Die vollstaendige
Liste steht in `com-offene-rollenpreise.json`:

| Gruppe | Variationen |
|---|---:|
| anderer Lieferant (keine Tiger-One-Ware) | 1631 |
| Tiger-One-SKU, steht nicht mehr in der Liste | 171 |

Die 171 sind vermutlich ausgelaufene Artikel – Marken wie Lovin' In Her Eyes,
James Loud Genetics und Ethos, deren uebrige Groessen bepreist sind. Ohne EK
laesst sich dafuer nichts rechnen; entweder liefert der Lieferant die Preise
nach, oder die Artikel gehoeren aus dem Shop.

Bei den 1631 anderen (HEMPER, Plagron, Atami, Royal Queen, PURIZE, Hanfjack-
Merch und weitere) fehlt jede EK-Quelle in diesem Repo.

## Dateien

- `com-rollenpreise-2026-09-16.json` – die 1132 geschriebenen Preise mit EK,
  Variations-ID und VK
- `com-offene-rollenpreise.json` – die 1802 Variationen ohne Rollenpreise
- `preis_plan.py` – rechnet die Preise und vergleicht mit dem Ist-Stand
- `schreib_preise.py` – schreibt, Batch je Elternprodukt, zwei Laeufe parallel
- `pruef_preise.py` – liest jede geschriebene Variation zurueck
- `hol_var.py`, `com_api.py` – Abzug der Variationen von hanfjack.com

Die Zugangsdaten fuer hanfjack.com liegen im Scratchpad der Sitzung
(`.com_creds`, `chmod 600`) und gehoeren nicht ins Repo. Benutzername ist die
E-Mail-Adresse, nicht der Anzeigename – mit `Hanfjack` antwortet die
Schnittstelle mit 401.
