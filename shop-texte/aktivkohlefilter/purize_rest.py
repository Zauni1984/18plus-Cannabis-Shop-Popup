# -*- coding: utf-8 -*-
"""PURIZE Rest: Super Slim 5mm, Slim 7mm (Air + Aktivkohle), Regular 9mm,
Big Size 14mm, Conical, Variety Bag — 12 Produkte, 6 Formatfamilien.
Jede Familie hat eine eigene, aus den Shopdaten belegte Abgrenzung."""
import json, itertools, difflib

TABELLE = """<h2>Technische Details im Überblick</h2>
<table>
<thead><tr><td><strong>Merkmal</strong></td><td><strong>Details</strong></td></tr></thead>
<tbody>%s</tbody>
</table>"""
def zeile(k, v): return "<tr><td><strong>%s</strong></td><td>%s</td></tr>" % (k, v)

VERSTOESSE = []
def bauen(pid, name, focus, desc, short, seo_title, seo_desc):
    if len(seo_title) > 60:
        VERSTOESSE.append((pid, 'Titel', len(seo_title), seo_title))
    if not (120 <= len(seo_desc) <= 160):
        VERSTOESSE.append((pid, 'Meta', len(seo_desc), seo_desc))
    return dict(id=pid, name=name, desc=desc, short=short, focus=focus,
                seo_title=seo_title, seo_desc=seo_desc)

out = []

# ---------------------------------------------------------- Super Slim 5 mm
tab_ss = TABELLE % "".join([
 zeile("Filtertyp","PURIZE Super Slim, Aktivkohlefilter"),
 zeile("Durchmesser","5,0 mm"),
 zeile("Länge","ca. 27 mm"),
 zeile("Füllung","Aktivkohle aus Kokosnussschalen"),
 zeile("Endkappen","beidseitig Keramik – keine Einbaurichtung"),
 zeile("Herstellung","Deutschland"),
])
out.append(bauen(13392,
 "PURIZE Aktivkohlefilter Super Slim Size 5mm – 50 Stück",
 "PURIZE Super Slim 5mm 50 Stück",
 '<p><strong>PURIZE Aktivkohlefilter Super Slim Size 5mm</strong> sind mit 5,0 mm der schmalste Filter '
 'im PURIZE-Sortiment — schmaler als der XTRA Slim, gedacht für Joints, bei denen jeder Millimeter zählt.</p>'
 '<p>Bei diesem Durchmesser verändert sich der Zug spürbar gegenüber breiteren Filtern: enger, direkter. '
 'Ob dir das liegt, merkst du am besten an der kleinen Packung, bevor du eine größere Menge kaufst.</p>'
 '<h2>Der schmalste Filter der Reihe</h2>'
 '<p>5,0 mm ist 0,9 mm schmaler als der XTRA Slim — auf den ersten Blick wenig, im Paper aber deutlich '
 'spürbar. Für sehr dünne Selbstgedrehte und Cones, bei denen ein 6-mm-Filter schon zu breit wirkt, ist '
 'das der passende Durchmesser.</p>'
 '<h2>Die 50er-Packung zum Ausprobieren</h2>'
 '<p>Fünfzig Filter reichen, um zu testen, ob dir der enge Zug liegt, bevor du dich auf eine größere '
 'Packung festlegst. Der Beutel ist flach genug für die Jackentasche.</p>'
 '<h2>Was den Super Slim ausmacht</h2><ul>'
 '<li><strong>5,0 mm:</strong> Der schmalste Standardfilter im PURIZE-Sortiment.</li>'
 '<li><strong>Keramik an beiden Enden:</strong> Keine Einbaurichtung.</li>'
 '<li><strong>Aktivkohle aus Kokosnussschalen:</strong> Kühlt den Rauch auf dem Weg zum Mundstück.</li>'
 '<li><strong>ca. 27 mm Länge:</strong> Genug Fläche zum Greifen beim Drehen.</li>'
 '<li><strong>Hergestellt in Deutschland.</strong></li></ul>'
 + tab_ss +
 '<h2>Praxistipps</h2>'
 '<p><strong>Erst testen, dann festlegen:</strong> Der enge Zug ist Geschmackssache. Die 50er-Packung ist '
 'dafür genau richtig bemessen.</p>'
 '<p><strong>Für sehr dünne Rolls:</strong> Bei Standard-Joints reicht meist der 6-mm-XTRA-Slim. Zum '
 'Super Slim greifst du, wenn das Paper selbst schon schmal ausfällt.</p>',
 '<p><strong>Der schmalste Filter der Reihe:</strong> Der <strong>PURIZE Super Slim 5mm</strong> misst '
 '5,0 mm — 0,9 mm schmaler als der XTRA Slim. Keramikkappen beidseitig, Kokosnuss-Aktivkohle, ca. 27 mm '
 'Länge, keine Einbaurichtung. 50 Stück im Beutel, hergestellt in Deutschland.</p>',
 "PURIZE Super Slim 5 mm Aktivkohlefilter 50er | Hanfjack",
 "PURIZE Super Slim 5,0 mm: der schmalste PURIZE-Filter, Keramikkappen beidseitig, Kokosnuss-Aktivkohle, "
 "ca. 27 mm. 50 Stück im Beutel, aus Deutschland."))

