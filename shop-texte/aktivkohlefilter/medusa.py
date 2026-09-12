# -*- coding: utf-8 -*-
"""Medusafilters 6 mm — 14 Varianten (5 Farben x 4 Größen).
Jede Seite bekommt einen eigenen Farb- UND einen eigenen Größenteil, damit die
Varianten nicht als Duplikate gegeneinander laufen."""
import json, itertools, difflib

FARBEN = {
"Organic": dict(
  p1=("Organic ist die einzige Variante der Reihe ohne Farbpigment. Die Cellulose-Kappen bleiben in ihrem "
      "natürlichen Braunton, so wie das Material aus der Produktion kommt. Wer beim Mundstück nichts "
      "zwischen sich und dem Rauch haben will, was dort nicht hingehört, greift zu dieser Variante — "
      "eine Frage der Haltung, nicht der Optik."),
  h2="Warum viele bei Organic bleiben",
  p2=("Gefärbte Filterkappen sind unbedenklich, aber es gibt Leute, die den Gedanken an Pigment direkt an "
      "den Lippen nicht mögen. Für die ist Organic gemacht. Im Paper fällt der Filter kaum auf: Das "
      "Cellulose-Braun liegt nah an ungebleichtem Papier, der Übergang wirkt wie aus einem Stück. Wer "
      "ungebleichte Longpaper dreht, bekommt damit das ruhigste Gesamtbild der ganzen Reihe."),
  bullet="<strong>Ohne Farbpigment:</strong> Cellulose im Naturton, nichts zugesetzt.",
  kurzlabel="Für Puristen gemacht",
  tipp=("Passt zu ungebleichtem Paper",
        "Der Naturton trifft ungebleichte Longpaper fast exakt. Bei weißem Paper setzt sich der Filter "
        "dagegen sichtbar ab — dort wirken die gefärbten Varianten bewusster gewählt.")),
"Rosé": dict(
  p1=("Rosé ist der leiseste der gefärbten Töne: ein warmes Altrosa, das im Paper sichtbar ist, ohne "
      "sich in den Vordergrund zu drängen. Die Farbe sitzt in der Cellulose-Kappe, nicht auf ihr — sie "
      "reibt sich nicht ab und färbt auch bei feuchten Lippen nicht."),
  h2="Wofür sich Rosé anbietet",
  p2=("Zwischen ungefärbt und knallig liegt eine Lücke, und genau die füllt Rosé. Der Ton harmoniert mit "
      "weißem wie mit braunem Paper und wirkt in beiden Fällen abgestimmt statt zufällig. In einer Runde, "
      "in der mehrere Filterfarben unterwegs sind, ist Rosé außerdem der Ton, den man am sichersten "
      "wiedererkennt, ohne lange hinzusehen."),
  bullet="<strong>Zurückhaltender Ton:</strong> Warmes Altrosa, sichtbar ohne aufdringlich zu sein.",
  kurzlabel="Der zurückhaltende Ton",
  tipp=("Bleibt farbecht",
        "Das Pigment steckt in der Cellulose, nicht als Lack darauf. Auch nach längerem Kontakt mit "
        "feuchten Lippen färbt der Filter nicht ab.")),
"Sunset": dict(
  p1=("Sunset ist der kräftigste Ton der Reihe — ein warmes Orangerot, das selbst aus braunem Paper "
      "heraussticht. Das ist der praktische Nutzen dieser Variante: Man sieht auf einen Meter Entfernung, "
      "welches Ende das Mundstück ist, auch bei schlechtem Licht."),
  h2="Wann die kräftige Farbe hilft",
  p2=("Draußen, abends, in einer Runde — genau dann wird die Farbe von Dekoration zu Funktion. Ein "
      "orangeroter Filter ist im Dämmerlicht sofort zu erkennen, während ein naturfarbener mit dem Paper "
      "verschwimmt. Wer regelmäßig im Freien dreht oder auf Festivals unterwegs ist, spart sich damit das "
      "Drehen und Suchen des richtigen Endes."),
  bullet="<strong>Auf Distanz erkennbar:</strong> Orangerot hebt sich auch von braunem Paper deutlich ab.",
  kurzlabel="Auch bei wenig Licht zu finden",
  tipp=("Nützlich bei wenig Licht",
        "Wenn du abends draußen bist, findest du das Mundende ohne Hinsehen. Bei naturfarbenen Filtern "
        "musst du erst tasten.")),
"Violet": dict(
  p1=("Violet ist ein tiefes Lila und bildet mit hellem Papier den stärksten Kontrast der Reihe. Anders "
      "als bei den warmen Tönen wirkt der Filter dadurch bewusst gesetzt — er verschwindet nicht, er "
      "gehört sichtbar dazu."),
  h2="Der Kontrasttyp der Reihe",
  p2=("Auf weißem Paper wirkt Lila am klarsten; auf ungebleichtem Braun wird der Ton dunkler und ruhiger. "
      "Beides funktioniert, nur eben unterschiedlich. In gemischten Runden ist Violet der Ton, der sich am "
      "wenigsten mit anderen verwechseln lässt — praktisch, wenn mehrere Leute gleichzeitig drehen und "
      "hinterher niemand weiß, welcher Joint wem gehört."),
  bullet="<strong>Stärkster Kontrast:</strong> Tiefes Lila, auf hellem Paper sofort zu erkennen.",
  kurzlabel="Der Ton mit dem stärksten Kontrast",
  tipp=("Zuordnung in der Runde",
        "Wenn mehrere gleichzeitig drehen, hilft eine eindeutige Farbe. Lila lässt sich mit keinem "
        "anderen Ton der Reihe verwechseln.")),
"Mixed": dict(
  p1=("Mixed ist die gemischte Packung: Organic, Rosé, Sunset, Violet und weitere Töne liegen "
      "durcheinander im Beutel, ohne feste Verteilung. Welche Farbe als Nächstes kommt, entscheidet der "
      "Griff — planen lässt sich das nicht."),
  h2="Wofür die gemischte Packung gedacht ist",
  p2=("Zwei Gründe sprechen für Mixed. Der erste ist praktisch: In einer Runde bekommt jeder eine andere "
      "Farbe, und hinterher weiß jeder, welcher Joint seiner ist. Der zweite ist banaler — man muss sich "
      "nicht entscheiden. Wer noch nicht weiß, welcher Ton ihm liegt, findet es über eine gemischte "
      "Packung schneller heraus als über vier einzelne."),
  bullet="<strong>Ohne feste Verteilung:</strong> Organic, Rosé, Sunset, Violet und weitere gemischt.",
  kurzlabel="Keine Farbentscheidung nötig",
  tipp=("Farbe als Zuordnung",
        "Jeder in der Runde nimmt eine andere Farbe — danach ist klar, welcher Joint wem gehört. Das ist "
        "der eigentliche Zweck der gemischten Packung.")),
}

