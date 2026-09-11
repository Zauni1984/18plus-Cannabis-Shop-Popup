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
| Sensi Seeds | 97 | offen (dünne Datenlage) |
| Amsterdam Genetics | 36 | offen (dünne Datenlage) |
| Buddha Seeds | 35 | offen (dünne Datenlage) |
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
