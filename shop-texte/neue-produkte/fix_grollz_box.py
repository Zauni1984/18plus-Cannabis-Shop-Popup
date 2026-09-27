# -*- coding: utf-8 -*-
"""Boxtexte 30294 und 30290 nachziehen.

Beide Texte sagten bisher, die Stueckzahl der Box sei nicht bekannt. Sie ist
es: Herstellerseite und Boxaufdruck nennen 50 Heftchen je Display und 50
Blaettchen je Heftchen (32 + 18 gratis). Dazu kommen die drei Siegel
(chlorfrei, ohne Gentechnik, vegan), das weisse Papier, die Heftchenfarbe,
die Fertigung in Spanien und die Herstellernummern GR08A-DIS / GR09A-DIS.
"""
import json, hj

SIEGEL = (
 '<h3 style="margin-top:1.8em">Chlorfrei, ohne Gentechnik, vegan</h3>\n'
 '<p>Drei Siegel stehen auf Box und Heftchen: Das Papier kommt ohne Chlor aus, '
 'die Rohstoffe sind nicht gentechnisch verändert, und auch die Gummierung ist '
 'rein pflanzlich – auf der Heftchenkante steht dafür „Vegan + Plant '
 'Based“. Gefertigt wird bei einem Partnerbetrieb in Spanien.</p>')

HINWEIS = (
 '<h3 style="margin-top:1.8em">Hinweise</h3>\n'
 '<p>Einzelne Heftchen führen wir separat. Filter Tips gehören nicht zum '
 'Lieferumfang; G-Rollz führt sie als eigene Artikel. Zu Grammatur und '
 'Blattmaß veröffentlicht G-Rollz keine Angaben – geschätzte Werte tragen '
 'wir nicht ein.</p>')