out.append(bauen(13397,
 "PURIZE Aktivkohlefilter Super Slim Size 5mm – 500 Stück",
 "PURIZE Super Slim 5mm 500 Stück",
 '<p><strong>PURIZE Aktivkohlefilter Super Slim Size 5mm</strong> sind mit 5,0 mm der schmalste Filter '
 'im PURIZE-Sortiment. Diese 500er-Packung ist die Vorratsgröße für alle, die wissen, dass ihnen der '
 'enge Zug liegt.</p>'
 '<p>Der Stückpreis fällt gegenüber der 50er-Packung deutlich, und die Frage, ob noch welche da sind, '
 'stellt sich für Monate nicht mehr.</p>'
 '<h2>Der schmalste Filter der Reihe</h2>'
 '<p>5,0 mm ist 0,9 mm schmaler als der XTRA Slim. Für sehr dünne Selbstgedrehte und Cones, bei denen '
 'ein 6-mm-Filter schon zu breit wirkt, ist das der passende Durchmesser.</p>'
 '<h2>Warum sich die 500er-Packung lohnt</h2>'
 '<p>Wer regelmäßig zum Super Slim greift, kommt mit 500 Stück mehrere Monate aus. Der Beutel wird dabei '
 'oft genug geöffnet, dass sich das Abfüllen einer kleinen Portion für unterwegs lohnt — so bleibt der '
 'Rest trocken.</p>'
 '<h2>Was den Super Slim ausmacht</h2><ul>'
 '<li><strong>5,0 mm:</strong> Der schmalste Standardfilter im PURIZE-Sortiment.</li>'
 '<li><strong>500 Stück:</strong> Niedrigerer Stückpreis als die 50er-Packung.</li>'
 '<li><strong>Keramik an beiden Enden:</strong> Keine Einbaurichtung.</li>'
 '<li><strong>Aktivkohle aus Kokosnussschalen.</strong></li>'
 '<li><strong>Hergestellt in Deutschland.</strong></li></ul>'
 + (TABELLE % "".join([zeile("Filtertyp","PURIZE Super Slim, Aktivkohlefilter"), zeile("Durchmesser","5,0 mm"),
    zeile("Länge","ca. 27 mm"), zeile("Füllung","Aktivkohle aus Kokosnussschalen"),
    zeile("Endkappen","beidseitig Keramik – keine Einbaurichtung"), zeile("Inhalt","500 Filter im Beutel"),
    zeile("Herstellung","Deutschland")])) +
 '<h2>Praxistipps</h2>'
 '<p><strong>Kleine Portion abfüllen:</strong> Bei 500 Stück lohnt sich ein kleines Gefäß für unterwegs — '
 'der große Beutel bleibt dann zu und trocken.</p>',
 '<p><strong>Vorrat für Monate:</strong> Der <strong>PURIZE Super Slim 5mm</strong> — 5,0 mm, der '
 'schmalste PURIZE-Filter — kommt hier als 500er-Packung mit niedrigerem Stückpreis. Keramikkappen '
 'beidseitig, Kokosnuss-Aktivkohle, ca. 27 mm, hergestellt in Deutschland.</p>',
 "PURIZE Super Slim 5 mm Aktivkohlefilter 500er | Hanfjack",
 "PURIZE Super Slim 5,0 mm im 500er-Beutel: schmalster PURIZE-Filter, Keramikkappen beidseitig, "
 "Kokosnuss-Aktivkohle, ca. 27 mm, hergestellt in Deutschland."))

out.append(bauen(13373,
 "PURIZE Aktivkohlefilter Super Slim Size 5mm 10 Gläser mit je 111 Filter",
 "PURIZE Super Slim 5mm 10 Gläser",
 '<p><strong>PURIZE Aktivkohlefilter Super Slim Size 5mm</strong> kommen in dieser Ausführung als '
 'Zehnergebinde: 10 Gläser mit je 111 Filtern, insgesamt 1.110 Stück.</p>'
 '<p>Das ist keine Menge für den Eigenbedarf, sondern für Weitergabe und Verkauf gedacht — jedes Glas '
 'bleibt einzeln verschlossen, bis es gebraucht wird.</p>'
 '<h2>Der schmalste Filter der Reihe</h2>'
 '<p>5,0 mm ist 0,9 mm schmaler als der XTRA Slim — für sehr dünne Selbstgedrehte und Cones.</p>'
 '<h2>Warum als Zehnergebinde</h2>'
 '<p>Zehn einzeln verschlossene Gläser statt eines großen Behälters: Neun bleiben dicht, während eines '
 'in Gebrauch ist. Für den Weiterverkauf im eigenen Geschäft oder als Display ist das die praktikable '
 'Form — jedes Glas lässt sich separat abgeben.</p>'
 '<h2>Was den Super Slim ausmacht</h2><ul>'
 '<li><strong>5,0 mm:</strong> Der schmalste Standardfilter im PURIZE-Sortiment.</li>'
 '<li><strong>10 Gläser à 111 Filter:</strong> 1.110 Stück, einzeln verschlossen.</li>'
 '<li><strong>Keramik an beiden Enden:</strong> Keine Einbaurichtung.</li>'
 '<li><strong>Aktivkohle aus Kokosnussschalen.</strong></li>'
 '<li><strong>Hergestellt in Deutschland.</strong></li></ul>'
 + (TABELLE % "".join([zeile("Filtertyp","PURIZE Super Slim, Aktivkohlefilter"), zeile("Durchmesser","5,0 mm"),
    zeile("Füllung","Aktivkohle aus Kokosnussschalen"), zeile("Endkappen","beidseitig Keramik"),
    zeile("Inhalt","10 Gläser à 111 Filter (1.110 Stück)"), zeile("Herstellung","Deutschland")])) +
 '<h2>Praxistipps</h2>'
 '<p><strong>Für Weitergabe geeignet:</strong> Jedes Glas lässt sich einzeln abgeben, ohne die übrigen '
 'zu öffnen.</p>',
 '<p><strong>Zehnergebinde:</strong> Der <strong>PURIZE Super Slim 5mm</strong> kommt hier als 10 Gläser '
 'à 111 Filter (1.110 Stück gesamt) — für Weitergabe oder Verkauf, jedes Glas einzeln verschlossen. '
 '5,0 mm, Keramikkappen beidseitig, hergestellt in Deutschland.</p>',
 "PURIZE Super Slim 5 mm, 10 Gläser à 111 | Hanfjack",
 "PURIZE Super Slim 5,0 mm als Zehnergebinde: 10 Gläser à 111 Filter, einzeln verschlossen. "
 "Keramikkappen beidseitig, Kokosnuss-Aktivkohle, Deutschland."))

