# -*- coding: utf-8 -*-
"""Liest die Packungsgroesse aus dem Produktnamen (z. B. 'VitaLink Turbo+, 250 ml')."""
import re

EINHEIT = {'l': 'L', 'liter': 'L', 'litre': 'L', 'ltr': 'L',
           'ml': 'ml', 'kg': 'kg', 'g': 'g', 'gr': 'g', 'gramm': 'g',
           'stk': 'Stück', 'stück': 'Stück', 'tabletten': 'Tabletten'}


def inhalt(name):
    # letzte Mengenangabe im Namen gewinnt: "Tray à 150 Stk., 52 x 31 cm, 1 L"
    treffer = re.findall(r'(?<![\w,.])(\d+(?:[.,]\d+)?)\s*(l|liter|litre|ltr|ml|kg|gramm|gr|g)\b',
                         name, re.I)
    if not treffer:
        return None
    zahl, e = treffer[-1]
    e = EINHEIT.get(e.lower())
    if not e:
        return None
    z = float(zahl.replace(',', '.'))
    grenze = {'L': 1000, 'kg': 1000, 'ml': 25000, 'g': 25000}.get(e, 1000)
    if z <= 0 or z > grenze:
        return None
    # Masse wie "52 x 31 x 3 cm" liefern kein g/l - schon durch die Einheit ausgeschlossen
    s = f'{z:g}'.replace('.', ',')
    return f'{s} {e}'


if __name__ == '__main__':
    import json, collections
    P = json.load(open('kat_1132.json'))
    c, ohne = collections.Counter(), []
    for p in P:
        i = inhalt(p['name'])
        if i:
            c[i] += 1
        else:
            ohne.append(p['name'])
    print(f'{sum(c.values())} von {len(P)} mit Inhalt')
    print(dict(c.most_common(18)))
    print('\nohne Inhalt, Beispiele:')
    for n in ohne[:12]:
        print('  ', n[:74])
