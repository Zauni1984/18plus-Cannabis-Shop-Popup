# -*- coding: utf-8 -*-
"""Legt den PURIZE Regular Size Leopard 50er auf hanfjack.de an.

Quelle der technischen Angaben ist das Schwesterprodukt 13690
(PURIZE Regular Size 9mm Weiss, 50 Stueck) - dieselbe Filterreihe, nur ein
anderes Design. Erfunden wird nichts: Durchmesser, Laenge, Fuellung,
Endkappen und Herkunft stammen von dort.

min_age wird bewusst nicht mitgeschickt.
"""
import json, base64, urllib.request

S='/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
CK=CS=None
for z in open(S+'.wc_creds'):
    if 'CK=' in z: CK=z.strip().split('CK=',1)[1]
    if 'CS=' in z: CS=z.strip().split('CS=',1)[1]
AUTH='Basic '+base64.b64encode(f'{CK}:{CS}'.encode()).decode()

BESCHREIBUNG = """<p><strong>PURIZE Aktivkohlefilter Regular Size 9mm</strong> im Leopard-Design: 9 mm Durchmesser (technisch 8,3 mm), 35,7 mm Länge — deutlich mehr Fläche als die Slim-Varianten, gebaut für dicke Joints, Blunts und Pfeifen.</p>
<p>Technisch ist es derselbe Filter wie die weiße Regular-Variante. Das Leopard-Design betrifft die Optik, nicht den Aufbau.</p>
<h3 style="margin-top:1.8em">Warum die Füllung hier anders ist</h3>
<p>Bei dieser Breite ändert sich die Füllung: Statt Kokosnuss-Aktivkohle wie in den schmaleren PURIZE-Filtern kommt Steinkohle-Aktivkohle zum Einsatz. Sie hat andere Korneigenschaften und eignet sich für die größere Filterfläche der Regular-Reihe. Am grundsätzlichen Aufbau ändert das nichts: Keramikkappen an beiden Enden, keine Einbaurichtung.</p>
<h3 style="margin-top:1.8em">Für dicke Konstruktionen gebaut</h3>
<p>Die 35,7 mm Länge geben bei breiten Papers und Pfeifenköpfen mehr Halt als ein Slim-Filter. Wer überwiegend dünne Joints dreht, ist mit XTRA Slim oder Slim besser bedient — der Regular ist für die Fälle gedacht, in denen ein schmaler Filter im breiten Paper verrutscht.</p>
<h3 style="margin-top:1.8em">Was den Regular Leopard ausmacht</h3>
<ul>
<li><strong>9 mm (8,3 mm technisch):</strong> Für dicke Joints, Blunts und Pfeifen.</li>
<li><strong>35,7 mm Länge:</strong> Mehr Halt in breiten Papers.</li>
<li><strong>Steinkohle-Aktivkohle:</strong> Andere Füllung als die schmaleren PURIZE-Filter.</li>
<li><strong>Keramik an beiden Enden:</strong> Keine Einbaurichtung.</li>
<li><strong>Leopard-Design:</strong> Optische Variante der Regular-Reihe.</li>
<li><strong>Hergestellt in Deutschland.</strong></li>
</ul>
<h3 style="margin-top:1.8em">Technische Details im Überblick</h3>
<table>
<thead>
<tr><td><strong>Merkmal</strong></td><td><strong>Details</strong></td></tr>
</thead>
<tbody>
<tr><td><strong>Filtertyp</strong></td><td>PURIZE Regular, Aktivkohlefilter</td></tr>
<tr><td><strong>Design</strong></td><td>Leopard</td></tr>
<tr><td><strong>Durchmesser</strong></td><td>9 mm (8,3 mm technisch)</td></tr>
<tr><td><strong>Länge</strong></td><td>35,7 mm</td></tr>
<tr><td><strong>Füllung</strong></td><td>Steinkohle-Aktivkohle</td></tr>
<tr><td><strong>Endkappen</strong></td><td>beidseitig Keramik</td></tr>
<tr><td><strong>Inhalt</strong></td><td>50 Stück im Beutel</td></tr>
<tr><td><strong>Herstellung</strong></td><td>Deutschland</td></tr>
</tbody>
</table>
<h3 style="margin-top:1.8em">Praxistipps</h3>
<p><strong>Für breite Papers:</strong> Bei King-Size- oder Wide-Papern sitzt der Regular fester als ein Slim-Filter.</p>"""