# --------------------------------------------------------------- Slim 7 mm
out.append(bauen(13678,
 "PURIZE Air Filter Slim Size 7mm Weiß – 50 Stück",
 "PURIZE Air Filter Slim 7mm",
 '<p><strong>PURIZE Air Filter Slim Size 7mm</strong> sind kein Aktivkohlefilter — sie enthalten keine '
 'Aktivkohle. Innen sitzt ausschließlich Filterpapier, das dem Joint einen festen Abschluss und einen '
 'gleichmäßigen Luftstrom gibt.</p>'
 '<p>Wer den Geschmack seiner Kräuter unverändert will und nur den festen Sitz eines Filters sucht, ohne '
 'dass irgendetwas dazwischen filtert, ist bei dieser Variante richtig — sie ist bewusst kein Ersatz für '
 'einen Aktivkohlefilter, sondern ein anderes Produkt.</p>'
 '<h2>Was ein Air Filter anders macht als ein Aktivkohlefilter</h2>'
 '<p>Der Unterschied ist einfach: Ohne Aktivkohle gibt es keine Kühlung durch das Filtermaterial und '
 'keine Bindung von Partikeln im Filterkern — der Air Filter sitzt als reiner Luftkanal im Papier. Er '
 'übernimmt die Funktion, die ein Filter fürs Drehen ohnehin hat: dem Papier ein festes Ende geben, '
 'Krümel zurückhalten, die sonst durchrutschen.</p>'
 '<h2>Für wen sich das eignet</h2>'
 '<p>Alle, die schon Aktivkohlefilter probiert haben und den Geschmacksunterschied nicht mögen, greifen '
 'zum Air Filter. Er verändert weder Temperatur noch Aroma — er ist im Kern nur ein Formstück im Paper.</p>'
 '<h2>Eigenschaften</h2><ul>'
 '<li><strong>Keine Aktivkohle:</strong> reiner Luftfilter aus Filterpapier.</li>'
 '<li><strong>7 mm:</strong> Slim-Format für klassische und dünne Joints.</li>'
 '<li><strong>27 mm Länge.</strong></li>'
 '<li><strong>Formstabil:</strong> gibt dem Papier einen festen Abschluss.</li>'
 '<li><strong>Hergestellt in Deutschland.</strong></li></ul>'
 + (TABELLE % "".join([zeile("Filtertyp","Air Filter, ohne Aktivkohle"), zeile("Durchmesser","7 mm"),
    zeile("Länge","27 mm"), zeile("Füllung","keine – Filterpapier"), zeile("Inhalt","50 Stück im Beutel"),
    zeile("Herstellung","Deutschland")])) +
 '<h2>Praxistipps</h2>'
 '<p><strong>Nicht mit Aktivkohlefiltern verwechseln:</strong> Wer Kühlung und Filterung sucht, ist bei '
 'einem echten Aktivkohlefilter wie dem XTRA Slim besser aufgehoben — dieser hier ist bewusst ohne '
 'Kohle.</p>',
 '<p><strong>Ohne Aktivkohle:</strong> Der <strong>PURIZE Air Filter Slim 7mm</strong> ist reines '
 'Filterpapier ohne Kohlefüllung — kein Geschmacks- oder Temperaturunterschied, nur ein fester Abschluss '
 'für den Joint. 27 mm Länge, 50 Stück im Beutel, hergestellt in Deutschland.</p>',
 "PURIZE Air Filter Slim 7 mm ohne Aktivkohle | Hanfjack",
 "PURIZE Air Filter Slim 7 mm: reines Filterpapier ohne Aktivkohle, 27 mm lang, fester Abschluss fürs "
 "Paper. 50 Stück im Beutel, hergestellt in Deutschland."))

