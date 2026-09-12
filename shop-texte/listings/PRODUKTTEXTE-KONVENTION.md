# Produkttexte, SEO, Tags & Produkteigenschaften (hanfjack.de)

Verbindliche Vorlage für die Texte der neu gelisteten Tiger-One-Samen.
Entschieden am 2026-09-10: **Fast-Buds-Stil** (die jüngste Textgeneration im Shop),
CTA **nur im SEO**, nicht in Beschreibung oder Kurzbeschreibung.

## Quellenregel

Alles, was im Text steht, kommt aus dem Tiger-One-Export (Genetik, Blühtyp,
THC-/CBD-Band, Aroma-, Effekt-, Ertrags- und Höhenfilter, `alt_description`
des Züchters). **Nichts wird erfunden.** Fehlt eine Angabe, steht das so im
Text („Zu dieser Artikelnummer liegen vom Züchter keine Angaben zu … vor").

### THC-Werte

Drei Stufen, in dieser Priorität:

1. **Punktwert**, wenn die `alt_description` einen nennt – mit Quelle:
   „THC: 26,6 % (Züchterangabe), Filterband 25 % und mehr".
2. **Echte Prozentspanne** aus dem Filterband: `0–4 %`, `5–9 %`, `10–15 %`,
   `16–24 %`, `25 % und mehr`. Keine Wortstufen wie „Hoch".
3. Kein Wert ohne Quelle.

## Aufbau der Beschreibung

```html
<p>Absatz 1: Sorte, Kreuzung, Einordnung, ggf. THC-Punktwert.</p>
<p>Absatz 2: Wuchs, Klima, Laufzeit, Besonderheiten.</p>
<h3>Auf einen Blick:</h3>
<ul>
<li><b>Kreuzung:</b> …</li>       <!-- genetic_description, x → × -->
<li><b>Genanteile:</b> …</li>     <!-- seeds_variety bzw. Züchter-Prozente -->
<li><b>THC:</b> …</li>
<li><b>CBD:</b> …</li>
<li><b>Blütezeit:</b> …</li>      <!-- Autos: <b>Erntereif:</b> … ab Keimung -->
<li><b>Ertrag indoor:</b> …</li>
<li><b>Ertrag outdoor:</b> …</li>
<li><b>Wuchshöhe:</b> …</li>
<li><b>Samentyp:</b> feminisiert[, autoflowering] / regulär</li>
</ul>
<h3>Aroma</h3>   <!-- nur wenn seeds_taste_filter gefüllt -->
<h3>Wirkung</h3> <!-- nur wenn seeds_effect_filter_2 gefüllt -->
<h3>Hinweise</h3><!-- Anbau-Tipp, NICHT die Zahlen aus der Liste wiederholen -->
<p><i>Verkauf als Sammlerstück und zu Sammel- und Zuchtzwecken. Die Keimung von
Hanfsamen ist in Deutschland nur mit behördlicher Erlaubnis zulässig. Bitte
beachte die für Dich geltende Rechtslage.</i></p>
```

Regeln: kein `<h1>`, durchgehend Du-Anrede, keine Hanfjack-CTA,
Rechtshinweis als letzter Absatz.

## Kurzbeschreibung

Ein `<p>`, ein bis zwei Sätze, die Kaufentscheidung tragend
(Genetik + Alleinstellung + eine Zahl). Keine CTA.

## Yoast SEO

| Feld | Muster |
| --- | --- |
| `seo_title` | `<Sorte> Hanfsamen kaufen \| Hanfjack` |
| `meta_description` | 110–160 Zeichen, Fakten zuerst, **CTA erlaubt** („Jetzt bei Hanfjack bestellen.") |
| `focus_keyword` | `<Sorte> Samen` |

## Tags (`product_tag`)

Bestehende Tags werden wiederverwendet, nicht dupliziert. Sechs bis zehn Stück:

`Hanfsamen` · Marke · Sortenname (klein) · `feminisiert`/`regulär` ·
`autoflowering` (falls Auto) · `Indica`/`Sativa`/`Hybrid` ·
THC-Band (`16-24 % THC`) · bis zu zwei Elternsorten ·
`CBD Samen` (falls CBD-Kategorie) · `Klassiker` / `Mischpaket` wo passend.

## Produkteigenschaften (Attribute)

Alle nicht variantenbildend und sichtbar, **`pa_inhalt` muss beim Schreiben
mitgesendet werden** (mit `variation: true` und allen Optionen), sonst verlieren
die Varianten ihr Attribut.

| ID | Attribut | Inhalt |
| --- | --- | --- |
| 2 | Inhalt | Packungsgrößen, `variation: true` |
| 12 | Variante | `Feminisiert` / `Regulär` (+ `Autoflowering`) |
| 16 | Genetik | Elternsorten einzeln |
| 4 | THC Gehalt | Prozentspanne |
| 6 | CBD Gehalt | Prozentspanne |
| 7 | Aroma | deutsche Tokens |
| 17 / 18 | Indica % / Sativa % | nur bei Züchter-Prozentangabe |
| 20 | Effekte | deutsche Tokens |
| 21 | Blütezeit (Tage) | Wochen × 7, z. B. `56-70` |
| 38 | Terpene | nur wenn der Züchter Terpene nennt |

### Übersetzungstabelle

**Aroma:** Fruity→Fruchtig · Earthy→Erdig · Spicy→Würzig · Skunky→Skunk ·
Woody→Holzig · Floral→Blumig · Piney→Pinie · Minty→Minzig · Gassy→Gas ·
Sweet→Süß · Citrus→Zitrus · Creamy→Cremig · Chem→Chem · Sour→Sauer ·
Herbal→Kräuter

**Effekte:** Uplifting→Stimmungshebend · Creative→Kreativ · Relaxing→Entspannt ·
Energetic→Energetisch · Focused→Fokussiert · Potent→Potent · Sleepy→Schläfrig ·
Couch-lock→Couch-Lock · Psychedelic→Psychedelisch

**Ertrag:** `Modest (up to 200gr/plant)`→bis 200 g/Pflanze ·
`Average (200-450)`→200–450 g/Pflanze · `High (450-750)`→450–750 g/Pflanze ·
`Very High (above 750)`→über 750 g/Pflanze (indoor analog in g/m²)

**Wuchshöhe:** Short→Kompakt · Medium→Mittel · Large→Groß

## Stand

| Marke | Produkte | Texte/SEO/Tags/Eigenschaften |
| --- | --- | --- |
| Seedsman | 97 | fertig (2026-09-10) |
| Sweet Seeds | 99 | fertig (2026-09-11) |
| TerpyZ Mutant Genetics | 18 | fertig (2026-09-11) |
| Sensi Seeds | 100 | fertig (2026-09-11) |
| Amsterdam Genetics | 36 | fertig (2026-09-11) |
| Buddha Seeds | 35 | fertig (2026-09-11) |
| Serious Seeds | 26 | fertig (2026-09-11) |
| Silent Seeds | 41 | fertig (2026-09-11) |
| Ripper Seeds | 26 | fertig (2026-09-11) |
| Purple City Genetics | 5 | fertig (2026-09-11) |
| Pyramid Seeds | 71 | fertig (2026-09-11) |
| Nirvana Seeds | 67 | fertig (2026-09-12) |

Bei Seedsman wurde zusätzlich ein unsauberer Produktname korrigiert:
ID 43483 → „Seedsman Relax Collection feminisiert (3 × 1 Samen)".

## Sweet-Seeds-Besonderheiten

Sweet Seeds führt mehrere parallele Linien derselben Genetik. Das wird in den
Texten konsequent unterschieden:

- **FAST-Version** (`seeds_flowering_type = Early/fast`): photoperiodische
  F1-Hybride, gekreuzt mit der hauseigenen Autoflower derselben Sorte. Bleibt
  lichtabhängig, blüht aber rund eine Woche kürzer.
  `Samentyp: feminisiert (FAST-Version, photoperiodisch)`.
- **XL Auto**: auf Größe und Ertrag selektierte Autoflower-Linie → Tag `XL Ertrag`.
- **Generationsangaben** („5. Autoflower-Generation") werden als eigene Zeile in
  „Auf einen Blick" geführt, wenn der Züchter sie nennt.
- **Packungsgrößen**: 4 und 7 Stück (Standard), 5 Stück (US-Genetik-Linie),
  10 Stück (Mischpakete).
- **Mischpakete** (Sweet Mix, Sweet Mix Auto, Terp Explosion Mix, Delicious &
  Resinous Mix Auto) erhalten den Tag `Mischpaket`; Aroma und Wirkung werden
  ausdrücklich als nicht einzeln angebbar ausgewiesen.

Konkrete THC-Punktwerte von Sweet Seeds, die in die Texte übernommen wurden:
28–32 % Pineapple Fruz · 26–32 % Super Boof x RS11 · 24–30 % Mimosa x Chimera #3 ·
23–30 % Permanent Marker XL Auto · 22–30 % Mental Rainbow F1 Fast ·
20–28 % Permanent Jealousy XL Auto · 20–26 % Gelonade · 20–25 % Studio 54 Stardust Auto ·
19–22 % Garlic Icing x Chimera #3.

Bei **Monster Maker** nennt Sweet Seeds keinen Zahlenwert („high THC content") –
dort steht deshalb ausdrücklich „keine konkrete Angabe des Züchters".

## TerpyZ-Besonderheiten

TerpyZ Mutant Genetics züchtet bewusst auf Blattanomalien und ungewöhnliche
Wuchsformen. Das wird in den Texten als Absicht benannt, nicht als Defekt:

- **Duckweb** – Schwimmhaut-Blattform (Pink Nova, Quackberry Rose)
- **Fern** – farnartige Blattstruktur (Mentha de Croco Fern-Linie)
- **SWAG** – fixierte Blattanomalie der SWAG-Linien (Zen-X Swag, Zfuel Swag)
- **Variegated** – Panaschierung; im Hinweistext wird erwähnt, dass panaschierte
  Pflanzen weniger Chlorophyll haben und deshalb langsamer wachsen
- **GPP** – Linien mit ungewöhnlichen Blütenformen (Ed Rosenbud u. a.)

14 von 18 Sorten sind **regulär** (männlich und weiblich) – der Hinweistext sagt
das ausdrücklich und nennt Zuchtzwecke als Einsatzgebiet; bei F2-, F3- und
BX-Generationen steht zusätzlich „ausdrücklich für Züchtungsarbeit gedacht".
Nur 4 Sorten sind feminisiert (Gary & Gas, Mentha de Croco Fern BX1 F2,
Neon Wasabi, Super Menthol Haze, Zitro).

Bei den 5 feminisierten Sorten nennt TerpyZ **keine THC- und CBD-Werte** –
dort steht durchgehend „keine Angabe des Züchters", nie eine geschätzte Zahl.
Neon Wasabi hat zusätzlich keine Blütezeitangabe.

Die **Mentha de Croco (MDC)** ist das Kernprojekt der Marke und Elternteil
nahezu aller feminisierten Sorten – das wird in den Texten als Zusammenhang
benannt.

## Variante für dünne Datenlagen

Wenn ein Züchter nur wenige Felder dokumentiert, werden die Fehlstellen **nicht**
als Reihe von „keine Angabe"-Zeilen in „Auf einen Blick" geführt – das liest sich
wie ein kaputtes Datenblatt. Stattdessen:

- „Auf einen Blick" listet nur, was tatsächlich belegt ist, plus die Packungsgrößen.
- Ein Satz unter „Hinweise" benennt die Lücke vollständig und namentlich:
  „Zu Blütezeit, Ertrag, Wuchshöhe, Aroma und Wirkung macht <Marke> bei dieser
  Sorte keine Angaben. Wir tragen hier nur ein, was der Züchter dokumentiert,
  und erfinden keine Werte."
- Einzelne fehlende Kernwerte (THC, CBD, Kreuzung) bleiben als eigene Zeile mit
  „keine Angabe des Züchters" stehen, weil Kunden dort gezielt hinsehen.

So angewandt bei Amsterdam Genetics.

## Amsterdam-Genetics-Besonderheiten

Der Export liefert hier Genetik (36/36), THC (34/36) und CBD (32/36), aber
**keine** Blütezeit, Ertrag, Wuchshöhe, Aroma, Wirkung oder Indica/Sativa-Einordnung.
Entsprechend tragen die Texte auch keinen Indica-/Sativa-/Hybrid-Tag.

Stärke der Marke ist die tief aufgeschlüsselte Genetik – oft bis in die zweite
Generation (etwa „Tahoe OG (Tahoe OG #1 x Tahoe OG #2) x OG Kush (Chem Dawg x
Lemon Thai)"). Die Texte nutzen das und benennen zusätzlich die internen
Zusammenhänge: White Choco ist Stammsorte von fünf weiteren Sorten, Lemon Ice
und Kosher Tangie Kush laufen in der Lemongrass zusammen, Skywalker Saga und
Grapefruit Superstar in der Skyrocket.

Sonderfälle:
- **Compromise CBD Version 1 und 2**: Kreuzung laut Lieferant „Undisclosed" –
  steht als „keine Angabe des Züchters" im Text. Worin sich die beiden Versionen
  unterscheiden, ist ebenfalls nicht dokumentiert und wird so benannt.
- **Blue Monkey CBD**: als CBD-Sorte geführt, THC 0–4 %, aber ohne CBD-Wert.
- **Amnesia Haze regulär** und **Critical Mass regulär**: CBD dokumentiert,
  THC nicht.
- **Quicksilver**: THC dokumentiert, CBD nicht.

## Neues Feld: Klimazone

Sensi Seeds gibt für jede Sorte eine Klimazone an. Das Feld wird in „Auf einen
Blick" als eigene Zeile geführt, weil es für den Außenanbau in Deutschland die
eigentliche Kaufinformation ist. Drei Werte kommen vor:

- **kühles / kaltes Klima** – reift auch in nördlichen Lagen draußen aus
- **gemäßigtes / kontinentales Klima**
- **sonniges / mediterranes Klima**

Ein passendes globales Produktattribut existiert im Shop noch nicht; die Angabe
steht deshalb nur im Text. Ein Attribut dafür anzulegen wäre ein eigener Schritt
und ist nicht entschieden.

## Sensi-Seeds-Besonderheiten

**Sensi Seeds veröffentlicht grundsätzlich keine THC- und CBD-Prozentwerte** –
weder im Tiger-One-Export (THC 3/100, CBD 0/100) noch auf der eigenen Website.
Der Züchter arbeitet durchgehend mit Kategorien. In allen 100 Texten steht
deshalb „Sensi Seeds nennt keine Prozentwerte" statt einer Zahl.

Die vier Stellen auf sensiseeds.com, an denen überhaupt eine Prozentzahl neben
„THC" stand, waren unbrauchbar und wurden verworfen: zwei fremdsprachige
Textreste aus Nutzerbewertungen, einmal der Sativa-Anteil, einmal ein
Satzfragment. Solche Fundstücke gelten nicht als Züchterangabe.

Einzige Ausnahme im ganzen Sortiment: **Satin Black Domina CBD** mit einem
dokumentierten Verhältnis THC:CBD von 2:1 und rund 8 Wochen Blüte indoor –
beides aus dem Marketingtext des Exports.

### Recherche auf sensiseeds.com

Die Sortendaten stammen von der Website des Züchters (`*.sensiseeds.com`, in der
Netzwerk-Policy der Arbeitsumgebung freigegeben). 98 von 100 Produkten wurden
über die Sitemap auf ihre Sortenseite gemappt und jede Seite nach dem Abruf
gegen den Seitentitel geprüft; falsche Zuordnungen wurden verworfen statt
übernommen. Damit ergibt sich:

| Feld | Export | nach Recherche |
|---|---|---|
| Indica/Sativa | 23 | 98 |
| Blütezeit (Kategorie) | 18 | 98 |
| Ertrag (Kategorie) | 22 | 96 |
| Wuchshöhe | 20 | 96 |
| Klimazone | – | 98 |
| Aroma | 6 | 94 |
| Wirkung | 2 | 90 |

Aroma und Wirkung wurden aus den Sortenbeschreibungen des Züchters abgeleitet
und in die Hausbegriffe übersetzt; die Texte sind selbst formuliert, nicht
übernommen.

Zwei Produkte haben beim Züchter keine eigene Sortenseite und wurden bewusst
**nicht** auf eine fremde Seite gemappt: **Satin Black Domina CBD** (nicht die
normale Black Domina) und das Mischpaket **Mixed**.

### Kategorien des Züchters, wie sie in den Texten stehen

- Ertrag: mittel · groß · üppig · XXL
- Blütezeit: kurz · durchschnittlich · lang · extralang
- Wuchshöhe: kompakt · durchschnittlich · hoch

Auffälligkeiten, die so übernommen und als Planungshinweis gekennzeichnet sind:
**Caramellow Kush Auto** und **Honey Melon Kush Auto** führt Sensi als
Autoflower mit langer Blütezeit, **Sensi Amnesia XXL Auto** sogar mit
extralanger – für Autos untypisch, aber so dokumentiert.

### Sortenstruktur

15 Sorten liegen als feminisierte und als reguläre Version vor; die Texte
unterscheiden beide und nennen bei regulären Samen ausdrücklich den Zuchtzweck.
Wo der Lieferant bei einer der beiden Versionen die vollständigere Genetik
angibt (etwa Big Bud, Jack Herer, Silver Haze, Sensi Skunk), wird das im Text
mit Quellenhinweis ergänzt statt einfach übertragen.

Zwei Lieferanten-Datenfragen bleiben offen und sind in den Texten benannt:
**California Indica** (feminisiert) und **Californian Indica** (regulär) haben
abweichende Genetikangaben; **NL#5 x Haze** und **Northern Lights #5 x Haze**
sind dieselbe Kreuzung unter zwei Namen.

## Buddha-Seeds-Besonderheiten

Buddha Seeds ist die erste Marke dieser Reihe, bei der die Züchterseite
**echte Zahlen** liefert – anders als Sensi Seeds. Recherchiert wurde auf
`buddhaseedbank.com/de/producto/<slug>/` (curl + eigener Parser, 1,2 s Pause,
33 Seiten für 35 Artikelnummern).

Abdeckung nach der Recherche (Export → Züchter):

| Feld | Export | nach Recherche |
| --- | --- | --- |
| Genetik/Genanteile | 25 | 35 |
| THC-Band | 15 | 15 (Züchter nennt keine Prozente) |
| CBD | 0 | 1 (Medikit: über 20 % CBD bei rund 1 % THC) |
| Blütezeit (Tage) | 0 | 16 (alle photoperiodischen Sorten) |
| Erntemonat | 0 | 16 |
| Ertrag indoor (g/m²) | 0 | 35 |
| Ertrag outdoor (g/Pflanze) | 0 | 35 |
| Aroma | 0 | 34 |
| Wirkung | 0 | 35 |
| Wuchshöhe | 0 | 1 (Deimos, rund 1 m) |

Regeln, die daraus entstanden sind:

- **Ertrag wird getrennt geführt:** `Ertrag indoor` in g/m², `Ertrag outdoor`
  in g bzw. kg **je Pflanze** – so gibt es der Züchter an.
- **Erntemonat** als eigene Zeile, nur die Nordhalbkugel-Angabe
  („Zweite Oktoberhälfte (Nordhalbkugel)").
- **Autoflower ohne Laufzeit:** Buddha Seeds nennt für die meisten Autos keine
  Tage ab Keimung. Ausnahmen mit echter Angabe: Magnum Auto (rund 85 Tage
  Gesamtzyklus), Assorted Mix Auto (50–55 bis 80–85 Tage). Sonst steht der
  Hinweis, dass der Züchter dazu nichts sagt.
- **Spanische Resttexte** auf der Züchterseite (Aroma war teils unübersetzt)
  wurden ins Deutsche übertragen, Maschinenübersetzungs-Fehler korrigiert
  („Narkotika und Arzneimittel" → „narkotisch und medizinisch orientiert").
- **Keine Indica-/Sativa-Prozente:** Buddha Seeds nennt nur Kategorien
  (Sativa, Indica, Indica-dominanter Hybrid) – Attribute 17/18 bleiben leer.

### Widersprüche zwischen Export und Züchter (dokumentiert, nicht stillschweigend geändert)

| Produkt | Befund |
| --- | --- |
| Kraken (44979) | Züchter: **photodependent**, 58–63 Tage Blüte, Ernte Ende September. Export: `Autoflowering`. Text und Attribute folgen dem Züchter, der Widerspruch steht unter „Hinweise". Produktname enthält kein „Auto". |
| Quasar Auto (44991) | Buddha Seeds führt Quasar **nur photoperiodisch**. Die Artikelnummer `BS-SBSF050001-10` (10 Samen, VK 70) ist zusätzlich `discontinued='y'`. 44993 „Quasar" (3 Samen) ist die echte Sorte. → Dublette, Deaktivierung empfohlen. |
| Deimos regulär (44975) | Züchter listet Deimos **nur feminisiert**. Die regulären Samen (`BS-BDR10`) haben keine Züchterseite; Werte stammen von der feminisierten Sortenseite, was im Text steht. |
| Purple Kush / Medikit CBD | Jeweils **zwei eigene Züchterseiten** (auto + photoperiodisch) mit unterschiedlichen Zahlen → legitime Paare, keine Dubletten. |

### Nicht gelistete Buddha-Sorten

Der Züchter führt zusätzlich `Pulsar`, `Morpheus`, `Panakeia` (hohe Terpene),
`Gorilla Auto`, `Assortierte Klassiker` und `Auto Diesel` – diese Sorten fehlen
in der Tiger-One-Liste und wären eine mögliche Sortimentslücke.

## Serious-Seeds-Besonderheiten

Serious Seeds hat die mit Abstand beste Datenlage aller Marken dieser Reihe.
Recherchiert wurde auf der deutschen Züchterseite
`seriousseeds.com/de/hanfsamen/<slug>` (curl + eigener Parser, 19 Sortenseiten
für 26 Artikelnummern, 16 Sorten).

Der Export war dagegen fast leer: von 42 Serious-Zeilen hatten 39 eine
Genetikangabe, aber nur 2 eine Blütezeit, 2 einen Ertrag, 0 eine Wuchshöhe,
0 Effekte. Nach der Recherche sind alle 26 Produkte vollständig.

### Zusätzliche Felder, die nur Serious Seeds liefert

| Feld | Inhalt |
| --- | --- |
| `Veg. Phase empfohlen` | Zeit von Keimung bis Umstellung auf 12/12, z. B. „2,5–4 Wochen" |
| `Blütezeit indoor` | Wochen plus, wo der Züchter sie nennt, Tage: „8–9 Wochen (53–63 Tage)" |
| `Erntezeit outdoor` | konkretes Datumsfenster, z. B. „Mitte/Ende Oktober" |
| `Auszeichnungen` | Zahl der Cups laut Züchter (AK-47: 27, Kali Mist: 16, Bubble Gum: 12) |

### Konkrete THC-/CBD-Werte (Züchter- bzw. Laborangaben)

| Sorte | Werte |
| --- | --- |
| Seriotica | 25–28 % THC — höchster Wert im Sortiment |
| White Russian (+ regulär) | 22 % THC / 1 % CBD |
| AK-47 (+ regulär) | 20 % THC / 1 % CBD, Labortest 1999: 21,5 % |
| Warlock | 20 % THC / 1 % CBD |
| Strawberry Akeil | AK-47-Linie: 20 % / 1 % |
| Fruity Durban | 18–20 % THC, kein CBD |
| Serious 6 (+ regulär) | 17 % THC im CANNA-Labor 2013 |
| White Russian Auto | 17 % THC im CANNA-Labor (erste Charge) |
| CBD-Chronic | Labordurchschnitt 5,4 % THC : 5,8 % CBD, beste Pflanze 7,88 % : 6,93 %, alle 10 Proben 1:1 |
| CBD-Warlock | Labordurchschnitt ~8 % THC : 4 % CBD, beste Pflanzen 16,86 % : 15,58 %, 9 von 24 Proben 2:1 |

Wo der Züchter nur Kategorien nennt („sehr hoch", „hoch", „mittel", „niedrig",
„keiner"), steht die Kategorie mit dem Zusatz „(Züchterkategorie)" und der
ausdrückliche Hinweis, dass kein Prozentwert vorliegt.

### Widersprüche und Besonderheiten

- **Seriotica:** Züchter 25–28 % THC, Lieferantenband nur 16–24 %. Beide Werte
  stehen im Text, jeweils mit Quelle. Attribut `THC Gehalt` = `25 % und mehr`.
- **Chronic:** Die Züchterseite widerspricht sich selbst (Spalte „9–10 Wochen",
  Zahlenblock „53–63 Tage"). Wir übernehmen nur die Wochenangabe.
- **Serious 6:** Serious Seeds empfiehlt die Indoor-Blüte **nur erfahrenen
  Growern**, weil laut Züchter 2 von 100 Pflanzen auf Lichtstress mit
  Hermaphroditismus reagieren. Das steht so im Text — im Freiland kein Thema.
- **Reguläre Samen:** Sieben Artikelnummern sind regulär (AK47, Bubble Gum,
  Chronic, Kali Mist, Serious 6, Serious Happiness, White Russian). Sie bekommen
  denselben Datenblock, aber eine eigene Einleitung zur Zuchtarbeit und den Tag
  `Zuchtsamen`.
- **Auslaufend beim Lieferanten:** Double Dutch (44283), Motavation (44291),
  Warlock (44311) sind als `discontinued` markiert; der Hinweis steht im Text.
- **Limitierte Editionen:** Strawberry Akeil und CBD-Warlock führt der Züchter
  ausdrücklich als limitiert.
- **Für deutsche Freilandlagen interessant:** Serious 6 (selbst im Norden Ende
  September fertig), Fruity Durban (6 Wochen Blüte, Mitte September), Seriosa
  und Seriotica (Mitte September). Kali Mist dagegen erst **Ende November** —
  im Text klar als Indoor-Sorte eingeordnet.

### Nicht gelistete Serious-Seeds-Sorten

Der Züchter führt zusätzlich `Biddy Early`, `Kali Bubba` und `Serious 7` —
diese drei fehlen in der Tiger-One-Liste.

## „discontinued" ist kein Bestandssignal

Festgelegt am 2026-09-11: Das Feld `discontinued` im Tiger-One-Export heißt
**nicht** ausverkauft. Die Sorten bleiben lieferbar, solange Bestand da ist — und
gerade alte, auslaufende Sorten sind oft besonders gefragt.

Daraus folgt:

- Auslaufende Artikel werden **gelistet und veröffentlicht** wie alle anderen.
  Sie werden nicht ausgeblendet, nicht deaktiviert und nicht auf „ausverkauft"
  gesetzt, nur weil die Markierung gesetzt ist.
- Eine Deaktivierung braucht immer einen eigenen Grund — etwa eine Dublette
  (Quasar Auto 44991) oder eine fehlende Lieferbarkeit aus dem Lagerbestand.
- Das Lagerbestands-Plugin wertet `discontinued` deshalb nicht aus. Der einzige
  belastbare Bestandswert kommt aus dem Stock Feed des Tiger-One-ERP.
- In den Listungsberichten bleibt die Zahl der markierten Artikel als Hinweis
  stehen, ist aber keine offene Entscheidung mehr.

Damit sind die früher als „offen" geführten Punkte zu Amsterdam Genetics (26),
Buddha Seeds (14), Nirvana (27), Pyramid (18), Ripper (5) und Silent (11)
geklärt: Alle bleiben gelistet.

## Silent-Seeds-Besonderheiten

Der Tiger-One-Export ist bei Silent Seeds sehr dünn (Blütezeit, Ertrag, Höhe,
Aroma, Effekte durchgehend leer, bei 5 Sorten fehlt sogar die Genetik). Quelle
der Texte ist deshalb die **deutsche Züchterseite silent-seeds.de**: 45 Sortenseiten
wurden geladen und ihr Datenblatt (Geschlecht, Genotyp, Cross, THC, CBD, Blütezeit
in Innenräumen, Produktion in Innenräumen, Außenproduktion, Ernte im Freiland,
Höhe im Freien, Umwelt, Geschmack) plus die Prosaabschnitte ausgewertet. Alle
41 Shop-Produkte haben dort eine Entsprechung.

- **Die Züchterprozente liegen oft über dem Tiger-One-Band.** Wo sich beide
  widersprechen, steht der Züchterwert zuerst und das Lieferantenband dahinter:
  „THC: 27–30 % (Züchterangabe); Lieferantenband Tiger One: 16–24 %". Betrifft
  Acai Jelly, B45, Gorilla Frost, OG Kush, Pink Sunset, Polar Gelato, Starfire OG.
- **Attribut `THC Gehalt`** kommt aus dem Züchterwert: Obergrenze über 25 % →
  `25 % und mehr`, sonst das passende Band.
- **Attribut `CBD Gehalt`** nur, wenn der Züchter einen Zahlenwert unter 1 %
  nennt. Bei „geringer Prozentsatz" (6 Sorten) und „weniger als 2 %" (7 Sorten)
  bleibt das Attribut leer, der Text sagt es ausdrücklich.
- **Autoflower:** Silent Seeds nennt auch für Autos nur das Feld „Blütezeit in
  Innenräumen" und sagt nicht, ob Blüte oder Gesamtzyklus gemeint ist. Die Zeile
  heißt deshalb „Blütezeit indoor", und die Hinweise sagen diese Unklarheit.
- **Widersprüchliche Einheit:** Bei **B45** (42573) und **Peach Cake** (42615)
  steht die Außenproduktion auf der Züchterseite in „g/m2", obwohl das Feld die
  Ernte je Pflanze meint; die Größenordnung entspricht den anderen Sorten. Im
  Text steht „1.400–1.600 g je Pflanze (auf der Züchterseite in g/m² angegeben)".
- **Moby Dick Auto** (42608) hat auf der Züchterseite keinen Wirkungsabschnitt.
  Der Wirkungstext sagt das und ordnet nur über die Genetik ein.
- **Mint Candy** (42606): Der Züchter schreibt die Kreuzung als
  „Face Off OG x 2 x Animal Face Mints". Die Schreibweise bleibt erhalten und ist
  im Text als Züchterschreibweise gekennzeichnet.
- **Maschinell übersetzte Elternnamen** der Züchterseite wurden auf die
  Originalnamen zurückgeführt (Kritisch + → Critical +, Apfelkrapfen → Apple
  Fritter, Käsekuchen → Cheese Cake, Eistorte → Ice Cream Cake,
  Tiergesichter-Minzbonbons → Animal Face Mints); wo der Export die Genetik
  saubererer führt, hat er Vorrang.
- **Sortenlinien:** `FAST Version` (Lemon Tree FAST, photoperiodisch und
  schneller), `XL Ertrag` (Alien Gas XXL Auto, Mac Dawg XXL Auto),
  `Limitierte Auflage` (Mint Candy, Rainbow Gas, Tropical Jam),
  `Julian Marley` (4 Sorten), `Sherbinskis` (5 Sorten), `Cookies` (2 Sorten).
- **Dinafem-Herkunft**: Critical Jack Auto und Moby Dick Auto führt Silent Seeds
  ausdrücklich als Originalgenetik von Dinafem Seeds – das steht so im Text.

## Ripper-Seeds-Besonderheiten

Der Tiger-One-Export ist hier dünn (THC 6/60 Zeilen, Höhe 0/60, Blütezeit 20/60).
Quelle der Texte ist deshalb **ripperseeds.com/en**: alle 26 Sortenseiten wurden
geladen und ihr Datenblatt (Plant type, Indoor flowering days bzw. Flowering from
germination, Outdoor flowering, Yield, Effect, Flavor) plus die Abschnitte
Description, Genetics, Aroma/Terpene, Effects und Morphology ausgewertet.

- **Ertrag:** Ripper Seeds nennt nur Kategorien (High, Medium to High, Very high).
  Wo der Export Zahlenbänder liefert (7 Sorten), stehen diese im Text
  („450–600 g/m²"), sonst die Züchterkategorie mit Quellenangabe.
- **Zwei neue Zeilen** in „Auf einen Blick" für diese Marke, weil der Züchter nur
  Filterstufen veröffentlicht: `Wirkungsstärke (Züchterfilter)` (sehr stark, stark,
  potent, gut beherrschbar) und `Geschmacksrichtung (Züchterfilter)` (fruchtig,
  trocken, durchdringend, säuerlich-erdig, erdiger Kush).
- **Auszeichnungen** werden aus der Award-Tabelle der Sortenseite gezählt und als
  „n Cup-Platzierungen (Züchterangabe)" geführt: Zombie Kush 25, Sour Ripper 10,
  Toxic 8, Old School 5, Criminal 4, Ripper Haze 4, Washing Machine 4, OMG 3,
  Chempie 1, DO-G 1, KmintZ 1.
- **THC:** Nur drei Sorten haben ein Lieferantenband (Ripper Haze, Toxic, Washing
  Machine → `16–24 %`), zwei Autos nennen auf der Züchterseite 15 bis 25 %
  (KmintZ Auto, Sour Ripper Auto → Attribut `16–24 %`). Bei allen anderen steht
  im Text ausdrücklich, dass Ripper Seeds keinen Prozentwert nennt, und das
  Attribut bleibt leer. **CBD nennt der Züchter nirgends.**
- **Autoflower:** Der Züchter gibt hier das Feld „Flowering from germination" an.
  Die Zeile heißt deshalb `Gesamtzyklus: … ab Keimung`, und das Attribut
  `Blütezeit (Tage)` bleibt bei Autos leer, weil der Wert keine Blütezeit ist.
- **Abweichende Zeitangaben** zwischen Datenblatt und Textteil (Shimo 50–60 Tage
  vs. 7–8 Wochen, KmintZ 60–70 Tage vs. 9 Wochen, CandyGaz 60–65 Tage vs.
  9 Wochen, Braincake 60–70 Tage vs. 65 Tage, Zake 55–60 Tage vs. 8–9 Wochen,
  Zombie Bride 60–70 Tage vs. 9 Wochen) stehen beide im Text, mit Quelle.
- **Namensabweichung:** Der Züchter schreibt **Criminal+**, der Shop führt
  „Criminal"; der Text nennt beide Schreibweisen. Gleiches Prinzip bei
  Braincake (Züchter: Brain Cake) und Break Pad Breath (Züchter: Brake Pad Breath).
- **Sortiment:** ripperseeds.com führt zusätzlich **Ripper Badazz regulär**, die
  im Shop fehlt – offener Punkt für die Sortimentsprüfung.
- **Packungsgrößen:** DO-G nur 3 Samen, Ripper Haze und Sideral nur 5 Samen,
  alle anderen 3 und 5.

## Purple-City-Genetics-Besonderheiten

Der Tiger-One-Export liefert hier **nur die Genetik** (5/5), sonst nichts. Die
eigenen Shops der Marke (purplecitygenetics.com, eu. und us.) führen diese fünf
Linien **nicht mehr**; der EU-Shop hat 42 ganz andere Sorten und gar keine
Beschreibungstexte. Quelle der Texte sind deshalb die Züchterangaben, wie sie der
europäische Fachhandel und die Sortendatenbank **SeedFinder** dokumentieren. Das
steht in jedem der fünf Texte ausdrücklich im Hinweisblock.

- **Keine THC-/CBD-Werte** dokumentiert – bei Purple #40 nur „hoher THC-Gehalt"
  ohne Zahl. Die Attribute `THC Gehalt` und `CBD Gehalt` bleiben leer.
- **Blütezeit** steht in Wochen (9–10 bzw. 9–11); die Tagesangabe in Klammern und
  das Attribut `Blütezeit (Tage)` sind daraus gerechnet (63–70 bzw. 63–77).
- **Ertrag:** Nur Purple #40 hat Zahlen (450–550 g/m², bis 800 g je Pflanze). Bei
  PCG Cookies × Watermelon Zkittlez nennt der Züchter „mittel", der Fachhandel
  500–600 g/m² und rund 700 g je Pflanze – beides steht mit Quelle im Text.
- **Drei der fünf Sorten sind regulär** (Lemon Caramel, Limón Picón, Smac Town);
  Attribut `Variante` = `Regulär`, Tag `Zuchtsamen`.
- **Alle fünf nur als 3er-Packung.**
- **Wiederkehrende Züchterhinweise:** Die Pflanzen verdoppeln in der Blüte ihre
  Höhe und brauchen Stützen; mehrere Linien färben bei kühlen Nächten violett aus.

## Pyramid-Seeds-Besonderheiten

Der Tiger-One-Export liefert nur Filterbänder und ein Genetik-Kürzel. Quelle aller
Zahlen sind deshalb die **deutschen Datenseiten von pyramidseeds.com** (`/de/`);
alle 71 Produkte konnten eindeutig einer Züchterseite zugeordnet werden. Die
Domains pyramidseeds.net und .es sind über den Egress-Proxy nicht erreichbar.

- **Zwei Lichtangaben:** Pyramid Seeds nennt jede Zeit doppelt – einmal unter HPS,
  einmal unter LED (LED jeweils rund 7 Tage kürzer). Die Zeile lautet deshalb
  „Blütezeit indoor: 55 Tage (unter LED 48 Tage)". Ins Attribut
  `Blütezeit (Tage)` geht der HPS-Wert.
- **Autos: Gesamtzyklus statt Blütezeit.** Für die selbstblühenden Linien gibt der
  Züchter „Zyklus Indoor" ab Aussaat an, nicht die Blütedauer. Die Zeile heißt
  entsprechend „Gesamtzyklus: 65 Tage ab Aussaat (unter LED 58 Tage)"; das
  Attribut `Blütezeit (Tage)` bleibt bei Autos leer.
- **Ertrag doppelt:** g/m² plus ein zweiter Wert „bis X g je 1,5 m² mit
  720-W-LED" – beides steht so im Text, weil der Züchter beides nennt.
- **Wuchshöhe indoor** gibt es fast nur für die Autos (z. B. 40–140 cm bei Purple
  Auto, bis 180 cm bei Ramses Auto); bei den Fotoperiodischen nur für Shark CBD
  (80–120 cm) und White Widow CBD (80–130 cm).
- **Kollektionen** stehen als Tag: `Bestseller` (Black Cherry Punch, Purple Auto,
  Super Hash, Tutankhamon) und `Heart Notes` – die erklärt sinnlichkeitsbezogene
  Linie mit Gorila Auto, Ice Cream und Lemon Larry OG.
- **CBD-Linien (7):** 7–12 % THC bei 10–15 % CBD, bei White Widow CBD
  (fotoperiodisch) 15–21 % CBD. Das sind **keine Nutzhanfsorten** – der Hinweis,
  dass 7–12 % THC psychoaktiv relevant bleiben, steht in jedem CBD-Text.
- **Attribut `CBD Gehalt`** kommt bei diesen Linien aus der **Unter**grenze der
  Züchterspanne (10–15 % → Band `10–15 %`), nicht aus der Obergrenze.
- **Attribut `THC Gehalt`:** Obergrenze ab 25 % → `25 % und mehr`. Betrifft 21 der
  71 Sorten, darunter Cookies USA mit 25–28 % (höchste Angabe im Katalog).
- **Extremwerte, die so im Text stehen:** Tutankhamon 26 % THC mit ausdrücklichem
  Verweis auf unabhängige Growertests bis 30 %; Tahoe Cure 25–27 % als
  „höchster THC-Gehalt der Kush-Linie"; Kukulkan bis 2 kg je Pflanze outdoor;
  Lennon als einzige Sorte mit Schwierigkeitsgrad „Experte" (80 Tage Blüte,
  Ernte erst im November).
- **Widersprüche/Lücken, die der Text benennt:** Ramses nennt 80 Tage Blüte bei
  gleichzeitig früher Septemberernte; Cookies USA Auto hat keinen LED-Zyklus und
  Northern Lights (fotoperiodisch) keinen LED-Blütewert; Gelato und Mendocino
  Purple Kush haben kein Outdoor-Erntefenster. Bei Alpujarrena, New York City,
  Purple und Romulan legt der Züchter die Elternlinien nicht offen.
- **Auto und Fotoperiodische weichen voneinander ab** – nicht nur im Zyklus: Bei
  Gelato dreht der Züchter das Genotyp-Verhältnis (Auto 55 % Sativa, Foto 55 %
  Indica) und beschreibt die Wirkung anders; bei Super OG Kush nennt er für die
  Auto den Sortennamen, für die Fotoperiodische Hindu Kush als Genetik.
- **Seltene Leitterpene:** Ocimen (nur Fresh Candy), Humulen (Ice Cream und
  Cookies USA Auto), Pinen an erster Stelle (Romulan).
- **Alle 71 in 4 und 7 Samen**, gepackt als 3+1 bzw. 5+2 – das steht in jedem
  Hinweisblock.

## Nirvana-Seeds-Besonderheiten

Nirvana ist die erste Marke, bei der die Züchterdaten nicht aus dem
Tiger-One-Export kommen, sondern aus drei Quellen mit klarer Rangfolge. Welche
Quelle ein Produkt nutzt, steht im Text selbst:

1. **Aktueller Nirvana-Webshop** (nirvanashop.com) – 54 der 67 Produkte. Die
   Datenblätter sind ausführlich: Kreuzung, THC, Blüte, Ertrag indoor/outdoor,
   Höhe, Resistenz, Schwierigkeitsgrad, Aroma und Wirkung.
2. **Archivierte Nirvana-Datenblätter** (SeedFinder-Sortenseiten für
   `nirvana-seeds`) – 10 Linien, die der Züchter nicht mehr selbst listet:
   Blackberry, GG-48, GG-48 Auto, Hawaii Maui Waui Auto, Ice (fem + reg), K2,
   Short Rider Auto, Somango XXL Auto, Top 44, White Rhino. Jeder dieser Texte
   sagt im ersten Absatz, dass die Angaben aus dem archivierten Datenblatt
   stammen, und bleibt auf das beschränkt, was dort steht (teils ohne THC-Wert,
   ohne Außenertrag oder nur mit Züchterkategorien wie „kurz“ oder „mittel“).
3. **Keine Züchterdaten** – 7 Artikel. Vier Autoflower-Versionen ohne eigenes
   Datenblatt (Top 44 Auto, Skunk #1 Auto, Orange Bud Auto, Raspberry Cough
   Auto) führen Aroma und Wirkung der photoperiodischen Linie und sagen das
   ausdrücklich; drei Artikel ohne dokumentierte Linie (Amnesia feminisiert,
   Amnesia Auto, Blueberry regulär) nennen nur den Samentyp.

Weitere Festlegungen:

- **Züchterkategorien statt Zahlen** werden als solche gekennzeichnet:
  `THC: hoch (Kategorie, ohne Zahlenwert)`, `Wuchshöhe: kurz (Züchterkategorie)`,
  `Ertrag indoor: mittel bis hoch (Züchterkategorie)`.
- **Zwei Packungsgrößen**: feminisiert und autoflowering als 5er, regulär als
  10er. Der Hinweis-Absatz nennt die Packung.
- **Reguläre Linien** bekommen den Tag `Zuchtsamen` und einen Hinweis zur
  Zuchtnutzung (Männchen separieren, Mutterpflanzen, Pollenlagerung).
- **Cup-Angaben** nur, wenn der Züchter sie selbst nennt: Ice trägt
  `Cup-Gewinner` (Nirvana schreibt den Sieg beim Cannabis Cup 1998 im eigenen
  Datenblatt). White Widow bekommt den Tag nicht, weil Nirvana dazu nichts
  veröffentlicht.
- **Tag-Auffüllung**: Wo Marke, Sorte, Samentyp und Klasse weniger als sechs
  Tags ergeben (Linien ohne THC-Wert), werden Elternsorten aus der
  Genetik-Angabe ergänzt. Einzige Ausnahme mit fünf Tags: Amnesia feminisiert –
  dort gibt es keine belegte Elternsorte.
- **Nirvana nennt für Autos `Gesamtzyklus` ab Aussaat**, nicht Blütezeit; das
  Attribut `Blütezeit (Tage)` (21) bleibt bei Autos deshalb leer.
