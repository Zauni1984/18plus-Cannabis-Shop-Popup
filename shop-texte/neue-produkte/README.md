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

### Warum Entwurf und nicht veroeffentlicht

Es fehlt das **Produktbild**. Die Herstellerseite fuehrt das Leopard-Design
nicht, und das B2B-Portal `b2b-headshop.de` antwortet auch ueber einen echten
Browser mit einer Bot-Sperre (HTTP 401) – von dort ist ohne Login nichts zu
holen. Ein Artikel ohne Bild ist im Shop und im Feed unbrauchbar, deshalb
steht er auf Entwurf, sonst aber vollstaendig.

### Ebenfalls offen

- **GTIN und MPN** sind leer. Beim Schwesterprodukt stehen dort
  `4260748410653` und `Reg50-White`; die Leopard-Nummern liegen nur im
  B2B-Portal. Ohne GTIN laeuft der Artikel im Google-Feed schlechter.
- `min_age` hat der Shop selbst auf 18 gesetzt – nichts zu tun.
