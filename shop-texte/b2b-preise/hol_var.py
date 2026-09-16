import sys, json, urllib.parse
sys.path.insert(0,'/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad')
from com_api import ruf
S='/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
TEILE=int(sys.argv[1]); TEIL=int(sys.argv[2])
eltern=json.load(open(S+'com_parents.json'))
raus={}
for i,p in enumerate(eltern):
    if i % TEILE != TEIL: continue
    q=urllib.parse.urlencode({'per_page':100,'status':'any',
        '_fields':'id,sku,regular_price,status,wwpro_wholesale_prices'})
    v=ruf(f"products/{p['id']}/variations?{q}")
    raus[p['id']]={'sku':p['sku'],'name':p['name'],'status':p['status'],'var':v}
    if i % 40 == 0: print(f'  Teil {TEIL}: {len(raus)}', flush=True)
json.dump(raus, open(S+f'com_allvars_{TEIL}.json','w'), ensure_ascii=False)
print('fertig', len(raus))
