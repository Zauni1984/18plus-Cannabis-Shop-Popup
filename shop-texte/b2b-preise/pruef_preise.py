import sys, json, urllib.parse
from decimal import Decimal
sys.path.insert(0,'/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad')
from com_api import ruf
S='/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
TEILE=int(sys.argv[1]); TEIL=int(sys.argv[2])
plan=json.load(open(S+'preis_plan.json'))
ids=sorted(plan, key=lambda x:int(x))
ok=fehl=0; abweichung=[]
for i,pid in enumerate(ids):
    if i % TEILE != TEIL: continue
    q=urllib.parse.urlencode({'per_page':100,'status':'any',
        '_fields':'id,sku,regular_price,wwpro_wholesale_prices'})
    v={x['id']:x for x in ruf(f'products/{pid}/variations?{q}')}
    for z in plan[pid]:
        x=v.get(z['id'])
        w=(x or {}).get('wwpro_wholesale_prices') or {}
        b=(w.get('b2b_customer') or {}); a=(w.get('anbauverein') or {})
        try:
            passt = (Decimal(b.get('own_price') or '-1')==Decimal(z['b2b'])
                     and Decimal(a.get('own_price') or '-1')==Decimal(z['anbauverein'])
                     and b.get('source')=='variation' and a.get('source')=='variation'
                     and Decimal(b.get('price') or '-1')==Decimal(z['b2b'])
                     and Decimal(a.get('price') or '-1')==Decimal(z['anbauverein']))
        except Exception: passt=False
        if passt: ok+=1
        else:
            fehl+=1
            if len(abweichung)<6: abweichung.append((z['sku'], z['b2b'], z['anbauverein'],
                b.get('own_price'), a.get('own_price'), b.get('source'), b.get('price'), a.get('price')))
    if i % 60 == 0: print(f'  Teil {TEIL}: {ok} ok, {fehl} abweichend', flush=True)
print(f'fertig Teil {TEIL}: {ok} ok, {fehl} abweichend')
for x in abweichung: print('  ', x)
