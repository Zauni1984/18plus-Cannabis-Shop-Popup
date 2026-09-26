# -*- coding: utf-8 -*-
"""Berechnet B2B- und Anbauverein-Preise fuer hanfjack.com.

Regel (Tiger One, tigerone-b2b.md):
    B2B Kunde   = EK x 1,10
    Anbauverein = EK x 1,30
Kaufmaennisch auf zwei Nachkommastellen, netto.
"""
import csv, json
from decimal import Decimal, ROUND_HALF_UP
from collections import Counter, defaultdict

S = '/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
B2B, AV = Decimal('1.10'), Decimal('1.30')

def kauf(d):
    """Kaufmaennisch runden - Pythons round() rundet zur geraden Zahl."""
    return str(d.quantize(Decimal('0.01'), rounding=ROUND_HALF_UP))

ek = {}
for r in csv.DictReader(open(S + 'ek_neu.csv', encoding='utf-8')):
    p = (r['price'] or '').strip()
    if r['sku'] and p:
        try: ek[r['sku']] = Decimal(p)
        except Exception: pass

daten = {}
for t in (0, 1): daten.update(json.load(open(S + f'com_allvars_{t}.json')))

plan = defaultdict(list)
z = Counter()
ohne_ek, unter_vk = [], []
for pid, p in daten.items():
    for v in p['var']:
        sku = (v.get('sku') or '').strip()
        z['Variationen gesamt'] += 1
        if not sku or sku not in ek:
            z['ohne EK im Sheet'] += 1
            if len(ohne_ek) < 8: ohne_ek.append((sku, p['name'][:40]))
            continue
        e = ek[sku]
        if e <= 0:
            z['EK ist 0'] += 1; continue
        neu_b, neu_a = kauf(e * B2B), kauf(e * AV)
        w = v.get('wwpro_wholesale_prices') or {}
        alt_b = (w.get('b2b_customer') or {}).get('own_price') or ''
        alt_a = (w.get('anbauverein') or {}).get('own_price') or ''
        # Der Shop speichert "49.5", berechnet wird "49.50" - das ist derselbe
        # Preis. Ein Textvergleich wuerde 140 sinnlose Schreibvorgaenge ausloesen.
        def gleich(a, b):
            try: return a != '' and Decimal(a) == Decimal(b)
            except Exception: return False
        if gleich(alt_b, neu_b) and gleich(alt_a, neu_a):
            z['bereits korrekt'] += 1; continue
        z['zu schreiben'] += 1
        if alt_b or alt_a: z['  davon Korrektur'] += 1
        # Woo Wholesale Pro deckelt nie ueber den reguaeren Preis hinaus
        vk = Decimal(v.get('regular_price') or '0')
        if vk and Decimal(neu_a) > vk:
            z['  Anbauverein ueber VK'] += 1
            unter_vk.append((sku, str(vk), neu_a))
        plan[pid].append({'id': v['id'], 'sku': sku, 'ek': str(e),
                          'b2b': neu_b, 'anbauverein': neu_a,
                          'vk': str(vk), 'status': v.get('status')})

json.dump(plan, open(S + 'preis_plan.json', 'w'), ensure_ascii=False, indent=1)
for k, n in z.most_common(): print(f'{n:6d}  {k}')
print(f'\n{len(plan)} Elternprodukte betroffen')
print('\nBeispiele ohne EK:', ohne_ek[:5])
if unter_vk:
    print(f'\nAnbauverein ueber VK ({len(unter_vk)}):')
    for x in unter_vk[:8]: print('   ', x)