out.append(bauen(13670,
 "PURIZE Aktivkohlefilter Slim Size 7mm Weiß",
 "PURIZE Aktivkohlefilter Slim 7mm",
 '<p><strong>PURIZE Aktivkohlefilter Slim Size 7mm</strong> sind — anders als der baugleich benannte '
 'Air Filter — mit echter Aktivkohle gefüllt. 7 mm Durchmesser, Keramikkappen an beiden Enden, '
 'Kokosnuss-Aktivkohle im Kern.</p>'
 '<p>Wer zwischen dem 5,9-mm-XTRA-Slim und dem 9-mm-Regular sucht, findet hier die Zwischengröße — '
 'etwas breiter als der schmalste PURIZE-Filter, ohne die Ausmaße der Regular-Reihe.</p>'
 '<h2>Die Zwischengröße im Sortiment</h2>'
 '<p>7 mm liegt zwischen XTRA Slim (5,9 mm) und Regular (9 mm). Für Joints, die etwas mehr Fläche als '
 'ein Slim-Filter brauchen, aber nicht die Breite eines Regular, ist das der passende Durchmesser.</p>'
 '<h2>Als Zehnerpackung im Bezug</h2>'
 '<p>Diese Ausführung wird als Paket aus zehn Beuteln zu je 50 Stück geführt — 500 Filter in einem Bezug, '
 'gedacht für laufenden Bedarf statt für die einzelne Probierpackung.</p>'
 '<h2>Was den Slim 7mm ausmacht</h2><ul>'
 '<li><strong>7 mm:</strong> Zwischengröße zwischen XTRA Slim und Regular.</li>'
 '<li><strong>Keramik an beiden Enden:</strong> Keine Einbaurichtung.</li>'
 '<li><strong>Aktivkohle aus Kokosnussschalen.</strong></li>'
 '<li><strong>10 Beutel à 50 Stück:</strong> 500 Filter im Bezug.</li>'
 '<li><strong>Hergestellt in Deutschland.</strong></li></ul>'
 + (TABELLE % "".join([zeile("Filtertyp","PURIZE Slim, Aktivkohlefilter"), zeile("Durchmesser","7 mm"),
    zeile("Füllung","Aktivkohle aus Kokosnussschalen"), zeile("Endkappen","beidseitig Keramik"),
    zeile("Inhalt","10 Beutel à 50 Stück (500 gesamt)"), zeile("Herstellung","Deutschland")])) +
 '<h2>Praxistipps</h2>'
 '<p><strong>Nicht mit dem Air Filter verwechseln:</strong> Beide heißen „Slim 7mm", nur dieser hier '
 'enthält Aktivkohle. Der Name allein sagt das nicht.</p>',
 '<p><strong>Die Zwischengröße:</strong> Der <strong>PURIZE Aktivkohlefilter Slim 7mm</strong> liegt '
 'zwischen XTRA Slim und Regular. Keramikkappen beidseitig, Kokosnuss-Aktivkohle, im Bezug aus 10 '
 'Beuteln à 50 Stück, hergestellt in Deutschland.</p>',
 "PURIZE Aktivkohlefilter Slim 7 mm, 10 Beutel | Hanfjack",
 "PURIZE Slim 7 mm mit Aktivkohle: Keramikkappen beidseitig, Kokosnuss-Aktivkohle, im Bezug aus 10 "
 "Beuteln à 50 Stück, hergestellt in Deutschland."))

# ------------------------------------------------------------- Regular 9 mm
out.append(bauen(13690,
 "PURIZE Aktivkohlefilter Regular Size 9mm Weiß – 50 Stück",
 "PURIZE Regular 9mm",
 '<p><strong>PURIZE Aktivkohlefilter Regular Size 9mm</strong> sind für dicke Joints, Blunts und '
 'Pfeifen gebaut: 9 mm Durchmesser (technisch 8,3 mm), 35,7 mm Länge — deutlich mehr Fläche als die '
 'Slim-Varianten.</p>'
 '<p>Bei dieser Breite ändert sich die Füllung: Statt Kokosnuss-Aktivkohle wie in den schmaleren PURIZE-'
 'Filtern kommt hier Steinkohle-Aktivkohle zum Einsatz.</p>'
 '<h2>Warum die Füllung hier anders ist</h2>'
 '<p>Steinkohle-Aktivkohle hat andere Korneigenschaften als Kokosnuss-Kohle und eignet sich für die '
 'größere Filterfläche der Regular-Reihe. Am grundsätzlichen Aufbau ändert das nichts: Keramikkappen an '
 'beiden Enden, keine Einbaurichtung.</p>'
 '<h2>Für dicke Konstruktionen gebaut</h2>'
 '<p>Die 35,7 mm Länge geben bei breiten Papers und Pfeifenköpfen mehr Halt als ein Slim-Filter. Wer '
 'überwiegend dünne Joints dreht, ist mit XTRA Slim oder Slim besser bedient — der Regular ist für die '
 'Fälle gedacht, in denen ein schmaler Filter im breiten Paper verrutscht.</p>'
 '<h2>Was den Regular 9mm ausmacht</h2><ul>'
 '<li><strong>9 mm (8,3 mm technisch):</strong> Für dicke Joints, Blunts und Pfeifen.</li>'
 '<li><strong>35,7 mm Länge:</strong> Mehr Halt in breiten Papers.</li>'
 '<li><strong>Steinkohle-Aktivkohle:</strong> Andere Füllung als die schmaleren PURIZE-Filter.</li>'
 '<li><strong>Keramik an beiden Enden:</strong> Keine Einbaurichtung.</li>'
 '<li><strong>Hergestellt in Deutschland.</strong></li></ul>'
 + (TABELLE % "".join([zeile("Filtertyp","PURIZE Regular, Aktivkohlefilter"), zeile("Durchmesser","9 mm (8,3 mm technisch)"),
    zeile("Länge","35,7 mm"), zeile("Füllung","Steinkohle-Aktivkohle"),
    zeile("Endkappen","beidseitig Keramik"), zeile("Inhalt","50 Stück im Beutel"), zeile("Herstellung","Deutschland")])) +
 '<h2>Praxistipps</h2>'
 '<p><strong>Für breite Papers:</strong> Bei King-Size- oder Wide-Papern sitzt der Regular fester als '
 'ein Slim-Filter.</p>',
 '<p><strong>Für dicke Konstruktionen:</strong> Der <strong>PURIZE Regular 9mm</strong> misst 35,7 mm '
 'bei 9 mm Durchmesser und nutzt Steinkohle- statt Kokosnuss-Aktivkohle. Keramikkappen beidseitig, '
 '50 Stück im Beutel, hergestellt in Deutschland.</p>',
 "PURIZE Aktivkohlefilter Regular 9 mm, 50er | Hanfjack",
 "PURIZE Regular 9 mm, 35,7 mm lang: Steinkohle-Aktivkohle, Keramikkappen beidseitig, für dicke "
 "Joints. 50 Stück im Beutel, aus Deutschland."))

