# -*- coding: utf-8 -*-
"""Entfernt Fehleintraege aus der Genetik.

In pa_genetik sind Werte gelandet, die dort nicht hingehoeren: Genanteile
("50% Indica / 50% Sativa"), nackte Prozentwerte und Fliesstext. Die
Genanteile werden nach pa_sativa und pa_indica verschoben, sofern die Felder
dort noch leer sind - der Wert geht also nicht verloren.
"""
import json, re, sys, time, urllib.request
sys.path.insert(0, '.')
from rq_apply import merge, AUTH
import attr_schema as A

SCHLECHT = json.load(open('genetik_schlecht.json'))
RAUS = {t['name'] for g in ('Genanteil', 'Prozentwert', 'Fliesstext')
        for t in SCHLECHT.get(g, [])}
TROCKEN = '--go' not in sys.argv
BASIS = 'https://hanfjack.de/wp-json/wc/v3/products'


def anteile(wert):
    """'80 % Indica / 20 % Sativa' -> {'pa_indica': ['80 %'], 'pa_sativa': ['20 %']}"""
    out = {}
    for zahl, typ in re.findall(r'(\d{1,3})\s*%\s*(Indica|Sativa)', wert, re.I):
        out['pa_' + typ.lower()] = [f'{zahl} %']
    for typ, zahl in re.findall(r'(Indica|Sativa)\s*[:\s]\s*(\d{1,3})\s*%', wert, re.I):
        out.setdefault('pa_' + typ.lower(), [f'{zahl} %'])
    return out


def req(pfad, data=None, method='GET'):
    r = urllib.request.Request(BASIS + pfad, data=data, method=method, headers=AUTH)
    return json.load(urllib.request.urlopen(r, timeout=90))


geaendert = fehler = 0
for gruppe in ('Genanteil', 'Prozentwert', 'Fliesstext'):
    for t in SCHLECHT.get(gruppe, []):
        if not t['count']:
            continue
        try:
            ps = req(f"?attribute=pa_genetik&attribute_term={t['id']}&per_page=100"
                     f"&status=any&_fields=id,name,attributes")
        except Exception as e:
            print('LISTE', t['name'][:30], str(e)[:50], flush=True); fehler += 1; continue
        for p in ps:
            da = {(a.get('slug') or ''): (a.get('options') or []) for a in p['attributes']}
            neu = {'pa_genetik': [o for o in da.get('pa_genetik', []) if o != t['name']]}
            if gruppe == 'Genanteil':
                for slug, w in anteile(t['name']).items():
                    if not da.get(slug):
                        neu[slug] = w
            if TROCKEN:
                print(f"  [trocken] {p['id']} {p['name'][:40]}")
                print(f"       raus: {t['name']!r}  ->  {dict((k, v) for k, v in neu.items() if k != 'pa_genetik')}")
                geaendert += 1
                continue
            try:
                r = req(f"/{p['id']}?_fields=id,attributes",
                        json.dumps({'attributes': merge(p['attributes'], neu)}).encode(), 'PUT')
                got = {(a.get('slug') or ''): (a.get('options') or []) for a in r['attributes']}
                if t['name'] in got.get('pa_genetik', []):
                    print(p['id'], 'ABWEICHUNG', flush=True); fehler += 1
                else:
                    geaendert += 1
            except Exception as e:
                print(p['id'], 'FEHLER', str(e)[:50], flush=True); fehler += 1
            time.sleep(0.7)

print(f"{'TROCKENLAUF: ' if TROCKEN else ''}{geaendert} Produkte bereinigt | {fehler} Fehler", flush=True)
