# -*- coding: utf-8 -*-
"""Vergibt Tags an Produkte, die bisher keine haben.

Gespeist wird ausschliesslich aus vorhandenen Strukturdaten: Marke, Kategorie
und die Produktattribute. Es wird nichts erfunden und nichts geraten.
"""
import json, re, html, sys
sys.path.insert(0, '.')
import tag_norm as N

MARKEN = json.load(open('marken_ci.json'))
# Oberkategorien taugen nicht als Tag - sie trennen nichts
ZU_ALLGEMEIN = {'Growshop', 'Headshop', 'Samen', 'Angebote', 'Hanfprodukte', 'Bundles'}
# Attribute, deren Werte als Schlagwort taugen
AUS_ATTRIBUT = ['pa_duengerart', 'pa_anwendungsphase', 'pa_form', 'pa_substrat',
                'pa_material', 'pa_anwendungsart', 'pa_variante', 'pa_typ',
                'pa_lichtspektrum', 'pa_einband', 'pa_spektrum']
# Werte, die als Tag nichts bringen
WERT_RAUS = {'Unbekannt', 'Alle Substrate', 'Ganze Kultur', 'Gießen (Wurzel)',
             'Gießen und Blattdüngung'}
MAX = 6


def tags(p):
    out = []

    def dazu(w):
        w = N.saeubern(html.unescape(w), MARKEN)
        if w and len(w) <= 34 and w not in out:
            out.append(w)

    for b in (p.get('brands') or [])[:1]:
        dazu(b['name'])
    kats = [html.unescape(k['name']) for k in (p.get('categories') or [])]
    for k in kats:
        if k not in ZU_ALLGEMEIN:
            dazu(k)
            break
    attr = {(a.get('slug') or ''): (a.get('options') or []) for a in p.get('attributes', [])}
    for slug in AUS_ATTRIBUT:
        for w in attr.get(slug, [])[:1]:
            if w not in WERT_RAUS and not re.search(r'\d', w):
                dazu(w)
        if len(out) >= MAX:
            break
    return out[:MAX]


if __name__ == '__main__':
    import collections
    D = json.load(open('ohne_tags.json'))
    M, leer = {}, 0
    c = collections.Counter()
    for p in D:
        t = tags(p)
        if len(t) < 2:
            leer += 1
            continue
        M[str(p['id'])] = {'name': p['name'], 'tags': t}
        c[len(t)] += 1
    json.dump(M, open('tag_neu_map.json', 'w'), ensure_ascii=False, indent=1)
    print(f'{len(M)} Produkte bekommen Tags, {leer} bleiben ohne (zu wenig Daten)')
    print('Tags je Produkt:', dict(sorted(c.items())))
    for p in list(M.values())[:8]:
        print(f"   {p['name'][:50]:50} {' · '.join(p['tags'])}")
