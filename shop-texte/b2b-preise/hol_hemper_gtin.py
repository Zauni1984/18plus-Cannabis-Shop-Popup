import sys, json, urllib.parse
sys.path.insert(0,'/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad')
from com_api import ruf
S='/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
prod=json.load(open(S+'com_alle_prod2.json'))
ziel=[p for p in prod if 'hemper' in (p['name'] or '').lower()]
print(f'{len(ziel)} HEMPER-Produkte', flush=True)
raus={}
for i,p in enumerate(ziel):
    d=ruf(f"products/{p['id']}?_fields=id,name,sku,type,status,gtin,regular_price,wwpro_wholesale_prices")
    eintrag={'kopf':d, 'var':[]}
    if p['type']=='variable':
        q=urllib.parse.urlencode({'per_page':100,'status':'any',
            '_fields':'id,sku,gtin,regular_price,status,attributes,wwpro_wholesale_prices'})
        eintrag['var']=ruf(f"products/{p['id']}/variations?{q}")
    raus[p['id']]=eintrag
    if i%40==0: print(' ',i, flush=True)
json.dump(raus, open(S+'hemper_gtin.json','w'), ensure_ascii=False)
print('fertig', len(raus))
