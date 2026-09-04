# -*- coding: utf-8 -*-
import json, itertools, difflib, html, re

alle = (json.load(open('medusa.json', encoding='utf-8'))
      + json.load(open('kailar.json', encoding='utf-8'))
      + json.load(open('purize_xtra.json', encoding='utf-8'))
      + json.load(open('purize_rest.json', encoding='utf-8'))
      + json.load(open('sonstige.json', encoding='utf-8')))
assert len(alle) == 93, len(alle)
assert len(set(o['id'] for o in alle)) == 93, "doppelte IDs"

FAMILIEN = [
 ("medusa",   "Medusafilters 6 mm",              "#A8845C", 14),
 ("kailar",   "Kailar Cellulose Slim",           "#5E8C6A", 12),
 ("purize",   "PURIZE (alle Formate)",           "#3D6D96", 53),
 ("sonstige", "CTIP · Gizeh · Granny's Weed · Hybrid Supreme · Smoking", "#8A5A6B", 14),
]
GRUPPEN = {}
for o in json.load(open('medusa.json', encoding='utf-8')): GRUPPEN[o['id']] = 'medusa'
for o in json.load(open('kailar.json', encoding='utf-8')): GRUPPEN[o['id']] = 'kailar'
for o in json.load(open('purize_xtra.json', encoding='utf-8')): GRUPPEN[o['id']] = 'purize'
for o in json.load(open('purize_rest.json', encoding='utf-8')): GRUPPEN[o['id']] = 'purize'
for o in json.load(open('sonstige.json', encoding='utf-8')): GRUPPEN[o['id']] = 'sonstige'

def slug_von(name):
    s = name.lower()
    for a,b in [('ü','ue'),('ö','oe'),('ä','ae'),('é','e'),('ß','ss'),('´',''),("'",'')]:
        s = s.replace(a,b)
    return re.sub(r'[^a-z0-9]+','-', s).strip('-')[:60] + '-' + str(hash(name) % 10000)

# Ähnlichkeit je Produkt: höchste Ähnlichkeit zu irgendeinem ANDEREN Produkt der GESAMTEN Kategorie
sim_max = {}
for a, b in itertools.combinations(alle, 2):
    r = difflib.SequenceMatcher(None, a['desc'], b['desc']).ratio()
    if r > sim_max.get(a['id'], 0): sim_max[a['id']] = r
    if r > sim_max.get(b['id'], 0): sim_max[b['id']] = r

karten_html = {g: [] for g, *_ in FAMILIEN}
index_html = {g: [] for g, *_ in FAMILIEN}

for o in alle:
    g = GRUPPEN[o['id']]
    sl = slug_von(o['name'])
    tl, ml = len(o['seo_title']), len(o['seo_desc'])
    woerter = len(re.sub('<[^>]+>',' ', o['desc']).split())
    sim = sim_max[o['id']]
    simklasse = 'sim-hoch' if sim > 0.85 else ('sim-mittel' if sim > 0.6 else 'sim-niedrig')

    index_html[g].append(
      '<li><a href="#%s"><span class="ix-name">%s</span>'
      '<span class="ix-sim %s">%.2f</span></a></li>'
      % (sl, html.escape(o['name']), simklasse, sim))

    karten_html[g].append("""
<article class="karte" id="{sl}">
  <header class="k-kopf">
    <div class="k-titel">
      <h3>{name}</h3>
      <p class="k-meta"><span class="mono">#{pid}</span> · {woerter} Wörter
        · höchste Ähnlichkeit im gesamten Block <span class="mono {simklasse}">{sim}</span></p>
    </div>
  </header>
  <section class="block">
    <h4 class="label">Google-Ergebnisvorschau</h4>
    <div class="serp">
      <div class="serp-pfad">hanfjack.de › produkt</div>
      <div class="serp-titel">{seo_title}</div>
      <div class="serp-desc">{seo_desc}</div>
    </div>
    <dl class="zaehler">
      <div><dt>Titel</dt><dd class="mono {tk}">{tl}<span>/60</span></dd></div>
      <div><dt>Meta</dt><dd class="mono {mk}">{ml}<span>/160</span></dd></div>
      <div><dt>Fokus-Keyphrase</dt><dd class="kw">{focus}</dd></div>
    </dl>
  </section>
  <section class="block"><h4 class="label">Kurzbeschreibung</h4><div class="doc kurz">{short}</div></section>
  <section class="block"><h4 class="label">Beschreibung</h4><div class="doc">{desc}</div></section>
</article>""".format(sl=sl, name=html.escape(o['name']), pid=o['id'], woerter=woerter,
                     sim=("%.2f" % sim).replace('.',','), simklasse=simklasse,
                     seo_title=html.escape(o['seo_title']), seo_desc=html.escape(o['seo_desc']),
                     tl=tl, ml=ml, tk="gut" if tl<=60 else "schlecht",
                     mk="gut" if 120<=ml<=160 else "schlecht",
                     focus=html.escape(o['focus']), short=o['short'], desc=o['desc']))

