import sys, json, urllib.parse
from decimal import Decimal
from collections import defaultdict
sys.path.insert(0,'/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad')
from com_api import ruf
S='/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
plan=json.load(open(sys.argv[1]))
nach=defaultdict(list)
for x in plan: nach[x['parent']].append(x)
g=lambda a,b: Decimal(str(a if a not in (None,'') else -1))==Decimal(b)
ok=0; ab=[]
for pid,zeilen in nach.items():
    q=urllib.parse.urlencode({'per_page':100,'status':'any','_fields':'id,sku,wwpro_wholesale_prices'})
    v={y['id']:y for y in ruf(f'products/{pid}/variations?{q}')}
    for x in zeilen:
        w=(v.get(x['id']) or {}).get('wwpro_wholesale_prices') or {}
        b=(w.get('b2b_customer') or {}); a=(w.get('anbauverein') or {})
        if (g(b.get('own_price'),x['b2b']) and g(a.get('own_price'),x['anbauverein'])
            and g(b.get('price'),x['b2b']) and g(a.get('price'),x['anbauverein'])): ok+=1
        else: ab.append((x['sku'], x['b2b'], b.get('own_price'), a.get('own_price')))
print(f'{ok} bestaetigt, {len(ab)} abweichend')
for x in ab[:8]: print('  ', x)