out.append(bauen(13684,
 "PURIZE Aktivkohlefilter Regular Short Size 9mm Weiß – 50 Stück",
 "PURIZE Regular Short 9mm",
 '<p><strong>PURIZE Aktivkohlefilter Regular Short Size 9mm</strong> haben denselben 9-mm-Durchmesser '
 'wie der reguläre Regular, sind mit 27 mm aber gut acht Millimeter kürzer.</p>'
 '<p>Der Grund für die kürzere Bauform ist Platz: In kompakteren Konstruktionen oder Pfeifenköpfen passt '
 'der lange Regular nicht immer, der Short schon.</p>'
 '<h2>Kürzer, gleicher Durchmesser</h2>'
 '<p>27 mm statt 35,7 mm — das ist der einzige Unterschied zum regulären Regular. Durchmesser, Füllung '
 'und Kappen sind identisch. Wer bereits weiß, dass ihm die Breite des Regular passt, aber die Länge zu '
 'viel ist, findet hier die kompaktere Variante.</p>'
 '<h2>Für kompakte Konstruktionen</h2>'
 '<p>Kürzere Filter beanspruchen weniger Platz im Paper oder Pfeifenkopf. Bei sehr dicken, aber kurzen '
 'Konstruktionen ist das der praktischere Zuschnitt.</p>'
 '<h2>Was den Regular Short ausmacht</h2><ul>'
 '<li><strong>9 mm (8,3 mm technisch):</strong> Gleicher Durchmesser wie der Regular.</li>'
 '<li><strong>27 mm Länge:</strong> Rund 8 mm kürzer als der Standard-Regular.</li>'
 '<li><strong>Steinkohle-Aktivkohle:</strong> Wie beim Regular.</li>'
 '<li><strong>Keramik an beiden Enden:</strong> Keine Einbaurichtung.</li>'
 '<li><strong>Hergestellt in Deutschland.</strong></li></ul>'
 + (TABELLE % "".join([zeile("Filtertyp","PURIZE Regular Short, Aktivkohlefilter"), zeile("Durchmesser","9 mm (8,3 mm technisch)"),
    zeile("Länge","27 mm"), zeile("Füllung","Steinkohle-Aktivkohle"),
    zeile("Endkappen","beidseitig Keramik"), zeile("Inhalt","50 Stück im Beutel"), zeile("Herstellung","Deutschland")])) +
 '<h2>Praxistipps</h2>'
 '<p><strong>Bei knappem Platz:</strong> Passt der lange Regular nicht in den Pfeifenkopf oder das '
 'Paper, ist der Short die naheliegende Alternative bei gleichem Durchmesser.</p>',
 '<p><strong>Kompakter als der Regular:</strong> Der <strong>PURIZE Regular Short 9mm</strong> hat '
 'denselben Durchmesser wie der Regular, ist mit 27 mm aber rund 8 mm kürzer. Steinkohle-Aktivkohle, '
 'Keramikkappen beidseitig, 50 Stück im Beutel, hergestellt in Deutschland.</p>',
 "PURIZE Regular Short 9 mm Aktivkohlefilter | Hanfjack",
 "PURIZE Regular Short, 9 mm bei 27 mm Länge: rund 8 mm kürzer als der Regular, gleicher Durchmesser. "
 "Steinkohle-Aktivkohle, 50 Stück im Beutel, Deutschland."))

