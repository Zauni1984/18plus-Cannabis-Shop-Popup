# -*- coding: utf-8 -*-
"""Raeumt Produktbeschreibungen strukturell auf. Aendert nie den Wortlaut."""
import re

ABSTAND = 'margin-top:1.8em'          # Luft ueber jeder Abschnitts-Ueberschrift

def _entferne_leading_h1(h):
    """Der Produktname als <h1> ganz oben ist eine Dublette: der Shop zeigt
    den Titel bereits darueber an, und zwei <h1> pro Seite sind ein Fehler."""
    return re.sub(r'^\s*<h1[^>]*>.*?</h1>\s*', '', h, count=1, flags=re.S|re.I)

def _vereinheitliche_ebenen(h):
    """h1, h2 und h4 werden zu h3 - dem Hausstandard von 3312 Produkten."""
    h = re.sub(r'<h[124]([^>]*)>', r'<h3\1>', h, flags=re.I)
    return re.sub(r'</h[124]>', '</h3>', h, flags=re.I)

def _gutenberg_weg(h):
    """<!-- wp:list --> und Co. hat wpautop in <p> gewickelt. Das ergibt
    leere Absaetze und verwaiste </p> mitten im Text."""
    h = re.sub(r'<p[^>]*>\s*<!--.*?-->\s*</p>', '', h, flags=re.S)
    h = re.sub(r'<!--.*?-->', '', h, flags=re.S)
    return h

MUELL_ATTR = ('data-path-to-node', 'data-index-in-node', 'data-start',
              'data-end', 'data-math', 'dir')

def _muell_attribute(h):
    """44 000 data-path-to-node- und data-index-in-node-Attribute stammen aus
    einem Export-Werkzeug und blaehen das HTML auf, ohne etwas zu bewirken.
    dir="auto" ist bei deutschem Text ebenfalls ohne Funktion. Spans und
    Bold-Tags, die danach leer dastehen, fallen mit weg."""
    for a in MUELL_ATTR:
        h = re.sub(r'\s+%s="[^"]*"' % a, '', h)
    # Klassen aus Chat- und Editor-Exporten zuerst - erst danach stehen die
    # Spans nackt da und lassen sich entpacken. wp-block-* und
    # has-fixed-layout bleiben, weil sie die Tabellendarstellung tragen.
    h = re.sub(r'\s+class="(?:|animating|whitespace-normal|math-inline|'
               r'[^"]*hover:[^"]*)"', '', h)
    h = re.sub(r'<(\w+)\s+>', r'<\1>', h)
    for _ in range(8):                       # <span><span>...</span></span>
        n = re.sub(r'<span>(.*?)</span>', r'\1', h, flags=re.S)
        if n == h: break
        h = n
    return h

TIEF = str.maketrans('0123456789', '\u2080\u2081\u2082\u2083\u2084\u2085\u2086\u2087\u2088\u2089')

def _latex_aufloesen(h):
    """Vier Produkte zeigen dem Kunden rohes LaTeX: $K_{2}O$, $SiO_2$,
    $3,14 \\mu mol/J$. Das wird zu lesbarem Text."""
    def um(m):
        t = m.group(1)
        t = t.replace('\\mu ', '\u00b5').replace('\\mu', '\u00b5')
        t = re.sub(r'_\{(\d+)\}', lambda x: x.group(1).translate(TIEF), t)
        t = re.sub(r'_(\d)',       lambda x: x.group(1).translate(TIEF), t)
        t = re.sub(r'\^\{?(\d+)\}?', lambda x: x.group(1), t)
        return t.replace('\\,', ' ').strip()
    h = re.sub(r'\$([^$<>]{1,40}?)\$', um, h)
    h = re.sub(r'<span[^>]*class="math-inline"[^>]*>(.*?)</span>', r'\1', h, flags=re.S)
    return h

def _leere_bloecke(h):
    for _ in range(3):
        vorher = h
        h = re.sub(r'<p[^>]*>(?:\s|&nbsp;|<br\s*/?>)*</p>', '', h, flags=re.I)
        h = re.sub(r'<(h[1-6])[^>]*>(?:\s|&nbsp;|<br\s*/?>)*</\1>', '', h, flags=re.I)
        h = re.sub(r'<li>(?:\s|&nbsp;)*</li>', '', h, flags=re.I)
        # WordPress raeumt beim Speichern Attribute aus <object>; was
        # zurueckbleibt, ist eine leere Huelle ohne Wirkung.
        h = re.sub(r'<object[^>]*>\s*</object>', '', h, flags=re.I)
        if h == vorher: break
    return h

def _offenes_p_am_ende(h):
    """Viele Texte enden auf ein <p dir="auto">, das nie geschlossen wird."""
    return re.sub(r'<p[^>]*>\s*$', '', h.rstrip()) if re.search(r'<p[^>]*>\s*$', h.rstrip()) else h

