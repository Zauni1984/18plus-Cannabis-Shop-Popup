# -*- coding: utf-8 -*-
"""Letzter Block: CTIP, Gizeh, Granny's Weed (6), Hybrid Supreme (4), Smoking (2).
14 Produkte, 5 Marken — jede mit ihrer eigenen, aus den Produktdaten belegten Abgrenzung."""
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

# ----------------------------------------------------------------- CTIP
out.append(bauen(14414,
 "CTIP Aktivkohlefilter konisch Ø 6-7mm – 25 Stück",
 "CTIP Aktivkohlefilter konisch",
 '<p><strong>CTIP Aktivkohlefilter konisch Ø 6–7 mm</strong> lösen ein Problem, das jeder Selbstdreher '
 'kennt: Ein gerader Filter hat im Papier keinen Anschlag. Er rutscht beim Rollen weg, und nach ein paar '
 'Minuten wird der Zug ungleichmäßig. Die konische Form verkeilt sich dagegen im Papier, sobald du '
 'zudrehst — 6 mm am Mundstück, 7 mm zur Glutseite hin.</p>'
 '<p>Dazu kommt eine Hülse aus Aluminium statt Papier. Sie hält Druck aus, ohne einzuknicken: Der Filter '
 'übersteht Hosentasche und Rucksack und behält seine Form. Die Aktivkohle im Inneren kühlt den Rauch auf '
 'dem Weg zum Mundstück ab und hält Krümel zurück, die sonst im Mund landen.</p>'
 '<h2>Warum die konische Form den Unterschied macht</h2>'
 '<p>Die Verjüngung greift im Papier, sobald du zudrehst — kein Nachschieben, kein Durchrutschen. Der '
 'Zugwiderstand bleibt dadurch vom ersten bis zum letzten Zug konstant, statt gegen Ende lockerer zu '
 'werden.</p>'
 '<h2>Was die Aluminiumhülse bringt</h2>'
 '<p>Anders als eine Papierhülse knickt Aluminium unter Druck nicht ein. Wer den Filter lose in die '
 'Tasche steckt statt ihn im Etui zu tragen, merkt den Unterschied direkt beim nächsten Drehen.</p>'
 '<h2>Eigenschaften</h2><ul>'
 '<li><strong>Konische Form:</strong> 6 mm am Mundstück, 7 mm zur Glutseite.</li>'
 '<li><strong>Aluminiumhülse:</strong> Druckfest, behält ihre Form auch lose in der Tasche.</li>'
 '<li><strong>Aktivkohle:</strong> Kühlt den Rauch, hält Krümel zurück.</li>'
 '<li><strong>26 mm Länge:</strong> Passt in Longpaper, King Size und vorgedrehte Cones.</li>'
 '<li><strong>25 Stück im wiederverschließbaren Beutel.</strong></li></ul>'
 + (TABELLE % "".join([zeile("Filtertyp","Aktivkohlefilter, konisch"), zeile("Durchmesser","6 mm (Mundstück) bis 7 mm (Glutseite)"),
    zeile("Länge","26 mm"), zeile("Hülse","Aluminium"), zeile("Füllung","Aktivkohle"),
    zeile("Inhalt","25 Stück im Beutel")])) +
 '<h2>Praxistipps</h2>'
 '<p><strong>Richtung beachten:</strong> Die schmale 6-mm-Seite gehört ans Mundende, die breite zeigt '
 'zum Kraut. Andersherum verliert die Form ihren Zweck.</p>'
 '<p><strong>Beim Drehen ansetzen:</strong> Leg den Filter zuerst ins Papier und rolle von dort aus — '
 'durch die Verjüngung zieht sich das Papier beim Zudrehen von selbst fest.</p>',
 '<p><strong>Rutscht nicht mehr weg:</strong> Die <strong>CTIP Aktivkohlefilter konisch Ø 6–7 mm</strong> '
 'verkeilen sich durch ihre Kegelform im Papier und halten den Zug gleichmäßig. Die Aluminiumhülse macht '
 'sie druckfest, die Aktivkohle kühlt den Rauch. 26 mm Länge, 25 Stück im Beutel.</p>',
 "CTIP Aktivkohlefilter konisch 6-7 mm | Hanfjack",
 "CTIP Aktivkohlefilter konisch: 6 mm Mundstück, 7 mm Glutseite, Aluminiumhülse, 26 mm lang. Verkeilt "
 "sich im Papier, kühlt den Rauch. 25 Stück im Beutel."))

