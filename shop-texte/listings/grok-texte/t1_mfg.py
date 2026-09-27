# -*- coding: utf-8 -*-
"""Weist den 808 Samen mit Tiger-One-Artikelnummer den Hersteller-Term ihrer Marke zu.

Der Hersteller ist eine Taxonomie (product_manufacturer) am Produkt. Zugewiesen
wird nur, wenn fuer die Marke ein Term existiert und das Produkt noch keinen hat.
"""
import json, os, sys, time, urllib.request, base64, re

S = os.path.dirname(os.path.abspath(__file__))
WP = open(os.path.join(S, '.wp_creds')).read()
U = re.search(r'WPUSER=(\S+)', WP).group(1)
P = re.search(r'WPAPP=(.+)', WP).group(1).strip()
H = {'Authorization': 'Basic ' + base64.b64encode(f'{U}:{P}'.encode()).decode(),
     'Content-Type': 'application/json'}


def req(pfad, data=None, method='GET'):
    r = urllib.request.Request('https://hanfjack.de/wp-json' + pfad,
                               data=json.dumps(data).encode() if data is not None else None,
                               method=method, headers=H)
    return json.load(urllib.request.urlopen(r, timeout=90))


ZUORDNUNG = json.load(open(os.path.join(S, 't1_mfg_map.json')))   # {marke: hersteller_term_id}
PRODUKTE = json.load(open(os.path.join(S, 't1_ohne_hersteller.json')))
DONE = os.path.join(S, 'T1MFG_DONE.txt')
done = {l.strip() for l in open(DONE)} if os.path.exists(DONE) else set()
log = open(DONE, 'a')
TROCKEN = '--go' not in sys.argv

ok = err = keine = 0
for p in PRODUKTE:
    pid = str(p['id'])
    if pid in done:
        continue
    marken = p.get('marke') or []
    tid = next((ZUORDNUNG[m] for m in marken if m in ZUORDNUNG), None)
    if not tid:
        keine += 1
        continue
    if TROCKEN:
        print(f"  [trocken] {pid} {p['name'][:46]} -> Hersteller #{tid}")
        ok += 1
        continue
    try:
        d = req(f'/wp/v2/product/{pid}', {'product_manufacturer': [tid]}, 'POST')
        if tid in (d.get('product_manufacturer') or []):
            log.write(pid + '\n'); log.flush(); ok += 1
        else:
            print(pid, 'ABWEICHUNG', d.get('product_manufacturer'), flush=True); err += 1
    except Exception as e:
        print(pid, p['name'][:34], 'FEHLER', str(e)[:60], flush=True); err += 1
    time.sleep(0.6)

print(f"{'TROCKENLAUF: ' if TROCKEN else ''}{ok} zugewiesen | {keine} ohne Hersteller-Term | {err} Fehler", flush=True)
