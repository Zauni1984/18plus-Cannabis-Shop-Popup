# -*- coding: utf-8 -*-
"""Findet Dubletten in den Produktattribut-Taxonomien (Gross/Klein, Umlaute, Plural)."""
import json, urllib.request, unicodedata, collections, re, sys
sys.path.insert(0, '.')
from rq_apply import AUTH
import attr_schema as A

def hole(aid):
    t, page = [], 1
    while True:
        u = f'https://hanfjack.de/wp-json/wc/v3/products/attributes/{aid}/terms?per_page=100&page={page}'
        d = json.load(urllib.request.urlopen(urllib.request.Request(u, headers=AUTH), timeout=60))
        t += d
        if len(d) < 100: break
        page += 1
    return t

NUMERISCH = {'pa_thc-gehalt', 'pa_cbd-gehalt', 'pa_sativa', 'pa_indica', 'pa_ruderalis',
             'pa_bluetezeit-tage', 'pa_ertrag', 'pa_wuchshoehe',
             # alles, wo eine Zahl den Wert traegt: 2,2 kg ist nicht 22 kg
             'pa_gewicht', 'pa_luftdurchsatz', 'pa_anschluss', 'pa_abmessungen',
             'pa_laenge', 'pa_durchmesser', 'pa_maschenweite', 'pa_presskraft',
             'pa_grammatur', 'pa_leistungsaufnahme', 'pa_ppf', 'pa_ppe', 'pa_lumen',
             'pa_geraeuschpegel', 'pa_spannung', 'pa_frequenz', 'pa_flaeche',
             'pa_groesse', 'pa_inhalt', 'pa_amp', 'pa_stromverbrauch', 'pa_npk'}


def key(n, slug=''):
    s = n.lower().replace('\u2013', '-').replace('\u2014', '-').replace('\u2212', '-')
    if slug in NUMERISCH:
        # Zahlen und Trennzeichen bleiben erhalten - 0-4 % und 0,4 % sind NICHT gleich,
        # 2,2 kg nicht 22 kg. Nur Schreibvarianten werden vereinheitlicht.
        s = re.sub(r'[\s~+]+', '', s)
        s = s.replace('\u00b2', '2').replace('\u00b3', '3')
        s = s.replace('\u03bc', '\u00b5')                 # griechisches My -> Mikrozeichen
        s = re.sub(r'(?<=\d)\.(?=\d)', ',', s)          # Dezimalpunkt -> Komma
        s = re.sub(r'[\u00d7]', 'x', s)                   # Malzeichen
        s = re.sub(r'[\u2013\u2014]', '-', s)
        s = re.sub(r'db\(a\)', 'dba', s)
        s = s.replace('\u00f8', '').replace('\u2300', '')  # Durchmesserzeichen
        return re.sub(r'[^a-z0-9,\-/%\u00b5]+', '', s)
    s = unicodedata.normalize('NFKD', s).replace('\u00df', 'ss')
    s = ''.join(c for c in s if not unicodedata.combining(c))
    s = re.sub(r'[^a-z0-9]+', '', s)
    return re.sub(r'(en|er|e|n|s)$', '', s)


if __name__ == '__main__':
    report = {}
    for slug, aid in sorted(A.ATTR_ID.items(), key=lambda x: x[1]):
        try:
            terms = hole(aid)
        except Exception as e:
            print(slug, 'FEHLER', str(e)[:50]); continue
        g = collections.defaultdict(list)
        for t in terms:
            g[key(t['name'], slug)].append((t['id'], t['name'], t['count']))
        dub = {k: v for k, v in g.items() if len(v) > 1}
        if dub:
            report[slug] = dub
            print(f'--- {slug} ({len(terms)} Terme, {len(dub)} Dublettengruppen)')
            for k, v in sorted(dub.items(), key=lambda x: -sum(i[2] for i in x[1]))[:15]:
                print('   ', ' | '.join(f'{n} ({c}) #{i}' for i, n, c in sorted(v, key=lambda x: -x[2])))
    json.dump(report, open('dedup_report.json', 'w'), ensure_ascii=False, indent=1)
    print('\nGruppen gesamt:', sum(len(v) for v in report.values()))
