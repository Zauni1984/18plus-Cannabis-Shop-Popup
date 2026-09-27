# -*- coding: utf-8 -*-
"""Ordnet die 14 Juicy-Jay's-Bilder zu.

Die Zuordnung laeuft ueber den Dateinamen und wurde an zwei Stichproben
gegen das Bild selbst geprueft: "Bluebberry" (Tippfehler im Dateinamen) ist
tatsaechlich Blueberry King Size Slim, "Melon-Mango" ist Mello Mango.

Bei 9006 werden die beiden alten Bilder ersetzt, nicht ergaenzt. Die alten
Mediendateien bleiben in der Mediathek - geloescht wird nichts.
"""
import json, base64, urllib.request

S='/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
CK=CS=None
for z in open(S+'.wc_creds'):
    if 'CK=' in z: CK=z.strip().split('CK=',1)[1]
    if 'CS=' in z: CS=z.strip().split('CS=',1)[1]
AUTH='Basic '+base64.b64encode(f'{CK}:{CS}'.encode()).decode()

ZUORDNUNG = [
    # (Produkt, Bild, Sorte)
    (45309, 45333, 'Grape 2in1'),
    (45310, 45325, 'Strawberry 2in1'),
    (45312, 45327, 'Watermelon 2in1'),
    (45313, 45330, 'Bubblegum 2in1'),
    (45314, 45329, 'Blueberry 2in1'),
    (45315, 45332, 'Cotton Candy 2in1'),
    (45316, 45334, 'Green Apple'),
    (45317, 45326, 'Strawberry'),
    (45318, 45337, 'Pineapple'),
    (45319, 45324, 'Raspberry'),
    (45320, 45331, 'Coconut'),
    (45321, 45336, 'Mello Mango'),
    (45322, 45335, 'Jamaican Rum'),
    (9006,  45328, 'Blueberry KSS (Austausch)'),
]

def ruf(pfad, daten=None, methode='GET'):
    d = json.dumps(daten).encode() if daten is not None else None
    r = urllib.request.Request('https://hanfjack.de/wp-json/wc/v3/'+pfad, data=d, method=methode,
        headers={'Authorization':AUTH,'Content-Type':'application/json'})
    with urllib.request.urlopen(r, timeout=120) as f: return json.load(f)

if __name__ == '__main__':
    for pid, bild, sorte in ZUORDNUNG:
        vorher = ruf(f'products/{pid}?_fields=id,name,images')
        a = ruf(f'products/{pid}', {'images': [{'id': bild}]}, 'PUT')
        ist = [i['id'] for i in a['images']]
        zeichen = 'OK' if ist == [bild] else 'PRUEFEN'
        print(f"  {pid:6} {sorte:26} {len(vorher['images'])} -> {ist}  {zeichen}")
