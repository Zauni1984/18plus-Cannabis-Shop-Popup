# -*- coding: utf-8 -*-
"""Rollenpreise aus der Tiger-One-Mappe, zweiter Zuordnungsweg.

    B2B Kunde   = EK x 1,10
    Anbauverein = EK x 1,30

Der erste Lauf verglich nur die Spalte "sku" der Mappe mit der Shop-SKU.
Sensi Seeds faellt dabei durch: dort steht in der Mappe eine Hausnummer
(SEN1560022), waehrend der Shop die Struktur parent_sku + Packungsgroesse
benutzt (sensi-afghan-fem-10). Beide Wege laufen hier nebeneinander.

Der Satz ist nicht geraten: die bereits bepreisten Sensi-Artikel tragen
durchgaengig 1,10 und 1,30.
"""
import csv, json, re
from decimal import Decimal, ROUND_HALF_UP
from collections import Counter

S='/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
B2B, AV = Decimal('1.10'), Decimal('1.30')
kauf = lambda d: str(d.quantize(Decimal('0.01'), rounding=ROUND_HALF_UP))

def paket(s):
    m = re.search(r'(\d+)', s or '')
    return m.group(1) if m else None

# Mappe: beide Schluessel auf denselben EK zeigen lassen
ek, marke, sorte = {}, {}, {}
for r in csv.DictReader(open(S+'ek_sensi.csv', encoding='utf-8')):
    p = (r.get('price') or '').strip()
    if not p: continue
    try: w = Decimal(p)
    except Exception: continue
    if w <= 0: continue
    s  = (r.get('sku') or '').strip()
    ps = (r.get('parent_sku') or '').strip()
    n  = paket(r.get('pack_size'))
    for schluessel in [x for x in (s, f'{ps}-{n}' if ps and n else None) if x]:
        ek.setdefault(schluessel, w)
        marke.setdefault(schluessel, (r.get('brand') or '').strip())
        sorte.setdefault(schluessel, (r.get('name') or '').strip())

pos = []
for p in json.load(open(S+'com_gtin_alle.json')):
    if p['type'] != 'variable' and (p.get('sku') or '').strip():
        pos.append(dict(art='produkt', id=p['id'], parent=None, sku=p['sku'].strip(),
                        name=p['name'], vk=p.get('regular_price'), status=p['status'],
                        ww=p.get('wwpro_wholesale_prices') or {}))
for t in (0, 1):
    for pid, q in json.load(open(S+f'com_allvars2_{t}.json')).items():
        for v in q['var']:
            if (v.get('sku') or '').strip():
                pos.append(dict(art='variation', id=v['id'], parent=int(pid), sku=v['sku'].strip(),
                                name=q['name'], vk=v.get('regular_price'), status=v.get('status'),
                                ww=v.get('wwpro_wholesale_prices') or {}))

plan, pruefen, z = [], [], Counter()
for p in pos:
    if (p['ww'].get('b2b_customer') or {}).get('own_price'):
        continue
    e = ek.get(p['sku'])
    if e is None: continue
    z['zugeordnet'] += 1
    vk = Decimal(p['vk'] or '0')
    nb, na = kauf(e*B2B), kauf(e*AV)
    eintrag = dict(art=p['art'], id=p['id'], parent=p['parent'], sku=p['sku'], name=p['name'],
                   marke=marke.get(p['sku']), sorte=sorte.get(p['sku']), status=p['status'],
                   ek=str(e), vk=str(vk), b2b=nb, anbauverein=na)
    if vk <= 0:
        z['ohne Verkaufspreis'] += 1
        pruefen.append(dict(eintrag, grund='kein Verkaufspreis')); continue
    if e > vk:
        z['EK ueber VK'] += 1
        pruefen.append(dict(eintrag, grund='EK ueber VK')); continue
    if Decimal(na) > vk:
        z['Anbauverein ueber VK'] += 1
        pruefen.append(dict(eintrag, grund='Anbauverein ueber VK')); continue
    plan.append(eintrag); z['zu schreiben'] += 1

json.dump(plan,    open(S+'sensi_plan.json','w'),    ensure_ascii=False, indent=1)
json.dump(pruefen, open(S+'sensi_pruefen.json','w'), ensure_ascii=False, indent=1)
for k, n in z.most_common(): print(f'{n:6d}  {k}')
print('\nnach Marke:', dict(Counter(x['marke'] for x in plan).most_common(8)))
