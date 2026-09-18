# Warum hanfjack.de wenig organischen Traffic bekommt

Befundaufnahme vom 18.09.2026. Alle Zahlen sind selbst gemessen, nicht
geschaetzt; wo eine Quelle fehlte, steht das dabei.

## Was zuerst ausgeschlossen wurde

Vier naheliegende Verdaechtige tragen **nicht**:

| Verdacht | Befund |
|---|---|
| Der 18+-Popup blockiert Googlebot | Nein. Startseite und Produktseite liefern an Googlebot dasselbe HTML wie an einen Browser (614.572 vs. 614.599 Byte; die 27 Byte sind ein Nonce). Kein Cloaking, kein Interstitial im Quelltext. |
| robots.txt oder Sitemap kaputt | Nein. robots.txt ist sauber, der Sitemap-Index listet 44 Sitemaps mit rund 11.700 URLs. |
| Die Seite ist nicht indexiert | Nein. Stichprobe von 150 veroeffentlichten Produkten gegen die gespeicherten Search-Console-Pruefungen: **122 „Submitted and indexed"** (81 %), 9 „Crawled – currently not indexed", 5 „Discovered", 10 unbekannt, 4 „Not found (404)". Die vier 404er liefern in Wahrheit HTTP 200 – veraltete Pruefdaten, kein echtes Problem. |
| Die Seite ist zu langsam | Nein. Rocket-Insights-Score 85 global, LCP 1,2–2,9 s, CLS 0, TBT nahe 0. |

Die Seite ist also **indexiert und schnell** – sie rankt nur nicht. Das
verschiebt die Suche von „wird sie gefunden" zu „warum ist sie nichts wert".

---

## Befund 1: 3.856 Produktseiten tragen eine erfundene 5-Sterne-Bewertung

**Das ist der schwerwiegendste Fund.** Das Plugin **SASWP** (Schema &
Structured Data for WP & AMP, erkennbar am
`class="saswp-schema-markup-output"`) erzeugt fuer jedes Produkt **ohne echte
Bewertung** eine Bewertung – und zwar so:

```json
"aggregateRating": {"ratingValue": "5", "reviewCount": 1},
"review": {
  "author":      {"@type": "Person", "name": "Hanfjack"},
  "description": "<die Yoast-Meta-Description des Produkts>",
  "reviewRating": {"ratingValue": "5"}
}
```

Der Haendler bewertet sich selbst mit 5 von 5, und der Bewertungstext ist die
eigene Meta-Description.

**Umfang:** Von 3.993 veroeffentlichten Produkten haben **3.856 (97 %) keine
einzige echte Bewertung** in WooCommerce. Alle 3.856 senden dieses Markup.

**Beleg am Einzelfall:** Produkt 45349 (RQS Organic King Size) wurde gestern
angelegt und hat `rating_count: 0`. Seine Seite meldet trotzdem
`aggregateRating 5/5, reviewCount 1`, Autor „Hanfjack".

Google verbietet das ausdruecklich: Selbstbewertungen des Haendlers sind
unzulaessiges Review-Markup. Die Folge ist im besten Fall der Verlust der
Sterne im Suchergebnis, im schlechteren eine **manuelle Massnahme wegen
Spam-Markup** – und die trifft die ganze Domain, nicht die einzelne Seite.
Das passt zu dem Bild: indexiert, schnell, ordentliche Texte, trotzdem kaum
Sichtbarkeit.

**Stand 18.09.2026: unveraendert aktiv.** Nachgeprueft an fuenf Produkten
ohne echte Bewertung – 45349 (RQS Organic), 45296 (PURIZE Leopard), 30294
(G-Rollz Diablos Box), 30844 (Barneys Farm Banana Runtz) – alle senden
weiterhin `aggregateRating 5/5, reviewCount 1`, Autor „Hanfjack". Produkt 504
mit einer echten Rezension sendet korrekt „Anonym" als Autor.

**Zu pruefen, bevor irgendetwas anderes angefasst wird:** Search Console →
Sicherheit und manuelle Massnahmen. Steht dort ein Eintrag, ist die Ursache
gefunden.

**Das Plugin heisst** „Schema & Structured Data for WP & AMP", Version 1.66,
Slug `schema-and-structured-data-for-wp`. SASWP ist die gaengige Abkuerzung
und zugleich das Code-Praefix des Plugins, daher die Klasse
`saswp-schema-markup-output` im Quelltext.

Es ist die **einzige** Quelle strukturierter Daten auf den Produktseiten: die
Seite enthaelt genau einen JSON-LD-Block, und der kommt von SASWP. Yoasts
eigenes Schema ist abgeschaltet. Die erfundene Bewertung hat also keine
zweite Quelle, die man uebersehen koennte.

Drei weitere Bewertungs-Plugins sind aktiv – Customer Reviews for
WooCommerce, Trustpilot-reviews und das Google-Bewertungs-Widget –, senden
aber aktuell kein konkurrierendes Produkt-Markup.

