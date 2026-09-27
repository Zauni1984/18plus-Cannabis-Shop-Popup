# -*- coding: utf-8 -*-
"""Zwei Black-Leaf-Pollenpressen anlegen (Groesse S und L).

Quelle: neardark.de/BL-Pollenpresse-S-Size/500203-46 und .../L-Size/500205-45
Bei Near Dark haengen an dem Artikel mehrere Varianten; gelistet wird je
Produkt nur die eine Variante von der jeweiligen Seite.

Gewicht bleibt leer - die Quelle nennt keines, und geraten wird hier nichts.
"""
import base64, json, random, re, sys, time, urllib.request
from decimal import Decimal, ROUND_HALF_UP

S = '/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
sys.path.insert(0, S)
import hjapi

t = open(S + '.wp_creds').read()
WPU = re.search(r'WPUSER=(\S+)', t).group(1)
WPP = re.search(r"WPAPP=['\"]?([^'\"\n]+)", t).group(1)
AUTH = 'Basic ' + base64.b64encode(f'{WPU}:{WPP}'.encode()).decode()

MARKE_BLACK_LEAF = 7321
HERSTELLER_NEAR_DARK = 7425
LIEFERZEIT_1_3 = 4236
KAT_VEREDELN = 16451          # Growshop > Veredeln & Extraktion
ATTR_MATERIAL, ATTR_FARBE = 58, 3
TAGS = [7522, 9464, 5277]     # Beilngries, Pollengewinnung, Aluminium

ABLAUF = (
    '<p>So läuft es ab: einen Bolzen herausnehmen, das Material einfüllen, den '
    'Bolzen wieder einsetzen und den Deckel aufschrauben. Dann werden die beiden '
    'Schraubdeckel abwechselnd gegeneinander zugedreht – der Druck presst das '
    'Material zu einem Taler.</p>\n')

def netto(brutto):
    """Der Shop speichert netto; die Vorgabe kommt brutto (19 %)."""
    return str((Decimal(brutto) / Decimal('1.19')).quantize(Decimal('0.01'),
                                                            ROUND_HALF_UP))


PRESSEN = [
    dict(groesse='S', lfd='500203-46', gtin='4251403331994', preis=None,
         brutto='7.50',
         durchmesser='21', hoehe='60', farbe='Aubergine',   # 7,50 brutto
         bild='https://blackleaf.pim.live.onacylabs.de/Produkte/28351/'
              'image-thumb__28351__Shopware/500203-46.91b016e4.jpg',
         zusatz='Mit 21 mm Durchmesser ist sie die kleine Variante für einzelne '
                'Portionen.'),
    dict(groesse='L', lfd='500205-45', gtin='4251403331291', preis=None,
         brutto='8.50',
         durchmesser='35', hoehe='68', farbe='Amber',       # 8,50 brutto
         bild='https://blackleaf.pim.live.onacylabs.de/Produkte/20813/'
              'image-thumb__20813__Shopware/500205-45.37fc38c8.png',
         zusatz='Mit 35 mm Durchmesser fasst sie deutlich mehr als die kleine '
                'Ausführung.'),
]


def hol(url):
    r = urllib.request.Request(url, headers={
        'User-Agent': 'Mozilla/5.0', 'Accept': 'image/*, */*'})
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
            time.sleep(2 ** v * 4)


def beschreibung(p):
    return (
        f'<p>Die <strong>Black Leaf Pollenpresse in Größe {p["groesse"]}</strong> '
        'ist aus stabilem Aluminium gefertigt und hat auf jeder Seite einen Bolzen '
        f'und einen Schraubdeckel. {p["zusatz"]}</p>\n'
        + ABLAUF +
        '<h3 style="margin-top:1.8em">Auf einen Blick:</h3>\n<ul>\n'
        '<li><b>Material:</b> Aluminium</li>\n'
        f'<li><b>Durchmesser:</b> {p["durchmesser"]} mm</li>\n'
        f'<li><b>Höhe:</b> {p["hoehe"]} mm</li>\n'
        f'<li><b>Farbe:</b> {p["farbe"]}</li>\n'
        '<li><b>Motiv:</b> Black Leaf Logo</li>\n'
        '<li><b>Bauweise:</b> zwei Bolzen, Schraubdeckel auf beiden Seiten</li>\n'
        f'<li><b>Artikelnummer des Herstellers:</b> {p["lfd"]}</li>\n'
        '</ul>')


def kurz(p):
    return (f'<p>Handliche Pollenpresse aus Aluminium mit zwei Bolzen und '
            f'Schraubdeckeln auf beiden Seiten – presst das Material zum Taler, '
            f'Ø {p["durchmesser"]} mm.</p>')


