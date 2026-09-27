# -*- coding: utf-8 -*-
"""Stellt zusammen, was an Yoast-Feldern geschrieben werden soll."""
import json, re
from collections import defaultdict
import yoast_bau as y

yo = json.load(open('yoast.json'))
at = {p['id']: p for p in json.load(open('attr_alle.json'))}
de = {p['id']: p for p in json.load(open('desc_kontrolle.json'))}

MIN_META = 80          # darunter ist ein Snippet schlechter als keines

def entschlacke(t):
    """Erst den Shop-Zusatz opfern, bevor am Produktnamen gekuerzt wird."""
    for weg in (' | Hanfjack', ' kaufen', ' - Cannabis Samen', ' Cannabis Samen'):
        if len(t) <= y.TITEL_MAX: break
        t = t.replace(weg, '')
    while len(t) > y.TITEL_MAX and '|' in t:    # angehaengte Segmente opfern
        t = t.rsplit('|', 1)[0].strip()
    return t.strip(' -–|')

plan, grund = {}, defaultdict(list)
for p in yo:
    pid = p['id']
    a   = at.get(pid)
    if not a: continue
    text = de.get(pid, {}).get('description')
    t_ist = (p['title'] or '').strip()
    m_ist = (p['metadesc'] or '').strip()
    feld  = {}

    if not t_ist:
        feld['title'] = y.titel(a['name'], a['attr']); grund[pid].append('Titel fehlte')
    elif len(t_ist) > y.TITEL_MAX:
        neu = entschlacke(t_ist)
        if len(neu) > y.TITEL_MAX: neu = y.titel(a['name'], a['attr'])
        feld['title'] = neu; grund[pid].append('Titel zu lang')

    if not m_ist:
        neu = y.meta(a['name'], a['attr'], text)
        if len(neu) >= MIN_META:
            feld['metadesc'] = neu; grund[pid].append('Beschreibung fehlte')
        else:
            grund[pid].append('zu wenig Material')
    elif len(m_ist) > y.META_MAX:
        feld['metadesc'] = y.kappe(m_ist, y.META_MAX); grund[pid].append('Beschreibung zu lang')

    if feld: plan[pid] = feld

# --- Dubletten aufloesen: der Produktname traegt die Marke ----------------
titel_jetzt = {}
for p in yo:
    t = plan.get(p['id'], {}).get('title', (p['title'] or '').strip())
    if t: titel_jetzt[p['id']] = t
gruppen = defaultdict(list)
for pid, t in titel_jetzt.items(): gruppen[t].append(pid)

for t, ids in gruppen.items():
    if len(ids) < 2: continue
    for pid in ids:
        a = at.get(pid)
        if not a: continue
        neu = y.titel(a['name'], a['attr'])
        if neu and neu != t:
            plan.setdefault(pid, {})['title'] = neu
            grund[pid].append('Titel doppelt')

json.dump({'plan': {str(k): v for k, v in plan.items()},
           'grund': {str(k): v for k, v in grund.items()}},
          open('yoast_plan.json', 'w'), ensure_ascii=False, indent=1)

from collections import Counter
c = Counter(g for gs in grund.values() for g in gs)
print(f'{len(plan)} Produkte zu schreiben')
for k, n in c.most_common(): print(f'  {n:5d}  {k}')
# Restdubletten
rest = defaultdict(list)
for pid, t in titel_jetzt.items():
    rest[plan.get(pid, {}).get('title', t)].append(pid)
print('\nDubletten danach:', sum(len(v) for v in rest.values() if len(v) > 1),
      'Produkte auf', sum(1 for v in rest.values() if len(v) > 1), 'Titeln')
