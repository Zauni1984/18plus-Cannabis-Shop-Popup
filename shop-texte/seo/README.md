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

## Befund 1 (erledigt): 3.856 Produktseiten trugen eine erfundene 5-Sterne-Bewertung

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

### ERLEDIGT am 18.09.2026

Die Automatikbewertung ist abgeschaltet. Kontrolle an einer Zufallsstichprobe
von **40 veroeffentlichten Produkten**, jeweils mit Cache-Umgehung abgerufen:

| Pruefpunkt | Ergebnis |
|---|---|
| Produkte ohne echte Bewertung mit erfundenem Rating | **0 von 40** |
| Produkt-Schema noch vorhanden | 40 von 40 |
| Angebot (Preis, Verfuegbarkeit) noch vorhanden | 40 von 40 – 20× `Offer` (einfache Produkte), 20× `AggregateOffer` (variable) |
| Produkte mit echter Rezension behalten ihr Markup | ja, geprueft an 504 (1 Rezension, Autor „Anonym") |

Einzeln nachgesehen und sauber: 45349 RQS Organic, 45296 PURIZE Leopard,
30294 G-Rollz Diablos Box, 30844 Barneys Farm Banana Runtz, 13783 Palacio
Tiger Massage Gel, 9006 Juicy Jay´s Blueberry. Keine `aggregateRating`, keine
`Review`-Bloecke mehr – und die wertvollen Teile des Markups sind
unangetastet.

**Wichtig fuer den naechsten Schritt:** Steht in der Search Console eine
manuelle Massnahme, verschwindet sie durch die Reparatur **nicht von selbst**.
Dann muss dort ein Antrag auf erneute Ueberpruefung gestellt werden – jetzt
ist der richtige Zeitpunkt dafuer, weil der Grund beseitigt ist.

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

## Befund 2 (erledigt): H1

**Stand 18.09.2026, Schlusskontrolle.** Jeder gepruefte Seitentyp hat genau
eine H1:

| Seitentyp | h1 |
|---|---|
| Startseite | „HANFJACK HANFPRODUKTE" |
| Impressum | „Impressum" |
| AGB | „AGB" |
| Produkt (neu) | „RQS Organic King Size" |
| Produkt (alt) | „Barneys Farm Runtz Auto 3er Pack" |
| Kategorie Papers | „Papers" |
| Kategorie Samen | „Samen" |
| Marke | „Royal Queen Seeds" |
| Schlagwort | „King Size" |
| Blog | „Blog" |

Dazu eine Stichprobe von 15 zufaelligen Produktseiten: **15 von 15 mit genau
einer H1**, keine ohne und keine mit mehreren.

### Wie es dahin kam

Drei Schritte, in dieser Reihenfolge:

1. Produkttitel auf H1 (vom Betreiber gesetzt).
2. Ueberschriften-Tag fuer Archive auf h1 – damit bekamen Kategorie-, Marken-,
   Schlagwort- und Blogseiten ihre H1 ueber den Baustein
   `page-header-title`.
3. An Seite 27543 (Startseite): `ocean_disable_title` von `"on"` auf
   `"default"` und `ocean_post_title_style` auf `"centered"` – der Titel stand
   in der Datenbank, wurde aber vom Theme unterdrueckt. Sicherung:
   `seite27543_meta_vorher.json`. Danach das Ueberschriften-Tag fuer einzelne
   Seiten von h4 auf h1 (Customizer), was Startseite, Impressum, AGB und die
   uebrigen Einzelseiten zugleich erledigt hat.

### Eine Kleinigkeit bleibt

Auf Produktseiten steht der Produktname **zweimal** als Ueberschrift: als
`<h4 class="page-header-title">` im Seitenkopf und als `<h1>` in der
Produktzusammenfassung. Das ist kein Fehler und kostet keine Rankings, aber
eine Ueberschrift ist entbehrlich. Wer aufraeumen will, blendet den
Seitenkopf-Titel auf Produkten aus.

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

- ~~**Keine `robots`-Meta-Angabe.**~~ **Das war ein Messfehler von mir.** Die
  Angabe steht auf jeder geprueften Seite, und zwar vollstaendig:
  `index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1`.
  Mein Suchmuster verlangte doppelte Anfuehrungszeichen, Yoast schreibt aber
  einfache (`<meta name='robots' …>`). Hier ist nichts zu tun – die grossen
  Bildvorschauen sind bereits freigegeben.
- **`User-agent: AdsBot / Disallow: /`** in der robots.txt sperrt Googles
  Anzeigen-Crawler aus. Organisch harmlos, aber wer Google Ads schaltet,
  bekommt dadurch Qualitaetsprobleme. **Bestaetigt, noch offen.** Die
  robots.txt wird virtuell von WordPress ausgeliefert (kein `Last-Modified`,
  `Content-Type: text/plain; charset=utf-8`), der Eintrag steht innerhalb des
  Yoast-Blocks. Aendern laesst er sich nur in **Yoast SEO → Werkzeuge →
  Datei-Editor → robots.txt**; der `yoast/v1`-Namespace bietet dafuer keine
  Route, ueber die Schnittstelle komme ich nicht heran. Zu loeschen sind die
  zwei Zeilen `User-agent: AdsBot` und `Disallow: /`.
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

1. ~~**SASWP-Automatikbewertung abschalten.**~~ Erledigt am 18.09.2026.
2. **Search Console auf manuelle Massnahmen pruefen** – und falls eine
   vorliegt, jetzt den Antrag auf erneute Ueberpruefung stellen. Die
   Reparatur hebt eine Massnahme nicht von allein auf.
3. **Ueberschriften-Tag fuer einzelne Seiten im Customizer von h4 auf h1.**
   Archive stehen schon auf h1, Produktseiten haben ihre H1 – es fehlen nur
   noch Startseite, Impressum, AGB und die uebrigen Einzelseiten.
4. **Attribut- und Kleinst-Schlagwort-Archive auf noindex.** Halbiert die
   indexierbare Flaeche und konzentriert sie auf das, was verkauft.
5. **`max-image-preview:large` setzen.**
6. **Search-Console-Export besorgen** und danach entscheiden, ob es um
   Sichtbarkeit oder um Klickrate geht.
