# Blog-Teil 1 — Übertreibungen/EU-Konformität & interne Verlinkung

**Scope:** WordPress-Kategorie 12980 ("Blog"), Seite 1 von 4 (die 50 Artikel mit
den niedrigsten IDs, `orderby=id, order=asc`). Drei weitere Agenten haben
parallel Seite 2–4 (höhere IDs) bearbeitet.

**Geprüfte Artikel (50, IDs 434–30981):** 434, 437, 694, 1991, 7700, 7709,
9780, 9788, 9791, 9794, 9800, 9803, 9807, 9812, 9815, 9820, 9823, 10444,
11560, 12046, 12053, 12476, 13874, 13881, 13890, 13894, 13899, 15142, 17544,
17547, **17616 (siehe Sonderfall unten)**, 17625, 17628, 20933, 21097, 23407,
23409, 27614, 28777, 28808, 28812, 28815, 28818, 29559, 29565, 30252, 30513,
30761, 30764, 30981.

## Technischer Sonderfall: Artikel 17616 nicht bearbeitbar

**"Keimung von Samen in Eazy Plugs – der einfache Weg zu starken Pflanzen"**
(Position 31 in der ID-Reihenfolge, zwischen "Static Hash" ID 17547 und
"Lammbock" ID 17625) lässt sich über **keinen** Lese-Tool aufrufen:
`wp_get_post`, `wp_get_post_meta` und `wp_yoast_get_post_seo` liefern für
diese ID durchgehend `"Tool execution failed"` — reproduzierbar bei
wiederholten Versuchen. Auch `wp_list_posts` mit `orderby=id` bricht ab,
sobald das Abfragefenster diese Position einschließt (unabhängig von
`per_page`/`page`), was bestätigt, dass der Fehler an diesem einen Post
hängt und nicht an der Paginierung liegt.

`wp_replace_in_post` funktioniert dagegen (Direktzugriff auf das
Datenbankfeld, `content_length` bestätigt 19.735 Zeichen), **außer** bei
Suchbegriffen, die "Eazy Plug" enthalten — jeder Versuch, in der Nähe dieser
Textstelle etwas zu ersetzen, bricht ebenfalls mit `Tool execution failed`
ab (mehrfach reproduziert), während Suchen an anderer Stelle im selben
Artikel (z. B. "starken Pflanzen") anstandslos funktionieren. Das deutet auf
einen lokal korrupten Byte-/Markup-Bereich genau um die (mutmaßlich
verlinkten) "Eazy Plug"-Erwähnungen hin — vermutlich ein defekter Link oder
ungültige Zeichenkodierung, die die REST-Antwort/Block-Verarbeitung dort
zum Absturz bringt.

Blinde Sondierungs-Suchen (No-Op-Replace, 0 Treffer, keine Änderung) nach
"heilt", "Angststörung" und "Krebs" verliefen ohne Fund — ein Hinweis, aber
keine Garantie, dass der lesbare Teil des Artikels keine Verstöße enthält.
Weder Compliance-Prüfung noch Link-Ergänzung konnten für diesen Artikel
durchgeführt werden, da der Volltext an keiner Stelle einsehbar ist.
**Empfehlung an den Seitenbetreiber:** Artikel 17616 im WP-Adminbereich
manuell öffnen (dort funktioniert die Blockbearbeitung ggf., da sie nicht
über dieselbe REST-Route läuft) und die "Eazy Plug"-Linkstelle(n) auf
kaputtes Markup/ungültige Zeichen prüfen.

## 1. Übertreibungen & EU-Konformität

**13 von 50 Artikeln** enthielten unqualifizierte Gesundheits-/Wirkaussagen
und wurden gefixt (gehedgt, nicht gelöscht). 36 Artikel waren bereits
sauber (reine Grow-/Rechts-/Sorten-/Unterhaltungsinhalte oder bereits mit
angemessener Einordnung). Ein Artikel (17616) war technisch nicht prüfbar
(siehe oben).

