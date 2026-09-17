# Neue Produkte auf hanfjack.de anlegen

## Ablauf

1. **Schwesterprodukt suchen.** Fast jedes neue Produkt ist eine Variante von
   etwas, das schon im Shop steht. Dessen Datensatz liefert Kategorie, Marke,
   Hersteller, Versandklasse, Gewicht, Steuerklasse, Attributnamen, Tags und
   den Textaufbau – alles bereits auf die Konventionen des Shops eingestellt.
2. **Nur das uebernehmen, was nachweislich gilt.** Technische Angaben aus dem
   Schwesterprodukt sind belastbar, solange sich die Ware nur im Design
   unterscheidet. Alles andere muss aus einer Quelle kommen.
3. **Netto rechnen.** Der Shop speichert netto. Bei 19 % ist
   `netto = brutto / 1,19`, bei Saatgut und anderen 7-%-Waren `/ 1,07`.
4. **`min_age` nicht mitschicken.** Der Shop setzt es selbst ueber die
   Kategorie; ein mitgeschicktes `min_age` hat frueher den Google-Feed
   zerschossen.
5. **Nach dem Anlegen nachsetzen:** `delivery_time` und `manufacturer` nimmt
   die Schnittstelle beim Anlegen nicht an. Sie muessen als eigener PUT
   folgen, und zwar als Objekt: `{'delivery_time': {'id': 4236}}` – eine
   blanke ID wird mit 400 abgelehnt. Dasselbe gilt fuer `brands`, das
   umgekehrt **nur** als Liste von IDs akzeptiert wird (`[1848]`), nicht als
   `[{'id': 1848}]`.
6. **Texte gegen `shop-texte/html-hygiene/html_fix.py` pruefen** – dann passt
   der Abstand ueber den Ueberschriften ohne Theme-CSS.

## Erstes Beispiel: PURIZE Regular Size Leopard 50er (17.09.2026)

**Produkt 45296**, angelegt als Entwurf.

| Feld | Wert |
|---|---|
| Name | PURIZE Aktivkohlefilter Regular Size 9mm Leopard 50 Stück |
| SKU | HJ-7310482 |
| Preis | 7,48 € netto = **8,90 € brutto** |
| Bestand | 16 |
| Lieferzeit | 1–3 Tage |
| Kategorie | Regular 8-9mm |
| Marke / Hersteller | PURIZE / PURIZE® Filters GmbH & Co.KG |
| Attribute | Motiv: Leopard · Inhalt: 50 Stück · Variante: 9mm |
| Tags | 10, identisch zum Schwesterprodukt |
| Grundpreis | 0,15 €/Stück (automatisch) |
| Yoast | Titel 49 Zeichen, Meta-Description 141 Zeichen |

**Quelle der technischen Angaben:** Produkt 13690, PURIZE Regular Size 9mm
Weiss 50 Stueck. Durchmesser 9 mm (8,3 mm technisch), Laenge 35,7 mm,
Steinkohle-Aktivkohle, Keramikkappen beidseitig, hergestellt in Deutschland.
Das Leopard-Design betrifft die Optik, nicht den Aufbau – im Text steht das
auch so.

Bilder wurden nachgereicht und zugeordnet, der Artikel ist **veroeffentlicht**.

Die Bilder haben den Text praezisiert: das Leopardenmuster sitzt auf dem
Filter selbst, nicht nur auf der Verpackung, und der Beutel ist
wiederverschliessbar. Beides steht jetzt in Beschreibung und Tabelle.

**Offen:** GTIN und MPN. Beim Schwesterprodukt stehen dort `4260748410653`
und `Reg50-White`; die Leopard-Nummern liegen nur im B2B-Portal. Ohne GTIN
laeuft der Artikel im Google-Feed schlechter.

---

## Zweites Beispiel: Smoking Supreme King Size Slim 2in1 (17.09.2026)

**Produkt 45305**, veroeffentlicht.

| Feld | Wert |
|---|---|
| Name | Smoking Supreme King Size Slim 2in1 |
| SKU / GTIN | HJ-2951736 / 8414775023164 |
| Preis | 1,68 € netto = **2,00 € brutto** |
| Bestand | 19, **kein Lieferrueckstand** |
| Lieferzeit | 1–3 Tage · Paket Standard |
| Kategorie | Papers · Marke Smoking · Miquel y Costas & Miquel, S.A. |
| Attribute | Format: King Size Slim · Inhalt: 1 Heft · Material: 100 % pflanzlich |
| Tags | 7 |
| Bilder | 3, Einzelheft geschlossen zuerst |

