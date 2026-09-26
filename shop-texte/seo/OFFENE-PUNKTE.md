# Was noch offen ist – und warum ich es nicht selbst erledigen kann

Stand 18.09.2026. Drei Punkte aus der Befundaufnahme sind noch offen. Alle
drei brauchen einen Menschen im Backend; ich habe fuer jeden geprueft, ob es
einen Weg ueber die Schnittstelle gibt, und fuer alle drei lautet die Antwort
nein.

---

## 1. Rund 5.800 duenne Archivseiten auf noindex

**Das ist der wirksamste der drei Punkte** – er halbiert die indexierbare
Flaeche der Domain und konzentriert sie auf das, was verkauft.

### Was ich probiert habe

| Weg | Ergebnis |
|---|---|
| Term-Meta `_yoast_wpseo_meta-robots-noindex` ueber das MCP-Werkzeug setzen | Wert wird gespeichert (`"1"`), **wirkt aber nicht** |
| Dasselbe ueber `wp/v2/product_cat` | Wert kommt als `"default"` zurueck, wird also nicht uebernommen |
| Attribut-Taxonomien ueber `wp/v2` | **Gar nicht erreichbar** – `pa_*` ist nicht `show_in_rest` |
| Yoast-Einstellungen ueber `wp/v2/settings` | Nicht registriert, keine der 26 Einstellungen gehoert Yoast |

Dass das Term-Meta nicht wirkt, liegt an Yoasts **Indexables-Tabelle**: Yoast
liest den Robots-Wert nicht bei jedem Aufruf aus dem Term-Meta, sondern aus
einer eigenen Tabelle. Ein direkt geschriebenes Meta landet dort nicht.
Nachgewiesen ueber `yoast/v1/get_head`, das Yoast selbst befragt – es meldete
auch nach dem Schreiben weiter `index, follow`. Der Testwert wurde wieder
auf `default` zurueckgesetzt.

### Der richtige Weg

**Yoast SEO → Einstellungen → Inhaltstypen bzw. Taxonomien.** Dort steht je
Taxonomie der Schalter „In Suchergebnissen anzeigen?". Auf **Nein** gestellt,
verschwindet die ganze Taxonomie aus dem Index **und aus der Sitemap** – ein
Schalter statt tausender Term-Eintraege.

Betroffen sind die **Attribut-Taxonomien**: `pa_genetik` (1.584 URLs),
`pa_aroma` (274), `pa_inhalt` (220), `pa_geschmack` (170), `pa_wuchshoehe`
(152), `pa_ertrag` (114), `pa_leistungsaufnahme` (106),
`pa_bluetezeit-tage` (83), `pa_effekte` (78), `pa_farbe` (70), `pa_groesse`
(59), `pa_variante` (53), `pa_thc-gehalt` (46) und die restlichen kleineren –
zusammen rund **3.200 URLs**.

Kategorien, Marken und Produkt-Schlagworte bleiben auf „Ja".

**Zu den Produkt-Schlagworten:** 1.009 der 2.347 haben hoechstens drei
Produkte. Die einzeln auf noindex zu setzen ginge nur ueber dasselbe
Term-Meta, das nicht wirkt. Entweder man laesst sie, oder man raeumt sie im
Backend auf – Schlagworte mit ein bis drei Produkten tragen ohnehin wenig.

---

## 2. AdsBot in der robots.txt

```
User-agent: AdsBot
Disallow: /
```

Das sperrt Googles Anzeigen-Crawler komplett aus. Organisch harmlos, aber wer
Google Ads schaltet, bekommt dadurch Qualitaetsprobleme bei jeder Anzeige.

Die robots.txt wird virtuell von WordPress ausgeliefert (kein
`Last-Modified`, `Content-Type: text/plain; charset=utf-8`), der Eintrag
steht innerhalb des Yoast-Blocks. Aendern geht nur in **Yoast SEO →
Werkzeuge → Datei-Editor → robots.txt**; der `yoast/v1`-Namensraum hat dafuer
keine Route. Zu loeschen sind die zwei Zeilen.

---

## 3. Merchant Center pruefen

Der Google-Shopping-Feed enthaelt **7.905 Positionen** und wird taeglich
erneuert. Dem stehen im ganzen Jahr nur **5.919 Haendlereintrags-Impressionen**
gegenueber. Zwischen Feed und Suchergebnis liegt also etwas.

Der Verdacht: **Ablehnungen im Merchant Center.** Samen und CBD fallen dort
unter eingeschraenkte Produkte, Papers, Growzubehoer und Pflegeprodukte
nicht. Dazu passt, dass das, was durchkommt, **13,82 % CTR auf Position 3,3**
holt – der mit Abstand beste Wert aller Suchdarstellungen.

