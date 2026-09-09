# Bloomtech / Aqua Master – B2B- und Anbauvereinspreise (hanfjack.com)

Quelle EK: `jtlexportartikelEK.csv` (JTL-Wawi Export, Spalte "Händler Netto"), 2001 Positionen.
Regel: **B2B Kunde = EK + 10 %**, **Anbauverein = EK + 30 %** (kaufmännisch auf 2 Stellen gerundet).
Geschrieben als exakte Rollenpreise in `_wwpro_price_b2b_customer` / `_wwpro_price_anbauverein`.

## Bloomtech (numerische Artikelnummer als Shop-SKU)
- **677 Produkte gesetzt** – Zuordnung 1:1 über die Artikelnummer = Shop-SKU.
- Datei: `bloomtech-b2b-preise.json`
- 12 Shop-Produkte mit numerischer SKU haben keine Zeile in der CSV und blieben ohne Preis:
  15639, 12737, 12739, 14719, 12715, 15727, 15704, 13031, 11084, 17293, 16970, 15188
- Die übrigen ~1300 CSV-Positionen haben kein Produkt auf hanfjack.com.

## Aqua Master (HJ-Artikelnummer als Shop-SKU)
- **14 Produkte gesetzt** – Zuordnung über das MPN-Feld (`_ts_mpn`), das die Bloomtech-Nummer trägt.
- Datei: `aquamaster-b2b-preise.json`
- Offen: **29419 Aqua Master Tools E300 Pro EC Substrat** (MPN 15514) – nicht in der Preisliste.

## Gedeckelte Produkte
107 Bloomtech-Produkte haben eine Handelsspanne unter dem Aufschlag; das Plugin begrenzt den
Rollenpreis dort auf den normalen VK. Liste: `bloomtech-gedeckelte-produkte.json`.
