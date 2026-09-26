# -*- coding: utf-8 -*-
"""RQS Organic Rolling Papers King Size auf hanfjack.de anlegen.

Quelle aller technischen Angaben: die Herstellerseite
royalqueenseeds.de/free-seeds/251-rqs-biologische-blaettchen.html
(Rohquelle liegt als rqs.html im Scratchpad) und die drei Produktbilder.

Belegt sind: 32 ungebleichte Blaettchen je Heftchen, natuerlicher Zellstoff
und Gummi arabicum aus Europa, ultraduenn und langsam brennend, Heftchen aus
Kraftpapier (Kiefer, Bambus, Agrarabfaelle), Mass 110 x 45 mm,
Artikelnummer RQSPR002OP.

Nicht belegt und deshalb nicht im Text: Filter Tips. Die nennt nur eine
Kundenrezension auf der Herstellerseite; im Herstellertext stehen sie nicht,
auf den Bildern ist kein Tip-Heftchen zu sehen.
"""
import json, hj

SKU = 'HJ-2022389'
GTIN = '8435523607214'   # die Nummer auf der Herstellerseite

KURZ = ('<p><strong>Ungebleicht und biologisch:</strong> 32 King-Size-Blättchen '
        'von Royal Queen Seeds aus natürlichem Zellstoff und Gummi arabicum '
        'aus Europa – ultradünn, langsam brennend, im Heftchen aus '
        'Kraftpapier.</p>')

LANG = (
 '<p>Die <strong>RQS Organic Rolling Papers</strong> sind die biologische Linie '
 'der Royal-Queen-Seeds-Blättchen: 32 ungebleichte Blättchen je Heftchen, '
 'gefertigt aus natürlichem Zellstoff und Gummi arabicum. Beide Rohstoffe '
 'werden in Europa beschafft.</p>\n'
 '<p>Im King-Size-Format von 110 × 45 mm sind die Blättchen ultradünn, '
 'brennen langsam ab und geben sanfte Züge – das Papier bleibt im '
 'Hintergrund, der Geschmack kommt vom Inhalt.</p>\n'
 '<h3 style="margin-top:1.8em">Das Heftchen ist Teil der Idee</h3>\n'
 '<p>Nachhaltig ist nicht nur das Papier, sondern auch die Hülle: Das Heftchen '
 'besteht aus Kraftpapier, das aus Kiefer, Bambus und Agrarabfällen hergestellt '
 'wird. Ein paar davon passen flach in Drehbox oder Reisetasche.</p>\n'
 '<h3 style="margin-top:1.8em">Auf einen Blick:</h3>\n'
 '<ul>\n'
 '<li><b>Format:</b> King Size, 110 × 45 mm</li>\n'
 '<li><b>Inhalt je Heftchen:</b> 32 Blättchen</li>\n'
 '<li><b>Papier:</b> natürlicher Zellstoff, ungebleicht, ultradünn</li>\n'
 '<li><b>Gummierung:</b> Gummi arabicum</li>\n'
 '<li><b>Herkunft der Rohstoffe:</b> Europa</li>\n'
 '<li><b>Brennverhalten:</b> langsam brennend</li>\n'
 '<li><b>Heftchen:</b> Kraftpapier aus Kiefer, Bambus und Agrarabfällen</li>\n'
 '<li><b>Artikelnummer des Herstellers:</b> RQSPR002OP</li>\n'
 '</ul>\n'
 '<h3 style="margin-top:1.8em">Hinweise</h3>\n'
 '<p>Filter Tips gehören nach dem Herstellertext nicht zum Lieferumfang, und auf '
 'den Produktbildern sind auch keine zu sehen. Zum Gewicht des Heftchens '
 'veröffentlicht Royal Queen Seeds keine Angabe; geschätzte Werte tragen wir '
 'nicht ein.</p>'
)

neu = {
    'name': 'RQS Organic Rolling Papers King Size',
    'type': 'simple',
    'status': 'publish',
    'sku': SKU,
    'gtin': GTIN,
    'mpn': 'RQSPR002OP',
    'regular_price': '0.84',          # 1,00 EUR brutto bei 19 %
    'tax_class': '',
    'shipping_class': 'paket-standard',
    'manage_stock': True,
    'stock_quantity': 15,
    'backorders': 'no',
    'catalog_visibility': 'visible',
    'short_description': KURZ,
    'description': LANG,
    'categories': [{'id': 607}],
    'tags': [{'id': 14916}, {'id': 264}, {'id': 759}, {'id': 293},
             {'id': 7027}, {'id': 7522}],
    'brands': [3669],
    'images': [{'id': 45346}, {'id': 45347}],
    'attributes': [
        {'id': 65, 'visible': True, 'variation': False,
         'options': ['King Size, 110 × 45 mm']},
        {'id': 2, 'visible': True, 'variation': False,
         'options': ['1 Heftchen mit 32 Blättchen']},
        {'id': 58, 'visible': True, 'variation': False,
         'options': ['Zellstoff und Gummi arabicum']},
    ],
    'meta_data': [
        {'key': '_yoast_wpseo_title',
         'value': 'RQS Organic Rolling Papers King Size – 32 Blättchen'},
        {'key': '_yoast_wpseo_metadesc',
         'value': 'RQS Organic Rolling Papers: 32 ungebleichte King-Size-Blättchen '
                  'aus Zellstoff und Gummi arabicum, Heftchen aus Kraftpapier. '
                  'Jetzt bei Hanfjack.'},
        {'key': '_yoast_wpseo_focuskw', 'value': 'RQS Organic Rolling Papers'},
    ],
}

for m in neu['meta_data']:
    print(m['key'], len(m['value']))

p = hj.ruf('products', neu, 'POST')
print('angelegt:', p['id'], p['name'], p['permalink'])

q = hj.ruf(f'products/{p["id"]}', {'manufacturer': {'id': 6565},
                                   'delivery_time': {'id': 4236}}, 'PUT')
print('Hersteller:', q.get('manufacturer'), '| Lieferzeit:', q.get('delivery_time'))
json.dump(q, open('/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/rqs_neu.json','w'), ensure_ascii=False)
