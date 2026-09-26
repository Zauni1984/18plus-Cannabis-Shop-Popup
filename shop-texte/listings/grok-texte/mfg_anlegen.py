# -*- coding: utf-8 -*-
"""Legt die fehlenden Hersteller-Terme an und traegt Anschrift und EU-Vertreter ein."""
import json, os, sys, html, time, urllib.request, base64
sys.path.insert(0, '.')
import mfg_neu

def creds(p='.wc_creds'):
    d = {}
    for l in open(p):
        l = l.strip().removeprefix('export ')
        if '=' in l:
            k, v = l.split('=', 1); d[k.strip()] = v.strip()
    return d['CK'], d['CS']

ck, cs = creds()
A = {'Authorization': 'Basic ' + base64.b64encode(f'{ck}:{cs}'.encode()).decode(),
     'Content-Type': 'application/json'}
BASIS = 'https://hanfjack.de/wp-json/wc/v3/products/manufacturers'
TROCKEN = '--go' not in sys.argv


def req(pfad, data=None, method='GET'):
    r = urllib.request.Request(BASIS + pfad,
                               data=json.dumps(data).encode() if data is not None else None,
                               method=method, headers=A)
    return json.load(urllib.request.urlopen(r, timeout=90))


ergebnis = {}
for name, v in mfg_neu.NEU.items():
    if TROCKEN:
        print(f"  [trocken] {name}")
        print(f"      A : {v['addr'][:120]}")
        print(f"      EU: {(v['eu'] or '(leer)')[:70]}")
        continue
    try:
        t = req('', {'name': name, 'formatted_address': v['addr'],
                     'formatted_eu_address': v['eu']}, 'POST')
        ok = html.unescape(t.get('formatted_address', '')) == v['addr']
        print(f"#{t['id']} {name} {'OK' if ok else 'ABWEICHUNG'}", flush=True)
        if not ok:
            print('      ->', repr(t.get('formatted_address'))[:160])
        ergebnis[name] = t['id']
    except Exception as e:
        print(name, 'FEHLER', str(e)[:110], flush=True)
    time.sleep(0.6)

if ergebnis:
    json.dump(ergebnis, open('mfg_neu_ids.json', 'w'), ensure_ascii=False, indent=1)
    print(len(ergebnis), 'Terme angelegt -> mfg_neu_ids.json')
