# -*- coding: utf-8 -*-
"""Schreibt die HEMPER-Rollenpreise auf hanfjack.com.

Einfache Produkte gehen einzeln ueber /products/{id}, Variationen gebuendelt
ueber /products/{parent}/variations/batch. Geschickt wird nur meta_data.
"""
import sys, json, time
from decimal import Decimal
from collections import defaultdict
sys.path.insert(0,'/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad')
from com_api import ruf
S='/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'

plan=json.load(open(S+'rqs_plan_neu.json'))
meta=lambda x: [{'key':'_wwpro_price_b2b_customer','value':x['b2b']},
                {'key':'_wwpro_price_anbauverein', 'value':x['anbauverein']}]
def geprueft(antwort, erwartet):
    # Der Shop gibt "16.5" zurueck, berechnet wurde "16.50" - derselbe Preis.
    # Ein Textvergleich meldet hier reihenweise Fehler, die keine sind.
    w=antwort.get('wwpro_wholesale_prices') or {}
    b=(w.get('b2b_customer') or {}); a=(w.get('anbauverein') or {})
    def gleich(x, y):
        try: return Decimal(str(x)) == Decimal(str(y))
        except Exception: return False
    return (gleich(b.get('own_price'), erwartet['b2b'])
            and gleich(a.get('own_price'), erwartet['anbauverein'])
            and b.get('source') in ('product','variation')
            and a.get('source') in ('product','variation'))

ok=fehler=0; beleg={}
einfach=[]
print(f'{len(einfach)} einfache Produkte', flush=True)
for i,x in enumerate(einfach):
    try:
        a=ruf(f"products/{x['id']}", {'meta_data':meta(x)}, 'PUT')
        if geprueft(a,x): ok+=1; beleg[str(x['id'])]=[x['sku'],x['b2b'],x['anbauverein'],x['herkunft']]
        else: fehler+=1; print('  FEHLER',x['id'],x['sku'],(a.get('wwpro_wholesale_prices') or {}), flush=True)
    except Exception as e:
        fehler+=1; print('  FEHLER',x['id'],x['sku'],repr(e)[:90], flush=True)
    if i%25==0: print(f'  {ok} ok, {fehler} Fehler', flush=True)
    time.sleep(0.25)

nach_eltern=defaultdict(list)
for x in plan:
    nach_eltern[x['parent']].append(x)
print(f'{sum(len(v) for v in nach_eltern.values())} Variationen in {len(nach_eltern)} Produkten', flush=True)
for i,(pid,zeilen) in enumerate(nach_eltern.items()):
    try:
        a=ruf(f'products/{pid}/variations/batch',
              {'update':[{'id':x['id'],'meta_data':meta(x)} for x in zeilen]}, 'POST')
        nach={e['id']:e for e in a.get('update',[])}
        for x in zeilen:
            e=nach.get(x['id'])
            if e and not e.get('error') and geprueft(e,x):
                ok+=1; beleg[str(x['id'])]=[x['sku'],x['b2b'],x['anbauverein'],x['herkunft']]
            else:
                fehler+=1; print('  FEHLER',x['id'],x['sku'],(e or {}).get('error'), flush=True)
    except Exception as ex:
        fehler+=len(zeilen); print('  BLOCK-FEHLER',pid,repr(ex)[:90], flush=True)
    if i%25==0: print(f'  {ok} ok, {fehler} Fehler', flush=True)
    time.sleep(0.3)
json.dump(beleg, open(S+'rqs_beleg.json','w'), ensure_ascii=False)
print(f'fertig: {ok} geschrieben, {fehler} Fehler')
