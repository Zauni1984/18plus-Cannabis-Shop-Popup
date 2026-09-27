# -*- coding: utf-8 -*-
"""PALACIO Soothe Beauty Set (KON009) auf hanfjack.de anlegen.

Quelle: Palacio-Artikel 777 aus cml.palacio.cz und das Produktbild, auf dem
die drei Tuben lesbar sind - Cannacool, Konopny Gel Forte, Cannahot, je
200 ml, alle "with bio hemp oil".

Das Set selbst fuehrt kein ingredients-Feld. Die drei Komponenten sind aber
eindeutig und stehen im Shop: PAL1198 = 509 Cannacool, PAL1122 = 510 Hanf
Massagegel Bio Oel Forte, PAL1197 = 508 Cannahot. Ihre Listen werden hier
zusammengesetzt - dasselbe Verfahren wie bei Sportpack, Cannapack und
Flexpack.
"""
import json, hj, pal_inci, html_fix

S = '/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
TEILE = [('Cannacool, kühlend', 509), ('Konopný Gel Forte', 510),
         ('Cannahot, wärmend', 508)]

match = json.load(open(S + 'pal_match.json'))
ing = {int(i): v['kat']['ingredients'] for i, v in match.items()}

KURZ = ('<p><strong>Drei Massagegele in der Geschenkbox:</strong> Cannacool '
        'kühlend, Konopný Gel Forte und Cannahot wärmend, je 200 ml – '
        'alle mit Bio-Hanföl.</p>')

LANG = (
 '<p>Das <strong>PALACIO Soothe Beauty Set</strong> legt die drei '
 'Massagegel-Varianten nebeneinander in eine Geschenkbox: das kühlende '
 '<b>Cannacool</b>, das kräftigere <b>Konopný Gel Forte</b> und das '
 'wärmende <b>Cannahot</b>, jeweils in der 200-ml-Tube.</p>\n'
 '<p>Kühlend und wärmend kommen üblicherweise zu unterschiedlichen '
 'Zeitpunkten zum Einsatz – das Set deckt beide Seiten ab und legt die '
 'Forte-Variante für stärker beanspruchte Partien dazu. Alle drei Tuben '
 'tragen den Aufdruck „with bio hemp oil“.</p>\n'
 '<h3 style="margin-top:1.8em">Auf einen Blick:</h3>\n'
 '<ul>\n'
 '<li><b>Enthalten:</b> Cannacool 200 ml, Konopný Gel Forte 200 ml, '
 'Cannahot 200 ml</li>\n'
 '<li><b>Gesamtmenge:</b> 600 ml</li>\n'
 '<li><b>Anwendung:</b> Massagegel, äußerlich zum Einmassieren</li>\n'
 '<li><b>Gebinde:</b> drei Tuben in der Geschenkbox</li>\n'
 '<li><b>Artikelnummer des Herstellers:</b> KON009</li>\n'
 '</ul>\n')

def inhaltsstoffe():
    zeilen = []
    for name, pid in TEILE:
        t = pal_inci.zerlegen(ing[pid])
        de, offen = pal_inci.deutsch(t)
        assert not offen, (name, offen)
        zeilen.append(f'<p><b>{name}:</b> ' + ', '.join(de) + '.</p>')
        zeilen.append('<p><b>INCI:</b> ' + ', '.join(t) + '.</p>')
    return '<h3 style="margin-top:1.8em">Inhaltsstoffe</h3>\n' + '\n'.join(zeilen)

HINWEIS = (
 '<h3 style="margin-top:1.8em">Hinweise</h3>\n'
 '<p>Palacio ist ein kosmetisches Mittel zur äußerlichen Anwendung. Vor der '
 'ersten Anwendung an einer kleinen Hautstelle testen, nicht auf verletzte '
 'oder gereizte Haut auftragen und den Kontakt mit den Augen vermeiden. Nach '
 'dem Auftragen die Hände waschen.</p>')

BESCHREIBUNG = LANG + inhaltsstoffe() + '\n' + HINWEIS

neu = {
    'name': 'Palacio Soothe Beauty Set - Cannacool, Forte und Cannahot',
    'type': 'simple', 'status': 'publish',
    'sku': 'HJ-2635225', 'gtin': '8595641303990', 'mpn': 'KON009',
    'regular_price': '21.01',            # 25,00 EUR brutto bei 19 %
    'tax_class': '', 'shipping_class': 'paket-standard',
    'weight': '0.73',
    'dimensions': {'length': '24.5', 'width': '23.5', 'height': '6.5'},
    'manage_stock': True, 'stock_quantity': 12, 'backorders': 'no',
    'catalog_visibility': 'visible',
    'short_description': KURZ, 'description': BESCHREIBUNG,
    'categories': [{'id': 5818}, {'id': 56}],
    'tags': [{'id': 181}, {'id': 403}, {'id': 3272}, {'id': 5795}, {'id': 7522}],
    'brands': [128],
    'meta_data': [
        {'key': '_yoast_wpseo_title',
         'value': 'Palacio Soothe Beauty Set – drei Massagegele, 600 ml'},
        {'key': '_yoast_wpseo_metadesc',
         'value': 'Palacio Soothe Beauty Set: Cannacool kühlend, Forte und '
                  'Cannahot wärmend, je 200 ml mit Bio-Hanföl. Jetzt bei Hanfjack.'},
        {'key': '_yoast_wpseo_focuskw', 'value': 'Palacio Soothe Beauty Set'},
    ],
}

if __name__ == '__main__':
    assert BESCHREIBUNG.strip() == html_fix.aufraeumen(BESCHREIBUNG).strip()
    for m in neu['meta_data']:
        print(m['key'], len(m['value']))
    assert len(neu['meta_data'][0]['value']) <= 60
    assert len(neu['meta_data'][1]['value']) <= 156

    p = hj.ruf('products', neu, 'POST')
    q = hj.ruf(f'products/{p["id"]}', {'manufacturer': {'id': 4693},
                                       'delivery_time': {'id': 4236}}, 'PUT')
    print('angelegt:', p['id'], p['name'], p['permalink'])
    print('Hersteller:', (q.get('manufacturer') or {}).get('name'),
          '| Lieferzeit:', (q.get('delivery_time') or {}).get('name'))
    json.dump({'id': p['id']}, open(S + 'kon009_neu.json', 'w'))
