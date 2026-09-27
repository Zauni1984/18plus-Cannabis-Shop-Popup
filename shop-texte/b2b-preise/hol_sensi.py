import sys, json, urllib.parse
sys.path.insert(0,'/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad')
from com_api import ruf
S='/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
prod=json.load(open(S+'com_gtin_alle.json'))
ziel=[p for p in prod if 'sensi' in (p['name'] or '').lower()]
print(f'{len(ziel)} Produkte', flush=True)
raus={}
for i,p in enumerate(ziel):
    e={'kopf':p,'var':[]}
    if p['type']=='variable':
        q=urllib.parse.urlencode({'per_page':100,'status':'any',
            '_fields':'id,sku,regular_price,status,attributes,wwpro_wholesale_prices'})
        e['var']=ruf(f"products/{p['id']}/variations?{q}")
    raus[p['id']]=e
    if i%30==0: print(' ',i, flush=True)
json.dump(raus, open(S+'sensi_vars.json','w'), ensure_ascii=False)
print('fertig', len(raus))