KURZ = """<p><strong>Für dicke Konstruktionen:</strong> Der <strong>PURIZE Regular 9mm</strong> im Leopard-Design misst 35,7 mm bei 9 mm Durchmesser und nutzt Steinkohle- statt Kokosnuss-Aktivkohle. Keramikkappen beidseitig, 50 Stück im Beutel, hergestellt in Deutschland.</p>"""

produkt = {
    'name': 'PURIZE Aktivkohlefilter Regular Size 9mm Leopard 50 Stück',
    'slug': 'purize-aktivkohlefilter-regular-size-9mm-leopard',
    'type': 'simple',
    'status': 'draft',
    'catalog_visibility': 'visible',
    'sku': 'HJ-7310482',
    'regular_price': '7.48',                 # 8,90 EUR brutto bei 19 %
    'tax_status': 'taxable',
    'tax_class': '',
    'manage_stock': True,
    'stock_quantity': 16,
    'stock_status': 'instock',
    'backorders': 'notify',
    'weight': '0.09',
    'shipping_class': 'paket-standard',
    'description': BESCHREIBUNG,
    'short_description': KURZ,
    'categories': [{'id': 4554}],            # Regular 8-9mm
    'brands': [1848],                # PURIZE
    'attributes': [
        {'id': 66, 'name': 'Motiv',    'options': ['Leopard'],   'visible': True, 'variation': False},
        {'id': 2,  'name': 'Inhalt',   'options': ['50 Stück'],  'visible': True, 'variation': False},
        {'id': 12, 'name': 'Variante', 'options': ['9mm'],       'visible': True, 'variation': False},
    ],
    'tags': [{'id': i} for i in (1346, 4605, 10203, 10669, 10725, 10788, 10792, 349, 1976, 7522)],
    'meta_data': [
        {'key': '_unit',              'value': 'stueck'},
        {'key': '_unit_base',         'value': '1'},
        {'key': '_unit_product',      'value': '50'},
        {'key': '_unit_price_auto',   'value': 'yes'},
        {'key': '_yoast_wpseo_title',    'value': 'PURIZE Regular 9 mm Leopard – 50 Aktivkohlefilter'},
        {'key': '_yoast_wpseo_metadesc', 'value': 'PURIZE Regular 9 mm im Leopard-Design, 35,7 mm lang: '
                                                  'Steinkohle-Aktivkohle, Keramikkappen beidseitig, 50 Stück '
                                                  'im Beutel. Jetzt bei Hanfjack.'},
        {'key': '_yoast_wpseo_focuskw',  'value': 'PURIZE Regular Leopard'},
    ],
}

def ruf(pfad, daten=None, methode='GET'):
    d = json.dumps(daten).encode() if daten is not None else None
    r = urllib.request.Request('https://hanfjack.de/wp-json/wc/v3/'+pfad, data=d, method=methode,
        headers={'Authorization':AUTH,'Content-Type':'application/json','User-Agent':'hj/1'})
    try:
        with urllib.request.urlopen(r, timeout=120) as f: return json.load(f)
    except urllib.error.HTTPError as e:
        print('HTTP', e.code, e.read().decode()[:600]); raise

if __name__ == '__main__':
    a = ruf('products', produkt, 'POST')
    print('angelegt:', a['id'], a['name'])
    print('  slug   :', a['slug'], '| status:', a['status'], '| sku:', a['sku'])
    print('  Preis  :', a['regular_price'], 'netto | Bestand:', a['stock_quantity'])
    json.dump(a, open(S+'purize_neu.json','w'), ensure_ascii=False)
