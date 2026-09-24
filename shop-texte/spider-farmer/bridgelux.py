# -*- coding: utf-8 -*-
"""Spider Farmer: Umstellung von Samsung auf Bridgelux in Texten, Tags und SEO.

Belegt am Hersteller (Stand 24.09.2026):
  SF1000, SF1000D, SF2000 (Bridgelux 3030), SF2000Pro, SF4000  -> Bridgelux
  SE1000W, SE1500, SE3000, SE4500, SE5000, SE7000              -> Bridgelux
  SF7000                                                        -> weiter Samsung LM301B
Quellen: spider-farmer.com/products/sf-1000-led-grow-light/ ("upgraded to
Premium Bridgelux"), .../sf-2000-led-grow-light/ ("Advanced Bridgelux 3030"),
.../sf-4000-led-grow-light/, .../spider-farmer-sf2000pro-led-grow-light/
(Seitentitel "2026 ... Bridgelux"), SE-Serie ueber die Sammelseite
se-series-led-grow-light, SF7000 weiterhin mit Samsung LM301B ausgewiesen.
"""
import json, re, sys

S = '/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
sys.path.insert(0, S)
import hjapi

TAG_SAMSUNG_EVO, TAG_SAMSUNG_301H = 6271, 6555
TAG_BRIDGELUX, TAG_BRIDGELUX_3030 = 6284, 11400   # "Bridgelux LEDs", "Bridgelux 3030"

WEEE = ('<p><i>Elektrogerät. Nicht über den Hausmüll entsorgen. '
        'WEEE-Reg.-Nr. DE 39526074.</i></p>')

SF1000_KURZ = ('<p>Dimmbare 100-W-Lampe mit Bridgelux-Dioden für 60 × 60 cm – '
               '249,2 µmol/s, 2,5 µmol/J und Anbindung an das GGS-Smart-System.</p>')

SF1000_TEXT = (
    '<p>Die SF1000 ist die dimmbare 100-W-Lampe für eine Fläche von 60 × 60 cm. '
    'Seit der Version 2026 sitzen darauf <strong>Bridgelux-Dioden</strong> statt '
    'der früheren Samsung LM301H EVO. Spider Farmer hat dafür die Zahl der Dioden '
    'erhöht, sodass jede einzelne weniger Last tragen muss; Lichtstrom, Ausbeute '
    'und Gleichmäßigkeit bleiben laut Hersteller unverändert.</p>\n'
    '<p>Über die Dimmerbox regelst Du die Helligkeit stufenlos, und die Lampe '
    'lässt sich in das Spider Farmer GGS Smart-System einbinden, über das '
    'Lichtzyklen, Dimmstufen und weiteres Zubehör zentral laufen. Gekühlt wird '
    'passiv, also ohne Lüftergeräusch – im Wohnraum ein Argument.</p>\n'
    '<h3 style="margin-top:1.8em">Auf einen Blick:</h3>\n<ul>\n'
    '<li><b>Modell:</b> SF1000 (Version 2026)</li>\n'
    '<li><b>LED-Chips:</b> Bridgelux</li>\n'
    '<li><b>Leistungsaufnahme:</b> 100 W ± 5 %</li>\n'
    '<li><b>PPF:</b> 249,2 µmol/s</li>\n'
    '<li><b>Lichtausbeute:</b> 2,5 µmol/J</li>\n'
    '<li><b>Spektrum:</b> 660–665 nm, 730–740 nm, 2800–3000 K, 4800–5000 K</li>\n'
    '<li><b>Kernfläche:</b> 2 × 2 ft (rund 60 × 60 cm)</li>\n'
    '<li><b>Maximale Fläche:</b> 3 × 3 ft (rund 90 × 90 cm)</li>\n'
    '<li><b>Dimmbar:</b> ja, stufenlos</li>\n'
    '<li><b>Kühlung:</b> passiv, lautlos</li>\n'
    '<li><b>Gewicht:</b> 1,92 kg</li>\n'
    '<li><b>Eingangsspannung:</b> 100–277 V (Wechselstrom)</li>\n'
    '<li><b>Herstellergarantie:</b> 5 Jahre</li>\n'
    '</ul>\n'
    '<h3 style="margin-top:1.8em">Anschluss &amp; Montage</h3>\n'
    '<p>Seilratschen, Haken und Netzkabel liegen bei. Die Helligkeit stellst Du '
    'an der Dimmerbox ein; mehrere Lampen lassen sich über das GGS-System '
    'gemeinsam steuern.</p>\n'
    '<h3 style="margin-top:1.8em">Hinweise</h3>\n'
    '<p>Die SF1000-D ist die nicht dimmbare Schwesterversion. Wer die Leistung '
    'in der Anzucht herunterfahren will, braucht die dimmbare SF1000.</p>\n'
    + WEEE)

