# -*- coding: utf-8 -*-
"""Erstellt den Plan: welcher Tag bleibt, welcher wird umbenannt, welcher faellt weg."""
import json, re, sys, collections
sys.path.insert(0, '.')
import tag_norm as N

T = json.load(open('tags_alle.json'))
MARKEN = json.load(open('marken_ci.json'))

# Wirkstoff- und Massangaben fliegen raus: die stehen jetzt in den Produktattributen
# Nur Messwerte, keine Produktarten: "16-24 % THC" faellt weg, "CBD Samen" bleibt
RAUS = [
  ('Wirkstoffangabe', r'\d\s*%|\d+\s*[-–]\s*\d+\s*%?\s*(?:THC|CBD|CBG)|'
                      r'\b(?:hoher|hohe|high|niedriger|viel|wenig)\s+(?:THC|CBD)\b|'
                      r'\b(?:THC|CBD|CBG)[- ]?(?:Gehalt|Wert|Anteil)\b'),
  ('Massangabe',      r'^[\d,.\s]+(?:x[\d,.\s]+)*\s*(?:cm|mm|m|ml|l|liter|g|kg|w|watt|d|zoll|"|st(?:ü|ue)ck)?$'),
  ('Massangabe',      r'^\d+(?:[.,]\d+)?\s*(?:cm|mm|ml|liter|l|kg|g|watt|w|zoll)\b'),
]


def raus_grund(name):
    n = N.entities(name)
    for grund, muster in RAUS:
        if re.search(muster, n, re.I):
            return grund
    return None


plan = {'entfernen': [], 'umbenennen': [], 'zusammenfuehren': [], 'unveraendert': []}
behalten = []
for t in T:
    g = raus_grund(t['name'])
    if g:
        plan['entfernen'].append({**t, 'grund': g})
    else:
        behalten.append(t)

# Normalisieren und nach Grundform gruppieren
gruppen = collections.defaultdict(list)
for t in behalten:
    neu = N.saeubern(t['name'], MARKEN)
    if not neu:
        plan['entfernen'].append({**t, 'grund': 'leer nach Bereinigung'})
        continue
    gruppen[N.grundform(neu)].append({**t, 'neu': neu})

for key, gr in gruppen.items():
    # Kanon: ein Markenname schlaegt alles - "Spider Farmer" darf nicht zu
    # "Spiderfarmer" werden. Sonst entscheidet die Haeufigkeit.
    def punkte(t):
        marke = 1_000_000 if t['neu'].lower() in MARKEN else 0
        return (marke + t['count'] * 10 - t['neu'].count('-') * 2, -len(t['neu']), t['neu'])
    kanon = max(gr, key=punkte)
    ziel = kanon['neu']
    for t in gr:
        if t['id'] == kanon['id']:
            (plan['unveraendert'] if t['name'] == ziel else plan['umbenennen']).append(
                {**t, 'ziel': ziel})
        else:
            plan['zusammenfuehren'].append({**t, 'ziel': ziel, 'ziel_id': kanon['id']})

json.dump(plan, open('tag_plan.json', 'w'), ensure_ascii=False, indent=1)
for k, v in plan.items():
    print(f'{k:18} {len(v):5} Tags, {sum(x["count"] for x in v):6} Zuordnungen')
print(f'\nTags nachher: {len(plan["unveraendert"]) + len(plan["umbenennen"])}')
