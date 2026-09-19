# -*- coding: utf-8 -*-
"""Attribute fuer den Rest des Sortiments: CBD-Blueten und -Oele, Lebensmittel,
Pflegeprodukte, Buecher, Merch, Vermehrungsmaterial.

Gleiches Vorgehen wie bei Headshop und Growbedarf: beschriftete Datenzeilen
lesen, Einheiten pruefen, Fliesstext verwerfen.
"""
import re, html, sys
sys.path.insert(0, '.')
import attr_schema as A

FELDER = [
  ('pa_inhalt',      ['Inhalt', 'Menge', 'Gebinde', 'Füllmenge']),
  ('pa_thc-gehalt',  ['THC-Gehalt', 'THC']),
  ('pa_cbd-gehalt',  ['CBD-Gehalt', 'CBD']),
  ('pa_spektrum',    ['Spektrum']),
  ('pa_traegeroel',  ['Trägeröl', 'Traegeroel', 'Basisöl']),
  ('pa_aroma',       ['Aroma', 'Geschmack']),
  ('pa_genetik',     ['Ausgangsgenetik', 'Genetik', 'Sorte']),
  ('pa_herkunft',    ['Herkunft', 'Anbau']),
  ('pa_material',    ['Material']),
  ('pa_grammatur',   ['Grammatur']),
  ('pa_verlag',      ['Verlag']),
  ('pa_einband',     ['Einband']),
  ('pa_isbn',        ['ISBN']),
  ('pa_format',      ['Format']),
]

EINHEIT = {
  'pa_inhalt':      r'\d',
  'pa_thc-gehalt':  r'\d',
  'pa_cbd-gehalt':  r'\d',
  'pa_grammatur':   r'\d\s*g',
  'pa_isbn':        r'\d{3}',
}

SATZ = re.compile(r'\b(?:ist|sind|wird|werden|hat|haben|kann|lässt|liegt|sorgt|bleibt|'
                  r'darfst?|musst?|solltest?|damit|sodass|weil|wenn|dabei|dadurch)\b', re.I)
LAENGE_MAX = {'pa_inhalt': 34, 'pa_thc-gehalt': 26, 'pa_cbd-gehalt': 26, 'pa_spektrum': 30,
              'pa_traegeroel': 34, 'pa_aroma': 44, 'pa_genetik': 44, 'pa_herkunft': 40,
              'pa_material': 40, 'pa_grammatur': 24, 'pa_verlag': 40, 'pa_einband': 30,
              'pa_isbn': 24, 'pa_format': 34}
LAND = re.compile(r'\b(Deutschland|China|Indien|Niederlande|Schweiz|Österreich|Spanien|'
                  r'Italien|Frankreich|Polen|Tschechien|USA|Slowenien|Portugal|Kroatien)\b')
UNGEFAEHR = re.compile(r'^(?:rund|ungefähr|etwa|circa|ca\.?|unter|bis zu|knapp)\s+', re.I)


def text(p):
    t = re.sub(r'<li[^>]*>', '\n', p.get('description') or '')
    t = html.unescape(re.sub(r'<[^>]+>', '\n', t))
    return re.sub(r'[ \t]+', ' ', t)


def feld(t, lab):
    m = re.search(r'(?:^|\n)\s*[•\-\*]?\s*' + re.escape(lab) +
                  r'\s*(?:\([^)\n]{0,30}\))?\s*:\s*([^\n]{1,110})', t, re.I)
    return re.sub(r'\s+', ' ', m.group(1)).strip(' .;,') if m else None


def saeubern(slug, v):
    if slug == 'pa_herkunft':
        m = LAND.search(v)
        return m.group(1) if m else None
    v = re.split(r'\s+[–—]\s+|\s*;\s*', v)[0].strip(' .;,')
    v = UNGEFAEHR.sub('', v)
    if slug in ('pa_thc-gehalt', 'pa_cbd-gehalt'):
        m = re.search(r'\d+(?:[,.]\d+)?\s*(?:bis|-|–)?\s*(?:\d+(?:[,.]\d+)?)?\s*%', v)
        if not m:
            return None
        v = re.sub(r'\s+', ' ', m.group(0)).replace('.', ',')
    if slug == 'pa_isbn':
        m = re.search(r'[\d-]{10,20}', v)
        return m.group(0) if m else None
    if slug == 'pa_aroma':
        teile = [x.strip(' .;,') for x in re.split(r'\s*,\s*|\s+und\s+', v)
                 if 2 < len(x.strip()) < 34 and not SATZ.search(x)]
        return teile or None
    if slug == 'pa_genetik':
        # nur am Kreuzungszeichen trennen - "Pineapple Express" hat ein x im Wort.
        # Was hinter einem Komma steht, ist Erlaeuterung und kein Sortenname.
        v = v.split(',')[0].strip(' .;,')
        teile = [x.strip(' .;,') for x in re.split(r'\s+[x×X]\s+', v)
                 if 2 < len(x.strip()) < 34 and not SATZ.search(x)]
        return teile or None
    if not v or SATZ.search(v) or len(v) > LAENGE_MAX.get(slug, 40):
        return None
    pruef = EINHEIT.get(slug)
    if pruef and not re.search(pruef, v, re.I):
        return None
    return v


def build(p):
    t = text(p)
    neu = {}
    for slug, labels in FELDER:
        if slug not in A.ATTR_ID:
            continue
        for lab in labels:
            v = feld(t, lab)
            if not v:
                continue
            v = saeubern(slug, v)
            if v:
                neu[slug] = v if isinstance(v, list) else [v]
                break
    return neu


if __name__ == '__main__':
    import json, collections
    D = json.load(open('rest_desc.json'))
    c = collections.Counter()
    bsp = collections.defaultdict(list)
    for p in D:
        for k, v in build(p).items():
            c[k] += 1
            if len(bsp[k]) < 4:
                bsp[k].append(v[0] if len(v) == 1 else v)
    print(f'{len(D)} Produkte')
    for k, n in c.most_common():
        print(f'  {n:4}  {k:18} {bsp[k]}')
