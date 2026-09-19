# -*- coding: utf-8 -*-
"""Haengt Tags produktweise um: Zusammenfuehrungen und Entfernungen in einem Durchgang.

Ein Produkt wird hoechstens einmal geschrieben, auch wenn mehrere seiner Tags
betroffen sind.
"""
import json, os, sys, time, urllib.request
sys.path.insert(0, '.')
from rq_apply import AUTH
from tag_merge import ZIEL

SCHUTZ = {'beilngries', 'hanfjack'}
BASIS = 'https://hanfjack.de/wp-json/wc/v3'
TROCKEN = '--go' not in sys.argv
TEILE = int(sys.argv[2]) if len(sys.argv) > 2 else 1
TEIL = int(sys.argv[3]) if len(sys.argv) > 3 else 0
DONE = f'TAGPROD_{TEIL}_DONE.txt'
done = {l.strip() for l in open(DONE)} if os.path.exists(DONE) else set()
log = open(DONE, 'a')

P = json.load(open('tag_plan.json'))
# aktueller Name je Tag-ID nach den Umbenennungen
NAME = {}
for k in ('unveraendert', 'umbenennen', 'zusammenfuehren'):
    for t in P[k]:
        z = t.get('ziel', t['name'])
        NAME[t['id']] = ZIEL.get(z.lower(), z)
WEG = {t['id'] for t in P['entfernen'] if t['name'].lower() not in SCHUTZ}
# Ziel-ID je Name (der erste Tag, der diesen Namen traegt, gewinnt)
ZIEL_ID = {}
for tid, n in NAME.items():
    ZIEL_ID.setdefault(n.lower(), tid)


def req(pfad, data=None, method='GET'):
    r = urllib.request.Request(BASIS + pfad,
                               data=json.dumps(data).encode() if data is not None else None,
                               method=method, headers=AUTH)
    return json.load(urllib.request.urlopen(r, timeout=90))


PROD = json.load(open('prod_tags.json'))
plan = []
for i, p in enumerate(PROD):
    if i % TEILE != TEIL:
        continue
    alt = [t['id'] for t in (p.get('tags') or [])]
    if not alt:
        continue
    neu, gesehen = [], set()
    for tid in alt:
        if tid in WEG:
            continue
        ziel = ZIEL_ID.get(NAME.get(tid, '').lower(), tid)
        if ziel not in gesehen:
            gesehen.add(ziel); neu.append(ziel)
    if neu != alt:
        plan.append((p, alt, neu))

print(f'{len(plan)} Produkte zu ändern (Teil {TEIL+1}/{TEILE})')
if TROCKEN:
    for p, alt, neu in plan[:5]:
        vorher = [t['name'] for t in p['tags']]
        nachher = [NAME.get(t, str(t)) for t in neu]
        print(f"  {p['name'][:46]}")
        print(f"     vorher  {len(alt):2}: {' · '.join(vorher)}")
        print(f"     nachher {len(neu):2}: {' · '.join(nachher)}")
    raise SystemExit

ok = err = 0
for p, alt, neu in plan:
    pid = str(p['id'])
    if pid in done:
        continue
    try:
        r = req(f'/products/{pid}?_fields=id,tags',
                {'tags': [{'id': t} for t in neu]}, 'PUT')
        if sorted(t['id'] for t in r['tags']) == sorted(neu):
            log.write(pid + '\n'); log.flush(); ok += 1
        else:
            print(pid, 'ABWEICHUNG', flush=True); err += 1
    except Exception as e:
        print(pid, p['name'][:30], 'FEHLER', str(e)[:60], flush=True); err += 1
    time.sleep(0.5)
print(f'Tags umgehängt: {ok} Produkte | {err} Fehler', flush=True)
