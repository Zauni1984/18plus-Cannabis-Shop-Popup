# -*- coding: utf-8 -*-
"""Kategorietexte, Stapel 6: die letzten 34 Kategorien.

Bei Lebensmitteln stehen keine gesundheitsbezogenen Angaben im Text - die
Health-Claims-Verordnung laesst sie ohne Zulassung nicht zu. Beschrieben
wird, was die Ware ist und wie sie verwendet wird.
"""
import sys
S = '/tmp/claude-0/-home-user-18plus-Cannabis-Shop-Popup/1414604f-ab94-5ea2-91e2-00ca46d10a2b/scratchpad/'
sys.path.insert(0, S)
from kat_zusatz import anhaengen

ZUSATZ = {

4147: """<strong>Lose oder im Beutel</strong>
Loser Tee lässt sich dosieren und entfaltet mehr Aroma, weil die Blätter Platz haben. Beutel sind bequemer und portionsgenau. Beides steht bei jedem Artikel in den Angaben.

<strong>Aufbewahren</strong>
Licht und Feuchtigkeit kosten Aroma. Dicht verschlossen, dunkel und trocken bleibt Tee am längsten frisch – nicht neben Gewürzen, er nimmt fremde Gerüche an.""",

4159: """<strong>Clip oder Standfuß</strong>
Clip-Ventilatoren klemmen an der Stange und kommen ohne Stellfläche aus. Standventilatoren bewegen mehr Luft, brauchen dafür Platz am Boden. Oszillierende Modelle streichen über den ganzen Bestand, statt eine Stelle dauernd anzublasen.

<strong>Richtig ausrichten</strong>
Der Luftstrom geht über die Pflanzen hinweg, nicht direkt hinein. Blätter sollen sich leicht bewegen; stehen sie dauerhaft im Wind, trocknen sie aus und zeigen Trockenschäden am Rand.

<strong>Warum das Stängel kräftigt</strong>
Bewegung reizt die Pflanze, stabileres Gewebe zu bilden. Wer ohne Umluft anbaut, bekommt weiche Triebe, die unter dem Gewicht der Blüten nachgeben.""",

16451: """<strong>Trocken trennen</strong>
Siebtrommeln und Pollenshaker arbeiten mechanisch: Das trockene Material wird über ein Sieb bewegt, die spröden Harzdrüsen brechen ab und fallen durch. Je feiner die Maschenweite, desto sauberer das Ergebnis und desto geringer die Ausbeute.

<strong>Kälte hilft</strong>
Harz löst sich besser, wenn es kalt und spröde ist. Material und Sieb vorher kühl zu stellen, macht den Unterschied.

<strong>Was sich lohnt</strong>
Verarbeitet wird vor allem, was beim Trimmen anfällt – Zuckerblätter und Bruch. Ganze Blüten dafür zu opfern, lohnt selten.""",

4220: """<strong>Wie sie arbeiten</strong>
Ein Feuchtigkeitsregler gibt Feuchte ab, wenn die Luft im Gefäß zu trocken wird, und nimmt sie auf, wenn sie zu feucht wird. Dadurch pendelt sich ein fester Wert ein, statt dass die Ware austrocknet oder schwitzt.

<strong>Welcher Wert</strong>
Üblich sind Packs für 58 oder 62 Prozent. 62 hält die Ware weicher und aromatischer, 58 gilt als die sicherere Wahl gegen Schimmel.

<strong>Haltbarkeit</strong>
Ein Pack ist erschöpft, wenn es hart wird. Es gehört nicht in direkten Kontakt mit der Ware, wenn der Hersteller das ausschließt – der Hinweis steht auf der Packung.""",

4144: """<strong>Geschält oder mit Schale</strong>
Geröstete Samen mit Schale sind knackiger und kräftiger im Geschmack, geschälte milder und weicher. Beides wird gern gewürzt angeboten, von Salz über Paprika bis Chili.

<strong>Verwendung</strong>
Pur als Knabberei, über Salat oder Suppe gestreut, oder in Teig eingearbeitet.

<strong>Aufbewahren</strong>
Nach dem Öffnen dicht verschließen. Die enthaltenen Fette reagieren auf Wärme und Licht, kühl und dunkel bleibt der Geschmack länger frisch.""",

7645: """<strong>Was drin ist</strong>
Tabakersatz besteht aus getrockneten Kräutern – häufig Damiana, Königskerze, Himbeerblatt oder Brennnessel. Nikotin ist nicht enthalten, Tabak ebenso wenig. Die genaue Mischung steht bei jedem Artikel in den Zutaten.

<strong>Wofür er verwendet wird</strong>
Als Beimischung, um Material zu strecken und den Abbrand gleichmäßiger zu machen, oder als vollständiger Ersatz für Tabak in einer Mischung.

<strong>Was das nicht heißt</strong>
Ohne Nikotin und ohne Tabak bedeutet nicht unbedenklich: Verbrennung erzeugt in jedem Fall Rauch und Schadstoffe.""",

4223: """<strong>Woher Terpene kommen</strong>
Terpene sind flüchtige Pflanzenstoffe und machen den Geruch vieler Pflanzen aus – Limonen riecht nach Zitrus, Myrcen erdig, Pinen nach Nadelwald. Sie kommen in vielen Pflanzen vor und werden aus diesen gewonnen.

<strong>Dosierung</strong>
Terpenkonzentrate sind sehr stark. Dosiert wird tropfenweise und im Zweifel zu wenig; nachlegen geht, zurücknehmen nicht.

<strong>Lagerung</strong>
Dicht verschlossen, kühl und dunkel. Terpene verflüchtigen sich bei Wärme und verlieren dabei genau das, wofür man sie gekauft hat.""",

16456: """<strong>Diffus oder spiegelnd</strong>
Spiegelnde Folie wirft Licht gerichtet zurück und kann dabei Brennpunkte erzeugen. Diffus streuende Folie verteilt es gleichmäßiger über den Bestand – für Growräume meist die bessere Wahl.

<strong>Anbringen</strong>
Die Folie wird faltenfrei und dicht an die Wand gebracht; Falten erzeugen Schatten und heiße Stellen. Reflexionsseite nach innen – bei beidseitig beschichteter Ware steht sie in den Angaben.

<strong>Lichtdicht ist etwas anderes</strong>
Schwarz-weiße Folie hat zwei Aufgaben: Die weiße Seite reflektiert, die schwarze hält Licht zurück. Für eine Dunkelphase ohne Störlicht zählt die schwarze Seite.""",

5181: """<strong>Wozu ein Mundstück</strong>
Es gibt dem Zug einen festen Abschluss und hält die Lippen vom heißen Material fern. Bei Chillums und Pfeifen verhindert es außerdem, dass etwas durchgezogen wird.

<strong>Material</strong>
Glas ist geschmacksneutral und leicht zu reinigen, Metall unempfindlich, Kunststoff am günstigsten. Bei allen gilt: Sie werden warm.

<strong>Reinigung</strong>
Ausklopfen und in Alkohol einlegen. Wer mehrere im Wechsel nutzt, muss seltener reinigen.""",

5819: """<strong>Was drinsteckt</strong>
Eine Auswahl aus dem Sortiment, zusammengestellt ohne vorherige Bekanntgabe. Der Warenwert liegt über dem Preis der Box – welche Artikel es genau sind, steht vorher nicht fest.

<strong>Für wen das passt</strong>
Wer Verbrauchsmaterial ohnehin braucht und keine bestimmte Marke sucht, macht damit wenig falsch. Wer ein konkretes Produkt möchte, kauft es besser einzeln.

<strong>Rückgabe</strong>
Das Widerrufsrecht gilt wie bei jeder anderen Bestellung – die Überraschung ändert daran nichts.""",

4148: """<strong>Was Rohkost hier bedeutet</strong>
Die Ware wird nicht über eine bestimmte Temperatur erhitzt – üblich sind 42 Grad als Grenze. Gepresst, gemahlen oder geschält wird trotzdem.

<strong>Verwendung</strong>
In Müsli, Smoothies, Salaten oder Aufstrichen, also überall dort, wo nicht gekocht wird.

<strong>Aufbewahren</strong>
Ungeröstete Ware ist empfindlicher als geröstete. Kühl, dunkel und dicht verschlossen lagern, angebrochene Packungen zügig aufbrauchen.""",

4553: """<strong>Wann 7 mm passt</strong>
7 mm füllt ein King-Size-Slim-Paper etwas satter als 6 mm und gibt dem Mundstück mehr Halt, ohne schon so breit zu sein wie ein Regular-Filter. Wer 6 mm als zu locker empfindet, landet meist hier.

<strong>Aufbau</strong>
Wie bei allen Aktivkohlefiltern liegt ein Kohlekern zwischen zwei Kappen. Länge und Kappenart stehen beim jeweiligen Artikel.

<strong>Aufbewahren</strong>
Aktivkohle zieht Feuchtigkeit. Angebrochene Packungen verschlossen halten.""",

5831: """<strong>Nitril oder Latex</strong>
Nitril ist reißfester, beständiger gegen Öle und Lösungsmittel und löst keine Latexallergie aus. Latex sitzt enger und gibt mehr Gefühl in den Fingern. Für die Ernte wird meist Nitril genommen.

<strong>Warum überhaupt Handschuhe</strong>
Harz klebt an der Haut und lässt sich schlecht abwaschen. Handschuhe halten die Hände sauber und die Blüten frei von Hautfett.

<strong>Größe</strong>
Zu enge Handschuhe reißen an den Fingerkuppen, zu weite falten sich. Die Größentabelle steht beim Artikel.""",

16439: """<strong>Wie aeroponische Anzucht arbeitet</strong>
Die Stecklinge hängen in Körbchen über einer Kammer, in der Wasser fein vernebelt oder gesprüht wird. Die Schnittstellen sitzen ständig in feuchter, sauerstoffreicher Luft – das lässt Wurzeln oft schneller entstehen als in Substrat.

<strong>Was dafür laufen muss</strong>
Pumpe und Düsen brauchen Strom und dürfen nicht ausfallen: Ohne Nebel trocknen freiliegende Wurzeln binnen Stunden aus.

<strong>Sauber halten</strong>
Düsen verstopfen durch Ablagerungen. Zwischen zwei Durchgängen gehört die Kammer gereinigt und die Düsen entkalkt.""",

4555: """<strong>Warum konisch</strong>
Ein gedrehter Cone verjüngt sich zum Mundstück hin. Ein konischer Filter folgt dieser Form, statt sie aufzuweiten – das Blättchen liegt am Filter an, ohne dass es am Übergang klafft.

<strong>Einsetzen</strong>
Die schmale Seite zeigt zum Mund. Andersherum eingesetzt zieht er schwerer.

<strong>Aufbewahren</strong>
Verschlossen halten, Aktivkohle zieht Feuchtigkeit aus der Luft.""",

6241: """<strong>Wann Befeuchten nötig ist</strong>
In der Anzucht und im frühen Wachstum liegt der übliche Zielbereich höher als später – Stecklinge und Sämlinge nehmen einen Teil des Wassers über das Blatt auf. In einer gut belüfteten Box fällt die Feuchte sonst schnell ab.

<strong>Zielwerte</strong>
Gängig sind etwa 65 bis 70 Prozent in der Anzucht, 50 bis 60 im Wachstum. In der Blüte wird bewusst trockener gefahren, dort ist eher der Entfeuchter gefragt.

<strong>Aufstellen</strong>
Nicht direkt auf die Pflanzen richten und nicht neben den Zuluftschlauch. Ein Hygrometer auf Pflanzenhöhe zeigt, ob die Einstellung passt.""",

6242: """<strong>Warum trockener in der Blüte</strong>
Dichte Blüten halten Feuchtigkeit im Inneren fest. Bleibt die Luft zu feucht, entsteht dort Schimmel, oft unsichtbar von außen. Deshalb wird gegen Ende der Blüte bewusst trocken gefahren.

<strong>Zielwerte</strong>
Gängig sind etwa 40 bis 50 Prozent in der Blüte, in den letzten Wochen eher am unteren Ende.

<strong>Wärme beachten</strong>
Ein Entfeuchter gibt Abwärme ab. In einer kleinen Box kann er die Temperatur merklich anheben – Lüftung entsprechend einplanen.""",

4554: """<strong>Wofür der große Durchmesser</strong>
8 bis 9 mm ergeben ein festes, breites Mundstück. Das passt zu dick gedrehten Joints, Blunts und zum Einsatz in Pfeifenköpfen, wo ein schmaler Filter durchrutschen würde.

<strong>Mehr Fläche</strong>
Ein größerer Querschnitt bedeutet mehr Kohle im Zug und einen leichteren Durchzug als bei Slim-Filtern.

<strong>Aufbewahren</strong>
Verschlossen halten – Aktivkohle zieht Feuchtigkeit.""",

5833: """<strong>Vorbeugen ist einfacher als heilen</strong>
Gelbtafeln zeigen fliegende Schädlinge, bevor der Befall sichtbar wird. Nützlinge und vorbeugende Mittel wirken, solange die Zahl klein ist. Ist der Bestand einmal durchsetzt, wird es aufwendig.

<strong>Anwendung</strong>
Gesprüht wird bei ausgeschalteter Lampe, auch auf die Blattunterseiten – dort sitzen Spinnmilben und Thripse. Mehrfach im Abstand von Tagen behandeln, weil Eier den ersten Durchgang überstehen.

<strong>In der Blüte</strong>
Viele Mittel dürfen in der Blüte nicht mehr eingesetzt werden oder hinterlassen Rückstände. Die Wartezeit steht auf der Packung und ist bindend.""",

4551: """<strong>Wann 5 mm sinnvoll ist</strong>
Schmaler heißt: mehr Platz im Blättchen für Material und ein zierlicheres Mundstück. Wer dünn dreht, empfindet 6 mm schnell als klobig.

<strong>Was das kostet</strong>
Weniger Querschnitt bedeutet auch weniger Kohle und einen etwas festeren Zug als bei breiteren Filtern.

<strong>Aufbewahren</strong>
Verschlossen halten, Aktivkohle zieht Feuchtigkeit aus der Luft.""",

4556: """<strong>Wofür 14 mm</strong>
Das ist das Format für Blunts und dicke Konstruktionen, bei denen ein schmaler Filter im Wrap verschwinden würde. Auch als Einsatz in weiten Pfeifenköpfen wird er verwendet.

<strong>Durchzug</strong>
Der große Querschnitt zieht sehr leicht. Wer einen festeren Zug gewohnt ist, merkt den Unterschied deutlich.

<strong>Aufbewahren</strong>
Verschlossen halten – Aktivkohle zieht Feuchtigkeit.""",

5426: """<strong>Nachfüllbar oder Einweg</strong>
Nachfüllbare Gasfeuerzeuge werden über das Ventil am Boden befüllt und halten bei normalem Gebrauch lange. Einwegmodelle sind billiger in der Anschaffung und landen am Ende im Restmüll.

<strong>Nachfüllen</strong>
Vor dem Befüllen die Flamme ganz herunterdrehen und das Feuerzeug auskühlen lassen. Nach dem Füllen einige Minuten warten, bevor es gezündet wird.

<strong>Unterwegs</strong>
Nicht im Auto in der Sonne liegen lassen – Gasfeuerzeuge stehen unter Druck.""",

4146: """<strong>Was Hanf im Getränk macht</strong>
Verwendet werden meist Hanfsamenextrakt oder Hanfaroma. Beides bringt eine leicht nussig-herbe Note und wirkt nicht berauschend.

<strong>Was in den Angaben steht</strong>
Zutaten, Nährwerte und Pfand stehen bei jedem Artikel. Bei Mehrwegflaschen kommt das Pfand im Warenkorb hinzu.

<strong>Kühl servieren</strong>
Die Hanfnote kommt gekühlt klarer heraus als bei Zimmertemperatur.""",

4149: """<strong>Was die Mischungen enthalten</strong>
Gemahlene oder ganze Hanfsamen, teils Hanfblatt, kombiniert mit klassischen Gewürzen. Die vollständige Zutatenliste steht bei jedem Artikel.

<strong>Verwendung</strong>
Über Gemüse, Kartoffeln, Salat oder Dips. Hanfsamen vertragen Hitze nur begrenzt – am besten erst nach dem Garen darüberstreuen.

<strong>Aufbewahren</strong>
Dicht verschlossen, trocken und dunkel, nicht über dem Herd. Wärme und Dampf lassen Gewürze schnell fade werden.""",

4150: """<strong>Warum es nicht in die Pfanne gehört</strong>
Kaltgepresstes Hanföl hat einen niedrigen Rauchpunkt. Erhitzt verliert es Geschmack und Inhaltsstoffe. Es ist ein Öl für kalte Speisen: Salat, Dips, über fertig gegartem Gemüse.

<strong>Geschmack</strong>
Nussig bis leicht grasig, je nach Presscharge unterschiedlich kräftig. Die grüne Farbe stammt aus dem Chlorophyll der Samenschale.

<strong>Aufbewahren</strong>
Dunkel und kühl, nach dem Öffnen im Kühlschrank. Hanföl enthält viele ungesättigte Fettsäuren und wird dadurch schneller ranzig als andere Öle.""",

15: """<strong>Was Nutzhanf von Cannabis unterscheidet</strong>
Es ist dieselbe Pflanzenart, aber andere Sorten. Nutzhanf ist auf Fasern und Samen gezüchtet und enthält von Natur aus so wenig THC, dass er nicht berauschend wirkt. Zugelassene Sorten sind in der EU gelistet.

<strong>Wofür er verwendet wird</strong>
Aus den Fasern entstehen Textilien, Seile und Dämmstoffe, aus den Samen Lebensmittel und Öl, aus den Presskuchen Mehl.""",

4152: """<strong>Geschält oder ungeschält</strong>
Geschälte Samen sind weich, mild und nussig und lassen sich über alles streuen. Ungeschälte haben die Schale dran, sind knackiger, herber und ballaststoffreicher.

<strong>Verwendung</strong>
Über Müsli, Joghurt, Salat oder Suppe. In Teig eingearbeitet vertragen sie auch Backhitze; als Topping kommen sie besser nach dem Garen dazu.

<strong>Aufbewahren</strong>
Geschälte Samen sind empfindlicher, weil die schützende Schale fehlt. Dicht verschlossen, kühl und dunkel, angebrochen zügig aufbrauchen.""",

16436: """<strong>Wie das Mikroklima entsteht</strong>
Die Haube hält die Feuchtigkeit, die aus Substrat und Blättern verdunstet, im Gewächshaus. Sämlinge und frische Stecklinge, die noch keine Wurzeln haben, ziehen Wasser über das Blatt – genau dafür ist die Haube da.

<strong>Lüftungsschieber</strong>
Dauerhaft geschlossen bleibt es zu nass und schimmelt. Die Schieber werden nach und nach weiter geöffnet, bis die Pflanzen ohne Haube auskommen. Das nennt sich Abhärten und dauert einige Tage.

<strong>Heizmatte darunter</strong>
In kühlen Räumen kommt eine Heizmatte unter die Schale. Die Temperatur im Wurzelraum zählt, nicht die im Zimmer.""",

4151: """<strong>Woraus Hanfmehl entsteht</strong>
Beim Pressen von Hanfsamenöl bleibt ein Presskuchen zurück. Getrocknet und vermahlen ergibt er Hanfmehl – ein Nebenprodukt der Ölherstellung, das dadurch deutlich weniger Fett enthält als die ganzen Samen.

<strong>Backen damit</strong>
Hanfmehl enthält kein Gluten und trägt einen Teig deshalb nicht allein. Üblich ist ein Anteil von etwa einem Zehntel bis einem Fünftel der Mehlmenge, der Rest bleibt herkömmliches Mehl.

<strong>Geschmack</strong>
Kräftig nussig und leicht herb, mit grünlichem Ton im Gebäck.""",

4127: """<strong>Was ein Test zeigt</strong>
Die gängigen Schnelltests arbeiten mit einer Farbreaktion und zeigen an, ob ein Stoff vorhanden ist und grob in welcher Größenordnung. Ein Laborwert ist das nicht – für eine belastbare Zahl braucht es eine Analyse.

<strong>Anwendung</strong>
Eine kleine Probe wird nach Anleitung mit dem Reagenz zusammengebracht und die Farbe mit der beiliegenden Skala verglichen. Reagenzien sind ätzend: Handschuhe tragen, nicht auf der Haut anwenden, Kinder fernhalten.

<strong>Haltbarkeit</strong>
Reagenzien altern und verfälschen dann das Ergebnis. Das Haltbarkeitsdatum steht auf der Packung, dunkel und kühl gelagert halten sie am längsten.""",

6881: """<strong>510-Thread</strong>
Cartridges mit 510-Gewinde passen auf jeden Akku desselben Standards – Kartusche und Akku müssen also nicht vom selben Hersteller stammen. Das ist der Grund, warum sich das Format durchgesetzt hat.

<strong>Vor dem ersten Zug</strong>
Die Kartusche einige Minuten aufrecht stehen lassen, damit das Liquid den Docht durchtränkt. Zu früh und zu kräftig gezogen, brennt der Docht trocken an.

<strong>Aufbewahren</strong>
Aufrecht und nicht in der Sonne. Wärme macht das Liquid dünnflüssig und es kann aus dem Mundstück austreten.""",

6212: """<strong>Was ein Set abnimmt</strong>
Die Artikel sind aufeinander abgestimmt und decken einen Anwendungsbereich vollständig ab – man muss nicht selbst prüfen, was zusammenpasst.

<strong>Inhalt</strong>
Was genau enthalten ist, steht bei jedem Set einzeln aufgeführt. Ein Vergleich mit den Einzelpreisen ist damit schnell gemacht.""",

605: """<strong>Material</strong>
Holz ist schnittfest und schont die Klinge, nimmt aber Gerüche an. Kunststoff und Glas lassen sich rückstandsfrei reinigen, Glas stumpft dafür jede Klinge ab.

<strong>Rutschfest arbeiten</strong>
Ein feuchtes Tuch unter dem Brett hält es an Ort und Stelle. Bretter mit Gummifüßen bringen das gleich mit.

<strong>Reinigung</strong>
Holz wird von Hand gespült und aufrecht getrocknet, nicht in der Spülmaschine – sonst reißt es. Kunststoff verträgt die Maschine.""",

4153: """<strong>Was Hanf darin ausmacht</strong>
Meist sind es Hanfsamen als Zutat, teils Hanfaroma. Beides stammt aus Nutzhanf und wirkt nicht berauschend.

<strong>Was in den Angaben steht</strong>
Zutaten, Nährwerte und Allergenhinweise stehen bei jedem Artikel. Bei Schokolade und Riegeln lohnt der Blick auf Spuren von Nüssen.

<strong>Aufbewahren</strong>
Kühl und trocken. Schokolade verträgt keine Wärme, Riegel mit Samen werden bei Licht schneller ranzig.""",
}