boxen = {
 30294: dict(
   mpn='GR08A-DIS',
   tags=[{'id': 7522}, {'id': 2012}, {'id': 279}, {'id': 1993},
         {'id': 1998}, {'id': 197}],
   attributes=[
     {'id': 65, 'visible': True, 'variation': False, 'options': ['King Size Slim']},
     {'id': 2,  'visible': True, 'variation': False,
      'options': ['50 Heftchen mit je 50 Blättchen']},
     {'id': 3,  'visible': True, 'variation': False, 'options': ['Rot']},
   ],
   short=('<p>Box mit 50 Heftchen à 50 ultradünnen King-Size-Slim-Blättchen '
          'von G-Rollz – chlorfrei, ohne Gentechnik und vegan.</p>'),
   lang=(
    '<p>Die <strong>G-Rollz Diablos King Slim Classic</strong> sind ultradünne '
    'King-Size-Slim-Blättchen aus weißem Papier, geliefert als ganze Box mit 50 '
    'Heftchen. Jedes Heftchen enthält 50 Blättchen – 32 plus 18 gratis, wie '
    'der Aufdruck sagt. Das macht 2.500 Blättchen je Box.</p>\n'
    '<p>Dünnes Papier verbrennt langsamer und tritt geschmacklich weniger in den '
    'Vordergrund – der Grund, warum sich ultradünne Sorten gehalten haben.</p>\n'
    + SIEGEL + '\n'
    '<h3 style="margin-top:1.8em">Auf einen Blick:</h3>\n'
    '<ul>\n'
    '<li><b>Format:</b> King Size Slim</li>\n'
    '<li><b>Inhalt:</b> 50 Heftchen à 50 Blättchen, zusammen 2.500 Blättchen</li>\n'
    '<li><b>Papier:</b> weiß, ultradünn</li>\n'
    '<li><b>Heftchen:</b> rot, Serie Diablos</li>\n'
    '<li><b>Ausgelobt:</b> chlorfrei, ohne Gentechnik, vegan</li>\n'
    '<li><b>Herstellung:</b> Partnerbetrieb in Spanien</li>\n'
    '<li><b>Artikelnummer des Herstellers:</b> GR08A-DIS</li>\n'
    '</ul>\n' + HINWEIS),
   metadesc=('G-Rollz Diablos Box: 50 Heftchen à 50 ultradünnen '
             'King-Size-Slim-Blättchen, chlorfrei und vegan. Jetzt bei Hanfjack.'),
 ),
 30290: dict(
   mpn='GR09A-DIS',
   tags=[{'id': 7522}, {'id': 2012}, {'id': 264}, {'id': 1998}, {'id': 197}],
   attributes=[
     {'id': 65, 'visible': True, 'variation': False, 'options': ['King Size']},
     {'id': 2,  'visible': True, 'variation': False,
      'options': ['50 Heftchen mit je 50 Blättchen']},
     {'id': 3,  'visible': True, 'variation': False, 'options': ['Schwarz']},
   ],
   short=('<p>Box mit 50 Heftchen à 50 extradünnen King-Size-Blättchen von '
          'G-Rollz – chlorfrei, ohne Gentechnik und vegan.</p>'),
   lang=(
    '<p>Die <strong>G-Rollz King´s Choice</strong> sind extradünne '
    'King-Size-Blättchen aus weißem Papier, geliefert als ganze Box mit 50 '
    'Heftchen. Jedes Heftchen enthält 50 Blättchen – 32 plus 18 gratis, wie '
    'der Aufdruck sagt. Das macht 2.500 Blättchen je Box.</p>\n'
    '<p>King Size ist das Standardformat für längere Drehungen. G-Rollz führt '
    'die King´s Choice als <i>King Size Wide</i> – sie ist also breiter als eine '
    'King Size Slim und lässt sich entsprechend voller drehen.</p>\n'
    + SIEGEL + '\n'
    '<h3 style="margin-top:1.8em">Auf einen Blick:</h3>\n'
    '<ul>\n'
    '<li><b>Format:</b> King Size, vom Hersteller als King Size Wide geführt</li>\n'
    '<li><b>Inhalt:</b> 50 Heftchen à 50 Blättchen, zusammen 2.500 Blättchen</li>\n'
    '<li><b>Papier:</b> weiß, extradünn</li>\n'
    '<li><b>Heftchen:</b> schwarz, Serie King´s Choice</li>\n'
    '<li><b>Ausgelobt:</b> chlorfrei, ohne Gentechnik, vegan</li>\n'
    '<li><b>Herstellung:</b> Partnerbetrieb in Spanien</li>\n'
    '<li><b>Artikelnummer des Herstellers:</b> GR09A-DIS</li>\n'
    '</ul>\n' + HINWEIS),
   metadesc=('G-Rollz King´s Choice Box: 50 Heftchen à 50 extradünnen '
             'King-Size-Blättchen, chlorfrei und vegan. Jetzt bei Hanfjack.'),
 ),
}

if __name__ == '__main__':
    import html_fix
    for pid, b in boxen.items():
        for f in ('short', 'lang'):
            assert b[f].strip() == html_fix.aufraeumen(b[f]).strip(), (pid, f)
        assert len(b['metadesc']) <= 156, (pid, len(b['metadesc']))
        print(pid, 'Meta', len(b['metadesc']))

    vorher = {pid: hj.ruf(f'products/{pid}') for pid in boxen}
    json.dump(vorher, open('/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/'
              '1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/grollz_box_vorher.json',
              'w'), ensure_ascii=False)

    for pid, b in boxen.items():
        q = hj.ruf(f'products/{pid}', {
            'mpn': b['mpn'], 'tags': b['tags'], 'attributes': b['attributes'],
            'short_description': b['short'], 'description': b['lang'],
            'meta_data': [{'key': '_yoast_wpseo_metadesc', 'value': b['metadesc']}],
        }, 'PUT')
        print('aktualisiert:', pid, q['name'], '| MPN', q['mpn'],
              '| Tags', len(q['tags']), '| Attribute', [(a['name'], a['options']) for a in q['attributes']])