# ----------------------------------------------------------------- Gizeh
out.append(bauen(525,
 "Gizeh Aktivfilter Kokoskohle 6mm, 34 Filter – 1 Packung",
 "Gizeh Aktivfilter Kokoskohle",
 '<p><strong>Gizeh Aktivfilter Kokoskohle 6 mm</strong> sind mehrfach verwendbar — nach dem Rauchen '
 'lässt sich der Filter ausklopfen, trocknen und erneut einsetzen, statt ihn nach einem Joint '
 'wegzuwerfen.</p>'
 '<p>Beide Enden tragen dieselbe Keramikkappe mit sieben Löchern. Das hält Krümel im Filter zurück und '
 'gibt dem Zug einen gleichmäßigen Widerstand, ohne dass eine Seite als Mundstück markiert werden muss.</p>'
 '<h2>Warum sich Wiederverwenden lohnt</h2>'
 '<p>Ein Filter, den du mehrmals einsetzt, muss nicht bei jedem Joint neu aus der Packung kommen. Nach '
 'dem Ausklopfen und kurzem Trocknen ist er wieder einsatzbereit — bei 34 Filtern in der Packung reicht '
 'das entsprechend länger als bei Einwegvarianten gleicher Stückzahl.</p>'
 '<h2>Sieben Löcher statt einem</h2>'
 '<p>Die Keramikkappen sind nicht einfach durchbohrt, sondern mit sieben kleinen Öffnungen versehen. Das '
 'verteilt den Luftstrom auf mehrere Wege statt auf einen einzigen — der Zug bleibt dadurch auch dann '
 'gleichmäßig, wenn sich ein Teil der Kohle zusetzt.</p>'
 '<h2>Eigenschaften</h2><ul>'
 '<li><strong>Mehrfach verwendbar:</strong> Ausklopfen, trocknen, erneut einsetzen.</li>'
 '<li><strong>6 mm:</strong> Passt in King-Size-Papers und dünne Selbstgedrehte.</li>'
 '<li><strong>Sieben Löcher je Kappe:</strong> Verteilter statt punktueller Luftstrom.</li>'
 '<li><strong>Keramik an beiden Enden:</strong> Keine Einbaurichtung.</li>'
 '<li><strong>Aktivkohle aus Kokosnussschalen.</strong></li>'
 '<li><strong>Hergestellt in Deutschland.</strong></li></ul>'
 + (TABELLE % "".join([zeile("Filtertyp","Aktivkohlefilter, mehrfach verwendbar"), zeile("Durchmesser","6 mm"),
    zeile("Länge","ca. 27 mm"), zeile("Füllung","Aktivkohle aus Kokosnussschalen"),
    zeile("Endkappen","beidseitig Keramik, je 7 Löcher"), zeile("Inhalt","ca. 34 Filter"),
    zeile("Herstellung","Deutschland")])) +
 '<h2>Praxistipps</h2>'
 '<p><strong>Zwischen den Sessions trocknen lassen:</strong> Ein feuchter Filter setzt sich schneller '
 'zu. Ein paar Stunden Pause reichen meist, bevor er wieder einsatzbereit ist.</p>',
 '<p><strong>Mehrfach verwendbar:</strong> Die <strong>Gizeh Aktivfilter Kokoskohle 6mm</strong> lassen '
 'sich ausklopfen, trocknen und erneut einsetzen. Beidseitige Keramikkappen mit je sieben Löchern, '
 'Aktivkohle aus Kokosnussschalen. Ca. 34 Filter je Packung, hergestellt in Deutschland.</p>',
 "Gizeh Aktivfilter Kokoskohle 6 mm, 34 Stück | Hanfjack",
 "Gizeh Aktivfilter aus Kokosnuss-Aktivkohle, 6 mm: mehrfach verwendbar, Keramikkappen mit je 7 Löchern. "
 "Ca. 34 Filter, hergestellt in Deutschland."))

# --------------------------------------------------- Granny's Weed: klassisch symmetrisch
out.append(bauen(21788,
 "Granny´s Weed Aktivkohlefilter 7mm blau vegan, 50 Stück",
 "Granny's Weed Aktivkohlefilter Blau",
 '<p><strong>Granny\'s Weed Aktivkohlefilter 7mm Blau</strong> tragen an beiden Enden dieselbe '
 'Keramikkappe — es gibt keine Seite, die zwingend zum Mundstück oder zur Glut zeigen muss.</p>'
 '<p>Das ist bei Filtern nicht selbstverständlich: Wer schon mal im Dunkeln oder in Eile gedreht hat, '
 'kennt den kurzen Moment des Überlegens, welche Seite jetzt welche ist. Bei einem symmetrischen Filter '
 'entfällt das.</p>'
 '<h2>Warum die Symmetrie praktisch ist</h2>'
 '<p>Du nimmst den Filter blind aus dem Beutel, drehst ihn ein, fertig — ohne hinzusehen, welches Ende '
 'wohin gehört. Bei schlechtem Licht oder wenn es schnell gehen muss, ist das der eigentliche Vorteil '
 'gegenüber Filtern mit fester Einbaurichtung.</p>'
 '<h2>Blau als Erkennungsfarbe</h2>'
 '<p>Das kräftige Blau hebt sich von den meisten anderen Filterfarben ab — praktisch, wenn du in einer '
 'Runde mehrere Farben im Umlauf hast und deinen eigenen Joint wiederfinden willst.</p>'
 '<h2>Eigenschaften</h2><ul>'
 '<li><strong>Beidseitig Keramik:</strong> Keine Einbaurichtung, blind einsetzbar.</li>'
 '<li><strong>6,9 mm (≈ 7 mm):</strong> Passt in Longpaper, Cones und Blunts.</li>'
 '<li><strong>26,5 mm Länge:</strong> Stabiler Sitz im Papier.</li>'
 '<li><strong>Vegane Kokosnuss-Aktivkohle.</strong></li>'
 '<li><strong>Zip-Standbeutel:</strong> Hält die Filter trocken.</li></ul>'
 + (TABELLE % "".join([zeile("Filtertyp","Aktivkohlefilter, symmetrisch"), zeile("Durchmesser","6,9 mm (≈ 7 mm)"),
    zeile("Länge","26,5 mm"), zeile("Füllung","vegane Kokosnuss-Aktivkohle"), zeile("Farbe","Blau"),
    zeile("Inhalt","50 Stück im Zip-Standbeutel")])) +
 '<h2>Praxistipps</h2>'
 '<p><strong>Beutel nach der Entnahme zudrücken:</strong> Aktivkohle zieht Luftfeuchtigkeit — ein '
 'zugedrückter Zip hält die restlichen Filter trocken.</p>',
 '<p><strong>Blind einsetzbar:</strong> Die <strong>Granny\'s Weed Aktivkohlefilter 7mm Blau</strong> '
 'tragen an beiden Enden dieselbe Keramikkappe — keine Einbaurichtung. Vegane Kokosnuss-Aktivkohle, '
 '26,5 mm Länge, 50 Stück im Zip-Standbeutel.</p>',
 "Granny's Weed Aktivkohlefilter 7 mm Blau | Hanfjack",
 "Granny's Weed Aktivkohlefilter Blau, 6,9 mm: beidseitig Keramik, keine Einbaurichtung, vegane "
 "Kokosnuss-Aktivkohle. 50 Stück im Zip-Standbeutel."))

