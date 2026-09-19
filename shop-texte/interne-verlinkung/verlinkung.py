# -*- coding: utf-8 -*-
"""Upsells und Cross-Sells fuer hanfjack.de berechnen.

Upsells  = 4 aehnliche Produkte (gleiche Kategorie, moeglichst gleiche Marke
           und gleiche Lagerquelle, Preis eher darueber) -> Produktseite
Cross-Sells = 2 ergaenzende Produkte aus einer passenden anderen Kategorie
           -> Warenkorb

Aufruf:   python3 verlinkung.py plan     (rechnet, schreibt plan.json)
          python3 verlinkung.py bericht  (Verteilung ansehen)
"""
import json, re, sys, os, math
from collections import defaultdict, Counter

HIER = os.path.dirname(os.path.abspath(__file__))
ARBEIT = os.environ.get('HJ_ARBEIT', HIER)

# Lagerquellen als Schlagwort. Produkte derselben Quelle werden bevorzugt
# untereinander verknuepft, weil sie zusammen versandfertig sind.
LAGERQUELLEN = ['Beilngries', 'Bloomtech', 'Grow In', 'Tiger One', 'Hanfjack']

# Ergaenzende Kategorien fuer Cross-Sells, Slug -> Ziel-Slugs.
# Gesucht wird vom tiefsten Zweig aufwaerts, der erste Treffer gilt.
ERGAENZUNG = {
    # ---- Samen und Vermehrung ----
    'samen':            ['duenger', 'erde-substrate', 'pflanzentoepfe', 'anzuchtmedien'],
    'feminisiert':      ['duenger', 'erde-substrate', 'pflanzentoepfe', 'anzuchtmedien'],
    'automatisch':      ['duenger', 'erde-substrate', 'pflanzentoepfe', 'anzuchtmedien'],
    'regular':          ['duenger', 'erde-substrate', 'pflanzentoepfe'],
    'cbd-samen':        ['duenger', 'erde-substrate', 'pflanzentoepfe'],
    'f1-samen':         ['duenger', 'erde-substrate', 'pflanzentoepfe'],
    'vermehrungsmaterial': ['erde-substrate', 'duenger', 'pflanzentoepfe'],
    'anzucht':          ['anzuchtmedien', 'anzuchtbeleuchtung', 'pflanzentoepfe'],
    'anzuchtbeleuchtung': ['anzuchtmedien', 'anzucht', 'pflanzentoepfe'],
    'anzuchtmedien':    ['anzucht', 'duenger', 'pflanzentoepfe'],
    'stecklingszubehoer': ['anzuchtmedien', 'duenger', 'pflanzzubehoer'],
    'zimmergewaechshaeuser': ['anzuchtmedien', 'anzuchtbeleuchtung', 'erde-substrate'],
    'hydrokultur-anzucht': ['anzuchtmedien', 'duenger', 'ph-wert'],
    # ---- Growshop ----
    'duenger':          ['ph-wert', 'bewaesserung', 'erde-substrate'],
    'duenger-sets':     ['ph-wert', 'erde-substrate', 'pflanzentoepfe'],
    'erde-substrate':   ['duenger', 'pflanzentoepfe', 'ph-wert'],
    'pflanzentoepfe':   ['erde-substrate', 'duenger', 'pflanzzubehoer'],
    'bewaesserung':     ['duenger', 'ph-wert', 'pflanzentoepfe'],
    'autopot-komplettsysteme': ['duenger', 'erde-substrate', 'ph-wert'],
    'autopot-tanks-zubehoer': ['duenger', 'ph-wert', 'pflanzentoepfe'],
    'autopot-toepfe-untersetzer': ['erde-substrate', 'duenger', 'ph-wert'],
    'autopot-verrohrung-ventile': ['duenger', 'ph-wert', 'pumpen'],
    'ph-wert':          ['duenger', 'bewaesserung', 'messgeraete'],
    'messgeraete':      ['ph-wert', 'duenger', 'controller'],
    'controller':       ['luefter-filter', 'messgeraete', 'ventilatoren'],
    'pumpen':           ['bewaesserung', 'duenger', 'ph-wert'],
    'led-growlampen':   ['growboxen', 'luefter-filter', 'controller'],
    'growboxen':        ['led-growlampen', 'luefter-filter', 'zeltzubehoer'],
    'zeltzubehoer':     ['growboxen', 'luefter-filter', 'folien-reflexion'],
    'folien-reflexion': ['growboxen', 'led-growlampen', 'zeltzubehoer'],
    'luefter-filter':   ['growboxen', 'controller', 'geruchsneutralisation'],
    'zu-und-abluft':    ['luefter-filter', 'growboxen', 'controller'],
    'ventilatoren':     ['luefter-filter', 'controller', 'growboxen'],
    'aktivkohlefilter': ['zu-und-abluft', 'luefter-filter', 'geruchsneutralisation'],
    'geruchsneutralisation': ['aktivkohlefilter', 'luefter-filter', 'curing-lagerung'],
    'beheizung':        ['controller', 'messgeraete', 'growboxen'],
    'luftbefeuchter':   ['controller', 'messgeraete', 'luefter-filter'],
    'luftentfeuchter-growbedarf': ['controller', 'messgeraete', 'luefter-filter'],
    'schaedlingsbekaempfung': ['duenger', 'ph-wert', 'pflanzzubehoer'],
    'pflanzzubehoer':   ['pflanzentoepfe', 'duenger', 'erntescheren'],
    'growzubehoer':     ['duenger', 'erde-substrate', 'ph-wert'],
    'erntescheren':     ['curing-lagerung', 'handschuhe', 'trimmer-erntehelfer'],
    'trimmer':          ['curing-lagerung', 'erntescheren', 'handschuhe'],
    'trimmer-erntehelfer': ['curing-lagerung', 'handschuhe', 'veredeln-extraktion'],
    'handschuhe':       ['erntescheren', 'trimmer-erntehelfer', 'curing-lagerung'],
    'lupen-mikroskope': ['erntescheren', 'messgeraete', 'waagen'],
    'komplettsets':     ['duenger', 'samen', 'erde-substrate'],
    'curing-lagerung':  ['feuchtigkeitsregler', 'waagen', 'aufbewahrung'],
    'extraktion-pressen': ['trimmer-erntehelfer', 'dabbing', 'curing-lagerung'],
    'veredeln-extraktion': ['extraktion-pressen', 'dabbing', 'curing-lagerung'],
    'growbedarf':       ['duenger', 'growzubehoer', 'erde-substrate'],
    # ---- Headshop ----
    'papers':           ['filter', 'feuerzeuge-zippo', 'rolling-trays'],
    'pre-rolled-papers': ['filter', 'feuerzeuge-zippo', 'rolling-trays'],
    'blunts':           ['filter', 'feuerzeuge-zippo', 'rolling-trays'],
    'filter':           ['papers', 'rolling-trays', 'feuerzeuge-zippo'],
    'aktivkohlefilter-filter': ['papers', 'rolling-trays', 'feuerzeuge-zippo'],
    'big-14mm':         ['papers', 'rolling-trays', 'feuerzeuge-zippo'],
    'extra-slim-6-mm':  ['papers', 'rolling-trays', 'feuerzeuge-zippo'],
    'konisch-kegelfoermig': ['papers', 'rolling-trays', 'feuerzeuge-zippo'],
    'regular-8-9mm':    ['papers', 'rolling-trays', 'feuerzeuge-zippo'],
    'slim-7mm':         ['papers', 'rolling-trays', 'feuerzeuge-zippo'],
    'super-slim-5mm':   ['papers', 'rolling-trays', 'feuerzeuge-zippo'],
    'glas-tips':        ['papers', 'rolling-trays', 'filter'],
    'rolling-trays':    ['papers', 'filter', 'kraeutermuehlen'],
    'kraeutermuehlen':  ['papers', 'rolling-trays', 'aufbewahrung'],
    'feuerzeuge-zippo': ['papers', 'filter', 'aschenbecher'],
    'feuerzeuge':       ['papers', 'filter', 'aschenbecher'],
    'zippo':            ['papers', 'filter', 'aschenbecher'],
    'aschenbecher':     ['papers', 'feuerzeuge-zippo', 'rolling-trays'],
    'bongs':            ['feuerzeuge-zippo', 'kraeutermuehlen', 'aufbewahrung'],
    'pipes':            ['feuerzeuge-zippo', 'kraeutermuehlen', 'aufbewahrung'],
    'mundstuecke':      ['bongs', 'vaporizer', 'glas-tips'],
    'dabbing':          ['vaporizer', 'mundstuecke', 'aufbewahrung'],
    'vaporizer':        ['mundstuecke', 'kraeutermuehlen', 'aufbewahrung'],
    'vapes-pods':       ['aufbewahrung', 'feuerzeuge-zippo', 'kraeutermuehlen'],
    'aufbewahrung':     ['feuchtigkeitsregler', 'kraeutermuehlen', 'papers'],
    'feuchtigkeitsregler': ['aufbewahrung', 'curing-lagerung', 'waagen'],
    'luftfilter':       ['geruchsneutralisation', 'aufbewahrung', 'luftfilter-ersatzfilter'],
    'luftfilter-ersatzfilter': ['luftfilter', 'geruchsneutralisation', 'aufbewahrung'],
    'waagen':           ['aufbewahrung', 'kraeutermuehlen', 'lupen-mikroskope'],
    'terpene':          ['dabbing', 'vaporizer', 'aufbewahrung'],
    'tabakersatz':      ['papers', 'filter', 'kraeutermuehlen'],
    'thc-test':         ['waagen', 'lupen-mikroskope', 'aufbewahrung'],
    'headshop':         ['papers', 'filter', 'feuerzeuge-zippo'],
    # ---- CBD, Lebensmittel, Pflege, Sonstiges ----
    'cbd':              ['papers', 'filter', 'kraeutermuehlen'],
    'cbd-blueten':      ['papers', 'filter', 'kraeutermuehlen'],
    'cbd-oel':          ['hanftee', 'pflegeprodukte', 'cbd-blueten'],
    'cbd-vapes':        ['vapes-pods', 'aufbewahrung', 'mundstuecke'],
    'cbd-fuer-tiere':   ['cbd-oel', 'pflegeprodukte', 'lebensmittel'],
    'lebensmittel':     ['hanftee', 'knabberhanf', 'pflegeprodukte'],
    'getraenke':        ['hanftee', 'knabberhanf', 'rohkost'],
    'gewuerze':         ['hanfoel', 'rohkost', 'knabberhanf'],
    'hanfsamen':        ['hanfoel', 'rohkost', 'hanftee'],
    'hanftee':          ['knabberhanf', 'hanfoel', 'pflegeprodukte'],
    'hanfoel':          ['rohkost', 'hanftee', 'pflegeprodukte'],
    'knabberhanf':      ['hanftee', 'rohkost', 'hanfoel'],
    'mehl':             ['hanfoel', 'rohkost', 'hanftee'],
    'rohkost':          ['hanfoel', 'hanftee', 'knabberhanf'],
    'pflegeprodukte':   ['lebensmittel', 'merch', 'cbd-oel'],
    'merch':            ['papers', 'feuerzeuge-zippo', 'aufbewahrung'],
    'buecher':          ['samen', 'duenger', 'growzubehoer'],
    'hanf-bundles-kaufen': ['duenger', 'samen', 'erde-substrate'],
    'dr-grow-sets':     ['duenger', 'erde-substrate', 'ph-wert'],
    'mystery-boxen':    ['papers', 'samen', 'merch'],
    'hanfprodukte':     ['papers', 'filter', 'merch'],
}

