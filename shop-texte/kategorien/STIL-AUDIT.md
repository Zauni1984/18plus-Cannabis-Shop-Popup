# Stil-Audit Kategorietexte

Stand: 2026-09-08. Geprueft wurden **alle** Produktkategorien beider Shops:
110 auf hanfjack.de, 132 auf hanfjack.com.

Geprueft wurde zweierlei: das Term-Meta `below_category_content` (Block unter
dem Archiv, einzeln per API abgefragt) und die Kategoriebeschreibung (offline
aus der WooCommerce-Liste).

Als **Alt-Stil** zaehlt ein Text mit mindestens einem dieser Merkmale, die alle
aus Copy-Paste aus einem Chat-Fenster stammen: `data-start`/`data-end`-Attribute,
`dir="auto"`, Emoji, Haekchen-Listen (\u2705 / \u2714), `<h3>`/`<h4>` statt `<h2>`,
CRLF-Umbrueche, Werbeschluss ("Jetzt entdecken", "Shoppe jetzt").

## Ergebnis `below_category_content`

| Shop | Kategorien | aktueller Stil | Alt-Stil | leer |
|---|---:|---:|---:|---:|
| hanfjack.de | 110 | 93 | 11 | 6 |
| hanfjack.com | 132 | 47 | 61 | 24 |

Von den 93 im aktuellen Stil auf .de hat diese Session 17 gesetzt, von den 47
auf .com 46 - der Altbestand auf .com war also praktisch vollstaendig im alten Stil.

### Alt-Stil auf hanfjack.de (11)

| Term | Kategorie | Produkte |
|---:|---|---:|
| 5818 | Bundles | 24 |
| 4147 | Hanftee | 7 |
| 4144 | Knabberhanf | 5 |
| 4148 | Rohkost | 4 |
| 5831 | Handschuhe | 3 |
| 4152 | Hanfsamen | 2 |
| 4150 | Hanföl | 2 |
| 4149 | Gewürze | 2 |
| 4146 | Getränke | 2 |
| 4151 | Mehl | 1 |
| 4153 | Süßigkeiten und Snacks | 0 |

Neun davon sind die Lebensmittel-Unterkategorien, dazu Bundles und Handschuhe.

### Alt-Stil auf hanfjack.com (61)

Betroffen sind hier auch die grossen Einstiegskategorien. Zusammen haengen
**5.575 Produktzuordnungen** an Kategorien mit Alt-Stil-Block.

| Term | Kategorie | Produkte |
|---:|---|---:|
| 91 | Samen | 1084 |
| 92 | Growbedarf | 955 |
| 89 | Headshop | 744 |
| 95 | Feminisiert | 685 |
| 106 | Dünger | 489 |
| 94 | Automatisch | 301 |
| 93 | Lüfter & Filter | 121 |
| 112 | Bongs | 107 |
| 104 | LED Growlampen | 89 |
| 131 | Aktivkohlefilter | 88 |
| 155 | Dabbing | 86 |
| 138 | Pre Rolled Papers | 82 |
| 133 | Extra Slim 6 mm | 74 |
| 125 | Zu- und Abluft | 64 |
| 107 | CBD | 52 |
| 153 | Growzubehör | 48 |
| 141 | Feuerzeuge & Zippo | 46 |
| 142 | Zippo | 43 |
| 154 | Vaporizer | 42 |
| 126 | Aktivkohlefilter | 41 |
| 130 | Erde & Substrate | 33 |
| 105 | Pflanzentöpfe | 33 |
| 111 | CBD Samen | 30 |
| 123 | Dünger Sets | 26 |
| 90 | Pflegeprodukte | 26 |
| 88 | Lebensmittel | 25 |
| 152 | Controller | 23 |
| 108 | CBD für Tiere | 13 |
| 140 | Glas-Tips | 11 |
| 129 | Blunts | 10 |
| 149 | Trimmer | 9 |
| 148 | Beheizung | 9 |
| 124 | Ventilatoren | 7 |
| 116 | Hanftee | 7 |
| 127 | Feuchtigkeitsregler | 6 |
| 128 | Terpene | 5 |
| 114 | Knabberhanf | 5 |
| 150 | Luftbefeuchter | 4 |
| 145 | Mystery Boxen | 4 |
| 139 | Mundstücke | 4 |
| 134 | Slim 7mm | 4 |
| 117 | Rohkost | 4 |
| 157 | CBD Vapes | 3 |
| 156 | Vapes | 3 |
| 151 | Luftentfeuchter | 3 |
| 146 | Schädlingsbekämpfung | 3 |
| 136 | Konisch/Kegelförmig | 3 |
| 132 | Super Slim 5mm | 3 |
| 144 | Bundles | 2 |
| 143 | Feuerzeuge | 2 |
| 137 | Big 14mm | 2 |
| 135 | Regular 8-9mm | 2 |
| 121 | Hanfsamen | 2 |
| 119 | Hanföl | 2 |
| 118 | Gewürze | 2 |
| 115 | Getränke | 2 |
| 120 | Mehl | 1 |
| 113 | THC Test | 1 |
| 122 | Süßigkeiten und Snacks | 0 |
| 109 | Hydroponik Systeme | 0 |
| 103 | Stecklinge | 0 |

