# -*- coding: utf-8 -*-
"""Schreibt B2B- und Anbauvereinpreise auf hanfjack.com.

Es wird ausschliesslich meta_data geschickt - Verkaufspreise, min_age und
alles andere bleiben unberuehrt.
"""
import sys, json, time
sys.path.insert(0,'/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad')
from com_api import ruf

S='/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
plan=json.load(open(S+'preis_plan.json'))
nur   = sys.argv[1] if len(sys.argv)>1 else 'alle'      # SKU-Praefix oder 'alle'
TEILE = int(sys.argv[2]) if len(sys.argv)>2 else 1
TEIL  = int(sys.argv[3]) if len(sys.argv)>3 else 0

aufgaben=[]
for nr,(pid,zeilen) in enumerate(sorted(plan.items(), key=lambda x:int(x[0]))):
    z = zeilen if nur=='alle' else [x for x in zeilen if x['sku'].startswith(nur)]
    if not z: continue
    if nur=='alle' and len(aufgaben) % TEILE != TEIL: pass
    aufgaben.append((int(pid), z))
if nur=='alle':
    aufgaben=[a for i,a in enumerate(aufgaben) if i % TEILE == TEIL]

print(f'{sum(len(z) for _,z in aufgaben)} Variationen in {len(aufgaben)} Produkten', flush=True)
ok=fehler=0; beleg={}
for i,(pid,zeilen) in enumerate(aufgaben):
    update=[{'id':z['id'],'meta_data':[
                {'key':'_wwpro_price_b2b_customer','value':z['b2b']},
                {'key':'_wwpro_price_anbauverein', 'value':z['anbauverein']}]}
            for z in zeilen]
    try:
        a=ruf(f'products/{pid}/variations/batch', {'update':update}, 'POST')
        for e in a.get('update',[]):
            w=e.get('wwpro_wholesale_prices') or {}
            q=[(w.get(r) or {}).get('source') for r in ('b2b_customer','anbauverein')]
            if e.get('error') or q!=['variation','variation']:
                fehler+=1; print('  FEHLER',e.get('id'),e.get('sku'),e.get('error') or q, flush=True)
            else:
                ok+=1
                beleg[e.get('sku')]=[e['id'],
                    (w['b2b_customer'] or {}).get('own_price'),
                    (w['anbauverein'] or {}).get('own_price')]
    except Exception as ex:
        fehler+=len(update); print('  BLOCK-FEHLER',pid,repr(ex)[:110], flush=True)
    if i % 25 == 0: print(f'  Teil {TEIL}: {ok} geschrieben, {fehler} Fehler', flush=True)
    time.sleep(0.3)
json.dump(beleg, open(S+f'preis_beleg_{nur}_{TEIL}.json','w'), ensure_ascii=False)
print(f'fertig: {ok} geschrieben, {fehler} Fehler')
