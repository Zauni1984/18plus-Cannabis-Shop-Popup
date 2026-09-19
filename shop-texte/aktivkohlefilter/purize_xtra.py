# -*- coding: utf-8 -*-
"""PURIZE XTRA Slim 6 mm (5,9 mm) — 41 Varianten.

Grundhaltung des Textes: Innen ist jede Variante identisch. Die Farbe ändert
nichts an der Leistung — das wird ausgesprochen statt beschwiegen, und jede
Farbe bekommt stattdessen einen echten, praktischen Unterschied.
Abgrenzung: Medusafilters läuft über den Hybridaufbau, Kailar über die
Cellulose-Kappen, PURIZE über Keramikkappen und das Format-Ökosystem.
"""
import json, itertools, difflib

# Editionen, deren Motiv ich nicht aus den Shopdaten belegen kann.
# Für die bleibt der Text bei Format und Filter, ohne ein Design zu erfinden.
UNGEPRUEFT = []

FARBEN = {
"Weiß": dict(
 p1=("Weiß ist die Ausgangsvariante, an der sich alle anderen messen: kein Pigment in der Keramik, "
     "kein Farbbezug zum Paper. Wer PURIZE zum ersten Mal kauft, kauft meistens diese."),
 h2="Die Variante ohne Farbentscheidung",
 p2=("In weißem Longpaper verschwindet der Filter fast vollständig — das ist für die einen der ganze "
     "Reiz und für die anderen der Grund, eine Farbe zu nehmen. Ehrlicherweise hat Weiß einen Nachteil: "
     "Gebrauchsspuren an der Keramikkappe sieht man hier am deutlichsten. Auf die Funktion hat das keinen "
     "Einfluss, auf die Optik nach der Hälfte des Joints schon."),
 bullet="<strong>Neutral zu jedem Paper:</strong> Kein Pigment, kein Farbbezug — der Standard der Reihe."),
"Blau": dict(
 p1=("Blau ist der einzige kühle Ton im Sortiment. Neben den warmen Farben und dem Naturton fällt er "
     "dadurch schon im Beutel auf, und im ungebleichten Paper entsteht der größte Farbabstand der Reihe."),
 h2="Der kühle Gegenpol",
 p2=("Braunes Paper und ein blauer Filter liegen auf dem Farbkreis fast gegenüber. Das Ergebnis ist der "
     "deutlichste Kontrast, den PURIZE anbietet — sichtbar auch dann, wenn der Joint auf dem Tisch liegt "
     "und man nur kurz hinsieht. Wer mehrere Filterfarben parallel im Umlauf hat, findet Blau am "
     "schnellsten wieder."),
 bullet="<strong>Kühler Ton:</strong> Größter Farbabstand zu ungebleichtem Paper."),
"Gelb": dict(
 p1=("Gelb ist der hellste gefärbte Ton der Reihe. Von allen Farben behält er bei wenig Licht am längsten "
     "seine Erkennbarkeit, weil Helligkeit im Dämmerlicht länger trägt als Farbigkeit."),
 h2="Die Farbe für schlechtes Licht",
 p2=("Wenn es dunkler wird, verliert das Auge zuerst die Farbwahrnehmung, dann erst die Helligkeit. "
     "Deshalb ist ein gelber Filter draußen am Abend noch zu erkennen, wenn Lila und Blau längst als "
     "Dunkelgrau erscheinen. Für Sessions im Freien ist das der praktischste Unterschied — man findet "
     "das Mundende, ohne zu tasten."),
 bullet="<strong>Hellster Ton:</strong> Bleibt bei wenig Licht am längsten erkennbar."),
"Grün": dict(
 p1=("Grün liegt farblich am nächsten an ungebleichtem Paper, ohne mit ihm zu verschmelzen. Der Filter "
     "bleibt sichtbar, wirkt aber nicht aufgesetzt."),
 h2="Sichtbar, ohne zu stören",
 p2=("Zwischen Organic, das im braunen Paper fast verschwindet, und Blau, das maximal absticht, liegt "
     "Grün genau in der Mitte. Man sieht, dass ein Filter drin ist, ohne dass er das Bild bestimmt. Für "
     "alle, die weder das eine noch das andere Extrem wollen, ist das die naheliegende Wahl."),
 bullet="<strong>Mittlerer Kontrast:</strong> Sichtbar in braunem Paper, ohne es zu dominieren."),
"Lila": dict(
 p1=("Lila ist der dunkelste gefärbte Ton bei PURIZE — und der, dem man den Gebrauch am wenigsten ansieht."),
 h2="Der Ton, der Gebrauchsspuren schluckt",
 p2=("Keramikkappen nehmen im Gebrauch Farbe an; bei hellen Tönen wird das nach der Hälfte des Joints "
     "sichtbar. Lila ist dunkel genug, dass es schlicht nicht auffällt. Wer einen Joint zwischendurch "
     "ablegt und später weiterraucht, hat davon mehr als von jeder Designfrage."),
 bullet="<strong>Dunkelster gefärbter Ton:</strong> Gebrauchsspuren an der Kappe fallen kaum auf."),
"Pink": dict(
 p1=("Pink ist der kräftigste Ton der Reihe und der einzige, der sich mit keinem anderen verwechseln "
     "lässt — weder bei Kunstlicht noch aus zwei Metern Entfernung."),
 h2="Unverwechselbar in gemischten Runden",
 p2=("Sobald mehrere Leute gleichzeitig drehen und die Joints nebeneinander liegen, wird die Filterfarbe "
     "zur Zuordnung. Pink erfüllt diese Aufgabe am zuverlässigsten, weil kein anderer Ton der Reihe in "
     "seine Nähe kommt. Das ist kein Designargument, sondern ein praktisches."),
 bullet="<strong>Kräftigster Ton:</strong> Mit keiner anderen Farbe der Reihe zu verwechseln."),
"Organic": dict(
 p1=("Organic ist die ungefärbte Variante: Keramik ohne Pigmentzusatz, in ihrem eigenen gebrochenen "
     "Naturton. Sie ist die Variante, die zu ungebleichtem Paper gehört."),
 h2="Für ungebleichtes Paper gedacht",
 p2=("Wer bewusst zu ungebleichten Longpapern greift, will meistens auch am Mundstück keinen Farbbruch. "
     "Genau dafür gibt es Organic: Der Übergang zwischen Paper und Kappe fällt kaum auf, der Joint wirkt "
     "aus einem Stück. Auf weißem Paper setzt sich der Naturton dagegen sichtbar ab — dort ist Weiß die "
     "passendere Wahl."),
 bullet="<strong>Ohne Pigmentzusatz:</strong> Keramik im Naturton, abgestimmt auf ungebleichtes Paper."),
"Rainbow": dict(
 p1=("Rainbow ist die gemischte Farbpackung: Die Töne des Sortiments liegen durcheinander im Beutel, "
     "ohne feste Verteilung."),
 h2="Wofür sich die gemischte Packung eignet",
 p2=("Der praktische Grund ist die Zuordnung: In einer Runde nimmt jeder eine andere Farbe, und hinterher "
     "weiß jeder, welcher Joint seiner ist. Der zweite Grund ist banaler — wer noch nicht weiß, welcher "
     "Ton ihm liegt, findet es über eine gemischte Packung schneller heraus als über acht einzelne, und "
     "nichts bleibt liegen."),
 bullet="<strong>Farben gemischt:</strong> Ohne feste Verteilung im Beutel."),
"Spion": dict(
 p1=("Die Spion-Edition ist eine Designvariante des XTRA Slim. Am Filter selbst ändert sich nichts: "
     "5,9 mm, Keramikkappen an beiden Enden, Kokosnuss-Aktivkohle."),
 h2="Designvariante, gleicher Filter",
 p2=("PURIZE bringt regelmäßig Editionen heraus, die sich allein über die Aufmachung unterscheiden. Das "
     "ist ehrlicherweise der ganze Unterschied — wer die Edition kauft, kauft die Optik, nicht eine andere "
     "Filterleistung. Wer sie sammelt, weiß das ohnehin; wer nur filtern will, kann genauso gut zur "
     "Standardfarbe greifen."),
 bullet="<strong>Designedition:</strong> Aufmachung abweichend, Filter identisch zur Standardvariante."),
"XMAS": dict(
 p1=("Die XMAS-Edition ist die Weihnachtsaufmachung des XTRA Slim. Der Filter darunter ist der gleiche "
     "wie das ganze Jahr über."),
 h2="Saisonale Aufmachung",
 p2=("Saisonale Editionen sind meistens nur für ein paar Monate im Regal und danach nicht mehr "
     "nachbestellbar. Das macht sie für Sammler interessant und für alle anderen zu einer Packung wie "
     "jede andere — mit dem Unterschied, dass sie sich als Kleinigkeit verschenken lässt, ohne dass man "
     "etwas erklären muss."),
 bullet="<strong>Saisonale Edition:</strong> Weihnachtsaufmachung, Filter unverändert."),
"Tyson 2.0": dict(
 p1=("Tyson 2.0 ist PURIZEs Zusammenarbeit mit der Marke von Mike Tyson. Die Aufmachung trägt dessen "
     "Branding, der Filter darunter ist der reguläre XTRA Slim."),
 h2="Markenkooperation, unveränderter Filter",
 p2=("Kooperationseditionen laufen in begrenzter Auflage und verschwinden danach aus dem Sortiment. "
     "Wer sie sammelt, greift deshalb früh zu. Für alle anderen gilt: Innen steckt derselbe 5,9-mm-Filter "
     "mit Keramikkappen wie in der weißen Standardpackung — die Kooperation ändert die Aufmachung, "
     "nicht die Funktion."),
 bullet="<strong>Kooperation mit Tyson 2.0:</strong> Begrenzte Auflage, Filter identisch zur Standardvariante."),
"Blazy Susan Lila": dict(
 p1=("Blazy Susan ist die US-Marke, die für ihre durchgefärbten Papers bekannt ist. Diese Edition ist die "
     "gemeinsame Ausgabe mit PURIZE, farblich auf deren Papers abgestimmt."),
 h2="Abgestimmt auf die Papers der Marke",
 p2=("Der Sinn einer Marken-Kooperation bei Filtern ist selten technisch, hier aber immerhin optisch "
     "nachvollziehbar: Wer Blazy-Susan-Papers dreht, bekommt einen Filter im passenden Ton statt eines "
     "Farbbruchs am Mundstück. Am Filter selbst ändert die Kooperation nichts."),
 bullet="<strong>Kooperation mit Blazy Susan:</strong> Farblich auf deren Papers abgestimmt."),
"Blazy Susan Pink": dict(
 p1=("Pink ist Blazy Susans Erkennungsfarbe, und diese Edition führt sie am Filter fort. Wer die pinken "
     "Papers der Marke dreht, bekommt hier das passende Mundstück."),
 h2="Der Ton, für den die Marke steht",
 p2=("Blazy Susan hat sich über durchgefärbte pinke Papers einen Namen gemacht — ein Filter in einem "
     "anderen Ton bricht dieses Bild. Diese Edition schließt die Lücke. Technisch bleibt es der reguläre "
     "XTRA Slim mit 5,9 mm und Keramikkappen."),
 bullet="<strong>Blazy Susan in Pink:</strong> Passend zu den durchgefärbten Papers der Marke."),
}
FARBEN["Austria"] = dict(
 p1=("Die Austria-Edition richtet sich mit ihrer Aufmachung an den österreichischen Markt. Am Filter "
     "selbst ändert das nichts: derselbe 5,9-mm-XTRA-Slim mit Keramikkappen wie in der weißen "
     "Standardpackung."),
 h2="Marktausgabe für Österreich",
 p2=("Solche länderbezogenen Aufmachungen sind meist eine Frage der Distribution, nicht der Technik — "
     "PURIZE bringt sie für einzelne Absatzmärkte heraus, ohne den Filter selbst zu verändern. Wer die "
     "Ausgabe wegen der Aufmachung sucht, greift hier richtig zu; wer nur filtern will, bekommt in Weiß "
     "exakt dasselbe."),
 bullet="<strong>Marktausgabe Österreich:</strong> Aufmachung angepasst, Filter identisch zur Standardvariante.")
