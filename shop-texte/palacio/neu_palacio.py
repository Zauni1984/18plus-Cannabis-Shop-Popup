# -*- coding: utf-8 -*-
"""Drei PALACIO-Zahnpasten aus palacio.cz/product/731..733 anlegen.

Die Seite ist eine SPA; die Daten kommen aus der dahinterliegenden API
(cml.palacio.cz/api/products/<id>?expand=1) und liefern Titel, Beschreibung,
EAN, Gewicht, Masse, Bilder und - fuer diese Aufgabe entscheidend - das Feld
"ingredients" mit der vollstaendigen INCI-Liste.

Die deutschen Texte sind Uebersetzungen des tschechischen Herstellertextes.
Wertende Herstellerangaben stehen als solche im Text. Die Behauptung, Bambus-
Aktivkohle remineralisiere den Zahnschmelz, ist nicht uebernommen.

Preis: kein VK angesagt. 5,04 EUR netto = 6,00 EUR brutto stammt vom
Schwesterprodukt 20190 (Palacio HEMP & DENT, ebenfalls 75 g Zahnpasta).
Deshalb Entwurf.
"""
import json, hj, pal_inci

S = '/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
GEMEINSAM = dict(
    type='simple', status='draft', regular_price='5.04', tax_class='',
    shipping_class='paket-standard', weight='0.095',
    dimensions={'length': '3.5', 'width': '3.5', 'height': '13.2'},
    manage_stock=True, stock_quantity=5, backorders='no',
    catalog_visibility='visible', categories=[{'id': 56}], brands=[128],
)

ANWENDUNG = (
 '<h3 style="margin-top:1.8em">Anwendung</h3>\n'
 '<p>Eine kleine Menge auf die Zahnbürste geben und mindestens zwei Minuten '
 'putzen, mindestens zweimal täglich. Bei Kindern darauf achten, dass die '
 'Paste nicht verschluckt und der Mund gründlich ausgespült wird.</p>\n'
 '<h3 style="margin-top:1.8em">JUST CLICK – Airless-Spender statt Tube</h3>\n'
 '<p>Kein Quetschen, kein verlorener Deckel: Die Paste sitzt in einem '
 'Airless-Spender, ein Klick gibt die Portion frei.</p>')

