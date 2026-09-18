# Kategorietexte auf hanfjack.de

## Das Format ist vorgegeben, und zwar enger als erwartet

**In Kategoriebeschreibungen laesst diese Installation kein HTML zu.**
Geprueft auf drei Wegen – WooCommerce-REST (`products/categories`), WordPress
(`wp/v2/product_cat`) und das MCP-Werkzeug `wp_update_term` – alle drei
liefern dasselbe: `<p>`, `<h2>` und `<ul>` verschwinden, ihr Inhalt bleibt
stehen und klebt am Nachbartext.

Ursache ist `wp_filter_kses` auf `pre_term_description`. Der Filter laesst nur
den kleinen Kommentar-Tagsatz durch. Dass der Anwendungspasswort-Nutzer
Administrator ist und `unfiltered_html` besitzt, aendert daran nichts –
geprueft.

**Was durchkommt:**

- **Leerzeilen.** WooCommerce laesst `wpautop` ueber die Beschreibung laufen:
  Leerzeile wird `<p>`, einzelner Umbruch wird `<br />`.
- **`<strong>`, `<em>`, `<a>`** und der Rest des Kommentar-Tagsatzes.

Daraus folgt das Format, das `kat_format.py` erzeugt:

```
Einleitungssatz.

<strong>Zwischenueberschrift</strong>
Absatz dazu.

<strong>Naechste Ueberschrift</strong>
Absatz dazu.
```

Auf der Seite wird daraus sauberes `<p>`/`<br />`-HTML mit fetten
Zwischenzeilen.

## Die 30 Bloecke von unten nach oben

Die Textbloecke aus `shop-texte/kategorien/below-category-content.json`
standen als Term-Meta `below_category_content` in der Datenbank und sollten
unter dem Produktraster erscheinen. **Ausgegeben wurden sie nie** – auf keiner
Kategorieseite taucht einer auf, und unter den WPCode-Snippets gibt es
nichts, was sie rendern wuerde. Sie sind jetzt in die Beschreibung ueber dem
Raster gezogen, wo sie hingehoeren.

30 Kategorien, von 83–192 Zeichen auf 219–516 Zeichen.

### Zwei Fehler dabei, beide behoben