FARBEN["France"] = dict(
 p1=("Die France-Edition ist die Marktausgabe für Frankreich. Sie unterscheidet sich in der Aufmachung "
     "von der deutschen Standardpackung, nicht im Filter selbst."),
 h2="Marktausgabe für Frankreich",
 p2=("Aufdruck und Verpackungstext sind auf den französischen Markt zugeschnitten, der Inhalt bleibt "
     "der reguläre XTRA Slim mit 5,9 mm und beidseitiger Keramikkappe. Für den Gebrauch macht das keinen "
     "Unterschied — nur für die Frage, welche Packung im Regal steht."),
 bullet="<strong>Marktausgabe Frankreich:</strong> Aufmachung angepasst, Filter identisch zur Standardvariante.")
FARBEN["ITALY"] = dict(
 p1=("Die ITALY-Edition ist auf den italienischen Markt zugeschnitten. Verpackung und Beschriftung "
     "unterscheiden sich, der Filter darin ist der reguläre XTRA Slim."),
 h2="Marktausgabe für Italien",
 p2=("Wie bei den anderen Länderausgaben liegt der Unterschied allein in der Aufmachung. Der Filter "
     "selbst — 5,9 mm, Keramikkappen an beiden Enden, Kokosnuss-Aktivkohle — ist identisch mit der "
     "deutschen Standardpackung."),
 bullet="<strong>Marktausgabe Italien:</strong> Aufmachung angepasst, Filter identisch zur Standardvariante.")
