# -*- coding: utf-8 -*-
"""Baut Yoast-Titel und Meta-Description aus echten Produktdaten.

Es wird nichts erfunden: jede Angabe stammt aus dem Produktnamen, einem
gepflegten Attribut oder dem Beschreibungstext des Produkts selbst.
"""
import re, html

TITEL_MAX, META_MAX, META_ZIEL = 60, 156, 120
ZAHL = re.compile(r'\d')

def sauber(s):
    return re.sub(r'\s+', ' ', html.unescape(s or '')).strip()

# --- einzelne Angaben in lesbares Deutsch bringen --------------------------

def _gehalt(v, stoff):
    """THC und CBD stehen teils als Prozentwert, teils als Stufe ("Hoch")."""
    if ZAHL.search(v):
        # "25 % und mehr" liest sich als Baustein besser mit vorangestelltem "über"
        m = re.match(r'([\d,.\-–~ ]+)%?\s*und mehr$', v.strip())
        if m: return f'über {m.group(1).strip()} % {stoff}'
        # '<1 %' wird beim Speichern zu '&lt;1 %' und sprengt die Laenge
        v = re.sub(r'^\s*<\s*', 'unter ', v)
        return f"{v.replace('%', '').strip()} % {stoff}"
    stufe = {'sehr hoch': 'sehr hoher', 'hoch': 'hoher', 'mittel': 'mittlerer',
             'niedrig': 'niedriger', 'sehr niedrig': 'sehr niedriger'}.get(v.strip().lower())
    return f'{stufe} {stoff}-Gehalt' if stufe else ''

def _nennenswert(v):
    """0-1 % CBD ist der Normalfall und sagt nichts aus."""
    return not re.fullmatch(r'[0\s]*[-–~]?\s*[01]?\s*%?', v.replace(',', '').strip())

FORM = {'flüssig': 'in flüssiger Form', 'flüssigdünger': 'als Flüssigdünger',
        'granulat': 'als Granulat', 'pulver': 'als Pulver', 'stäbchen': 'als Stäbchen',
        'tabletten': 'als Tabletten', 'pellets': 'als Pellets', 'gel': 'als Gel',
        'paste': 'als Paste', 'spray': 'als Spray', 'konzentrat': 'als Konzentrat'}

PHASE = {'wachstum': 'Wachstumsphase', 'blüte': 'Blütephase',
         'vegetation': 'Vegetationsphase', 'vegetationsphase': 'Vegetationsphase',
         'keimung': 'Keimung', 'anzucht': 'Anzucht', 'spülen': 'Spülphase',
         'alle phasen': 'alle Phasen', 'ernte': 'Ernte', 'blütephase': 'Blütephase'}

def _form(v):
    return FORM.get(v.strip().lower(), '')

def _phase(v):
    teile = [PHASE.get(x.strip().lower(), '') for x in re.split(r',| und ', v)]
    teile = [t for t in teile if t]
    if not teile: return ''
    if teile == ['alle Phasen']: return 'für alle Phasen'
    return 'für die ' + (teile[0] if len(teile) == 1 else f'{teile[0]} und {teile[-1]}')

BAUSTEIN = [
    ('pa_thc-gehalt',       lambda v: _gehalt(v, 'THC')),
    ('pa_cbd-gehalt',       lambda v: _gehalt(v, 'CBD') if _nennenswert(v) else ''),
    ('pa_indica',           lambda v: f"{v.rstrip('% ').strip()} % Indica" if ZAHL.search(v) else ''),
    ('pa_sativa',           lambda v: f"{v.rstrip('% ').strip()} % Sativa" if ZAHL.search(v) else ''),
    ('pa_bluetezeit-tage',  lambda v: f'{v} Tage Blüte' if ZAHL.search(v) else ''),
    ('pa_ertrag',           lambda v: f'Ertrag {v}'),
    ('pa_genetik',          lambda v: 'aus ' + v.replace(' und ', ' x ')),
    ('pa_npk',              lambda v: f'NPK {v}'),
    ('pa_inhalt',           lambda v: v),
    ('pa_leistungsaufnahme',lambda v: v),
    ('pa_luftdurchsatz',    lambda v: v),
    ('pa_anschluss',        lambda v: f'Anschluss {v}'),
    ('pa_maschenweite',     lambda v: f'{v} Maschenweite'),
    ('pa_abmessungen',      lambda v: v),
    ('pa_laenge',           lambda v: v),
    ('pa_durchmesser',      lambda v: f'Ø {v}'),
    ('pa_material',         lambda v: f'aus {v}'),
    ('pa_form',             _form),
    ('pa_anwendungsphase',  _phase),
    ('pa_substrat',         lambda v: f'für {v}'),
    ('pa_passend-fuer',     lambda v: f'passend für {v}'),
    ('pa_aroma',            lambda v: f'Aroma {v}'),
    ('pa_geschmack',        lambda v: f'Geschmack {v}'),
    ('pa_effekte',          lambda v: f'Wirkung {v}'),
]

UNBRAUCHBAR = re.compile(r'Verhältnis|:\s|unbekannt|keine Angabe|k\.\s?A\.|n/a|^-+$', re.I)

def fakten(attr, hoechstens=6):
    raus = []
    for slug, formt in BAUSTEIN:
        werte = attr.get(slug) or []
        if not werte: continue
        v = sauber(' und '.join(werte[:2]))
        if not v or len(v) > 46 or UNBRAUCHBAR.search(v): continue
        gebaut = formt(v)
        if not gebaut: continue
        raus.append(gebaut)
        if len(raus) >= hoechstens: break
    return raus

