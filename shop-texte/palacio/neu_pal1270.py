# -*- coding: utf-8 -*-
"""PAL1270 anlegen: PALACIO Hanf-Massagegel mit Panthenol, 200 ml.

Quelle der Produktdaten: cml.palacio.cz/api/products/396?expand=1
Die Inhaltsstoffe kommen vollstaendig und auf Deutsch aus pal_inci.py,
darunter zusaetzlich die INCI-Liste im Original.
"""
import base64, json, random, re, sys, time, urllib.request

S = '/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
sys.path.insert(0, S)
sys.path.insert(0, '/home/user/18plus-Cannabis-Shop-Popup/shop-texte/palacio')
import hj
import pal_inci

QUELLE = 'https://cml.palacio.cz/api/products/396?expand=1'

t = open(S + '.wp_creds').read()
WPU = re.search(r'WPUSER=(\S+)', t).group(1)
WPP = re.search(r"WPAPP=['\"]?([^'\"\n]+)", t).group(1)
AUTH = 'Basic ' + base64.b64encode(f'{WPU}:{WPP}'.encode()).decode()


def hol(url, kopf=None):
    # Die Palacio-API verlangt einen Accept-Kopf, sonst 415.
    standard = {'User-Agent': 'Mozilla/5.0', 'Accept': 'application/json, image/*, */*'}
    r = urllib.request.Request(url, headers=kopf or standard)
    with urllib.request.urlopen(r, timeout=240) as f:
        return f.read(), f.headers.get('Content-Type', 'image/jpeg').split(';')[0]


def bild_hoch(roh, ct, name):
    for v in range(5):
        try:
            r = urllib.request.Request(
                'https://hanfjack.de/wp-json/wp/v2/media', data=roh, method='POST',
                headers={'Authorization': AUTH, 'Content-Type': ct,
                         'Content-Disposition': f'attachment; filename="{name}"',
                         'User-Agent': 'hj/1'})
            with urllib.request.urlopen(r, timeout=300) as f:
                return json.load(f)
        except Exception as e:
            if v == 4:
                raise
            print(f'   Wiederholung {v+1} fuer {name}: {str(e)[:70]}')
            time.sleep(2 ** v * 3)


def text(quelle):
    """Beschreibung im Stil der uebrigen Palacio-Produkte."""
    inci = pal_inci.block(quelle['ingredients'])
    if isinstance(inci, tuple):
        inci = inci[0]
    return (
        '<p>Das <strong>PALACIO Hanf-Massagegel mit Panthenol</strong> ist ein '
        'Massagegel mit Hanfsamenöl-Estern und Panthenol. Menthol, Campher und '
        'Eukalyptusöl sorgen für den kühlen Auftritt, wie man ihn aus der '
        'Sportpflege kennt.</p>\n'
        '<p>Dazu kommen achtzehn Pflanzenauszüge, darunter Arnika, Ringelblume, '
        'Rosskastanie, Schafgarbe, Ackerschachtelhalm, Holunderblüte und '
        'Lavendel. Palacio beschreibt das Gel als revitalisierend, erfrischend '
        'und feuchtigkeitsspendend; es ziehe leicht ein.</p>\n'
        '<h3 style="margin-top: 1.8em;">Auf einen Blick:</h3>\n<ul>\n'
        '<li><b>Anwendung:</b> Massagegel</li>\n'
        '<li><b>Inhalt:</b> 200 ml</li>\n'
        '<li><b>Gebinde:</b> Tube</li>\n'
        '<li><b>Kennzeichnende Bestandteile:</b> Hanfsamenöl-Glycereth-8-Ester, '
        'Panthenol, Menthol, Campher, Eukalyptusblattöl</li>\n'
        '<li><b>Pflanzenauszüge:</b> achtzehn, unter anderem Arnika, '
        'Ringelblume und Rosskastanie</li>\n'
        '<li><b>Artikelnummer des Herstellers:</b> PAL1270</li>\n'
        '</ul>\n'
        + inci + '\n'
        '<h3 style="margin-top: 1.8em;">Anwendung</h3>\n'
        '<p>Eine kleine Menge auf die gewünschte Stelle geben und mit kreisenden '
        'Bewegungen einmassieren, bis das Gel eingezogen ist. Danach die Hände '
        'waschen. Nicht auf verletzte oder gereizte Haut auftragen und den '
        'Kontakt mit den Augen vermeiden.</p>'
    )