1. **Erster Versuch als HTML geschrieben.** Ergebnis: alle Tags weg, die
   Ueberschriften klebten am Text („Woraus Hanftee bestehtGetrocknete
   Blaetter…"). Neu gebaut im zulaessigen Format.
2. **Listen zusammengeklebt.** `<ul><li>` wurde als ein Block behandelt, die
   Punkte liefen ineinander („entsorgt.Cartridges:"). Betraf die vier Bloecke
   4152, 5831, 6881 und 13416. `_absaetze()` zieht Listenpunkte jetzt einzeln
   heraus.

Stand vor dem Eingriff: `kat_beschreibung_vorher.json`.
Kontrolle danach: keine zusammengeklebten Stellen mehr.

---

## Stapel 1: die sechs groessten Kategorien (18.09.2026)

| ID | Kategorie | Produkte | vorher | jetzt |
|---|---|---|---|---|
| 532 | Samen | 1.608 | 172 | **1.412** |
| 538 | Growshop | 1.356 | 170 | **1.252** |
| 548 | Feminisiert | 966 | 145 | **1.360** |
| 547 | Automatisch | 471 | 192 | **1.393** |
| 55 | Headshop | 826 | 216 | **985** |
| 1132 | Duenger | 487 | 164 | **1.521** |

Die vorhandene Einleitung steht in allen sechs unveraendert als erster
Absatz – sie war sachlich richtig und im Hausstil, nur zu kurz allein. Der
Rest ist neu und wurde per Zusicherung im Skript gegen Verlust gesichert.

### Was in den Texten steht

Keine Werbesprache, kein Aufruf zum Kauf, nichts Erfundenes. Erklaert wird,
was jemand vor dem Kauf wissen muss:

- **Samen:** die vier Samenarten und wofuer jede taugt, Auswahlkriterien
  (Bluetezeit, Wuchshoehe, Ertrag, drinnen oder draussen), Lagerung,
  rechtlicher Rahmen.
- **Growshop:** was zur Grundausstattung gehoert, warum Licht und Abluft die
  zwei Stellschrauben sind, womit Einsteiger anfangen.
- **Feminisiert:** wie feminisierte Samen entstehen, was das im Anbau spart,
  der Unterschied photoperiodisch zu automatisch, wann reguläre Samen die
  bessere Wahl sind.
- **Automatisch:** warum Ruderalis-Erbgut nach Alter blueht statt nach
  Tageslaenge, der Zeitplan, die Grenzen (kleinerer Ertrag, wenig Zeit zur
  Erholung nach Stress), was im Anbau anders laeuft.
- **Headshop:** die drei Wege Drehen, Wasserpfeife, Verdampfen mit dem
  jeweils zugehoerigen Zubehoer; Verbrauchsmaterial gegen Anschaffung.
- **Duenger:** was NPK bedeutet, organisch gegen mineralisch, warum Wachstum
  und Bluete verschiedene Verhaeltnisse brauchen, Dosierung mit pH- und
  Leitwertbereichen.

### Die Rechtsangabe

Im Samen-Text steht auf Ansage: **bis zu drei Pflanzen je erwachsener Person
am eigenen Wohnsitz.** Die Zahl kommt aus dem Konsumcannabisgesetz und wurde
ausdruecklich freigegeben; ohne Freigabe waere sie nicht in den Text
gekommen.

Stand vor dem Eingriff: `kat_texte_1_vorher.json`.

---

## Stapel 2: die naechsten zwoelf (18.09.2026)

| ID | Kategorie | Produkte | vorher | jetzt |
|---|---|---|---|---|
| 6523 | Growzubehoer | 308 | 145 | 1.198 |
| 549 | Regular | 141 | 150 | 1.029 |
| 607 | Papers | 127 | 165 | 994 |
| 539 | Luefter & Filter | 118 | 167 | 1.089 |
| 3852 | Bongs | 106 | 222 | 1.156 |
| 608 | Filter | 106 | 267 | 915 |
| 4550 | Aktivkohlefilter | 91 | 270 | 1.147 |
| 6864 | Dabbing | 86 | 126 | 1.101 |
| 1401 | Bewaesserung | 83 | 153 | 1.204 |
| 4706 | Pre Rolled Papers | 80 | 167 | 882 |
| 844 | LED Growlampen | 75 | 160 | 1.120 |
| 531 | Growboxen | 71 | 174 | 1.261 |

### Zwei Stellen, an denen bewusst nicht geworben wird

- **Bongs:** „Gesuender wird das Rauchen dadurch nicht – es fuehlt sich nur
  milder an." Die Wasserfilterung kuehlt und faengt Partikel, mehr laesst
  sich nicht belegen.
- **Filtertips aus Karton:** „Gefiltert wird dabei nichts – er gibt nur
  Form." Der Unterschied zum Aktivkohlefilter steht damit im Text, statt
  beide als dasselbe zu verkaufen.

### Zahlen im Text, und woher sie kommen

- **Abluft:** „Als Faustregel wird das Volumen der Box etwa einmal pro Minute
  ausgetauscht" – bei 1 × 1 × 2 m also rund 120 m³/h. Als Faustregel
  gekennzeichnet, nicht als Herstellerangabe.
- **Aktivkohlefilter:** 6 mm passt zu King-Size-Papers und Cones, 5 bis 14 mm
  sind die gaengigen Durchmesser. Deckt sich mit den Unterkategorien.
- **Growboxen:** 60 × 60 fuer ein bis zwei Pflanzen, 80er und 100er als
  Hausgroessen, 120er fuer mehrere – plus der Hinweis, dass die Hoehe nach
  Lampe, Abstand und Topf knapper ausfaellt als die Aussenmasse vermuten
  lassen.
- **LED:** PPF gegen PPE erklaert, weil Watt allein nichts ueber das Licht an
  der Pflanze sagt.

Stand vor dem Eingriff: `kat_texte_2_vorher.json`.