FARBEN["UK"] = dict(
 p1=("Die UK-Edition ist die Marktausgabe für Großbritannien. Beschriftung und Verpackung folgen dem "
     "britischen Markt, der Filter bleibt der reguläre XTRA Slim."),
 h2="Marktausgabe für Großbritannien",
 p2=("Der Inhalt unterscheidet sich nicht von der Standardpackung: 5,9 mm Durchmesser, Keramikkappen an "
     "beiden Enden, Kokosnuss-Aktivkohle. Die UK-Ausgabe ist in erster Linie für den britischen "
     "Vertrieb gedacht, nicht als eigenständige Filtervariante."),
 bullet="<strong>Marktausgabe Großbritannien:</strong> Aufmachung angepasst, Filter identisch zur Standardvariante.")
FARBEN["Mixed"] = dict(
 p1=("Mixed ist PURIZEs zweite Sortimentspackung neben Rainbow: mehrere Töne des Sortiments in einem "
     "Behälter, ohne dass eine Farbe einzeln dazugekauft werden muss."),
 h2="Die ganze Farbpalette in einem Kauf",
 p2=("Wer alle Töne kennenlernen will, ohne acht einzelne Packungen zu kaufen, ist mit Mixed am "
     "schnellsten durch. Statt einer Farbe pro Packung bekommst du einen Querschnitt durch das Sortiment "
     "in einem Behälter — praktisch für alle, die nicht bei einer Farbe bleiben wollen oder für eine "
     "Gruppe kaufen, in der jeder eine andere möchte."),
 bullet="<strong>Sortimentspackung:</strong> Mehrere Farbtöne in einem Behälter, ohne Einzelkauf.")


