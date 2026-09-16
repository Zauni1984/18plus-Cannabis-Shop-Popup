# -*- coding: utf-8 -*-
"""Growbedarf-Attribute aus den beschrifteten Datenbloecken der Beschreibung.

Die Texte fuehren die technischen Werte als 'Label: Wert'-Zeilen. Gelesen wird
nur, was dort ausdruecklich steht - abgeleitet oder gerundet wird nichts.
"""
import re, html

# Label im Text -> Attribut. Reihenfolge zaehlt, das erste Label gewinnt.
FELDER = [
  ('pa_leistungsaufnahme', ['Leistungsaufnahme', 'Leistung', 'Lichtquelle', 'Nennleistung']),
  ('pa_ppf',               ['PPF', 'Lichtleistung gesamt', 'Photonenstrom', 'Lichtstrom PPF']),
  ('pa_ppe',               ['Gesamt-Photonen-Wirkungsgrad', 'Lichtausbeute', 'PPE',
                            'Photonen-Wirkungsgrad', 'Wirkungsgrad']),
  ('pa_lichtspektrum',     ['Spektrum', 'Lichtspektrum', 'Farbspektrum']),
  ('pa_spannung',          ['Eingangsspannung', 'Spannung', 'Betriebsspannung']),
  ('pa_luftdurchsatz',     ['Kapazität Praktisch', 'Luftdurchsatz', 'Luftleistung',
                            'Fördervolumen', 'Kapazität']),
  ('pa_anschluss',         ['Anschlussdurchmesser', 'Anschluss Ø', 'Anschluss']),
  ('pa_abmessungen',       ['Außenmaß', 'Abmessungen', 'Maße', 'Außenmaße']),
  ('pa_material',          ['Material', 'Werkstoff']),
  ('pa_gewicht',           ['Gewicht', 'Eigengewicht']),
  ('pa_schutzart',         ['Schutzart', 'IP-Schutzart']),
  ('pa_flaeche',           ['Ausgelegte Fläche', 'Maximale Fläche', 'Kernfläche',
                            'Empfohlene Fläche', 'Abdeckung']),
  ('pa_geraeuschpegel',    ['Geräuschpegel', 'Lautstärke', 'Schalldruckpegel']),
  ('pa_lumen',             ['Lumen', 'Lichtstrom']),
  ('pa_farbe',             ['Farbe']),
]

# Werte, die keine Angabe sind
LEER = re.compile(r'^(?:-|–|k\.?\s?A\.?|keine Angabe|n/?a|auf Anfrage)$', re.I)

# Jeder Wert muss die Einheit seines Attributs tragen. Ohne passende Einheit
# ist es eine andere Angabe - "Anschluss: USB" ist kein Anschlussdurchmesser.
EINHEIT = {
  'pa_leistungsaufnahme': r'\d\s*(?:W|Watt)\b',
  'pa_ppf':               r'\d\s*[µμu]mol/s',
  'pa_ppe':               r'\d[,.]?\d*\s*[µμu]mol/J',
  'pa_lichtspektrum':     r'\d\s*(?:K\b|nm\b)|\bVollspektrum\b|\bweiß\b',
  'pa_spannung':          r'\d\s*V\b',
  'pa_luftdurchsatz':     r'\d\s*m[³3]\s*/\s*h',
  'pa_anschluss':         r'[Øø⌀]|\d\s*mm\b|\d\s*(?:Zoll|")',
  'pa_abmessungen':       r'\d\s*(?:mm|cm|m)\b',
  'pa_gewicht':           r'\d\s*(?:kg|g)\b',
  'pa_schutzart':         r'\bIP\s?\d{2}\b',
  'pa_flaeche':           r'\d\s*(?:cm|m)\b|m²',
  'pa_geraeuschpegel':    r'\d\s*dB',
  'pa_lumen':             r'\d\s*(?:lm\b|Lumen)',
  'pa_material':          r'^[A-Za-zÄÖÜäöüß][^0-9]{2,44}$',
  'pa_farbe':             r'^[A-Za-zÄÖÜäöüß/ -]{3,24}$',
}


def text(p):
    t = p.get('description') or ''
    t = re.sub(r'<li[^>]*>', '\n', t)
    t = re.sub(r'<[^>]+>', '\n', t)
    t = html.unescape(t)
    return re.sub(r'[ \t]+', ' ', t)


def feld(t, label):
    m = re.search(r'(?:^|\n)\s*[•\-\*]?\s*' + re.escape(label) +
                  r'\s*(?:\([^)\n]{0,40}\))?\s*:\s*([^\n]{1,90})', t, re.I)
    if not m:
        return None
    v = m.group(1).strip(' .;,')
    v = re.sub(r'\s+', ' ', v)
    # angehaengte zweite Angabe abtrennen: "100 cm, Durchmesser: 40 cm"
    v = re.split(r',\s*[A-ZÄÖÜ][\wÄÖÜäöüß ]{2,34}:', v)[0].strip(' .;,')
    v = re.split(r',\s*(?:Strom|Transportgewicht|Artikelgewicht|Durchmesser|Länge)\b', v)[0].strip(' .;,')
    if not v or LEER.match(v) or len(v) > 70:
        return None
    return v