# ---------------------------------------------------------- Big Size 14 mm
out.append(bauen(14343,
 "PURIZE Aktivkohlefilter Big Size 14mm – 3 Stück",
 "PURIZE Big Size 14mm 3 Stück",
 '<p><strong>PURIZE Aktivkohlefilter Big Size 14mm</strong> sind mit 14 mm Durchmesser und 40 mm Länge '
 'die größten Filter im PURIZE-Sortiment — gebaut für breite Blunts und dicke Konstruktionen, bei denen '
 'ein Slim- oder Regular-Filter zu schmal wäre.</p>'
 '<p>Diese Dreierpackung ist die kleinste verfügbare Menge — zum Testen, ob der große Durchmesser zu '
 'deiner Drehweise passt.</p>'
 '<h2>Der größte Filter der Reihe</h2>'
 '<p>14 mm ist mehr als das Doppelte des XTRA Slim. Für breite Papers, Blunt-Wraps oder Konstruktionen '
 'mit viel Volumen sitzt ein Filter dieser Größe stabiler als jeder schmalere.</p>'
 '<h2>Drei Stück zum Ausprobieren</h2>'
 '<p>Bevor du eine größere Packung kaufst, zeigt die Dreierpackung, ob dir Durchmesser und Länge liegen. '
 'Bei so wenigen Filtern lohnt sich das Ausprobieren, ohne gleich viel zu investieren.</p>'
 '<h2>Was den Big Size ausmacht</h2><ul>'
 '<li><strong>14 mm:</strong> Der größte Durchmesser im PURIZE-Sortiment.</li>'
 '<li><strong>40 mm Länge:</strong> Für breite und voluminöse Konstruktionen.</li>'
 '<li><strong>Keramik an beiden Enden:</strong> Keine Einbaurichtung.</li>'
 '<li><strong>Aktivkohle aus Kokosnussschalen.</strong></li>'
 '<li><strong>Hergestellt in Deutschland.</strong></li></ul>'
 + (TABELLE % "".join([zeile("Filtertyp","PURIZE Big Size, Aktivkohlefilter"), zeile("Durchmesser","14 mm"),
    zeile("Länge","40 mm"), zeile("Füllung","Aktivkohle aus Kokosnussschalen"),
    zeile("Endkappen","beidseitig Keramik"), zeile("Inhalt","3 Stück"), zeile("Herstellung","Deutschland")])) +
 '<h2>Praxistipps</h2>'
 '<p><strong>Erst die kleine Packung:</strong> Bei diesem Durchmesser lohnt sich das Ausprobieren, bevor '
 'du die größere Packung kaufst.</p>',
 '<p><strong>Zum Ausprobieren:</strong> Der <strong>PURIZE Big Size 14mm</strong> ist der größte Filter '
 'der Reihe — 14 mm Durchmesser, 40 mm Länge, für breite Blunts. Diese Dreierpackung ist die kleinste '
 'verfügbare Menge. Keramikkappen beidseitig, hergestellt in Deutschland.</p>',
 "PURIZE Big Size 14 mm Aktivkohlefilter, 3er | Hanfjack",
 "PURIZE Big Size 14 mm, 40 mm lang: größter PURIZE-Filter, für breite Blunts. Diese 3er-Packung zum "
 "Ausprobieren, Keramikkappen beidseitig, Deutschland."))

out.append(bauen(13698,
 "PURIZE Aktivkohlefilter Big Size 14mm – 7 Stück",
 "PURIZE Big Size 14mm 7 Stück",
 '<p><strong>PURIZE Aktivkohlefilter Big Size 14mm</strong> sind mit 14 mm Durchmesser und 40 mm Länge '
 'die größten Filter im PURIZE-Sortiment — gebaut für breite Blunts und dicke Konstruktionen.</p>'
 '<p>Die Siebenerpackung ist die Standardgröße für alle, die wissen, dass der große Durchmesser zu ihrer '
 'Drehweise passt.</p>'
 '<h2>Der größte Filter der Reihe</h2>'
 '<p>14 mm ist mehr als das Doppelte des XTRA Slim. Für breite Papers, Blunt-Wraps oder Konstruktionen '
 'mit viel Volumen sitzt ein Filter dieser Größe stabiler als jeder schmalere.</p>'
 '<h2>Sieben Stück für den laufenden Bedarf</h2>'
 '<p>Verglichen mit der Dreierpackung reicht die Siebenerpackung für mehrere Wochen, ohne dass gleich '
 'eine große Menge auf Vorrat liegt. Bei dieser Größe wird der Filter ohnehin nicht jeden Tag gebraucht.</p>'
 '<h2>Was den Big Size ausmacht</h2><ul>'
 '<li><strong>14 mm:</strong> Der größte Durchmesser im PURIZE-Sortiment.</li>'
 '<li><strong>40 mm Länge:</strong> Für breite und voluminöse Konstruktionen.</li>'
 '<li><strong>Keramik an beiden Enden:</strong> Keine Einbaurichtung.</li>'
 '<li><strong>Aktivkohle aus Kokosnussschalen.</strong></li>'
 '<li><strong>Hergestellt in Deutschland.</strong></li></ul>'
 + (TABELLE % "".join([zeile("Filtertyp","PURIZE Big Size, Aktivkohlefilter"), zeile("Durchmesser","14 mm"),
    zeile("Länge","40 mm"), zeile("Füllung","Aktivkohle aus Kokosnussschalen"),
    zeile("Endkappen","beidseitig Keramik"), zeile("Inhalt","7 Stück"), zeile("Herstellung","Deutschland")])) +
 '<h2>Praxistipps</h2>'
 '<p><strong>Für regelmäßigen Gebrauch:</strong> Wer den großen Durchmesser öfter braucht, ist mit '
 'sieben Stück besser bedient als mit der Dreierpackung.</p>',
 '<p><strong>Für regelmäßigen Gebrauch:</strong> Der <strong>PURIZE Big Size 14mm</strong> — größter '
 'Filter der Reihe, 14 mm Durchmesser, 40 mm Länge — kommt hier als Siebenerpackung. Keramikkappen '
 'beidseitig, Kokosnuss-Aktivkohle, hergestellt in Deutschland.</p>',
 "PURIZE Big Size 14 mm Aktivkohlefilter, 7er | Hanfjack",
 "PURIZE Big Size 14 mm, 40 mm lang: größter PURIZE-Filter, für breite Blunts. 7 Stück, Keramikkappen "
 "beidseitig, Kokosnuss-Aktivkohle, Deutschland."))