FORMATE = {
"50": dict(inhalt="50 Filter im wiederverschließbaren Beutel", kurz="50er", lang="50 Stück",
 p1=("Der 50er-Beutel ist PURIZEs Standardgröße: reicht bei regelmäßigem Gebrauch zwei bis drei Wochen "
     "und passt flach in jede Tasche."),
 h2="Die Packung, die man mitnimmt",
 p2=("Fünfzig Filter sind wenig genug, um den Beutel komplett dabeizuhaben, und genug, um nicht ständig "
     "nachlegen zu müssen. Der Zip hält dicht, solange man ihn zudrückt — und weil die Packung schnell "
     "aufgebraucht ist, wird Lagerung hier nie zum Thema."),
 bullet="<strong>50 Stück im Zip-Beutel:</strong> Zwei bis drei Wochen, flach genug für die Tasche.",
 tipps=[("Zip wirklich zudrücken",
         "Aktivkohle zieht Luftfeuchtigkeit. Ein offener Beutel macht die letzten Filter zäh im Zug.")]),
"250": dict(inhalt="250 Filter im Beutel", kurz="250er", lang="250 Stück",
 p1=("Die 250er-Packung ist der Nachkauf, wenn die Farbe feststeht: fünfmal die Standardmenge, "
     "entsprechend niedrigerer Stückpreis."),
 h2="Wenn die Entscheidung gefallen ist",
 p2=("Wer den 50er-Beutel dreimal nachgekauft hat, weiß, dass die Größe passt. Ab dann ist die "
     "250er-Packung die vernünftige Wahl — sie hält rund zwei Monate und kostet je Filter deutlich "
     "weniger. Ein Punkt bleibt: Über zwei Monate wird der Beutel oft genug geöffnet, dass sich das "
     "Umfüllen einer kleinen Portion lohnt."),
 bullet="<strong>250 Stück:</strong> Rund zwei Monate, deutlich niedrigerer Stückpreis.",
 tipps=[("Kleine Portion abfüllen",
         "Nimm dir 30 bis 50 Stück für unterwegs heraus und lass den großen Beutel zu. Der Rest bleibt trocken.")]),
"500": dict(inhalt="500 Filter im Beutel", kurz="500er", lang="500 Stück",
 p1=("Fünfhundert Filter sind eine Vorratsentscheidung — gedacht für alle, die täglich drehen oder für "
     "mehrere Leute mitkaufen."),
 h2="Wann sich die Vorratsmenge lohnt",
 p2=("Bei täglichem Gebrauch reicht die Packung ein gutes halbes Jahr, bei gelegentlichem entsprechend "
     "länger. Der Stückpreis ist der niedrigste, den es im Beutel gibt. Die Frage bei dieser Menge ist "
     "nicht der Preis, sondern die Lagerung: Ein Beutel, der ein halbes Jahr lang täglich geöffnet wird, "
     "zieht jedes Mal feuchte Luft."),
 bullet="<strong>500 Stück:</strong> Ein gutes halbes Jahr bei täglichem Gebrauch.",
 tipps=[("Umfüllen statt täglich öffnen",
         "Fülle den Wochenbedarf in ein kleines Gefäß ab. Der große Beutel bleibt dann zu und trocken."),
        ("Trocken lagern",
         "Nicht im Bad, nicht auf der Fensterbank. Ein Schrank genügt.")]),
"Glas": dict(inhalt="Filter im Schraubglas", kurz="Glas", lang="Filter im Glas",
 p1=("Die Glasvariante tauscht den Beutel gegen ein Schraubglas. Bei größeren Mengen ist das kein "
     "Zubehör, sondern der Grund, warum sie funktioniert."),
 h2="Warum bei größeren Mengen das Glas kommt",
 p2=("Ein Zip-Beutel wird mit jedem Öffnen ein Stück undichter — die Folie knickt, der Verschluss nutzt "
     "sich ab. Bei fünfzig Filtern ist das egal, weil sie vorher aufgebraucht sind. Bei mehreren hundert "
     "nicht mehr. Das Schraubglas dichtet über die gesamte Nutzungsdauer gleich gut, und der letzte Filter "
     "zieht wie der erste."),
 bullet="<strong>Schraubglas statt Beutel:</strong> Dichtet über die ganze Nutzungsdauer gleich gut.",
 tipps=[("Deckel jedes Mal zudrehen",
         "Der ganze Vorteil hängt am Verschluss — handfest genügt, aber eben immer."),
        ("Nicht ins direkte Sonnenlicht",
         "Über Monate bleicht Sonne die gefärbten Kappen aus. Ein Schrank reicht.")]),
"Gebinde-Glas": dict(inhalt="10 Gläser", kurz="10 Gläser", lang="10 Gläser",
 p1=("Das Zehnergebinde richtet sich an alle, die nicht für sich allein kaufen: zehn verschlossene Gläser "
     "in einem Karton."),
 h2="Für Wiederverkauf und Großbedarf",
 p2=("Der Vorteil gegenüber einer einzelnen Großpackung ist die Aufteilung: Zehn separat verschlossene "
     "Gläser lassen sich einzeln weitergeben, einzeln öffnen und einzeln lagern. Neun bleiben dicht, "
     "während eines in Gebrauch ist — bei einer einzelnen Großmenge geht das nicht."),
 bullet="<strong>10 Gläser einzeln verschlossen:</strong> Neun bleiben dicht, während eines in Gebrauch ist.",
 tipps=[("Nur ein Glas gleichzeitig öffnen",
         "Der Sinn des Gebindes liegt darin, dass die übrigen neun ungeöffnet bleiben.")]),
"Gebinde-Beutel": dict(inhalt="20 Beutel à 50 Filter", kurz="20 Beutel", lang="20 Beutel à 50 Filter",
 p1=("Zwanzig einzelne 50er-Beutel in einem Gebinde — gedacht für Weitergabe, Wiederverkauf oder als "
     "Jahresvorrat in handlichen Portionen."),
 h2="Portioniert statt in einem Stück",
 p2=("Tausend Filter in einem Beutel wären nach wenigen Wochen durchfeuchtet. In zwanzig einzeln "
     "verschlossenen Portionen bleibt das Problem aus: Neunzehn Beutel liegen zu, während einer in "
     "Gebrauch ist. Für den Weiterverkauf ist die Aufteilung ohnehin die einzige praktikable Form."),
 bullet="<strong>20 einzelne Beutel:</strong> Portioniert — 19 bleiben zu, während einer benutzt wird.",
 tipps=[("Immer nur einen Beutel öffnen",
         "So bleibt der Rest über Monate so trocken wie am ersten Tag.")]),
"33": dict(inhalt="33 Filter im wiederverschließbaren Beutel", kurz="33er", lang="33 Stück",
 p1=("Die Editionsbeutel fassen 33 Filter — etwas weniger als die Standardpackung, dafür in der "
     "jeweiligen Aufmachung."),
 h2="Editionsgröße",
 p2=("Sondereditionen laufen meist in kleineren Auflagen und in einer eigenen Packungsgröße. "
     "Dreiunddreißig Stück reichen bei regelmäßigem Gebrauch gut zwei Wochen — genug, um die Edition zu "
     "benutzen statt sie nur ins Regal zu stellen."),
 bullet="<strong>33 Stück:</strong> Editionsgröße, gut zwei Wochen bei regelmäßigem Gebrauch.",
 tipps=[("Zip zudrücken",
         "Auch bei kleinen Mengen gilt: Ein offener Beutel macht die letzten Filter zäh im Zug.")]),
}