### 434 — "Hanfjack eröffnet neuen Shop für CBD"
**Vorher:** *"Hanf als Novel Food ist eine vielseitige Pflanze mit vielen
gesundheitlichen Vorteilen. Es kann helfen, Angstzustände und Schmerzen zu
lindern, den Schlaf zu verbessern und das Wohlbefinden zu fördern. Darüber
hinaus ist Hanf als Novel Food eine natürliche und sichere Alternative zu
herkömmlichen Medikamenten..."*
**Nachher:** *"Hanf als Novel Food ist eine vielseitige Pflanze, die von
vielen Menschen für ihr entspannendes Potenzial geschätzt wird. In der
traditionellen Anwendung wird Hanf eine beruhigende und wohltuende Wirkung
nachgesagt – wissenschaftlich ist dies bislang nicht abschließend belegt,
weshalb Hanfprodukte keine medizinische Behandlung ersetzen. Als Novel Food
unterliegen die Produkte strengen Zulassungs- und Qualitätsstandards."*

### 437 — "CBD: Ein Überblick über die Wirkung, Sicherheit und ärztliche Meinungen"
**Vorher:** *"CBD wird oft für seine beruhigenden Eigenschaften und seine
Fähigkeit, Angstzustände und Schmerzen zu lindern, gepriesen. Einige
Studien deuten darauf hin, dass CBD auch bei bestimmten
Gesundheitsproblemen wie Epilepsie und Schizophrenie wirksam sein kann."*
**Nachher:** *"CBD wird oft eine beruhigende Wirkung nachgesagt. Einige
Studien untersuchen zudem, wie CBD bei bestimmten neurologischen und
psychischen Gesundheitsthemen wirken könnte – belastbare, allgemein
anerkannte Ergebnisse gibt es dazu bislang nicht."* (konkrete
Krankheitsnamen Epilepsie/Schizophrenie entfernt, wie in der Vorgabe für
diagnosenahe Formulierungen gefordert.)

