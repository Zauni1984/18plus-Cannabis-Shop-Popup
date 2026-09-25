# -*- coding: utf-8 -*-
"""Fokus-Keyphrase, Synonyme und weitere Keyphrasen fuer alle Produkte.

Yoast Premium kennt drei Felder:
  _yoast_wpseo_focuskw            die Fokus-Keyphrase
  _yoast_wpseo_keywordsynonyms    Synonyme, JSON-Array mit einer kommagetrennten
                                  Zeichenkette - so legt es die Oberflaeche ab
  _yoast_wpseo_focuskeywords      weitere Keyphrasen, JSON-Array aus
                                  {"keyword": ..., "score": 0}

Gebaut wird ausschliesslich aus vorhandenen Daten: Produktname, Marke und
Kategorie. Nichts wird erfunden, und es bleibt bei hoechstens zwei Synonymen
und zwei weiteren Keyphrasen - mehr verwaessert nur die Analyse.
"""
import html, json, os, re, sys

S = '/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
sys.path.insert(0, S)
import hjapi

ARBEIT = os.environ.get('HJ_ARBEIT', S)

# Kategorie -> (Typwort, Synonym des Typworts, Zusatz fuer weitere Keyphrase)
TYPEN = [
    ('feminisiert',        'feminisiert',   'feminisierte Samen',   'Samen'),
    ('automatisch',        'Auto',          'Autoflower Samen',     'Samen'),
    ('regular',            'regulär',       'reguläre Samen',       'Samen'),
    ('cbd-samen',          'CBD Samen',     'CBD Cannabis Samen',   'Samen'),
    ('f1-samen',           'F1 Samen',      'F1 Hybrid Samen',      'Samen'),
    ('papers',             'Papers',        'Blättchen',            'Blättchen'),
    ('pre-rolled-papers',  'Cones',         'vorgerollte Blättchen', 'Cones'),
    ('filter',             'Filter',        'Aktivkohlefilter',     'Filter'),
    ('aktivkohlefilter-filter', 'Aktivkohlefilter', 'Aktivkohle Filter', 'Filter'),
    ('bongs',              'Bong',          'Wasserpfeife',         'Bong'),
    ('pipes',              'Pfeife',        'Handpfeife',           'Pfeife'),
    ('kraeutermuehlen',    'Grinder',       'Kräutermühle',         'Grinder'),
    ('rolling-trays',      'Rolling Tray',  'Drehunterlage',        'Rolling Tray'),
    ('vaporizer',          'Vaporizer',     'Verdampfer',           'Vaporizer'),
    ('dabbing',            'Dab Zubehör',   'Dabbing Zubehör',      'Dabbing'),
    ('led-growlampen',     'LED Growlampe', 'Pflanzenlampe',        'Growlampe'),
    ('anzuchtbeleuchtung', 'Anzuchtlampe',  'Stecklingslampe',      'Anzuchtlampe'),
    ('growboxen',          'Growbox',       'Growzelt',             'Growbox'),
    ('luefter-filter',     'Abluftset',     'Growraum Belüftung',   'Belüftung'),
    ('zu-und-abluft',      'Abluftschlauch', 'Lüftungsrohr',        'Abluft'),
    ('duenger',            'Dünger',        'Pflanzennahrung',      'Dünger'),
    ('erde-substrate',     'Erde',          'Substrat',             'Erde'),
    ('pflanzentoepfe',     'Pflanztopf',    'Topf',                 'Pflanztopf'),
    ('bewaesserung',       'Bewässerung',   'Bewässerungssystem',   'Bewässerung'),
    ('ph-wert',            'pH-Messgerät',  'pH Messung',           'pH-Messgerät'),
    ('trimmer-erntehelfer', 'Trimmer',      'Erntehelfer',          'Trimmer'),
    ('extraktion-pressen', 'Presse',        'Extraktion',           'Presse'),
    ('veredeln-extraktion', 'Presse',       'Pollenpresse',         'Presse'),
    ('curing-lagerung',    'Aufbewahrung',  'Curing Behälter',      'Aufbewahrung'),
    ('aufbewahrung',       'Aufbewahrung',  'Vorratsdose',          'Aufbewahrung'),
    ('feuerzeuge-zippo',   'Feuerzeug',     'Sturmfeuerzeug',       'Feuerzeug'),
    ('pflegeprodukte',     'Hanfpflege',    'Hanfkosmetik',         'Pflege'),
    ('lebensmittel',       'Hanf Lebensmittel', 'Hanfprodukt',      'Hanf'),
    ('cbd',                'CBD',           'CBD Produkt',          'CBD'),
    ('merch',              'Merch',         'Fanartikel',           'Merch'),
]

