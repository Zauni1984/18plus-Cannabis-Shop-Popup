# -*- coding: utf-8 -*-
"""Ordnet die Herstellerwerte den Shop-Produkten zu (Marke + normalisierter Name)."""
import json, re, unicodedata, collections

MARKE = {'hesi': 'hesi', 'plagron': 'plagron'}


def norm(s):
    s = unicodedata.normalize('NFKD', (s or '').lower()).replace('ß', 'ss')
    s = ''.join(c for c in s if not unicodedata.combining(c))
    s = re.sub(r'\b\d+(?:[.,]\d+)?\s*(l|liter|ml|kg|g|stk|stück)\b', ' ', s)
    s = re.sub(r'[^a-z0-9]+', ' ', s)
    # Markennamen und Fuellwoerter raus
    s = re.sub(r'\b(hesi|plagron|duenger|dunger)\b', ' ', s)
    return re.sub(r'\s+', ' ', s).strip()


D = json.load(open('d_web_daten.json'))
kat = {}
for u, v in D.items():
    if not v['werte']:
        continue
    kat.setdefault((v['marke'], norm(v['slug'].replace('-', ' '))), v)

P = json.load(open('kat_1132.json'))
M, offen = {}, []
for p in P:
    marken = [b['name'].lower() for b in (p.get('brands') or [])]
    m = next((MARKE[x] for x in MARKE if any(x in b for b in marken)), None)
    if not m:
        continue
    n = norm(p['name'])
    tr = kat.get((m, n))
    if tr:
        M[str(p['id'])] = {'name': p['name'], 'quelle': tr['slug'], 'attr':
                           {k: [v] for k, v in tr['werte'].items()}}
    else:
        offen.append((m, p['name'], n))

json.dump(M, open('d_web_map.json', 'w'), ensure_ascii=False, indent=1)
print(f'{len(M)} Treffer, {len(offen)} offen')
print('Katalogschluessel:', sorted({k[1] for k in kat})[:12])
for o in offen[:12]:
    print('   offen:', o[0], '|', o[1][:46], '|', o[2])
