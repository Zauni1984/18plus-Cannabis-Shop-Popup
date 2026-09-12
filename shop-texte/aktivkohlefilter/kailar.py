# -*- coding: utf-8 -*-
"""Kailar Cellulose Slim 6 mm — 12 Varianten (6 Farben x 3 Gebinde).
Abgrenzung zu Medusafilters: Kailars Eigenheit sind Cellulose-Endkappen
ANSTELLE von Keramik. Darum dreht sich der Text, nicht um die Kohle."""
import json, itertools, difflib

FARBEN = {
"Grün": dict(
 p1=("Grün ist Kailars Hausfarbe — der Ton, an dem die Marke im Regal erkannt wird. Im Paper "
     "sitzt ein sattes Laubgrün, das weder ins Neon noch ins Olive kippt."),
 h2="Die Farbe, an der man Kailar erkennt",
 p2=("Bei Filtern ist Farbe selten eine reine Geschmacksfrage. Grün liegt im mittleren Helligkeitsbereich: "
     "hell genug, um sich von braunem Paper abzusetzen, dunkel genug, um auf weißem nicht zu blass zu "
     "wirken. Wer zwischen mehreren Marken wechselt, sieht am Filterende sofort, welche gerade im Umlauf "
     "ist — praktisch, wenn du zwei Sorten parallel testest."),
 bullet="<strong>Sattes Laubgrün:</strong> Kailars Hausfarbe, auf hellem wie braunem Paper erkennbar.",
 tipp=("Marken auseinanderhalten",
       "Wenn du parallel eine zweite Filtermarke testest, ist die Farbe die schnellste Unterscheidung — "
       "du musst dir nichts notieren.")),
"Pink": dict(
 p1=("Pink ist der hellste Ton der Reihe und der einzige, der auch bei Kunstlicht seine Sättigung behält. "
     "Auf ungebleichtem Paper entsteht dadurch der auffälligste Kontrast, den Kailar anbietet."),
 h2="Wenn der Filter sichtbar sein soll",
 p2=("Es gibt zwei Lager: Die einen wollen, dass der Filter im Paper verschwindet, die anderen wollen ihn "
     "sehen. Pink ist eindeutig für das zweite Lager. Der praktische Nebeneffekt: In einer Runde, in der "
     "mehrere gedreht haben, findest du deinen ohne Nachfragen wieder — und bei schwachem Licht bleibt "
     "Pink am längsten erkennbar."),
 bullet="<strong>Höchste Sichtbarkeit:</strong> Behält die Sättigung auch bei Kunstlicht.",
 tipp=("Bleibt bei Kunstlicht farbig",
       "Warmes Zimmerlicht zieht dunklen Tönen die Farbe. Pink lässt sich davon am wenigsten beeindrucken.")),
"Schwarz": dict(
 p1=("Schwarz ist der einzige dunkle Ton im Kailar-Sortiment — und der einzige, dem man den Gebrauch nicht "
     "ansieht. Was bei hellen Kappen nach ein paar Zügen als Verfärbung sichtbar wird, bleibt hier unsichtbar."),
 h2="Der Ton, dem man nichts ansieht",
 p2=("Helle Cellulose-Kappen nehmen im Gebrauch Farbe an. Das ist normal, ändert nichts an der Funktion, "
     "sieht aber gebraucht aus. Bei Schwarz fällt es schlicht nicht auf. Für alle, die einen Joint nicht in "
     "einem Zug durchrauchen und ihn zwischendurch ablegen, ist das der praktischste Unterschied im "
     "ganzen Sortiment. Optisch passt Schwarz zu jedem Paper, weil es keine Farbfamilie beansprucht."),
 bullet="<strong>Zeigt keinen Gebrauch:</strong> Verfärbungen der Kappe bleiben unsichtbar.",
 tipp=("Für Joints, die abgelegt werden",
       "Wer nicht in einem Zug durchraucht, sieht bei hellen Filtern schnell Gebrauchsspuren. Schwarz "
       "verzeiht das.")),
"Weiß": dict(
 p1=("Weiß ist der neutrale Ton der Reihe: unauffällig zu weißem Paper, klar abgesetzt von ungebleichtem "
     "Braun. Wer keine Farbentscheidung treffen will, trifft mit Weiß die, die immer passt."),
 h2="Die Variante ohne Festlegung",
 p2=("Nicht jeder will, dass am Mundstück eine Farbe Position bezieht. Weiß fügt sich in weißes Longpaper "
     "so ein, dass der Übergang kaum auffällt, und setzt sich von braunem Paper klar genug ab, um den "
     "Filter zu markieren. Es ist der Ton, den man kauft, wenn der Filter seine Arbeit machen und sonst "
     "nichts sagen soll."),
 bullet="<strong>Neutral zu jedem Paper:</strong> Unauffällig in Weiß, klar abgesetzt in Braun.",
 tipp=("Der sichere Griff",
       "Wenn du Filter für mehrere Leute kaufst und die Vorlieben nicht kennst: Weiß eckt bei niemandem an.")),
"Organic": dict(
 p1=("Organic ist die ungefärbte Variante — Cellulose in ihrem natürlichen Braunton, ohne zugesetztes "
     "Pigment. Der Ton schwankt von Charge zu Charge minimal, weil er aus dem Material selbst kommt."),
 h2="Ohne Pigment, mit sichtbarer Herkunft",
 p2=("Dass der Farbton leicht variiert, ist bei Organic kein Mangel, sondern der Beleg dafür, dass nichts "
     "nachgefärbt wurde. Wer bewusst zu ungebleichtem Paper greift, bekommt hier den passenden Filter: "
     "Der Übergang zwischen Paper und Kappe verschwimmt fast vollständig, der Joint wirkt aus einem Stück. "
     "Auf weißem Paper setzt sich das Braun dagegen deutlich ab."),
 bullet="<strong>Ohne Farbpigment:</strong> Cellulose im Naturton, Schwankungen zwischen Chargen inklusive.",
 tipp=("Zu ungebleichtem Paper",
       "Der Naturton trifft ungebleichte Longpaper fast exakt — der Übergang fällt kaum auf.")),
"Mixed": dict(
 p1=("Mixed enthält die Töne des Sortiments gemischt in einer Packung, ohne feste Verteilung. Was du "
     "greifst, entscheidet der Zufall — planen lässt sich das nicht."),
 h2="Wofür sich die gemischte Packung eignet",
 p2=("Der offensichtliche Grund ist, sich nicht auf eine Farbe festlegen zu müssen. Der weniger "
     "offensichtliche: Wer Kailar zum ersten Mal kauft, weiß nicht, ob ihm der Cellulose-Zug liegt, und "
     "schon gar nicht, welcher Ton ihm gefällt. Eine gemischte Packung beantwortet beide Fragen "
     "gleichzeitig — und was übrig bleibt, wird trotzdem geraucht."),
 bullet="<strong>Ohne feste Verteilung:</strong> Die Töne des Sortiments gemischt in einer Packung.",
 tipp=("Zum Herausfinden",
       "Eine gemischte Packung beantwortet die Farbfrage schneller als sechs einzelne — und nichts bleibt liegen.")),
}

