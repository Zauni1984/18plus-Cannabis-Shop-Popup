# -*- coding: utf-8 -*-
"""Zaehlt, wie viele Samen je Attribut befuellt sind."""
import json, sys, time, urllib.request, collections
sys.path.insert(0, '.')
from rq_apply import AUTH
import attr_schema as A

KAT = 342          # Kategorie "Samen"
prods, page = [], 1
while True:
    u = (f'https://hanfjack.de/wp-json/wc/v3/products?per_page=100&page={page}'
         f'&category={KAT}&status=publish&_fields=id,name,sku,attributes,brands')
    d = json.load(urllib.request.urlopen(urllib.request.Request(u, headers=AUTH), timeout=90))
    prods += d
    if len(d) < 100:
        break
    page += 1
    time.sleep(0.4)

c = collections.Counter()
for p in prods:
    for a in p.get('attributes', []):
        if (a.get('slug') or '') in A.ATTR_ID and a.get('options'):
            c[a['slug']] += 1
n = len(prods)
print(f'{n} veroeffentlichte Samen\n')
print(f'{"Attribut":24} {"befuellt":>8} {"Abdeckung":>10}')
for slug in sorted(A.ATTR_ID, key=lambda s: -c[s]):
    print(f'{slug:24} {c[slug]:8} {c[slug]*100//n:9} %')
json.dump({'anzahl': n, 'abdeckung': dict(c)}, open('abdeckung.json', 'w'), ensure_ascii=False, indent=1)
