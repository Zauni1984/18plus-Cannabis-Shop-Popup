# HTML-Hygiene der Produkttexte

## Anlass

Gemeldet wurde: "Da sind `<br>` anscheinend nicht ausreichend gesetzt."

Das stimmte nicht. Eine Pruefung aller 4441 Beschreibungen fand **kein
einziges** Produkt mit fehlendem Umbruch – kein Text ausserhalb eines
Block-Elements, kein `<br>`-Stapel als Absatzersatz. Das Markup des
gemeldeten Produkts (Dimlux Xtreme 800 W, ID 38495) war sauber: `<p>`,
`<h3>`, `<ul>`, alles korrekt geschlossen.

Der sichtbare Fehler hatte zwei andere Ursachen.

### Ursache 1: Abstand kommt vom Theme, nicht vom Text

OceanWP setzt:

```css
h1,h2,h3,h4,h5,h6 { margin: 0 0 20px }   /* oben: 0 */
ul,ol             { margin: 15px 0 15px 20px }
p                 { margin: 0 0 20px }
```

Eine Ueberschrift hat also **oben gar keinen Abstand**. Nach einer Liste
bleiben 15 px, nach einem Absatz 20 px, unter der Ueberschrift dagegen
20 px. Die Ueberschrift klebt dadurch an dem, was ueber ihr steht, und
wirkt wie ein Teil davon – genau der gemeldete Eindruck.

Das ist **kein Produktfehler**: es betrifft alle 4441 Produkte gleich und
laesst sich im Text nicht beheben, solange man auf Theme-CSS setzt.

**Warum es trotzdem im Text steht:** die Texte werden auch in anderen
Shops eingesetzt, in denen kein Zugriff auf das Theme-CSS besteht. Der
Abstand reist deshalb als Inline-Angabe mit:

```html
<h3 style="margin-top:1.8em">Technische Daten</h3>
```

Die erste Ueberschrift eines Textes bekommt keinen Abstand – ueber ihr
steht nichts. `1.8em` ist relativ zur Schriftgroesse und wirkt damit in
jedem Theme gleich, unabhaengig von dessen Grundwerten.

### Ursache 2: drei Textvorlagen nebeneinander

| Ueberschriften-Ebenen | Produkte |
|---|---|
| nur h3 | 3312 |
| h1 + h3 | 665 |
| nur h2 | 316 |
| h3 + h4 | 77 |
| sonstige | 34 |
| keine Ueberschrift | 37 |

Derselbe Abschnitt rendert damit je nach Produkt in drei verschiedenen
Groessen: h1 ist gut doppelt so gross wie h3.

Besonders auffaellig waren 664 Produkte, die **den Produktnamen als `<h1>`
wiederholen** – direkt ueber dem Einleitungstext, obwohl der Shop den
Titel bereits darueber anzeigt. Zwei `<h1>` pro Seite sind ausserdem ein
SEO-Fehler.

## Was der Lauf geaendert hat

Alles rein strukturell. **Kein Wort am Text wurde veraendert** – geprueft
durch Vergleich des reinen Fliesstextes vor und nach dem Lauf, ueber alle
4441 Produkte.

| Aenderung | Produkte |
|---|---|
| Abstand ueber Ueberschriften als Inline-Angabe | 4400 |
| Muell-Attribute entfernt (44 151 Stueck) | 513 |
| `dir="auto"` entfernt (3132 Stueck) | 178 |
| Ueberschriften auf h3 vereinheitlicht | ~400 |
| doppelter Produktname als `<h1>` entfernt | 664 |
| leere Absaetze entfernt | 53 |
| offenes `<p>` am Textende geschlossen | 41 |
| Gutenberg-Kommentare entfernt | 4 |
| rohes LaTeX in lesbaren Text gewandelt | 4 |
| `<span style="font-weight:bold">` zu `<strong>` | 2 |

Das HTML ist dadurch von 9,16 MB auf 8,21 MB geschrumpft (10 %).

### Die Muell-Attribute

513 Produkte trugen 31 280 `data-path-to-node`- und 12 871
`data-index-in-node`-Attribute aus einem Export-Werkzeug. Sie bewirken
nichts und blaehen jede Seite auf. Mit ihnen fielen die Spans weg, die
nur diese Attribute trugen.

### Das LaTeX

Vier Produkte zeigten dem Kunden rohe Formelsyntax:

| Produkt | vorher | nachher |
|---|---|---|
| HY-PRO Rootstimulator Terra | `$K_{2}O$` | `K₂O` |
| HY-PRO Generator 100ml | `$SiO_2$` | `SiO₂` |
| AC Infinity IONFRAME EVO4/EVO8 | `$3,14 \mu mol/J$` | `3,14 µmol/J` |

## Dateien

- `html_fix.py` – die Umbauten. Jede Funktion behandelt genau einen Fehler.
- `pruef_html.py` – die Pruefung. Laeuft gegen den JSON-Abzug und meldet,
  was noch offen ist. Nach dem Lauf meldet sie nur noch die beabsichtigte
  Inline-Angabe.
- `schreib_html.py` – schreibt zurueck, ueber den Batch-Endpunkt in
  Bloecken von 20. `alle 2 0` und `alle 2 1` teilen die Arbeit auf zwei
  Laeufe; mehr als zwei gleichzeitig hat der Shop frueher mit einer
  IP-Sperre beantwortet.

**`aufraeumen()` ist wiederholbar.** Ein zweiter Lauf ueber bereits
bereinigtes HTML aendert nichts mehr – geprueft ueber alle 4441 Produkte.
Das war anfangs nicht so: verschachtelte `<span><span>` loesten sich pro
Durchlauf nur eine Ebene weit auf, und die Klassen wurden erst nach dem
Entpacken entfernt, sodass die Spans beim ersten Lauf noch bekleidet
dastanden. Beides ist behoben; die Reihenfolge in `_muell_attribute()`
ist deshalb nicht beliebig.

## Was bewusst offen bleibt

**357 Produkte nutzen `<p><strong>Text</strong></p>` als Ueberschrift**
statt `<h3>`. Das rendert in normaler Textgroesse und faellt neben echten
Ueberschriften auf. Eine automatische Umwandlung waere aber riskant: in
derselben Form stehen dort auch Aufforderungen wie *"Qnubu Zip-Beutel
jetzt bei Hanfjack bestellen!"* und wiederholte Produktnamen, die keine
Ueberschriften sind. 276 verschiedene Texte, die meisten kommen genau
einmal vor. Das braucht eine Entscheidung pro Fall.

Eindeutig ist nur eine Gruppe: *"Herstellerangaben gemaess GPSR"*, 15 Mal,
immer als Abschnittstitel.

**147 Produkte bauen Kennwert-Bloecke mit `<br>`** statt mit einer Liste:

```html
<p><strong>Indoor:</strong><br />Ertrag ...<br />Hoehe ...</p>
```

Das rendert korrekt, ist aber eine vierte Vorlage neben den drei oben.

**2 Produkte haben gar keine Beschreibung**: Propagator Set S (15365) und
Propagator Set XL (15371). Dafuer braucht es Text, keinen Umbau.

## Regel fuer neue Texte

- Abschnittstitel als `<h3 style="margin-top:1.8em">`, die erste ohne
  `style`
- Aufzaehlungen als `<ul><li>`, nicht als `<br>`-Kette
- kein `<h1>` im Beschreibungstext – der Produkttitel ist bereits eins
- keine Attribute aus Chat- oder Editor-Exporten uebernehmen
