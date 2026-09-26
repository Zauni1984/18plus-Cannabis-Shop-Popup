# Grok-Texte ersetzen – hanfjack.de

**Stand:** 14.09.2026 · **Shop:** hanfjack.de

## Was „Grok-Texte" sind

Eine ältere Generation KI-generierter Produkttexte, per Copy-Paste in den Editor
gekippt. Sie tragen die Signatur `dir="auto"` (auch `dir="ltr"` / `dir="rtl"`) in
den `<p>`- und `<ul>`-Tags, in **Beschreibung und Kurzbeschreibung**. Sie werden
nicht repariert, sondern vollständig durch den Hausstil ersetzt
(siehe `PRODUKTTEXTE-KONVENTION.md`, Abschnitt „Hardware-Variante").

## Wie sie gefunden werden

Kostenfrei über die öffentliche Store-API, ohne API-Calls gegen den Shop:

```
https://hanfjack.de/wp-json/wc/store/v1/products?per_page=100&page=N
```

liefert `description` und `short_description` im Klartext. Erfasst werden dabei
**nur veröffentlichte, sichtbare Produkte** – private Produkte und Entwürfe
müssen separat über die API geprüft werden (steht noch aus).

**Ergebnis des Scans vom 14.09.2026: 864 betroffene Produkte im öffentlichen Katalog.**

## Erledigt: Norddampf (18 Produkte)

Beschreibung, Kurzbeschreibung und Yoast SEO neu geschrieben. Fakten aus den
Herstellerseiten auf norddampf.com (abgerufen 14.09.2026), **nicht** aus den
alten Shop-Texten – die waren teils falsch: die Meta-Description des Relict nannte
2300 mAh, der Hersteller nennt 2600 mAh.

| ID | Produkt | Beschreibung (Zeichen) | Kurzbeschreibung |
| --- | --- | ---: | ---: |
| 1693 | Relict Vaporizer | 2131 | 157 |
| 17777 | Hammah Vaporizer | 1631 | 154 |
| 17763 | Voity Vaporizer | 1578 | 178 |
| 17764 | Terp Pen | 1334 | 136 |
| 17773 | Terp Pen Atomizer (privat) | 812 | 125 |
| 17768 | DAB Pen Mini | 1274 | 127 |
| 19466 | DAB Pen Mini Atomizer | 738 | 128 |
| 17772 | Hot Knife | 809 | 122 |
| 18663 | Hammah Bubbler | 1032 | 93 |
| 1702 | Relict Bubbler | 1051 | 141 |
| 1706 | Relict Bong Adapter | 852 | 112 |
| 18658 | Relict Dosierkapseln | 850 | 128 |
| 23424 | Relict Dosierkapseln mit Tropfkissen | 930 | 133 |
| 17802 | Relict Capsule Caddy | 607 | 118 |
| 28666 | Nordy Flow Glasmundstück Weiß | 1081 | 138 |
| 28664 | Nordy Flow Glasmundstück Rosa | 1075 | 138 |
| 28662 | Nordy Flow Glasmundstück Grün | 1051 | 138 |
| 28658 | Nordy Flow Glasmundstück Seegrün | 1070 | 141 |

Gegenprüfung nach dem Schreiben: alle 17 öffentlichen Norddampf-Produkte über die
Store-API erneut geladen, **0 Treffer** auf `dir=`. Der Terp Pen Atomizer (17773)
ist privat und wurde direkt in der Schreib-Antwort geprüft.

Wo Norddampf keine Werte nennt, steht das namentlich im Text statt einer Schätzung –
beim DAB Pen Mini etwa zu Akkukapazität, Temperaturstufen und Maßen.

## Offen: 847 Produkte

Verteilung nach Kategorie (Mehrfachzuordnung möglich, ab 5 Produkten):

| Kategorie | Produkte |
| --- | ---: |
| Feminisiert | 164 |
| Angebote | 156 |
| Automatisch | 88 |
| LED Growlampen | 50 |
| Zippo | 43 |
| Dabbing | 40 |
| Growboxen | 38 |
| Rolling Trays | 36 |
| Dünger | 36 |
| Pflegeprodukte | 28 |
| Papers | 24 |
| Growzubehör | 21 |
| Komplettsets | 18 |
| CBD Blüten | 16 |
| CBD Öl | 16 |
| F1 Samen | 15 |
| Pre Rolled Papers | 15 |
| Lebensmittel | 15 |
| Erde & Substrate | 14 |
| Aufbewahrung | 13 |
| Pflanzentöpfe | 11 |
| Regular | 11 |
| Blunts | 10 |
| CBD für Tiere | 9 |
| Vaporizer | 8 |
| Zu- und Abluft | 8 |
| Bundles | 7 |
| Bewässerung | 7 |
| Anzucht | 7 |
| Hanftee | 7 |
| Controller | 6 |
| Feuchtigkeitsregler | 6 |
| CBD Samen | 5 |
| Headshop | 5 |
| Tabakersatz | 5 |
| Kräutermühlen | 5 |
| Beheizung | 5 |
| Terpene | 5 |
| Knabberhanf | 5 |

Dazu kommen kleinere Kategorien mit weniger als 5 betroffenen Produkten.

Die drei großen Blöcke Feminisiert (164), Automatisch (88) und Regular (11) sind
Saatgut – dort gilt das Saatgut-Gerüst mit Rechtshinweis, nicht die Hardware-Variante.
„Angebote" (156) ist eine Querschnittskategorie und überschneidet sich mit den anderen.

## Offene Punkte

- Private Produkte und Entwürfe auf Grok-Texte prüfen (Store-API zeigt sie nicht).
- Gleicher Scan für hanfjack.com und moinkiffers.de.
