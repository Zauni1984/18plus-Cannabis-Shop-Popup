# -*- coding: utf-8 -*-
"""Plan aus plan.json in den Shop schreiben (nur upsell_ids und cross_sell_ids).

Schreibt in Stapeln, merkt sich fertige IDs in geschrieben.txt und kann
jederzeit erneut gestartet werden.
"""
import json, os, sys, time
sys.path.insert(0, os.environ.get('HJ_ARBEIT', '.'))
import hjapi

ARBEIT = os.environ.get('HJ_ARBEIT', '.')
STAPEL = int(os.environ.get('HJ_STAPEL', '40'))
PAUSE = float(os.environ.get('HJ_PAUSE', '2.5'))

plan = json.load(open(os.path.join(ARBEIT, 'plan.json')))
fertig_pfad = os.path.join(ARBEIT, 'geschrieben.txt')
fertig = set()
if os.path.exists(fertig_pfad):
    fertig = {int(z) for z in open(fertig_pfad).read().split() if z.strip()}

# Sicherung der alten Werte einmalig ablegen
sich_pfad = os.path.join(ARBEIT, 'sicherung_vorher.json')
if not os.path.exists(sich_pfad):
    alt = {sid: {'upsell_ids': v['alt_up'], 'cross_sell_ids': v['alt_cs']}
           for sid, v in plan.items() if v['alt_up'] or v['alt_cs']}
    json.dump(alt, open(sich_pfad, 'w'))
    print(f'Sicherung: {len(alt)} Produkte mit bisherigen Verknuepfungen')

offen = [(int(sid), v) for sid, v in plan.items()
         if int(sid) not in fertig
         and (v['upsell_ids'] != v['alt_up'] or v['cross_sell_ids'] != v['alt_cs'])]
offen.sort()
print(f'offen: {len(offen)} Produkte', flush=True)

geschrieben = 0
with open(fertig_pfad, 'a') as prot:
    for i in range(0, len(offen), STAPEL):
        teil = offen[i:i+STAPEL]
        nutz = [{'id': pid, 'upsell_ids': v['upsell_ids'],
                 'cross_sell_ids': v['cross_sell_ids']} for pid, v in teil]
        antwort = hjapi.ruf('products/batch?_fields=update', {'update': nutz},
                            'POST', pause=PAUSE)
        zurueck = {p['id'] for p in antwort.get('update', []) if 'id' in p}
        fehler = [p for p in antwort.get('update', []) if p.get('error')]
        if fehler:
            print('FEHLER:', json.dumps(fehler[:3], ensure_ascii=False)[:500], flush=True)
        for pid, _ in teil:
            if pid in zurueck:
                prot.write(f'{pid}\n')
                geschrieben += 1
        prot.flush()
        print(f'{i+len(teil):>5}/{len(offen)}  geschrieben {geschrieben}', flush=True)
print('fertig', geschrieben)