produkte = [
 dict(pal=731, sku='HJ-6720902', mpn='PAL1432', gtin='8595641303839',
   name='PALACIO Zahnpasta Refreshing mit Hanf- und Orangenöl 75 g',
   tags=[{'id': 181}, {'id': 197}, {'id': 7522}],
   short=('<p><strong>Frische aus Orangenöl und Menthol:</strong> Zahnpasta mit '
          'Hanföl sowie Kamillen- und Ringelblumenextrakt, ohne Fluorid, im '
          'Airless-Spender JUST CLICK.</p>'),
   kopf=(
    '<p>Die <strong>PALACIO Refreshing</strong> ist eine Zahnpasta mit Hanföl, '
    'Orangenöl und Menthol. Die Auszüge aus Kamille und Ringelblume sind auf '
    'gereiztes Zahnfleisch und ein empfindliches Mundmilieu ausgerichtet.</p>\n'
    '<p>Palacio gibt 97 % Bestandteile natürlichen Ursprungs an. Die Paste kommt '
    'ohne Fluorid und ohne synthetische Farbstoffe aus und ist als niedrig '
    'abrasiv sowie für Kinder ab sechs Jahren geeignet ausgewiesen.</p>'),
   punkte=[('Anwendung', 'Zahnpasta'), ('Inhalt', '75 g'),
           ('Gebinde', 'Airless-Spender JUST CLICK'),
           ('Geschmack', 'Orange und Minze'),
           ('Ohne', 'Fluorid, synthetische Farbstoffe'),
           ('Ausgelobt', '97 % natürlichen Ursprungs, vegan, niedrig abrasiv'),
           ('Geeignet', 'auch für Kinder ab 6 Jahren'),
           ('Artikelnummer des Herstellers', 'PAL1432')],
   yoast=('PALACIO Zahnpasta Refreshing mit Hanföl, 75 g',
          'PALACIO Refreshing: Zahnpasta mit Hanföl, Orangenöl und Menthol, '
          'ohne Fluorid, im Airless-Spender. Jetzt bei Hanfjack.',
          'PALACIO Zahnpasta Refreshing')),
 dict(pal=732, sku='HJ-7854149', mpn='PAL1433', gtin='8595641303846',
   name='PALACIO Zahnpasta Sensitive mit Hanföl und Hydroxylapatit 75 g',
   tags=[{'id': 181}, {'id': 197}, {'id': 7522}],
   short=('<p><strong>Biokompatibel mit dem Zahnschmelz:</strong> Zahnpasta mit '
          'Hydroxylapatit, Calcium und Hanföl sowie einem Komplex aus sieben '
          'Kräutern, ohne Fluorid, im Airless-Spender JUST CLICK.</p>'),
   kopf=(
    '<p>Die <strong>PALACIO Sensitive</strong> setzt auf Omyadent® 100-OG, eine '
    'Kombination aus Hydroxylapatit und Calcium. Palacio gibt an, dass sie den '
    'Zahnschmelz remineralisiert und stärkt, die Empfindlichkeit der Zähne '
    'senkt und die natürliche Aufhellung unterstützt.</p>\n'
    '<p>Dazu kommen Hanföl für Mundschleimhaut und Zahnfleisch und der '
    'hauseigene 7-Herbs-Komplex aus Kamille, Ringelblume, Salbei und weiteren '
    'Kräutern. Angegeben sind über 97 % Bestandteile natürlichen Ursprungs, '
    'kein Fluorid und eine niedrige Abrasivität.</p>'),
   punkte=[('Anwendung', 'Zahnpasta'), ('Inhalt', '75 g'),
           ('Gebinde', 'Airless-Spender JUST CLICK'),
           ('Besonderheit', 'Omyadent® 100-OG: Hydroxylapatit und Calcium'),
           ('Geschmack', 'Minze'), ('Ohne', 'Fluorid'),
           ('Ausgelobt', 'über 97 % natürlichen Ursprungs, vegan, niedrig abrasiv'),
           ('Geeignet', 'auch für Kinder ab 6 Jahren'),
           ('Artikelnummer des Herstellers', 'PAL1433')],
   yoast=('PALACIO Zahnpasta Sensitive mit Hydroxylapatit, 75 g',
          'PALACIO Sensitive: Zahnpasta mit Hydroxylapatit, Calcium und Hanföl '
          'für empfindliche Zähne, ohne Fluorid. Jetzt bei Hanfjack.',
          'PALACIO Zahnpasta Sensitive')),
 dict(pal=733, sku='HJ-4575145', mpn='PAL1431', gtin='8595641303853',
   name='PALACIO Zahnpasta Whitening mit Hanföl und Aktivkohle 75 g',
   tags=[{'id': 181}, {'id': 197}, {'id': 15412}, {'id': 7522}],
   short=('<p><strong>Gegen Verfärbungen von Kaffee, Wein und Tabak:</strong> '
          'Zahnpasta mit Bambus-Aktivkohle, Zink, Aloe vera und Hanföl, ohne '
          'Fluorid, im Airless-Spender JUST CLICK.</p>'),
   kopf=(
    '<p>Die <strong>PALACIO Whitening</strong> arbeitet mit Aktivkohle aus '
    'Bambus, die Verfärbungen von der Zahnoberfläche lösen soll – Palacio '
    'nennt ausdrücklich Wein, Kaffee und Tabak. Zink ist gegen Zahnbelag und '
    'Mundgeruch gesetzt.</p>\n'
    '<p>Aloe vera ist auf das Zahnfleisch ausgerichtet, Nelkenöl wirkt '
    'antiseptisch, Minzöl sorgt für Frische, und Hanföl soll die '
    'Mundschleimhaut schützen. Angegeben sind über 97 % Bestandteile '
    'natürlichen Ursprungs, kein Fluorid und keine zugesetzten Farbstoffe.</p>'),
   punkte=[('Anwendung', 'Zahnpasta'), ('Inhalt', '75 g'),
           ('Gebinde', 'Airless-Spender JUST CLICK'),
           ('Besonderheit', 'Aktivkohle aus Bambus, Zink, Aloe vera'),
           ('Geschmack', 'Minze, Aloe vera und Nelke'),
           ('Ohne', 'Fluorid, zugesetzte Farbstoffe'),
           ('Ausgelobt', 'über 97 % natürlichen Ursprungs, vegan'),
           ('Geeignet', 'auch für Kinder ab 6 Jahren'),
           ('Artikelnummer des Herstellers', 'PAL1431')],
   yoast=('PALACIO Zahnpasta Whitening mit Aktivkohle, 75 g',
          'PALACIO Whitening: Zahnpasta mit Bambus-Aktivkohle, Zink und Hanföl '
          'gegen Verfärbungen, ohne Fluorid. Jetzt bei Hanfjack.',
          'PALACIO Zahnpasta Whitening')),
]

def bauen(p):
    roh = json.load(open(S + f'palapi_{p["pal"]}.json'))['ingredients']
    inci, offen = pal_inci.block(roh)
    assert not offen, (p['sku'], offen)
    liste = '\n'.join(f'<li><b>{k}:</b> {v}</li>' for k, v in p['punkte'])
    lang = (p['kopf'] + '\n'
            '<h3 style="margin-top:1.8em">Auf einen Blick:</h3>\n'
            f'<ul>\n{liste}\n</ul>\n' + inci + '\n' + ANWENDUNG)
    return lang

if __name__ == '__main__':
    import html_fix
    for p in produkte:
        lang = bauen(p)
        assert lang.strip() == html_fix.aufraeumen(lang).strip(), p['sku']
        t, md, kw = p['yoast']
        print(p['sku'], 'Titel', len(t), '| Meta', len(md))
        assert len(t) <= 60 and len(md) <= 156

    for p in produkte:
        t, md, kw = p['yoast']
        neu = dict(GEMEINSAM, sku=p['sku'], name=p['name'], mpn=p['mpn'],
                   gtin=p['gtin'], tags=p['tags'],
                   short_description=p['short'], description=bauen(p),
                   attributes=[{'id': 2, 'visible': True, 'variation': False,
                                'options': ['75g']}],
                   meta_data=[{'key': '_yoast_wpseo_title', 'value': t},
                              {'key': '_yoast_wpseo_metadesc', 'value': md},
                              {'key': '_yoast_wpseo_focuskw', 'value': kw}])
        r = hj.ruf('products', neu, 'POST')
        q = hj.ruf(f'products/{r["id"]}', {'manufacturer': {'id': 4693},
                                           'delivery_time': {'id': 4236}}, 'PUT')
        print('angelegt:', r['id'], r['name'], '|', (q.get('manufacturer') or {}).get('name'),
              '|', (q.get('delivery_time') or {}).get('name'))
