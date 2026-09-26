# -*- coding: utf-8 -*-
"""Juicy Jay's: Boxtexte neu, 13 sortenreine Einzelprodukte anlegen.

Keine Varianten - die bestehenden Einzelsorten (9006 Blueberry, 9009 Very
Cherry, 9013 Watermelon KSS) bleiben eigenstaendige Produkte, die neuen
kommen als weitere Einzelprodukte dazu.

Blueberry aus der KSS-Liste ist EAN-gleich mit 9006. Statt eines zweiten
Artikels wandert der eine lagernde Beutel auf den Bestand von 9006.

Technische Angaben stammen aus den Boxtexten (32 Blaettchen je Heft, King
Size Slim 110 mm, Triple Dip, Slow Burning, Spanien) und aus 9006.
MPN bleibt leer: das Muster JJ-PP-KSS-<Sorte> stammt aus der eigenen Pflege,
nicht vom Hersteller.
"""
import json, base64, urllib.request

S='/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
CK=CS=None
for z in open(S+'.wc_creds'):
    if 'CK=' in z: CK=z.strip().split('CK=',1)[1]
    if 'CS=' in z: CS=z.strip().split('CS=',1)[1]
AUTH='Basic '+base64.b64encode(f'{CK}:{CS}'.encode()).decode()

def ruf(pfad, daten=None, methode='GET'):
    d = json.dumps(daten).encode() if daten is not None else None
    r = urllib.request.Request('https://hanfjack.de/wp-json/wc/v3/'+pfad, data=d, method=methode,
        headers={'Authorization':AUTH,'Content-Type':'application/json','User-Agent':'hj/1'})
    try:
        with urllib.request.urlopen(r, timeout=120) as f: return json.load(f)
    except urllib.error.HTTPError as e:
        print('  HTTP', e.code, e.read().decode()[:300]); raise

# --- Sorten -------------------------------------------------------------
# (Anzeigename, Aroma-Satz, EAN, Bestand)
ZWEIINEINS = [
    ('Grape',        'Traubenaroma – süß und weich, näher an Weingummi als an frischer Traube', '716165306771', 2),
    ('Strawberry',   'Erdbeeraroma – süß und deutlich fruchtig',                                '716165306726', 4),
    ('Watermelon',   'Wassermelonenaroma – frisch und wässrig-süß, eher leicht als schwer',     '716165306757', 4),
    ('Bubblegum',    'Kaugummiaroma – süß und verspielt, die Sorte für Nostalgiker',            '716165306764', 4),
    ('Blueberry',    'Blaubeeraroma – süß und deutlich fruchtig, ohne ins Künstliche zu kippen','716165306733', 1),
    ('Cotton Candy', 'Zuckerwattearoma – die süßeste Sorte der Reihe',                          '716165306740', 4),
]
NUR_PAPIER = [
    ('Green Apple',  'Aroma des grünen Apfels – säuerlich-frisch statt einfach süß',            '716165174622', 1),
    ('Strawberry',   'Erdbeeraroma – süß und deutlich fruchtig',                                '716165178408', 2),
    ('Pineapple',    'Ananasaroma – tropisch-süß mit einer säuerlichen Spitze',                 '716165200185', 3),
    ('Raspberry',    'Himbeeraroma – fruchtig mit einer herben Note',                           '716165172598', 1),
    ('Coconut',      'Kokosaroma – cremig-süß, die untypischste Sorte der Reihe',               '716165179092', 1),
    ('Mello Mango',  'Mangoaroma – tropisch und weich',                                         '716165172611', 3),
    ('Jamaican Rum', 'Rumaroma – würzig-süß, deutlich kräftiger als die Fruchtsorten',          '716165178774', 3),
]

