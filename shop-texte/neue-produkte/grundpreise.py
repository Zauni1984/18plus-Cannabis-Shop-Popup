# -*- coding: utf-8 -*-
"""Grundpreise fuer die in dieser Sitzung angelegten Produkte nachtragen.

Der Shop nutzt WooCommerce Germanized. Der Grundpreis haengt an drei Feldern:
`unit` (die Einheit als Term), `unit_price.base` und `unit_price.product`;
mit `price_auto` rechnet das Plugin den Preis selbst aus.

Die Konvention steht im Bestand:
- Blättchen: Einheit Stück, Basis 1, Menge = Blattzahl je Heft.
  8992 (2in1, 33 Blättchen + 33 Tips) steht auf 33 - gezählt werden die
  Blättchen, nicht Blättchen plus Tips.
- Flüssiges und Cremes: Einheit l, Basis 1, Menge in Litern.
- Zahnpasta: Einheit kg (so steht 20190 im Bestand), Menge in Kilogramm.
- Sets: Einheit l, Menge = Gesamtinhalt (14701 = 0,4 l fuer 2 x 200 ml).
"""
import hj

STUECK, KG, LITER = 261, 16, 26

PLAN = {
    # Blättchen-Hefte: Blattzahl je Heft
    45305: (STUECK, '33'),   # Smoking Supreme 2in1
    45309: (STUECK, '32'), 45310: (STUECK, '32'), 45312: (STUECK, '32'),
    45313: (STUECK, '32'), 45314: (STUECK, '32'), 45315: (STUECK, '32'),
    45316: (STUECK, '32'), 45317: (STUECK, '32'), 45318: (STUECK, '32'),
    45319: (STUECK, '32'), 45320: (STUECK, '32'), 45321: (STUECK, '32'),
    45322: (STUECK, '32'),
    45340: (STUECK, '32'),   # Dutch Passion
    45349: (STUECK, '32'),   # RQS Organic
    45350: (STUECK, '32'),   # Anesia Organic
    45357: (STUECK, '50'),   # G-Rollz Diablos
    45358: (STUECK, '50'),   # G-Rollz King's Choice
    # Zahnpasten, 75 g
    45365: (KG, '0.075'), 45366: (KG, '0.075'), 45367: (KG, '0.075'),
    # Geschenksets, Gesamtinhalt
    45379: (LITER, '0.6'),   # 3 x 200 ml
    45384: (LITER, '1.2'),   # 500 + 500 + 200 ml
    45385: (LITER, '0.25'),  # 150 + 50 + 50 ml
}

if __name__ == '__main__':
    for pid, (einheit, menge) in sorted(PLAN.items()):
        q = hj.ruf(f'products/{pid}', {
            'unit': {'id': einheit},
            'unit_price': {'base': '1', 'product': menge, 'price_auto': True},
        }, 'PUT')
        up = q.get('unit_price') or {}
        u = q.get('unit') or {}
        nm = u.get('name', '-') if isinstance(u, dict) else '-'
        print(f"{pid} | {nm:6} | {up.get('product') or '-':6} | netto "
              f"{up.get('price') or 'FEHLT':>8} | {q['name'][:40]}")