# Groessen, Mengen, Verpackungsangaben - fuer eine Keyphrase unbrauchbar
MUELL = re.compile(
    r'(\s*[-–—]\s*)?\b('
    r'\d+[.,]?\d*\s*(ml|l|liter|g|kg|mm|cm|m|watt|w|st(ü|ue)ck|stk|blatt|bl(ä|ae)ttchen|pack|er[- ]pack|zoll|pint)'
    r'|\d+er(\s*(pack|set|packung))?'
    r'|[ØøΦ⌀]\s*[:=]?\s*\d+[.,]?\d*\s*(mm|cm)?'
    r'|(H|B|T|SG)\s*:\s*\d+[.,]?\d*\s*(mm|cm)?'
    r'|\d+\s*x\s*\d+(\s*x\s*\d+)?\s*(mm|cm|m)?'
    r')\b\.?', re.I)

FUELL = re.compile(r'\b(mit|und|für|fuer|aus|im|in|der|die|das|von|zum|zur|inkl|inklusive|ca)\b', re.I)
LOB = re.compile(r'\b(legend(ä|ae)re?s?|ertragreiche?s?|ertragsstarke?s?|stimulierende?s?|'
                 r'potente?s?|erfrischende?s?|stabile?s?|farbenpr(ä|ae)chtige?s?|premium|'
                 r'vollspektrum|dimmbar(e|es)?|neu)\b', re.I)


def sauber(t):
    """Name ohne Groessen-, Mengen- und Verpackungsangaben."""
    t = html.unescape(t or '')
    t = t.replace('\u00b4', "'").replace('\u2019', "'")
    t = re.sub(r'[\u2013\u2014]', '-', t)
    # Groessenangaben nur am Ende oder nach Trennzeichen entfernen
    t = re.split(r'\s+[-,]\s+', t)[0] if MUELL.search(re.split(r'\s+[-,]\s+', t)[-1]) else t
    t = MUELL.sub(' ', t)
    t = re.sub(r'\(([^)]*)\)', ' ', t)          # Klammerzusaetze
    t = re.sub(r'\s{2,}', ' ', t)
    t = re.sub(r'(\s[\d.,]+)+$', '', t)          # uebrig gebliebene Zahlenreste
    return t.strip(' -,')


def ohne_marke(text, marke):
    if not marke:
        return text
    kurz = re.sub(r'^' + re.escape(marke) + r'\s*', '', text, flags=re.I).strip(' -,')
    # Schreibweisen der Marke koennen abweichen (Lovin\' In Her Eyes)
    if kurz == text and len(marke.split()) > 1:
        muster = r'^' + r'\s*'.join(re.escape(w) for w in marke.split()) + r'\s*'
        kurz = re.sub(muster, '', text, flags=re.I).strip(' -,')
    return kurz or text


def typ(slugs):
    for slug, typwort, synonym, zusatz in TYPEN:
        if slug in slugs:
            return typwort, synonym, zusatz
    return None, None, None


def schlecht(fokus):
    """Fokus-Keyphrase, die ersetzt gehoert."""
    if not fokus or not fokus.strip():
        return True
    if len(fokus.split()) >= 7:
        return True
    if re.search(r'%|\bTHC\b|Aroma|\d+\s*(ml|g|kg|mm|cm)\b', fokus, re.I):
        return True
    return False


def bauen(name, marke, slugs, fokus_alt):
    rein = sauber(name)
    typwort, typsyn, zusatz = typ(slugs)

    if schlecht(fokus_alt):
        w = LOB.sub(' ', rein).split()[:5]
        while w and FUELL.fullmatch(w[-1]):
            w.pop()
        fokus = ' '.join(w).strip(' -,')
    else:
        fokus = fokus_alt.strip()
    if not fokus or len(fokus) < 4:
        return None

    fl = fokus.lower()
    basis = ohne_marke(fokus, marke)          # Fokus ohne Markennamen
    synonyme, weitere = [], []

    # Synonym 1: dieselbe Sache mit bzw. ohne Marke
    if marke:
        if marke.lower() in fl:
            if basis and basis.lower() != fl and len(basis.split()) >= 2:
                synonyme.append(basis)
        else:
            synonyme.append(f'{marke} {fokus}')
    # Synonym 2: gebraeuchliche andere Bezeichnung der Warengruppe
    if typsyn and typsyn.lower() not in fl:
        synonyme.append(typsyn)

    # Weitere 1: Kaufabsicht, auf der vollstaendigen Bezeichnung
    weitere.append(f'{basis} kaufen' if basis else f'{fokus} kaufen')
    # Weitere 2: Bezeichnung plus Warengruppe, nur wenn sie noch nicht drinsteht
    if zusatz and zusatz.lower() not in fl and zusatz.lower() not in basis.lower() \
            and len(basis.split()) <= 4:
        weitere.append(f'{basis} {zusatz}')

    def ohne_fuellende_endung(e):
        w = e.split()
        while w and FUELL.fullmatch(w[-1]):
            w.pop()
        return ' '.join(w)

    def putzen(liste):
        aus = []
        for e in liste:
            e = ohne_fuellende_endung(re.sub(r'\s{2,}', ' ', e or '').strip(' -,'))
            if len(e) < 5 or len(e.split()) > 5:
                continue
            if e.lower() == fl or e.lower() in [x.lower() for x in aus]:
                continue
            if re.search(r'\b\d+$', e) and len(e.split()) <= 2:
                continue
            aus.append(e)
        return aus[:2]

    return {'fokus': fokus, 'fokus_neu': schlecht(fokus_alt),
            'synonyme': putzen(synonyme), 'weitere': putzen(weitere)}