if __name__ == '__main__':
    import re
    txt = ' '.join(ZUSATZ.values())
    ok = {'dass','muss','Biss','Presse','genauer','dauerhaft','zuerst','Nass','nass','Wasser',
          'besser','lassen','messen','misst','passen','passt','gepresst','verschlossen','Aussehen',
          'Aussagen','Messen','nussig','dessen','abgegossen','Sauerstoff','sauerstoffreicher',
          'dauert','quellen','quetschen','Dauer','steuern','Feuer','Feuerzeug','Feuerzeuge',
          'Feuerzeugs','Feuchte','Feuchtigkeit','Gemuese','Rauchpunkt','Presscharge','Presskuchen',
          'Bauart','Bauarten','Neuem','genau','Auswahl','aufbrauchen','Gebrauch','gebraucht',
          'Verbrauchsmaterial','Warenwert','Sauerstoffreich','aeroponische','Querschnitt','bequemer','dauernd','genaue',
          'streuen','streuende','Gasfeuerzeuge','Dauerhaft','sauerstoffreicher'}
    verd = sorted({w for w in re.findall(r'\b\w+\b', txt)
                   if re.search(r'(?<![Ff])ae|oe|ue', w) and not re.search(r'[äöüß]', w) and w not in ok})
    assert not verd, verd
    anhaengen(ZUSATZ, 'kat_texte_6_vorher.json')