**Quellen der Angaben:** Produkt 18977 (dieselbe Ware als 24er-Box) liefert
Format 110 × 44 mm, 33 Blaettchen + 33 Tips je Heft, FSC-Zertifizierung,
Naturgummi und Slow Burning. Die Produktbilder ergaenzen Ultra Smooth Touch,
Herstellung in Barcelona und den Spezialschnitt der Tips.

### Der MPN bleibt bewusst leer

Das naheliegende Muster – Box = `VE-` plus Einzelnummer – traegt nicht.
Produkt 18980 („Master KS Ultra Slim Box") und 18981 („Brown Creator Box")
tragen **beide** `VE-SMK-Kukuxumusu-KS`, also die Nummer eines dritten
Artikels. Im Shop stehen also mindestens zwei falsche MPNs; daraus laesst
sich nichts ableiten. Ein leeres Feld ist besser als eine falsche Nummer.

### Zwei Dinge zum Nacharbeiten

- **Das Gewicht 0,019 kg** stammt vom baugleichen Smoking Red King Size 2in1
  (8992) – gleiche Bauart, 33 Blaettchen mit Tips. Es ist uebernommen, nicht
  gemessen.
- **Die Box 18977 steht auf `private`** und hat einen kaputten Kurztext
  (`<p>Ultrafeine, transparente</p>` bricht mitten im Satz ab). Wenn sie
  wieder oeffentlich werden soll, gehoert der Text ueberarbeitet.

### Nachtrag zur Laengenkontrolle

Die Meta-Description war beim ersten Schreiben 161 Zeichen lang und damit
ueber der Grenze von 156. Bei neuen Produkten lohnt der Blick darauf, bevor
der Artikel live geht – Google schneidet sonst ab.


---

## Drittes Beispiel: Dutch Passion Rolling Papers King Size Slim + Tips (17.09.2026)

**Produkt 45340**, Entwurf (Bilder kommen nach).

| Feld | Wert |
|---|---|
| SKU / MPN | HJ-7777627 / ROLPSINGLE |
| Preis | 1,68 € netto = **2,00 € brutto** |
| Bestand | 7, kein Lieferrueckstand |
| Lieferzeit | 1–3 Tage · Paket Standard |
| Kategorie | Papers · Marke und Hersteller Dutch Passion |
| Attribute | Format: King Size Slim · Inhalt: 1 Heft |
| Tags | 6 |

**Quelle:** die Herstellerseite selbst – anders als bei den B2B-Portalen ist
dutch-passion.com frei abrufbar. Belegt sind King Size Slim, Slow Burning,
Tips im Heft, die Verpackung als Origami-Rolling-Tray und die Artikelnummer
ROLPSINGLE.

**Der MPN ist hier gesetzt**, anders als bei Smoking und Juicy Jay's: Er steht
auf der Herstellerseite, ist also keine Ableitung aus einem Muster.

### Was der Hersteller nicht nennt

Weder Blattzahl noch Papiermaterial noch eine EAN. Das steht so auch im
Hinweistext des Produkts, und geschaetzt wird nichts. Das **Gewicht** bleibt
aus demselben Grund leer – Papers, Tips und Faltkarton zusammen lassen sich
nicht aus den Juicy-Jay's-Werten ableiten.

### Nebenbefund zum Hersteller-Datensatz

Der Eintrag 6071 fuehrt **Lauwe Zaunreither GbR als EU-Verantwortlichen**.
Zaadhandel Dutch Passion BV sitzt aber in Amsterdam, also in der EU – nach
GPSR Art. 16 ist der Hersteller dann selbst der Wirtschaftsakteur und
braucht keinen Bevollmaechtigten. Der Eintrag wurde nicht veraendert, gehoert
aber geprueft.

---

## Viertes Beispiel: RQS Organic Rolling Papers King Size (17.09.2026)

**Produkt 45349**, veroeffentlicht.

| Feld | Wert |
|---|---|
| SKU / GTIN / MPN | HJ-2022389 / 8435523607214 / RQSPR002OP |
| Preis | 0,84 € netto = **1,00 € brutto** (19 %) |
| Bestand | 15, kein Lieferrueckstand |
| Lieferzeit | 1–3 Tage · Paket Standard |
| Kategorie | Papers · Marke Royal Queen Seeds · Snorkel Spain S.L. |
| Attribute | Format: King Size, 110 × 45 mm · Inhalt: 1 Heftchen mit 32 Blaettchen · Material: Zellstoff und Gummi arabicum |
| Tags | 6 |
| Bilder | 2 |
| Yoast | Titel 51 Zeichen, Meta-Description 143 Zeichen |

