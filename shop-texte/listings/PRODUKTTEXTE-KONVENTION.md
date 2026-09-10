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
| Sweet Seeds | 99 | offen |
| TerpyZ Mutant Genetics | 18 | offen |
| Sensi Seeds | 97 | offen (dünne Datenlage) |
| Amsterdam Genetics | 36 | offen (dünne Datenlage) |
| Buddha Seeds | 35 | offen (dünne Datenlage) |
| Serious Seeds | 26 | offen (dünne Datenlage) |

Bei Seedsman wurde zusätzlich ein unsauberer Produktname korrigiert:
ID 43483 → „Seedsman Relax Collection feminisiert (3 × 1 Samen)".