BASIS_BULLETS = [
 "<strong>5,9 mm:</strong> Der schmalste Standardfilter von PURIZE, für dünne Selbstgedrehte, Longpaper und Cones.",
 "<strong>Keramik an beiden Enden:</strong> Keine Einbaurichtung — du legst den Filter ein, wie du ihn greifst.",
 "<strong>Aktivkohle aus Kokosnussschalen:</strong> Kühlt den Rauch auf dem Weg zum Mundstück.",
 "<strong>ca. 27 mm Länge:</strong> Genug Fläche, um das Paper beim Drehen sauber zu greifen.",
 "<strong>Hergestellt in Deutschland:</strong> Gleichbleibende Maßhaltigkeit von Charge zu Charge.",
]

TABELLE = """<h2>Technische Details im Überblick</h2>
<table>
<thead><tr><td><strong>Merkmal</strong></td><td><strong>Details</strong></td></tr></thead>
<tbody>
<tr><td><strong>Filtertyp</strong></td><td>PURIZE XTRA Slim, Aktivkohlefilter</td></tr>
<tr><td><strong>Durchmesser</strong></td><td>5,9 mm</td></tr>
<tr><td><strong>Länge</strong></td><td>ca. 27 mm</td></tr>
<tr><td><strong>Füllung</strong></td><td>Aktivkohle aus Kokosnussschalen</td></tr>
<tr><td><strong>Endkappen</strong></td><td>beidseitig Keramik – keine Einbaurichtung</td></tr>
<tr><td><strong>Ausführung</strong></td><td>%s</td></tr>
<tr><td><strong>Inhalt</strong></td><td>%s</td></tr>
<tr><td><strong>Herstellung</strong></td><td>Deutschland</td></tr>
</tbody>
</table>"""

