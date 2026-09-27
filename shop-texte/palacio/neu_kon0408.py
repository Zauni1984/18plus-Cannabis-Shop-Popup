# -*- coding: utf-8 -*-
"""PALACIO Soothe Beauty Set KON004 und KON008 anlegen.

Die Sets selbst fuehren kein ingredients-Feld. Die Komponenten stehen aber im
Palacio-Katalog und sind auf den Kartonbildern lesbar:

KON004 - Vlasovy sampon 500 ml (PAL0355), Telove maslo 200 ml (PAL1192),
         Kremovy sprchovy gel 500 ml (PAL1224)
KON008 - Pletovy krem 50 ml (PAL0441), Nocni pletovy krem 50 ml (PAL1229),
         Cistici pena na oblicej 150 ml (PAL1209)

Zum Shampoo fuehrt der Katalog vier 500-ml-Varianten. Ihre INCI-Listen sind
wortgleich, die Zuordnung ist damit ohne Risiko.
"""
import json, hj, pal_inci, html_fix

S = '/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
kat = {(x.get('catalog_id') or ''): x for x in json.load(open(S + 'pal_katalog_voll.json'))}

def inhaltsstoffe(teile):
    zeilen = []
    for name, cid in teile:
        t = pal_inci.zerlegen(kat[cid]['ingredients'])
        de, offen = pal_inci.deutsch(t)
        assert not offen, (name, offen)
        zeilen.append(f'<p><b>{name}:</b> ' + ', '.join(de) + '.</p>')
        zeilen.append('<p><b>INCI:</b> ' + ', '.join(t) + '.</p>')
    return '<h3 style="margin-top:1.8em">Inhaltsstoffe</h3>\n' + '\n'.join(zeilen)

HINWEIS = (
 '<h3 style="margin-top:1.8em">Hinweise</h3>\n'
 '<p>Palacio ist ein kosmetisches Mittel zur äußerlichen Anwendung. Vor der '
 'ersten Anwendung an einer kleinen Hautstelle testen, nicht auf verletzte '
 'oder gereizte Haut auftragen und den Kontakt mit den Augen vermeiden.</p>')