HINWEIS_ALT = ('<p>Spider Farmer stellt die LED-Bestückung derzeit auf Bridgelux '
               'um. Welche Chips verbaut sind, kann je nach Produktionscharge '
               'abweichen – wir geben hier nur an, was das aktuelle Datenblatt '
               'ausweist.</p>')
HINWEIS_SF4000 = ('<p>Seit der Version 2026 verbaut Spider Farmer in der SF4000 '
                  'Bridgelux-Dioden statt der früheren Samsung LM301H EVO. Die '
                  'Diodenzahl ist dafür höher, Lichtstrom und Ausbeute bleiben '
                  'laut Hersteller gleich.</p>')
HINWEIS_SF7000 = ('<p>Die SF7000 ist von der Umstellung auf Bridgelux nicht '
                  'betroffen: Der Hersteller weist sie weiterhin mit Samsung '
                  'LM301B aus, anders als SF1000, SF2000, SF2000Pro und '
                  'SF4000.</p>')


def hol(pid):
    return hjapi.ruf(f'products/{pid}', pause=2)


def tags_tauschen(p, zusatz=()):
    alt = [t['id'] for t in p.get('tags') or []]
    neu = [i for i in alt if i not in (TAG_SAMSUNG_EVO, TAG_SAMSUNG_301H)]
    for i in (TAG_BRIDGELUX,) + tuple(zusatz):
        if i not in neu:
            neu.append(i)
    return neu, alt


def meta(p, schluessel):
    for m in p.get('meta_data') or []:
        if m['key'] == schluessel:
            return m['value']
    return ''


def schreiben(pid, daten, was):
    q = hjapi.ruf(f'products/{pid}', daten, 'PUT', pause=3)
    print(f"{pid} {q['sku']:<16} {was}")
    return q


def ersetzen(text, alt, neu, pid):
    assert alt in text, f'{pid}: Textstelle nicht gefunden: {alt[:60]}'
    return text.replace(alt, neu)