**Quelle:** die Herstellerseite royalqueenseeds.de/free-seeds/251-rqs-biologische-blaettchen.html,
zusaetzlich die Produktbilder. Belegt sind 32 ungebleichte Blaettchen je
Heftchen, natuerlicher Zellstoff und Gummi arabicum aus Europa, ultraduenn und
langsam brennend, das Heftchen aus Kraftpapier (Kiefer, Bambus,
Agrarabfaelle), das Mass 110 × 45 mm und die Artikelnummer RQSPR002OP.

### Die EAN kam aus zwei Richtungen

Angesagt war `8435523607641`, auf der Herstellerseite steht aber nur
`8435523607214`. Nach Ruecksprache gilt die Online-Nummer. Ein Abgleich gegen
alle 178 Shop-Produkte der Marke zeigte keinen Konflikt – beide Nummern waren
im Shop frei. Die Produktsuche taugt dafuer nicht: sie indexiert `gtin` nicht
und meldet fuer jede EAN „nicht gefunden".

### Tips stehen bewusst nicht im Text

Eine Kundenrezension auf der Herstellerseite sagt „Tips gleich dabei". Der
Herstellertext nennt keine, und auf den Bildern ist kein Tip-Heftchen zu
sehen. Statt der Rezension zu folgen, benennt der Hinweistext die Lage.

### Nebenbefunde

- **Medium 45348** (`RQS-biologische-Blaettchen3.jpg`) ist byte-identisch mit
  45346 – ein Doppel-Upload. Es wurde nicht zugeordnet und nicht geloescht.
- **Hersteller 6565 (Snorkel Spain S.L.)** fuehrt wie Dutch Passion einen
  EU-Bevollmaechtigten, obwohl Spanien in der EU liegt. Gehoert zur Liste in
  `shop-texte/gpsr/`.
- **Backorders `no` und Versandklasse `paket-standard`** sind nicht geraten,
  sondern die Konvention der Kategorie Papers: 121 von 128 veroeffentlichten
  Artikeln stehen auf `no`, alle 128 auf `paket-standard`.

---

## Fuenftes Beispiel: Anesia Seeds Organic Rolling Papers King Size (17.09.2026)

**Produkt 45350**, **Entwurf** – geht live, sobald die Bilder da sind.

| Feld | Wert |
|---|---|
| SKU | HJ-3192942 |
| Preis | 0,84 € netto = **1,00 € brutto** (19 %) |
| Bestand | 50, kein Lieferrueckstand |
| Gewicht | 0,001 kg |
| Lieferzeit | 1–3 Tage · Paket Standard |
| Kategorie | Papers · Marke Anesia Seeds · Ruperts Farm SL |
| Attribute | Format: King Size · Inhalt: 1 Heftchen mit 32 Blaettchen · Farbe: Schwarz · Motiv: Krokodil |
| Tags | 4 |
| Yoast | Titel 45 Zeichen, Meta-Description 121 Zeichen |

**Quelle:** ausschliesslich die Ansage. Zu dem Heftchen steht online nichts,
also stehen im Datensatz auch nur die genannten Angaben: 32 Blaettchen, King
Size, schwarzes Heftchen mit Anesia-Logo und Krokodil-Motiv, 1 g, keine EAN.

### Was leer bleibt und warum es im Text steht

Papiermaterial, Gummierung, Grammatur und Blattmass sind nicht belegt. Statt
sie vom RQS- oder Dutch-Passion-Heftchen abzuleiten – andere Marke, anderes
Werk – benennt der Hinweistext sie als offen. Ebenso das Wort „Organic": es
stammt aus der Artikelbezeichnung, nicht aus einem Zertifikat, und der Text
sagt das auch.

### Drei uebernommene Konventionen statt Vorgaben

Lieferzeit 1–3 Tage, Versandklasse Paket Standard und `backorders: no` waren
nicht angesagt. Sie folgen der Kategorie Papers (alle 128 veroeffentlichten
Artikel auf Paket Standard, 121 davon auf `no`) und dem Umstand, dass die Ware
mit 50 Stueck selbst lagernd ist – daher auch der Tag `Beilngries`.

### Der Artikelname weicht von der Ansage ab