GEBINDE = {
"250": dict(inhalt="250 Filter im Beutel", kurz="250er", lang="250 Stück",
 p1=("Der 250er-Beutel ist Kailars Standardgröße: genug für rund zwei Monate regelmäßigen Gebrauch, "
     "klein genug, um komplett in eine Schublade oder Dose zu passen."),
 h2="Die Größe für den laufenden Bedarf",
 p2=("Zwischen Probierpackung und Vorratskauf liegt die Menge, die man tatsächlich am häufigsten nachlegt. "
     "250 Stück decken den normalen Bedarf, ohne dass der Beutel monatelang offensteht. Das ist der "
     "unauffällige Vorteil dieser Größe: Sie ist aufgebraucht, bevor Lagerung zum Thema wird."),
 bullet="<strong>250 Stück im Beutel:</strong> Rund zwei Monate bei regelmäßigem Gebrauch.",
 tipps=[("Beutel zudrücken",
         "Aktivkohle zieht Luftfeuchtigkeit. Ein zugedrückter Beutel hält die Filter bis zum letzten trocken.")]),
"500": dict(inhalt="500 Filter im Glas", kurz="500er Glas", lang="500 Stück im Glas",
 p1=("Die 500er-Variante kommt im Schraubglas statt im Beutel. Das ist bei dieser Menge kein Zubehör, "
     "sondern der Grund, warum sie funktioniert."),
 h2="Warum ab dieser Menge das Glas kommt",
 p2=("Ein Beutel wird mit jedem Öffnen ein Stück undichter — der Zip nutzt sich ab, die Folie knickt, und "
     "irgendwann schließt er nicht mehr richtig. Bei 250 Stück ist das egal, weil sie vorher aufgebraucht "
     "sind. Bei 500 nicht mehr. Das Schraubglas hält über die ganze Nutzungsdauer gleich dicht, und der "
     "letzte Filter zieht wie der erste."),
 bullet="<strong>500 Stück im Schraubglas:</strong> Dichtet über die ganze Nutzungsdauer gleich gut.",
 tipps=[("Deckel handfest zudrehen",
         "Der Vorteil des Glases hängt am Verschluss — handfest genügt, aber eben jedes Mal."),
        ("Nicht auf die Fensterbank",
         "Direktes Sonnenlicht bleicht die gefärbten Kappen über Monate aus. Ein Schrank reicht.")]),
"825": dict(inhalt="825 Filter im Glas", kurz="825er Glas", lang="825 Stück im Glas",
 p1=("825 Filter im Glas sind die größte Einheit, die Kailar anbietet — und die mit dem niedrigsten "
     "Stückpreis der Reihe."),
 h2="Wann sich die größte Einheit rechnet",
 p2=("Der Rechenweg ist einfach: Wer täglich dreht, kommt mit 825 Filtern über ein gutes Jahr; wer für "
     "mehrere Leute mitkauft, entsprechend kürzer. In beiden Fällen liegt der Preis je Filter deutlich "
     "unter dem der 250er-Packung. Die Frage bei dieser Menge ist nicht der Preis, sondern ob du sie "
     "vernünftig lagern kannst — und genau dafür ist das Glas da."),
 bullet="<strong>825 Stück im Glas:</strong> Niedrigster Stückpreis der Reihe.",
 tipps=[("Kleine Menge abfüllen",
         "Lass das Glas stehen und füll dir 30 bis 50 Stück für unterwegs ab. So wird die Menge nicht unhandlich."),
        ("Ein Jahr realistisch einplanen",
         "Bei täglichem Gebrauch reicht das Glas rund ein Jahr. Aktivkohle verdirbt nicht, solange sie trocken bleibt.")]),
}

