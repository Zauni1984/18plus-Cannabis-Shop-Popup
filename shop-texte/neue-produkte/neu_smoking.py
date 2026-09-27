# -*- coding: utf-8 -*-
"""Legt das Smoking Supreme King Size Slim 2in1 Einzelheft auf hanfjack.de an.

Angaben aus zwei Quellen, beide belastbar:
  - Produkt 18977 (dieselbe Ware als 24er-Box): Format 110 x 44 mm,
    100 % pflanzlich, FSC-zertifiziert, Naturgummi, Slow Burning,
    33 Blaettchen + 33 Tips je Heft, Kategorie, Marke, Hersteller.
  - Die Produktbilder: Ultra Smooth Touch, ultrafein, Made in Spain
    (Barcelona), Tips mit Spezialschnitt zum Rollen.

Der MPN bleibt leer: das Muster VE-<Einzelnummer> traegt im Shop nicht,
18980 und 18981 tragen beide VE-SMK-Kukuxumusu-KS, also eine fremde Nummer.
min_age wird nicht mitgeschickt.
"""
import json, base64, urllib.request

S='/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
CK=CS=None
for z in open(S+'.wc_creds'):
    if 'CK=' in z: CK=z.strip().split('CK=',1)[1]
    if 'CS=' in z: CS=z.strip().split('CS=',1)[1]
AUTH='Basic '+base64.b64encode(f'{CK}:{CS}'.encode()).decode()

BESCHREIBUNG = """<p><strong>Smoking Supreme King Size Slim</strong> ist ein 2in1-Heft: 33 ultrafeine Blättchen im Format 110 × 44 mm und 33 Tips in derselben Packung. Die Tips tragen einen Spezialschnitt, mit dem sich der Filter ohne Nachschneiden rollen lässt.</p>
<p>Das Papier ist zu 100 % pflanzlich und FSC-zertifiziert, die Gummierung besteht aus Naturgummi ohne chemische Zusätze. Gefertigt wird in Barcelona.</p>
<h3 style="margin-top:1.8em">Ultrafein und langsam brennend</h3>
<p>Die Supreme-Reihe gehört zu den dünnsten Papieren von Smoking — das Papier tritt beim Rauchen hinter den Inhalt zurück. Zusammen mit dem langsamen, gleichmäßigen Abbrand bleibt weniger Papiergeschmack übrig als bei dickeren Sorten.</p>
<h3 style="margin-top:1.8em">Tips im Heft statt separat</h3>
<p>Weil die 33 Tips im selben Heft stecken, entfällt die zweite Packung in der Tasche. Die Anzahl geht auf: zu jedem Blättchen gehört ein Tip.</p>
<h3 style="margin-top:1.8em">Was das Supreme-Heft ausmacht</h3>
<ul>
<li><strong>33 Blättchen + 33 Tips:</strong> Beides im selben Heft.</li>
<li><strong>King Size Slim, 110 × 44 mm:</strong> Das gängige Format für längere Joints.</li>
<li><strong>Ultrafein:</strong> Eines der dünnsten Papiere von Smoking.</li>
<li><strong>Slow Burning:</strong> Langsamer, gleichmäßiger Abbrand.</li>
<li><strong>100 % pflanzlich, FSC-zertifiziert:</strong> Naturgummi ohne chemische Zusätze.</li>
<li><strong>Hergestellt in Spanien.</strong></li>
</ul>
<h3 style="margin-top:1.8em">Technische Details im Überblick</h3>
<table>
<thead>
<tr><td><strong>Merkmal</strong></td><td><strong>Details</strong></td></tr>
</thead>
<tbody>
<tr><td><strong>Produkt</strong></td><td>Smoking Supreme, Papers mit Tips</td></tr>
<tr><td><strong>Format</strong></td><td>King Size Slim, 110 × 44 mm</td></tr>
<tr><td><strong>Inhalt</strong></td><td>1 Heft mit 33 Blättchen und 33 Tips</td></tr>
<tr><td><strong>Papier</strong></td><td>ultrafein, 100 % pflanzlich, FSC-zertifiziert</td></tr>
<tr><td><strong>Gummierung</strong></td><td>Naturgummi ohne chemische Zusätze</td></tr>
<tr><td><strong>Abbrand</strong></td><td>Slow Burning</td></tr>
<tr><td><strong>Herstellung</strong></td><td>Spanien (Barcelona)</td></tr>
</tbody>
</table>
<h3 style="margin-top:1.8em">Praxistipps</h3>
<p><strong>Tip zuerst rollen:</strong> Der Spezialschnitt gibt die Rollrichtung vor — erst den Tip formen, dann das Blättchen darum legen.</p>"""