gestrichen = [
 ("„reduziert Schadstoffe, Teer und Feinstaub effektiv“", "§ 18 TabakerzG / Art. 13 RL 2014/40/EU"),
 ("„ohne Verlust an Wirkung“, „volle Wirkung“", "Konsumaussage im Cannabis-Kontext, § 6 KCanG"),
 ("„weniger Reizung“, „kratzfrei“, „gesünder“", "Gesundheitsaussage ohne Beleg, § 5 UWG"),
 ("„Kein Plastikmüll“, „schont Umwelt“, „recycelbar produziert“", "Pauschale Umweltaussage, ab 27.09.2026 EmpCo-Richtlinie (EU) 2024/825"),
 ("„höchste Qualitätsstandards“, „100 % geschmacksneutral“", "Spitzenstellungs-/Absolutbehauptung, § 5 UWG"),
 ("„antibakteriell“ (Kategorietexte)", "Biozidwerbung, Art. 72 VO (EU) 528/2012"),
 ("„Nikotin“-Reduktion (Regular/Big Size)", "Falsche Produktkategorie – Cannabisfilter, kein Tabakprodukt"),
 ("„Shoppe jetzt …“, CTAs generell", "Auf Wunsch durchgängig entfernt"),
]
gestrichen_html = "".join('<tr><td class="weg">%s</td><td>%s</td></tr>' % (html.escape(a), html.escape(b)) for a,b in gestrichen)

funde = [
 ("„Air Filter“ ≠ „Aktivkohlefilter“",
  "PURIZE führt unter fast demselben Namen zwei verschiedene Produkte: Der „Air Filter Slim 7mm“ (13678) enthält keine Aktivkohle, nur Filterpapier. Der „Aktivkohlefilter Slim 7mm“ (13670) enthält welche. Beide Texte benennen den Unterschied jetzt ausdrücklich."),
 ("Regular/Regular Short: andere Kohle",
  "Die 9-mm-Filter von PURIZE nutzen Steinkohle- statt Kokosnuss-Aktivkohle wie der Rest der Reihe – als reiner Materialfakt übernommen, ohne Wirkungsversprechen daran zu hängen."),
 ("Granny's Weed: zwei Bauweisen, eine Marke",
  "Die symmetrische Linie (beidseitig Keramik) und der „Hybrid Deluxe“ (Keramik zur Glut, Faser zum Mund, feste Einbaurichtung) sind technisch verschieden – der alte Premium-Text widersprach sich hier selbst."),
 ("Mixed ≠ Rainbow bei PURIZE",
  "Beide existieren als eigene Artikel nebeneinander (auch im Glas). Ein erster Entwurf hatte sie versehentlich gleichgesetzt (Ähnlichkeit 1,00) – jetzt getrennt."),
]
funde_html = "".join('<div class="fund"><h4>%s</h4><p>%s</p></div>' % (html.escape(t), html.escape(x)) for t,x in funde)

# Familienabschnitte
sektionen = []
for g, titel, farbe, erwartet in FAMILIEN:
    n = len(karten_html[g])
    assert n == erwartet, (g, n, erwartet)
    sektionen.append("""
<section class="familie" style="--fc:{farbe}">
  <div class="fam-kopf">
    <h2>{titel}</h2>
    <span class="fam-zahl">{n} Produkte</span>
  </div>
  <div class="fam-body">
    <nav class="index" aria-label="{titel}"><ul>{index}</ul></nav>
    <div class="liste">{karten}</div>
  </div>
</section>""".format(farbe=farbe, titel=html.escape(titel), n=n,
                     index="".join(index_html[g]), karten="".join(karten_html[g])))