out.append(bauen(21798,
 "Granny´s Weed Aktivkohlefilter Premium schwarz 6mm, 50 Stück",
 "Granny's Weed Aktivkohlefilter Schwarz",
 '<p><strong>Granny\'s Weed Aktivkohlefilter Premium Schwarz</strong> sind die dunkle Variante der '
 'klassischen, symmetrisch aufgebauten Filterlinie — beide Enden tragen dieselbe Keramikkappe.</p>'
 '<p>Schwarz ist im Sortiment die Farbe, der man Gebrauchsspuren am wenigsten ansieht: Was bei hellen '
 'Kappen nach einigen Zügen als Verfärbung auffällt, bleibt hier unsichtbar.</p>'
 '<h2>Warum Schwarz sich anders trägt als helle Farben</h2>'
 '<p>Wer einen Joint zwischendurch ablegt und später weiterraucht, sieht bei hellen Filtern schneller, '
 'dass sie schon in Gebrauch waren. Bei Schwarz fällt das nicht auf — ein rein praktischer Unterschied, '
 'kein Qualitätsmerkmal.</p>'
 '<h2>Symmetrisch wie die ganze Reihe</h2>'
 '<p>Wie bei der blauen Variante gibt es keine feste Einbaurichtung — du setzt den Filter ein, wie du '
 'ihn greifst.</p>'
 '<h2>Eigenschaften</h2><ul>'
 '<li><strong>Beidseitig Keramik:</strong> Keine Einbaurichtung.</li>'
 '<li><strong>5,9 mm:</strong> Slim-Format für dünne Selbstgedrehte.</li>'
 '<li><strong>ca. 27 mm Länge.</strong></li>'
 '<li><strong>Kokosnussschalen-Aktivkohle, vegan.</strong></li>'
 '<li><strong>Mattes Schwarz:</strong> Zeigt Gebrauchsspuren kaum.</li></ul>'
 + (TABELLE % "".join([zeile("Filtertyp","Aktivkohlefilter, symmetrisch"), zeile("Durchmesser","5,9 mm"),
    zeile("Länge","ca. 27 mm"), zeile("Füllung","Kokosnussschalen-Aktivkohle, vegan"), zeile("Farbe","Schwarz, matt"),
    zeile("Inhalt","50 Stück im Beutel")])) +
 '<h2>Praxistipps</h2>'
 '<p><strong>Für Joints, die abgelegt werden:</strong> Wer nicht in einem Zug durchraucht, profitiert '
 'am meisten von der dunklen Farbe.</p>',
 '<p><strong>Zeigt keinen Gebrauch:</strong> Die <strong>Granny\'s Weed Aktivkohlefilter Premium '
 'Schwarz</strong> sind symmetrisch aufgebaut, beidseitig Keramik, 5,9 mm, vegane Kokosnuss-Aktivkohle. '
 '50 Stück im Beutel.</p>',
 "Granny's Weed Aktivkohlefilter Schwarz 6mm | Hanfjack",
 "Granny's Weed Premium Schwarz, 5,9 mm: beidseitig Keramik, vegane Kokosnuss-Aktivkohle, matte "
 "Oberfläche zeigt kaum Gebrauchsspuren. 50 Stück im Beutel."))

out.append(bauen(21795,
 "Granny´s Weed Aktivkohlefilter Premium 7mm weiß vegan, 100 Stück",
 "Granny's Weed Aktivkohlefilter Premium Weiß",
 '<p><strong>Granny\'s Weed Aktivkohlefilter Premium Weiß</strong> sind die 100er-Packung der '
 'symmetrischen Filterlinie — beidseitig Keramik, kein Ende ist als Mundstück festgelegt.</p>'
 '<p>Statt eines einfachen Beutels kommt diese Packung in einer wiederverschließbaren Magnetbox, die '
 'sich mit einem Klappen öffnet und schließt, statt jedes Mal einen Zip-Verschluss zu bedienen.</p>'
 '<h2>Die Magnetbox als Unterschied</h2>'
 '<p>Bei 100 Filtern wird die Box öfter geöffnet als bei einer kleinen Packung. Ein Magnetverschluss '
 'hält dabei genauso dicht wie ein Zip, lässt sich aber mit einer Hand bedienen und bricht nicht wie ein '
 'oft geöffneter Zip-Beutel.</p>'
 '<h2>Symmetrisch wie die ganze Reihe</h2>'
 '<p>Wie bei den anderen Farben der klassischen Linie tragen beide Enden dieselbe Keramikkappe — du '
 'setzt den Filter ein, wie du ihn greifst.</p>'
 '<h2>Eigenschaften</h2><ul>'
 '<li><strong>Beidseitig Keramik:</strong> Keine Einbaurichtung.</li>'
 '<li><strong>6,9 mm (≈ 7 mm), 26,5 mm Länge.</strong></li>'
 '<li><strong>Vegane Kokosnuss-Aktivkohle.</strong></li>'
 '<li><strong>Wiederverschließbare Magnetbox</strong> statt Beutel.</li>'
 '<li><strong>100 Stück.</strong></li></ul>'
 + (TABELLE % "".join([zeile("Filtertyp","Aktivkohlefilter, symmetrisch"), zeile("Durchmesser","6,9 mm (≈ 7 mm)"),
    zeile("Länge","26,5 mm"), zeile("Füllung","vegane Kokosnuss-Aktivkohle"), zeile("Farbe","Weiß"),
    zeile("Verpackung","wiederverschließbare Magnetbox"), zeile("Inhalt","100 Stück")])) +
 '<h2>Praxistipps</h2>'
 '<p><strong>Box statt Beutel:</strong> Bei häufigem Öffnen hält die Magnetbox länger dicht als ein '
 'vielfach genutzter Zip-Verschluss.</p>',
 '<p><strong>Im Magnetbox-Format:</strong> Die <strong>Granny\'s Weed Aktivkohlefilter Premium '
 'Weiß</strong> kommen in einer wiederverschließbaren Magnetbox statt im Beutel. Beidseitig Keramik, '
 'vegane Kokosnuss-Aktivkohle, 6,9 mm, 100 Stück.</p>',
 "Granny's Weed Premium Aktivkohlefilter Weiß | Hanfjack",
 "Granny's Weed Premium Weiß, 6,9 mm: beidseitig Keramik, vegane Kokosnuss-Aktivkohle, in "
 "wiederverschließbarer Magnetbox. 100 Stück."))

