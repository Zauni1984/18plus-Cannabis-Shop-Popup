# -*- coding: utf-8 -*-
"""Zwei G-Rollz-Einzelheftchen aus den Boxartikeln 30294 und 30290 ableiten.

Quellen: die Herstellerseite thenewways.com (New Ways BV ist im Shop als
Hersteller beider Boxen hinterlegt) mit den Artikelnummern GR08A-DIS und
GR09A-DIS - beide EANs stimmen mit den Box-GTINs im Shop ueberein - und die
Boxbilder, auf denen der Aufdruck des Einzelheftchens lesbar ist.

Belegt: 32 + 18 gratis = 50 Blaettchen je Heftchen, weisses Papier,
Diablos = King Slim Classic ultra thin im roten Heftchen, King's Choice =
King Size extra thin im schwarzen Heftchen, beide chlorfrei, ohne Gentechnik
und vegan, gefertigt bei einem Partnerbetrieb in Spanien.

Nicht belegt und deshalb leer: GTIN (die Nummern gehoeren den Displays),
MPN (nur GR08A-DIS/GR09A-DIS sind veroeffentlicht, die Einzelnummer nicht),
Gewicht und Blattmasse.

Preis fehlt noch - beide gehen als Entwurf raus.
"""
import json, hj

GEMEINSAM = dict(
    type='simple', status='draft', tax_class='', shipping_class='paket-standard',
    manage_stock=True, stock_quantity=50, backorders='no',
    catalog_visibility='visible', categories=[{'id': 607}], brands=[2004],
)

def hinweise(box_name):
    return (
     '<h3 style="margin-top:1.8em">Hinweise</h3>\n'
     '<p>Dies ist das <b>Einzelheftchen</b>. Die ganze Box mit 50 Heftchen steht '
     f'als „{box_name}“ separat im Shop. Filter Tips gehören nicht dazu; '
     'G-Rollz führt sie als eigene Artikel. Eine EAN gibt es nur für die Box, '
     'nicht für das einzelne Heftchen. Zu Gewicht, Grammatur und Blattmaß '
     'veröffentlicht G-Rollz keine Angaben – geschätzte Werte tragen wir nicht '
     'ein.</p>')

SIEGEL = (
 '<h3 style="margin-top:1.8em">Chlorfrei, ohne Gentechnik, vegan</h3>\n'
 '<p>Drei Siegel stehen auf dem Heftchen: Das Papier kommt ohne Chlor aus, die '
 'Rohstoffe sind nicht gentechnisch verändert, und auch die Gummierung ist '
 'rein pflanzlich – auf der Heftchenkante steht dafür „Vegan + Plant '
 'Based“. Gefertigt wird bei einem Partnerbetrieb in Spanien.</p>')