def beschreibung(sorte, aroma, mit_tips):
    tips_satz = (' Im Heft stecken zusätzlich integrierte Tips, eine zweite Packung braucht es also nicht.'
                 if mit_tips else '')
    tips_li   = '\n<li><b>Tips:</b> im Heft enthalten</li>' if mit_tips else ''
    return f"""<p>Juicy Jay’s {sorte} ist ein aromatisiertes King-Size-Slim-Papier. Der Geschmack wird im Triple-Dip-Verfahren aufgebracht, bei dem das Papier dreifach durch die Aromalösung läuft – das ist der Grund, warum die Note bis zum Ende trägt und nicht nach zwei Zügen verschwindet.</p>
<p>{aroma}. Das Heft enthält 32 Blättchen im King-Size-Slim-Format mit 110 mm Länge.{tips_satz} Hergestellt wird in Spanien.</p>
<h3 style="margin-top:1.8em">Auf einen Blick:</h3>
<ul>
<li><b>Format:</b> King Size Slim, 110 mm Länge</li>
<li><b>Blätter je Heft:</b> 32</li>
<li><b>Aroma:</b> {sorte}</li>
<li><b>Aromaverfahren:</b> Triple Dip – dreifach aromatisiert</li>{tips_li}
<li><b>Brennverhalten:</b> langsam brennend</li>
<li><b>Herstellung:</b> Spanien</li>
<li><b>Tabak:</b> nicht enthalten</li>
</ul>
<h3 style="margin-top:1.8em">Hinweise</h3>
<p>Aromatisierte Papiere sollten im verschlossenen Heft und trocken gelagert werden. Liegt das Heft offen, verflüchtigt sich ein Teil der Aromastoffe – der Geschmack lässt dann schon vor dem Anzünden nach.</p>"""

def kurz(sorte, aroma, mit_tips):
    zusatz = ' mit integrierten Tips' if mit_tips else ''
    kern = aroma.split('–')[0].strip().replace('Aroma des grünen Apfels', 'Aroma vom grünen Apfel')
    return (f'<p>Aromatisiertes King-Size-Slim-Papier{zusatz} – 32 Blättchen, 110 mm, '
            f'dreifach aromatisiert im Triple-Dip-Verfahren. {kern}.</p>')

def bau(sorte, aroma, ean, bestand, mit_tips, sku, preis_netto):
    name = f"Juicy Jay´s {sorte} King Size Slim" + (' 2in1' if mit_tips else '')
    slug = ('juicy-jays-' + sorte.lower().replace(' ', '-') +
            '-king-size-slim' + ('-2in1' if mit_tips else ''))
    attr = [
        {'id': 65, 'name': 'Format',    'options': ['King Size Slim, 110 mm Länge'], 'visible': True, 'variation': False},
        {'id': 11, 'name': 'Geschmack', 'options': [sorte],                          'visible': True, 'variation': False},
        {'id': 64, 'name': 'Herkunft',  'options': ['Spanien'],                      'visible': True, 'variation': False},
    ]
    p = {
        'name': name, 'slug': slug, 'type': 'simple', 'status': 'publish',
        'catalog_visibility': 'visible', 'sku': sku, 'gtin': ean,
        'regular_price': preis_netto, 'tax_status': 'taxable', 'tax_class': '',
        'manage_stock': True, 'stock_quantity': bestand, 'stock_status': 'instock',
        'backorders': 'no', 'shipping_class': 'paket-standard',
        'description': beschreibung(sorte, aroma, mit_tips),
        'short_description': kurz(sorte, aroma, mit_tips),
        'categories': [{'id': 607}], 'brands': [1783], 'attributes': attr,
        'tags': [{'id': 7522}],
        'meta_data': [
            {'key': '_ts_gtin', 'value': ean},
            {'key': '_yoast_wpseo_title',
             'value': f'Juicy Jay’s {sorte} King Size Slim' + (' 2in1' if mit_tips else '')},
            {'key': '_yoast_wpseo_metadesc',
             'value': (f'Juicy Jay’s {sorte} King Size Slim: 32 aromatisierte Blättchen, 110 mm, '
                       f'dreifach im Triple-Dip-Verfahren aromatisiert' +
                       (', mit integrierten Tips' if mit_tips else '') + '. Jetzt bei Hanfjack.')},
            {'key': '_yoast_wpseo_focuskw', 'value': f'Juicy Jay’s {sorte}'},
        ],
    }
    if not mit_tips: p['weight'] = '0.006'    # belegt ueber 9006, 9009, 9013
    return p

# --- Boxtexte -----------------------------------------------------------