# --------------------------------------------------- Granny's Weed Hybrid Deluxe
out.append(bauen(21786,
 "Granny´s Weed Hybrid Aktivkohlefilter Deluxe 6mm, 50 Stück",
 "Granny's Weed Hybrid Deluxe",
 '<p><strong>Granny\'s Weed Hybrid Aktivkohlefilter Deluxe</strong> haben — anders als die klassische '
 'Linie derselben Marke — zwei unterschiedliche Enden: eine Keramikkappe zur Glutseite, einen '
 'Faserabschluss zum Mundstück.</p>'
 '<p>Das ist kein Nachteil gegenüber der symmetrischen Bauweise, sondern eine andere Lösung für dasselbe '
 'Problem: Aktivkohle setzt sich zu, wenn warmer Rauch auf einen kalten Filter trifft und Feuchtigkeit '
 'kondensiert. Die Keramikkappe hält die Hitze von der Kohle fern, der Faserabschluss fängt die '
 'Feuchtigkeit ab, bevor sie zurück in den Filter zieht.</p>'
 '<h2>Warum die Einbaurichtung hier wichtig ist</h2>'
 '<p>Anders als bei beidseitig-keramischen Filtern kommt es hier auf die Richtung an: Die Keramikkappe '
 'muss zur Glut zeigen, der Faserabschluss zum Mund. Andersherum eingesetzt verliert die Konstruktion '
 'ihren Zweck.</p>'
 '<h2>39 mm statt der üblichen 27 mm</h2>'
 '<p>Die Extralänge gibt beim Drehen mehr Fläche zum Greifen und hält gleichzeitig etwas mehr Abstand '
 'zur Glut als kürzere Filter.</p>'
 '<h2>Eigenschaften</h2><ul>'
 '<li><strong>Hybridbauweise:</strong> Keramik zur Glut, Faser zum Mund — feste Einbaurichtung.</li>'
 '<li><strong>6 mm, 39 mm Länge:</strong> Mehr Fläche zum Greifen beim Drehen.</li>'
 '<li><strong>Vegane Kokosnuss-Aktivkohle.</strong></li>'
 '<li><strong>Zip-Beutel, 50 Stück.</strong></li></ul>'
 + (TABELLE % "".join([zeile("Filtertyp","Hybrid-Aktivkohlefilter"), zeile("Durchmesser","6 mm"),
    zeile("Länge","39 mm"), zeile("Konstruktion","Keramikkappe (Glutseite) + Faserabschluss (Mundseite)"),
    zeile("Füllung","vegane Kokosnuss-Aktivkohle"), zeile("Inhalt","50 Stück im Zip-Beutel")])) +
 '<h2>Praxistipps</h2>'
 '<p><strong>Richtung merken:</strong> Weiße Keramikkappe zur Glut, Faserende zum Mund — bei diesem '
 'Filter anders als bei den meisten anderen der Marke.</p>'
 '<p><strong>Bei Kälte kurz vorwärmen:</strong> Ein kalter Filter kondensiert eher. Kurz in der Hand '
 'aufwärmen vor dem Anzünden hilft im Winter.</p>',
 '<p><strong>Feste Einbaurichtung, anderer Aufbau:</strong> Der <strong>Granny\'s Weed Hybrid Deluxe</strong> '
 'kombiniert eine Keramikkappe zur Glut mit einem Faserabschluss zum Mund — 39 mm Länge, vegane '
 'Kokosnuss-Aktivkohle. 50 Stück im Zip-Beutel.</p>',
 "Granny's Weed Hybrid Deluxe Aktivkohlefilter | Hanfjack",
 "Granny's Weed Hybrid Deluxe: Keramikkappe zur Glut, Faserabschluss zum Mund, 6 mm, 39 mm Länge. "
 "Vegane Kokosnuss-Aktivkohle, 50 Stück im Zip-Beutel."))

