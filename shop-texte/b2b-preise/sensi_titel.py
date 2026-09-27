# -*- coding: utf-8 -*-
"""Sensi Seeds ueber den Sortennamen zuordnen.

Die restlichen Shop-Artikel tragen HJ-Nummern. Die Mappe fuehrt aber den
Sortennamen, also laeuft die Bruecke ueber Sorte + Typ + Packungsgroesse.

Der Typ muss mit: "Hindu Kush" gibt es feminisiert, regulaer und automatic,
mit verschiedenen Einkaufspreisen. Wer ihn wegnormalisiert, mischt sie.
Zugeordnet wird nur, wenn genau eine Mappen-Zeile passt.
"""
import csv, json, re
from decimal import Decimal, ROUND_HALF_UP
from collections import defaultdict, Counter

S='/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
B2B, AV = Decimal('1.10'), Decimal('1.30')
kauf = lambda d: str(d.quantize(Decimal('0.01'), rounding=ROUND_HALF_UP))

def typ(s):
    s = (s or '').lower()
    if re.search(r'\bauto\w*', s):            return 'auto'
    if re.search(r'\bregul[aä]r\w*|\breg\b', s): return 'reg'
    return 'fem'

def sorte(s):
    s = (s or '').lower()
    s = re.sub(r'feminised|feminized|feminisiert|regular|regul[aä]r|automatic|autoflower\w*|'
               r'\bauto\b|seeds?|samen|cannabis|sensi|pack\w*|st[uü]ck|stk', ' ', s)
    s = re.sub(r'[-–]\s*\d+\s*$', ' ', s)
    s = re.sub(r'\b\d+\s*(er)?\b', ' ', s)
    s = s.replace('®', ' ').replace('™', ' ')
    return ' '.join(re.sub(r'[^a-z0-9#&+ ]', ' ', s).split())

# Mappe
mappe = defaultdict(list)
for r in csv.DictReader(open(S+'ek_sensi.csv', encoding='utf-8')):
    if (r.get('brand') or '').strip() != 'Sensi Seeds': continue
    p = (r.get('price') or '').strip()
    m = re.search(r'(\d+)', r.get('pack_size') or '')
    if not p or not m: continue
    try: w = Decimal(p)
    except Exception: continue
    mappe[(sorte(r.get('name')), typ(r.get('name')), m.group(1))].append(
        (w, (r.get('sku') or '').strip(), (r.get('name') or '').strip()))

daten = json.load(open(S+'sensi_vars.json'))
def groesse(v):
    for a in v.get('attributes', []):
        m = re.search(r'(\d+)', str(a.get('option') or ''))
        if m: return m.group(1)
    return None

plan, pruefen, z = [], [], Counter()
for pid, e in daten.items():
    k = e['kopf']
    stellen = []
    if not e['var']:
        stellen.append(dict(art='produkt', id=k['id'], parent=None, sku=k.get('sku'),
                            vk=k.get('regular_price'), status=k.get('status'),
                            ww=k.get('wwpro_wholesale_prices') or {}, gr=None))
    for v in e['var']:
        stellen.append(dict(art='variation', id=v['id'], parent=int(pid), sku=v.get('sku'),
                            vk=v.get('regular_price'), status=v.get('status'),
                            ww=v.get('wwpro_wholesale_prices') or {}, gr=groesse(v)))
    for st in stellen:
        if (st['ww'].get('b2b_customer') or {}).get('own_price'):
            z['schon bepreist'] += 1; continue
        eintrag = dict(art=st['art'], id=st['id'], parent=st['parent'], sku=st['sku'],
                       name=k['name'], sorte=sorte(k['name']), typ=typ(k['name']),
                       groesse=st['gr'], status=st['status'], vk=str(st['vk'] or '0'))
        if not st['gr']:
            z['ohne Mengenangabe'] += 1
            pruefen.append(dict(eintrag, grund='keine Packungsgroesse am Artikel')); continue
        schl = (eintrag['sorte'], eintrag['typ'], st['gr'])
        kand = mappe.get(schl, [])
        if not kand:
            z['kein Titeltreffer'] += 1
            pruefen.append(dict(eintrag, grund='Sorte, Typ und Groesse nicht in der Mappe')); continue
        preise = {x[0] for x in kand}
        if len(preise) > 1:
            z['mehrdeutig'] += 1
            pruefen.append(dict(eintrag, grund=f'{len(preise)} verschiedene EK fuer denselben Schluessel',
                                kandidaten=[[str(x[0]), x[1], x[2]] for x in kand])); continue
        ek = kand[0][0]
        vk = Decimal(eintrag['vk'])
        nb, na = kauf(ek*B2B), kauf(ek*AV)
        eintrag.update(ek=str(ek), sku_mappe=kand[0][1], mappe_name=kand[0][2],
                       b2b=nb, anbauverein=na)
        if vk <= 0:
            z['ohne Verkaufspreis'] += 1
            pruefen.append(dict(eintrag, grund='kein Verkaufspreis')); continue
        q = vk / ek
        if q < Decimal('1.15') or q > Decimal('4'):
            z['Spanne auffaellig'] += 1
            pruefen.append(dict(eintrag, grund=f'VK/EK = {q:.2f}')); continue
        if Decimal(na) > vk:
            z['Anbauverein ueber VK'] += 1
            pruefen.append(dict(eintrag, grund='Anbauverein ueber VK')); continue
        plan.append(eintrag); z['zu schreiben'] += 1

json.dump(plan,    open(S+'sensi_titel_plan.json','w'),    ensure_ascii=False, indent=1)
json.dump(pruefen, open(S+'sensi_titel_pruefen.json','w'), ensure_ascii=False, indent=1)
for k2, n in z.most_common(): print(f'{n:6d}  {k2}')
