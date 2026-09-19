# -*- coding: utf-8 -*-
"""Die 30 below_category_content-Bloecke in die Kategoriebeschreibung ziehen.

Die Bloecke standen als Term-Meta `below_category_content` in der Datenbank
und sollten unter dem Produktraster erscheinen. Ausgegeben wurden sie nie -
auf keiner Kategorieseite taucht einer auf, und unter den WPCode-Snippets
gibt es nichts, was sie rendern wuerde. Sie gehoeren ohnehin nach oben.

Der vorhandene Einleitungssatz bleibt vorn stehen, der Block kommt darunter.
Die Abstaende ueber den Ueberschriften setzt html_fix, damit es ohne
Theme-CSS passt.
"""
import json, re, sys, html_fix, hj

S = '/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
QUELLE = '/home/user/18plus-Cannabis-Shop-Popup/shop-texte/kategorien/below-category-content.json'

def nur_text(s):
    return re.sub(r'\s+', ' ', re.sub(r'<[^>]+>', ' ', s or '')).strip()

def zusammen(beschreibung, block):
    b = (beschreibung or '').strip()
    # Der Einleitungssatz liegt als blanker Text vor - fuer sauberes HTML
    # bekommt er einen Absatz, falls er noch keinen hat.
    if b and not re.match(r'\s*<(p|h[1-6]|ul|ol|div)\b', b, re.I):
        b = '<p>' + b + '</p>'
    return (b + '\n' + block.strip()).strip()

if __name__ == '__main__':
    de = json.load(open(QUELLE))['de']
    vorher, plan = {}, {}
    for i in sorted(de, key=int):
        k = hj.ruf(f'products/categories/{i}?_fields=id,name,description')
        if 'id' not in k:
            print('uebersprungen, nicht gefunden:', i); continue
        if nur_text(de[i])[:40] and nur_text(de[i])[:40] in nur_text(k['description']):
            print('schon oben, uebersprungen:', i, k['name']); continue
        neu = html_fix.aufraeumen(zusammen(k['description'], de[i])).strip()
        # Die Einleitung darf nicht verloren gehen
        assert nur_text(k['description'])[:30] in nur_text(neu), i
        assert nur_text(de[i])[:30] in nur_text(neu), i
        vorher[i] = k['description']
        plan[i] = (k['name'], neu)

    json.dump(vorher, open(S + 'kat_beschreibung_vorher.json', 'w'), ensure_ascii=False)
    print(f'\n{len(plan)} Kategorien werden erweitert.\n')

    for i, (name, neu) in plan.items():
        q = hj.ruf(f'products/categories/{i}', {'description': neu}, 'PUT')
        print(f"{i:>6} | {len(nur_text(vorher[i])):3d} -> {len(nur_text(q['description'])):4d} Zeichen | {name[:36]}")