Zu pruefen im Merchant Center unter Diagnose: wie viele Artikel abgelehnt
sind und mit welcher Begruendung. Einen Zugang dorthin habe ich nicht.

---

## Und weiterhin offen: der Abfragen-Export

Der Search-Console-Export enthielt Diagramm, Geraete, Laender und
Suchdarstellung – **nicht die Abfragen und nicht die Seiten**. Ohne die
beiden laesst sich nicht sagen, auf welche Begriffe die Domain auf Position
28 haengt und welche Seiten die 930.000 Impressionen holen. Vor allem die
Desktop-Luecke (Position 34,2 gegenueber 19,8 mobil) bleibt ohne Abfragedaten
unerklaerbar.

---

# Nachtrag 18.09.2026: zwei von drei erledigt

## AdsBot ist raus

Die robots.txt enthaelt kein `AdsBot` mehr. Googles Anzeigen-Crawler darf die
Seite wieder lesen.

## Die Archive sind auf noindex – und zwar gruendlich

| | vorher | jetzt |
|---|---|---|
| Sitemaps im Index | 44 | **9** |
| URLs in der Sitemap | 10.297 | **4.515** |

Geprueft am ausgelieferten HTML:

| Seite | robots |
|---|---|
| /inhalt/75g/ | `noindex, follow` |
| /aroma/erdig/ | `noindex, follow` |
| /produkt-schlagwort/king-size/ | `noindex, follow` |
| /produkt-kategorie/papers/ | `index, follow` |
| /marke/royal-queen-seeds/ | `index, follow` |

Was uebrig bleibt: 3.994 Produkte, 104 Kategorien, 219 Marken, 156 Beitraege,
34 Seiten, 8 Blog-Kategorien. **Die indexierbare Flaeche ist um 56 Prozent
geschrumpft**, und was bleibt, sind genau die Seiten, die etwas verkaufen
oder erklaeren.

### Eine Nebenwirkung, die eine Entscheidung verdient

Mit abgeschaltet wurden auch die **Produkt-Schlagworte insgesamt** – alle
2.339, nicht nur die 1.009 mit hoechstens drei Produkten. Darunter sind rund
**466 Schlagworte mit zehn und mehr Produkten**, etwa „King Size" mit 56.
Solche Seiten koennen durchaus ranken.

Das ist vertretbar und viele Shops machen es genauso: Schlagwort-Archive
ueberschneiden sich stark mit Kategorien, und nach einem Core Update ist
Konzentration das Naheliegende. Es sollte nur eine Entscheidung sein und kein
Versehen. Wer die starken Schlagworte zurueckholen will, kann sie einzeln
wieder auf „Ja" stellen – die Taxonomie-Einstellung ist die Voreinstellung,
der Term schlaegt sie.

## Geoblocker sperrt Googlebot (19.09.2026, dringend)

Nach Aktivierung von Geoblocker und Hostinger CDN gemessen. Von einer
US-Adresse antwortet der Shop mit **403 „Der Zugriff aus Ihrem Land (US) wurde
blockiert."** – und zwar unabhaengig vom User-Agent, auch fuer Googlebot und
Bingbot:

| URL | Chrome | Googlebot |
|---|---|---|
| `/` | 200 | 200 |
| `/automatisch/` | 403 | 403 |
| `/produkt/barneys-farm-runtz-auto/` | 403 | 403 |
| `/sitemap_index.xml` | 403 | 403 |
| `/robots.txt` | 200 | 200 |

Die 200er sind ausschliesslich Seiten, die im WP-Rocket-Cache liegen und vom
LiteSpeed vor PHP ausgeliefert werden; mit Cache-Buster (`/shop/?nc=123`) wird
auch daraus ein 403. Der Geoblocker laeuft also in PHP und trifft alles, was
tatsaechlich gerendert wird.

Fuer deutsche Kunden ist das richtig. Googlebot crawlt aber ueberwiegend aus
den USA. Anhaltende 403 auf Sitemaps, Kategorie- und Produktseiten fuehren zu
Crawling-Fehlern in der Search Console und mittelfristig zum Verlust der
Indexierung – das Gegenteil von dem, was die Ueberarbeitung erreichen soll.

**Zu tun:** im Geoblocker die Suchmaschinen-Crawler ausnehmen (Whitelist fuer
Googlebot/Bingbot per Reverse-DNS, ersatzweise die Google-IP-Bereiche aus
`https://developers.google.com/static/search/apis/ipranges/googlebot.json`)
und die Sitemap-Dateien generell freigeben. Danach in der Search Console
„Live-Test" auf einer Kategorie- und einer Produktseite pruefen.

Die REST-API mit Schluesselpaar ist nicht betroffen (200), die Pflege laeuft
weiter.