# Kategorien, die keine inhaltliche Naehe stiften (Querschnitt).
NEUTRAL = {'angebote', 'uncategorized', 'produktarchiv', 'restposten', 'sale'}

MAX_NUTZUNG_UPSELL = 12   # wie oft ein Produkt hoechstens Upsell-Ziel sein darf
MAX_NUTZUNG_CROSS = 40    # dito fuer Cross-Sells (kleinere Auswahl, hoehere Grenze)


def laden(name):
    return json.load(open(os.path.join(ARBEIT, name)))


def zahl(w):
    try:
        return float(str(w).replace(',', '.'))
    except (TypeError, ValueError):
        return 0.0


class Katalog:
    def __init__(self):
        self.produkte = laden('katalog.json')
        kats = laden('kategorien.json')
        self.kat = {k['id']: k for k in kats}
        self.slug_zu_id = {}
        for k in kats:
            self.slug_zu_id.setdefault(k['slug'], k['id'])
        self.ahnen = {k['id']: self._ahnen(k['id']) for k in kats}
        self.tiefe = {i: len(a) for i, a in self.ahnen.items()}
        self.nach_id = {p['id']: p for p in self.produkte}

    def _ahnen(self, kid):
        """Liste von der Wurzel bis zur Kategorie selbst."""
        kette = []
        cur = kid
        while cur in self.kat:
            kette.append(cur)
            cur = self.kat[cur]['parent']
        return list(reversed(kette))

    def kats_von(self, p):
        return [c['id'] for c in p.get('categories') or []
                if c['id'] in self.kat and self.kat[c['id']]['slug'] not in NEUTRAL]

    def tiefste_kat(self, p):
        ids = self.kats_von(p)
        if not ids:
            return None
        return max(ids, key=lambda i: (self.tiefe.get(i, 0), -self.kat[i]['count']))

    def quelle(self, p):
        namen = {t['name'] for t in p.get('tags') or []}
        for q in LAGERQUELLEN:
            if q in namen:
                return q
        return None

    def marke(self, p):
        b = p.get('brands') or []
        return b[0]['id'] if b else None