out.append(bauen(32013,
 "Granny´s Weed Hybrid Aktivkohlefilter Deluxe 6mm, 50 Stück x4",
 "Granny's Weed Hybrid Deluxe 4er-Set",
 '<p><strong>Granny\'s Weed Hybrid Aktivkohlefilter Deluxe – 50 Stück x4</strong> bündelt vier Packungen '
 'des Hybrid Deluxe zu einem Set — 200 Filter statt einer Einzelpackung.</p>'
 '<p>Der Filter selbst ist identisch mit der Einzelpackung: Keramikkappe zur Glutseite, Faserabschluss '
 'zum Mund, 6 mm Durchmesser, 39 mm Länge.</p>'
 '<h2>Warum als Viererset</h2>'
 '<p>Wer den Hybrid Deluxe bereits kennt und regelmäßig nachkauft, spart mit dem Set den wiederholten '
 'Einzelkauf. Die vier Packungen bleiben einzeln verschlossen, bis sie gebraucht werden.</p>'
 '<h2>Auf die Richtung achten</h2>'
 '<p>Wie bei der Einzelpackung gilt: Die Keramikkappe gehört zur Glutseite, der Faserabschluss zum '
 'Mund. Das unterscheidet den Hybrid Deluxe von den beidseitig-keramischen Filtern derselben Marke.</p>'
 '<h2>Eigenschaften</h2><ul>'
 '<li><strong>4 × 50 Stück:</strong> 200 Filter, vier einzeln verschlossene Packungen.</li>'
 '<li><strong>Hybridbauweise:</strong> Keramik zur Glut, Faser zum Mund.</li>'
 '<li><strong>6 mm, 39 mm Länge.</strong></li>'
 '<li><strong>Vegane Kokosnuss-Aktivkohle.</strong></li></ul>'
 + (TABELLE % "".join([zeile("Filtertyp","Hybrid-Aktivkohlefilter"), zeile("Durchmesser","6 mm"),
    zeile("Länge","39 mm"), zeile("Konstruktion","Keramikkappe (Glutseite) + Faserabschluss (Mundseite)"),
    zeile("Inhalt","4 Packungen à 50 Stück (200 gesamt)")])) +
 '<h2>Praxistipps</h2>'
 '<p><strong>Eine Packung zur Zeit öffnen:</strong> So bleiben die übrigen drei ungeöffnet und trocken, '
 'bis sie gebraucht werden.</p>',
 '<p><strong>Vier Packungen im Set:</strong> Der <strong>Granny\'s Weed Hybrid Deluxe</strong> — '
 'Keramikkappe zur Glut, Faserabschluss zum Mund — kommt hier als Viererset mit 200 Filtern gesamt, '
 'einzeln verpackt.</p>',
 "Granny's Weed Hybrid Deluxe 4er-Set, 200 Stück | Hanfjack",
 "Granny's Weed Hybrid Deluxe im Viererset: 4 × 50 Stück, 200 Filter gesamt. Keramikkappe zur Glut, "
 "Faserabschluss zum Mund, 6 mm."))

# --------------------------------------------------------- Granny's Weed Purpfeife
out.append(bauen(21792,
 "Granny´s Weed Premium Holzpfeife mit 9mm Aktivkohlefilter - Purpfeife",
 "Granny's Weed Holzpfeife",
 '<p><strong>Granny\'s Weed Premium Holzpfeife</strong> ist aus massivem Nussbaumholz gefertigt und '
 'kommt komplett ausgestattet: Mundstück, sechs 9-mm-Aktivkohlefilter, fünf Ersatzsiebe und eine '
 'Geschenkbox gehören zum Lieferumfang.</p>'
 '<p>Anders als bei Papier oder Glas bleibt bei einer Holzpfeife die Handhabung dieselbe wie bei jeder '
 'Pfeife: stopfen, Sieb einsetzen, Filter einlegen, anzünden.</p>'
 '<h2>Was im Lieferumfang steckt</h2>'
 '<p>Die Pfeife kommt nicht allein — sechs Aktivkohlefilter reichen für mehrere Sessions, bevor du '
 'nachkaufen musst, und fünf Ersatzsiebe decken den Verschleiß der ersten Zeit ab, ohne dass du sofort '
 'Zubehör bestellen musst.</p>'
 '<h2>Pflege und Reinigung</h2>'
 '<p>Nach der Session den Filter entnehmen, die Asche ausklopfen und mit einem Pfeifenreiniger sowie '
 'lauwarmem Wasser säubern. Für das Holz reicht gelegentliches Einreiben mit einem natürlichen Holzöl — '
 'so bleibt die Maserung geschmeidig, und die Pfeife entwickelt mit der Zeit eine eigene Patina.</p>'
 '<h2>Eigenschaften</h2><ul>'
 '<li><strong>Material:</strong> massives Nussbaumholz.</li>'
 '<li><strong>Länge:</strong> ca. 15 cm.</li>'
 '<li><strong>Filter:</strong> 9 mm Aktivkohlefilter, 6 Stück enthalten.</li>'
 '<li><strong>Lieferumfang:</strong> Pfeife, Mundstück, 6 Filter, 5 Ersatzsiebe, Geschenkbox.</li></ul>'
 + (TABELLE % "".join([zeile("Material","massives Nussbaumholz"), zeile("Länge","ca. 15 cm"),
    zeile("Filterdurchmesser","9 mm"), zeile("Lieferumfang","Pfeife, Mundstück, 6 Filter, 5 Siebe, Geschenkbox")])) +
 '<h2>Praxistipps</h2>'
 '<p><strong>Nach jeder Session reinigen:</strong> Rückstände lassen sich frisch leichter entfernen als '
 'nach dem Trocknen.</p>'
 '<p><strong>Holzöl in Maßen:</strong> Ein dünner Film genügt — zu viel Öl macht das Holz eher klebrig '
 'als geschmeidig.</p>',
 '<p><strong>Komplett ausgestattet:</strong> Die <strong>Granny\'s Weed Premium Holzpfeife</strong> aus '
 'Nussbaumholz kommt mit Mundstück, 6 Aktivkohlefiltern (9 mm), 5 Ersatzsieben und Geschenkbox. '
 'Ca. 15 cm lang.</p>',
 "Granny's Weed Premium Holzpfeife, 9mm Filter | Hanfjack",
 "Granny's Weed Holzpfeife aus Nussbaumholz, ca. 15 cm, mit Mundstück, 6 Aktivkohlefiltern (9 mm), "
 "5 Ersatzsieben und Geschenkbox."))

