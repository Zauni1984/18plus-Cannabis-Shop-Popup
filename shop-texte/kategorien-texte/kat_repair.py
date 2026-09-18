# -*- coding: utf-8 -*-
"""Die 30 beim ersten Versuch zerschossenen Kategoriebeschreibungen richten.

Der erste Lauf hat Einleitung und Block zusammengefuegt und als HTML
geschrieben. WordPress hat die Tags entfernt, dadurch klebten die
Ueberschriften am Text ("Woraus Hanftee bestehtGetrocknete Blaetter...").
Hier wird aus Sicherung und Blockdatei neu gebaut, diesmal im Format, das
die Installation zulaesst.
"""
import json, re, sys
S = '/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
sys.path.insert(0, S)
import kat_format, hj

QUELLE = '/home/user/18plus-Cannabis-Shop-Popup/shop-texte/kategorien/below-category-content.json'

def nur_text(s):
    return re.sub(r'\s+', ' ', re.sub(r'<[^>]+>', ' ', s or '')).strip()

if __name__ == '__main__':
    de = json.load(open(QUELLE))['de']
    vorher = json.load(open(S + 'kat_beschreibung_vorher.json'))
    ergebnis = {}
    for i in sorted(vorher, key=int):
        neu = kat_format.bauen(vorher[i],
                               kat_format.aus_block(kat_format.entitaeten(de[i])))
        # Nichts darf verloren gehen
        assert nur_text(vorher[i])[:30] in nur_text(neu), i
        assert nur_text(kat_format.entitaeten(de[i]))[:30] in nur_text(neu), i
        # Ueberschrift darf nicht am Text kleben
        assert '</strong>\n' in neu or '<strong>' not in neu, i
        ergebnis[i] = neu

    for i, neu in ergebnis.items():
        q = hj.ruf(f'products/categories/{i}', {'description': neu}, 'PUT')
        gespeichert = q['description']
        ok = gespeichert.replace('\r\n', '\n').strip() == neu.strip()
        print(f"{i:>6} | {len(nur_text(gespeichert)):4d} Zeichen | "
              f"{'unveraendert uebernommen' if ok else 'ABWEICHUNG'} | {q['name'][:30]}")
        ergebnis[i] = gespeichert
    json.dump(ergebnis, open(S + 'kat_beschreibung_repariert.json', 'w'), ensure_ascii=False)