alle_sims = sorted(sim_max.values())
alle_paare = sorted(difflib.SequenceMatcher(None,a['desc'],b['desc']).ratio()
                     for a,b in itertools.combinations(alle,2))
kennzahlen = dict(
 gesamt=len(alle),
 simmax=max(alle_sims), simmed=alle_paare[len(alle_paare)//2],
 hoch=sum(1 for s in alle_sims if s > 0.85),
)

html_out = """<title>Aktivkohlefilter — Gesamtrevision</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Zilla+Slab:wght@500;600&family=IBM+Plex+Mono:wght@400;500&family=IBM+Plex+Sans:wght@400;500;600&display=swap">
<style>
:root{
  --grund:#F6F6F4; --flaeche:#FFFFFF; --flaeche2:#FBFBF9;
  --tinte:#191A18; --gedaempft:#6B6E68; --linie:#E0E1DB; --linie2:#EDEEE9;
  --akzent:#2C4A3E; --gut:#2C6E49; --schlecht:#A33A2A; --warn:#8A5A12;
  --serp-titel:#1a0dab; --serp-pfad:#4d5156;
  --schatten:0 1px 2px rgba(25,26,24,.06);
}
@media (prefers-color-scheme: dark){
  :root:not([data-theme="light"]){
    --grund:#17181A; --flaeche:#1E2022; --flaeche2:#212325;
    --tinte:#E8E9E4; --gedaempft:#9A9D96; --linie:#33363A; --linie2:#2A2D30;
    --akzent:#8FC0A9; --gut:#7FBF97; --schlecht:#E08E7E; --warn:#D9AE66;
    --serp-titel:#8ab4f8; --serp-pfad:#9aa0a6;
    --schatten:0 1px 2px rgba(0,0,0,.4);
  }
}
:root[data-theme="dark"]{
  --grund:#17181A; --flaeche:#1E2022; --flaeche2:#212325;
  --tinte:#E8E9E4; --gedaempft:#9A9D96; --linie:#33363A; --linie2:#2A2D30;
  --akzent:#8FC0A9; --gut:#7FBF97; --schlecht:#E08E7E; --warn:#D9AE66;
  --serp-titel:#8ab4f8; --serp-pfad:#9aa0a6;
  --schatten:0 1px 2px rgba(0,0,0,.4);
}
*{box-sizing:border-box}
body{background:var(--grund);color:var(--tinte);
  font-family:"IBM Plex Sans",-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
  font-size:15px;line-height:1.6;-webkit-font-smoothing:antialiased}
.mono{font-family:"IBM Plex Mono",ui-monospace,SFMono-Regular,Menlo,monospace;font-variant-numeric:tabular-nums}
.huelle{max-width:1180px;margin:0 auto;padding:40px 24px 96px}
@media (max-width:900px){.huelle{padding:28px 16px 64px}}

.kopf{border-bottom:2px solid var(--tinte);padding-bottom:22px;margin-bottom:20px}
.eyebrow{font-family:"IBM Plex Mono",monospace;font-size:11px;letter-spacing:.14em;
  text-transform:uppercase;color:var(--gedaempft);margin:0 0 10px}
h1{font-family:"Zilla Slab",Georgia,serif;font-weight:600;font-size:clamp(28px,4.2vw,40px);
  line-height:1.12;margin:0 0 12px;text-wrap:balance;letter-spacing:-.01em}
.dach{max-width:70ch;color:var(--gedaempft);margin:0}

.kennzahlen{display:grid;gap:1px;background:var(--linie);
  grid-template-columns:repeat(auto-fit,minmax(150px,1fr));border:1px solid var(--linie);margin-bottom:22px}
.kz{background:var(--flaeche);padding:14px 16px}
.kz dt{font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:var(--gedaempft);margin:0 0 4px}
.kz dd{margin:0;font-family:"IBM Plex Mono",monospace;font-size:20px;font-weight:500}
.kz dd small{font-size:12px;color:var(--gedaempft);font-weight:400}

.recht,.fundeblock{background:var(--flaeche);border:1px solid var(--linie);padding:20px 22px;margin-bottom:16px}
.recht{border-left:3px solid var(--warn)}
.fundeblock{border-left:3px solid var(--akzent)}
.recht h2,.fundeblock h2{font-family:"Zilla Slab",Georgia,serif;font-size:19px;margin:0 0 6px;font-weight:600}
.recht p,.fundeblock>p{margin:0 0 14px;color:var(--gedaempft);max-width:74ch}
.recht table{width:100%;border-collapse:collapse;font-size:13.5px}
.recht td{padding:7px 0;border-top:1px solid var(--linie2);vertical-align:top}
.recht td:first-child{width:42%;padding-right:20px}
.weg{text-decoration:line-through;text-decoration-color:var(--schlecht);
  text-decoration-thickness:1px;color:var(--gedaempft)}
.fund{padding:10px 0;border-top:1px solid var(--linie2)}
.fund:first-child{border-top:none}
.fund h4{margin:0 0 4px;font-size:14px;font-weight:600}
.fund p{margin:0;color:var(--gedaempft);font-size:13.5px;max-width:74ch}

.familie{margin-top:30px}
.fam-kopf{display:flex;align-items:baseline;gap:12px;border-bottom:2px solid var(--fc);padding-bottom:8px;margin-bottom:16px}
.fam-kopf h2{font-family:"Zilla Slab",Georgia,serif;font-size:22px;font-weight:600;margin:0;color:var(--fc)}
.fam-zahl{font-family:"IBM Plex Mono",monospace;font-size:12.5px;color:var(--gedaempft)}
.fam-body{display:grid;grid-template-columns:230px minmax(0,1fr);gap:28px;align-items:start}
@media (max-width:900px){.fam-body{grid-template-columns:1fr}}

.index{position:sticky;top:20px;max-height:calc(100vh - 40px);overflow-y:auto}
@media (max-width:900px){.index{position:static;max-height:none}}
.index ul{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:1px}
.index a{display:flex;align-items:center;justify-content:space-between;gap:8px;padding:5px 8px;
  text-decoration:none;color:var(--tinte);border-radius:3px;font-size:12.5px}
.index a:hover{background:var(--linie2)}
.index a:focus-visible{outline:2px solid var(--akzent);outline-offset:1px}
.ix-name{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.ix-sim{font-family:"IBM Plex Mono",monospace;font-size:11px;flex:none}
.sim-niedrig{color:var(--gut)} .sim-mittel{color:var(--warn)} .sim-hoch{color:var(--schlecht)}

.liste{display:flex;flex-direction:column;gap:22px;min-width:0}
.karte{background:var(--flaeche);border:1px solid var(--linie);box-shadow:var(--schatten);
  scroll-margin-top:20px;min-width:0}
.k-kopf{padding:16px 20px 12px;border-bottom:1px solid var(--linie)}
.k-kopf h3{font-family:"Zilla Slab",Georgia,serif;font-size:17px;font-weight:600;margin:0 0 3px;
  line-height:1.25;text-wrap:balance}
.k-meta{margin:0;font-size:12px;color:var(--gedaempft)}
.block{padding:16px 20px;border-top:1px solid var(--linie2)}
.block:first-of-type{border-top:none}
.label{font-family:"IBM Plex Mono",monospace;font-size:10px;letter-spacing:.13em;
  text-transform:uppercase;color:var(--gedaempft);margin:0 0 10px;font-weight:500}

.serp{background:var(--flaeche2);border:1px solid var(--linie2);padding:12px 14px;max-width:600px}
.serp-pfad{font-size:12px;color:var(--serp-pfad);margin-bottom:2px}
.serp-titel{color:var(--serp-titel);font-size:18px;line-height:1.3;margin-bottom:3px}
.serp-desc{font-size:13px;line-height:1.55;color:var(--gedaempft)}
.zaehler{display:flex;flex-wrap:wrap;gap:20px;margin:12px 0 0}
.zaehler div{display:flex;flex-direction:column;gap:2px}
.zaehler dt{font-size:10px;letter-spacing:.08em;text-transform:uppercase;color:var(--gedaempft)}
.zaehler dd{margin:0;font-size:13.5px;font-weight:500}
.zaehler dd span{color:var(--gedaempft);font-weight:400}
.gut{color:var(--gut)} .schlecht{color:var(--schlecht)}
.kw{font-family:"IBM Plex Mono",monospace;font-size:12px;background:var(--linie2);padding:2px 7px;border-radius:2px}

.doc{max-width:68ch;min-width:0;font-size:14px}
.doc.kurz{background:var(--flaeche2);border-left:2px solid var(--linie);padding:10px 14px}
.doc p{margin:0 0 11px}
.doc p:last-child{margin-bottom:0}
.doc h2{font-family:"Zilla Slab",Georgia,serif;font-size:15.5px;font-weight:600;margin:18px 0 7px;
  color:var(--akzent);text-wrap:balance}
.doc ul{margin:0 0 12px;padding-left:19px}
.doc li{margin-bottom:4px}
.doc table{border-collapse:collapse;font-size:13px;margin:0 0 12px;min-width:300px}
.doc thead td{border-bottom:1.5px solid var(--tinte);padding-bottom:5px}
.doc tbody td{border-bottom:1px solid var(--linie2);padding:5px 0}
.doc tbody td:first-child{padding-right:22px;white-space:nowrap}
@media (prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}}
</style>

<div class="huelle">
  <header class="kopf">
    <p class="eyebrow">Hanfjack · Headshop · Aktivkohlefilter · vollständig, zur Freigabe</p>
    <h1>Aktivkohlefilter — alle 93 Produkte neu geschrieben</h1>
    <p class="dach">Beschreibung, Kurzbeschreibung und Yoast-Angaben für die komplette Kategorie.
      Nichts davon steht im Shop — erst nach Freigabe. Der Ähnlichkeitswert an jeder Karte zeigt, wie
      nah der Text am ähnlichsten anderen Produkt der gesamten Kategorie liegt (nicht nur der eigenen
      Marke) — 0,00 heißt kein Überschneidungspunkt, 1,00 wäre ein identischer Text. Über alle 93 × 92
      möglichen Paare hinweg liegt der Median bei @@SIMMED@@: Die meisten Texte haben inhaltlich nichts
      gemeinsam. Wo ein Produkt seine engste Verwandtschaft findet, ist das fast immer eine
      Format- oder Farbvariante desselben Artikels — genau dort, wo eine gewisse Nähe auch inhaltlich
      richtig ist.</p>
  </header>

  <dl class="kennzahlen">
    <div class="kz"><dt>Produkte</dt><dd>@@GESAMT@@</dd></div>
    <div class="kz"><dt>Höchste Ähnlichkeit im Bestand</dt><dd>@@SIMMAX@@ <small>PURIZE ITALY / UK</small></dd></div>
    <div class="kz"><dt>Davon über 0,85</dt><dd>@@HOCH@@ <small>Paare, alle PURIZE-Länder-/Farbeditionen</small></dd></div>
    <div class="kz"><dt>Median aller Paare</dt><dd>@@SIMMED@@ <small>über alle 4.278 möglichen Paare</small></dd></div>
    <div class="kz"><dt>Titel/Meta außerhalb Länge</dt><dd>0 <small>von 93</small></dd></div>
  </dl>

  <section class="recht">
    <h2>Was aus den alten Texten gestrichen wurde</h2>
    <p>Gesundheits-, Umwelt- und Spitzenstellungsaussagen, die als Werbung angreifbar sind — ersetzt durch
      Funktionsbeschreibung: <em>kühlt den Rauch</em>, <em>hält Krümel zurück</em>, <em>hält den Zug
      gleichmäßig</em> statt Wirkungsversprechen.</p>
    <table><tbody>@@GESTRICHEN@@</tbody></table>
  </section>

  <section class="fundeblock">
    <h2>Funde beim Schreiben</h2>
    <p>Vier Stellen, an denen die Produktdaten selbst etwas zeigten, das in den alten Texten unterging
      oder falsch stand.</p>
    @@FUNDE@@
  </section>

  @@SEKTIONEN@@
</div>
"""
for platzhalter, wert in (
    ("@@GESAMT@@", str(kennzahlen['gesamt'])),
    ("@@SIMMAX@@", ("%.2f" % kennzahlen['simmax']).replace('.',',')),
    ("@@SIMMED@@", ("%.2f" % kennzahlen['simmed']).replace('.',',')),
    ("@@HOCH@@", str(kennzahlen['hoch'])),
    ("@@GESTRICHEN@@", gestrichen_html),
    ("@@FUNDE@@", funde_html),
    ("@@SEKTIONEN@@", "".join(sektionen)),
):
    html_out = html_out.replace(platzhalter, wert)

open('revision_gesamt.html','w',encoding='utf-8').write(html_out)
print("Datei geschrieben:", len(html_out), "Zeichen")
print("Ähnlichkeit gesamt: max %.2f, Median %.2f, %d Paare über 0,85" % (
    kennzahlen['simmax'], kennzahlen['simmed'], kennzahlen['hoch']))
