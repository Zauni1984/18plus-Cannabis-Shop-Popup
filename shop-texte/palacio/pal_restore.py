# -*- coding: utf-8 -*-
"""Inhaltsstoffe bei den Palacio-Produkten wieder eintragen.

In allen 32 Palacio-Datensaetzen fehlte die Inhaltsstoffliste; 28 trugen
stattdessen den Satz, Palacio veroeffentliche keine belegbare Liste. Das
stimmt nicht: die API hinter palacio.cz liefert zu jedem Artikel ein Feld
"ingredients" mit der vollstaendigen INCI-Liste. Die Zuordnung laeuft ueber
EAN, sonst ueber die Katalognummer PALxxxx.

Eingetragen wird beides: die Liste in deutschen Namen, kommagetrennt, und
darunter die unveraenderte INCI-Liste. So steht die rechtlich massgebliche
Form neben der lesbaren - dasselbe Muster wie bei Produkt 26719.
"""
import json, re, hj, pal_inci, html_fix

S = '/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
FALSCH = ' Eine vollständige Inhaltsstoffliste veröffentlicht Palacio nicht in einer Form, die wir hier belegen könnten.'
FLUORID_ALT = ('Ob die Zahnpasta Fluorid enthält, gibt die Produktbezeichnung nicht '
               'an – maßgeblich ist die Angabe auf der Verpackung.')
FLUORID_NEU = ('Die Inhaltsstoffliste nennt keine Fluoridverbindung; maßgeblich '
               'bleibt die Angabe auf der Verpackung.')

# Bundles, deren Inhalt der Text selbst benennt
BUNDLES = {
 14701: [('Forte Sport Gel wärmend', 1878), ('Forte Sport Gel kühlend', 1879)],
 14704: [('Cannahot', 508), ('Cannacool', 509)],
 14708: [('Flexgel kühlend', 502), ('Flexgel wärmend', 503)],
}

def einsetzen(desc, block):
    """Block vor die Hinweise haengen, sonst ans Ende."""
    m = re.search(r'<h3[^>]*>\s*Hinweise\s*</h3>', desc)
    if m:
        return desc[:m.start()] + block + '\n' + desc[m.start():]
    return desc.rstrip() + '\n' + block

def bereinigen(desc):
    desc = desc.replace(FALSCH, '')
    desc = desc.replace(FLUORID_ALT, FLUORID_NEU)
    return desc

def block_einzeln(roh):
    html, offen = pal_inci.block(roh)
    assert not offen, offen
    return html

def block_bundle(teile, ing_von):
    zeilen = []
    for name, pid in teile:
        t = pal_inci.zerlegen(ing_von[pid])
        de, offen = pal_inci.deutsch(t)
        assert not offen, (name, offen)
        zeilen.append(f'<p><b>{name}:</b> ' + ', '.join(de) + '.</p>')
        zeilen.append('<p><b>INCI:</b> ' + ', '.join(t) + '.</p>')
    return ('<h3 style="margin-top:1.8em">Inhaltsstoffe</h3>\n' + '\n'.join(zeilen))

if __name__ == '__main__':
    match = json.load(open(S + 'pal_match.json'))
    jetzt = {x['id']: x for x in json.load(open(S + 'palacio_jetzt.json'))}
    ing_von = {int(i): v['kat']['ingredients'] for i, v in match.items()}

    plan = {}
    for i, v in match.items():
        pid = int(i)
        neu = einsetzen(bereinigen(jetzt[pid]['description']),
                        block_einzeln(v['kat']['ingredients']))
        plan[pid] = neu
    for pid, teile in BUNDLES.items():
        plan[pid] = einsetzen(bereinigen(jetzt[pid]['description']),
                              block_bundle(teile, ing_von))

    # Der Bestand schreibt "margin-top: 1.8em;", die Hygiene "margin-top:1.8em".
    # Deshalb wird der fertige Text durch aufraeumen geschickt und nur dessen
    # Idempotenz geprueft.
    for pid in list(plan):
        plan[pid] = html_fix.aufraeumen(plan[pid]).strip()
        assert plan[pid] == html_fix.aufraeumen(plan[pid]).strip(), pid
        assert 'nicht in einer Form' not in plan[pid], pid
        assert 'INCI' in plan[pid], pid
    print('geprueft:', len(plan), 'Produkte')

    json.dump({str(k): jetzt[k]['description'] for k in plan},
              open(S + 'palacio_desc_vorher.json', 'w'), ensure_ascii=False)

    for pid, neu in sorted(plan.items()):
        q = hj.ruf(f'products/{pid}', {'description': neu}, 'PUT')
        print('aktualisiert:', pid, q['name'][:46])