GROESSEN = {
50: dict(inhalt="50 Filter im Beutel", kurz="50er", lang="50 Stück",
  p1=("Fünfzig Filter sind die Menge, mit der man eine Marke ausprobiert, ohne sich festzulegen. Bei "
      "regelmäßigem Gebrauch reicht der Beutel zwei bis drei Wochen, und er ist flach genug für die "
      "Innentasche einer Jacke."),
  h2="Warum die kleine Packung zum Einstieg passt",
  p2=("Der 6-mm-Durchmesser fällt schmaler aus als die verbreiteten 8-mm-Filter, und der Zug fühlt sich "
      "dadurch anders an — enger, kontrollierter. Ob dir das liegt, weißt du nach zwei, drei Joints. "
      "Genau dafür ist diese Größe da: ausprobieren, bevor du dich auf einen Vorrat festlegst."),
  bullet="<strong>Handliche Menge:</strong> 50 Stück, flach genug für die Jackentasche.",
  tipps=[("Beutel nach jeder Entnahme zudrücken",
          "Aktivkohle zieht Luftfeuchtigkeit. Ein zugedrückter Beutel hält die Filter bis zum letzten trocken.")]),
100: dict(inhalt="100 Filter im Beutel", kurz="100er", lang="100 Stück",
  p1=("Hundert Filter sind die Größe zum Nachkaufen, wenn die Marke sitzt. Der Beutel hält bei "
      "regelmäßigem Gebrauch gut einen Monat und lässt sich trotzdem komplett mitnehmen — Umfüllen "
      "erübrigt sich."),
  h2="Die Größe für den laufenden Bedarf",
  p2=("Zwischen der Probierpackung und dem Monatsvorrat liegt die Menge, die man tatsächlich am "
      "häufigsten kauft. Hundert Stück decken den normalen Bedarf, ohne dass der Beutel wochenlang offen "
      "steht und Feuchtigkeit zieht. Das ist der stille Vorteil dieser Größe: Sie ist aufgebraucht, bevor "
      "die Lagerung zum Thema wird."),
  bullet="<strong>Monatsbedarf:</strong> 100 Stück, aufgebraucht bevor Lagerung zum Thema wird.",
  tipps=[("Nicht offen liegen lassen",
          "Ein zugedrückter Beutel hält die Filter trocken. Ein offener macht sie über Wochen zäh im Zug.")]),
1000: dict(inhalt="1000 Filter im Beutel", kurz="1000er", lang="1000 Stück",
  p1=("Tausend Filter sind eine Vorratsentscheidung. Der Preis je Filter fällt gegenüber der kleinen "
      "Packung deutlich, und die Frage, ob noch welche da sind, stellt sich für Monate nicht mehr."),
  h2="Wann sich die Vorratspackung rechnet",
  p2=("Der Rechenweg ist einfach: Wer täglich dreht, kommt mit tausend Filtern über ein Jahr. Wer für "
      "mehrere Leute mitkauft, entsprechend kürzer. In beiden Fällen ist der Stückpreis der niedrigste "
      "der Reihe. Der Haken liegt woanders — bei der Lagerung, denn ein Beutel, den du monatelang täglich "
      "öffnest, zieht jedes Mal feuchte Luft."),
  bullet="<strong>Niedrigster Stückpreis:</strong> 1000 Filter, Vorrat für Monate.",
  tipps=[("In kleine Portionen umfüllen",
          "Füll dir 30 bis 50 Stück in ein kleines Gefäß ab und lass den großen Beutel zu. So bleibt der "
          "Rest monatelang trocken."),
         ("Trocken und dunkel lagern",
          "Nicht im Bad, nicht auf der Fensterbank. Ein Schrank genügt — entscheidend ist nur, dass keine "
          "dauerhafte Feuchtigkeit herankommt.")]),
2222: dict(inhalt="2222 Filter im Schraubglas", kurz="2222er Glas", lang="2222 Stück im Glas",
  p1=("2222 Filter im Schraubglas sind die größte Einheit der Reihe. Das Glas ist dabei kein Deko-Element, "
      "sondern der Grund, warum die Menge überhaupt praktikabel ist."),
  h2="Warum bei dieser Menge das Gefäß entscheidet",
  p2=("Ein Beutel dieser Größe wäre nach dem zwanzigsten Öffnen durchfeuchtet — Aktivkohle nimmt "
      "Luftfeuchtigkeit auf, und mit jedem Öffnen kommt neue hinein. Das dicht schließende Schraubglas "
      "unterbindet genau das: Zwischen zwei Entnahmen bleibt die Kohle trocken, und der letzte Filter "
      "zieht so gleichmäßig wie der erste. Bei kleineren Mengen ist das egal, hier ist es der Kern der Sache."),
  bullet="<strong>Dicht schließendes Glas:</strong> Hält die Aktivkohle bis zum letzten Filter trocken.",
  tipps=[("Deckel jedes Mal zudrehen",
          "Der ganze Vorteil des Glases hängt am Verschluss. Handfest genügt, aber eben immer."),
         ("Als Nachfüllstation nutzen",
          "Lass das Glas stehen und füll dir daraus einen kleinen Beutel für unterwegs ab. So wird die "
          "große Menge nicht unhandlich.")]),
}