Angesagt war „Anesia Seeds KS Organic Heftchen". Im Shop heisst der Artikel
**Anesia Seeds Organic Rolling Papers King Size**, damit er in der Reihe mit
den anderen Blättchen-Heften steht (Dutch Passion, RQS, Smoking).

---

## Regel: „Papers" gehoert nicht in den Produkttitel (17.09.2026)

Im ganzen Katalog trugen genau drei Artikel „Papers" im Namen – alle drei
neu angelegt. Der Rest der Kategorie heisst seit jeher
`<Marke> <Sorte> <Format>`, etwa „Juicy Jay´s Jamaican Rum King Size Slim"
oder „Smoking Supreme King Size Slim 2in1". Die drei wurden nachgezogen:

| ID | vorher | jetzt |
|---|---|---|
| 45340 | Dutch Passion Rolling Papers King Size Slim + Tips | Dutch Passion King Size Slim + Tips |
| 45349 | RQS Organic Rolling Papers King Size | RQS Organic King Size |
| 45350 | Anesia Seeds Organic Rolling Papers King Size | Anesia Seeds Organic King Size |

**Die Slugs blieben stehen.** Sie tragen `rolling-papers` weiter, aendern
wuerde bestehende URLs brechen und eine Weiterleitung noetig machen.

**Die Yoast-Titel ebenfalls.** Dort ist „Rolling Papers" ein Suchbegriff und
steht nicht im Produktfeed.

---

## Sechstes Beispiel: zwei G-Rollz-Einzelheftchen aus den Boxen (17.09.2026)

**45357 G-Rollz Diablos King Size Slim** und **45358 G-Rollz King´s Choice
King Size**, beide **Entwurf** – es fehlen Bilder des Einzelheftchens.

| Feld | beide |
|---|---|
| SKU | HJ-4847587 / HJ-8480023 |
| Preis | 0,84 € netto = **1,00 € brutto** (19 %) |
| Bestand | je 50, kein Lieferrueckstand |
| Lieferzeit | 1–3 Tage · Paket Standard |
| Kategorie | Papers · Marke G-Rollz · New Ways BV |
| Tags | 7 bzw. 6 |

### Die Boxbilder haben die Arbeit gemacht

Die Ausgangsartikel 30294 und 30290 trugen beide den Satz „Wie viele Heftchen
die Box enthaelt, gibt die Produktbezeichnung nicht an". Zwei Quellen haben
das aufgeloest:

- **thenewways.com** (New Ways BV ist im Shop als Hersteller beider Boxen
  hinterlegt): `GR08A-DIS` = „Diablos – 50 White KS Slim Papers (50 Booklets
  Display)", `GR09A-DIS` = „King's Choice – 50 White KS Wide Papers (50
  Booklets Display)". **Beide EANs der Seite stimmen mit den GTINs der
  Shop-Boxen ueberein** – damit ist die Zuordnung hart, nicht geraten.
- **Die Boxbilder im Shop.** Der Aufdruck ist lesbar: „32 + 18 FREE PAPERS IN
  EACH BOOKLET", „KING SLIM CLASSIC – 50 ultra thin papers" bzw. „KING SIZE
  PAPERS – 50 extra thin papers", dazu die Siegel Chlorine Free, Non GMO,
  Vegan Product, „Vegan + Plant Based" und „Partner (Spain)".

Beide Boxtexte wurden entsprechend nachgezogen (`fix_grollz_box.py`, Stand
vorher in `grollz_box_vorher.json`), inklusive MPN, Tags, Attributen und
Meta-Description. 50 Heftchen à 50 Blaettchen sind 2.500 Blaettchen je Box.

### Was WebFetch falsch gelesen hat

Die Zusammenfassung der Herstellerseite behauptete fuer Diablos „Tips
Included: Yes". Im Rohtext steht davon nichts – andere G-Rollz-Linien heissen
ausdruecklich „… Papers + Tips", diese beiden nicht. Die Rohquelle schlaegt
die Zusammenfassung.

### Was leer bleibt

- **GTIN** der Einzelheftchen: die bekannten Nummern gehoeren den Displays.
- **MPN** der Einzelheftchen: veroeffentlicht ist nur `GR08A-DIS` /
  `GR09A-DIS`. Die Basisnummer ohne `-DIS` waere eine Ableitung aus einem
  Muster – genau die Falle aus dem Smoking-Beispiel.
- **Gewicht:** 0,344 kg je Display durch 50 waere 6,9 g, aber der Karton
  zaehlt mit. Das ist eine Schaetzung, also bleibt das Feld leer.
