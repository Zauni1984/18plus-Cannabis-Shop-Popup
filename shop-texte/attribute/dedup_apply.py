# -*- coding: utf-8 -*-
"""Fuehrt Dubletten-Terme in den Produktattributen zusammen.

Je Gruppe: alle Produkte der Alt-Terme auf den Kanon-Term umhaengen,
danach den leeren Alt-Term loeschen.
"""
import json, os, sys, time, urllib.request
sys.path.insert(0, '.')
from rq_apply import AUTH
import attr_schema as A

BASIS = 'https://hanfjack.de/wp-json/wc/v3'
PLAN = json.load(open('dedup_plan.json'))
DONE = 'DEDUP_DONE.txt'
done = {l.strip() for l in open(DONE)} if os.path.exists(DONE) else set()
log = open(DONE, 'a')
TROCKEN = '--go' not in sys.argv
LOESCHEN = '--loeschen' in sys.argv   # ohne Flag bleiben die leeren Terme stehen


def req(pfad, data=None, method='GET'):
    r = urllib.request.Request(BASIS + pfad, data=data, method=method, headers=AUTH)
    return json.load(urllib.request.urlopen(r, timeout=90))


def produkte(slug, tid):
    out, page = [], 1
    while True:
        d = req(f'/products?per_page=100&page={page}&attribute={slug}&attribute_term={tid}')
        out += d
        if len(d) < 100:
            return out
        page += 1


um = gel = 0
for slug, gruppen in PLAN.items():
    aid = A.ATTR_ID[slug]
    for g in gruppen:
        kanon = g['kanon']['name']
        for alt in g['alt']:
            mark = f"{slug}:{alt['id']}"
            if mark in done:
                continue
            try:
                ps = produkte(slug, alt['id'])
            except Exception as e:
                print('LISTE-FEHLER', slug, alt['name'], str(e)[:60], flush=True); continue
            fehler = False
            for p in ps:
                attrs = []
                for a in p.get('attributes', []):
                    e = {'id': a.get('id', 0), 'name': a.get('name'),
                         'position': a.get('position', 0), 'visible': a.get('visible', True),
                         'variation': a.get('variation', False),
                         'options': a.get('options') or []}
                    if not e['id']:
                        e.pop('id')
                    if (a.get('slug') or '') == slug:
                        neu = []
                        for o in e['options']:
                            o = kanon if o == alt['name'] else o
                            if o not in neu:
                                neu.append(o)
                        e['options'] = neu
                    attrs.append(e)
                if TROCKEN:
                    print(f"  [trocken] {p['id']} {p['name'][:40]} {slug}: {alt['name']!r} -> {kanon!r}")
                    continue
                try:
                    req(f"/products/{p['id']}", json.dumps({'attributes': attrs}).encode(), 'PUT')
                    um += 1
                except Exception as e:
                    print('PUT-FEHLER', p['id'], str(e)[:60], flush=True); fehler = True
                time.sleep(0.8)
            if TROCKEN:
                print(f"[trocken] Term loeschen {slug} {alt['name']!r} (#{alt['id']}, {len(ps)} Produkte)")
                continue
            if fehler:
                continue
            if not LOESCHEN:
                log.write(mark + '\n'); log.flush()
                continue
            try:
                req(f"/products/attributes/{aid}/terms/{alt['id']}?force=true", method='DELETE')
                gel += 1
                log.write(mark + '\n'); log.flush()
            except Exception as e:
                print('DEL-FEHLER', slug, alt['name'], str(e)[:60], flush=True)
            time.sleep(0.5)

print(f"{'TROCKENLAUF' if TROCKEN else 'Dubletten'}: {um} Produkte umgehaengt, "
      f"{gel} Terme geloescht" + ('' if LOESCHEN else ' (leere Terme bleiben stehen)'), flush=True)
