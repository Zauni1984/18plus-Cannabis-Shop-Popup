# Juicy Jay's: Boxtexte neu, 13 sortenreine Einzelprodukte (17.09.2026)

## Entscheidung: keine Varianten

Die erste Idee war, die beiden Mix-n-Roll-Boxen in variable Produkte mit
„Box + Einzelsorte" umzubauen. Verworfen: der Shop fuehrt Juicy-Jay's-Sorten
seit jeher als eigenstaendige Produkte (9006 Blueberry, 9009 Very Cherry,
9013 Watermelon). Die bestehenden bleiben unveraendert, die neuen kommen als
weitere Einzelprodukte dazu.

## Die EAN-Kollision

**Blueberry aus der King-Size-Slim-Liste (716165178750) gibt es schon** – als
Produkt 9006 zu 1,00 € mit 13 Stueck. Kein zweiter Artikel; der eine lagernde
Beutel ist auf **9006 gebucht, Bestand 13 → 14**.

Gefunden wurde das nur ueber einen Direktvergleich der gespeicherten GTINs:
die Produktsuche der Schnittstelle durchsucht das `gtin`-Feld **nicht**, eine
Suche nach der EAN meldet „nicht im Shop", obwohl der Artikel dasteht. Wer
sich darauf verlaesst, legt Dubletten an.

**Offen:** 9006 kostet weiterhin 1,00 €, die sieben neuen Sorten derselben
Reihe 1,50 €. Derselbe Artikeltyp zu zwei Preisen.

## Was angelegt wurde

### 2in1 (32 Blaettchen + integrierte Tips), je 2,00 € brutto

| ID | Sorte | EAN | Bestand |
|---|---|---|---:|
| 45309 | Grape | 716165306771 | 2 |
| 45310 | Strawberry | 716165306726 | 4 |
| 45312 | Watermelon | 716165306757 | 4 |
| 45313 | Bubblegum | 716165306764 | 4 |
| 45314 | Blueberry | 716165306733 | 1 |
| 45315 | Cotton Candy | 716165306740 | 4 |

### King Size Slim (32 Blaettchen), je 1,50 € brutto

| ID | Sorte | EAN | Bestand |
|---|---|---|---:|
| 45316 | Green Apple | 716165174622 | 1 |
| 45317 | Strawberry | 716165178408 | 2 |
| 45318 | Pineapple | 716165200185 | 3 |
| 45319 | Raspberry | 716165172598 | 1 |
| 45320 | Coconut | 716165179092 | 1 |
| 45321 | Mello Mango | 716165172611 | 3 |
| 45322 | Jamaican Rum | 716165178774 | 3 |

Alle: Kategorie Papers, Marke Juicy Jay's, Lieferzeit 1–3 Tage, kein
Lieferrueckstand, Paket Standard, drei Attribute (Format, Geschmack,
Herkunft), eigener Text, Kurztext und Yoast-Felder.

### Die beiden Boxen

18984 und 18985 haben neue Fliesstexte – vorher waren es reine
Stichpunktlisten, und in 18984 war die Sortenliste ein leeres `<li></li>`.
Beide stehen jetzt auf **3–7 Tage** und erlauben **Nachbestellung**
(`backorders: notify`), die Einzelpackungen nicht.

## Was bewusst leer blieb

**MPN.** Die drei bestehenden Einzelsorten tragen `JJ-PP-KSS-<Sorte>` – ein
konsistentes Muster, aber aus der eigenen Pflege, nicht vom Hersteller. Beim
Smoking-Sortiment hat sich ein aehnlich „offensichtliches" Muster als falsch
erwiesen (zwei Boxen trugen die Nummer eines dritten Artikels). Die GTIN
identifiziert den Artikel eindeutig, der MPN bleibt leer.

**Gewicht der 2in1-Hefte.** Die reinen Papierhefte haben 0,006 kg – belegt
ueber 9006, 9009 und 9013. Fuer die 2in1-Variante mit Tipkarte gibt es keinen
belegten Wert; das Feld bleibt leer, wie schon bei den Boxen.

**Bilder.** Alle 13 neuen Produkte haben noch keins.

## Nachtrag

Sechs der 2in1-Meta-Descriptions waren beim ersten Schreiben ueber 156
Zeichen – der Zusatz „mit integrierten Tips" hat sie gesprengt. Korrigiert.
Bei generierten SEO-Texten lohnt die Laengenpruefung im selben Lauf.
