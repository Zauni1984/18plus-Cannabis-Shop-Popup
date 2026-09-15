# -*- coding: utf-8 -*-
"""Erstellt den Zusammenfuehrungsplan fuer Attribut-Dubletten."""
import json, re, sys
sys.path.insert(0, '.')
import attr_schema as A

R = json.load(open('dedup_report.json'))
# Attribute, bei denen die Schreibweise fest vorgegeben ist
PROZENT = {'pa_sativa', 'pa_indica', 'pa_ruderalis'}
FEST = {'pa_terpene': {'β-caryophyllen': 'Caryophyllen'}}


STUFEN = set(A.THC_CBD_STUFEN)


def punkte(name, slug, count):
    """hoeher = besser geeignet als Kanon"""
    p = count * 10
    if slug in ('pa_thc-gehalt', 'pa_cbd-gehalt') and name in STUFEN:
        p += 20000                     # definierte Bandbreite aus attr_schema
    if slug in ('pa_thc-gehalt', 'pa_cbd-gehalt'):
        if re.search(r'\d\s%$', name):
            p += 300                   # Leerzeichen vor dem Prozentzeichen
        if '\u2013' in name:
            p += 100                   # Halbgeviertstrich als Bereichstrenner
    if slug in PROZENT:
        p += 10000 if re.fullmatch(r'\d+\s%', name) else -10000
    if slug == 'pa_ertrag':
        p += 500 if ' - ' not in name else 0        # kompakte Schreibweise 400-450
    if re.search(r'[()]', name) and slug not in ('pa_ertrag', 'pa_wuchshoehe'):
        p -= 5000                      # Klammerreste aus fehlerhaftem Split
    if name.startswith(('\u2022', '-', '~')):
        p -= 5000
    if '\u2019' in name:
        p -= 100                       # typografischer Apostroph -> gerader bevorzugt
    if name != name.strip() or re.search(r'\s{2,}', name):
        p -= 1000
    return p


# Werte, die sich nur durch ein angehaengtes '+' unterscheiden, sind
# eigenstaendige Angaben (Critical + != Critical, 70+ Tage != 70 Tage)
AUSNAHME_SLUGS = {'pa_bluetezeit-tage'}


def echte_dublette(terme):
    """Ein '+' ist eine Angabe, keine Schreibvariante: Critical + ist eine andere
    Sorte als Critical, Critical + Auto eine andere als Critical Auto."""
    namen = [n for _, n, _ in terme]
    mit = [n for n in namen if '+' in n]
    ohne = [n for n in namen if '+' not in n]
    if not mit or not ohne:
        return True
    def ohne_plus(n):
        return ' '.join(n.replace('+', ' ').split()).lower()
    return not any(ohne_plus(a) == ohne_plus(b) for a in mit for b in ohne)


plan = {}
for slug, gruppen in R.items():
    if slug in AUSNAHME_SLUGS:
        continue
    fest = FEST.get(slug, {})
    for k, terme in gruppen.items():
        if not echte_dublette(terme):
            print(f'  uebersprungen ({slug}): ' + ' | '.join(n for _, n, _ in terme))
            continue
        best = None
        for tid, name, cnt in terme:
            if name.lower() in fest:
                best = (tid, fest[name.lower()], cnt) if fest[name.lower()] == name else best
        if best is None:
            best = max(terme, key=lambda t: punkte(t[1], slug, t[2]))
        rest = [t for t in terme if t[0] != best[0]]
        if not rest:
            continue
        plan.setdefault(slug, []).append({'kanon': {'id': best[0], 'name': best[1], 'count': best[2]},
                                          'alt': [{'id': t[0], 'name': t[1], 'count': t[2]} for t in rest]})

json.dump(plan, open('dedup_plan.json', 'w'), ensure_ascii=False, indent=1)
for slug, gs in plan.items():
    betroffen = sum(t['count'] for g in gs for t in g['alt'])
    print(f'{slug}: {len(gs)} Gruppen, {sum(len(g["alt"]) for g in gs)} Terme werden zusammengefuehrt, '
          f'{betroffen} Produktzuordnungen umgehaengt')
print()
for slug in ('pa_sativa', 'pa_terpene', 'pa_effekte', 'pa_ertrag', 'pa_thc-gehalt', 'pa_cbd-gehalt'):
    for g in plan.get(slug, [])[:6]:
        print(f'  {slug}: {g["kanon"]["name"]!r} <= ' + ', '.join(f'{a["name"]!r}({a["count"]})' for a in g['alt']))
