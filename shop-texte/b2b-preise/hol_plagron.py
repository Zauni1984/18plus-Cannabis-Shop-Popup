import sys, json, urllib.parse
sys.path.insert(0,'/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad')
from com_api import ruf
S='/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
prod=json.load(open(S+'com_gtin_alle.json'))
ziel=[p for p in prod if 'plagron' in (p['name'] or '').lower()]
print(f'{len(ziel)} Plagron-Produkte', flush=True)
raus={}
for i,p in enumerate(ziel):
    d=ruf(f"products/{p['id']}?_fields=id,name,sku,type,status,gtin,mpn,regular_price,meta_data,wwpro_wholesale_prices")
    e={'kopf':d,'var':[]}
    if p['type']=='variable':
        q=urllib.parse.urlencode({'per_page':100,'status':'any',
            '_fields':'id,sku,gtin,mpn,regular_price,status,attributes,meta_data,wwpro_wholesale_prices'})
        e['var']=ruf(f"products/{p['id']}/variations?{q}")
    raus[p['id']]=e
    if i%25==0: print(' ',i, flush=True)
json.dump(raus, open(S+'plagron.json','w'), ensure_ascii=False)
print('fertig', len(raus))
