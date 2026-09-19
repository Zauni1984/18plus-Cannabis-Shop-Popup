# -*- coding: utf-8 -*-
"""Zieht die Geraetekompatibilitaet aus dem Produktnamen.

Bei Ersatzteilen ist das die einzige Angabe, nach der wirklich gefiltert wird.
Gelesen wird ausschliesslich der Produktname: Im Fliesstext steht "fuer diesen
Fall" oder "fuer gewerbliche Kaeufer" - das sind keine Geraetebezeichnungen.
"""
import re

# Marke -> Modellmuster. Reihenfolge zaehlt, das erste Muster gewinnt.
MODELLE = [
  ('Twister',       [r'Tandem\s*T4', r'\bT[246]\b', r'Cure\s*Puck', r'Trim\s*Saver']),
  ('Trimpro',       [r'Automatik\s*XL', r'\bAutomatik\b', r'Rotor\s*XL', r'\bRotor\b',
                     r'Workstation', r'Trimbox', r'\bOriginal\b', r'\bXL\b']),
  ('Trimbox',       [r'Workstation', r'Trimbox']),
  ('CenturionPro',  [r'Tabletop', r'\bMini\b', r'\bOriginal\b', r'Gladiator', r'Silver\s*Bullet']),
  ('Homebox',       [r'Ambient\s*\w+', r'Vista\s*\w+', r'Fixture\s*Poles']),
  ('AC Infinity',   [r'\bUIS\b', r'Cloudline\s*\w*', r'Cloudlab\s*\w*', r'Cloudforge\s*\w*']),
  ('Nutriculture',  [r'X-?Stream', r'Wilma\s*\w*', r'Amazon\s*\w*']),
  ('Pure Factory',  [r'Top\s*Spinner', r'\bRotor\b']),
  ('Spider Farmer', [r'SF-?\w+', r'SE-?\w+', r'G\d{4}\w*']),
]


def passend(name, marken):
    """-> 'Twister T6' oder None"""
    for marke, muster in MODELLE:
        if not any(marke.lower().split()[0] in m.lower() for m in marken) \
           and marke.lower().split()[0] not in name.lower():
            continue
        for mu in muster:
            m = re.search(mu, name, re.I)
            if m:
                modell = re.sub(r'\s+', ' ', m.group(0)).strip()
                modell = modell.upper() if re.fullmatch(r'[a-z]{2,4}\d*', modell, re.I) \
                    and len(modell) <= 4 else modell.title()
                # Kuerzel bleiben gross: XL, UIS, SF
                modell = re.sub(r'\b(Xl|Xxl|Uis|Sf|Se)\b',
                                lambda m: m.group(1).upper(), modell)
                return f'{marke} {modell}'
    return None


if __name__ == '__main__':
    import json, collections
    D = json.load(open('luecke_desc.json'))
    rest = {str(p['id']) for p in json.load(open('rest_ohne_attr.json'))}
    M, c = {}, collections.Counter()
    for p in D:
        if str(p['id']) not in rest:
            continue
        marken = [b['name'] for b in (p.get('brands') or [])]
        w = passend(p['name'], marken)
        if w:
            M[str(p['id'])] = {'name': p['name'], 'attr': {'pa_passend-fuer': [w]}}
            c[w] += 1
    json.dump(M, open('passend_map.json', 'w'), ensure_ascii=False, indent=1)
    print(f'{len(M)} Produkte mit Gerätezuordnung')
    for w, n in c.most_common(14):
        print(f'   {n:3}  {w}')
