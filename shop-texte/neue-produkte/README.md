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
