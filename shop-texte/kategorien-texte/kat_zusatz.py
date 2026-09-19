# -*- coding: utf-8 -*-
"""Kategorietexte erweitern: Bestand bleibt, Abschnitte kommen dahinter.

Ab Stapel 4 wird nicht mehr der ganze Text neu geschrieben, sondern nur der
Zusatz. Der vorhandene Text - Einleitung und, wo vorhanden, der
hochgezogene Block - bleibt unveraendert vorn stehen. Das ist schneller und
kann nichts verlieren.
"""
import json, re, sys
S = '/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
sys.path.insert(0, S)
import hj

def nur_text(s):
    return re.sub(r'\s+', ' ', re.sub(r'<[^>]+>', ' ', s or '')).strip()

def anhaengen(zusatz, name_datei):
    """{term_id: neue Abschnitte} -> schreiben, Bestand voranstellen."""
    vorher, plan = {}, {}
    for tid, zus in zusatz.items():
        k = hj.ruf(f'products/categories/{tid}?_fields=id,name,description')
        alt = (k['description'] or '').strip()
        if nur_text(zus)[:40] in nur_text(alt):
            print(f'{tid:>6} | schon vorhanden, uebersprungen | {k["name"]}')
            continue
        vorher[tid] = k['description']
        neu = (alt + '\n\n' + zus.strip()).strip()
        assert nur_text(alt)[:40] in nur_text(neu), tid
        assert zus.count('<strong>') >= 2, tid
        plan[tid] = (k['name'], neu)
    json.dump(vorher, open(S + name_datei, 'w'), ensure_ascii=False)
    for tid, (name, neu) in plan.items():
        q = hj.ruf(f'products/categories/{tid}', {'description': neu}, 'PUT')
        ok = q['description'].replace('\r\n', '\n').strip() == neu.strip()
        print(f"{tid:>6} | {len(nur_text(vorher[tid])):4d} -> {len(nur_text(q['description'])):5d} | "
              f"{'ok' if ok else 'ABWEICHUNG'} | {name}")