def boxtext(zeile, sorten, mit_tips):
    liste = '\n'.join(f'<li>{s}</li>' for s in sorten)
    tips  = ' und integrierte Tips' if mit_tips else ''
    je    = 24 // len(sorten)
    return f"""<p>Die Mix-n-Roll-Box von Juicy Jay’s ist eine Displaybox mit 24 Heften, die {len(sorten)} Geschmacksrichtungen zu je {je} Heften mischt. Wer nicht weiß, welche Sorte es werden soll, bekommt sie hier alle auf einmal.</p>
<p>Jedes Heft enthält 32 aromatisierte Blättchen im King-Size-Slim-Format mit 110 mm Länge{tips}. Der Geschmack wird im Triple-Dip-Verfahren aufgebracht: Das Papier läuft dreifach durch die Aromalösung, damit die Note bis zum Ende trägt. Hergestellt wird in Spanien.</p>
<h3 style="margin-top:1.8em">Auf einen Blick:</h3>
<ul>
<li><b>Inhalt:</b> Displaybox mit 24 Heften</li>
<li><b>Je Heft:</b> 32 Blättchen{tips}</li>
<li><b>Format:</b> King Size Slim, 110 mm Länge</li>
<li><b>Aromaverfahren:</b> Triple Dip – dreifach aromatisiert</li>
<li><b>Brennverhalten:</b> langsam brennend</li>
<li><b>Herstellung:</b> Spanien</li>
<li><b>Tabak:</b> nicht enthalten</li>
</ul>
<h3 style="margin-top:1.8em">Die {len(sorten)} Sorten in der Box</h3>
<ul>
{liste}
</ul>
<h3 style="margin-top:1.8em">Hinweise</h3>
<p>Alle Sorten dieser Box gibt es bei uns auch einzeln, falls sich eine davon als Favorit herausstellt. Aromatisierte Papiere sollten im verschlossenen Heft und trocken gelagert werden – liegt das Heft offen, verflüchtigt sich ein Teil der Aromastoffe.</p>"""

def boxkurz(sorten, mit_tips):
    tips = ' und integrierten Tips' if mit_tips else ''
    return (f'<p>Displaybox mit 24 Heften Juicy Jay’s – je 32 aromatisierte Blättchen im Format '
            f'King Size Slim{tips}, dreifach im Triple-Dip-Verfahren aromatisiert. '
            f'{len(sorten)} Geschmacksrichtungen gemischt.</p>')

if __name__ == '__main__':
    frei = json.load(open(S+'freie_skus.json'))
    angelegt = []
    i = 0
    for sorte, aroma, ean, bestand in ZWEIINEINS:
        p = bau(sorte, aroma, ean, bestand, True, frei[i], '1.68')   # 2,00 EUR brutto
        a = ruf('products', p, 'POST'); i += 1
        ruf(f"products/{a['id']}", {'delivery_time': {'id': 4236}}, 'PUT')
        angelegt.append((a['id'], a['name'], ean, bestand)); print('  neu:', a['id'], a['name'])
    for sorte, aroma, ean, bestand in NUR_PAPIER:
        p = bau(sorte, aroma, ean, bestand, False, frei[i], '1.26')  # 1,50 EUR brutto
        a = ruf('products', p, 'POST'); i += 1
        ruf(f"products/{a['id']}", {'delivery_time': {'id': 4236}}, 'PUT')
        angelegt.append((a['id'], a['name'], ean, bestand)); print('  neu:', a['id'], a['name'])

    # Blueberry-Beutel auf das bestehende Produkt 9006
    alt = ruf('products/9006?_fields=id,stock_quantity')
    neu_best = (alt['stock_quantity'] or 0) + 1
    ruf('products/9006', {'stock_quantity': neu_best}, 'PUT')
    print(f"  9006 Blueberry: Bestand {alt['stock_quantity']} -> {neu_best}")

    # Boxen: neue Texte, Lieferzeit 3-7 Tage, Nachbestellung erlaubt
    sorten_2in1 = [s for s, *_ in ZWEIINEINS]
    sorten_kss  = ['Blueberry'] + [s for s, *_ in NUR_PAPIER]
    for pid, sorten, tips in ((18985, sorten_2in1, True), (18984, sorten_kss, False)):
        b = ruf(f'products/{pid}', {
            'description': boxtext(pid, sorten, tips),
            'short_description': boxkurz(sorten, tips),
            'backorders': 'notify',
            'delivery_time': {'id': 16535},
        }, 'PUT')
        print(f"  Box {pid}: {len(b['description'])} Z. | Lieferzeit "
              f"{(b.get('delivery_time') or {}).get('name')} | backorders {b['backorders']}")
    json.dump(angelegt, open(S+'juicy_angelegt.json','w'), ensure_ascii=False)
    print(f'\n{len(angelegt)} Produkte angelegt')
