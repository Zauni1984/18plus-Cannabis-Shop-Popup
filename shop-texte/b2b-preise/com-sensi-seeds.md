# Sensi Seeds – Rollenpreise (17.09.2026)

## Regel

Sensi Seeds kommt aus der Tiger-One-Mappe, Spalte F ist der EK.

| Rolle | Formel |
|---|---|
| B2B Kunde | EK × 1,10 |
| Anbauverein | EK × 1,30 |

Der Satz wurde nicht angenommen, sondern nachgerechnet: die bereits
bepreisten Sensi-Artikel tragen bei **101 von 101** Werten genau 1,10 und
1,30.

## Warum der erste Lauf die Marke verfehlt hat

Der Tiger-One-Lauf verglich die Spalte `sku` der Mappe mit der Shop-SKU. Bei
Sensi Seeds steht dort eine Hausnummer (`SEN1560022`), waehrend der Shop die
Struktur **`parent_sku` + Packungsgroesse** benutzt (`sensi-afghan-fem-10`).
Von 216 Sensi-Zeilen trafen so nur die wenigsten.

| Zuordnungsweg | Positionen |
|---|---:|
| `parent_sku` + Packungsgroesse | 77 |
| Sortenname + Typ + Groesse | 47 |
| **geschrieben** | **124** |

Alle 124 wurden zurueckgelesen, keine Abweichung.

## Der Typ muss mitgefuehrt werden

Beim Titelabgleich reicht der Sortenname nicht. „Hindu Kush" gibt es
feminisiert, regulaer und automatic – drei Artikel mit verschiedenen
Einkaufspreisen. Wer „feminised", „regular" und „auto" wegnormalisiert, um
Shop- und Mappennamen zur Deckung zu bringen, wirft sie zusammen.

Der Schluessel ist deshalb **Sorte + Typ + Groesse**, und zugeordnet wird nur,
wenn genau eine Mappen-Zeile passt. Mehrdeutige Faelle: keine.

Ein zweiter Stolperstein: der Namensfilter „sensi" fing auch
**Paradise Seeds Sensi Star** ein – eine andere Marke. Der Plan wurde
darauf geprueft; kein Fremdartikel ist darin gelandet.

Die Handelsspanne liegt im Median bei VK = 2,38 × EK.

## Was offen bleibt

18 Positionen (`com-sensi-offen.json`):

- **13 Sorten, die die Mappe nicht fuehrt**: Chemberries, Dosimosa, Girl
  Scout Cookies, Master Kush, Tezla OG, Vlad the Inhaler, Sauvignon Blanc
  Automatic und weitere. Diese Artikel stehen im Shop, aber nicht mehr im
  Tiger-One-Sortiment.
- **3 Artikel ohne Packungsgroesse** am Produkt, darunter Chocolate Rainbow
  XXL 3er Pack.
- **2 Treffer des Namensfilters**, die gar keine Sensi-Seeds-Ware sind
  (Paradise Seeds Sensi Star).

## Dateien

- `com-sensi-rollenpreise.json` – die 124 geschriebenen Preise mit EK,
  Mappen-SKU und Zuordnungsweg
- `com-sensi-offen.json`
- `sensi_plan.py` – Weg ueber `parent_sku` + Packung
- `sensi_titel.py` – Weg ueber Sorte, Typ und Groesse
- `hol_sensi.py`, `schreib_sensi.py`, `schreib_sensi2.py`
