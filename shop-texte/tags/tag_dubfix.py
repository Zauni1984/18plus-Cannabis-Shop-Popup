# -*- coding: utf-8 -*-
"""Loest die doppelt vergebenen Tag-Namen auf.

Der Term mit den meisten Produkten ueberlebt. Produkte der Zwillinge werden
umgehaengt, die Zwillinge geloescht, und der Ueberlebende bekommt den
sauberen Slug des geloeschten Zwillings.
"""
import json, sys, time, urllib.request, html
sys.path.insert(0, '.')
from rq_apply import AUTH


def req(p, d=None, m='GET'):
    r = urllib.request.Request('https://hanfjack.de/wp-json/wc/v3' + p,
                               data=json.dumps(d).encode() if d is not None else None,
                               method=m, headers=AUTH)
    return json.load(urllib.request.urlopen(r, timeout=90))


def klar(n):
    return html.unescape(html.unescape(n))


DUB = json.load(open('tag_dub_final.json'))
TROCKEN = '--go' not in sys.argv
um = weg = slug = 0
for name, gruppe in DUB.items():
    gr = sorted(gruppe, key=lambda x: -x['count'])
    bleibt, zwillinge = gr[0], gr[1:]
    # der sauberste Slug ist der ohne Prozentkodierung und ohne Altlast
    kandidat = min((z['slug'] for z in zwillinge), key=len, default=None)
    for z in zwillinge:
        if z['count']:
            ps = req(f"/products?tag={z['id']}&per_page=100&status=any&_fields=id,name,tags")
            for p in ps:
                neu, seen = [], set()
                for t in p['tags']:
                    tid = bleibt['id'] if t['id'] == z['id'] else t['id']
                    if tid not in seen:
                        seen.add(tid); neu.append(tid)
                if TROCKEN:
                    print(f"   [trocken] {p['name'][:40]} : #{z['id']} -> #{bleibt['id']}")
                    continue
                req(f"/products/{p['id']}?_fields=id", {'tags': [{'id': t} for t in neu]}, 'PUT')
                um += 1
                time.sleep(0.5)
        if TROCKEN:
            print(f"   [trocken] Zwilling #{z['id']} {klar(z['name'])!r} löschen (slug {z['slug']})")
            continue
        try:
            req(f"/products/tags/{z['id']}?force=true", m='DELETE')
            weg += 1
        except Exception as e:
            print('DEL-FEHLER', z['id'], str(e)[:60])
        time.sleep(0.3)
    if not TROCKEN and kandidat and kandidat != bleibt['slug']:
        try:
            r = req(f"/products/tags/{bleibt['id']}", {'slug': kandidat}, 'PUT')
            print(f"   #{bleibt['id']} {klar(r['name'])!r}: slug {bleibt['slug']} -> {r['slug']}")
            slug += 1
        except Exception as e:
            print('SLUG-FEHLER', bleibt['id'], str(e)[:60])
        time.sleep(0.3)
print(f"{'TROCKENLAUF: ' if TROCKEN else ''}{um} Produkte umgehängt, {weg} Zwillinge gelöscht, {slug} Slugs bereinigt")