produkte = [
 dict(sku='HJ-9768207', gtin='8595641304409', mpn='KON004', pal=772,
   name='Palacio Soothe Beauty Set - Körper und Haar mit Hanföl',
   weight='1.61', dim={'length': '24.5', 'width': '23.5', 'height': '6.5'},
   stock=12,
   teile=[('Hanf-Haarshampoo 500 ml', 'PAL0355'),
          ('Hanf-Körperbutter 200 ml', 'PAL1192'),
          ('Cremeduschgel mit Hanföl 500 ml', 'PAL1224')],
   kurz=('<p><strong>Waschen und pflegen in einer Box:</strong> Hanf-Haarshampoo '
         '500 ml, Cremeduschgel 500 ml und Hanf-Körperbutter 200 ml – '
         'zusammen 1,2 Liter Hanfpflege.</p>'),
   lang=(
    '<p>Das <strong>PALACIO Soothe Beauty Set</strong> für Körper und Haar '
    'stellt eine einfache Routine zusammen: waschen, duschen, eincremen. Das '
    '<b>Hanf-Haarshampoo</b> (500 ml) ist für alle Haartypen ausgelobt, das '
    '<b>Cremeduschgel mit Hanföl</b> (500 ml) reinigt die Haut, und die '
    '<b>Hanf-Körperbutter</b> (200 ml) kommt danach als reichhaltige '
    'Pflege für Gesicht und Körper.</p>\n'
    '<p>Alle drei tragen Hanfsamenöl im Namen und in der Rezeptur. Zusammen '
    'sind das 1,2 Liter – das Set ist eher Grundausstattung fürs Bad als '
    'Probierpaket.</p>\n'
    '<h3 style="margin-top:1.8em">Auf einen Blick:</h3>\n'
    '<ul>\n'
    '<li><b>Enthalten:</b> Hanf-Haarshampoo 500 ml, Cremeduschgel 500 ml, '
    'Hanf-Körperbutter 200 ml</li>\n'
    '<li><b>Gesamtmenge:</b> 1,2 Liter</li>\n'
    '<li><b>Anwendung:</b> Haarwäsche, Dusche und Körperpflege</li>\n'
    '<li><b>Gebinde:</b> drei Artikel in der Geschenkbox</li>\n'
    '<li><b>Artikelnummer des Herstellers:</b> KON004</li>\n'
    '</ul>\n'),
   yoast=('Palacio Soothe Beauty Set – Körper und Haar, 1,2 l',
          'Palacio Soothe Beauty Set: Hanf-Haarshampoo und Cremeduschgel je 500 ml '
          'plus Körperbutter 200 ml. Jetzt bei Hanfjack.',
          'Palacio Soothe Beauty Set Körper und Haar')),
 dict(sku='HJ-2876138', gtin='8595641304485', mpn='KON008', pal=776,
   name='Palacio Soothe Beauty Set - Gesichtspflege Tag, Nacht und Reinigung',
   weight='0.65', dim={'length': '22.5', 'width': '14', 'height': '7.5'},
   stock=10,
   teile=[('Hanf-Gesichtscreme 50 ml', 'PAL0441'),
          ('Hanf-Nachtcreme 50 ml', 'PAL1229'),
          ('Gesichtsreinigungsschaum 150 ml', 'PAL1209')],
   kurz=('<p><strong>Drei Schritte für das Gesicht:</strong> Reinigungsschaum '
         '150 ml, Tagescreme 50 ml und Nachtcreme 50 ml – alle drei mit '
         'Hanföl.</p>'),
   lang=(
    '<p>Das <strong>PALACIO Soothe Beauty Set</strong> für das Gesicht deckt '
    'die drei üblichen Schritte ab: Der <b>Reinigungsschaum</b> (150 ml) nimmt '
    'Schmutz und überschüssigen Talg auf, die <b>Gesichtscreme</b> (50 ml) ist '
    'als 24-Stunden-Pflege ausgelobt, und die <b>Nachtcreme</b> (50 ml) ist auf '
    'die Regeneration über Nacht ausgerichtet.</p>\n'
    '<p>Alle drei arbeiten mit Hanfsamenöl. Als Set spart man sich die Frage, '
    'welche Produkte zueinander passen – Palacio empfiehlt es ausdrücklich '
    'als Einsteiger-Set.</p>\n'
    '<h3 style="margin-top:1.8em">Auf einen Blick:</h3>\n'
    '<ul>\n'
    '<li><b>Enthalten:</b> Gesichtsreinigungsschaum 150 ml, Gesichtscreme 50 ml, '
    'Nachtcreme 50 ml</li>\n'
    '<li><b>Gesamtmenge:</b> 250 ml</li>\n'
    '<li><b>Anwendung:</b> Gesichtsreinigung sowie Tages- und Nachtpflege</li>\n'
    '<li><b>Gebinde:</b> drei Artikel in der Geschenkbox</li>\n'
    '<li><b>Artikelnummer des Herstellers:</b> KON008</li>\n'
    '</ul>\n'),
   yoast=('Palacio Soothe Beauty Set – Gesichtspflege, 3-teilig',
          'Palacio Soothe Beauty Set: Reinigungsschaum 150 ml, Tagescreme und '
          'Nachtcreme je 50 ml mit Hanföl. Jetzt bei Hanfjack.',
          'Palacio Soothe Beauty Set Gesichtspflege')),
]

def bauen(p):
    return p['lang'] + inhaltsstoffe(p['teile']) + '\n' + HINWEIS

if __name__ == '__main__':
    for p in produkte:
        d = bauen(p)
        assert d.strip() == html_fix.aufraeumen(d).strip(), p['mpn']
        t, md, kw = p['yoast']
        print(p['mpn'], 'Titel', len(t), '| Meta', len(md))
        assert len(t) <= 60 and len(md) <= 156

    for p in produkte:
        t, md, kw = p['yoast']
        neu = dict(
            name=p['name'], type='simple', status='publish', sku=p['sku'],
            gtin=p['gtin'], mpn=p['mpn'],
            regular_price='25.21',          # 30,00 EUR brutto bei 19 %
            tax_class='', shipping_class='paket-standard',
            weight=p['weight'], dimensions=p['dim'],
            manage_stock=True, stock_quantity=p['stock'], backorders='no',
            catalog_visibility='visible',
            short_description=p['kurz'], description=bauen(p),
            categories=[{'id': 5818}, {'id': 56}], brands=[128],
            tags=[{'id': 181}, {'id': 403}, {'id': 5795}, {'id': 7522}],
            meta_data=[{'key': '_yoast_wpseo_title', 'value': t},
                       {'key': '_yoast_wpseo_metadesc', 'value': md},
                       {'key': '_yoast_wpseo_focuskw', 'value': kw}])
        r = hj.ruf('products', neu, 'POST')
        hj.ruf(f'products/{r["id"]}', {'manufacturer': {'id': 4693},
                                       'delivery_time': {'id': 4236}}, 'PUT')
        print('angelegt:', r['id'], p['mpn'], r['name'], r['permalink'])
