# Nachzug: Produkte ausserhalb `status=publish`

## Warum es diesen Lauf gibt

Alle vorherigen Attribut- und Tag-Laeufe haben die Produktliste ueber
`/wc/v3/products?status=publish` geholt. Das ist der REST-Standard und faellt
nicht auf, solange man nur die Trefferzahl vergleicht. Entwuerfe und private
Produkte waren damit aber nie im Blick.

Der Katalog hat 4441 Produkte:

| Status  | Produkte |
|---------|----------|
| publish | 3976 |
| private | 352 |
| draft   | 113 |

**Regel fuer kuenftige Laeufe:** immer `status=any` abfragen und die drei
Status getrennt auswerten. Ein Produkt wechselt von privat auf oeffentlich,
ohne dass jemand die Attribute nachtraegt.

## Was der Lauf gemacht hat

Ausgangslage: 410 Produkte ohne Attribut, 104 ohne Tags.

1. Zwei neue Attribute angelegt:
   - `pa_durchsatz` (#76, "Durchsatz") – Maschinendurchsatz in kg/h
   - `pa_passend-fuer` (#77, "Passend fuer") – Geraetekompatibilitaet
2. `gb_attr.py` erweitert (siehe `../growbedarf/`):
   - `Glas\w*` als Material, `my` und `mesh` zusaetzlich zu `µm`
   - generische Mengenangabe fuer `pa_inhalt`, aber **nur aus dem
     Produktnamen** – im Fliesstext ist eine Grammangabe meist ein
     Versandgewicht (HEMPER bekam so `pa_inhalt: 238 g`)
   - `4g` wird zu `4 g` normalisiert
3. 222 Attribute geschrieben (zwei Schards a 111)
4. 92 Tags fuer bis dahin ungetaggte Produkte
5. 81 Werte "Passend fuer" aus `passend.py`

## Ergebnis

| Status  | mit Attribut | Quote | mit Tags |
|---------|--------------|-------|----------|
| publish | 3893 | 97 % | 3967 |
| private |  340 | 96 % |  348 |
| draft   |  101 | 89 % |  112 |

Offen: 107 ohne Attribut, 14 ohne Tags.

## `pa_durchsatz` ist leer geblieben

Das Attribut wurde angelegt, hat aber **keinen einzigen Wert gefunden**. Keine
Trimmer-Beschreibung im Katalog nennt einen Durchsatz in kg/h. Das Attribut
bleibt vorhanden, damit die Angabe einen Platz hat, sobald sie aus
Herstellerunterlagen kommt – befuellt ist es nicht.

## Der Rest (107 Produkte)

`ohne_attribut_rest.json` listet sie vollstaendig. Es sind ganz ueberwiegend
Ersatz- und Zubehoerteile: "Schraube fuer Air-Pot", "CenturionPro Reel
Bearings", "Pyramiden-Sack, klein, Ersatzteil fuer Bubble Machine",
"Trimpro Ersatzteil, Klinge und Nabe". Solche Positionen haben keine eigenen
Messwerte – ihre einzige sinnvolle Eigenschaft ist "passend fuer", und die ist
gesetzt, wo der Name das Geraet nennt. Hier ist nichts mehr automatisch zu
holen; was fehlt, muesste aus Herstellerunterlagen kommen.

Verteilung nach Marke: Bloomtech 9, ohne Marke 7, Spider Farmer 7, Trimpro 6,
AutoPot 6, GIB 5, Palacio 5, Grodan 4, Rest einzeln.

## `passend.py`

Liest die Geraetekompatibilitaet **nur aus dem Produktnamen**, nie aus dem
Fliesstext: im Text wird ein Geraetename oft nur als Vergleich genannt. Die
Modellliste ist fest, weil eine freie Erkennung bei Namen wie "Master Trimmer
Ersatztrommel fuer MT 500" sonst die Masseinheit als Modell nimmt.
