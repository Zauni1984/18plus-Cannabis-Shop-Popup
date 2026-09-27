# Plagron-Rollenpreise aus der Bloomtech-Liste (17.09.2026)

## Regel

Bloomtech ist der Lieferant. Der Satz steht in
`bloomtech-aquamaster-README.md` und gilt unveraendert:

| Rolle | Formel |
|---|---|
| B2B Kunde | EK × 1,10 |
| Anbauverein | EK × 1,30 |

Quelle ist der JTL-Export, Spalte **„JTL-Wawi: Händler Netto"**. Die neue
Datei ist inhaltsgleich mit der frueher verwendeten: alle 677 Werte, die sich
mit `bloomtech-b2b-preise.json` ueberschneiden, stimmen zeichengenau. Die
Bloomtech-Preise haben sich also nicht geaendert.

## Zuordnung

**Ein Titelabgleich war nicht moeglich: der Export fuehrt keine
Artikelbezeichnungen.** Er hat drei Spalten – Artikelnummer, EAN und
Haendler-Netto – plus Staffelpreise. Zwei Wege blieben:

| Weg | Positionen |
|---|---:|
| Shop-SKU ist die Bloomtech-Artikelnummer | 25 |
| Shop-GTIN steht als EAN in der Liste | 30 |
| **geschrieben** | **55** |

Alle 55 wurden zurueckgelesen: `own_price` stimmt, `price == own_price`,
`source` auf `product` bzw. `variation`. Keine Abweichung.

Die Handelsspanne liegt im Median bei VK = 1,63 × EK, Spanne 1,30 bis 2,58 –
plausibel fuer Distributionsware.

## Was fehlt: die Artikelbezeichnungen

**49 Plagron-Positionen bleiben ohne Preis** (`com-plagron-offen.json`):

- **38 ohne GTIN im Shop** – vor allem die Erden in 50-Liter-Saecken
  (Cocos Premium, Batmix, Promix, Allmix, Royalmix, Lightmix, Growmix).
- **11 mit GTIN**, die aber in der Liste nicht vorkommt.

Gleichzeitig stehen **13 Zeilen mit Plagron-EAN in der Liste**, denen kein
Shop-Artikel zugeordnet ist. Es fehlt also nicht der Preis, sondern die
Bruecke zwischen beiden Seiten.

Andere Wege wurden geprueft und verworfen:

- **MPN**: keine der 104 offenen Positionen fuehrt eins. Bei Aqua Master lief
  die Zuordnung frueher genau darueber – bei Plagron ist das Feld leer.
- **Ueber den Verkaufspreis**: der Shop-VK laesst sich nicht sauber auf die
  Bloomtech-VK-Liste zurueckfuehren. Das Verhaeltnis streut zwischen 0,96 und
  1,71, nur 19 von 55 liegen auf 2 % genau gleich. Damit ist ein Artikel ueber
  den Preis nicht identifizierbar.

**Das loest ein Export mit der Spalte „Artikelname" oder „Artikelbezeichnung".**
JTL-Wawi kann sie mitgeben; dann greift derselbe Titelabgleich, der bei HEMPER
funktioniert hat.

## Dateien

- `com-plagron-rollenpreise.json` – die 55 geschriebenen Preise mit EK,
  Bloomtech-Nummer und Zuordnungsweg
- `com-plagron-offen.json` – die 49 Positionen ohne Quelle
- `hol_plagron.py`, `plagron_plan.py`, `schreib_plagron.py`
