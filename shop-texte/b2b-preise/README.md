# B2B- und Anbauverein-Preise (hanfjack.com)

Stand: 08.09.2026 &ndash; Fast Buds Autoflower und Feminisiert

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

| Linie | Produkte | Variationen |
| --- | --- | --- |
| Autoflower (HJ-7000001 &ndash; HJ-7000063) | 63 | 435 |
| Feminisiert (HJ-7000064 &ndash; HJ-7000079) | 16 | 112 |
| **Summe** | **79** | **547** |

Alle geschriebenen Variationen wurden über `wwpro_wholesale_prices` mit
`source: "variation"` bestätigt; SKU und Variations-ID stimmen in allen Fällen
überein.

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
IDs wurden für alle 547 Variationen über die zurückgemeldeten SKUs geprüft
&ndash; keine Abweichung. Für HJ-7000079 (letztes Produkt, ohne Nachfolger)
wurden die IDs direkt gelesen.

## Nachtrag 10er-Packungen

Drei Strains hatten im Auto-Sheet einen falschen EK für die 10er-Packung
(9,50&nbsp;€). Korrigiert auf **49,50&nbsp;€**, damit B2B 54,45&nbsp;€ und
Anbauverein 69,30&nbsp;€:

- HJ-7000021-10 &ndash; Gelato Auto
- HJ-7000024-10 &ndash; Gorilla Z Auto
- HJ-7000063-10 &ndash; Ztrawberriez Auto

Der Laden-VK dieser drei Variationen stammte ebenfalls aus dem falschen EK
(17,76&nbsp;€ netto) und wurde nach der im Katalog durchgängig verwendeten Regel
(brutto&nbsp;=&nbsp;2&nbsp;&times;&nbsp;EK, netto&nbsp;=&nbsp;brutto&nbsp;/&nbsp;1,07)
auf **92,52&nbsp;€ netto / 99,00&nbsp;€ brutto** korrigiert. Damit greifen die
Rollenpreise wieder: das Plugin geht nie über den regulären Preis hinaus, und
vor der Korrektur wurden für beide Rollen nur 17,76&nbsp;€ ausgespielt.

## Dateien

- `fastbuds-autos-b2b-plan.json` &ndash; berechnete Preise je HJ-SKU
- `fastbuds-autos-com-variation-ids.json` &ndash; HJ-SKU &rarr; Variations-ID auf .com
- `fastbuds-autos-geschrieben.json` &ndash; Verifikation: SKU &rarr; [ID, B2B, Anbauverein, source, source]
- `fastbuds-fems-b2b-plan.json`, `fastbuds-fems-com-variation-ids.json`,
  `fastbuds-fems-geschrieben.json` &ndash; dasselbe für die feminisierte Linie