# (id, Anzeigename, Farbe/Edition, Format)
PRODUKTE = [
 (13612,"PURIZE Aktivkohlefilter XTRA Slim 6mm im Beutel Weiß – 50 Stück","Weiß","50"),
 (13569,"PURIZE Aktivkohlefilter XTRA Slim 6mm im Beutel Blau – 50 Stück","Blau","50"),
 (13588,"PURIZE Aktivkohlefilter XTRA Slim 6mm im Beutel Gelb – 50 Stück","Gelb","50"),
 (13595,"PURIZE Aktivkohlefilter XTRA Slim 6mm im Beutel Grün – 50 Stück","Grün","50"),
 (13603,"PURIZE Aktivkohlefilter XTRA Slim 6mm im Beutel Lila – 50 Stück","Lila","50"),
 (13626,"PURIZE Aktivkohlefilter XTRA Slim 6mm im Beutel Pink – 50 Stück","Pink","50"),
 (13619,"PURIZE Aktivkohlefilter XTRA Slim 6mm im Beutel Organic – 50 Stück","Organic","50"),
 (13580,"PURIZE Aktivkohlefilter XTRA Slim 6mm im Beutel Rainbow – 50 Stück","Rainbow","50"),
 (13633,"PURIZE Aktivkohlefilter XTRA Slim 6mm im Beutel Spion – 50 Stück","Spion","50"),
 (20065,"PURIZE Aktivkohlefilter XTRA Slim Size 6mm XMAS Weihnachts-Edition 50 Stück","XMAS","50"),
 (27691,"PURIZE Aktivkohlefilter XTRA Slim 6mm – Weiß, 250 Stück","Weiß","250"),
 (27683,"PURIZE Aktivkohlefilter XTRA Slim 6mm – Blau, 250 Stück","Blau","250"),
 (27685,"PURIZE Aktivkohlefilter XTRA Slim 6mm – Gelb, 250 Stück","Gelb","250"),
 (27686,"PURIZE Aktivkohlefilter XTRA Slim 6mm – Grün, 250 Stück","Grün","250"),
 (27684,"PURIZE Aktivkohlefilter XTRA Slim 6mm – Lila, 250 Stück","Lila","250"),
 (27688,"PURIZE Aktivkohlefilter XTRA Slim 6mm – Pink, 250 Stück","Pink","250"),
 (27687,"PURIZE Aktivkohlefilter XTRA Slim 6mm – Organic, 250 Stück","Organic","250"),
 (27689,"PURIZE Aktivkohlefilter XTRA Slim 6mm – Rainbow, 250 Stück","Rainbow","250"),
 (27690,"PURIZE Aktivkohlefilter XTRA Slim 6mm – Spy, 250 Stück","Spion","250"),
 (27692,"PURIZE Aktivkohlefilter XTRA Slim 6mm – Mixed, 500 Stück","Mixed","500"),
 (13456,"PURIZE Aktivkohlefilter XTRA Slim 6mm weiß im Glas","Weiß","Glas"),
 (13543,"PURIZE Aktivkohlefilter XTRA Slim 6mm Blau im Glas","Blau","Glas"),
 (13535,"PURIZE Aktivkohlefilter XTRA Slim 6mm Gelb im Glas","Gelb","Glas"),
 (13526,"PURIZE Aktivkohlefilter XTRA Slim 6mm Grün im Glas","Grün","Glas"),
 (13519,"PURIZE Aktivkohlefilter XTRA Slim 6mm Lila im Glas","Lila","Glas"),
 (13481,"PURIZE Aktivkohlefilter XTRA Slim 6mm Pink im Glas","Pink","Glas"),
 (13488,"PURIZE Aktivkohlefilter XTRA Slim 6mm Organic im Glas","Organic","Glas"),
 (13472,"PURIZE Aktivkohlefilter XTRA Slim 6mm Rainbow im Glas","Rainbow","Glas"),
 (13465,"PURIZE Aktivkohlefilter XTRA Slim 6mm Spion im Glas","Spion","Glas"),
 (13438,"PURIZE Aktivkohlefilter XTRA Slim 6mm Mixed im Glas","Mixed","Glas"),
 (13552,"PURIZE Aktivkohlefilter XTRA Slim 6mm XMAS im Glas","XMAS","Glas"),
 (13640,"PURIZE Aktivkohlefilter XTRA Slim 6mm im Beutel Austria","Austria","33"),
 (13647,"PURIZE Aktivkohlefilter XTRA Slim 6mm im Beutel France","France","33"),
 (13561,"PURIZE Aktivkohlefilter XTRA Slim 6mm im Beutel ITALY","ITALY","33"),
 (13652,"PURIZE Aktivkohlefilter XTRA Slim 6mm im Beutel UK","UK","33"),
 (13659,"PURIZE Aktivkohlefilter XTRA Slim 6mm im Beutel Mixed","Mixed","33"),
 (13665,"PURIZE Aktivkohlefilter XTRA Slim 6mm im Beutel Tyson 2.0","Tyson 2.0","33"),
 (14393,"PURIZE Blazy Susan Aktivkohlefilter XTRA Slim 6mm im Beutel Lila – 50 Stück","Blazy Susan Lila","50"),
 (14392,"PURIZE Blazy Susan Aktivkohlefilter XTRA Slim 6mm im Beutel Pink – 50 Stück","Blazy Susan Pink","50"),
 (13400,"PURIZE x Blazy Susan XTRA Slim 6mm 10 Gläser mit je 100 Filter","Blazy Susan Pink","Gebinde-Glas"),
 (13405,"PURIZE x Blazy Susan XTRA Slim 6mm 20 Beutel mit je 50 Filter","Blazy Susan Pink","Gebinde-Beutel"),
]