# Werte, die frei im Namen oder in Stichpunkten ohne Doppelpunkt stehen
FREI = [
  ('pa_luftdurchsatz', r'(\d{2,5}\s*m[³3]\s*/\s*h)'),
  ('pa_anschluss',     r'[Øø⌀]\s*(\d{2,3})\s*mm'),
  ('pa_leistungsaufnahme', r'(?<![\d,.])(\d{1,4})\s*W(?:att)?\b(?!\w)'),
  ('pa_inhalt',        r'(?:Volumen|Inhalt)\s*[:\s]\s*(\d+(?:[,.]\d+)?\s*(?:L|Liter|ml))\b'),
  ('pa_abmessungen',   r'(?:Außenmaß|Außenmaße|Maße)\s*[:\s]\s*'
                       r'(\d+(?:[,.]\d+)?\s*[x×]\s*\d+(?:[,.]\d+)?(?:\s*[x×]\s*\d+(?:[,.]\d+)?)?\s*(?:mm|cm|m)\b)'),
  ('pa_durchmesser_hilf', r'Durchmesser\s*[Øø⌀]?\s*(\d+(?:[,.]\d+)?\s*cm)\b'),
  ('pa_maschenweite',  r'(?<![\d,.])(\d{2,4})\s*[µμu]m\b'),
  ('pa_presskraft',    r'(?<![\d,.])(\d+(?:[,.]\d+)?)\s*(?:Tonnen?|t)\b(?!\w)'),
]

# Werkstoffe, wie sie in den Stichpunkten stehen
MATERIAL = [
  ('Edelstahl',   r'\bEdelstahl\w*\b|\brostfrei\w*\s+Stahl\b|\bstainless\b|\bV[24]A\b'),
  ('Stahl',       r'\bStahl(?:rohr\w*)?\b'),
  ('Aluminium',   r'\bAlu(?:minium)?\b'),
  ('Nylon',       r'\bNylon\b|\bPolyamid\b'),
  ('Polyethylen', r'\bPolyethylen\b|\bHart-?PE\b|\bHDPE\b'),
  ('Polypropylen',r'\bPolypropylen\b|\bPP\b'),
  ('PVC',         r'\bPVC\b'),
  ('Kunststoff',  r'\bKunststoff\b'),
  ('Glas',        r'\bGlas\b'),
  ('Silikon',     r'\bSilikon\b'),
  ('Textil',      r'\bTextil\b|\bOxford\b|\bPolyester\b'),
  ('Keramik',     r'\bKeramik\b'),
]


def material(t, name):
    for wert, muster in MATERIAL:
        for quelle in (name, t):
            m = re.search(muster, quelle)
            if m and not re.search(VERNEINT, quelle[max(0, m.start() - 30):m.start()], re.I):
                return wert
    return None


VERNEINT = r'(?:nicht|kein[ae]?[rnms]?|ohne|statt|frei von)\W{0,24}$'


def frei(t, name):
    """sucht erst im Namen, dann im Text"""
    out = {}
    for slug, muster in FREI:
        for quelle in (name, t):
            m = re.search(muster, quelle, re.I)
            if m:
                w = m.group(1).strip()
                if slug == 'pa_anschluss':
                    w = f'{w} mm'
                if slug == 'pa_leistungsaufnahme':
                    w = f'{w} W'
                if slug == 'pa_maschenweite':
                    w = f'{w} µm'
                if slug == 'pa_presskraft':
                    w = f'{w} t'.replace('.', ',')
                if slug == 'pa_luftdurchsatz':
                    w = re.sub(r'\s*m[³3]\s*/\s*h', ' m³/h', w)
                w = re.sub(r'\s{2,}', ' ', w).strip()
                if slug == 'pa_inhalt':
                    if re.search(r'ml$', w, re.I):
                        w = re.sub(r'\s*ml$', ' ml', w, flags=re.I)
                    else:
                        w = re.sub(r'\s*(?:l|liter)$', ' L', w, flags=re.I)
                out[slug] = [w]
                break
    out.pop('pa_durchmesser_hilf', None)
    return out


def build(p):
    t = text(p)
    neu = {}
    for slug, label in FELDER:
        for lab in label:
            v = feld(t, lab)
            if not v:
                continue
            pruef = EINHEIT.get(slug)
            if pruef and not re.search(pruef, v, re.I):
                continue
            neu[slug] = [v]
            break
    for slug, w in frei(t, p.get('name', '')).items():
        neu.setdefault(slug, w)
    if 'pa_material' not in neu:
        mat = material(t, p.get('name', ''))
        if mat:
            neu['pa_material'] = [mat]
    return neu


if __name__ == '__main__':
    import json, collections
    D = json.load(open('gb_desc.json'))
    c = collections.Counter()
    bsp = collections.defaultdict(list)
    for p in D:
        for k, v in build(p).items():
            c[k] += 1
            if len(bsp[k]) < 4:
                bsp[k].append(v[0])
    print(f'{len(D)} Produkte')
    for k, n in c.most_common():
        print(f'  {n:4}  {k:22} z. B. {bsp[k]}')