def anlegen():
    quelle = json.loads(hol(QUELLE)[0].decode())
    assert quelle['catalog_id'] == 'PAL1270', quelle['catalog_id']
    print(f"Quelle: {quelle['title']} | EAN {quelle['ean']} | "
          f"{quelle['volume']} l | {quelle['weight_brutto']} kg")

    daten = {
        'name': 'PALACIO Hanf-Massagegel mit Panthenol 200 ml',
        'type': 'simple',
        'status': 'publish',
        'catalog_visibility': 'visible',
        'sku': 'HJ-' + str(random.randint(1000000, 9999999)),
        'regular_price': '6.30',
        'description': text(quelle),
        'short_description': (
            '<p><strong>Kühlende Frische mit Panthenol:</strong> Hanf-Massagegel '
            'mit Menthol, Campher und Eukalyptusöl sowie achtzehn '
            'Pflanzenauszügen, in der 200-ml-Tube.</p>'),
        'manage_stock': True,
        'stock_quantity': 25,
        'stock_status': 'instock',
        'backorders': 'no',
        'weight': '0.220',
        'dimensions': {'length': '8', 'width': '5', 'height': '18'},
        'shipping_class': 'paket-standard',
        'categories': [{'id': 56}],
        'brands': [128],
        'tags': [{'id': 181}, {'id': 7522}, {'id': 967}],
        'attributes': [{'id': 2, 'name': 'Inhalt', 'position': 0, 'visible': True,
                        'variation': False, 'options': ['200 ml']}],
        'upsell_ids': [509, 508, 1879, 19954],
        'cross_sell_ids': [20192, 504],
        'unit': {'id': 26},
        'unit_price': {'base': '1', 'product': '0.2', 'price_auto': True},
        'meta_data': [
            {'key': '_ts_gtin', 'value': str(quelle['ean'])},
            {'key': '_ts_mpn', 'value': 'PAL1270'},
            {'key': '_yoast_wpseo_title',
             'value': 'PALACIO Hanf-Massagegel mit Panthenol, 200 ml'},
            {'key': '_yoast_wpseo_metadesc',
             'value': 'PALACIO Massagegel mit Hanf, Panthenol, Menthol und '
                      'achtzehn Pflanzenauszügen, 200 ml Tube. Jetzt bei '
                      'Hanfjack bestellen.'},
            {'key': '_yoast_wpseo_focuskw',
             'value': 'PALACIO Hanf-Massagegel mit Panthenol'},
        ],
    }
    neu = hj.ruf('products', daten, 'POST')
    pid = neu['id']
    print(f"angelegt: {pid} | {neu['name']} | SKU {neu['sku']} | {neu['price']} EUR")

    # Lieferzeit und Hersteller brauchen einen eigenen Aufruf
    nach = hj.ruf(f'products/{pid}', {'delivery_time': {'id': 4236},
                                      'manufacturer': {'id': 4693}}, 'PUT')
    lz = (nach.get('delivery_time') or {}).get('name')
    hs = (nach.get('manufacturer') or {}).get('name')
    print(f'   Lieferzeit {lz} | Hersteller {hs}')

    # Bilder vom Hersteller holen und anhaengen
    bilder = [f['link'] for f in quelle.get('files') or [] if f.get('type') == 'image']
    ids = []
    for n, link in enumerate(bilder, 1):
        roh, ct = hol(link)
        endung = {'image/png': 'png', 'image/webp': 'webp'}.get(ct, 'jpg')
        m = bild_hoch(roh, ct, f'palacio-hanf-massagegel-panthenol-200ml-{n}.{endung}')
        ids.append(m['id'])
        print(f'   Bild {n}: Medium {m["id"]} ({len(roh)//1024} KB)')
    if ids:
        q = hj.ruf(f'products/{pid}', {'images': [{'id': i} for i in ids]}, 'PUT')
        print(f'   {len(q["images"])} Bilder zugeordnet')

    fertig = hj.ruf(f'products/{pid}')
    return fertig


if __name__ == '__main__':
    p = anlegen()
    up = p.get('unit_price') or {}
    print(f"\nfertig: {p['permalink']}")
    print(f"  Grundpreis {up.get('price')} EUR je {(p.get('unit') or {}).get('name')}"
          f" | Bestand {p.get('stock_quantity')} | Status {p['status']}")
