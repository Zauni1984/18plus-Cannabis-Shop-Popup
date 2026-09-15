# -*- coding: utf-8 -*-
"""Autoflowering-Sorten bluehen unabhaengig vom Lichtzyklus. Ein fester
Erntemonat existiert dort nicht - der Shop fuehrt dafuer seit den ersten
Markenlaeufen den Wert "Ganzjaehrig (Auto)". Dieses Skript traegt ihn bei
allen Autos nach, bei denen das Feld noch leer ist.
"""
import json, sys, time, urllib.request
sys.path.insert(0, '.')
from rq_apply import AUTH

KAT = 532          # Kategorie "Samen"
prods, page = [], 1
while True:
    u = (f'https://hanfjack.de/wp-json/wc/v3/products?per_page=100&page={page}'
         f'&category={KAT}&status=publish&_fields=id,name,attributes')
    d = json.load(urllib.request.urlopen(urllib.request.Request(u, headers=AUTH), timeout=90))
    prods += d
    if len(d) < 100:
        break
    page += 1
    time.sleep(0.4)

M = {}
for p in prods:
    a = {(x.get('slug') or ''): (x.get('options') or []) for x in p.get('attributes', [])}
    if a.get('pa_erntemonat'):
        continue
    if any('auto' in v.lower() for v in a.get('pa_variante', [])):
        M[str(p['id'])] = {'name': p['name'], 'attr': {'pa_erntemonat': ['Ganzjährig (Auto)']}}

json.dump(M, open('auto_ernte_map.json', 'w'), ensure_ascii=False, indent=1)
print(f'{len(prods)} Samen geprueft, {len(M)} Autos ohne Erntemonat')