BASIS_BULLETS = [
 "<strong>Zwei Materialien, zwei Aufgaben:</strong> Das Aktivkohlegranulat kühlt den Rauch, die Cellulose-Kappen halten es im Filter.",
 "<strong>Keine Einbaurichtung:</strong> Beide Enden sind gleich aufgebaut — du legst den Filter ein, wie du ihn greifst.",
 "<strong>6 mm Extra Slim:</strong> Passt in schmale Selbstgedrehte, Longpaper und vorgedrehte Cones.",
 "<strong>Hergestellt in Deutschland:</strong> Gleichbleibende Maßhaltigkeit von Charge zu Charge.",
]

TABELLE = """<h2>Technische Details im Überblick</h2>
<table>
<thead><tr><td><strong>Merkmal</strong></td><td><strong>Details</strong></td></tr></thead>
<tbody>
<tr><td><strong>Filtertyp</strong></td><td>Hybrid-Aktivkohlefilter mit Cellulose-Kappen</td></tr>
<tr><td><strong>Durchmesser</strong></td><td>6 mm (Extra Slim)</td></tr>
<tr><td><strong>Länge</strong></td><td>ca. 25 mm</td></tr>
<tr><td><strong>Füllung</strong></td><td>Aktivkohlegranulat</td></tr>
<tr><td><strong>Endkappen</strong></td><td>beidseitig Cellulose – keine Einbaurichtung</td></tr>
<tr><td><strong>Farbe</strong></td><td>%s</td></tr>
<tr><td><strong>Inhalt</strong></td><td>%s</td></tr>
<tr><td><strong>Herstellung</strong></td><td>Deutschland</td></tr>
</tbody>
</table>"""

