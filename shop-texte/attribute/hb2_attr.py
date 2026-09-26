# -*- coding: utf-8 -*-
"""Parser fuer die Sortenseiten von humboldtseedcompany.com (Hersteller)."""
import json, re, html, sys
sys.path.insert(0, '.')
import attr_schema as A
import vokabular as V

FELD = r'(GENETICS|TYPE\(S/H/I\)|TYPE|Smell|Flavors?|Effects?|Appearance|' \
       r'Harvest\s*Planning|Flowering\s*Time|THC|CBD|Yield)'


def text(html_roh):
    t = re.sub(r'<(script|style)[^>]*>.*?</\1>', ' ', html_roh or '', flags=re.S | re.I)
    t = re.sub(r'<[^>]+>', '\n', t)
    return html.unescape(re.sub(r'[ \t]+', ' ', t))


def felder(t):
    d = {}
    for m in re.finditer(FELD + r'\s*:\s*\n?\s*([^\n]{2,160})', t, re.I):
        k = re.sub(r'\s+', ' ', m.group(1)).upper().replace('TYPE(S/H/I)', 'TYPE')
        v = m.group(2).strip(' .,;')
        if v and k not in d:
            d[k] = v
    return d


def _ci(tabelle):
    return {k.lower(): v for k, v in tabelle.items()}


AROMA_CI, EFFEKT_CI = _ci(V.AROMA), _ci(V.EFFEKT)
SHOP = {slug: {n.lower(): n for n in namen}
        for slug, namen in json.load(open('shop_terms.json')).items()}


def _liste(roh, tabelle, slug):
    """Nur Werte uebernehmen, die sicher zugeordnet werden koennen -
    unuebersetzte englische Freitexte werden verworfen."""
    if not roh:
        return []
    out = []
    for teil in re.split(r'\s*[,/&]\s*|\s+and\s+|\s+with\s+', roh):
        t = teil.strip(' .').lower()
        if not t:
            continue
        w = tabelle.get(t) or SHOP.get(slug, {}).get(t)
        if not w:                       # einzelnes Wort noch einmal versuchen
            worte = t.split()
            if len(worte) == 1:
                w = tabelle.get(worte[0]) or SHOP.get(slug, {}).get(worte[0])
        if w and w not in out:
            out.append(w)
    return out


def genetik(roh):
    if not roh:
        return []
    s = roh.replace('(', ' ').replace(')', ' ').replace('\u2019', "'")
    teile = [re.sub(r'\s+', ' ', x).strip(' ,.') for x in re.split(r'\s+[×xX]\s+', s)]
    return [t for t in teile if 2 <= len(t) <= 60 and t.lower() not in ('unknown', 'n/a')]


def typ(roh):
    if not roh:
        return None
    r = roh.lower()
    if 'indica dominant' in r or 'indica-dominant' in r:
        return 'Indica-dominant'
    if 'sativa dominant' in r or 'sativa-dominant' in r:
        return 'Sativa-dominant'
    if 'balanced' in r or re.fullmatch(r'\s*hybrid\s*', r):
        return 'Hybrid (ausgewogen)'
    return None


def build(seite):
    t = text(seite['content']['rendered'])
    f = felder(t)
    neu = {}
    g = genetik(f.get('GENETICS'))
    if g:
        neu['pa_genetik'] = g
    ty = typ(f.get('TYPE'))
    if ty:
        neu['pa_typ'] = [ty]
    ar = _liste(f.get('SMELL'), AROMA_CI, 'pa_aroma')
    if ar:
        neu['pa_aroma'] = ar
    ge = _liste(f.get('FLAVORS') or f.get('FLAVOR'), AROMA_CI, 'pa_geschmack')
    if ge:
        neu['pa_geschmack'] = ge
    ef = _liste(f.get('EFFECTS') or f.get('EFFECT'), EFFEKT_CI, 'pa_effekte')
    if ef:
        neu['pa_effekte'] = ef
    # Bluetezeit nur, wenn ausdruecklich die Bluete gemeint ist -
    # "days from germination" ist die Gesamtdauer, nicht die Bluetezeit
    bl = f.get('FLOWERING TIME')
    if bl:
        z = [int(x) for x in re.findall(r'\b(\d{2,3})\b', bl) if 28 <= int(x) <= 140]
        if z:
            neu['pa_bluetezeit-tage'] = ['-'.join(str(x) for x in sorted(set(z))[:2])]
    return neu


if __name__ == '__main__':
    P = json.load(open('hb2_pages.json'))
    n = 0
    for p in P:
        b = build(p)
        if b:
            n += 1
            if n <= 8:
                print(p['title']['rendered'][:42], '->', b)
    print(n, 'Seiten mit Daten')