KURZ = """<p><strong>Papers und Tips in einem Heft:</strong> Das <strong>Smoking Supreme King Size Slim</strong> enthält 33 ultrafeine Blättchen im Format 110 × 44 mm und 33 Tips mit Spezialschnitt. 100 % pflanzlich, FSC-zertifiziert, Naturgummi, langsamer Abbrand — hergestellt in Spanien.</p>"""

produkt = {
    'name': 'Smoking Supreme King Size Slim 2in1',
    'slug': 'smoking-supreme-king-size-slim-2in1',
    'type': 'simple',
    'status': 'publish',
    'catalog_visibility': 'visible',
    'sku': 'HJ-2951736',
    'gtin': '8414775023164',
    'regular_price': '1.68',                 # 2,00 EUR brutto bei 19 %
    'tax_status': 'taxable',
    'tax_class': '',
    'manage_stock': True,
    'stock_quantity': 19,
    'stock_status': 'instock',
    'backorders': 'no',                      # kein Lieferrueckstand
    'weight': '0.019',                       # wie das baugleiche Red 2in1 (8992)
    'shipping_class': 'paket-standard',
    'description': BESCHREIBUNG,
    'short_description': KURZ,
    'categories': [{'id': 607}],             # Papers
    'brands': [1762],                        # Smoking
    'images': [{'id': 45302}, {'id': 45304}, {'id': 45303}],
    'attributes': [
        {'id': 65, 'name': 'Format',   'options': ['King Size Slim'],    'visible': True, 'variation': False},
        {'id': 2,  'name': 'Inhalt',   'options': ['1 Heft'],            'visible': True, 'variation': False},
        {'id': 58, 'name': 'Material', 'options': ['100 % pflanzlich'],  'visible': True, 'variation': False},
    ],
    'tags': [{'id': i} for i in (279, 1993, 7392, 7395, 7027, 14916, 7522)],
    'meta_data': [
        {'key': '_ts_gtin', 'value': '8414775023164'},
        {'key': '_yoast_wpseo_title',    'value': 'Smoking Supreme King Size Slim – 33 Blatt + 33 Tips'},
        {'key': '_yoast_wpseo_metadesc', 'value': 'Smoking Supreme King Size Slim, 110 × 44 mm: 33 ultrafeine '
                                                  'Blättchen und 33 Tips mit Spezialschnitt im Heft, FSC-zertifiziert '
                                                  'und Naturgummi. Jetzt bei Hanfjack.'},
        {'key': '_yoast_wpseo_focuskw',  'value': 'Smoking Supreme King Size Slim'},
    ],
}

def ruf(pfad, daten=None, methode='GET'):
    d = json.dumps(daten).encode() if daten is not None else None
    r = urllib.request.Request('https://hanfjack.de/wp-json/wc/v3/'+pfad, data=d, method=methode,
        headers={'Authorization':AUTH,'Content-Type':'application/json','User-Agent':'hj/1'})
    try:
        with urllib.request.urlopen(r, timeout=120) as f: return json.load(f)
    except urllib.error.HTTPError as e:
        print('HTTP', e.code, e.read().decode()[:500]); raise

if __name__ == '__main__':
    a = ruf('products', produkt, 'POST')
    print('angelegt:', a['id'], a['name'])
    b = ruf(f"products/{a['id']}", {'delivery_time': {'id': 4236}, 'manufacturer': {'id': 1775}}, 'PUT')
    print('  Lieferzeit :', (b.get('delivery_time') or {}).get('name'))
    print('  Hersteller :', (b.get('manufacturer') or {}).get('name'))
    json.dump(b, open(S+'smoking_neu.json','w'), ensure_ascii=False)
