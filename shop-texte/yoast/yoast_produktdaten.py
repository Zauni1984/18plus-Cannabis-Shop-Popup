# -*- coding: utf-8 -*-
"""Yoast mit den Produktdaten fuettern, die der Shop laengst hat.

1) Produkt-Identifier: GTIN aus `global_unique_id` und MPN aus `_ts_mpn`
   wandern in `wpseo_global_identifier_values`, das Feld, aus dem Yoast
   WooCommerce SEO das Product-Schema baut.
2) Primaerkategorie: `_yoast_wpseo_primary_product_cat` bestimmt, welchen Pfad
   Yoast im Breadcrumb und im Schema zeigt. Ohne sie greift sich WordPress
   irgendeine der zugeordneten Kategorien - bei "Samen > Feminisiert" plus
   "Angebote" ist das Gluecksache.

Gewaehlt wird die tiefste, spezifischste Kategorie; Querschnittsbegriffe wie
"Angebote" oder "Produktarchiv" zaehlen nicht.
"""
import json, os, sys

S = '/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
sys.path.insert(0, S)
import hjapi

ARBEIT = os.environ.get('HJ_ARBEIT', S)
NEUTRAL = {'angebote', 'uncategorized', 'produktarchiv', 'restposten', 'sale'}
STAPEL = int(os.environ.get('HJ_STAPEL', '10'))
PAUSE = float(os.environ.get('HJ_PAUSE', '3'))

LEER = {'gtin8': '', 'gtin12': '', 'gtin13': '', 'gtin14': '', 'isbn': '', 'mpn': ''}


def laden(name):
    return json.load(open(os.path.join(ARBEIT, name)))


def baum(kats):
    nach = {k['id']: k for k in kats}
    tiefe = {}
    for k in kats:
        n, cur = 0, k['id']
        while cur in nach:
            n += 1
            cur = nach[cur]['parent']
        tiefe[k['id']] = n
    return nach, tiefe


def identifier(p):
    """GTIN nach Laenge einsortieren, MPN dazu. Leer bleibt leer."""
    werte = dict(LEER)
    roh = str(p.get('global_unique_id') or '').strip().replace(' ', '').replace('-', '')
    if roh.isdigit() and len(roh) in (8, 12, 13, 14):
        werte[f'gtin{len(roh)}'] = roh
    mpn = ''
    for m in p.get('meta_data') or []:
        if m['key'] == '_ts_mpn' and m.get('value'):
            mpn = str(m['value']).strip()
    werte['mpn'] = mpn
    return werte if any(werte.values()) else None


def primaer(p, nach, tiefe):
    kandidaten = [c['id'] for c in p.get('categories') or []
                  if c['id'] in nach and nach[c['id']]['slug'] not in NEUTRAL]
    if not kandidaten:
        return None
    return max(kandidaten, key=lambda i: (tiefe.get(i, 0), -nach[i]['count']))


def vorhanden(p, schluessel):
    for m in p.get('meta_data') or []:
        if m['key'] == schluessel:
            return m.get('value')
    return None


def plan():
    kats = laden('kategorien.json')
    nach, tiefe = baum(kats)
    produkte = laden('yoast_produkte.json')
    auftrag = []
    zaehler = {'identifier': 0, 'primaer': 0, 'beides': 0, 'nichts': 0}
    for p in produkte:
        meta = []
        ids = identifier(p)
        if ids and vorhanden(p, 'wpseo_global_identifier_values') != ids:
            meta.append({'key': 'wpseo_global_identifier_values', 'value': ids})
        pk = primaer(p, nach, tiefe)
        if pk and str(vorhanden(p, '_yoast_wpseo_primary_product_cat') or '') != str(pk):
            meta.append({'key': '_yoast_wpseo_primary_product_cat', 'value': str(pk)})
        if not meta:
            zaehler['nichts'] += 1
            continue
        zaehler['beides' if len(meta) == 2 else
                ('identifier' if meta[0]['key'].startswith('wpseo_global') else 'primaer')] += 1
        auftrag.append({'id': p['id'], 'meta_data': meta})
    print(f"{len(produkte)} Produkte geprueft")
    print(f"  nur Identifier : {zaehler['identifier']}")
    print(f"  nur Kategorie  : {zaehler['primaer']}")
    print(f"  beides         : {zaehler['beides']}")
    print(f"  nichts zu tun  : {zaehler['nichts']}")
    print(f"  zu schreiben   : {len(auftrag)}")
    json.dump(auftrag, open(os.path.join(ARBEIT, 'yoast_auftrag.json'), 'w'))
    return auftrag


def schreiben():
    auftrag = json.load(open(os.path.join(ARBEIT, 'yoast_auftrag.json')))
    fertig_pfad = os.path.join(ARBEIT, 'yoast_geschrieben.txt')
    fertig = set()
    if os.path.exists(fertig_pfad):
        fertig = {int(z) for z in open(fertig_pfad).read().split() if z.strip()}
    offen = [a for a in auftrag if a['id'] not in fertig]
    print(f'offen: {len(offen)}', flush=True)
    with open(fertig_pfad, 'a') as prot:
        for i in range(0, len(offen), STAPEL):
            teil = offen[i:i+STAPEL]
            antwort = hjapi.ruf('products/batch', {'update': teil}, 'POST', pause=PAUSE)
            zurueck = {x['id'] for x in antwort.get('update', []) if 'id' in x}
            for a in teil:
                if a['id'] in zurueck:
                    prot.write(f"{a['id']}\n")
            prot.flush()
            print(f'{i+len(teil):>5}/{len(offen)}', flush=True)
    print('fertig')


if __name__ == '__main__':
    if len(sys.argv) > 1 and sys.argv[1] == 'schreiben':
        schreiben()
    else:
        plan()
