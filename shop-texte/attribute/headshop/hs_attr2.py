# -*- coding: utf-8 -*-
"""Headshop-Attribute aus den beschrifteten Datenbloecken der Beschreibung.

Die Texte enthalten viel Fliesstext in den Datenzeilen ("Form: Ein Ende rund,
ein Ende spitz zulaufend"). Uebernommen wird nur, was wie ein Attributwert
aussieht - kurz, ohne Satzbau, mit der passenden Einheit.
"""
import re, html

FELDER = [
  ('pa_abmessungen', ['Maße', 'Abmessungen', 'Außenmaß']),
  ('pa_material',    ['Material', 'Werkstoff']),
  ('pa_format',      ['Format']),
  ('pa_farbe',       ['Farbe']),
  ('pa_inhalt',      ['Inhalt', 'Verpackungseinheit']),
  ('pa_motiv',       ['Motiv', 'Dekor', 'Motivserie']),
  ('pa_brennstoff',  ['Brennstoff']),
  ('pa_laenge',      ['Länge']),
  ('pa_durchmesser', ['Durchmesser']),
  ('pa_gewicht',     ['Gewicht']),
  ('pa_herkunft',    ['Herkunft', 'Hergestellt in', 'Herstellung', 'Fertigung']),
]

EINHEIT = {
  'pa_abmessungen': r'\d\s*(?:mm|cm)\b',
  'pa_laenge':      r'\d\s*(?:mm|cm|m)\b',
  'pa_durchmesser': r'\d\s*(?:mm|cm)\b',
  'pa_gewicht':     r'\d\s*(?:g|kg)\b',
  'pa_inhalt':      r'\d',
}

# Satzbau erkennen: Attributwerte haben keine Verben und keine Nebensaetze
SATZ = re.compile(r'\b(?:ist|sind|wird|werden|hat|haben|kann|lässt|liegt|sorgt|bleibt|'
                  r'sitzt|passt|damit|sodass|weil|wenn|dabei|dadurch|hältst?|gibt|'
                  r'bietet|zulaufend|geschnitten|ohne dass)\b', re.I)

LAENGE_MAX = {'pa_material': 42, 'pa_format': 38, 'pa_farbe': 26, 'pa_inhalt': 34,
              'pa_motiv': 38, 'pa_brennstoff': 44, 'pa_abmessungen': 44,
              'pa_laenge': 26, 'pa_durchmesser': 26, 'pa_gewicht': 26,
              'pa_herkunft': 40}

LAND = re.compile(r'\b(Deutschland|China|Indien|Niederlande|Spanien|Frankreich|Italien|'
                  r'Österreich|Schweiz|Polen|Tschechien|USA|Vereinigte Staaten|Japan|'
                  r'Korea|Taiwan|Türkei|Vietnam|Thailand|Belgien|Portugal|Ungarn|'
                  r'Slowakei|Slowenien|Brasilien|Mexiko|Indonesien|Kanada|'
                  r'Vereinigtes Königreich|Großbritannien)\b')


def text(p):
    t = re.sub(r'<li[^>]*>', '\n', p.get('description') or '')
    t = html.unescape(re.sub(r'<[^>]+>', '\n', t))
    return re.sub(r'[ \t]+', ' ', t)


def feld(t, lab):
    m = re.search(r'(?:^|\n)\s*[•\-\*]?\s*' + re.escape(lab) +
                  r'\s*(?:\([^)\n]{0,30}\))?\s*:\s*([^\n]{1,110})', t, re.I)
    if not m:
        return None
    v = re.sub(r'\s+', ' ', m.group(1)).strip(' .;,')
    return v or None


FORMAT_OK = re.compile(r'\bKing\s?Size\b|\bSlim\b|\bRegular\b|\bMedium\b|\bLarge\b|'
                       r'\bSmall\b|\b1\s?1?[/¼]\s?\d?\b|\b1¼\b|\bSingle\s?Wide\b|'
                       r'\bDouble\b|\bCone\b|\bRolls?\b|\d\s*(?:mm|cm)\b', re.I)
UNGEFAEHR = re.compile(r'^(?:rund|ungefähr|etwa|circa|ca\.?|knapp|gut)\s+', re.I)
MASS = re.compile(r'\d+(?:[,.]\d+)?\s*(?:[x×]\s*\d+(?:[,.]\d+)?\s*)*'
                  r'(?:mm|cm|m|g|kg|Zoll)\b', re.I)


def saeubern(slug, v):
    if slug == 'pa_herkunft':
        m = LAND.search(v)
        return m.group(1) if m else None
    # angehaengte Erlaeuterung abtrennen
    v = re.split(r'\s+[–—-]\s+|\s*;\s*', v)[0].strip(' .;,')
    v = UNGEFAEHR.sub('', v)
    if slug in ('pa_gewicht', 'pa_laenge', 'pa_durchmesser'):
        m = MASS.search(v)                      # nur die Messgroesse behalten
        if not m:
            return None
        v = m.group(0)
    if slug == 'pa_abmessungen':
        masse = MASS.findall(v)
        if not masse:
            return None
    if slug == 'pa_format' and not FORMAT_OK.search(v):
        return None
    if slug == 'pa_material':
        v = v.split(',')[0].strip(' .;,')
    if slug == 'pa_brennstoff':
        v = re.sub(r',?\s*nicht enthalten.*$', '', v, flags=re.I).strip(' .;,')
    if slug in ('pa_farbe', 'pa_material', 'pa_motiv'):
        v = re.split(r',\s*(?:d(?:er|ie|as)|ein|mit|für|und|in)\b', v)[0].strip(' .;,')
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
        for lab in labels:
            v = feld(t, lab)
            if not v:
                continue
            v = saeubern(slug, v)
            if v:
                neu[slug] = [v]
                break
    return neu


if __name__ == '__main__':
    import json, collections
    D = json.load(open('hs_desc.json'))
    c = collections.Counter()
    bsp = collections.defaultdict(list)
    for p in D:
        for k, v in build(p).items():
            c[k] += 1
            if len(bsp[k]) < 5:
                bsp[k].append(v[0])
    print(f'{len(D)} Produkte')
    for k, n in c.most_common():
        print(f'  {n:4}  {k:18} {bsp[k]}')
