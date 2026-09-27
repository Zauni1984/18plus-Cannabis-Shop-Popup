# -*- coding: utf-8 -*-
"""Schreibt die geplanten Yoast-Felder. yoast.json ist das Backup."""
import json, base64, urllib.request, time, sys
CK=CS=None
for z in open('.wc_creds'):
    if 'CK=' in z: CK=z.strip().split('CK=',1)[1]
    if 'CS=' in z: CS=z.strip().split('CS=',1)[1]
AUTH='Basic '+base64.b64encode(f'{CK}:{CS}'.encode()).decode()
BATCH='https://hanfjack.de/wp-json/wc/v3/products/batch'
SCHLUESSEL={'title':'_yoast_wpseo_title','metadesc':'_yoast_wpseo_metadesc'}

def POST(n):
    d=json.dumps(n).encode()
    for v in range(5):
        try:
            r=urllib.request.Request(BATCH, data=d, method='POST',
                headers={'Authorization':AUTH,'Content-Type':'application/json','User-Agent':'hj/1'})
            with urllib.request.urlopen(r, timeout=180) as f: return json.load(f)
        except Exception:
            if v==4: raise
            time.sleep(2**v*3)

plan=json.load(open('yoast_plan.json'))['plan']
nur = set(sys.argv[1].split(',')) if len(sys.argv)>1 and sys.argv[1]!='alle' else None
TEILE=int(sys.argv[2]) if len(sys.argv)>2 else 1
TEIL =int(sys.argv[3]) if len(sys.argv)>3 else 0
GROESSE=20

aufgaben=[]
for nr,(pid,feld) in enumerate(sorted(plan.items(), key=lambda x:int(x[0]))):
    if nur and pid not in nur: continue
    if not nur and nr % TEILE != TEIL: continue
    aufgaben.append({'id':int(pid),
        'meta_data':[{'key':SCHLUESSEL[k],'value':v} for k,v in feld.items()]})
print(f'{len(aufgaben)} Produkte zu schreiben', flush=True)
ok=fehler=0
for i in range(0,len(aufgaben),GROESSE):
    b=aufgaben[i:i+GROESSE]
    try:
        a=POST({'update':b})
        for e in a.get('update',[]):
            if e.get('error'): fehler+=1; print('  FEHLER',e.get('id'),str(e['error'].get('message'))[:90],flush=True)
            else: ok+=1
    except Exception as e:
        fehler+=len(b); print('  BLOCK-FEHLER',b[0]['id'],repr(e)[:110],flush=True)
    if (i//GROESSE)%10==0: print(f'  Teil {TEIL}: {ok} geschrieben, {fehler} Fehler', flush=True)
    time.sleep(0.4)
print(f'fertig: {ok} geschrieben, {fehler} Fehler')
