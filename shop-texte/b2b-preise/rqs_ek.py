# -*- coding: utf-8 -*-
"""Liest die EK-Preise aus der RQS-Preisliste 2026.

Aufbau je Zeile: A-E die SKUs fuer 1/3/5/10/25 Samen, F der Sortenname,
G/I/K/M/O die zugehoerigen Wholesale-Preise (dazwischen leere Mengenspalten),
T-X die Verbraucherpreise. Eine Zeile ohne SKU ist eine Zwischenueberschrift.
"""
import sys, re
from decimal import Decimal
sys.path.insert(0,'/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad')
from xlsx import lies, blaetter

DATEI = '/root/.claude/uploads/1414604f-ab94-5ea2-91e2-00ca46d10a2b/5506ce9f-RQS_Pricelist_2026_-_Seeds_Wholesale.xlsx'
GROESSEN = [(0, 6, 19, '1'), (1, 8, 20, '3'), (2, 10, 21, '5'),
            (3, 12, 22, '10'), (4, 14, 23, '25')]

def wert(z, i):
    if i >= len(z): return None
    s = str(z[i] or '').replace(',', '.').strip()
    try:
        w = Decimal(s); return w if w > 0 else None
    except Exception: return None

def lies_ek(datei=DATEI):
    raus = {}
    for name, pfad in blaetter(datei):
        for z in lies(datei, pfad):
            sorte = str(z[5]).strip() if len(z) > 5 else ''
            for sku_i, ek_i, vk_i, packung in GROESSEN:
                if sku_i >= len(z): continue
                sku = str(z[sku_i] or '').strip()
                # SKUs sind Buchstaben-Ziffern-Codes, keine Ueberschriften
                if not re.fullmatch(r'[A-Z]{2,5}\d{4,8}', sku): continue
                ek = wert(z, ek_i)
                if ek is None: continue
                raus[sku] = {'ek': ek, 'vk_liste': wert(z, vk_i),
                             'sorte': sorte, 'packung': packung, 'blatt': name}
    return raus

if __name__ == '__main__':
    d = lies_ek()
    print(f'{len(d)} SKUs mit EK')
    from collections import Counter
    print('Blaetter:', dict(Counter(v['blatt'] for v in d.values())))
    print('Packungen:', dict(Counter(v['packung'] for v in d.values())))
    print('EK-Spanne:', min(v['ek'] for v in d.values()), '-', max(v['ek'] for v in d.values()))
    # Gegenprobe: Verbraucherpreis sollte grob dem Doppelten entsprechen
    q = [float(v['vk_liste']/v['ek']) for v in d.values() if v['vk_liste']]
    import statistics as st
    print(f'Verbraucher/EK: Median {st.median(q):.2f}, min {min(q):.2f}, max {max(q):.2f}')
    for s, v in list(d.items())[:5]:
        print(f"  {s:12} {v['packung']:>3} Samen  EK {v['ek']:>7}  Liste-VK {v['vk_liste']}  {v['sorte'][:30]}")