# ------------------------------------------------------------------ Conical
out.append(bauen(13387,
 "PURIZE Aktivkohlefilter Conical – konisch – 50 Stück",
 "PURIZE Conical konisch",
 '<p><strong>PURIZE Aktivkohlefilter Conical</strong> laufen von 5,9 mm auf 5,4 mm zu — eine Verjüngung, '
 'die genau der Form eines gerollten Cones entspricht.</p>'
 '<p>Ein zylindrischer Filter hat im Cone keinen Anschlag und rutscht beim Drehen leicht durch. Die '
 'konische Form verkeilt sich stattdessen im sich verjüngenden Papier.</p>'
 '<h2>Für Cones gemacht</h2>'
 '<p>Die Verjüngung von 5,9 auf 5,4 mm folgt der natürlichen Form eines Cones nach. Der Filter sitzt '
 'dadurch fest, ohne dass du beim Drehen nachschieben oder das Paper extra spannen musst.</p>'
 '<h2>Auch für normale Joints geeignet</h2>'
 '<p>Der Unterschied zum XTRA Slim liegt allein in der Form, nicht im Durchmesser am breiten Ende — '
 'auch in geraden Selbstgedrehten funktioniert der Conical, sein eigentlicher Vorteil zeigt sich aber '
 'im Cone.</p>'
 '<h2>Was den Conical ausmacht</h2><ul>'
 '<li><strong>5,9 → 5,4 mm konisch:</strong> Passt sich der Cone-Form an.</li>'
 '<li><strong>Keramik an beiden Enden:</strong> Keine Einbaurichtung.</li>'
 '<li><strong>Aktivkohle aus Kokosnussschalen.</strong></li>'
 '<li><strong>ca. 27 mm Länge.</strong></li>'
 '<li><strong>Hergestellt in Deutschland.</strong></li></ul>'
 + (TABELLE % "".join([zeile("Filtertyp","PURIZE Conical, konischer Aktivkohlefilter"), zeile("Durchmesser","5,9 → 5,4 mm"),
    zeile("Länge","ca. 27 mm"), zeile("Füllung","Aktivkohle aus Kokosnussschalen"),
    zeile("Endkappen","beidseitig Keramik"), zeile("Inhalt","50 Stück im Beutel"), zeile("Herstellung","Deutschland")])) +
 '<h2>Praxistipps</h2>'
 '<p><strong>Verjüngung ausnutzen:</strong> Leg den Filter zuerst ins Papier und rolle von dort — die '
 'Form zieht sich beim Zudrehen von selbst fest.</p>',
 '<p><strong>Für Cones geformt:</strong> Der <strong>PURIZE Conical</strong> verjüngt sich von 5,9 auf '
 '5,4 mm und folgt damit der Form eines Cones. Keramikkappen beidseitig, Kokosnuss-Aktivkohle, 50 Stück '
 'im Beutel, hergestellt in Deutschland.</p>',
 "PURIZE Conical konisch, 50 Stück | Hanfjack",
 "PURIZE Conical: 5,9 auf 5,4 mm konisch zulaufend, passend für Cones. Keramikkappen beidseitig, "
 "Kokosnuss-Aktivkohle. 50 Stück, Deutschland."))

out.append(bauen(13381,
 "PURIZE Aktivkohlefilter Conical – konisch 10 Gläser mit je 120 Filter",
 "PURIZE Conical 10 Gläser",
 '<p><strong>PURIZE Aktivkohlefilter Conical</strong> laufen von 5,9 mm auf 5,4 mm zu — passend zur '
 'Form eines Cones. Diese Ausführung kommt als Zehnergebinde: 10 Gläser mit je 120 Filtern, insgesamt '
 '1.200 Stück.</p>'
 '<p>Wie bei den anderen Zehnergebinden im PURIZE-Sortiment ist das eine Menge für Weitergabe und '
 'Verkauf, nicht für den alleinigen Eigenbedarf.</p>'
 '<h2>Für Cones gemacht</h2>'
 '<p>Die Verjüngung von 5,9 auf 5,4 mm folgt der natürlichen Form eines Cones nach und verhindert das '
 'Durchrutschen, das bei geraden Filtern in dieser Papierform auftritt.</p>'
 '<h2>Warum als Zehnergebinde</h2>'
 '<p>Zehn einzeln verschlossene Gläser statt eines großen Behälters: Neun bleiben dicht, während eines '
 'in Gebrauch ist — praktisch für Weiterverkauf oder Verteilung an mehrere Personen.</p>'
 '<h2>Was den Conical ausmacht</h2><ul>'
 '<li><strong>5,9 → 5,4 mm konisch:</strong> Passt sich der Cone-Form an.</li>'
 '<li><strong>10 Gläser à 120 Filter:</strong> 1.200 Stück, einzeln verschlossen.</li>'
 '<li><strong>Keramik an beiden Enden:</strong> Keine Einbaurichtung.</li>'
 '<li><strong>Aktivkohle aus Kokosnussschalen.</strong></li>'
 '<li><strong>Hergestellt in Deutschland.</strong></li></ul>'
 + (TABELLE % "".join([zeile("Filtertyp","PURIZE Conical, konischer Aktivkohlefilter"), zeile("Durchmesser","5,9 → 5,4 mm"),
    zeile("Füllung","Aktivkohle aus Kokosnussschalen"), zeile("Endkappen","beidseitig Keramik"),
    zeile("Inhalt","10 Gläser à 120 Filter (1.200 Stück)"), zeile("Herstellung","Deutschland")])) +
 '<h2>Praxistipps</h2>'
 '<p><strong>Für Weitergabe geeignet:</strong> Jedes Glas lässt sich einzeln abgeben, ohne die übrigen '
 'zu öffnen.</p>',
 '<p><strong>Zehnergebinde für Cones:</strong> Der <strong>PURIZE Conical</strong> — 5,9 auf 5,4 mm '
 'konisch — kommt hier als 10 Gläser à 120 Filter (1.200 Stück gesamt), einzeln verschlossen. '
 'Keramikkappen beidseitig, hergestellt in Deutschland.</p>',
 "PURIZE Conical, 10 Gläser à 120 | Hanfjack",
 "PURIZE Conical als Zehnergebinde: 10 Gläser à 120 Filter, konisch für Cones, einzeln verschlossen. "
 "Keramikkappen beidseitig, Deutschland."))

