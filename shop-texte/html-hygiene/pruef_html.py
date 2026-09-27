# -*- coding: utf-8 -*-
"""Prueft Produktbeschreibungen auf echte Markup-Fehler."""
import json, re, sys
from collections import Counter, defaultdict
from html.parser import HTMLParser

LEER   = {'br','img','hr','input','meta','link','source','wbr','col','area','embed'}
BLOCK  = {'p','h1','h2','h3','h4','h5','h6','ul','ol','table','div','blockquote',
          'pre','figure','section','iframe','hr','dl'}

class Baum(HTMLParser):
    """Sammelt offene Tags und meldet, was nicht sauber schliesst."""
    def __init__(self):
        super().__init__(convert_charrefs=True)
        self.stapel, self.fehler, self.tiefe0_text = [], [], []
        self.ebene = 0
    def handle_starttag(self, tag, attrs):
        if tag in LEER: return
        if self.ebene == 0: self.wurzel = tag
        self.stapel.append(tag); self.ebene += 1
    def handle_endtag(self, tag):
        if tag in LEER: return
        if tag not in self.stapel:
            self.fehler.append(f'</{tag}> ohne oeffnendes Tag'); return
        while self.stapel:
            o = self.stapel.pop(); self.ebene -= 1
            if o == tag: break
            self.fehler.append(f'<{o}> nicht geschlossen')
    def handle_data(self, d):
        if self.ebene == 0 and d.strip():
            self.tiefe0_text.append(d.strip()[:70])

def pruefe(h):
    """Gibt eine Liste von Befundkuerzeln zurueck."""
    b = []
    if not h or not h.strip(): return ['leer']
    roh = h

    # 1. Text, der in keinem Block-Element steckt -> laeuft beim Rendern zusammen
    p = Baum()
    try: p.feed(roh); p.close()
    except Exception as e: b.append('parserfehler')
    if p.tiefe0_text: b.append('text-ohne-block')
    if p.stapel: b.append('tag-offen')
    if p.fehler:  b.append('tag-kaputt')

    # 2. <br> als Absatzersatz
    if re.search(r'(?:<br\s*/?>\s*){2,}', roh, re.I): b.append('br-stapel')
    # <br> direkt vor/nach einem Block-Tag ist immer ueberfluessig
    if re.search(r'<br\s*/?>\s*</?(?:p|h[1-6]|ul|ol|li|div|table)\b', roh, re.I): b.append('br-am-block')
    if re.search(r'</(?:p|h[1-6]|ul|ol|div|table)>\s*<br\s*/?>', roh, re.I): b.append('br-am-block')

    # 3. doppelt kodierte Entities / sichtbares Markup
    if re.search(r'&amp;(?:amp|nbsp|quot|lt|gt|#\d+);', roh): b.append('entity-doppelt')
    if re.search(r'&lt;\s*/?\s*(?:p|br|ul|li|h[1-6]|strong|em)\b', roh, re.I): b.append('markup-sichtbar')

    # 4. leere Absaetze
    if re.search(r'<p>(?:\s|&nbsp;|<br\s*/?>)*</p>', roh, re.I): b.append('p-leer')
    if re.search(r'<(h[1-6])>(?:\s|&nbsp;)*</\1>', roh, re.I): b.append('ueberschrift-leer')

    # 5. ungueltige Verschachtelung
    if re.search(r'<p>[^<]*<(?:ul|ol|div|h[1-6])\b', roh, re.I): b.append('block-in-p')
    if re.search(r'<li>', roh, re.I) and not re.search(r'<(?:ul|ol)\b', roh, re.I): b.append('li-ohne-liste')

    # 6. Ueberschrift als fetter Absatz statt <h?>
    if re.search(r'<p>\s*<(?:strong|b)>[^<]{3,60}:?\s*</(?:strong|b)>\s*</p>', roh, re.I):
        b.append('pseudo-ueberschrift')

    # 7. Editor-Muell
    if re.search(r'\bstyle\s*=', roh, re.I): b.append('inline-style')
    if re.search(r'mso-|MsoNormal|class="Apple|<font\b|<span\s+style', roh, re.I): b.append('word-muell')
    if re.search(r'<(?:o:p|w:)', roh, re.I): b.append('word-muell')

    # 8. unaufgeloeste Shortcodes
    if re.search(r'\[[a-z_]{3,}[^\]]{0,80}\]', roh): b.append('shortcode')

    # 9. literale Zeilenumbrueche als Text
    if '\\n' in roh: b.append('literales-n')
    return b

def main():
    daten = json.load(open(sys.argv[1] if len(sys.argv)>1 else 'desc_alle.json'))
    zaehler, beispiele = Counter(), defaultdict(list)
    treffer = {}
    for pr in daten:
        for feld in ('description','short_description'):
            for kuerzel in pruefe(pr.get(feld) or ''):
                if kuerzel == 'leer' and feld == 'short_description': continue
                schl = f'{feld[:5]}:{kuerzel}'
                zaehler[schl] += 1
                if len(beispiele[schl]) < 4:
                    beispiele[schl].append((pr['id'], pr['name'][:60]))
                treffer.setdefault(pr['id'], {'name':pr['name'],'status':pr['status'],'befunde':[]})
                treffer[pr['id']]['befunde'].append(schl)
    print(f'{len(daten)} Produkte geprueft, {len(treffer)} mit Befund\n')
    for k, n in zaehler.most_common():
        print(f'{n:5d}  {k}')
        for pid, nm in beispiele[k]: print(f'         {pid}  {nm}')
    json.dump(treffer, open('pruef_treffer.json','w'), ensure_ascii=False, indent=1)

main()
