# -*- coding: utf-8 -*-
"""Plant die HEMPER-Rollenpreise fuer hanfjack.com.

    B2B Kunde   = EK x 1,10
    Anbauverein = EK x 1,35
Kaufmaennisch auf zwei Nachkommastellen, netto.
"""
import sys, json
from decimal import Decimal, ROUND_HALF_UP
from collections import Counter
sys.path.insert(0,'/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad')
from hemper_map import lies_roh, ek_fuer

S='/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
B2B, AV = Decimal('1.10'), Decimal('1.35')

def kauf(d): return str(d.quantize(Decimal('0.01'), rounding=ROUND_HALF_UP))

roh = lies_roh()
pos = []
for p in json.load(open(S+'com_alle_prod.json')):
    if p['type'] != 'variable' and (p['sku'] or '').strip():
        pos.append(dict(art='produkt', id=p['id'], parent=None, sku=p['sku'].strip(),
                        vk=p.get('regular_price'), name=p['name'], status=p['status'],
                        ww=p.get('wwpro_wholesale_prices') or {}))
for t in (0,1):
    for pid, pp in json.load(open(S+f'com_allvars_{t}.json')).items():
        for v in pp['var']:
            if (v['sku'] or '').strip():
                pos.append(dict(art='variation', id=v['id'], parent=int(pid), sku=v['sku'].strip(),
                                vk=v.get('regular_price'), name=pp['name'], status=v.get('status'),
                                ww=v.get('wwpro_wholesale_prices') or {}))

plan, pruefen, z = [], [], Counter()
for p in pos:
    e, art, quelle = ek_fuer(p['sku'], roh)
    if e is None: continue
    z['im Sheet gefunden'] += 1
    z['  Art: ' + art] += 1
    nb, na = kauf(e*B2B), kauf(e*AV)
    ab = (p['ww'].get('b2b_customer') or {}).get('own_price') or ''
    aa = (p['ww'].get('anbauverein') or {}).get('own_price') or ''
    def gleich(a, b):
        try: return a != '' and Decimal(a) == Decimal(b)
        except Exception: return False
    if gleich(ab, nb) and gleich(aa, na):
        z['bereits korrekt'] += 1; continue
    vk = Decimal(p['vk'] or '0')
    eintrag = dict(art=p['art'], id=p['id'], parent=p['parent'], sku=p['sku'],
                   name=p['name'], status=p['status'], ek=str(e), quelle=quelle,
                   ek_art=art, b2b=nb, anbauverein=na, vk=str(vk),
                   alt_b2b=ab, alt_av=aa)
    if vk <= 0:
        z['  ohne VK - zurueckgestellt'] += 1; pruefen.append(dict(eintrag, grund='kein Verkaufspreis')); continue
    if e > vk:
        z['  EK ueber VK - zurueckgestellt'] += 1; pruefen.append(dict(eintrag, grund='EK ueber VK')); continue
    q = vk / e
    if q < Decimal('1.15') or q > Decimal('6'):
        z['  Spanne auffaellig - zurueckgestellt'] += 1
        pruefen.append(dict(eintrag, grund=f'VK/EK = {q:.2f}')); continue
    if Decimal(na) > vk:
        z['  Anbauverein ueber VK - zurueckgestellt'] += 1
        pruefen.append(dict(eintrag, grund='Anbauverein ueber VK')); continue
    if ab or aa: z['  davon Korrektur'] += 1
    plan.append(eintrag)
    z['zu schreiben'] += 1

json.dump(plan,    open(S+'hemper_plan.json','w'),   ensure_ascii=False, indent=1)
json.dump(pruefen, open(S+'hemper_pruefen.json','w'), ensure_ascii=False, indent=1)
for k, n in z.most_common(): print(f'{n:6d}  {k}')
