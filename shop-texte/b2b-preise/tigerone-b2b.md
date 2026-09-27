# Tiger One Seeds – B2B- und Anbauvereinpreise (hanfjack.com)

**Stand:** 09.09.2026 · **Shop:** hanfjack.com (B2B/Anbauverein) · **Quelle:** Tiger-One-Preisliste (Google Sheet, Spalte F = EK netto)

## Aufschlag

| Rolle | Meta-Key | Formel |
|---|---|---|
| B2B Kunde | `_wwpro_price_b2b_customer` | EK × 1,10 |
| Anbauverein | `_wwpro_price_anbauverein` | EK × 1,30 |

Rundung: kaufmännisch auf 2 Nachkommastellen. Alle Preise netto (Seed-Variationen laufen auf `tax_class: reduced-rate`, `regular_price` ist der Nettopreis).

## Ergebnis

- **201 Variationen** in **183 Elternprodukten** bepreist – alle Tiger-One-SKUs, die auf hanfjack.com existieren.
- Abgleich rein über SKU (exakter Treffer). Die 6.668 SKUs der Liste wurden gegen den Shop geprüft; nur die unten aufgeführten 8 Marken sind gelistet.
- **Keine Deckelung durch Woo Wholesale Pro**: bei jeder Variation ist `price == own_price` und `source == "variation"`. Die Rollenpreise liegen bei ca. 55 % (B2B) bzw. 65 % (Anbauverein) des regulären VK.
- Verkaufspreise (B2C) wurden **nicht** angefasst.

### Nach Marke

| Marke | Variationen |
|---|---:|
| Ethos Genetics | 65 |
| Ace Seeds | 37 |
| The Cali Connection | 28 |
| Brothers Grimm Seeds (inkl. Trailer Park Boys) | 22 |
| James Loud Genetics | 20 |
| Solfire Gardens | 15 |
| Lovin' In Her Eyes | 11 |
| Grand Daddy Genetics | 3 |
| **Summe** | **201** |

## Hinweise

- Die Tiger-One-SKUs hängen an den **Variationen**; die Elternprodukte tragen weiterhin ihre `HJ-`-SKUs und wurden nicht verändert.
- Ein Ausreißer: `CCG-008-F6` (Variation 31324) hängt an Produkt 22668 „The Cali Connection Fruit Tree (Gold Collection)" mit Status `private` – ebenfalls bepreist.
- Aktions-/Sonderpreise aus der Liste wurden ignoriert, es zählt ausschließlich der reguläre EK aus Spalte F.
- Alle Einzelpreise stehen in `tigerone-b2b-preise.json`.