# --------------------------------------------------------------------------
# Plan und Schreiben
# --------------------------------------------------------------------------
STAPEL = int(os.environ.get('HJ_STAPEL', '10'))
PAUSE = float(os.environ.get('HJ_PAUSE', '3'))


def _laden(name):
    return json.load(open(os.path.join(ARBEIT, name)))


def _meta(p, schluessel):
    for m in p.get('meta_data') or []:
        if m['key'] == schluessel:
            return m.get('value')
    return None


def plan():
    produkte = _laden('yoast_produkte.json')
    kats = {k['id']: k for k in _laden('kategorien.json')}
    marken = _laden('marken.json')
    auftrag, zahl = [], {'fokus': 0, 'syn': 0, 'weitere': 0, 'nichts': 0}
    for p in produkte:
        slugs = [kats[c['id']]['slug'] for c in p.get('categories') or [] if c['id'] in kats]
        alt = str(_meta(p, '_yoast_wpseo_focuskw') or '')
        r = bauen(p['name'], marken.get(str(p['id'])), slugs, alt)
        if not r:
            zahl['nichts'] += 1
            continue
        meta = []
        if r['fokus_neu'] and r['fokus'] != alt:
            meta.append({'key': '_yoast_wpseo_focuskw', 'value': r['fokus']})
            zahl['fokus'] += 1
        # Synonyme legt Yoast als JSON-Array mit einer kommagetrennten Zeichenkette ab
        if r['synonyme'] and not _meta(p, '_yoast_wpseo_keywordsynonyms'):
            meta.append({'key': '_yoast_wpseo_keywordsynonyms',
                         'value': json.dumps([', '.join(r['synonyme'])], ensure_ascii=False)})
            zahl['syn'] += 1
        if r['weitere'] and not _meta(p, '_yoast_wpseo_focuskeywords'):
            meta.append({'key': '_yoast_wpseo_focuskeywords',
                         'value': json.dumps([{'keyword': w, 'score': 0} for w in r['weitere']],
                                             ensure_ascii=False)})
            zahl['weitere'] += 1
        if not meta:
            zahl['nichts'] += 1
            continue
        auftrag.append({'id': p['id'], 'meta_data': meta})
    print(f"{len(produkte)} Produkte geprueft")
    print(f"  Fokus-Keyphrase ersetzt : {zahl['fokus']}")
    print(f"  Synonyme gesetzt        : {zahl['syn']}")
    print(f"  weitere Keyphrasen      : {zahl['weitere']}")
    print(f"  nichts zu tun           : {zahl['nichts']}")
    print(f"  zu schreiben            : {len(auftrag)}")
    json.dump(auftrag, open(os.path.join(ARBEIT, 'keyphrase_auftrag.json'), 'w'))
    return auftrag


def schreiben():
    auftrag = json.load(open(os.path.join(ARBEIT, 'keyphrase_auftrag.json')))
    pfad = os.path.join(ARBEIT, 'keyphrase_geschrieben.txt')
    fertig = {int(z) for z in open(pfad).read().split()} if os.path.exists(pfad) else set()
    offen = [a for a in auftrag if a['id'] not in fertig]
    print(f'offen: {len(offen)}', flush=True)
    with open(pfad, 'a') as prot:
        for i in range(0, len(offen), STAPEL):
            teil = offen[i:i+STAPEL]
            antwort = hjapi.ruf('products/batch', {'update': teil}, 'POST', pause=PAUSE)
            zurueck = {x['id'] for x in antwort.get('update', []) if 'id' in x}
            for a in teil:
                if a['id'] in zurueck:
                    prot.write(f"{a['id']}\n")
            prot.flush()
            print(f'{i+len(teil):>5}/{len(offen)}', flush=True)
    print('fertig')


if __name__ == '__main__':
    if len(sys.argv) > 1 and sys.argv[1] == 'schreiben':
        schreiben()
    else:
        plan()