produkte = [
  dict(
    sku='HJ-4847587',
    name='G-Rollz Diablos King Size Slim',
    tags=[{'id': 14916}, {'id': 279}, {'id': 2012}, {'id': 1993},
          {'id': 1998}, {'id': 197}, {'id': 7522}],
    attributes=[
      {'id': 65, 'visible': True, 'variation': False, 'options': ['King Size Slim']},
      {'id': 2,  'visible': True, 'variation': False,
       'options': ['1 Heftchen mit 50 Blättchen']},
      {'id': 3,  'visible': True, 'variation': False, 'options': ['Rot']},
    ],
    short=('<p><strong>50 statt 32 Blättchen:</strong> Das rote '
           'Diablos-Heftchen von G-Rollz enthält 32 King-Size-Slim-Blättchen '
           'plus 18 gratis – ultradünn, chlorfrei und vegan.</p>'),
    lang=(
     '<p>Die <strong>G-Rollz Diablos King Slim Classic</strong> sind ultradünne '
     'King-Size-Slim-Blättchen aus weißem Papier. Wie die 50 zustande kommen, '
     'steht auf dem Heftchen selbst: 32 Blättchen plus 18 gratis.</p>\n'
     '<p>Dünnes Papier verbrennt langsamer und tritt geschmacklich weniger in den '
     'Vordergrund – der Grund, warum sich ultradünne Sorten gehalten haben.</p>\n'
     + SIEGEL + '\n'
     '<h3 style="margin-top:1.8em">Auf einen Blick:</h3>\n'
     '<ul>\n'
     '<li><b>Format:</b> King Size Slim</li>\n'
     '<li><b>Inhalt je Heftchen:</b> 50 Blättchen (32 + 18 gratis)</li>\n'
     '<li><b>Papier:</b> weiß, ultradünn</li>\n'
     '<li><b>Heftchen:</b> rot, Serie Diablos</li>\n'
     '<li><b>Ausgelobt:</b> chlorfrei, ohne Gentechnik, vegan</li>\n'
     '<li><b>Herstellung:</b> Partnerbetrieb in Spanien</li>\n'
     '</ul>\n'
     + hinweise('G-Rollz Diablos King Slim Classic ultra dünn - 1 Box')),
    yoast=('G-Rollz Diablos King Size Slim – 50 Blättchen',
           'G-Rollz Diablos: 50 ultradünne King-Size-Slim-Blättchen (32 + 18 '
           'gratis) im roten Heftchen, chlorfrei und vegan. Jetzt bei Hanfjack.',
           'G-Rollz Diablos'),
  ),
  dict(
    sku='HJ-8480023',
    name='G-Rollz King´s Choice King Size',
    tags=[{'id': 14916}, {'id': 264}, {'id': 2012},
          {'id': 1998}, {'id': 197}, {'id': 7522}],
    attributes=[
      {'id': 65, 'visible': True, 'variation': False, 'options': ['King Size']},
      {'id': 2,  'visible': True, 'variation': False,
       'options': ['1 Heftchen mit 50 Blättchen']},
      {'id': 3,  'visible': True, 'variation': False, 'options': ['Schwarz']},
    ],
    short=('<p><strong>50 statt 32 Blättchen:</strong> Das schwarze '
           'King´s-Choice-Heftchen von G-Rollz enthält 32 King-Size-Blättchen '
           'plus 18 gratis – extradünn, chlorfrei und vegan.</p>'),
    lang=(
     '<p>Die <strong>G-Rollz King´s Choice</strong> sind extradünne '
     'King-Size-Blättchen aus weißem Papier. Wie die 50 zustande kommen, steht '
     'auf dem Heftchen selbst: 32 Blättchen plus 18 gratis.</p>\n'
     '<p>King Size ist das Standardformat für längere Drehungen. G-Rollz führt '
     'die King´s Choice als <i>King Size Wide</i> – sie ist also breiter als '
     'eine King Size Slim und lässt sich entsprechend voller drehen.</p>\n'
     + SIEGEL + '\n'
     '<h3 style="margin-top:1.8em">Auf einen Blick:</h3>\n'
     '<ul>\n'
     '<li><b>Format:</b> King Size, vom Hersteller als King Size Wide geführt</li>\n'
     '<li><b>Inhalt je Heftchen:</b> 50 Blättchen (32 + 18 gratis)</li>\n'
     '<li><b>Papier:</b> weiß, extradünn</li>\n'
     '<li><b>Heftchen:</b> schwarz, Serie King´s Choice</li>\n'
     '<li><b>Ausgelobt:</b> chlorfrei, ohne Gentechnik, vegan</li>\n'
     '<li><b>Herstellung:</b> Partnerbetrieb in Spanien</li>\n'
     '</ul>\n'
     + hinweise('G-Rollz King´s Choice KS - 1 Box')),
    yoast=('G-Rollz King´s Choice King Size – 50 Blättchen',
           'G-Rollz King´s Choice: 50 extradünne King-Size-Blättchen (32 + 18 '
           'gratis) im schwarzen Heftchen, chlorfrei und vegan. Jetzt bei Hanfjack.',
           'G-Rollz King´s Choice'),
  ),
]

if __name__ == '__main__':
    import html_fix
    for p in produkte:
        for feld in ('short', 'lang'):
            a = p[feld]
            assert a.strip() == html_fix.aufraeumen(a).strip(), (p['sku'], feld)
        t, md, kw = p['yoast']
        print(p['sku'], 'Titel', len(t), '| Meta', len(md))
        assert len(t) <= 60 and len(md) <= 156

    for p in produkte:
        t, md, kw = p['yoast']
        neu = dict(GEMEINSAM, sku=p['sku'], name=p['name'], tags=p['tags'],
                   attributes=p['attributes'],
                   short_description=p['short'], description=p['lang'],
                   meta_data=[{'key': '_yoast_wpseo_title', 'value': t},
                              {'key': '_yoast_wpseo_metadesc', 'value': md},
                              {'key': '_yoast_wpseo_focuskw', 'value': kw}])
        r = hj.ruf('products', neu, 'POST')
        q = hj.ruf(f'products/{r["id"]}', {'manufacturer': {'id': 8030},
                                           'delivery_time': {'id': 4236}}, 'PUT')
        print('angelegt:', r['id'], r['name'], '|', (q.get('manufacturer') or {}).get('name'),
              '|', (q.get('delivery_time') or {}).get('name'))