# --------------------------------------------------------------- Hybrid Supreme
out.append(bauen(13842,
 "Hybrid Supreme Aktivkohlefilter 250 Stück Pack weiß Ø 6,4 mm",
 "Hybrid Supreme 6,4mm 250 Stück",
 '<p><strong>Hybrid Supreme Aktivkohlefilter Ø 6,4 mm</strong> kombinieren Aktivkohle aus '
 'Kokosnussschalen mit Cellulose und Keramikkappen — drei Materialien statt der üblichen zwei.</p>'
 '<p>Entwickelt in Österreich, produziert in Europa. Diese 250er-Packung ist die Vorratsgröße der Reihe.</p>'
 '<h2>Drei Materialien in einem Filter</h2>'
 '<p>Die Aktivkohle bildet den Kern, die Cellulose gibt zusätzliche Struktur, die Keramikkappen halten '
 'beides zusammen. Diese Kombination unterscheidet Hybrid Supreme von reinen Aktivkohle- oder reinen '
 'Cellulosefiltern.</p>'
 '<h2>Die Vorratsgröße</h2>'
 '<p>250 Stück im Zip-Beutel reichen bei regelmäßigem Gebrauch mehrere Monate — für alle, die wissen, '
 'dass der 6,4-mm-Durchmesser zu ihrer Drehweise passt.</p>'
 '<h2>Eigenschaften</h2><ul>'
 '<li><strong>6,4 mm, 30 mm Länge.</strong></li>'
 '<li><strong>Aktivkohle + Cellulose + Keramikkappen.</strong></li>'
 '<li><strong>250 Stück im Zip-Beutel.</strong></li>'
 '<li><strong>Entwickelt in Österreich, produziert in Europa.</strong></li></ul>'
 + (TABELLE % "".join([zeile("Filtertyp","Hybrid-Aktivkohlefilter"), zeile("Durchmesser","6,4 mm"),
    zeile("Länge","30 mm"), zeile("Füllung","Aktivkohle (Kokosnuss) + Cellulose"),
    zeile("Endkappen","Keramik"), zeile("Inhalt","250 Stück im Zip-Beutel"), zeile("Herkunft","Entwicklung Österreich, Produktion Europa")])) +
 '<h2>Praxistipps</h2>'
 '<p><strong>Bei 250 Stück lohnt sich Umfüllen:</strong> Ein kleines Gefäß für unterwegs hält den '
 'großen Beutel geschlossen und trocken.</p>',
 '<p><strong>Drei Materialien:</strong> Der <strong>Hybrid Supreme Aktivkohlefilter Ø 6,4 mm</strong> '
 'kombiniert Aktivkohle, Cellulose und Keramikkappen. 30 mm Länge, 250 Stück im Zip-Beutel, entwickelt '
 'in Österreich.</p>',
 "Hybrid Supreme Aktivkohlefilter 6,4 mm, 250er | Hanfjack",
 "Hybrid Supreme, 6,4 mm: Aktivkohle, Cellulose und Keramikkappen kombiniert, 30 mm lang. 250 Stück im "
 "Zip-Beutel, entwickelt in Österreich."))

out.append(bauen(13836,
 "Hybrid Supreme Aktivkohlefilter 1000 Stück Glas Ø 6,4 mm",
 "Hybrid Supreme 6,4mm 1000 Stück",
 '<p><strong>Hybrid Supreme Aktivkohlefilter Ø 6,4 mm</strong> kombinieren Aktivkohle aus '
 'Kokosnussschalen mit Cellulose und Keramikkappen. Diese Ausführung kommt als Schraubglas mit 1000 '
 'Filtern.</p>'
 '<p>Bei dieser Menge ist das Glas kein Zubehör, sondern die Voraussetzung: Ein Beutel dieser Größe '
 'würde nach häufigem Öffnen durchfeuchten, das Glas dichtet über die gesamte Nutzungsdauer gleich gut.</p>'
 '<h2>Drei Materialien in einem Filter</h2>'
 '<p>Die Aktivkohle bildet den Kern, die Cellulose gibt zusätzliche Struktur, die Keramikkappen halten '
 'beides zusammen.</p>'
 '<h2>Warum als Glas statt als Beutel</h2>'
 '<p>Bei 1000 Stück wird der Behälter über Monate hinweg regelmäßig geöffnet. Ein Schraubglas hält dabei '
 'länger dicht als ein Zip-Beutel, dessen Verschluss sich mit jedem Öffnen etwas abnutzt.</p>'
 '<h2>Eigenschaften</h2><ul>'
 '<li><strong>6,4 mm.</strong></li>'
 '<li><strong>Aktivkohle + Cellulose + Keramikkappen.</strong></li>'
 '<li><strong>1000 Stück im Schraubglas.</strong></li>'
 '<li><strong>Entwickelt in Österreich, produziert in Europa.</strong></li></ul>'
 + (TABELLE % "".join([zeile("Filtertyp","Hybrid-Aktivkohlefilter"), zeile("Durchmesser","6,4 mm"),
    zeile("Füllung","Aktivkohle (Kokosnuss) + Cellulose"), zeile("Endkappen","Keramik"),
    zeile("Inhalt","1000 Stück im Schraubglas"), zeile("Herkunft","Entwicklung Österreich, Produktion Europa")])) +
 '<h2>Praxistipps</h2>'
 '<p><strong>Deckel jedes Mal zudrehen:</strong> Der Vorteil des Glases hängt am Verschluss — handfest '
 'genügt, aber eben immer.</p>',
 '<p><strong>Im Schraubglas:</strong> Der <strong>Hybrid Supreme Aktivkohlefilter Ø 6,4 mm</strong> — '
 'Aktivkohle, Cellulose und Keramikkappen kombiniert — kommt hier als 1000er-Glas, das über die ganze '
 'Nutzungsdauer dicht bleibt.</p>',
 "Hybrid Supreme Aktivkohlefilter 6,4 mm im Glas | Hanfjack",
 "Hybrid Supreme im 1000er-Schraubglas, 6,4 mm: Aktivkohle, Cellulose und Keramikkappen, hält über "
 "Monate dicht. Entwickelt in Österreich."))

