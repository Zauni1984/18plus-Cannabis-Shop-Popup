# -*- coding: utf-8 -*-
"""Loescht Tags, die nichts gruppieren.

Geloescht wird nur, was dem Zweck eines Tags nicht dient:
- Tags an genau einem Produkt: sie verbinden nichts, und die interne Suche
  findet das Produkt ohnehin ueber seinen Namen
- SEO-Floskeln ("... kaufen"): doppelt zum Basis-Tag
- Fragmente ohne Bedeutung

Nicht geloescht werden Tags an zwei oder mehr Produkten. Die Stichprobe hat
gezeigt, dass sie arbeiten: "UV-Schutz Glas" verbindet zwei Miron-Glaeser,
"Zitrone CBD Oel" die 5%- und die 10%-Variante, "SF1000" zwei Modellvarianten.
"""
import json, os, re, sys, time, urllib.request, html
sys.path.insert(0, '.')
from rq_apply import AUTH

SCHUTZ = {'beilngries', 'hanfjack'}
TROCKEN = '--go' not in sys.argv
DONE = 'TAGWEG_DONE.txt'
done = {l.strip() for l in open(DONE)} if os.path.exists(DONE) else set()
log = open(DONE, 'a')


def klar(n):
    return html.unescape(html.unescape(n))


def req(p, d=None, m='GET'):
    r = urllib.request.Request('https://hanfjack.de/wp-json/wc/v3' + p,
                               data=json.dumps(d).encode() if d is not None else None,
                               method=m, headers=AUTH)
    return json.load(urllib.request.urlopen(r, timeout=90))


T = json.load(open('tags_jetzt.json'))
weg = []
for t in T:
    n = klar(t['name'])
    if n.lower() in SCHUTZ:
        continue
    if t['count'] == 1:
        weg.append({**t, 'grund': 'nur ein Produkt'})
    elif t['count'] > 0 and re.search(r'\b(kaufen|bestellen|günstig|billig)\b', n, re.I):
        weg.append({**t, 'grund': 'SEO-Floskel'})
    elif t['count'] > 0 and len(n.strip()) < 2:
        weg.append({**t, 'grund': 'Fragment'})

json.dump(weg, open('tag_weg_backup.json', 'w'), ensure_ascii=False, indent=2)
print(f'{len(weg)} Tags zu löschen, {sum(t["count"] for t in weg)} Zuordnungen betroffen')
import collections
print(dict(collections.Counter(t['grund'] for t in weg)))
if TROCKEN:
    for t in weg[:10]:
        print(f"   [trocken] {klar(t['name'])!r} ({t['count']}) [{t['grund']}]")
    raise SystemExit

ok = err = 0
for t in weg:
    if str(t['id']) in done:
        continue
    try:
        req(f"/products/tags/{t['id']}?force=true", m='DELETE')
        log.write(str(t['id']) + '\n'); log.flush(); ok += 1
    except Exception as e:
        print('FEHLER', klar(t['name'])[:30], str(e)[:60], flush=True); err += 1
    time.sleep(0.3)
print(f'{ok} Tags gelöscht | {err} Fehler', flush=True)
