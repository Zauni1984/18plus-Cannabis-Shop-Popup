# -*- coding: utf-8 -*-
"""Anesia Seeds Organic Rolling Papers King Size auf hanfjack.de anlegen.

Alle Angaben stammen aus der Ansage: 32 Blaettchen, King Size, schwarzes
Heftchen mit Anesia-Logo und Krokodil-Motiv, 0,001 kg, keine EAN, 50 lagernd,
1,00 EUR brutto. Online gibt es zu dem Heftchen nichts weiter - Papier,
Gummierung, Grammatur und Blattmass bleiben deshalb leer und werden im
Hinweistext auch als offen benannt.

Entwurf, bis die Bilder da sind.
"""
import json, hj

SKU = 'HJ-3192942'

KURZ = ('<p><strong>Schwarzes Heftchen mit Krokodil:</strong> 32 '
        'King-Size-Blättchen von Anesia Seeds – das Cover trägt das '
        'Markenlogo und ein Krokodil-Motiv.</p>')

LANG = (
 '<p>Die <strong>Anesia Seeds Organic Rolling Papers</strong> sind das '
 'Blättchen-Heft zur Samenbank: 32 Blättchen im King-Size-Format in einem '
 'schwarzen Heftchen, das auf der Vorderseite das Anesia-Logo und ein '
 'Krokodil-Motiv trägt.</p>\n'
 '<p>Ein Heftchen wiegt rund ein Gramm und verschwindet flach in Drehbox, '
 'Hosentasche oder Tabakbeutel.</p>\n'
 '<h3 style="margin-top:1.8em">Auf einen Blick:</h3>\n'
 '<ul>\n'
 '<li><b>Format:</b> King Size</li>\n'
 '<li><b>Inhalt je Heftchen:</b> 32 Blättchen</li>\n'
 '<li><b>Heftchen:</b> schwarz, mit Anesia-Logo und Krokodil-Motiv</li>\n'
 '<li><b>Gewicht:</b> rund 1 g je Heftchen</li>\n'
 '</ul>\n'
 '<h3 style="margin-top:1.8em">Hinweise</h3>\n'
 '<p>Anesia Seeds veröffentlicht zu diesem Heftchen keine weiteren Angaben: '
 'Papiermaterial, Gummierung, Grammatur und Blattmaß sind nicht belegt, und '
 'eine EAN gibt es zu dem Artikel nicht. Die Bezeichnung „Organic“ stammt '
 'aus der Artikelbezeichnung des Herstellers – eine Zertifizierung ist damit '
 'nicht nachgewiesen. Geschätzte Werte tragen wir nicht ein.</p>'
)

neu = {
    'name': 'Anesia Seeds Organic Rolling Papers King Size',
    'type': 'simple',
    'status': 'draft',                 # bis die Bilder da sind
    'sku': SKU,
    'regular_price': '0.84',           # 1,00 EUR brutto bei 19 %
    'tax_class': '',
    'shipping_class': 'paket-standard',
    'weight': '0.001',
    'manage_stock': True,
    'stock_quantity': 50,
    'backorders': 'no',
    'catalog_visibility': 'visible',
    'short_description': KURZ,
    'description': LANG,
    'categories': [{'id': 607}],
    'tags': [{'id': 14916}, {'id': 264}, {'id': 2658}, {'id': 7522}],
    'brands': [2620],
    'attributes': [
        {'id': 65, 'visible': True, 'variation': False, 'options': ['King Size']},
        {'id': 2,  'visible': True, 'variation': False,
         'options': ['1 Heftchen mit 32 Blättchen']},
        {'id': 3,  'visible': True, 'variation': False, 'options': ['Schwarz']},
        {'id': 66, 'visible': True, 'variation': False, 'options': ['Krokodil']},
    ],
    'meta_data': [
        {'key': '_yoast_wpseo_title',
         'value': 'Anesia Seeds Organic Rolling Papers King Size'},
        {'key': '_yoast_wpseo_metadesc',
         'value': 'Anesia Seeds Organic Rolling Papers: 32 King-Size-Blättchen '
                  'im schwarzen Heftchen mit Krokodil-Motiv. Jetzt bei Hanfjack.'},
        {'key': '_yoast_wpseo_focuskw', 'value': 'Anesia Seeds Rolling Papers'},
    ],
}

for m in neu['meta_data']:
    print(m['key'], len(m['value']))

p = hj.ruf('products', neu, 'POST')
print('angelegt:', p['id'], p['name'], p['permalink'])

q = hj.ruf(f'products/{p["id"]}', {'manufacturer': {'id': 6973},
                                   'delivery_time': {'id': 4236}}, 'PUT')
print('Hersteller:', (q.get('manufacturer') or {}).get('name'),
      '| Lieferzeit:', (q.get('delivery_time') or {}).get('name'))
json.dump(q, open('/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/anesia_neu.json','w'), ensure_ascii=False)
