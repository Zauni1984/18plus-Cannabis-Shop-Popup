# -*- coding: utf-8 -*-
"""Ergaenzt den G-Rollz Pets Rock Reggae Medium Tray (30312).

Bisher stand als ganze Beschreibung "<ul><li>27,5x17,5cm</li></ul>" - 32
Zeichen, kein Kurztext. Die neun Schwester-Trays folgen einem festen Muster;
dieses Produkt bekommt denselben Aufbau, dieselben Attribute und dieselbe
Tag-Struktur. Der Serien-Tag "Pets Rock" fehlte und wurde angelegt.

Zum Material macht G-Rollz keine Angaben - der Hinweistext sagt das auch so,
wie bei den Geschwistern.
"""
import json, base64, urllib.request

S='/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
CK=CS=None
for z in open(S+'.wc_creds'):
    if 'CK=' in z: CK=z.strip().split('CK=',1)[1]
    if 'CS=' in z: CS=z.strip().split('CS=',1)[1]
AUTH='Basic '+base64.b64encode(f'{CK}:{CS}'.encode()).decode()

BESCHREIBUNG = """<p>Rolling Tray aus der Pets-Rock-Serie von G-Rollz mit dem Motiv Reggae – Medium mit 17,5 × 27,5 cm.</p>
<p>Ein Rolling Tray ist eine flache Schale mit hochgezogenem Rand: Sie hält zusammen, was sonst auf dem Tisch verteilt liegt, und was daneben geht, bleibt in der Schale statt im Teppich.</p>
<h3 style="margin-top:1.8em">Auf einen Blick:</h3>
<ul>
<li><b>Format:</b> Medium (17,5 × 27,5 cm)</li>
<li><b>Motivserie:</b> Pets Rock</li>
<li><b>Motiv:</b> Reggae</li>
</ul>
<h3 style="margin-top:1.8em">Hinweise</h3>
<p>Die Maße sind die entscheidende Angabe – ein Tray muss dorthin passen, wo es liegen soll. Zu Material und weiteren Kennwerten veröffentlicht G-Rollz keine Angaben, die wir hier belegen könnten – wir tragen dazu keine geschätzten Werte ein.</p>"""

KURZ = ("<p>Rolling Tray 17,5 × 27,5 cm aus der Pets-Rock-Serie von G-Rollz, "
        "Motiv Reggae.</p>")

aenderung = {
    'description': BESCHREIBUNG,
    'short_description': KURZ,
    'attributes': [
        {'id': 12, 'name': 'Variante', 'options': ['Pets Rock'],                 'visible': True, 'variation': False},
        {'id': 3,  'name': 'Farbe',    'options': ['Bunt'],                      'visible': True, 'variation': False},
        {'id': 65, 'name': 'Format',   'options': ['Medium (17,5 × 27,5 cm)'],   'visible': True, 'variation': False},
        {'id': 66, 'name': 'Motiv',    'options': ['Reggae'],                    'visible': True, 'variation': False},
    ],
    'tags': [{'id': i} for i in (8287, 8290, 1981, 8292, 2012, 5740, 8297, 20721, 7522)],
    'meta_data': [
        {'key': '_yoast_wpseo_title',    'value': 'G-Rollz Pets Rock Reggae Medium Tray 17,5 × 27,5 cm'},
        {'key': '_yoast_wpseo_metadesc', 'value': 'G-Rollz Rolling Tray aus der Pets-Rock-Serie mit dem Motiv '
                                                  'Reggae: Medium-Format 17,5 × 27,5 cm, flache Schale mit '
                                                  'hochgezogenem Rand. Jetzt bei Hanfjack.'},
        {'key': '_yoast_wpseo_focuskw',  'value': 'G-Rollz Pets Rock Reggae Tray'},
    ],
}

def ruf(pfad, daten=None, methode='GET'):
    d = json.dumps(daten).encode() if daten is not None else None
    r = urllib.request.Request('https://hanfjack.de/wp-json/wc/v3/'+pfad, data=d, method=methode,
        headers={'Authorization':AUTH,'Content-Type':'application/json'})
    with urllib.request.urlopen(r, timeout=120) as f: return json.load(f)

if __name__ == '__main__':
    a = ruf('products/30312', aenderung, 'PUT')
    m = {x['key']: x['value'] for x in a['meta_data']}
    print('30312 aktualisiert')
    print('  Beschreibung', len(a['description']), 'Zeichen | Kurz', len(a['short_description']))
    print('  Attribute   ', [(x['name'], x['options']) for x in a['attributes']])
    print('  Tags        ', [t['name'] for t in a['tags']])
    print('  Yoast-Titel ', len(m.get('_yoast_wpseo_title','')), m.get('_yoast_wpseo_title'))
    print('  Yoast-Meta  ', len(m.get('_yoast_wpseo_metadesc','')))
