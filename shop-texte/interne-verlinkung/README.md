# Interne Verlinkung: Upsells und Cross-Sells (hanfjack.de)

Jedes Produkt bekommt **4 Upsells** und **2 Cross-Sells** per Produkt-ID.

* **Upsells** stehen auf der Produktseite unter „Das könnte dir auch gefallen"
  und sind damit echte interne Links – der SEO-Teil der Sache.
* **Cross-Sells** erscheinen nur im Warenkorb. Sie helfen dem Warenkorbwert,
  bringen aber keine Linkkraft, weil der Warenkorb auf noindex steht.

## Wie die Auswahl entsteht

`verlinkung.py plan` liest `katalog.json` und `kategorien.json` (beides ein
Abzug der REST-API im Arbeitsverzeichnis, Pfad über `HJ_ARBEIT`) und schreibt
`plan.json`.

Als Ziel kommt nur in Frage, was auch verkauft werden kann: veröffentlicht,
im Katalog sichtbar, nicht ausverkauft, Preis über null.

**Upsells** werden aus der engsten Kategorie gezogen, die mindestens sechs
Kandidaten hat. Punkte gibt es für die Tiefe der gemeinsamen Kategorie
(25 je Ebene), gleiche Marke (45), gleiche Lagerquelle (30) und einen Preis
knapp über dem eigenen. Bestehende, inhaltlich passende Verknüpfungen
bekommen einen Bonus, damit gepflegte Handarbeit erhalten bleibt.

**Cross-Sells** kommen aus der Tabelle `ERGAENZUNG`: Slug der eigenen
Kategorie → Slugs ergänzender Kategorien, gesucht vom tiefsten Zweig
aufwärts. Samen → Dünger, Erde, Töpfe, Anzuchtmedien. Papers → Filter,
Feuerzeuge, Rolling Trays. Growbox → Lampe, Lüfter, Zeltzubehör. Kleinteile
unter 3 Euro fallen bei teureren Artikeln heraus, sonst landet eine
0,29-Euro-Schraube als Vorschlag im Warenkorb.

**Lagerquellen** werden gemischt erkannt – Schlagwort für das eigene Lager,
Artikelnummer bzw. Marke für die Lieferanten:

| Quelle | Erkennung | Produkte | Upsells in der eigenen Quelle |
|---|---|---:|---:|
| Beilngries | Schlagwort `Beilngries` | 801 | 96,8 % |
| Bloomtech | Artikelnummer 5-stellig numerisch | 754 | 96,5 % |
| Grow In | Artikelnummer 6-stellig numerisch | 315 | 98,9 % |
| Hanfjack | Schlagwort `Hanfjack` | 244 | 97,3 % |
| Tiger One | Marke (9 Sortenhäuser) | 222 | 96,4 % |

Die Grow-In- und Bloomtech-Nummern sind die Artikelnummern der Lieferanten und
überschneiden sich nicht (6- gegen 5-stellig, siehe
`../b2b-preise/growin-preispruefung.md`). Bei Tiger One hängen die
Lieferantennummern an den **Variationen** (`CCG-008-F6`, `ACEMIXREGU10`), die
Elternprodukte tragen HJ-Nummern – deshalb läuft die Erkennung dort über die
Marke: Ethos Genetics, Ace Seeds, The Cali Connection, Brothers Grimm Seeds,
Trailer Park Boys, James Loud Genetics, Solfire Gardens, Lovin' In Her Eyes,
Grand Daddy Genetics.

Schlagwort schlägt Artikelnummer: was in Beilngries liegt, ist tatsächlich
vorrätig und wird zuerst dorthin verknüpft. 2.131 Produkte gehören keiner
dieser Quellen an; sie werden nach Kategorie und Marke verknüpft.

Ein Produkt darf höchstens **12-mal** Upsell-Ziel sein (Cross-Sell 40-mal).
Das verteilt die Links statt sie auf wenige Hubs zu bündeln: 3.334 von 3.340
verkaufsfähigen Produkten erhalten mindestens einen Link.

## Schreiben

`schreiben.py` überträgt `plan.json` in Stapeln über `products/batch` und
merkt sich fertige IDs in `geschrieben.txt`, ist also wiederholbar.
Stapelgröße und Pause über `HJ_STAPEL` und `HJ_PAUSE`; bei DB-Fehlern oder
Bot-Sperre wartet der Aufrufwrapper und versucht es erneut.

`sicherung_vorher.json` hält die 1.318 Produkte fest, die vorher schon
Verknüpfungen hatten – Stand vor dem ersten Durchlauf.

## Zahlen des ersten Durchlaufs

| | |
|---|---|
| Produkte mit Verknüpfungen | 4.467 |
| Upsell-Verknüpfungen | 17.864 (99,9 % gemeinsame Kategorie, 81 % gleiche Marke) |
| Cross-Sell-Verknüpfungen | 8.932 |
| Produkte ohne vollständigen Satz | 1 (Golden Bell, nur im Produktarchiv) |
