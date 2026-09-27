# -*- coding: utf-8 -*-
"""Nacharbeit zur Bridgelux-Umstellung.

A) Attribute der SF-1000 und SF-1000-D auf den Stand 2026 und auf den
   Aufbau der Schwesterprodukte bringen.
B) Die vier gleichbedeutenden Bridgelux-Schlagworte zu einem zusammenfuehren
   und die leer gebliebenen Begriffe loeschen.

Quelle der Werte: spider-farmer.com/products/sf-1000-led-grow-light/
(Datenblatt und Vergleichstabelle der SF-Serie, Stand 24.09.2026).

Nicht angetastet: "Samsung LM301H EVO" (6271) haengt an vier AC-Infinity-
Produkten, die tatsaechlich Samsung-Dioden fuehren.
"""
import sys

S = '/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
sys.path.insert(0, S)
import hjapi

# Attribut-IDs wie bei den Schwesterprodukten
WEEE, LEISTUNG, PPF, PPE, SPEKTRUM, SPANNUNG, MASSE, GEWICHT, FLAECHE = (
    23, 26, 29, 30, 28, 27, 57, 59, 61)

SPEKTRUM_WERT = '660–665 nm, 730–740 nm, 2800–3000 K, 4800–5000 K'


def attr(paare):
    aus = []
    for pos, (aid, name, wert) in enumerate(paare):
        aus.append({'id': aid, 'name': name, 'position': pos, 'visible': True,
                    'variation': False, 'options': [wert]})
    return aus


ATTRIBUTE = {
    # SF1000, Version 2026: PPF 249,2 µmol/s, PPE 2,5, 100 W @AC100-277V,
    # Lichtmass 32,5 x 29,0 x 5,9 cm, Bruttogewicht 1,92 kg, max 3 x 3 ft
    16213: attr([
        (WEEE, 'WEEE Nummer', '39526074'),
        (LEISTUNG, 'Leistungsaufnahme', '100 W ± 5 %'),
        (PPF, 'PPF', '249,2 µmol/s'),
        (PPE, 'PPE', '2,5 µmol/J'),
        (SPEKTRUM, 'Lichtspektrum', SPEKTRUM_WERT),
        (SPANNUNG, 'Spannung', '100–277 V (Wechselstrom)'),
        (MASSE, 'Abmessungen', '32,5 × 29,0 × 5,9 cm'),
        (GEWICHT, 'Gewicht', '1,92 kg'),
        (FLAECHE, 'Ausgelegte Fläche', '3 × 3 ft (rund 90 × 90 cm)'),
    ]),
    # SF1000D: PPF 211 µmol/s und Flaeche 60 x 60 cm laut Vergleichstabelle
    16214: attr([
        (WEEE, 'WEEE Nummer', '39526074'),
        (LEISTUNG, 'Leistungsaufnahme', '100 W ± 5 %'),
        (PPF, 'PPF', '211 µmol/s'),
        (PPE, 'PPE', '2,5 µmol/J'),
        (SPEKTRUM, 'Lichtspektrum', SPEKTRUM_WERT),
        (SPANNUNG, 'Spannung', '100–277 V (Wechselstrom)'),
        (MASSE, 'Abmessungen', '31,5 × 28,0 × 5,6 cm'),
        (GEWICHT, 'Gewicht', '1,88 kg'),
        (FLAECHE, 'Ausgelegte Fläche', '2 × 2 ft (rund 60 × 60 cm)'),
    ]),
}

TAG_ZIEL = 6284                       # "Bridgelux LEDs"
TAG_WEG = {7107: 'Bridgelux LED', 11400: 'Bridgelux 3030',
           11427: 'Bridgelux 3030 LED', 6555: 'Samsung 301H'}


def attribute_setzen():
    for pid, neu in ATTRIBUTE.items():
        alt = hjapi.ruf(f'products/{pid}?_fields=id,sku,attributes', pause=2)
        vorher = {a['name']: a['options'] for a in alt.get('attributes') or []}
        q = hjapi.ruf(f'products/{pid}', {'attributes': neu}, 'PUT', pause=3)
        nachher = {a['name']: a['options'] for a in q.get('attributes') or []}
        print(f"{pid} {q['sku']}: {len(vorher)} -> {len(nachher)} Attribute")
        for name in sorted(set(vorher) | set(nachher)):
            a, b = vorher.get(name), nachher.get(name)
            if a != b:
                print(f"    {name:<20} {a} -> {b}")


def tags_zusammenfuehren():
    for tid, name in TAG_WEG.items():
        betroffen = hjapi.ruf(
            f'products?tag={tid}&per_page=50&status=any&_fields=id,sku,tags', pause=2)
        for p in betroffen:
            ids = [t['id'] for t in p.get('tags') or [] if t['id'] != tid]
            if TAG_ZIEL not in ids:
                ids.append(TAG_ZIEL)
            q = hjapi.ruf(f'products/{p["id"]}', {'tags': [{'id': i} for i in ids]},
                          'PUT', pause=3)
            print(f"  {p['id']} {q['sku']:<16} {name} -> Bridgelux LEDs")
        # Begriff loeschen, aber nur wenn er wirklich leer ist
        pruef = hjapi.ruf(f'products/tags/{tid}?_fields=id,name,count', pause=2)
        if pruef.get('count'):
            print(f"  ACHTUNG {tid} {name}: noch {pruef['count']} Produkte, nicht geloescht")
            continue
        hjapi.ruf(f'products/tags/{tid}?force=true', None, 'DELETE', pause=3)
        print(f"  Begriff geloescht: {tid} {name}")


if __name__ == '__main__':
    print('=== A) Attribute')
    attribute_setzen()
    print('\n=== B) Schlagworte')
    tags_zusammenfuehren()
    print('\n=== Kontrolle')
    for t in hjapi.ruf('products/tags?search=bridgelux&per_page=20&_fields=id,name,count', pause=2):
        print(f"  {t['id']:>6} {t['name']:<24} count={t['count']}")
    for t in hjapi.ruf('products/tags?search=samsung&per_page=20&_fields=id,name,count', pause=2):
        print(f"  {t['id']:>6} {t['name']:<24} count={t['count']}")