# ------------------------------------------------------------- Variety Bag
out.append(bauen(14389,
 "PURIZE Aktivkohlefilter Variety Bag",
 "PURIZE Variety Bag",
 '<p><strong>PURIZE Aktivkohlefilter Variety Bag</strong> enthält 14 Filter in 7 Formaten — je 2 Stück '
 'XTRA Slim (6 mm), XTRA Slim Long, Slim (7 mm), Regular (9 mm), Regular Short, Super Slim (5 mm) und '
 'Conical.</p>'
 '<p>Statt sieben einzelne Packungen zu kaufen, um herauszufinden, welcher Durchmesser und welche Länge '
 'passen, probierst du hier alle sieben in einem Beutel durch.</p>'
 '<h2>Die ganze Formatbreite auf einmal</h2>'
 '<p>Zwischen 5,0 mm und 9 mm liegen bei PURIZE mehrere Durchmesser, dazu die konische Form für Cones. '
 'Welcher davon zum eigenen Paper und zur eigenen Drehweise passt, lässt sich am zuverlässigsten '
 'ausprobieren statt vorher zu berechnen.</p>'
 '<h2>Wie du den Bag nutzt</h2>'
 '<p>Zieh dir über mehrere Sessions je einen Filter jedes Formats — am besten mit demselben Paper, damit '
 'der Unterschied nur am Filter liegt. Danach weißt du, welche Packung sich als Nächstes lohnt.</p>'
 '<h2>Enthaltene Formate</h2><ul>'
 '<li><strong>XTRA Slim, 6 mm:</strong> Der Standardfilter der Reihe.</li>'
 '<li><strong>XTRA Slim Long:</strong> Wie XTRA Slim, mit mehr Länge.</li>'
 '<li><strong>Slim, 7 mm:</strong> Zwischengröße zwischen XTRA Slim und Regular.</li>'
 '<li><strong>Regular, 9 mm:</strong> Für dicke Joints, Blunts und Pfeifen.</li>'
 '<li><strong>Regular Short:</strong> Wie Regular, rund 8 mm kürzer.</li>'
 '<li><strong>Super Slim, 5 mm:</strong> Der schmalste Filter im Sortiment.</li>'
 '<li><strong>Conical:</strong> Konisch zulaufend, passend für Cones.</li></ul>'
 + (TABELLE % "".join([zeile("Inhalt","14 Filter, 7 Formate à 2 Stück"), zeile("Endkappen","beidseitig Keramik"),
    zeile("Füllung","Aktivkohle aus Kokosnussschalen"), zeile("Herstellung","Deutschland")])) +
 '<h2>Praxistipps</h2>'
 '<p><strong>Ein Format pro Session:</strong> So lässt sich der Unterschied am klarsten spüren, statt '
 'mehrere Formate durcheinander zu probieren.</p>',
 '<p><strong>Alle Formate in einem Beutel:</strong> Der <strong>PURIZE Variety Bag</strong> enthält '
 '14 Filter in 7 Formaten — je 2 Stück XTRA Slim, XTRA Slim Long, Slim, Regular, Regular Short, Super '
 'Slim und Conical. Kokosnuss-Aktivkohle, Keramikkappen, hergestellt in Deutschland.</p>',
 "PURIZE Variety Bag – 7 Filterformate testen | Hanfjack",
 "PURIZE Variety Bag: 14 Filter in 7 Formaten (XTRA Slim, Long, Slim, Regular, Regular Short, "
 "Super Slim, Conical) zum Testen. Kokosnuss-Aktivkohle."))

json.dump(out, open('purize_rest.json','w'), ensure_ascii=False, indent=1)

print("PURIZE Rest erzeugt: %d" % len(out))
print("Längenverstöße:")
for v in VERSTOESSE: print("  #%s %s: %d Zeichen -> %s" % v)
if not VERSTOESSE: print("  keine")
for k in ('seo_title','seo_desc','desc','short'):
    w = [o[k] for o in out]
    doppelt = len(w) - len(set(w))
    print("  %-10s eindeutig: %s%s" % (k, doppelt == 0, "" if doppelt == 0 else "  (%d Dubletten)" % doppelt))
paare = sorted((difflib.SequenceMatcher(None,a['desc'],b['desc']).ratio(), a['id'], b['id'])
               for a,b in itertools.combinations(out,2))
print("  Ähnlichkeit: max %.2f (%s/%s) · Median %.2f · min %.2f"
      % (paare[-1][0], paare[-1][1], paare[-1][2], paare[len(paare)//2][0], paare[0][0]))