def lauf():
    # ---- SF1000: Text, Kurztext, SEO, Name, Tag
    p = hol(16213)
    neu, alt = tags_tauschen(p)
    schreiben(16213, {
        'name': 'Spider Farmer SF-1000 100W Vollspektrum LED Pflanzenlampe 2026',
        'short_description': SF1000_KURZ,
        'description': SF1000_TEXT,
        'tags': [{'id': i} for i in neu],
        'meta_data': [
            {'key': '_yoast_wpseo_title',
             'value': 'Spider Farmer SF-1000 100W LED 2026 | Bridgelux'},
            {'key': '_yoast_wpseo_metadesc',
             'value': 'Spider Farmer SF1000 Version 2026: 100 W dimmbar mit '
                      'Bridgelux-Dioden, 249,2 µmol/s für 60 × 60 cm. Jetzt bei '
                      'Hanfjack bestellen.'},
            {'key': '_yoast_wpseo_focuskw', 'value': 'Spider Farmer SF-1000 2026'},
        ]}, f'Text, Kurztext, SEO, Name, Tags {alt} -> {neu}')

    # ---- SF1000D: Samsung-Restangabe raus, Version im Namen
    p = hol(16214)
    d = ersetzen(p['description'],
                 '<li><b>Lichtausbeute:</b> 2,5 µmol/J (Highest efficiency 3,14 µmol/j single diode)</li>',
                 '<li><b>Lichtausbeute:</b> 2,5 µmol/J</li>', 16214)
    d = ersetzen(d, '<li><b>Modell:</b> SF1000D</li>',
                 '<li><b>Modell:</b> SF1000D (Version 2026)</li>', 16214)
    schreiben(16214, {'name': p['name'] + ' 2026', 'description': d},
              'Samsung-Diodenwert entfernt, Version 2026')

    # ---- SF2000: Bridgelux 3030 benennen
    p = hol(16215)
    neu, alt = tags_tauschen(p, (TAG_BRIDGELUX_3030,))
    d = ersetzen(p['description'], '<li><b>LED-Chips:</b> Bridgelux</li>',
                 '<li><b>LED-Chips:</b> Bridgelux 3030</li>', 16215)
    d = ersetzen(d, '<li><b>Modell:</b> SF2000</li>',
                 '<li><b>Modell:</b> SF2000 (Version 2026)</li>', 16215)
    schreiben(16215, {'name': p['name'] + ' 2026', 'description': d,
                      'tags': [{'id': i} for i in neu],
                      'meta_data': [{'key': '_yoast_wpseo_title',
                                     'value': 'Spider Farmer SF2000 200W LED 2026 | Bridgelux 3030'}]},
              f'Bridgelux 3030, Version 2026, Tags {alt} -> {neu}')

    # ---- SF2000Pro
    p = hol(16216)
    neu, alt = tags_tauschen(p)
    d = ersetzen(p['description'], '<li><b>Modell:</b> SF2000Pro</li>',
                 '<li><b>Modell:</b> SF2000Pro (Version 2026)</li>', 16216)
    schreiben(16216, {'name': p['name'] + ' 2026', 'description': d,
                      'tags': [{'id': i} for i in neu]},
              f'Version 2026, Tags {alt} -> {neu}')

    # ---- SF4000: Samsung aus dem Namen, Hinweis eindeutig, SEO
    p = hol(16218)
    neu, alt = tags_tauschen(p)
    d = ersetzen(p['description'], HINWEIS_ALT, HINWEIS_SF4000, 16218)
    d = ersetzen(d, '<li><b>Modell:</b> SF4000</li>',
                 '<li><b>Modell:</b> SF4000 (Version 2026)</li>', 16218)
    schreiben(16218, {
        'name': 'Spider Farmer SF-4000 450W LED Growlampe 2026',
        'description': d,
        'tags': [{'id': i} for i in neu],
        'meta_data': [
            {'key': '_yoast_wpseo_title',
             'value': 'Spider Farmer SF-4000 450W LED 2026 | Bridgelux'},
            {'key': '_yoast_wpseo_metadesc',
             'value': 'Spider Farmer SF4000 Version 2026: 450 W mit '
                      'Bridgelux-Dioden, 1171 µmol/s für 120 × 120 cm. Jetzt bei '
                      'Hanfjack bestellen.'},
            {'key': '_yoast_wpseo_focuskw',
             'value': 'Spider Farmer SF-4000 450W LED Growlampe'},
        ]}, f'Name ohne Samsung, Hinweis, SEO, Tags {alt} -> {neu}')

    # ---- SF7000: bleibt Samsung LM301B, Hinweis richtigstellen
    p = hol(16219)
    d = ersetzen(p['description'], HINWEIS_ALT, HINWEIS_SF7000, 16219)
    d = ersetzen(d, '<li><b>Modell:</b> SF7000</li>',
                 '<li><b>Modell:</b> SF7000</li>\n<li><b>LED-Chips:</b> Samsung LM301B</li>',
                 16219)
    schreiben(16219, {'description': d}, 'Hinweis richtiggestellt, Samsung LM301B benannt')

    # ---- SE-Serie: nur Tags
    for pid in (16227, 16229, 16231):
        p = hol(pid)
        neu, alt = tags_tauschen(p)
        schreiben(pid, {'tags': [{'id': i} for i in neu]}, f'Tags {alt} -> {neu}')

    # ---- Sets: Tags und Jahresangabe
    for pid in (16238, 16239, 16240):
        p = hol(pid)
        neu, alt = tags_tauschen(p)
        name = p['name'] if '2026' in p['name'] else p['name'] + ' (2026)'
        schreiben(pid, {'name': name, 'tags': [{'id': i} for i in neu]},
                  f'Name {name[-8:]}, Tags {alt} -> {neu}')


if __name__ == '__main__':
    lauf()
