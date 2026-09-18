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