def _verwaiste_endtags(h):
    """Entfernt </p>, zu denen es kein oeffnendes Tag mehr gibt."""
    teile, tiefe, raus = re.split(r'(</?p\b[^>]*>)', h), 0, []
    for t in teile:
        if re.fullmatch(r'<p\b[^>]*>', t or ''): tiefe += 1
        elif re.fullmatch(r'</p>', t or ''):
            if tiefe == 0: continue
            tiefe -= 1
        raus.append(t)
    return ''.join(raus)

def _abstand_ueber_ueberschriften(h):
    """Der eigentliche Punkt: ohne Theme-CSS gibt kein Shop einer Ueberschrift
    Luft nach oben. Die Angabe reist deshalb im Text mit. Die erste
    Ueberschrift bekommt keinen Abstand - darueber steht nichts."""
    h = re.sub(r'(<h[1-6][^>]*?)\s*style="[^"]*"', r'\1', h, flags=re.I)  # alte Angabe raus
    erste = [True]
    def setz(m):
        tag, rest = m.group(1), m.group(2)
        if erste[0]:
            erste[0] = False
            return f'<{tag}{rest}>'
        return f'<{tag}{rest} style="{ABSTAND}">'
    # nur Ueberschriften, denen Inhalt vorausgeht
    vor_erster = re.match(r'\s*<h[1-6]\b', h, re.I)
    if not vor_erster: erste[0] = False
    return re.sub(r'<(h[1-6])([^>]*)>', lambda m: setz(m), h, flags=re.I)

LEERTAG = {'br','img','hr','input','meta','link','source','wbr','col','area','embed'}
BLOCKSTART = {'p','h1','h2','h3','h4','h5','h6','ul','ol','table','div',
              'blockquote','pre','figure','hr','dl','section'}

def _baum_reparieren(h):
    """Schliesst, was offen geblieben ist. Browser tun das ohnehin still -
    hier steht es danach im Text, damit jeder Shop gleich rendert.
    Ein <p>, das nur ein Block-Element einleitet, war nie ein Absatz und
    faellt weg statt geschlossen zu werden."""
    teile = re.split(r'(<[^>]+>)', h)
    stapel, raus = [], []
    for t in teile:
        m = re.fullmatch(r'<\s*(/?)\s*([a-zA-Z][\w:-]*)([^>]*)>', t or '')
        if not m:
            raus.append(t); continue
        schliessend, tag = bool(m.group(1)), m.group(2).lower()
        if tag in LEERTAG:
            raus.append(t); continue
        if not schliessend:
            # <p> wird von jedem folgenden Block-Element beendet
            if tag in BLOCKSTART and 'p' in stapel:
                while stapel and stapel[-1] != 'p':
                    raus.append('</%s>' % stapel.pop())
                if stapel:
                    stapel.pop()
                    # war der Absatz leer, verschwindet er ganz
                    j = len(raus) - 1
                    inhalt = ''
                    while j >= 0 and not re.fullmatch(r'<p\b[^>]*>', raus[j] or ''):
                        inhalt += raus[j] or ''; j -= 1
                    if j >= 0 and not re.sub(r'\s|&nbsp;', '', inhalt):
                        del raus[j:]
                    else:
                        raus.append('</p>')
            if tag == 'li' and stapel and stapel[-1] == 'li':
                stapel.pop(); raus.append('</li>')
            stapel.append(tag); raus.append(t)
        else:
            if tag not in stapel:
                continue                      # verwaistes Endtag faellt weg
            while stapel:
                o = stapel.pop()
                raus.append(t if o == tag else '</%s>' % o)
                if o == tag: break
    while stapel:
        raus.append('</%s>' % stapel.pop())
    return ''.join(raus)

def _weissraum(h):
    h = re.sub(r'\n{3,}', '\n\n', h)
    h = re.sub(r'>\s*\n\s*<', '>\n<', h)
    return h.strip() + '\n'

def aufraeumen(h):
    if not h or not h.strip(): return h
    h = _gutenberg_weg(h)
    h = _entferne_leading_h1(h)
    h = _latex_aufloesen(h)
    h = _muell_attribute(h)
    h = _offenes_p_am_ende(h)
    h = _verwaiste_endtags(h)
    h = _leere_bloecke(h)
    h = _vereinheitliche_ebenen(h)
    h = _baum_reparieren(h)
    h = _leere_bloecke(h)
    h = _abstand_ueber_ueberschriften(h)
    return _weissraum(h)

def _bold_span(h):
    """<span style="...font-weight: bold..."> ist ein <strong> mit Umweg."""
    h = re.sub(r'<span[^>]*font-weight:\s*(?:bold|[6-9]00)[^>]*>(.*?)</span>',
               r'<strong>\1</strong>', h, flags=re.S|re.I)
    return re.sub(r'<span[^>]*style="[^"]*"[^>]*>(.*?)</span>', r'\1', h, flags=re.S|re.I)

def aufraeumen_kurz(h):
    """Kurzbeschreibung: keine Ueberschriften, also auch kein Abstand -
    nur Muell raus."""
    if not h or not h.strip(): return h
    h = _gutenberg_weg(h)
    h = _latex_aufloesen(h)
    h = _bold_span(h)
    h = _muell_attribute(h)
    h = _baum_reparieren(h)
    h = _leere_bloecke(h)
    return _weissraum(h)
