# -*- coding: utf-8 -*-
"""HEMPER, zweiter Durchgang: Zuordnung ueber die GTIN.

Die Shop-Artikel tragen ueberwiegend HJ-Nummern, die in der Lieferantenmappe
nicht vorkommen. Die EAN steht aber in beiden: im Shop als gtin, in der Mappe
als UPC. Darueber finden sich 206 Positionen, die ueber die SKU unauffindbar
waren.
"""
import sys, json, csv, glob, re
from decimal import Decimal, ROUND_HALF_UP
from collections import Counter
sys.path.insert(0,'/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad')
from hemper_map import lies_roh, ek_fuer

S='/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
B2B, AV = Decimal('1.10'), Decimal('1.35')
kauf = lambda d: str(d.quantize(Decimal('0.01'), rounding=ROUND_HALF_UP))

roh = lies_roh()
upc = json.load(open(S+'hemper_upc.json'))
pos = json.load(open(S+'hemper_pos.json'))

plan, pruefen, z = [], [], Counter()
for p in pos:
    if (p['ww'].get('b2b_customer') or {}).get('own_price'):
        z['schon bepreist'] += 1; continue
    sku_sheet, weg = None, None
    e, art, quelle = ek_fuer(p['sku'] or '', roh)
    if e is not None: sku_sheet, weg = quelle, 'SKU'
    else:
        g = p.get('gtin') or ''
        if g in upc:
            sku_sheet, weg = upc[g], 'GTIN'
            e, art, _ = ek_fuer(sku_sheet, roh)
    eintrag = dict(art=p['art'], id=p['id'], parent=p['parent'], sku=p['sku'],
                   gtin=p.get('gtin'), name=p['name'], status=p['status'],
                   vk=str(p['vk'] or '0'), sku_sheet=sku_sheet, weg=weg)
    if e is None:
        z['keine Preisquelle'] += 1
        pruefen.append(dict(eintrag, grund='weder SKU noch GTIN in der Mappe')); continue
    vk = Decimal(eintrag['vk'])
    nb, na = kauf(e*B2B), kauf(e*AV)
    eintrag.update(ek=str(e), ek_art=art, b2b=nb, anbauverein=na)
    if vk <= 0:
        z['ohne Verkaufspreis'] += 1
        pruefen.append(dict(eintrag, grund='kein Verkaufspreis')); continue
    if e > vk:
        z['EK ueber VK'] += 1
        pruefen.append(dict(eintrag, grund='EK ueber VK')); continue
    q = vk / e
    if q < Decimal('1.15') or q > Decimal('6'):
        z['Spanne auffaellig'] += 1
        pruefen.append(dict(eintrag, grund=f'VK/EK = {q:.2f}')); continue
    if Decimal(na) > vk:
        z['Anbauverein ueber VK'] += 1
        pruefen.append(dict(eintrag, grund='Anbauverein ueber VK')); continue
    plan.append(eintrag); z['zu schreiben ueber ' + weg] += 1

json.dump(plan,    open(S+'hemper_plan2.json','w'),   ensure_ascii=False, indent=1)
json.dump(pruefen, open(S+'hemper_pruefen2.json','w'), ensure_ascii=False, indent=1)
for k, n in z.most_common(): print(f'{n:6d}  {k}')