def titel(farbe, kurz):
    for k in ["PURIZE XTRA Slim 6 mm Aktivkohlefilter %s %s | Hanfjack" % (farbe, kurz),
              "PURIZE XTRA Slim 6 mm Aktivkohlefilter %s %s" % (farbe, kurz),
              "PURIZE XTRA Slim 6 mm Filter %s %s | Hanfjack" % (farbe, kurz),
              "PURIZE XTRA Slim %s %s | Hanfjack" % (farbe, kurz)]:
        if len(k) <= 60: return k
    return ("PURIZE XTRA Slim %s %s" % (farbe, kurz))[:60]

def meta(farbe, lang):
    kandidaten = [
      ("PURIZE XTRA Slim %s, 5,9 mm: Aktivkohlefilter mit Keramikkappen an beiden Enden, ca. 27 mm, "
       "ohne Einbaurichtung. %s, hergestellt in Deutschland.") % (farbe, lang),
      ("PURIZE XTRA Slim %s, 5,9 mm: Keramikkappen beidseitig, Kokosnuss-Aktivkohle, ca. 27 mm, "
       "ohne Einbaurichtung. %s, aus Deutschland.") % (farbe, lang),
      ("PURIZE XTRA Slim %s, 5,9 mm: beidseitig Keramik, Kokoskohle, ohne Einbaurichtung. %s.")
      % (farbe, lang),
      ("PURIZE XTRA Slim %s, 5,9 mm: Keramikkappen, Kokoskohle, ohne Einbaurichtung. %s.")
      % (farbe, lang),
      ("PURIZE XTRA Slim, 5,9 mm: Keramikkappen, Kokoskohle, ohne Einbaurichtung. %s.") % lang,
    ]
    passend = [m for m in kandidaten if 120 <= len(m) <= 160]
    if passend:
        return passend[0]
    # Kein Kandidat traf das Fenster — den mit der kürzesten Überlänge nehmen und kappen.
    ueberlang = [m for m in kandidaten if len(m) > 160]
    if ueberlang:
        kuerzester = min(ueberlang, key=len)
        return kuerzester[:157].rsplit(' ', 1)[0] + '...'
    return min(kandidaten, key=lambda m: abs(len(m) - 140))

