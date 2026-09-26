# -*- coding: utf-8 -*-
"""Schreibt die aufgeraeumten Beschreibungen zurueck. desc_alle.json ist das Backup."""
import json, base64, urllib.request, time, sys
import html_fix

CK=CS=None
for z in open('.wc_creds'):
    if 'CK=' in z: CK=z.strip().split('CK=',1)[1]
    if 'CS=' in z: CS=z.strip().split('CS=',1)[1]
AUTH='Basic '+base64.b64encode(f'{CK}:{CS}'.encode()).decode()
BATCH='https://hanfjack.de/wp-json/wc/v3/products/batch'

def POST(nutzlast):
    d=json.dumps(nutzlast).encode()
    for v in range(5):
        try:
            r=urllib.request.Request(BATCH, data=d, method='POST',
                headers={'Authorization':AUTH,'Content-Type':'application/json','User-Agent':'hj/1'})
            with urllib.request.urlopen(r, timeout=180) as f: return json.load(f)
        except Exception as e:
            if v==4: raise
            time.sleep(2**v * 3)

nur = set(int(x) for x in sys.argv[1].split(',')) if len(sys.argv)>1 and sys.argv[1]!='alle' else None
TEILE = int(sys.argv[2]) if len(sys.argv) > 2 else 1     # Anzahl paralleler Schreiber
TEIL  = int(sys.argv[3]) if len(sys.argv) > 3 else 0
GROESSE = 20

alle = json.load(open('desc_alle.json'))
aufgaben = []
for nr, p in enumerate(alle):
    if nur and p['id'] not in nur: continue
    if not nur and nr % TEILE != TEIL: continue
    d_neu = html_fix.aufraeumen(p.get('description') or '')
    k_neu = html_fix.aufraeumen_kurz(p.get('short_description') or '')
    feld = {}
    if d_neu != (p.get('description') or ''):       feld['description'] = d_neu
    if k_neu != (p.get('short_description') or ''): feld['short_description'] = k_neu
    if feld: aufgaben.append(dict(id=p['id'], **feld))

print(f'{len(aufgaben)} Produkte zu schreiben', flush=True)
ok = fehler = 0
for i in range(0, len(aufgaben), GROESSE):
    block = aufgaben[i:i+GROESSE]
    try:
        a = POST({'update': block})
        for e in a.get('update', []):
            if e.get('error'): fehler += 1; print('  FEHLER', e.get('id'), e['error'].get('message')[:90], flush=True)
            else: ok += 1
    except Exception as e:
        fehler += len(block); print('  BLOCK-FEHLER', block[0]['id'], repr(e)[:120], flush=True)
    if (i//GROESSE) % 10 == 0: print(f'  Teil {TEIL}: {ok} geschrieben, {fehler} Fehler', flush=True)
    time.sleep(0.4)
print(f'fertig: {ok} geschrieben, {fehler} Fehler')