### Strain-Profil-Serie (7700–9823, einheitliches Content-Template)
13 Sorten-Artikel (White Widow, Amnesia Haze, Banana Purple Punch, Acapulco
Gold, Ayahuasca Purple, Blue Cheese, Cookies Kush, Critical Kush, Dos Si
Dos, Gelato #45, Glue Gelato, GMO, Gorilla Glue) teilen sich einen
"Medizinische Anwendungen"-Abschnitt mit Disclaimer-Zitat ("ersetzt keinen
medizinischen Rat"). Fünf davon (Banana Purple Punch, Ayahuasca Purple,
Blue Cheese, Cookies Kush, GMO) waren bereits sauber gehedgt
("Nutzer berichten von einer möglichen Linderung bei…") und wurden nicht
angefasst. Bei den übrigen 8 wurde gefixt:

- **White Widow (7700), Amnesia Haze (7709), Dos Si Dos (9807):** zu direkte
  Einleitungssätze ("wird oft … geschätzt/eingesetzt/verwendet, um … zu
  lindern") ersetzt durch *"In anekdotischen Erfahrungsberichten wird
  [Sorte] im Zusammenhang mit folgenden Themen erwähnt (wissenschaftlich
  nicht abschließend belegt):"*
- **Amnesia Haze (7709), Acapulco Gold (9788), Gelato #45 (9812):** die
  Bullet-Point-"Depression" (klarer Krankheitsname laut Vorgabe) ersetzt
  durch *"Gedrückte Stimmung"*.
- **Critical Kush (9803), Glue Gelato (9815), Gorilla Glue (9823):** die
  Bullet-Point-"Angstzuständen" ersetzt durch *"Innerer Anspannung"*.

### 21097 — "Entdecken Sie unsere neuen Cannabis Stecklinge…" (Strawberry Guave/Studio 54/Dantes Inferno, `status: private`)
**Vorher:** *"Zudem berichten einige Anbauer von potenziellen
therapeutischen Vorteilen, die mit dieser Sorte verbunden sind, darunter
eine beruhigende Wirkung und eine positive Stimmung. Diese Eigenschaften
machen Strawberry Guave nicht nur zu einer idealen Wahl für Genussraucher,
sondern auch für therapeutische Anwender."*
**Nachher:** *"Manche Anbauer beschreiben zudem eine beruhigende Wirkung und
eine positive Stimmung im Zusammenhang mit dieser Sorte – wissenschaftlich
belegte therapeutische Effekte sind das jedoch nicht, individuelle
Erfahrungen können stark variieren."* (Die Formulierung "ideale Wahl … für
therapeutische Anwender" wurde entfernt.)

### 28815 — "CBD für Tiere 2026"
**Vorher:** *"Studien (meist an Hunden) deuten auf positive Effekte hin –
z. B. bei Arthrose, Epilepsie oder Stress."*
**Nachher:** *"Erste tiermedizinische Studien (meist an Hunden) untersuchen
mögliche Effekte in verschiedenen Bereichen – die Studienlage ist bislang
aber begrenzt, weshalb CBD eine tierärztliche Behandlung nicht ersetzt."*
(konkrete Diagnosen Arthrose/Epilepsie entfernt.)

### 29559 — "CBD für Anfänger: Vorteile und Tipps 2026" (umfangreichste Korrektur)
Drei Punkte der "5 Hauptgründe"-Liste gefixt:
1. **Schmerzlinderung** — "Chronische Schmerzen (Rücken, Gelenke,
   Kopfschmerzen, Menstruationsbeschwerden, Sportverletzungen) gehören zu
   den häufigsten Anwendungsgebieten" → umformuliert zu anekdotischer
   Erfahrung bei "Verspannungen oder Beschwerden nach dem Sport" plus
   explizitem Hinweis, dass CBD keine ärztliche Behandlung bei chronischen
   Schmerzen ersetzt.
2. **"Stress & Ängste reduzieren" / "CBD wirkt beruhigend"** — unqualifizierte
   Tatsachenbehauptung ("CBD wirkt beruhigend") umformuliert zu "CBD wird …
   eine beruhigende Wirkung nachgesagt" + Beleg-Disclaimer; Überschrift von
   "Ängste" (diagnosenah) auf "Anspannung" geändert.
3. **Entzündungshemmend & Regeneration** — konkrete Diagnosen
   "rheumatische Beschwerden" und **"Neurodermitis"** entfernt, durch
   "Menschen mit empfindlicher Haut" ersetzt, plus Disclaimer zur
   wissenschaftlichen Beleglage.

Alle Fixes folgen der Vorgabe: Fakten/Themen bleiben erhalten, werden aber
klar als anekdotisch/wissenschaftlich unbelegt eingeordnet statt als
Tatsache dargestellt; explizite Krankheitsnamen (Depression, Angstzustände,
Arthrose, Epilepsie, Neurodermitis) wurden durch neutrale, nicht-klinische
Formulierungen ersetzt.

### Keine Verstöße gefunden (36 Artikel)
Reine Grow-Guides (Equipment, Keimung, Stecklinge, Anbau-Recht),
Sorten-/Genetik-Portraits ohne Medizinabschnitt, Marken-/Shop-News,
Rechts-/Politik-Updates (EFSA, MedCanG, CanG-Evaluation — bereits mit
"kein medizinischer Rat"-Disclaimern) und Unterhaltungsinhalte
(Filmkritiken) enthielten ausschließlich sachliche Fakten oder bereits
korrekt gehedgte, allgemein bekannte psychoaktive/entspannende
Cannabis-Effekte (Kernthema des Blogs, kein Verstoß laut Vorgabe).

## 2. Interne Produkt-/Kategorie-Verlinkung

**9 Artikel** erhielten insgesamt **16 neue Links** zu echten, per
`wp_wc_list_products`/`wp_wc_list_product_categories` verifizierten
Produkten/Kategorien. Die meisten Artikel (v. a. die neueren
2026er-Guides und die Strain-Profile mit eingebettetem
WooCommerce-Produktraster) waren bereits ausreichend verlinkt und wurden
nicht verändert, um kein Keyword-Spam-Verlinken zu erzeugen.

- **434** — "CBD-Blüten" → [CBD Blüten](https://hanfjack.de/produkt-kategorie/cbd/cbd-blueten/),
  "CBD-Öle" → [CBD Öl](https://hanfjack.de/produkt-kategorie/cbd/cbd-oel/)
- **437** — "CBD" (Erstnennung) → [CBD-Kategorie](https://hanfjack.de/produkt-kategorie/cbd/)
- **1991** (Cannabis-Anbau-Guide, private) — "White Widow" → [Barneys Farm White Widow Regular](https://hanfjack.de/produkt/barneys-farm-white-widow-regular-5er-packung-5er-gift-pack-gratis-dazu/),
  "Feuchtigkeitskontroll-Pakete" → [Integra Boost Feuchtigkeitsregler](https://hanfjack.de/produkt/integra-boost-feuchtigkeitsregler-55-67g/)
- **9823** (Gorilla Glue, hatte keinen Produktlink) — "Gorilla Glue" im Titel →
  [Dutch Passion Gorilla Super Glue](https://hanfjack.de/produkt/gorilla-super-glue/)
- **17544** (Paradise Seeds Durga Mata) — "Paradise Seeds" →
  [Marken-Seite Paradise Seeds](https://hanfjack.de/marke/paradise-seeds/)
  (Direktlink auf das Produkt selbst vermieden, da beide Durga-Mata-Produkte
  `status: private` sind und für Besucher nicht erreichbar wären)
- **17628** (Mimosa x Orange Punch, hatte keinen Produktlink) — "Mimosa Evo" →
  [Barneys Farm Mimosa EVO](https://hanfjack.de/produkt/barneys-farm-mimosa-evo/),
  "Mimosa x Orange Punch" (Fazit) → [Barneys Farm Mimosa x Orange Punch](https://hanfjack.de/produkt/mimosa-x-orange-punch/)
- **21097** (Neue Stecklinge, hatte keinen Produktlink) — die drei
  Sorten-Überschriften verlinkt: "Strawberry Guave" →
  [Produkt](https://hanfjack.de/produkt/strawberry-guave/), "Studio 54" →
  [Produkt](https://hanfjack.de/produkt/studio-54/), "Dantes Inferno" →
  [Produkt](https://hanfjack.de/produkt/dantes-inferno-8/)
- **23407** (Spider-Farmer-Lieferengpässe, hatte keinen Link) — "Spider
  Farmer" (Erstnennung) → [Marken-Seite Spider Farmer](https://hanfjack.de/marke/spider-farmer/)
- **28818** (Cannabisgesetz-Evaluation) — enthielt drei **kaputte
  Platzhalter-Links** im Fließtext (`[CBD-Öle & Blüten]`, `[Legale
  CBD-Samen]`, `[Grow-Equipment]` als reiner eckiger-Klammer-Text ohne
  `<a>`-Tag). In echte Links repariert: → [CBD-Kategorie](https://hanfjack.de/produkt-kategorie/cbd/),
  → [CBD-Samen](https://hanfjack.de/produkt-kategorie/samen/cbd-samen/),
  → [Growbedarf](https://hanfjack.de/produkt-kategorie/growbedarf/)

Nicht verändert: Artikel mit bereits eingebettetem WooCommerce-Produktraster
(die meisten Strain-Profile), Artikel mit bereits 3+ passenden Links
(z. B. 12053 Growzelte-Guide, 13874/13881/13890/13894/13899
Samen-Guides, 30252 Autoflower-Top10, 30513 Doktorfreezy-Cup,
30761/30764 Anbau-Recht-Guides, 30981 F1-Hybriden — alle bereits mit
5–11 sinnvollen Produkt-/Marken-/Kategorie-Links) sowie Artikel ohne
natürlichen Produktbezug (Logo-Relaunch, Hanfjack-Cup-Ergebnisse,
Filmkritiken, TikTok-Statement, Rechtsprechungs-News). Bereits vorhandene
Links wurden in keinem Fall angetastet oder entfernt.

## Sonstige Beobachtung (nicht behoben, außerhalb des Auftragsumfangs)

- **12046** ("🪴 Stecklinge kaufen…", private): Die drei bestehenden Links
  zeigen auf `https://mintcream-crab-935546.hostingersite.com/produkt-kategorie/stecklinge/`
  — eine Staging-/Vorschau-Domain statt `hanfjack.de`. Da die Vorgabe
  ausdrücklich verlangt, bestehende interne Links nicht anzufassen, wurde
  dies nicht repariert, aber hier dokumentiert.
- **21097**: enthält an einer Stelle ein fehlplatziertes kyrillisches Wort
  ("Zusammengefasst основывается das Vertrauen…") — vermutlich ein
  KI-Generierungsartefakt. Liegt außerhalb des Compliance-/Link-Auftrags
  und wurde nicht korrigiert.

## Vorgehen

Für jeden Artikel: `wp_get_post` gelesen, Content gegen die
EU-Konformitätsregel und auf Link-Potenzial geprüft, Fixes chirurgisch via
`wp_replace_in_post` (Suchtext exakt genug für eindeutigen Treffer)
umgesetzt, Treffer per `replacements_count` bestätigt und bei den größeren
Änderungen zusätzlich per erneutem `wp_get_post` verifiziert. Für
Produktlinks wurde jeweils vorab per `wp_wc_list_products` bzw.
`wp_wc_list_product_categories` geprüft, dass ein echtes, öffentlich
erreichbares (`status: publish`) Produkt/eine Kategorie existiert.
