# B2B- und Anbauverein-Preise (hanfjack.com)

Stand: 08.09.2026 &ndash; Fast Buds Autoflower

## Regel

Quelle sind die EK-Preise (netto) aus Spalte H des Preis-Sheets.

| Rolle | Meta-Key | Aufschlag |
| --- | --- | --- |
| B2B Kunde | `_wwpro_price_b2b_customer` | EK + 10&nbsp;% |
| Anbauverein | `_wwpro_price_anbauverein` | EK + 40&nbsp;% |

Gerundet kaufmännisch auf zwei Nachkommastellen. Die Werte sind netto und liegen
damit in derselben Basis wie `regular_price` der Variationen (der Shop speichert
netto, Anzeige brutto mit 7&nbsp;% ermäßigtem Satz).

Die Werte gelten vorerst ausschließlich für Fast Buds.

## Umfang

- 63 variable Produkte (HJ-7000001 &ndash; HJ-7000063), Autoflower
- 435 Variationen geschrieben, alle mit `source: "variation"` bestätigt
- Feminisierte Linie (HJ-7000064 &ndash; HJ-7000079) folgt später

## Schreibweg

Das Plugin *Woo Wholesale Pro* stellt keine eigenen Abilities auf dem Connector
bereit; nur der Meta-Weg funktioniert. Variationen sind über die generischen
Meta-Tools nicht erreichbar (`product_variation` hat keine wp/v2-REST-Base),
deshalb:

```
wp_wc_batch_update_variations(
  product_id = <Eltern-ID>,
  update = [{ id: <Variations-ID>,
              meta_data: [{key: "_wwpro_price_b2b_customer", value: "13.20"},
                          {key: "_wwpro_price_anbauverein",  value: "16.80"}] }, ...])
```

Kontrolle über das schreibgeschützte Feld `wwpro_wholesale_prices` in der
Antwort: `source` muss auf `variation` stehen.

## Variations-IDs

`wp_wc_list_product_variations` liefert pro Produkt sehr große Antworten. Die
IDs sind stattdessen aus dem Anlagemuster abgeleitet: Eltern, dann 0&ndash;2
Bilder, dann die Variationen in aufsteigender Packungsgröße; die letzte
Variation liegt direkt vor der nächsten Eltern-ID. Abgeleitete und tatsächliche
IDs wurden für alle 435 Variationen über die zurückgemeldeten SKUs geprüft
&ndash; keine Abweichung.

## Offen

Drei Strains haben im Sheet einen unplausiblen EK für die 10er-Packung
(9,50&nbsp;€, während die 5er 27,50&nbsp;€ kostet; vergleichbare Sorten liegen
bei 49,50&nbsp;€). Diese drei Variationen wurden **nicht** geschrieben:

- HJ-7000021-10 &ndash; Gelato Auto
- HJ-7000024-10 &ndash; Gorilla Z Auto
- HJ-7000063-10 &ndash; Ztrawberriez Auto

## Dateien

- `fastbuds-autos-b2b-plan.json` &ndash; berechnete Preise je HJ-SKU
- `fastbuds-autos-com-variation-ids.json` &ndash; HJ-SKU &rarr; Variations-ID auf .com
- `fastbuds-autos-geschrieben.json` &ndash; Verifikation: SKU &rarr; [ID, B2B, Anbauverein, source, source]
