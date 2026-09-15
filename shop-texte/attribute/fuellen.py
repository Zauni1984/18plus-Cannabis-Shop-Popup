# -*- coding: utf-8 -*-
"""Generischer Schreiber: fuellt ausschliesslich LEERE Attribute.

Aufruf: python3 fuellen.py <mapfile> <donefile> <label>
Die Map hat die Form {produkt_id: {"name": ..., "attr": {slug: [werte]}}}.
"""
import json, os, sys, time, urllib.request
sys.path.insert(0, '.')
from rq_apply import merge, AUTH

MAP, DONE, LABEL = sys.argv[1], sys.argv[2], sys.argv[3]
M = json.load(open(MAP))
done = {l.strip() for l in open(DONE)} if os.path.exists(DONE) else set()
log = open(DONE, 'a')
ok = err = voll = 0
BASIS = 'https://hanfjack.de/wp-json/wc/v3/products/'

for pid, m in M.items():
    if pid in done:
        continue
    try:
        cur = json.load(urllib.request.urlopen(urllib.request.Request(
            BASIS + pid, headers=AUTH), timeout=90))
    except Exception as e:
        print(pid, m['name'][:34], 'GET-FEHLER', str(e)[:60], flush=True)
        err += 1; time.sleep(2); continue
    da = {(a.get('slug') or ''): (a.get('options') or []) for a in cur.get('attributes', [])}
    neu = {s: v for s, v in m['attr'].items() if not da.get(s)}
    if not neu:
        log.write(pid + '\n'); log.flush(); voll += 1; time.sleep(0.35); continue
    body = json.dumps({'attributes': merge(cur.get('attributes'), neu)}).encode()
    try:
        r = json.load(urllib.request.urlopen(urllib.request.Request(
            BASIS + pid, data=body, method='PUT', headers=AUTH), timeout=90))
        got = {(a.get('slug') or ''): (a.get('options') or []) for a in r.get('attributes', [])}
        fehlt = [s for s in neu if not got.get(s)]
        if fehlt:
            print(pid, m['name'][:34], 'ABWEICHUNG', fehlt, flush=True); err += 1
        else:
            log.write(pid + '\n'); log.flush(); ok += 1
    except Exception as e:
        print(pid, m['name'][:34], 'FEHLER', str(e)[:60], flush=True); err += 1
    time.sleep(0.9)

print(f'{LABEL}: {ok} ergaenzt | {voll} waren schon vollstaendig | {err} Fehler', flush=True)
