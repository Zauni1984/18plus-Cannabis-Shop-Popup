# -*- coding: utf-8 -*-
"""Stichprobe: stimmen die Verknuepfungen im Shop mit plan.json ueberein?

Aufruf: HJ_ARBEIT=<Abzugsordner> python3 pruefen.py [Anzahl]
Prueft zufaellige Produkte ueber die REST-API und meldet Abweichungen.
"""
import json, os, random, sys
sys.path.insert(0, os.environ.get('HJ_ARBEIT', '.'))
import hjapi

ARBEIT = os.environ.get('HJ_ARBEIT', '.')
anzahl = int(sys.argv[1]) if len(sys.argv) > 1 else 25

plan = json.load(open(os.path.join(ARBEIT, 'plan.json')))
fertig_pfad = os.path.join(ARBEIT, 'geschrieben.txt')
fertig = [int(z) for z in open(fertig_pfad).read().split()] if os.path.exists(fertig_pfad) else []
if not fertig:
    print('nichts geschrieben')
    raise SystemExit(0)

random.seed()
probe = random.sample(fertig, min(anzahl, len(fertig)))
abweichung = 0
for pid in probe:
    d = hjapi.ruf(f'products/{pid}?_fields=id,upsell_ids,cross_sell_ids', pause=1.0)
    soll = plan[str(pid)]
    if (d.get('upsell_ids') or []) != soll['upsell_ids'] or \
       (d.get('cross_sell_ids') or []) != soll['cross_sell_ids']:
        abweichung += 1
        print(f'ABWEICHUNG {pid}')
        print(f'   soll UP {soll["upsell_ids"]}  CS {soll["cross_sell_ids"]}')
        print(f'   ist  UP {d.get("upsell_ids")}  CS {d.get("cross_sell_ids")}')
print(f'{len(probe)} Produkte geprueft, {abweichung} Abweichungen')
