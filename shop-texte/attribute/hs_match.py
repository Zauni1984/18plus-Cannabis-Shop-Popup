# -*- coding: utf-8 -*-
import json, re, sys, unicodedata
sys.path.insert(0, '.')
import hs_attr as H

VENDOR = {
    '420 Fast Buds': 'Fast Buds', "Barney's Farm": 'Barneys Farm',
    'Green House Seeds': 'Green House Seed Co.', 'Exotic Seeds': 'Exotic Seed',
    'Cali Connection': 'The Cali Connection',
}
RAUS = (r'feminisiert|feminised|feminized|autoflowering|autoflower|auto|regular|regulaer|'
        r'hanfsamen|cannabissamen|samen|seeds|seed|fast version|ff|strain|sorte')
PACK = r'\b\d+\s*(?:er)?\s*(?:samen|seeds?|stk|stueck|stuck|st|pack|packung|x)\b'
PRAEFIX = {'the bulldog seeds': ['tb', 'the bulldog', 'bulldog'],
           'fast buds': ['420 fast buds', 'fast buds'],
           'green house seed co.': ['green house seeds', 'green house'],
           'humboldt seed company': ['humboldt seed co', 'humboldt'],
           'the cali connection': ['cali connection'],
           'exotic seed': ['exotic seeds'],
           'barneys farm': ["barney's farm", 'barneys farm'],
           'n.y.ceeds': ['nyceeds', 'n y ceeds']}


def _ascii(s):
    s = unicodedata.normalize('NFKD', s or '').replace('ß', 'ss')
    return ''.join(c for c in s if not unicodedata.combining(c)).lower()


def norm(s, marke=''):
    s = _ascii(s)
    s = re.sub(r'\(.*?\)|\[.*?\]', ' ', s)
    s = re.sub(r'[^a-z0-9#:]+', ' ', s)
    s = re.sub(PACK, ' ', s)
    kand = PRAEFIX.get(_ascii(marke), []) + [_ascii(marke)]
    for k in sorted({re.sub(r'[^a-z0-9 ]+', ' ', x).strip() for x in kand if x}, key=len, reverse=True):
        s = re.sub(r'\b' + re.escape(k).replace(r'\ ', r'\s+') + r'\b', ' ', s)
    s = re.sub(r'\b(?:' + RAUS + r')\b', ' ', s)
    return re.sub(r'\s+', ' ', s).strip()


def ist_auto(s):
    return bool(re.search(r'\bauto|\bff\b|fast\s*version', (s or '').lower()))


def baue_map(rest_ids, seeds_file='seeds.json'):
    S = {str(p['id']): p for p in json.load(open(seeds_file))}
    HS = json.load(open('hs_products.json'))
    kat = {}
    for p in HS:
        marke = VENDOR.get(p.get('vendor') or '', p.get('vendor') or '')
        attr = H.build(p)
        if not attr:
            continue
        k = (marke.lower(), norm(p['title'], marke), ist_auto(p['title']))
        kat.setdefault(k, (p, attr))
        kat.setdefault((k[0], k[1], None), (p, attr))
    M, offen = {}, []
    for pid in rest_ids:
        p = S.get(pid)
        if not p:
            continue
        marken = [t['name'] for t in p.get('brands') or []]
        if not marken:
            offen.append((pid, p['name'], '(keine Marke)')); continue
        mk = marken[0]
        n = norm(p['name'], mk)
        tr = kat.get((mk.lower(), n, ist_auto(p['name']))) or kat.get((mk.lower(), n, None))
        if tr:
            M[pid] = {'name': p['name'], 'hs_title': tr[0]['title'],
                      'hs_handle': tr[0]['handle'], 'marke': mk, 'attr': tr[1]}
        else:
            offen.append((pid, p['name'], mk))
    return M, offen


if __name__ == '__main__':
    offen_ids = [str(x) for x in json.load(open('offen_ids.json'))]
    done = {l.strip() for l in open('DESC_DONE.txt')}
    rest = [x for x in offen_ids if x not in done]
    M, off = baue_map(rest)
    print(f'Treffer {len(M)} von {len(rest)} offenen')
    json.dump(M, open('hs_map.json', 'w'), ensure_ascii=False, indent=1)
    import collections
    c = collections.Counter(m['marke'] for m in M.values())
    print('TREFFER:', dict(c.most_common()))
    c2 = collections.Counter(o[2] for o in off)
    print('OFFEN:', dict(c2.most_common(20)))