def anlegen(p):
    name = (f'Black Leaf Pollenpresse {p["groesse"]} '
            f'Ø {p["durchmesser"]} mm Aluminium')
    daten = {
        'name': name,
        'type': 'simple',
        'status': 'publish',
        'catalog_visibility': 'visible',
        'sku': 'HJ-' + str(random.randint(1000000, 9999999)),
        'regular_price': netto(p['brutto']),
        'description': beschreibung(p),
        'short_description': kurz(p),
        'manage_stock': True,
        'stock_quantity': 4,
        'stock_status': 'instock',
        'backorders': 'no',
        'dimensions': {'length': str(round(int(p['durchmesser']) / 10, 1)),
                       'width': str(round(int(p['durchmesser']) / 10, 1)),
                       'height': str(round(int(p['hoehe']) / 10, 1))},
        'shipping_class': 'paket-standard',
        'categories': [{'id': KAT_VEREDELN}],
        'brands': [MARKE_BLACK_LEAF],
        'tags': [{'id': i} for i in TAGS],
        'attributes': [
            {'id': ATTR_MATERIAL, 'name': 'Material', 'position': 0,
             'visible': True, 'variation': False, 'options': ['Aluminium']},
            {'id': ATTR_FARBE, 'name': 'Farbe', 'position': 1,
             'visible': True, 'variation': False, 'options': [p['farbe']]},
        ],
        'meta_data': [
            {'key': '_ts_gtin', 'value': p['gtin']},
            {'key': '_ts_mpn', 'value': p['lfd']},
            {'key': '_yoast_wpseo_title',
             'value': f'Black Leaf Pollenpresse {p["groesse"]}, '
                      f'Ø {p["durchmesser"]} mm, Aluminium'},
            {'key': '_yoast_wpseo_metadesc',
             'value': f'Black Leaf Pollenpresse Größe {p["groesse"]}: Aluminium, '
                      f'Ø {p["durchmesser"]} mm, zwei Bolzen und Schraubdeckel. '
                      'Jetzt bei Hanfjack bestellen.'},
            {'key': '_yoast_wpseo_focuskw',
             'value': f'Black Leaf Pollenpresse {p["groesse"]}'},
        ],
    }
    neu = hjapi.ruf('products', daten, 'POST', pause=3)
    pid = neu['id']
    print(f"angelegt: {pid} | {neu['name']} | SKU {neu['sku']} | {neu['price']} EUR")

    nach = hjapi.ruf(f'products/{pid}', {'delivery_time': {'id': LIEFERZEIT_1_3},
                                         'manufacturer': {'id': HERSTELLER_NEAR_DARK}},
                     'PUT', pause=3)
    print(f"   Lieferzeit {(nach.get('delivery_time') or {}).get('name')} | "
          f"Hersteller {(nach.get('manufacturer') or {}).get('name')}")

    roh, ct = hol(p['bild'])
    endung = {'image/png': 'png', 'image/webp': 'webp'}.get(ct, 'jpg')
    m = bild_hoch(roh, ct, f'black-leaf-pollenpresse-{p["groesse"].lower()}.{endung}')
    q = hjapi.ruf(f'products/{pid}', {'images': [{'id': m['id']}]}, 'PUT', pause=3)
    print(f'   Bild: Medium {m["id"]} ({len(roh)//1024} KB), '
          f'{len(q["images"])} zugeordnet')
    return pid


if __name__ == '__main__':
    ids = {}
    for p in PRESSEN:
        ids[p['groesse']] = anlegen(p)
    # Verknuepfungen: die beiden Pressen untereinander, dazu die Pollengeraete
    POLLEN = [39959, 39960, 39958]        # Pollenshaker, XL, Heisenberg
    GRINDER = [526, 9045]                 # als Ergaenzung im Warenkorb
    for g, pid in ids.items():
        anderer = ids['L' if g == 'S' else 'S']
        q = hjapi.ruf(f'products/{pid}', {
            'upsell_ids': [anderer] + POLLEN,
            'cross_sell_ids': GRINDER}, 'PUT', pause=3)
        print(f"{pid} ({g}): Upsells {q['upsell_ids']} | Cross {q['cross_sell_ids']}")
    for g, pid in ids.items():
        d = hjapi.ruf(f'products/{pid}?_fields=id,name,permalink,price,stock_quantity,status', pause=2)
        print(f"\n{d['id']} {d['name']}\n   {d['permalink']}\n   "
              f"{d['price']} EUR | Bestand {d['stock_quantity']} | {d['status']}")