BASIS_BULLETS = [
 "<strong>Cellulose statt Keramik:</strong> Beide Endkappen bestehen aus Cellulose — das ist der Unterschied zum verbreiteten Keramikfilter.",
 "<strong>Keine Einbaurichtung:</strong> Beide Enden sind gleich aufgebaut, du legst den Filter ein wie du ihn greifst.",
 "<strong>Vegane Aktivkohle:</strong> Gewonnen aus Kokosnussschalen.",
 "<strong>ca. 5,9 mm:</strong> Slim-Format für schmale Selbstgedrehte, Longpaper und Cones.",
]

TABELLE = """<h2>Technische Details im Überblick</h2>
<table>
<thead><tr><td><strong>Merkmal</strong></td><td><strong>Details</strong></td></tr></thead>
<tbody>
<tr><td><strong>Filtertyp</strong></td><td>Aktivkohlefilter mit Cellulose-Endkappen</td></tr>
<tr><td><strong>Durchmesser</strong></td><td>ca. 5,9 mm (Slim)</td></tr>
<tr><td><strong>Aktivkohle</strong></td><td>vegan, aus Kokosnussschalen</td></tr>
<tr><td><strong>Endkappen</strong></td><td>beidseitig Cellulose – keine Einbaurichtung</td></tr>
<tr><td><strong>Farbe</strong></td><td>%s</td></tr>
<tr><td><strong>Inhalt</strong></td><td>%s</td></tr>
</tbody>
</table>"""

PRODUKTE = [
 (27671,"Grün","250"),(27676,"Mixed","250"),(27675,"Organic","250"),
 (27674,"Pink","250"),(27673,"Schwarz","250"),(27672,"Weiß","250"),
 (27679,"Grün","500"),(27678,"Mixed","500"),(27677,"Pink","500"),
 (27680,"Grün","825"),(27681,"Mixed","825"),(27682,"Pink","825"),
]
NAMEN = {
 27671:"Kailar Aktivkohlefilter Cellulose Slim 6mm Grün (250 Stück)",
 27676:"Kailar Aktivkohlefilter Cellulose Slim 6mm Mixed (250 Stück)",
 27675:"Kailar Aktivkohlefilter Cellulose Slim 6mm Organic (250 Stück)",
 27674:"Kailar Aktivkohlefilter Cellulose Slim 6mm Pink (250 Stück)",
 27673:"Kailar Aktivkohlefilter Cellulose Slim 6mm Schwarz (250 Stück)",
 27672:"Kailar Aktivkohlefilter Cellulose Slim 6mm Weiß (250 Stück)",
 27679:"Kailar Aktivkohlefilter Cellulose Slim 6mm Grün im Glas – 500 Stück",
 27678:"Kailar Aktivkohlefilter Cellulose Slim 6mm Mixed im Glas – 500 Stück",
 27677:"Kailar Aktivkohlefilter Cellulose Slim 6mm Pink im Glas – 500 Stück",
 27680:"Kailar Aktivkohlefilter Cellulose Slim Grün 6mm 825er Glas",
 27681:"Kailar Aktivkohlefilter Cellulose Slim Mixed 6mm 825er Glas",
 27682:"Kailar Aktivkohlefilter Cellulose Slim Pink 6mm 825er Glas",
}

