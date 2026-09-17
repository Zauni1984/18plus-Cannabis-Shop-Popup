# -*- coding: utf-8 -*-
"""Ersetzt den Text der Smoking-Supreme-Box (18977).

Bisher: nur eine Stichpunktliste, dazu ein Kurztext, der mitten im Satz
abbricht ("Ultrafeine, transparente"). Die vier Schwesterboxen 18978 bis
18981 haben Fliesstexte nach festem Muster - zwei Absaetze, "Auf einen
Blick", "Hinweise". Diese Box bekommt denselben Aufbau.

"transparent" wird nicht uebernommen: die Verpackung sagt ultrafein
(ULTRAFINO-ULTRATHIN) und Ultra Smooth Touch, von Transparenz steht dort
nichts.
"""
import json, base64, urllib.request

S='/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
CK=CS=None
for z in open(S+'.wc_creds'):
    if 'CK=' in z: CK=z.strip().split('CK=',1)[1]
    if 'CS=' in z: CS=z.strip().split('CS=',1)[1]
AUTH='Basic '+base64.b64encode(f'{CK}:{CS}'.encode()).decode()

BESCHREIBUNG = """<p>Die Supreme-Reihe gehört zu den dünnsten Papieren von Smoking. Das Papier ist zu 100 % pflanzlich und FSC-zertifiziert, die Gummierung besteht aus Naturgummi ohne chemische Zusätze.</p>
<p>Jedes Heft enthält 33 Blättchen im Format 110 × 44 mm und zusätzlich 33 Tips – daher die Bezeichnung 2in1. Die Tips tragen einen Spezialschnitt, mit dem sich der Filter ohne Nachschneiden rollen lässt. Diese Displaybox fasst 24 Hefte.</p>
<h3 style="margin-top:1.8em">Auf einen Blick:</h3>
<ul>
<li><b>Inhalt:</b> Displaybox mit 24 Heften</li>
<li><b>Je Heft:</b> 33 Blättchen und 33 Tips</li>
<li><b>Format:</b> 110 × 44 mm (King Size Slim)</li>
<li><b>Papier:</b> ultrafein, 100 % pflanzlich</li>
<li><b>Zertifizierung:</b> aus FSC®-zertifizierter Forstwirtschaft</li>
<li><b>Gummierung:</b> Naturgummi ohne chemische Zusätze</li>
<li><b>Brennverhalten:</b> langsam brennend</li>
<li><b>Herstellung:</b> Spanien (Barcelona)</li>
</ul>
<h3 style="margin-top:1.8em">Hinweise</h3>
<p>Der Spezialschnitt der Tips gibt die Rollrichtung vor: erst den Tip formen, dann das Blättchen darum legen. Die Anzahl geht auf – zu jedem Blättchen gehört ein Tip.</p>"""

KURZ = ("<p>Displaybox mit 24 Heften Smoking Supreme – je 33 ultrafeine Blättchen "
        "im Format 110 × 44 mm und 33 Tips mit Spezialschnitt.</p>")

def ruf(pfad, daten=None, methode='GET'):
    d = json.dumps(daten).encode() if daten is not None else None
    r = urllib.request.Request('https://hanfjack.de/wp-json/wc/v3/'+pfad, data=d, method=methode,
        headers={'Authorization':AUTH,'Content-Type':'application/json'})
    with urllib.request.urlopen(r, timeout=120) as f: return json.load(f)

if __name__ == '__main__':
    a = ruf('products/18977', {'description': BESCHREIBUNG, 'short_description': KURZ}, 'PUT')
    print('18977 aktualisiert')
    print('  Beschreibung:', len(a['description']), 'Zeichen')
    print('  Kurz        :', len(a['short_description']), 'Zeichen')
    import re
    print('  Kurztext    :', re.sub(r'<[^>]+>','',a['short_description']))
