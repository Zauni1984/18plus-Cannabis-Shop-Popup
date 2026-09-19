# -*- coding: utf-8 -*-
"""Plagron-Rollenpreise aus der Bloomtech-Liste (hanfjack.com).

Bloomtech ist der Lieferant; die Regel steht in bloomtech-aquamaster-README.md:
    B2B Kunde   = EK x 1,10
    Anbauverein = EK x 1,30

Der JTL-Export fuehrt keine Artikelbezeichnungen, ein Titelabgleich ist damit
nicht moeglich. Zwei Wege bleiben:

    SKU  - der Shop traegt teils direkt die Bloomtech-Artikelnummer
    EAN  - die Liste fuehrt zu 1895 Positionen eine EAN, der Shop ein gtin
"""
import csv, json, re
from decimal import Decimal, ROUND_HALF_UP
from collections import Counter

S='/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
F="/root/.claude/uploads/1414604f-ab94-5ea2-91e2-00ca46d10a2b/05fb1757-jtl-export-artikel-EK.csv"
B2B, AV = Decimal('1.10'), Decimal('1.30')
kauf = lambda d: str(d.quantize(Decimal('0.01'), rounding=ROUND_HALF_UP))

rows = list(csv.DictReader(open(F, encoding='utf-8'), delimiter=';'))
def geld(s):
    s=(s or '').strip().replace('.','').replace(',','.')
    try:
        w=Decimal(s); return w if w>0 else None
    except Exception: return None
nr2ek  = {}
ean2nr = {}
for r in rows:
    nr=(r.get('Artikelnummer') or '').strip()
    w =geld(r.get('JTL-Wawi: Händler Netto'))
    if nr and w: nr2ek[nr]=w
    e=(r.get('EAN/Barcode') or '').strip()
    if e and nr: ean2nr[e]=nr

pos=json.load(open(S+'plagron_pos.json'))
plan, pruefen, z = [], [], Counter()
for p in pos:
    if ((p['ww'].get('b2b_customer') or {}).get('own_price')):
        z['schon bepreist'] += 1; continue
    ek = weg = nr = None
    if p['sku'] in nr2ek:
        ek, weg, nr = nr2ek[p['sku']], 'SKU', p['sku']
    elif p['gtin'] and p['gtin'] in ean2nr and ean2nr[p['gtin']] in nr2ek:
        nr = ean2nr[p['gtin']]; ek, weg = nr2ek[nr], 'EAN'
    e = dict(art=p['art'], id=p['id'], parent=p['parent'], sku=p['sku'], gtin=p['gtin'],
             name=p['name'], status=p['status'], vk=str(p['vk'] or '0'),
             bloomtech_nr=nr, weg=weg)
    if ek is None:
        z['keine Preisquelle'] += 1
        pruefen.append(dict(e, grund='weder SKU noch EAN in der Bloomtech-Liste')); continue
    vk = Decimal(e['vk'])
    nb, na = kauf(ek*B2B), kauf(ek*AV)
    e.update(ek=str(ek), b2b=nb, anbauverein=na)
    if vk <= 0:
        z['ohne Verkaufspreis'] += 1
        pruefen.append(dict(e, grund='kein Verkaufspreis')); continue
    if ek > vk:
        z['EK ueber VK'] += 1
        pruefen.append(dict(e, grund='EK ueber VK')); continue
    if Decimal(na) > vk:
        # Das Plugin deckelt ohnehin auf den VK; dokumentiert als gedeckelt.
        z['Anbauverein ueber VK - gedeckelt'] += 1
        pruefen.append(dict(e, grund='Anbauverein ueber VK, Plugin deckelt')); continue
    plan.append(e); z['zu schreiben ueber ' + weg] += 1

json.dump(plan,    open(S+'plagron_plan.json','w'),   ensure_ascii=False, indent=1)
json.dump(pruefen, open(S+'plagron_pruefen.json','w'), ensure_ascii=False, indent=1)
for k,n in z.most_common(): print(f'{n:6d}  {k}')