out.append(bauen(14403,
 "Hybrid Supreme Aktivkohlefilter 33 Stück weiß Ø 6,4 mm mit 4m Endlospaper",
 "Hybrid Supreme mit Endlospapier",
 '<p><strong>Hybrid Supreme Aktivkohlefilter mit 4 m Endlospapier</strong> bündelt 33 Filter mit einer '
 '4 Meter langen, 45 mm breiten Papierrolle in einer Packung.</p>'
 '<p>Anders als vorgeschnittenes Papier lässt sich vom Endlospapier jede Länge selbst abreißen — von '
 'kurzen bis zu sehr langen Joints, ohne dass mehrere Papierformate im Haus sein müssen.</p>'
 '<h2>Was das Kombipaket löst</h2>'
 '<p>Wer Filter und Papier normalerweise getrennt kauft, hat mit diesem Set beides in einer Packung. Das '
 'ist vor allem dann praktisch, wenn du unterschiedlich lange Joints drehst — die Rollenlänge passt sich '
 'an, ein vorgeschnittenes Paper tut das nicht.</p>'
 '<h2>Die Filter selbst</h2>'
 '<p>Wie beim Rest der Reihe: Aktivkohle aus Kokosnussschalen, Cellulose, Keramikkappen, 6,4 mm '
 'Durchmesser, 30 mm Länge.</p>'
 '<h2>Eigenschaften</h2><ul>'
 '<li><strong>33 Hybridfilter, 6,4 mm, 30 mm Länge.</strong></li>'
 '<li><strong>4 m Endlospapier, 45 mm breit, ungebleicht.</strong></li>'
 '<li><strong>Selbst zuschneidbare Länge</strong> statt fester Papierformate.</li>'
 '<li><strong>Entwickelt in Österreich, produziert in Europa.</strong></li></ul>'
 + (TABELLE % "".join([zeile("Filter","33 Stück, 6,4 mm, 30 mm Länge"), zeile("Papier","4 m Endlospapier, 45 mm breit, ungebleicht"),
    zeile("Füllung Filter","Aktivkohle (Kokosnuss) + Cellulose"), zeile("Endkappen","Keramik"),
    zeile("Herkunft","Entwicklung Österreich, Produktion Europa")])) +
 '<h2>Praxistipps</h2>'
 '<p><strong>Länge vorher abmessen:</strong> 10 bis 20 cm sind ein gängiger Bereich — probier dich '
 'einmal durch, dann weißt du deine bevorzugte Länge.</p>',
 '<p><strong>Filter und Papier im Set:</strong> 33 Hybridfilter (6,4 mm) und 4 m Endlospapier (45 mm '
 'breit) in einer Packung — die Papierlänge bestimmst du beim Abreißen selbst.</p>',
 "Hybrid Supreme mit 4m Endlospapier, 33 Filter | Hanfjack",
 "Hybrid Supreme Kombipack: 33 Filter (6,4 mm) plus 4 m Endlospapier, 45 mm breit, ungebleicht. Länge "
 "selbst zuschneidbar."))

out.append(bauen(14404,
 "Hybrid Supreme Aktivkohlefilter 55 Stück Magenta Ø 6,4 mm",
 "Hybrid Supreme Magenta",
 '<p><strong>Hybrid Supreme Aktivkohlefilter Magenta</strong> ist die farbige Variante der Reihe — '
 'gleicher Aufbau aus Aktivkohle, Cellulose und Keramikkappen, nur in Magenta statt Weiß.</p>'
 '<p>Diese Packung fasst 55 Stück — eine Zwischengröße zwischen der 33er-Kombipackung und der '
 '250er-Vorratspackung.</p>'
 '<h2>Warum eine eigene Farbe</h2>'
 '<p>Magenta hebt sich von den meisten anderen Filterfarben im Sortiment ab. In einer Runde mit '
 'mehreren Filterfarben ist das der Ton, der am schnellsten wiedererkannt wird.</p>'
 '<h2>Der Aufbau bleibt gleich</h2>'
 '<p>Wie beim Rest der Hybrid-Supreme-Reihe: Aktivkohle aus Kokosnussschalen, Cellulose, Keramikkappen, '
 '6,4 mm Durchmesser.</p>'
 '<h2>Eigenschaften</h2><ul>'
 '<li><strong>6,4 mm, 55 Stück.</strong></li>'
 '<li><strong>Farbe:</strong> Magenta.</li>'
 '<li><strong>Aktivkohle + Cellulose + Keramikkappen.</strong></li>'
 '<li><strong>Entwickelt in Österreich, produziert in Europa.</strong></li></ul>'
 + (TABELLE % "".join([zeile("Filtertyp","Hybrid-Aktivkohlefilter"), zeile("Durchmesser","6,4 mm"),
    zeile("Füllung","Aktivkohle (Kokosnuss) + Cellulose"), zeile("Endkappen","Keramik"), zeile("Farbe","Magenta"),
    zeile("Inhalt","55 Stück"), zeile("Herkunft","Entwicklung Österreich, Produktion Europa")])) +
 '<h2>Praxistipps</h2>'
 '<p><strong>Als Erkennungsfarbe nutzen:</strong> In einer Runde mit mehreren Filterfarben lässt sich '
 'Magenta am schnellsten wiederfinden.</p>',
 '<p><strong>Farbige Variante:</strong> Der <strong>Hybrid Supreme Aktivkohlefilter Magenta</strong> — '
 '6,4 mm, Aktivkohle, Cellulose und Keramikkappen — kommt hier in Magenta statt Weiß. 55 Stück.</p>',
 "Hybrid Supreme Aktivkohlefilter Magenta, 55er | Hanfjack",
 "Hybrid Supreme in Magenta, 6,4 mm: Aktivkohle, Cellulose und Keramikkappen kombiniert, 55 Stück, "
 "entwickelt in Österreich."))