PRODUKTE = [
 (13722,"Mixed",1000),(13704,"Mixed",2222),
 (14349,"Organic",100),(13715,"Organic",1000),(14408,"Organic",50),
 (14406,"Rosé",100),(13712,"Rosé",1000),(14409,"Rosé",50),
 (14407,"Sunset",100),(13708,"Sunset",1000),(14411,"Sunset",50),
 (14405,"Violet",100),(13718,"Violet",1000),(14410,"Violet",50),
]

def titel(farbe, kurz):
    for k in ["Medusafilters Aktivkohlefilter 6 mm %s %s | Hanfjack" % (farbe, kurz),
              "Medusafilters Aktivkohlefilter 6 mm %s %s" % (farbe, kurz),
              "Medusafilters Filter 6 mm %s %s | Hanfjack" % (farbe, kurz)]:
        if len(k) <= 60: return k
    return ("Medusafilters 6 mm %s %s | Hanfjack" % (farbe, kurz))[:60]

def bauen(pid, farbe, groesse):
    f, g = FARBEN[farbe], GROESSEN[groesse]
    name = "Medusafilters Aktivkohlefilter 6mm %s – %s" % (
        farbe, "2222 Stück Glas" if groesse == 2222 else "%d Stück" % groesse)
    bullets = [f["bullet"], g["bullet"]] + BASIS_BULLETS
    tipps = [f["tipp"]] + g["tipps"]
    desc = (
      '<p><strong>%s</strong> sind Hybridfilter: ein Kern aus Aktivkohlegranulat, an beiden Enden von '
      'Cellulose-Kappen gehalten. Die Kappen sorgen dafür, dass das Granulat im Filter bleibt und nicht in '
      'den Mund wandert — der Zug bleibt über die ganze Länge gleichmäßig, statt gegen Ende zäh zu werden.</p>'
      '<p>%s</p><p>%s</p>'
      '<h2>%s</h2><p>%s</p>'
      '<h2>%s</h2><p>%s</p>'
      '<h2>Was die Medusafilters ausmacht</h2><ul>%s</ul>'
      '%s'
      '<h2>Praxistipps</h2>%s'
    ) % (name, f["p1"], g["p1"], f["h2"], f["p2"], g["h2"], g["p2"],
         "".join("<li>%s</li>" % b for b in bullets),
         TABELLE % (farbe, g["inhalt"]),
         "".join("<p><strong>%s:</strong> %s</p>" % (t, x) for t, x in tipps))
    short = ('<p><strong>%s:</strong> Die <strong>%s</strong> sind 6-mm-Hybridfilter mit Aktivkohlegranulat '
             'und beidseitigen Cellulose-Kappen — das Granulat bleibt im Filter, der Zug bleibt gleichmäßig, '
             'eine Einbaurichtung gibt es nicht. %s %s aus deutscher Fertigung.</p>'
             ) % (f["kurzlabel"], name, f["p1"].split(".")[0] + ".", g["lang"])
    seo_desc = ("Medusafilters %s in 6 mm: Hybridfilter mit Aktivkohle und beidseitigen Cellulose-Kappen, "
                "25 mm, ohne Einbaurichtung. %s, aus deutscher Fertigung.") % (farbe, g["lang"])
    if len(seo_desc) > 160:
        seo_desc = ("Medusafilters %s, 6 mm: Hybridfilter mit Aktivkohle und Cellulose-Kappen, 25 mm, "
                    "ohne Einbaurichtung. %s, deutsche Fertigung.") % (farbe, g["lang"])
    return dict(id=pid, name=name, desc=desc, short=short,
                focus="Medusafilters Aktivkohlefilter 6mm %s" % farbe,
                seo_title=titel(farbe, g["kurz"]), seo_desc=seo_desc)

out = [bauen(*p) for p in PRODUKTE]
json.dump(out, open('medusa.json','w'), ensure_ascii=False, indent=1)

fehler = [(o['id'], len(o['seo_title']), len(o['seo_desc'])) for o in out
          if len(o['seo_title']) > 60 or not (120 <= len(o['seo_desc']) <= 160)]
print("Medusafilters erzeugt: %d  |  Längenfehler: %s" % (len(out), fehler or "keine"))
for k in ('seo_title','seo_desc','desc','short'):
    w = [o[k] for o in out]
    print("  %-10s eindeutig: %s" % (k, len(set(w)) == len(w)))
paare = sorted((difflib.SequenceMatcher(None,a['desc'],b['desc']).ratio(), a['id'], b['id'])
               for a,b in itertools.combinations(out,2))
print("  Ähnlichkeit: max %.2f · Median %.2f · min %.2f"
      % (paare[-1][0], paare[len(paare)//2][0], paare[0][0]))
