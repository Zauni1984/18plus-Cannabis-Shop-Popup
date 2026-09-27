# Listing-Konventionen (hanfjack.de)

Feste Regeln für das Anlegen neuer Produkte. Gelten für alle Marken, nicht nur Tiger One.

## SKU

**Jedes Elternprodukt braucht eine SKU.** Ohne SKU lassen sich Produkte nicht von einem
Shop in den anderen syncen. Das gilt auch für variable Produkte, bei denen die
Lieferanten-Artikelnummer eigentlich nur an der Variante hängt.

Konvention:

| Ebene | SKU |
| --- | --- |
| Elternprodukt | Lieferanten-Artikelnummer (Basis), z. B. `NV-AK48` |
| Variante | Basis + Packungsgröße, z. B. `NV-AK48-5` |

Wenn der Lieferant schon Packungs-Suffixe mitliefert (z. B. Silent Seeds
`SIL-ZK2-FEM` / `SIL-ZK2-FEM-3`), wird genau dieses Schema übernommen.
Wenn nicht (z. B. Nirvana Seeds), wird der Suffix aus der Packungsgröße gebildet.

## Produkte mit `HJ-`-SKU

Produkte und Varianten, deren SKU mit `HJ-` beginnt, werden **nie** angefasst,
solange sie nicht ausdrücklich benannt werden.

## Preise

- Endkundenpreise immer über **hanfjack.de** pflegen, nicht über hanfjack.com.
- Aktions- und Sonderpreise werden ignoriert, es werden immer die normalen Preise eingepflegt.
- Preise werden auf volle Euro **abgerundet**.
- Preise werden als **Netto** eingetragen, der Shop zeigt brutto.

## Stammdaten Samen

| Feld | Wert |
| --- | --- |
| Steuer | `tax_status: taxable`, `tax_class: reduzierter-preis` (7 %) |
| Versandklasse | `paket-standard` (Term 12792) |
| Lieferzeit | `delivery_time: {"id": 5417}` = „2-3 Wochen" |
| Attribut | `pa_inhalt` (Attribut-ID 2, global, variantenbildend) |
| Kategorien | Samen 532 → Feminisiert 548, Automatisch 547, Regular 549, CBD 2309, F1 12729 |
| Marke | Taxonomie `pwb-brand` (nicht `product_brand`), per `wp_add_post_terms` setzen |
| Gewicht | `0.01` |

## Hersteller & Altersfreigabe

- Herstelleradressen und Packungsgrößen werden nie erfunden, nur Dokumentiertes eingetragen.
- **Vitaponix** und **Jack Puck** bleiben ohne Hersteller (VITAPONIX LTD ist 2017 aufgelöst).
- Beim Schreiben auf hanfjack.de wird `min_age` nie mitgesendet.
- Dünger dürfen **keine** 18+-Altersfreigabe tragen (bricht Google Merchant Center).

## Medien

Medien löschen ist nicht umkehrbar – nichts löschen, was ein Produkt noch referenziert.