def reihe(f):
    # 'und' vor dem letzten Glied - ausser ein Glied traegt selbst schon eins,
    # dann liest sich die Kette als reine Aufzaehlung besser.
    if not f: return ''
    if len(f) == 1: return f[0]
    if any(' und ' in x for x in f): return ', '.join(f)
    return ', '.join(f[:-1]) + ' und ' + f[-1]

# --- Enden aufraeumen ------------------------------------------------------

SCHLUSSWORT = re.compile(
    r'\s+(?:und|oder|sowie|mit|für|aus|von|zum|zur|im|in|auf|bei|als|der|die|das|'
    r'den|dem|ein|eine|einen|ist|sind|wird|werden|bis|ab|je|pro|x)$', re.I)

def saeubere_ende(t):
    t = t.rstrip(' ,;:–-')
    while True:
        k = SCHLUSSWORT.sub('', t)
        if k == t: break
        t = k.rstrip(' ,;:–-')
    return t

def kappe(s, grenze):
    if len(s) <= grenze: return s
    schnitt = s[:grenze]
    satz = schnitt.rsplit('. ', 1)[0] + '.' if '. ' in schnitt else ''
    # Ein sauberes Satzende ist schoener - aber nicht um den Preis des
    # halben Textes, nur weil ein einziges Zeichen zu viel war.
    if len(satz) >= grenze * 0.8:
        return satz
    wort = saeubere_ende(schnitt.rsplit(' ', 1)[0])
    # Ein angeschnittener Nebensatz am Ende ("..., ideal") wirkt wie ein Fehler.
    kopf, _, schwanz = wort.rpartition(', ')
    if kopf and len(schwanz) < 34 and not schwanz.endswith(('.', '!', '?')):
        wort = saeubere_ende(kopf)
    # "... und kräftige" - das Bindewort schleppt ein angeschnittenes Glied mit
    wort = saeubere_ende(re.sub(r'\s+(?:und|oder|sowie|bzw\.?)\s+\S+$', '', wort))
    return wort + ('' if wort.endswith(('.', '!', '?')) else '.')

# --- Titel -----------------------------------------------------------------

BALLAST = re.compile(
    r'\s*[-–,]\s*(?:1\s*(?:Stück|Pckg|Packung)|je\s+Pckg|Cannabis\s+Samen'
    r'|Hanfsamen|[Ff]eminisiert\w*)\s*$')

def kurzname(name, grenze):
    n = sauber(name)
    while len(n) > grenze:
        k = BALLAST.sub('', n)
        if k == n: break
        n = k
    if len(n) > grenze:
        n = n[:grenze].rsplit(' ', 1)[0]
    n = saeubere_ende(n)
    n = re.sub(r'\s*[-–,]\s*\d+\s*$', '', n)       # abgeschnittenes "... - 50"
    return n.rstrip(' ,;-–|')

def titel(name, attr):
    n = sauber(name)
    if len(n) > TITEL_MAX:
        return kurzname(n, TITEL_MAX)
    platz = TITEL_MAX - len(n) - 3                 # " – "
    if platz >= 8:
        for f in fakten(attr, 4):
            f = re.sub(r'^(?:aus|passend für) ', '', f)
            if len(f) <= platz and f.lower() not in n.lower():
                return f'{n} – {f}'
    return n

# --- Meta-Description ------------------------------------------------------

BOILERPLATE = re.compile(
    r'Hausmüll|WEEE|Elektrogerät|GPSR|Herstellerangab|Verpackungsg|Batterie|'
    r'Entsorg|Sammelstelle|Gewährleistung|Widerruf|Versandkosten|Jugendschutz|'
    r'18 Jahre|Rechtlich|Sammelwert', re.I)

def saetze(h, nur_anfang=True):
    # Der Textanfang traegt die Produktaussage; weiter hinten stehen
    # Pflegetipps und Pflichtangaben, die im Suchergebnis nichts verloren haben.
    roh = html.unescape(h or '')
    if nur_anfang:
        bloecke = re.findall(r'<p[^>]*>(.*?)</p>', roh, re.S)
        roh = ' '.join(bloecke[:2]) if bloecke else roh
    roh = re.sub(r'https?://\S+', ' ', roh)                 # eingebettete Links
    roh = re.sub(r'</(?:p|h[1-6]|li|div|td)>', '. ', roh, flags=re.I)
    t = re.sub(r'\s+', ' ', re.sub(r'<[^>]+>', ' ', roh)).strip()
    t = re.sub(r'\.\s*\.', '.', t)
    return [x.strip() for x in re.findall(r'[^.!?]{20,}?[.!?](?=\s|$)', t)
            if not BOILERPLATE.search(x) and x.strip()[:1].isupper()]

def meta(name, attr, beschreibung):
    n = sauber(name)
    f = fakten(attr, 6)
    s = f'{n}: {reihe(f)}.' if len(f) >= 2 else ''
    while len(s) > META_MAX and len(f) > 2:
        f = f[:-1]; s = f'{n}: {reihe(f)}.'
    if len(s) < META_ZIEL:
        gesehen = set()
        for satz in saetze(beschreibung) + saetze(beschreibung, False):
            if satz in gesehen: continue
            gesehen.add(satz)
            if not s and satz.lower().startswith(n.lower()[:18]):
                continue                           # Name doppelt wirkt albern
            kandidat = (s + ' ' + satz).strip()
            if len(kandidat) <= META_MAX: s = kandidat
            if len(s) >= META_ZIEL: break
    if not s:
        for satz in saetze(beschreibung) + saetze(beschreibung, False):
            if len(satz) <= META_MAX: s = satz; break
        s = s or n
    return kappe(s.strip(), META_MAX)