def bauen(pid, name, farbe, fmt):
    f, g = FARBEN[farbe], FORMATE[fmt]
    bullets = [f["bullet"], g["bullet"]] + BASIS_BULLETS
    desc = (
      '<p><strong>%s</strong> sind PURIZEs schmalste Standardfilter: 5,9 mm Durchmesser, rund 27 mm lang, '
      'an beiden Enden mit einer Keramikkappe verschlossen. Die Kappen halten die Kokosnuss-Aktivkohle im '
      'Filter und geben ihm die Form — deshalb gibt es keine falsche Einbaurichtung und der Zug bleibt '
      'über die ganze Länge gleichmäßig.</p>'
      '<p>%s</p><p>%s</p>'
      '<h2>%s</h2><p>%s</p>'
      '<h2>%s</h2><p>%s</p>'
      '<h2>Was den XTRA Slim ausmacht</h2><ul>%s</ul>'
      '%s'
      '<h2>Praxistipps</h2>%s'
      '<p><strong>Farbe und Leistung:</strong> Alle Ausführungen des XTRA Slim sind innen identisch '
      'aufgebaut. Die Farbe der Keramikkappe ändert nichts an Kühlung, Zug oder Standzeit — sie ist eine '
      'Frage der Optik und der Wiedererkennung, nicht der Funktion.</p>'
    ) % (name, f["p1"], g["p1"], f["h2"], f["p2"], g["h2"], g["p2"],
         "".join("<li>%s</li>" % b for b in bullets),
         TABELLE % (farbe, g["inhalt"]),
         "".join("<p><strong>%s:</strong> %s</p>" % (t, x) for t, x in g["tipps"]))
    short = ('<p><strong>%s:</strong> Der <strong>%s</strong> misst 5,9 mm bei rund 27 mm Länge und trägt an '
             'beiden Enden eine Keramikkappe — keine Einbaurichtung, gleichmäßiger Zug. Aktivkohle aus '
             'Kokosnussschalen, hergestellt in Deutschland. %s</p>'
             ) % (f["h2"], name, g["lang"] + ".")
    return dict(id=pid, name=name, desc=desc, short=short,
                focus="PURIZE XTRA Slim 6mm %s" % farbe,
                seo_title=titel(farbe, g["kurz"]), seo_desc=meta(farbe, g["lang"]))

out = [bauen(*p) for p in PRODUKTE]
json.dump(out, open('purize_xtra.json','w'), ensure_ascii=False, indent=1)

fehler = [(o['id'], len(o['seo_title']), len(o['seo_desc'])) for o in out
          if len(o['seo_title']) > 60 or not (120 <= len(o['seo_desc']) <= 160)]
print("PURIZE XTRA Slim erzeugt: %d  |  Längenfehler: %s" % (len(out), fehler or "keine"))
for k in ('seo_title','seo_desc','desc','short'):
    w = [o[k] for o in out]
    doppelt = len(w) - len(set(w))
    print("  %-10s eindeutig: %s%s" % (k, doppelt == 0, "" if doppelt == 0 else "  (%d Dubletten)" % doppelt))
paare = sorted((difflib.SequenceMatcher(None,a['desc'],b['desc']).ratio(), a['id'], b['id'])
               for a,b in itertools.combinations(out,2))
print("  Ähnlichkeit: max %.2f (%s/%s) · Median %.2f · min %.2f"
      % (paare[-1][0], paare[-1][1], paare[-1][2], paare[len(paare)//2][0], paare[0][0]))
print("  Wörter: %d bis %d" % (min(len(o['desc'].split()) for o in out),
                               max(len(o['desc'].split()) for o in out)))