Sonderfall: Term 89 (Zubehoer) enthaelt rohes DOM-Markup aus einem Chat-Fenster,
inklusive `class="markdown prose"` und `data-message-author-role="assistant"`.

### Leere Bloecke

Auf .de sind 6 leer, davon 3 mit Produkten: Dr. Grow Sets (1), Merch (36),
Angebote (208). Die uebrigen drei sind leere Systemkategorien
(Uncategorized, Produktarchiv, Schneidbretter).

Auf .com sind 24 leer, davon 22 mit Produkten:

| Term | Kategorie | Produkte |
|---:|---|---:|
| 8031 | Angebote | 190 |
| 101 | Papers | 112 |
| 102 | Filter | 103 |
| 4303 | Growboxen | 93 |
| 8215 | Bewässerung | 83 |
| 159 | Pipes | 71 |
| 110 | Rolling Trays | 66 |
| 8172 | Vermehrungsmaterial | 42 |
| 5897 | Merch | 40 |
| 98 | Aufbewahrung | 32 |
| 8632 | F1 Samen | 29 |
| 4351 | Komplettsets | 25 |
| 6072 | CBD Blüten | 22 |
| 15 | Uncategorized | 18 |
| 6020 | CBD Öl | 16 |
| 4723 | Erntescheren | 16 |
| 8007 | Kräutermühlen | 11 |
| 97 | Aschenbecher | 11 |
| 11301 | Anzucht | 10 |
| 158 | Waagen | 8 |
| 161 | Tabakersatz | 5 |
| 5150 | Handschuhe | 3 |

## Ergebnis Kategoriebeschreibungen

Deutlich besser: nur 1 Beschreibung auf .de im Alt-Stil (CBD Vapes, Term 6881)
und 6 auf .com (Stecklinge 103, Hydroponik Systeme 109, Vapes 156, Terpene 128,
CBD Vapes 157, Suessigkeiten und Snacks 122).

## Hinweis vor einer Umstellung

WordPress fuehrt fuer Term-Meta **keine Revisionen**. Ein Ueberschreiben von
`below_category_content` ist nicht rueckholbar. Vor einer Umstellung sollten die
bestehenden Texte daher zuerst ausgelesen und hier im Repository gesichert werden.

## Nebenbefund: doppelte Kategorien auf hanfjack.com

Acht Kategoriepaare tragen denselben Namen, wobei jeweils eine Variante
**null Produkte** hat (per Produktabfrage bestaetigt, nicht nur `count`):

| Kategorie | befuellt | leeres Duplikat |
|---|---|---|
| Beheizung | 148 (9 Produkte) | 9610 (0 Produkte) |
| CBD Vapes | 157 (3 Produkte) | 6205 (0 Produkte) |
| Controller | 152 (23 Produkte) | 6727 (0 Produkte) |
| Erntescheren | 4723 (16 Produkte) | 4734 (0 Produkte) |
| Komplettsets | 4351 (25 Produkte) | 6198 (0 Produkte) |
| Luftentfeuchter | 151 (3 Produkte) | 9687 (0 Produkte) |
| Pflanzentoepfe | 105 (33 Produkte) | 6147 (0 Produkte) |
| Trimmer | 149 (9 Produkte) | 6724 (0 Produkte) |

Die leeren Duplikate sind genau jene, die in dieser Session Bild, SEO und
Beschreibung bekommen haben - sie standen in der Luecken-Liste, weil sie leer
waren. Sie erzeugen als indexierbare, leere Archivseiten Thin Content.
Ob sie geloescht, zusammengefuehrt oder auf noindex gesetzt werden, ist eine
Entscheidung des Shopbetreibers und wurde nicht angefasst.

Nicht betroffen: Aktivkohlefilter 126 und 131 heissen zwar gleich, sind aber
zwei verschiedene Dinge (Abluftfilter vs. Filter-Tips) und beide befuellt.