# ------------------------------------------------------------------- Smoking
out.append(bauen(14413,
 "Smoking White Aktivkohlefilter Slim 6mm - 30 Stück",
 "Smoking White Aktivkohlefilter",
 '<p><strong>Smoking White Aktivkohlefilter Slim 6mm</strong> ist die Filterlinie der Papermarke '
 'Smoking — 6 mm Durchmesser, reines Weiß, Keramikkappen an beiden Enden.</p>'
 '<p>Als 30er-Packung ist das die kleinere Menge im Sortiment — passend, wenn du erst ausprobieren '
 'willst, ob dir die Marke neben deinem gewohnten Filter liegt.</p>'
 '<h2>Wofür sich die 30er-Packung eignet</h2>'
 '<p>Dreißig Filter reichen für einen ersten Eindruck, ohne dass du dich gleich auf eine größere Menge '
 'festlegst. Der Beutel ist klein genug für die Jackentasche.</p>'
 '<h2>Symmetrisch aufgebaut</h2>'
 '<p>Beide Enden tragen dieselbe Keramikkappe — keine Einbaurichtung, du setzt den Filter ein, wie du '
 'ihn greifst.</p>'
 '<h2>Eigenschaften</h2><ul>'
 '<li><strong>6 mm, ca. 27–30 mm Länge.</strong></li>'
 '<li><strong>Beidseitig Keramik:</strong> Keine Einbaurichtung.</li>'
 '<li><strong>Kokosnuss-Aktivkohle.</strong></li>'
 '<li><strong>30 Stück im Beutel.</strong></li></ul>'
 + (TABELLE % "".join([zeile("Filtertyp","Aktivkohlefilter, symmetrisch"), zeile("Durchmesser","6 mm"),
    zeile("Länge","ca. 27–30 mm"), zeile("Füllung","Kokosnuss-Aktivkohle"), zeile("Farbe","Weiß"),
    zeile("Inhalt","30 Stück im Beutel")])) +
 '<h2>Praxistipps</h2>'
 '<p><strong>Zum Kennenlernen der Marke:</strong> Bei 30 Stück lohnt sich das Ausprobieren, bevor du '
 'eine größere Packung kaufst.</p>',
 '<p><strong>Zum Ausprobieren:</strong> Der <strong>Smoking White Aktivkohlefilter Slim 6mm</strong> '
 'ist beidseitig Keramik, keine Einbaurichtung, Kokosnuss-Aktivkohle. 30 Stück im Beutel.</p>',
 "Smoking White Aktivkohlefilter Slim 6mm | Hanfjack",
 "Smoking White Aktivkohlefilter, 6 mm: beidseitig Keramik, keine Einbaurichtung, Kokosnuss-Aktivkohle. "
 "30 Stück im Beutel."))

out.append(bauen(14412,
 "Smoking Brown Aktivkohlefilter Slim 6mm - 30 Stück",
 "Smoking Brown Aktivkohlefilter",
 '<p><strong>Smoking Brown Aktivkohlefilter Slim 6mm</strong> ist die braune Variante derselben '
 'Filterlinie wie Smoking White — 6 mm Durchmesser, Keramikkappen an beiden Enden, Kokosnuss-Aktivkohle.</p>'
 '<p>Der einzige Unterschied zur weißen Ausführung ist die Farbe der Keramikkappe.</p>'
 '<h2>Braun statt Weiß</h2>'
 '<p>Auf ungebleichtem Paper fällt Braun weniger auf als Weiß — der Filter fügt sich optisch näher in '
 'Naturpapier ein, statt sich sichtbar davon abzusetzen.</p>'
 '<h2>Symmetrisch aufgebaut</h2>'
 '<p>Wie bei der weißen Variante tragen beide Enden dieselbe Keramikkappe — keine Einbaurichtung.</p>'
 '<h2>Eigenschaften</h2><ul>'
 '<li><strong>6 mm, ca. 27–30 mm Länge.</strong></li>'
 '<li><strong>Beidseitig Keramik:</strong> Keine Einbaurichtung.</li>'
 '<li><strong>Kokosnuss-Aktivkohle.</strong></li>'
 '<li><strong>30 Stück im Beutel.</strong></li></ul>'
 + (TABELLE % "".join([zeile("Filtertyp","Aktivkohlefilter, symmetrisch"), zeile("Durchmesser","6 mm"),
    zeile("Länge","ca. 27–30 mm"), zeile("Füllung","Kokosnuss-Aktivkohle"), zeile("Farbe","Braun"),
    zeile("Inhalt","30 Stück im Beutel")])) +
 '<h2>Praxistipps</h2>'
 '<p><strong>Zu ungebleichtem Paper:</strong> Braun setzt sich von Naturpapier weniger ab als eine '
 'weiße Kappe.</p>',
 '<p><strong>Für ungebleichtes Paper:</strong> Der <strong>Smoking Brown Aktivkohlefilter Slim 6mm</strong> '
 'ist beidseitig Keramik in Braun, keine Einbaurichtung, Kokosnuss-Aktivkohle. 30 Stück im Beutel.</p>',
 "Smoking Brown Aktivkohlefilter Slim 6mm | Hanfjack",
 "Smoking Brown Aktivkohlefilter, 6 mm: beidseitig Keramik in Braun, keine Einbaurichtung, "
 "Kokosnuss-Aktivkohle. 30 Stück im Beutel."))

json.dump(out, open('sonstige.json','w'), ensure_ascii=False, indent=1)
print("Sonstige erzeugt: %d" % len(out))
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
