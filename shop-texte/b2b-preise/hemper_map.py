# -*- coding: utf-8 -*-
"""Ordnet jeder Shop-Position den richtigen HEMPER-EK zu.

Der Knackpunkt: eine Sheet-Zeile traegt bis zu drei Preise - pro Stueck, pro
Display und pro Karton. Der Shop verkauft teils die Display-Box unter genau
dieser SKU, teils das Einzelstueck unter derselben SKU mit Zusatz "-EINZEL".
Wer stumpf nach SKU zuordnet, schreibt den Stueckpreis auf die Box: bei
HMP-FT-GLASS-10MM-DISPLAY waeren das 1,50 statt 15,00 Euro.

Erschwerend heisst dieselbe Spalte je Reiter anders. In 956249339.csv steht
unter "Wholesale Price" der Displaypreis, in 1601411394.csv der Stueckpreis.
Die Zuordnung laeuft deshalb ueber Arithmetik: der Kartonpreis geteilt durch
die Anzahl Displays bzw. Stueck sagt, welcher Wert gemeint ist.
"""
import csv, glob, os, re
from decimal import Decimal

ORDNER = '/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/hemper/'

def geld(s):
    s = (s or '').replace('€', '').replace(',', '').strip()
    try:
        w = Decimal(s); return w if w > 0 else None
    except Exception: return None

def zahl(s):
    s = (s or '').replace(',', '').strip()
    try:
        w = Decimal(s); return w if w > 0 else None
    except Exception: return None

def _spalte(kopf, muss, darf_nicht=()):
    for i, c in enumerate(kopf):
        c = c.replace('\n', ' ')
        if all(re.search(m, c, re.I) for m in muss) and not any(re.search(d, c, re.I) for d in darf_nicht):
            return i
    return None

def lies_roh():
    """{SKU: {stueck, display, bezeichnung, datei, herkunft}}"""
    raus = {}
    for f in sorted(glob.glob(ORDNER + '*.csv')):
        z = list(csv.reader(open(f, encoding='utf-8')))
        if not z: continue
        kopf = [c.replace('\n', ' ').strip() for c in z[0]]
        i_case  = _spalte(kopf, [r'wholesale', r'case'])
        i_disp  = _spalte(kopf, [r'wholesale', r'display|tower'])
        i_unit  = _spalte(kopf, [r'wholesale'], [r'case', r'display', r'tower', r'pallet'])
        # Gesucht ist "Displays Per Case", nicht "Units Per Display" - beide
        # tragen das Wort display, nur die erste auch das Wort case.
        n_disp  = _spalte(kopf, [r'display|tower', r'case'],
                          [r'wholesale', r'cm\)|kg\)|weight|height|width|length'])
        n_je_disp = _spalte(kopf, [r'per', r'display|tower'],
                            [r'wholesale', r'case', r'cm\)|kg\)|weight|height|width|length'])
        n_unit  = _spalte(kopf, [r'unit|piece|cone', r'per'], [r'wholesale', r'msrp', r'cm|kg'])
        sku_sp  = kopf.index('SKU') if 'SKU' in kopf else 2
        for r in z[1:]:
            if len(r) <= sku_sp: continue
            sku = (r[sku_sp] or '').strip()
            if not sku: continue
            hol = lambda i, fn=geld: fn(r[i]) if i is not None and i < len(r) else None
            case, disp, unit = hol(i_case), hol(i_disp), hol(i_unit)
            je_disp   = hol(n_disp, zahl)          # Displays je Karton
            stk_je_disp = hol(n_je_disp, zahl)     # Stueck je Display

            if disp is None and unit is not None:
                # Nur eine Wholesale-Spalte. Der Kartonpreis verraet, was sie meint;
                # fehlt er, entscheidet die SKU: eine Zeile ueber eine Display-Box
                # nennt den Preis der Box, nicht den eines einzelnen Stuecks.
                ist_box = bool(re.search(r'^DISPLAY-|-DISPLAY$', sku, re.I)) or \
                          (r[1] or '').upper().startswith('DISPLAY') or \
                          'DISPLAY' in (r[1] or '').upper()
                if case is not None and je_disp and abs(case / je_disp - unit) < Decimal('0.02'):
                    disp, unit = unit, None
                elif case is None and ist_box:
                    disp, unit = unit, None
            # Fehlt der Stueckpreis, laesst er sich aus der Box herleiten.
            if unit is None and disp is not None and stk_je_disp:
                unit = (disp / stk_je_disp).quantize(Decimal('0.01'))
            eintrag = raus.get(sku)
            if eintrag:                               # SKU steht in mehreren Reitern
                eintrag['stueck']  = eintrag['stueck']  or unit
                eintrag['display'] = eintrag['display'] or disp
                eintrag['herkunft'].append(os.path.basename(f))
                continue
            raus[sku] = {'stueck': unit, 'display': disp,
                         'bezeichnung': (r[1] or '').strip(),
                         'datei': os.path.basename(f),
                         'herkunft': [os.path.basename(f)]}
    return raus

def ek_fuer(shop_sku, roh):
    """(EK, Art, Sheet-SKU) oder (None, Grund, None)."""
    s = shop_sku.strip()
    if s.upper().endswith('-EINZEL'):
        basis = s[:-len('-EINZEL')]
        # Im Sheet steht dieselbe Ware als Display: mal mit Suffix
        # (HMP-FT-GLASS-10MM-DISPLAY), mal mit Praefix (DISPLAY-HMP-GT-...).
        z = None
        for kandidat in (basis, basis + '-DISPLAY', 'DISPLAY-' + basis):
            z = roh.get(kandidat)
            if z: basis = kandidat; break
        if not z: return None, 'Basis-SKU nicht im Sheet', None
        if z['stueck'] is None: return None, 'kein Stueckpreis im Sheet', None
        return z['stueck'], 'Stueck', basis
    z = roh.get(s)
    if not z: return None, 'SKU nicht im Sheet', None
    ist_box = bool(re.search(r'^DISPLAY-|-DISPLAY$', s, re.I)) or \
              (z['bezeichnung'] or '').upper().startswith('DISPLAY')
    if ist_box:
        if z['display'] is not None: return z['display'], 'Display', s
        if z['stueck']  is not None: return z['stueck'],  'Stueck, kein Displaypreis vorhanden', s
        return None, 'kein Preis im Sheet', None
    if z['stueck'] is not None:  return z['stueck'],  'Stueck', s
    if z['display'] is not None: return z['display'], 'Display', s
    return None, 'kein Preis im Sheet', None
