# -*- coding: utf-8 -*-
"""Plant die fehlenden RQS-Rollenpreise auf hanfjack.com.

    B2B Kunde   = EK x 1,10
    Anbauverein = EK x 1,30

Zwei Quellen fuer den EK, streng getrennt gefuehrt:

1. "liste"    - der Wholesale-Preis aus RQS_Pricelist_2026 - Seeds Wholesale.
2. "herleitung" - fuer Packungsgroessen, die RQS nicht mehr fuehrt. Der Shop
   hat dafuer Verkaufspreise, die nach der Hausregel brutto = 2 x EK,
   netto = brutto / 1,07 gebildet wurden; die Regel trifft 431 der 461
   Positionen mit bekanntem EK. Rueckgerechnet ergeben 153 von 154 Faellen
   einen Betrag auf dem Viertel-Euro genau, und die Preiskurve pro Samen
   faellt so, wie es die Liste bei anderen Sorten auch tut. Der eine krumme
   Fall bleibt liegen.
"""
import sys, json, re
from decimal import Decimal, ROUND_HALF_UP
from collections import Counter, defaultdict
sys.path.insert(0,'/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad')
from rqs_ek import lies_ek

S='/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
R='/home/user/18plus-Cannabis-Shop-Popup/shop-texte/b2b-preise/'
B2B, AV = Decimal('1.10'), Decimal('1.30')
kauf = lambda d: str(d.quantize(Decimal('0.01'), rounding=ROUND_HALF_UP))

ek_liste = lies_ek()
zuord    = {p['produkt_id']: p for p in json.load(open(R+'rqs-plan.json'))}
daten    = json.load(open(S+'rqs_vars.json'))
nach_sorte = defaultdict(dict)
for sku, v in ek_liste.items():
    nach_sorte[v['sorte'].strip()][v['packung']] = (sku, v['ek'])

def groesse(v):
    for a in v.get('attributes', []):
        m = re.search(r'(\d+)', str(a.get('option') or ''))
        if m: return m.group(1)
    return None

plan, pruefen, z = [], [], Counter()
for pid, p in daten.items():
    pid = int(pid)
    sorte = (zuord.get(pid) or {}).get('quelle')
    for v in p['var']:
        w = v.get('wwpro_wholesale_prices') or {}
        if (w.get('b2b_customer') or {}).get('own_price'):
            z['schon bepreist'] += 1; continue
        gr = groesse(v)
        vk = Decimal(v.get('regular_price') or '0')
        e  = dict(id=v['id'], parent=pid, sku=v.get('sku'), name=p['name'],
                  sorte=sorte, groesse=gr, status=v.get('status'), vk=str(vk))
        if not gr:
            z['ohne Mengenangabe'] += 1
            pruefen.append(dict(e, grund='Variation ohne Mengen-Attribut')); continue
        if vk <= 0:
            z['ohne Verkaufspreis'] += 1
            pruefen.append(dict(e, grund='kein Verkaufspreis')); continue

        treffer = nach_sorte.get(sorte or '', {}).get(gr)
        if treffer:
            ek, herkunft, sku_rqs = treffer[1], 'liste', treffer[0]
        else:
            ek = (vk * Decimal('1.07') / 2).quantize(Decimal('0.01'), rounding=ROUND_HALF_UP)
            herkunft, sku_rqs = 'herleitung', None
            if (ek * 4) % 1 != 0:                 # kein glatter Viertel-Euro
                z['Herleitung unsauber'] += 1
                pruefen.append(dict(e, ek=str(ek), grund='Rueckrechnung ergibt keinen glatten Betrag'))
                continue
        nb, na = kauf(ek*B2B), kauf(ek*AV)
        e.update(ek=str(ek), herkunft=herkunft, sku_rqs=sku_rqs, b2b=nb, anbauverein=na)
        if Decimal(na) > vk:
            z['Anbauverein ueber VK'] += 1
            pruefen.append(dict(e, grund='Anbauverein ueber VK')); continue
        plan.append(e); z['zu schreiben: ' + herkunft] += 1

json.dump(plan,    open(S+'rqs_plan_neu.json','w'), ensure_ascii=False, indent=1)
json.dump(pruefen, open(S+'rqs_pruefen.json','w'),  ensure_ascii=False, indent=1)
for k, n in z.most_common(): print(f'{n:6d}  {k}')
