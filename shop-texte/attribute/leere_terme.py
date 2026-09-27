# -*- coding: utf-8 -*-
"""Loescht die durch dedup_apply leer gewordenen Attribut-Terme.

Sicherung: geloescht wird nur, was der Shop selbst mit count == 0 meldet.
Jeder Term wird vorher einzeln abgefragt und vorher in leere_terme_backup.json
gesichert.
"""
import json, os, sys, time, urllib.request
sys.path.insert(0, '.')
from rq_apply import AUTH
import attr_schema as A

BASIS = 'https://hanfjack.de/wp-json/wc/v3'
PLAN = json.load(open('dedup_plan.json'))
TROCKEN = '--go' not in sys.argv
DONE = 'TERME_DONE.txt'
done = {l.strip() for l in open(DONE)} if os.path.exists(DONE) else set()
log = open(DONE, 'a')


def req(pfad, method='GET'):
    r = urllib.request.Request(BASIS + pfad, method=method, headers=AUTH)
    return json.load(urllib.request.urlopen(r, timeout=90))


backup, weg, behalten, fehler = [], 0, [], 0
for slug, gruppen in PLAN.items():
    aid = A.ATTR_ID[slug]
    for g in gruppen:
        for alt in g['alt']:
            mark = f"{slug}:{alt['id']}"
            if mark in done:
                continue
            try:
                t = req(f"/products/attributes/{aid}/terms/{alt['id']}")
            except Exception as e:
                if '404' in str(e):
                    log.write(mark + '\n'); log.flush()   # schon weg
                    continue
                print('LESEN', slug, alt['name'], str(e)[:50], flush=True); fehler += 1; continue
            if t.get('count', 0) != 0:
                behalten.append((slug, t['name'], t['count']))
                continue
            backup.append({'attribut': slug, 'attribut_id': aid, 'id': t['id'],
                           'name': t['name'], 'slug': t['slug'],
                           'beschreibung': t.get('description', ''),
                           'kanon': g['kanon']['name']})
            if TROCKEN:
                continue
            try:
                req(f"/products/attributes/{aid}/terms/{t['id']}?force=true", 'DELETE')
                weg += 1
                log.write(mark + '\n'); log.flush()
            except Exception as e:
                print('LOESCHEN', slug, t['name'], str(e)[:60], flush=True); fehler += 1
            time.sleep(0.5)

json.dump(backup, open('leere_terme_backup.json', 'w'), ensure_ascii=False, indent=1)
print(f"{'TROCKENLAUF: ' if TROCKEN else ''}{len(backup)} Terme mit Zaehler 0"
      f"{'' if TROCKEN else f', {weg} geloescht'} | {len(behalten)} haben noch Produkte | {fehler} Fehler")
for b in behalten[:20]:
    print('   behalten:', b)
