import json, base64, urllib.request, urllib.parse, time
CK=CS=None
for z in open('.wc_creds'):
    if 'CK=' in z: CK=z.strip().split('CK=',1)[1]
    if 'CS=' in z: CS=z.strip().split('CS=',1)[1]
AUTH='Basic '+base64.b64encode(f'{CK}:{CS}'.encode()).decode()
B='https://hanfjack.de/wp-json/wc/v3/products'
def GET(u):
    for v in range(5):
        try:
            r=urllib.request.Request(u,headers={'Authorization':AUTH,'User-Agent':'hj/1'})
            with urllib.request.urlopen(r,timeout=120) as f: return json.load(f)
        except Exception:
            if v==4: raise
            time.sleep(2**v)
YO=('_yoast_wpseo_title','_yoast_wpseo_metadesc','_yoast_wpseo_focuskw',
    '_yoast_wpseo_meta-robots-noindex','_yoast_wpseo_canonical')
raus=[]; s=1
while True:
    q=urllib.parse.urlencode({'status':'any','per_page':100,'page':s,'orderby':'id','order':'asc',
        '_fields':'id,name,status,type,catalog_visibility,permalink,categories,meta_data'})
    d=GET(f'{B}?{q}')
    if not d: break
    for p in d:
        m={x['key']:x['value'] for x in p.get('meta_data',[]) if x.get('key') in YO}
        raus.append({'id':p['id'],'name':p['name'],'status':p['status'],'type':p['type'],
                     'sicht':p.get('catalog_visibility'),
                     'kat':[c['name'] for c in p.get('categories',[])],
                     **{k.replace('_yoast_wpseo_',''):(m.get(k) or '') for k in YO}})
    print(s, len(raus), flush=True); s+=1
json.dump(raus, open('yoast.json','w'), ensure_ascii=False)
print('fertig', len(raus))
