# Samen Automatisch (Seite 1+2) – Yoast-Meta-Description-Nachsweep

Nachsweep zu `shop-texte/samen-automatisch-1/README.md`: dort wurde der
Yoast-`meta_description`-Sweep nach 62 von 200 individuell verifizierten
Produkten (100 % Trefferquote) bewusst als Stichprobe abgeschlossen. Dieser
Nachsweep hat **alle 200 Produkte** der WooCommerce-Kategorie **Automatisch**
(ID 547, Samen-Baum, Cannabis-Vermehrungsmaterial nach § 1 Nr. 8c KCanG,
Seite 1+2, je 100, aufsteigend nach ID) einzeln über
`wp_yoast_get_post_seo` gelesen und, wo nötig, per `wp_yoast_update_post_seo`
gefixt. Ausdrücklich **nicht** Teil dieses Sweeps: H1-Tags und CTA-Phrasen
im Fließtext/in der Kurzbeschreibung — das ist bereits zu 100 % erledigt
(siehe `shop-texte/samen-automatisch-1/README.md`). Kein Produktname enthielt
"HEMPER", "Goody Glass" oder "Smoke Friends" — der Ausschluss griff nicht.

## Ergebnis

| Kennzahl | Wert |
|---|---|
| Geprüfte Produkte | 200 / 200 |
| Gefixt (CTA entfernt oder Description neu erstellt) | 128 |
| Bereits sauber (kein Fix nötig) | 72 |

Das bestätigt endgültig das bereits in `samen-automatisch-1/README.md`
dokumentierte Muster: **jede** `meta_description` mit einer
Hanfjack-Marken-CTA-Phrase ("Jetzt bei Hanfjack (online) kaufen/bestellen/
sichern!") wurde gefixt. Von den 200 Produkten waren 62 bereits durch den
vorherigen Sweep korrigiert; im Rahmen dieses Nachsweeps wurden **128**
weitere/verbliebene Treffer individuell gefixt.

## Was als Verstoß galt

- `meta_description` endet auf eine Hanfjack-Marken-CTA-Phrase, z. B.
  "Jetzt bei Hanfjack online bestellen!", "Jetzt bei Hanfjack sichern!".
- `meta_description` ist komplett leer (kein `description`-Feld in
  `yoast_head_json`).

## Was ausdrücklich kein Verstoß war (unangetastet gelassen)

- Generische Kauf-Phrasen **ohne** den Markennamen "Hanfjack", z. B.
  "Feminisierte Autoflower-Samen kaufen." / "… online kaufen." — durchgängig
  bei einem Teil des Exotic-Seeds-Blocks (u. a. Z&Z Auto 32941, Herz OG Auto
  32949, Toof Decay Auto 32973, Strawberry Nuggets Auto 32975, Samsquanch OG
  Auto 32977).
- Die Hanfjack-Seeds-Eigenmarke-Produkte (Aurora Auto 31172, Guatlz Auto
  31181, Katzenminze Auto 31184) enthalten nur den sachlichen
  Charity-Spenden-Hinweis ("5 € Spende für den Kinderschutz pro Packung")
  ohne Kaufaufforderung — kein CTA-Verstoß, unverändert gelassen.
- Genetik-/THC-/Terpen-/Ertrags-/Blütezeit-Fachsprache — normaler, legaler
  Content bei Cannabis-Samen, nicht angefasst.

## Fix-Methodik

Zwei Fixmuster, je nach Ausgangslage:

1. **CTA-Halbsatz entfernt**: Der Rest der faktenbasierten Beschreibung
   (Genetik/Kreuzung, THC-%, Aroma, Blütezeit/Ertrag) blieb unverändert
   erhalten, nur der abschließende Kaufsatz wurde gestrichen. Wo der
   verbleibende Text unter 120 Zeichen fiel, wurde ein kurzer,
   sachlicher Zusatzsatz ergänzt (z. B. "Kompakter Wuchs, ideal für drinnen
   und draußen." / "Robuste Autoflower-Genetik."), um im
   Ziel-Zeichenbereich 120–160 zu bleiben.
2. **Komplett neu erstellt** (2 Fälle mit leerem Feld: Sensi Seeds Hindu
   Kush Automatic 13282, Sensi Seeds Sauvignon Blanc Automatic 13286):
   neue, produktspezifische Beschreibung (120–160 Zeichen) mit Fokus auf
   Genetik/THC/Aroma/Ertrag statt Kaufaufforderung.

Alle 128 Fixes lagen nach Anwendung im Zielbereich 120–160 Zeichen
(Python-`len()`-Verifikation vor jedem Schreibvorgang).

## Betroffene Marken-/Content-Blöcke (Auswahl)

Das CTA-Muster zog sich über praktisch jeden Marken-Block der Kategorie:
Legacy-Klassiker, Fast Buds, Barneys Farm (kompletter 8er-Block: Afghan
Hash Plant, Bruce Banner, Bubblegum, Cherry Cola, Lemon Cherry Cookies,
London Pound Cake, Trainwreck, Tropicana Cookies Auto), Buddha Seeds,
Humboldt Seed Co., Paradise Seeds, RQS x Tyson, Mosca Seeds (Strawberry
Guava Auto 30310), Sweet Seeds (Studio 54 Stardust Auto 30311), ein Teil
des Mephisto-Genetics-Blocks (Orange Diesel, Fantasmo Express, Alien Vs
Triangle, Canna-Cheese CBD 1:1, 3 Bears OG, 24 Carat Auto), The Bulldog
Seeds (Skittlez Auto 33193, Gelato Auto 33194 — beide Status `private`,
dennoch geprüft und gefixt), der komplette Green House Seed Co.
Neuauflage-Block (Sweet Mango, White Widow, Biscotti, Northern Lights,
West Coast OG X Gelato #41, King's Kush Auto) und Royal Queen Seeds
(Sticky Queen Auto 33294).

## Verifikation

- Jeder `wp_yoast_update_post_seo`-Aufruf lieferte
  `updated_fields: ["meta_description"]` als Bestätigung des Schreibvorgangs.
- Ausschließlich `wp_yoast_get_post_seo` (Lesen) und
  `wp_yoast_update_post_seo` (Schreiben) verwendet — nie
  `wp_wc_update_product` oder `wp_wc_batch_update_products`, daher kein
  Risiko eines `_min_age`-Resets über den WooCommerce-REST-Write-Pfad.
- Status (private/publish) wurde nicht verändert, auch wenn inhaltlich
  gefixt wurde.
