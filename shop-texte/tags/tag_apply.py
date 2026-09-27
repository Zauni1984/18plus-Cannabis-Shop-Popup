# -*- coding: utf-8 -*-
"""Setzt den Tag-Plan um.

Reihenfolge:
1. Umbenennen  - ein Aufruf je Tag, die Produktzuordnungen bleiben bestehen
2. Zusammenfuehren - Produkte auf den Zieltag umhaengen
3. Entfernen   - Tag von den Produkten loesen

Geschuetzt sind 'Beilngries' und 'Hanfjack': Beilngries markiert den eigenen
Lagerbestand und wird unter keinen Umstaenden angefasst.
"""
import json, os, re, sys, time, urllib.request
sys.path.insert(0, '.')
from rq_apply import AUTH
from tag_merge import ZIEL

SCHUTZ = {'beilngries', 'hanfjack'}
BASIS = 'https://hanfjack.de/wp-json/wc/v3'
TROCKEN = '--go' not in sys.argv
DONE = 'TAG_DONE.txt'
done = {l.strip() for l in open(DONE)} if os.path.exists(DONE) else set()
log = open(DONE, 'a')


def req(pfad, data=None, method='GET'):
    r = urllib.request.Request(BASIS + pfad,
                               data=json.dumps(data).encode() if data is not None else None,
                               method=method, headers=AUTH)
    return json.load(urllib.request.urlopen(r, timeout=90))


P = json.load(open('tag_plan.json'))
NAMEN = {t['name']: t['id'] for k in P for t in P[k]}


def ziel_von(t):
    """Normalisiertes Ziel plus inhaltliche Zusammenfuehrung"""
    z = t.get('ziel', t['name'])
    return ZIEL.get(z.lower(), z)


# --- 1. und 2.: alles, was einen neuen Namen bekommt -------------------------
umbenennen, zusammen = [], []
belegt = {}
for k in ('unveraendert', 'umbenennen', 'zusammenfuehren'):
    for t in P[k]:
        if t['name'].lower() in SCHUTZ:
            continue
        z = ziel_von(t)
        if z == t['name']:
            belegt.setdefault(z.lower(), t['id'])
for k in ('unveraendert', 'umbenennen', 'zusammenfuehren'):
    for t in P[k]:
        if t['name'].lower() in SCHUTZ:
            continue
        z = ziel_von(t)
        if z == t['name']:
            continue
        if z.lower() in belegt and belegt[z.lower()] != t['id']:
            zusammen.append({**t, 'zielname': z, 'ziel_id': belegt[z.lower()]})
        else:
            umbenennen.append({**t, 'zielname': z})
            belegt[z.lower()] = t['id']

entfernen = [t for t in P['entfernen'] if t['name'].lower() not in SCHUTZ]
entfernen += [{'id': NAMEN[n], 'name': n, 'count': c, 'grund': 'SEO-Floskel'}
              for n, c in []]

print(f'1. umbenennen     {len(umbenennen):5} Tags')
print(f'2. zusammenführen {len(zusammen):5} Tags, {sum(t["count"] for t in zusammen):5} Zuordnungen')
print(f'3. entfernen      {len(entfernen):5} Tags, {sum(t["count"] for t in entfernen):5} Zuordnungen')
if TROCKEN:
    for t in umbenennen[:6]:
        print(f"   [um]  {t['name']!r} -> {t['zielname']!r}")
    for t in sorted(zusammen, key=lambda x: -x['count'])[:6]:
        print(f"   [zu]  {t['name']!r} -> {t['zielname']!r} (#{t['ziel_id']})")
    for t in sorted(entfernen, key=lambda x: -x['count'])[:6]:
        print(f"   [weg] {t['name']!r}  [{t['grund']}]")
    raise SystemExit

ok = err = 0
for t in umbenennen:
    m = f"um:{t['id']}"
    if m in done:
        continue
    try:
        r = req(f"/products/tags/{t['id']}", {'name': t['zielname']}, 'PUT')
        if r['name'] == t['zielname']:
            log.write(m + '\n'); log.flush(); ok += 1
        else:
            print('ABWEICHUNG', t['name'], '->', r['name'], flush=True); err += 1
    except Exception as e:
        print('UM-FEHLER', t['name'][:30], str(e)[:60], flush=True); err += 1
    time.sleep(0.35)
print(f'umbenannt {ok}, Fehler {err}', flush=True)
