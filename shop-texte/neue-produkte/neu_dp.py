# -*- coding: utf-8 -*-
"""Legt die Dutch Passion Rolling Papers Slim + Tips auf hanfjack.de an.

Alle Angaben stammen von der Herstellerseite
dutch-passion.com/de/merchandise/rolling-papers-slim-tips:
King Size Slim, Slow Burning, Tips enthalten, Verpackung entfaltet sich zu
einem Rolling Tray im Origami-Stil und laesst sich danach wieder flach
zusammenlegen. Artikelnummer ROLPSINGLE.

Dutch Passion nennt weder Blattzahl noch Papiermaterial noch eine EAN - das
steht so auch im Hinweistext. Geschaetzt wird nichts.

Entwurf, weil die Bilder noch fehlen. min_age wird nicht mitgeschickt.
"""
import json, base64, urllib.request

S='/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
CK=CS=None
for z in open(S+'.wc_creds'):
    if 'CK=' in z: CK=z.strip().split('CK=',1)[1]
    if 'CS=' in z: CS=z.strip().split('CS=',1)[1]
AUTH='Basic '+base64.b64encode(f'{CK}:{CS}'.encode()).decode()

BESCHREIBUNG = """<p>Die <strong>Dutch Passion Rolling Papers Slim + Tips</strong> sind King-Size-Slim-Blättchen mit Tips im selben Heft. Der Unterschied zu anderen Heften steckt in der Verpackung: Sie lässt sich zu einem Rolling Tray im Origami-Stil aufklappen.</p>
<p>Aufgefaltet liegt alles auf einer Fläche mit Rand, danach wird das Tray wieder zu einem flachen Päckchen zusammengelegt. Unterwegs ersetzt das die Unterlage, die man sonst nicht dabeihat.</p>
<h3 style="margin-top:1.8em">Die Verpackung ist das Tray</h3>
<p>Ein Rolling Tray nimmt sonst Platz weg – dieses ist das Päckchen selbst. Was beim Drehen danebengeht, bleibt auf der Fläche statt auf dem Tisch, und nach Gebrauch faltet man es zurück in die Hosentasche.</p>
<h3 style="margin-top:1.8em">Auf einen Blick:</h3>
<ul>
<li><b>Format:</b> King Size Slim</li>
<li><b>Tips:</b> im Heft enthalten</li>
<li><b>Verpackung:</b> entfaltet sich zum Rolling Tray, faltet flach zurück</li>
<li><b>Brennverhalten:</b> langsam brennend</li>
<li><b>Artikelnummer des Herstellers:</b> ROLPSINGLE</li>
</ul>
<h3 style="margin-top:1.8em">Hinweise</h3>
<p>Dutch Passion nennt für dieses Heft weder die Blattzahl noch das Papiermaterial – wir tragen dazu keine geschätzten Werte ein. Was gesichert ist, steht oben.</p>"""

KURZ = ("<p><strong>Papers, Tips und Unterlage in einem:</strong> King-Size-Slim-Blättchen "
        "von Dutch Passion mit Tips im Heft – die Verpackung entfaltet sich zu einem "
        "Rolling Tray im Origami-Stil und lässt sich danach wieder flach zusammenlegen.</p>")

produkt = {
    'name': 'Dutch Passion Rolling Papers King Size Slim + Tips',
    'slug': 'dutch-passion-rolling-papers-king-size-slim-tips',
    'type': 'simple', 'status': 'draft', 'catalog_visibility': 'visible',
    'sku': 'HJ-7777627',
    'mpn': 'ROLPSINGLE',
    'regular_price': '1.68',                 # 2,00 EUR brutto bei 19 %
    'tax_status': 'taxable', 'tax_class': '',
    'manage_stock': True, 'stock_quantity': 7, 'stock_status': 'instock',
    'backorders': 'no', 'shipping_class': 'paket-standard',
    'description': BESCHREIBUNG, 'short_description': KURZ,
    'categories': [{'id': 607}],             # Papers
    'brands': [2621],                        # Dutch Passion
    'attributes': [
        {'id': 65, 'name': 'Format', 'options': ['King Size Slim'], 'visible': True, 'variation': False},
        {'id': 2,  'name': 'Inhalt', 'options': ['1 Heft'],         'visible': True, 'variation': False},
    ],
    'tags': [{'id': i} for i in (279, 7027, 14916, 5740, 733, 7522)],
    'meta_data': [
        {'key': '_ts_mpn', 'value': 'ROLPSINGLE'},
        {'key': '_yoast_wpseo_title',    'value': 'Dutch Passion Rolling Papers King Size Slim + Tips'},
        {'key': '_yoast_wpseo_metadesc', 'value': 'Dutch Passion King Size Slim Papers mit Tips im Heft – '
                                                  'die Verpackung entfaltet sich zum Rolling Tray im '
                                                  'Origami-Stil. Jetzt bei Hanfjack.'},
        {'key': '_yoast_wpseo_focuskw',  'value': 'Dutch Passion Rolling Papers'},
    ],
}

def ruf(pfad, daten=None, methode='GET'):
    d = json.dumps(daten).encode() if daten is not None else None
    r = urllib.request.Request('https://hanfjack.de/wp-json/wc/v3/'+pfad, data=d, method=methode,
        headers={'Authorization':AUTH,'Content-Type':'application/json'})
    try:
        with urllib.request.urlopen(r, timeout=120) as f: return json.load(f)
    except urllib.error.HTTPError as e:
        print('HTTP', e.code, e.read().decode()[:400]); raise

if __name__ == '__main__':
    a = ruf('products', produkt, 'POST')
    print('angelegt:', a['id'], a['name'])
    b = ruf(f"products/{a['id']}", {'delivery_time': {'id': 4236}, 'manufacturer': {'id': 6071}}, 'PUT')
    m = {x['key']: x['value'] for x in b['meta_data']}
    print('  Status     ', b['status'], '| SKU', b['sku'], '| MPN', b.get('mpn'))
    print('  VK         ', b['regular_price'], 'netto =', round(float(b['regular_price'])*1.19, 2), 'brutto')
    print('  Bestand    ', b['stock_quantity'], '| backorders', b['backorders'])
    print('  Lieferzeit ', (b.get('delivery_time') or {}).get('name'))
    print('  Hersteller ', (b.get('manufacturer') or {}).get('name'))
    print('  Marke      ', [x['name'] for x in (b.get('brands') or [])])
    print('  Attribute  ', [(x['name'], x['options']) for x in b['attributes']])
    print('  Tags       ', [t['name'] for t in b['tags']])
    print('  Yoast      ', len(m.get('_yoast_wpseo_title','')), '/', len(m.get('_yoast_wpseo_metadesc','')))
    print('  min_age    ', b.get('min_age'), '| Bilder', len(b['images']))
