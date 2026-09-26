# Yoast SEO Premium und WooCommerce SEO: Produktdaten uebergeben

Stand 26.09.2026. Yoast SEO 28.5, Yoast SEO Premium 28.5, Yoast SEO WooCommerce
16.9. Die Schema-Ausgabe von Yoast fuer Produkte und die Marken-Taxonomie
(`pwb-brand`) sind in den Yoast-Einstellungen aktiviert.

## Was geschrieben wurde

`yoast_produktdaten.py` rechnet aus einem Abzug aller Produkte einen Auftrag und
schreibt ihn in Stapeln:

| | Produkte |
|---|---|
| geprueft | 4.467 |
| geschrieben | **4.312** |
| davon Identifier (`wpseo_global_identifier_values`) | 2.279 |
| davon Primaerkategorie (`_yoast_wpseo_primary_product_cat`) | 4.138 |
| nichts zu tun | 155 |

Stichprobe von 20 Produkten nach dem Lauf: keine Abweichung.

**Identifier** kommen aus dem Bestand: GTIN aus `global_unique_id` (das Feld,
das Germanized und WooCommerce fuehren), MPN aus `_ts_mpn`. Die GTIN wird nach
Laenge einsortiert (8, 12, 13 oder 14 Stellen).

**Primaerkategorie** ist die tiefste, spezifischste Kategorie des Produkts.
Querschnittsbegriffe wie „Angebote", „Produktarchiv" oder „Uncategorized"
zaehlen nicht mit – sonst landet ein Samen im Breadcrumb unter „Angebote".

## Was davon sichtbar ist

Vorher trug das Product-Schema weder Marke noch GTIN, und der Breadcrumb griff
sich irgendeine Kategorie. Jetzt:

    Startseite > Shop > Growshop > LED Growlampen > Spider Farmer SF-1000 …
    Product: name, sku, brand (Spider Farmer), gtin13, offers, image

## Grenze: MPN

Die MPN steht im Feld, wird von dieser Yoast-Version aber **nicht ins Schema
uebernommen**; ausgegeben wird nur die GTIN, und die zieht Yoast aus
`global_unique_id`. Geschrieben schadet die MPN nicht (Yoast fuellt dasselbe
Feld ueber seine Oberflaeche), Nutzen bringt sie erst im Merchant-Center-Feed.
AdTribes hat dafuer `_woosea_mpn` und `_woosea_gtin`, beide sind noch leer.

## Nebenbefund

Bei den beiden Black-Leaf-Pollenpressen (45614, 45616) waren `_ts_gtin` und
`_ts_mpn` leer – beim Anlegen am 22.09. sind die Werte aus dem `meta_data` des
POST nicht durchgekommen. Beim Palacio-Gel war derselbe Effekt aufgefallen und
per zweitem PUT geheilt worden, bei den Pressen nicht. Jetzt nachgetragen:
4251403331994 / 500203-46 und 4251403331291 / 500205-45.

**Lehre:** Beim Anlegen gehen `_ts_gtin` und `_ts_mpn` im selben Aufruf
verloren. Sie brauchen einen eigenen PUT nach dem Anlegen – und eine Kontrolle.

## Synonyme und weitere Keyphrasen (26.09.2026)

Yoast Premium bringt zwei zusaetzliche Felder je Beitrag:

    _yoast_wpseo_keywordsynonyms   JSON-Array mit einer kommagetrennten Zeichenkette
    _yoast_wpseo_focuskeywords     JSON-Array aus {"keyword": ..., "score": 0}

`keyphrases.py` fuellt beide fuer den ganzen Katalog, gebaut aus Produktname,
Marke und Kategorie – nichts erfunden.

| | Produkte |
|---|---|
| geschrieben | **4.334** |
| Fokus-Keyphrase ersetzt | 1.342 |
| Synonyme gesetzt | 4.042 |
| weitere Keyphrasen | 3.821 |
| nichts zu tun | 133 |

Stichprobe von 20 nach dem Lauf: keine Abweichung.

### Die Regeln

**Synonym 1** ist dieselbe Sache mit bzw. ohne Markennamen – der Fall
„Barneys Farm Runtz Auto" neben „Runtz Auto". **Synonym 2** ist die
gebraeuchliche andere Bezeichnung der Warengruppe: Bong ↔ Wasserpfeife,
Grinder ↔ Kraeutermuehle, Growlampe ↔ Pflanzenlampe, Duenger ↔
Pflanzennahrung. **Weitere Keyphrasen** decken Kaufabsicht („… kaufen") und
Warengruppe ab. Hoechstens zwei je Feld – mehr verwaessert die Analyse.

Ersetzt wurde eine Fokus-Keyphrase nur, wenn sie keine war: leer (308),
sieben Woerter und mehr (562) oder mit Prozent-, THC- und Massangaben. Aus
„Legendaere Sweedbar Northern Lights mit 19 % THC und wuerzigem
Kiefer-Zitronen-Aroma" wird so eine knappe Bezeichnung.

### Was das bringt – und was nicht

Diese drei Felder stehen **nicht im Quelltext**. Sie steuern allein Yoasts
Analyse im Editor. Der Gewinn ist redaktionell: man sieht je Produkt, ob Text,
Titel und Meta die Nebenbegriffe abdecken. Rankings bewegen sich erst durch
die Texte, die man daraufhin anfasst.

### Grenze

Beim Kuerzen auf fuenf Woerter entstehen vereinzelt harte Schnitte („Spider
Farmer GlowR80 2x tief"). Einzelne Faelle lassen sich im Editor in Sekunden
nachziehen; ein Skriptlauf dafuer lohnt nicht.
