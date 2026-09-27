# -*- coding: utf-8 -*-
"""Kategoriebeschreibungen: das Format, das diese Installation zulaesst.

WordPress schickt Term-Beschreibungen durch `wp_filter_kses`, und zwar auf
jedem Weg - WooCommerce-REST, wp/v2 und das MCP-Werkzeug liefern alle
dasselbe Ergebnis. Erlaubt ist nur der kleine Kommentar-Tagsatz; `<p>`,
`<h2>` und `<ul>` fallen raus, ihr Inhalt bleibt stehen und klebt am
Nachbartext. Genau das ist beim ersten Versuch passiert.

Was durchkommt:

- **Leerzeilen.** WooCommerce laesst `wpautop` ueber die Beschreibung laufen,
  eine Leerzeile wird also zu einem `<p>`, ein einzelner Umbruch zu `<br />`.
- **`<strong>`, `<em>`, `<a>`** und der Rest des Kommentar-Tagsatzes.

Daraus ergibt sich das Format: Absaetze durch Leerzeilen trennen,
Zwischenueberschriften als `<strong>`-Zeile mit einfachem Umbruch davor.
"""
import re, html as _html

def aus_block(block_html):
    """<h2>…</h2><p>…</p> -> [(Ueberschrift, [Absatz, …]), …]"""
    teile = re.split(r'<h2[^>]*>(.*?)</h2>', block_html, flags=re.S)
    raus = []
    if teile[0].strip():
        raus.append((None, _absaetze(teile[0])))
    for i in range(1, len(teile), 2):
        raus.append((_klar(teile[i]), _absaetze(teile[i + 1] if i + 1 < len(teile) else '')))
    return raus

def _klar(s):
    return re.sub(r'\s+', ' ', re.sub(r'<[^>]+>', '', s or '')).strip()

def _absaetze(s):
    """Absaetze und Listenpunkte, jeder als eigene Zeile.

    Listen muessen einzeln heraus: `_klar()` ueber ein ganzes `<ul>` klebt
    sonst die Punkte aneinander ("entsorgt.Cartridges:").
    """
    aus = []
    for m in re.finditer(r'<p[^>]*>(.*?)</p>|<li[^>]*>(.*?)</li>', s or '', re.S):
        t = _klar(m.group(1) if m.group(1) is not None else m.group(2))
        if t: aus.append(t)
    if not aus:
        t = _klar(s)
        if t: aus.append(t)
    return aus

def bauen(einleitung, abschnitte):
    """Einleitung plus Abschnitte -> Klartext im zulaessigen Format."""
    bloecke = []
    e = _klar(einleitung)
    if e: bloecke.append(e)
    for titel, absaetze in abschnitte:
        if titel:
            bloecke.append(f'<strong>{titel}</strong>\n' + '\n'.join(absaetze))
        else:
            bloecke.extend(absaetze)
    return '\n\n'.join(b for b in bloecke if b.strip())

def entitaeten(s):
    """&ndash; und Co. ausschreiben - im Klartextfeld hilft die Entitaet nicht."""
    return _html.unescape(s)