def titel(farbe, kurz):
    for k in ["Kailar Aktivkohlefilter Cellulose 6 mm %s %s | Hanfjack" % (farbe, kurz),
              "Kailar Aktivkohlefilter Cellulose 6 mm %s %s" % (farbe, kurz),
              "Kailar Cellulose Slim 6 mm %s %s | Hanfjack" % (farbe, kurz)]:
        if len(k) <= 60: return k
    return "Kailar Cellulose 6 mm %s %s | Hanfjack" % (farbe, kurz)

def meta(farbe, lang):
    kandidaten = [
      ("Kailar Cellulose Slim %s, 6 mm: Aktivkohlefilter mit beidseitigen Cellulose-Kappen statt Keramik, "
       "vegane Kokoskohle, ohne Einbaurichtung. %s.") % (farbe, lang),
      ("Kailar Cellulose Slim %s, 6 mm: Cellulose-Kappen statt Keramik, vegane Kokoskohle, ohne "
       "Einbaurichtung. %s.") % (farbe, lang),
      ("Kailar Cellulose Slim %s, ca. 5,9 mm: beidseitige Cellulose-Kappen statt Keramik, vegane "
       "Aktivkohle aus Kokosnussschalen. %s.") % (farbe, lang)]
    for m in kandidaten:
        if 120 <= len(m) <= 160: return m
    return min(kandidaten, key=lambda m: abs(len(m) - 140))

def bauen(pid, farbe, gr):
    f, g = FARBEN[farbe], GEBINDE[gr]
    name = NAMEN[pid]
    bullets = [f["bullet"], g["bullet"]] + BASIS_BULLETS
    tipps = [f["tipp"]] + g["tipps"]
    desc = (
      '<p><strong>%s</strong> unterscheiden sich in einem Punkt von den meisten Aktivkohlefiltern: '
      'Die Endkappen bestehen nicht aus Keramik, sondern aus Cellulose. Das macht den Zug etwas weicher '
      'und hält zusätzlich feine Partikel zurück, die sonst am Kohlekern vorbeikommen.</p>'
      '<p>%s</p><p>%s</p>'
      '<h2>%s</h2><p>%s</p>'
      '<h2>%s</h2><p>%s</p>'
      '<h2>Was die Kailar Cellulose ausmacht</h2><ul>%s</ul>'
      '%s'
      '<h2>Praxistipps</h2>%s'
    ) % (name, f["p1"], g["p1"], f["h2"], f["p2"], g["h2"], g["p2"],
         "".join("<li>%s</li>" % b for b in bullets),
         TABELLE % (farbe, g["inhalt"]),
         "".join("<p><strong>%s:</strong> %s</p>" % (t, x) for t, x in tipps))
    short = ('<p><strong>Cellulose statt Keramik:</strong> Die <strong>%s</strong> tragen an beiden Enden '
             'Cellulose-Kappen — der Zug fällt dadurch weicher aus, und feine Partikel bleiben zurück. '
             'Vegane Aktivkohle aus Kokosnussschalen, ca. 5,9 mm, keine Einbaurichtung. %s %s</p>'
             ) % (name, f["p1"].split("—")[0].strip().rstrip('.') + ".", g["lang"] + ".")
    return dict(id=pid, name=name, desc=desc, short=short,
                focus="Kailar Aktivkohlefilter %s" % farbe,
                seo_title=titel(farbe, g["kurz"]), seo_desc=meta(farbe, g["lang"]))

out = [bauen(*p) for p in PRODUKTE]
json.dump(out, open('kailar.json','w'), ensure_ascii=False, indent=1)

fehler = [(o['id'], len(o['seo_title']), len(o['seo_desc'])) for o in out
          if len(o['seo_title']) > 60 or not (120 <= len(o['seo_desc']) <= 160)]
print("Kailar erzeugt: %d  |  Längenfehler: %s" % (len(out), fehler or "keine"))
for k in ('seo_title','seo_desc','desc','short'):
    w = [o[k] for o in out]
    print("  %-10s eindeutig: %s" % (k, len(set(w)) == len(w)))
paare = sorted((difflib.SequenceMatcher(None,a['desc'],b['desc']).ratio(), a['id'], b['id'])
               for a,b in itertools.combinations(out,2))
print("  Ähnlichkeit: max %.2f (%s/%s) · Median %.2f · min %.2f"
      % (paare[-1][0], paare[-1][1], paare[-1][2], paare[len(paare)//2][0], paare[0][0]))
print("  Wörter: %d bis %d" % (min(len(o['desc'].split()) for o in out),
                               max(len(o['desc'].split()) for o in out)))