**Die Reparatur** liegt in den SASWP-Einstellungen: die automatische
Bewertung fuer Produkte ohne Rezension abschalten. Produkte mit echten
Bewertungen (137 Stueck, z. B. Barneys Farm Runtz Auto mit zwei Rezensionen
von „Ben") behalten ihr Markup – das ist legitim.

---

## Befund 2: H1 – Produktseiten erledigt, Rest offen

**Stand 18.09.2026, zweite Pruefung:** Bis auf die Startseite hat jetzt jeder
Seitentyp eine H1.

| Seitentyp | h1 |
|---|---|
| Produkt (neu) | „RQS Organic King Size" |
| Produkt (alt) | „Barneys Farm Runtz Auto 3er Pack" |
| Kategorie Papers | „Papers" |
| Kategorie Samen | „Samen" |
| Kategorie Pflegeprodukte | „Pflegeprodukte" |
| Marke | „Royal Queen Seeds" |
| Schlagwort | „King Size" |
| Blog | „Blog" |
| **Startseite** | **keine** |

### Warum die Startseite als einzige keine H1 hat

Die Startseite ist Seite **27543**, und ihr Titel lautet tatsaechlich
„HANFJACK HANFPRODUKTE". Er wird nur nicht ausgegeben: die Seite traegt das
OceanWP-Meta

```
ocean_disable_title: "on"
```

und der Body bekommt entsprechend die Klasse `page-header-disabled`. Der
Titel steht also in der Datenbank, das Theme unterdrueckt ihn. Im
Seiteninhalt selbst (117.000 Zeichen Builder-Layout) kommt weder eine H1 noch
das Wort „Hanfprodukte" vor; die oberste Ueberschrift ist ein `h3`
(„Wizard Trees").

### Umgesetzt, aber noch nicht fertig

Auf Ansage wurde an Seite 27543 gesetzt:

```
ocean_disable_title:    "on"  ->  "default"
ocean_post_title_style: ""    ->  "centered"
```

Der Titel erscheint jetzt und ist mittig (`page-header centered-page-header`).
Sicherung des alten Zustands: `seite27543_meta_vorher.json`.

**Es ist trotzdem noch keine H1.** OceanWP gibt den Seitentitel bei
*einzelnen Seiten* als `<h4 class="page-header-title">` aus – auf der
Startseite genauso wie auf /impressum/. Bei *Archiven* steht derselbe
Baustein dagegen als `<h1>`:

| Seite | page-header-title |
|---|---|
| /produkt-kategorie/papers/ | `h1` |
| /marke/royal-queen-seeds/ | `h1` |
| /blog/ | `h1` |
| /impressum/ | `h4` |
| Startseite | `h4` |

Die Ueberschriftenebene fuer Archive ist also schon umgestellt, die fuer
einzelne Seiten nicht. Das ist eine **Theme-Einstellung im Customizer**
(Seitentitel → Ueberschriften-Tag), kein Seiten-Meta – ueber die
REST-Schnittstelle nicht erreichbar, der `oceanwp/v1`-Namespace bietet nur
Onboarding-Routen. Der Schalter muss im Customizer von h4 auf h1 gestellt
werden; er wirkt dann auf alle einzelnen Seiten, was auch fuer Impressum,
AGB und Co. richtig ist.

### Der urspruengliche Befund

Geprueft an drei Produkten, neu und alt:

| Seite | h1 |
|---|---|
| /produkt/rqs-organic-rolling-papers-king-size/ | „RQS Organic King Size" |
| /produkt/barneys-farm-runtz-auto/ | „Barneys Farm Runtz Auto 3er Pack" |
| /produkt/palacio-hanfsalbe-regenerierend-125ml-dose/ | „Palacio Hanfsalbe regenerierend – 125ml Dose" |

Der urspruengliche Befund lautete:

### Ausgangslage

Geprueft an fuenf Seitentypen, alle ohne `<h1>`:

| Seitentyp | Beispiel | h1 |
|---|---|---|
| Produkt | /produkt/rqs-organic-rolling-papers-king-size/ | fehlt |
| Produkt | /produkt/palacio-hanfsalbe-regenerierend-125ml-dose/ | fehlt |
| Kategorie | /produkt-kategorie/papers/ | fehlt |
| Kategorie | /produkt-kategorie/samen/ | fehlt |
| Marke | /marke/royal-queen-seeds/ | fehlt |
| Schlagwort | /produkt-schlagwort/king-size/ | fehlt |

Der Produktname steht in einem `<h2>`, darunter folgen weitere `<h2>`
(„Beschreibung", „Zusaetzliche Informationen", „Rezensionen"). Die Startseite
hat als einzige eine H1 – und die lautet „HANFJACK HANFPRODUKTE".

Das betrifft **rund 11.000 URLs** und ist ein Fehler im OceanWP-Template,
kein Inhaltsproblem. Eine H1 allein macht keine Rankings, aber sie ist das
staerkste On-Page-Signal fuer das Thema einer Seite, und sie fehlt
flaechendeckend.

---

## Befund 3: Rund 6.000 duenne Archivseiten in der Sitemap

Die Sitemap meldet rund 11.700 URLs. Davon sind nur 3.994 Produktseiten:

| Typ | URLs |
|---|---|
| Produkte | 3.994 |
| **Produkt-Schlagworte** | **2.339** |
| **pa_genetik** | **1.584** |
| pa_aroma | 274 |
| pa_inhalt | 220 |
| post_tag | 229 |
| Marken | 219 |
| pa_geschmack, pa_wuchshoehe, pa_ertrag, pa_leistungsaufnahme u. a. | zusammen rund 800 |
| Produktkategorien | 104 |
| Seiten und Beitraege | 190 |

**Von 2.347 Produkt-Schlagworten haben 1.009 hoechstens drei Produkte**, acht
haben gar keins. Dazu kommen Attribut-Archive, die als Landingpage sinnlos
sind: `/inhalt/75g/`, `/inhalt/1-heft/`, `/farbe/schwarz/`. Niemand sucht
danach, und sie sehen einander zum Verwechseln aehnlich.

Das Verhaeltnis ist damit **drei duenne Archivseiten auf zwei echte
Produktseiten**. Das verbraucht Crawl-Budget, verduennt die interne
Verlinkung und liefert Google massenhaft Seiten, die fuer sich genommen
nichts wert sind.

**Sinnvoll waere:** Attribut-Archive auf `noindex` und aus der Sitemap
nehmen (Yoast kann das pro Taxonomie), Schlagwort-Archive unter vier
Produkten ebenso. Kategorien, Marken und die grossen Schlagworte bleiben.

---

## Befund 4: Kleinere Sachen

- **Keine `robots`-Meta-Angabe.** Auf keiner geprueften Seite steht ein
  `<meta name="robots">`. Damit fehlt auch `max-image-preview:large` – die
  Produktbilder erscheinen in der Suche nur als kleine Vorschau statt gross.
  Fuer einen Shop kostet das Klicks.
- **`User-agent: AdsBot / Disallow: /`** in der robots.txt sperrt Googles
  Anzeigen-Crawler aus. Organisch harmlos, aber wer Google Ads schaltet,
  bekommt dadurch Qualitaetsprobleme.
- **Weiterleitungskette** auf /produkt/northern-lights/: 883 ms
  Redirect-Dauer, TTFB 1.368 ms gegenueber ~500 ms bei den anderen
  Messpunkten. Lohnt einen Blick auf die Redirect-Tabelle.
- **hanfjack.com ist voll crawlbar** und fuehrt dieselben Produkte unter
  denselben Slugs (`/produkt/palacio-hanfsalbe-regenerierend-125ml-dose/` auf
  beiden Domains). Die Texte sind zu 51–77 % identisch. Das ist kein
  Duplicate-Content-Notfall, aber die B2B-Domain sollte nicht mit der
  B2C-Domain um dieselben Suchbegriffe konkurrieren.

---

## Was nicht gemessen werden konnte

**Die eigentlichen Zahlen.** Google Site Kit ist installiert und mit Search
Console, Analytics 4, Ads und Tag Manager verbunden – aber die API antwortet
dem Anwendungspasswort-Nutzer mit

> `missing_required_scopes` – Site Kit hat keinen Zugriff auf die relevanten
> Daten von Search Console, da du beim Einrichten nicht die noetigen
> Berechtigungen erteilt hast.

Site Kit gibt die Daten nur dem Google-Konto heraus, das die Verbindung
hergestellt hat. Ohne Klicks, Impressionen, CTR und Durchschnittsposition
laesst sich nicht sagen, **ob** die Seite gar nicht erst in den Ergebnissen
auftaucht (Autoritaetsproblem) oder ob sie auftaucht und nicht geklickt wird
(Snippet-Problem). Das sind zwei verschiedene Baustellen.

Ein CSV-Export aus der Search Console (letzte 3 oder 6 Monate, Abfragen und
Seiten) wuerde das in einem Schritt klaeren.

---

## Reihenfolge

1. **Search Console auf manuelle Massnahmen pruefen.** Ein Eintrag dort
   erklaert alles andere und macht jede weitere Optimierung sinnlos, solange
   er steht.
2. **SASWP-Automatikbewertung abschalten.** 3.856 Seiten mit erfundenen
   Rezensionen sind ein Risiko, das nichts einbringt.
3. **H1 ins Template.** Einmal im Child-Theme, wirkt auf rund 11.000 URLs.
4. **Attribut- und Kleinst-Schlagwort-Archive auf noindex.** Halbiert die
   indexierbare Flaeche und konzentriert sie auf das, was verkauft.
5. **`max-image-preview:large` setzen.**
6. **Search-Console-Export besorgen** und danach entscheiden, ob es um
   Sichtbarkeit oder um Klickrate geht.
