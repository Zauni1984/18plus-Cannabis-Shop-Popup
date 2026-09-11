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
| Serious Seeds | 26 | offen (dünne Datenlage) |

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