def ziel_geeignet(p):
    return (p.get('status') == 'publish'
            and p.get('catalog_visibility') in ('visible', 'catalog')
            and p.get('stock_status') != 'outofstock'
            and (p.get('parent_id') or 0) == 0
            and p.get('type') in ('simple', 'variable', 'grouped', 'woosb')
            and zahl(p.get('price')) > 0)


def quelle_geeignet(p):
    return ((p.get('parent_id') or 0) == 0
            and p.get('status') in ('publish', 'private', 'draft')
            and p.get('type') in ('simple', 'variable', 'grouped', 'external', 'woosb'))


def ahnen_menge(kat, p):
    """Alle Kategorie-IDs samt Vorfahren, die das Produkt beruehrt."""
    ids = set()
    for kid in kat.kats_von(p):
        ids.update(kat.ahnen.get(kid, []))
    return ids


def plan_rechnen():
    kat = Katalog()
    produkte = kat.produkte
    ziele = [p for p in produkte if ziel_geeignet(p)]
    quellen = [p for p in produkte if quelle_geeignet(p)]
    quellen.sort(key=lambda p: p['id'])

    ahnen = {p['id']: ahnen_menge(kat, p) for p in produkte}
    tiefste = {p['id']: kat.tiefste_kat(p) for p in produkte}
    preis = {p['id']: zahl(p.get('price')) for p in produkte}
    quelle = {p['id']: kat.quelle(p) for p in produkte}
    marke = {p['id']: kat.marke(p) for p in produkte}

    # Index: Kategorie (inkl. Vorfahren) -> moegliche Ziele
    nach_kat = defaultdict(list)
    for p in ziele:
        for kid in ahnen[p['id']]:
            nach_kat[kid].append(p)

    nutzung_up = Counter()
    nutzung_cs = Counter()
    plan = {}
    ohne_up, ohne_cs = [], []

    def pool_fuer(p):
        """Kandidaten aus der engsten Kategorie, die genug Auswahl bietet."""
        kette = kat.ahnen.get(tiefste[p['id']] or -1, [])
        for kid in reversed(kette):
            pool = nach_kat.get(kid, [])
            if len(pool) >= 6:
                return pool
        return nach_kat.get(kette[0], []) if kette else []

    def gemeinsame_tiefe(a, b):
        g = ahnen[a] & ahnen[b]
        return max((kat.tiefe.get(i, 0) for i in g), default=0)

    for s in quellen:
        sid = s['id']
        eigen = preis[sid]
        alt_up = [i for i in (s.get('upsell_ids') or [])]
        alt_cs = [i for i in (s.get('cross_sell_ids') or [])]

        # ---------- Kandidaten fuer ergaenzende Produkte ----------
        ziel_slugs = []
        for kid in reversed(kat.ahnen.get(tiefste[sid] or -1, [])):
            slug = kat.kat[kid]['slug']
            if slug in ERGAENZUNG:
                ziel_slugs = ERGAENZUNG[slug]
                break
        bew_cs = []
        for rang, slug in enumerate(ziel_slugs):
            zk = kat.slug_zu_id.get(slug)
            if not zk or zk in ahnen[sid]:
                continue
            for c in nach_kat.get(zk, []):
                cid = c['id']
                if cid == sid or nutzung_cs[cid] >= MAX_NUTZUNG_CROSS:
                    continue
                w = 40 - rang * 10
                if quelle[sid] and quelle[sid] == quelle[cid]:
                    w += 30
                p2 = preis[cid]
                if eigen > 0 and p2 > 0:
                    r = p2 / eigen
                    w += 20 if r <= 1 else max(0, 20 - (r - 1) * 10)
                # Kleinteile wie Schrauben oder Einzeltoepfe sind als Beilage
                # zu einem teureren Artikel unpassend
                if eigen > 8 and p2 < 3:
                    w -= 16
                elif p2 < 1.5:
                    w -= 8
                if cid in alt_cs:
                    w += 20
                w -= 1.5 * nutzung_cs[cid]
                w += ((sid * 17 + cid) % 7) * 0.5
                bew_cs.append((w, cid, zk))
        bew_cs.sort(key=lambda x: (-x[0], x[1]))

        # ---------- Upsells ----------
        pool = pool_fuer(s)
        bewertet = []
        for c in pool:
            cid = c['id']
            if cid == sid or nutzung_up[cid] >= MAX_NUTZUNG_UPSELL:
                continue
            tief = gemeinsame_tiefe(sid, cid)
            if tief == 0:
                continue
            w = 25.0 * tief
            if marke[sid] and marke[sid] == marke[cid]:
                w += 45
            if quelle[sid] and quelle[sid] == quelle[cid]:
                w += 30
            p2 = preis[cid]
            if eigen > 0 and p2 > 0:
                r = p2 / eigen
                w += (22 - min(20, (r - 1) * 12)) if r >= 1 else (10 - min(10, (1 - r) * 12))
            # bestehende Verknuepfung erhalten, wenn sie inhaltlich passt
            if cid in alt_up and (tief >= kat.tiefe.get(tiefste[sid] or -1, 0)
                                  or (marke[sid] and marke[sid] == marke[cid])):
                w += 35
            w -= 9 * nutzung_up[cid]
            w += ((sid * 31 + cid) % 7) * 0.4
            bewertet.append((w, cid))
        bewertet.sort(key=lambda x: (-x[0], x[1]))

        eigene_quelle = quelle[sid]
        if eigene_quelle:
            intern = [c for c in bewertet if quelle[c[1]] == eigene_quelle]
            extern = [c for c in bewertet if quelle[c[1]] != eigene_quelle]
            reihe = intern + extern if len(intern) >= 4 else bewertet
        else:
            reihe = bewertet
        up = [cid for _, cid in reihe[:4]]
        if len(up) < 4:                       # zu kleine Kategorie: ergaenzende Produkte
            for _, cid, _zk in bew_cs:
                if cid not in up:
                    up.append(cid)
                if len(up) >= 4:
                    break
        if len(up) < 4:
            ohne_up.append(sid)

        # ---------- Cross-Sells ----------
        cs = []
        genutzte_kat = set()
        for w, cid, zk in bew_cs:
            if len(cs) >= 2:
                break
            if cid in up or zk in genutzte_kat:
                continue
            cs.append(cid)
            genutzte_kat.add(zk)
        if len(cs) < 2:                        # Notnagel: beste uebrige
            for w, cid, zk in bew_cs:
                if cid not in cs and cid not in up:
                    cs.append(cid)
                if len(cs) >= 2:
                    break
        if len(cs) < 2:
            ohne_cs.append(sid)

        for cid in up:
            nutzung_up[cid] += 1
        for cid in cs:
            nutzung_cs[cid] += 1
        plan[sid] = {'upsell_ids': up, 'cross_sell_ids': cs,
                     'alt_up': alt_up, 'alt_cs': alt_cs}

    json.dump(plan, open(os.path.join(ARBEIT, 'plan.json'), 'w'))
    print(f'Quellen {len(quellen)}, moegliche Ziele {len(ziele)}')
    print(f'ohne vollstaendige Upsells: {len(ohne_up)}   ohne Cross-Sells: {len(ohne_cs)}')
    print(f'Produkte als Upsell-Ziel: {len(nutzung_up)} (max {max(nutzung_up.values()) if nutzung_up else 0})')
    print(f'Produkte als Cross-Ziel : {len(nutzung_cs)} (max {max(nutzung_cs.values()) if nutzung_cs else 0})')
    aend = sum(1 for sid, v in plan.items()
               if v['upsell_ids'] != v['alt_up'] or v['cross_sell_ids'] != v['alt_cs'])
    print(f'zu schreiben: {aend}')
    return plan


if __name__ == '__main__':
    befehl = sys.argv[1] if len(sys.argv) > 1 else 'plan'
    if befehl == 'plan':
        plan_rechnen()
