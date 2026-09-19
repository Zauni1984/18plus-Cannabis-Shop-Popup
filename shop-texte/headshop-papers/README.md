# Headshop-Papers – Stil-Check (Kategorien Papers, Pre Rolled Papers)

Hausstil-Prüfung der Produkte in den beiden Headshop-Unterkategorien
**Papers** (ID 607, 119 Produkte gesamt) und **Pre Rolled Papers**
(ID 4706, 81 Produkte gesamt). In beiden Kategorien wurden alle Produkte,
deren Name "HEMPER" enthält, komplett ausgeschlossen (71 in Papers, 65 in
Pre Rolled Papers – jeweils die komplette HEMPER-Cones/Rolls/Glasfilter-Serie).
"Goody Glass" und "Smoke Friends" kamen in keiner der beiden Kategorien vor.

Alle 16 nicht-HEMPER Produkte aus Pre Rolled Papers sind eine echte
Teilmenge der 48 nicht-HEMPER Produkte aus Papers (G-Rollz Pre-Rolled-Serie,
King-Palm-Serie, PURIZE, RAW Challenge/Cone Filler/Organic Pre Rolled/Pre
Rolled). Es musste daher nur **eine** Checkliste mit 48 Produkten
durchgearbeitet werden, nicht zwei separate, sich überlappende Listen.

Wie beim Headshop-Mini-Projekt war dies **kein** Full-Rewrite: Fixiert wurden
nur tatsächliche Verstöße gegen die 6-Punkte-Checkliste (kein H1 im Text,
keine Hanfjack-Marken-CTA, durchgehende Du-Anrede, EU-Konformität ohne
gesundheits-/tabakverherrlichende Aussagen, saubere Yoast-SEO-Felder,
saubere `short_description`).

## Befund

**17 von 48 Produkten** hatten tatsächliche Verstöße und wurden gezielt
gefixt, **31 Produkte** waren bereits konform und blieben unverändert.

Zwei Verstoß-Muster traten auf:

1. **Fehlende Yoast-`meta_description`** (5 Produkte: 8995, 8992, 8959,
   8955 sowie – als beschädigte statt fehlende Variante – 22813): Bei den
   ersten vier Produkten fehlte das Feld komplett und wurde nach dem
   etablierten Hausstil-Muster (faktisch, EAN-terminiert, 120–160 Zeichen,
   keine CTA) neu ergänzt. Bei 22813 (G-Rollz Banksy Graffiti Lizzie
   Stardust Unbleached) war das Feld vorhanden, aber offensichtlich
   abgeschnitten ("… extra dünne. EAN: …" – Satz ohne Bezugswort) und wurde
   korrigiert.
2. **Durchgängige Sie-Anrede statt Du-Anrede** in `post_content` (3
   Produkte, alle Gizeh King Size Slim): 520, 521 und 528 verwendeten in
   ihren Fließtexten noch komplett die formelle Sie/Ihr-Anrede
   ("Erleben Sie …", "bietet Ihnen …", "Entscheiden Sie sich …") – ein
   älteres Textmuster, das in dieser Produktreihe konsequent auf Du
   umgestellt wurde (insgesamt 10 Einzel-Ersetzungen über die drei
   Produkte). Bei 520 zusätzlich mehrere zugehörige `Ihr/Ihre/Ihnen`-Bezüge
   im Fließtext auf `dein/deine/dir` umgestellt. Bei 528 und 521 wurde
   zusätzlich die Sie-Anrede im Yoast-Metadesc ("Entdecken Sie …",
   "Probieren Sie …") auf Du umgestellt.

Die restlichen 9 Fixes (30303, 18687, 18686, 18685, 30294, 30290, 9378,
12472, 9392) betrafen ausschließlich Yoast-`meta_description`-Korrekturen
(Länge/Formulierung) ohne Eingriff in `post_content` – bei diesen war der
Fließtext bereits sauber (keine H1, keine Marken-CTA, keine Sie-Anrede).

Bei allen 48 Produkten waren `_yoast_wpseo_focuskw` und `_yoast_wpseo_title`
durchgehend gesetzt und unauffällig; die SEO-Titel-Endung "Produktname |
Hanfjack" ist erwartungsgemäß Standard und kein Verstoß. `short_description`
war bei allen geprüften Produkten bereits sauber (reine Bullet-Listen ohne
H1/CTA-Probleme, teils leer) und wurde nicht verändert.

## Vorgehen

- Textfixes in `post_content` ausschließlich über `wp_replace_in_post`
  (String-Suche, kein Regex nötig), **nie** über
  `wp_wc_update_product`/`wp_wc_batch_update_products` — Letzteres hätte
  beim Schreiben von `description` ungewollt `_min_age` (Pflichtfeld "18")
  löschen können.
- Sie→Du-Umstellung produktweise pro Satz/Absatz als eigener
  `wp_replace_in_post`-Aufruf (Suchstring = kompletter Satz mit
  Kontext, um Eindeutigkeit sicherzustellen), da die Anrede über mehrere
  Verbformen und Possessivpronomen hinweg konsistent umgestellt werden
  musste (z. B. "genießen Sie … Ihrer Zigaretten, sondern unterstützen"
  → "genießt du … deiner Zigaretten, sondern unterstützt").
- Yoast-Fixes über `wp_yoast_update_post_seo` (`meta_description`):
  fehlende Felder nach Hausstil-Muster ergänzt (Produktname, Format/Maße,
  Kernfakten, EAN, 120–160 Zeichen), ein abgeschnittener Satz repariert,
  Sie-Anrede in zwei Metadescs auf Du umgestellt.
- Verifikation je Fix durch erneutes Lesen: `wp_wc_get_product` für
  `post_content`-Änderungen, `wp_yoast_get_post_seo` für alle SEO-Fixes –
  jeweils der bereinigte Wert im Live-Ergebnis bestätigt.
- Zur Bestätigung der Gesamtzahlen (119 Papers gesamt / 81 Pre Rolled
  Papers gesamt, 71 bzw. 65 davon HEMPER) wurden beide Kategorien
  vollständig paginiert über `wp_wc_list_products` (Status "any", je 100
  pro Seite) abgefragt.
- Rate-Limit-Handling: Bei `wp_yoast_update_post_seo`- und vereinzelt auch
  `wp_yoast_get_post_seo`-Aufrufen traten wiederholt "Rate limit exceeded"-
  Fehler auf, auch bei Einzelaufrufen kurz nacheinander. Abhilfe: Wartezeit
  über `Bash`-Sleep-Loop (15–40 Sekunden, eskalierend), danach ausschließlich
  Einzelaufrufe statt Parallel-Batches – das hat die Fehlerrate zuverlässig
  auf null gebracht, ohne Datenverlust oder Duplikate.

## Sonderfälle

- **Private-Status-Produkte** (521, 520 Gizeh Bio-Hanf & 2in1;
  9378, 9392 RAW Black Organic/Classic; 13851 RAW Pre Rolled 1000 Stück):
  normal geprüft und wo nötig gefixt wie alle anderen, keine
  Sonderbehandlung. 521 und 520 trugen trotz `noindex`-Status dieselben
  Sie-Anrede-Verstöße wie das publizierte Geschwisterprodukt 528 und wurden
  ebenfalls korrigiert.
- **Nicht-Hanfjack-CTA im Fließtext** (9027, RAW Cone Filler King Size):
  enthält im Body eine generische Kauf-Ermunterung ("Sichere dir jetzt die
  RAW Cone Filler …") ohne Markennennung "Hanfjack" – laut Checkliste-Punkt 2
  (der explizit nur Hanfjack-Marken-CTA verbietet) formal kein Verstoß,
  daher unverändert gelassen.
- **Markenerwähnung ohne CTA-Charakter** (18985, Juicy Jay's Mix n Roll
  KSS 2in1 Box): Yoast-Metadesc enthält "Im Headshop Hanfjack" – eine reine
  Standort-/Kontextangabe ohne Handlungsaufforderung, damit kein CTA im Sinne
  der Checkliste ("Bestelle X jetzt bei Hanfjack") und unverändert gelassen.
- **Cross-Listing mit Kategorie Blunts** (19892–19896 King Palm Slim Rolls,
  9013/9009/9006 sowie perspektivisch weitere Juicy-Jay's-Produkte): diese
  Produkte erscheinen zusätzlich in der Kategorie Blunts (ID 4224) und
  wurden dort im Rahmen des Headshop-Mini-Projekts bereits geprüft. In
  diesem Durchgang wurden sie regulär erneut verifiziert (kein Doppel-Fix
  nötig, alle bereits konform) statt übersprungen.
- **HEMPER-Ausschluss**: 71 Produkte in Papers und 65 in Pre Rolled Papers
  (komplette HEMPER French Cones/King-Size- und Mini-Size-Cones/Rolls-Serie
  mit Glas- oder Kartonfilter, jeweils in 5–8 Geschmacksrichtungen) wurden
  vollständig von der Prüfung ausgenommen, wie in der Aufgabenstellung
  vorgegeben.

## Vollständige Produktliste (48/48, HEMPER-Produkte ausgeschlossen)

- [x] 14386 – Barneys Farm KS 32 2in1 — OK
- [x] 9002 – Bob Marley King Size — OK
- [x] 30303 – e-zwider Strawberry flavored 1 1/2 - 1 Box — FIXED (Yoast metadesc)
- [x] 8950 – Elements Green King Size Slim — OK
- [x] 18982 – Elements King Size Slim Box 50 Hefte á 32 — OK
- [x] 8948 – Elements Pink King Size Slim — OK
- [x] 18983 – Elements Pink King Size Slim Box 50 Hefte á 32 — OK
- [x] 8955 – Elements Rainbow King Size — FIXED (Yoast metadesc fehlte komplett → ergänzt, 144 Zeichen)
- [x] 15601 – G-Rollz Lightly Dyed Pink Pre Rolled KS 20er — OK
- [x] 18687 – G-Rollz Banksy Graffiti Flower Thrower Unbleached Pre Rolled KS 20er — FIXED (Yoast metadesc)
- [x] 22813 – G-Rollz Banksy Graffiti Lizzie Stardust Unbleached — FIXED (Yoast metadesc war abgeschnitten/unvollständig, repariert)
- [x] 18686 – G-Rollz Collector Colossal Dream Organic Green Hemp Pre Rolled KS 20er — FIXED (Yoast metadesc)
- [x] 18685 – G-Rollz Collector Naked Shroom Blue Pre Rolled KS 20er — FIXED (Yoast metadesc)
- [x] 30294 – G-Rollz Diablos King Slim Classic ultra dünn - 1 Box — FIXED (Yoast metadesc)
- [x] 30290 – G-Rollz King´s Choice KS - 1 Box — FIXED (Yoast metadesc)
- [x] 520 – Gizeh King Size Slim 2in1 (Status: private) — FIXED (Sie/Ihr→Du/dein in post_content, 5 Ersetzungen)
- [x] 521 – Gizeh King Size Slim Bio-Hanf & Gras 34 Blättchen (Status: private) — FIXED (Sie→Du in post_content ×2 + Yoast metadesc)
- [x] 528 – Gizeh King Size Slim extra fein 34 Blatt - 1 Packung — FIXED (Sie→Du in post_content ×3 + Yoast metadesc)
- [x] 21801 – Granny´s Weed 2in1 KSS, eine Box mit 24 Packungen — OK
- [x] 21804 – Granny´s Weed Organic 2in1 KSS, eine Box mit 24 Packungen — OK
- [x] 9006 – Juicy Jay´s Blueberry King Size Slim — OK
- [x] 18984 – Juicy Jay´s Mix n Roll King Size Slim Box 24 x 32 mit Aroma (Status: private) — OK
- [x] 18985 – Juicy Jay´s Mix n Roll KSS 2in1 Box 24 x 32 mit Aroma (Status: private) — OK
- [x] 9009 – Juicy Jay´s Very Cherry King Size Slim — OK
- [x] 9013 – Juicy Jay´s Watermelon KSS mit Aroma — OK
- [x] 19892 – King Palm Banana Cream (1,5g) — OK
- [x] 19893 – King Palm Berry Terps (1,5g) — OK
- [x] 19895 – King Palm Magic Mint (1,5g) — OK
- [x] 19894 – King Palm Margarita (1,5g) — OK
- [x] 19896 – King Palm Watermelon (1,5g) — OK
- [x] 19585 – OCB Slim Premium 32 Blatt — OK
- [x] 13847 – PURIZE PreRolled Xtra Slim - 800 Stück — OK
- [x] 9378 – RAW Black Organic Hemp 1 1/4 Size - 50 Blatt (Status: private) — FIXED (Yoast metadesc)
- [x] 12472 – RAW Challenge - XXXL — FIXED (Yoast metadesc)
- [x] 9392 – RAW Classic 1 1/4 Size - 50 Blatt (Status: private) — FIXED (Yoast metadesc)
- [x] 9027 – RAW Cone Filler King Size — OK (nicht-markengebundene CTA im Fließtext, kein Verstoß laut Checkliste)
- [x] 8959 – RAW Organic Connoisseur King Size Slim 2in1 — FIXED (Yoast metadesc fehlte komplett → ergänzt, 156 Zeichen)
- [x] 15603 – RAW Organic Pre Rolled King Size 32er Pack — OK
- [x] 13851 – RAW Pre Rolled 1 1/4 - 1000 Stück (Status: private) — OK
- [x] 15602 – RAW Pre Rolled 1 1/4 Size 6er Pack — OK
- [x] 18981 – Smoking Brown "Creator" King Size 2in1 Box — OK
- [x] 18978 – Smoking Deluxe King Size Box — OK
- [x] 18979 – Smoking Kukuxumusu KS Box — OK
- [x] 8995 – Smoking Master KS Ultra Slim — FIXED (Yoast metadesc fehlte komplett → ergänzt, 146 Zeichen)
- [x] 18980 – Smoking Master KS Ultra Slim Box — OK
- [x] 8992 – Smoking Red King Size 2in1 — FIXED (Yoast metadesc fehlte komplett → ergänzt, 150 Zeichen)
- [x] 18977 – Smoking Supreme King Size Slim 2in1 Box (Status: private) — OK
- [x] 9018 – Smoking Thinnest King Size — OK

## Abschluss

Alle 48 nicht-HEMPER Produkte der beiden Kategorien Papers und Pre Rolled
Papers sind geprüft (die 16 Pre-Rolled-Papers-Produkte als Teilmenge der 48
Papers-Produkte einmal mitprovft). **17 von 48 Produkten** hatten
tatsächliche Yoast-SEO- oder Hausstil-Verstöße und wurden gezielt gefixt;
**31 Produkte** waren bereits konform und blieben unverändert. Größter
Einzelbefund: eine komplette Sie-Anrede in den Fließtexten dreier Gizeh-
King-Size-Slim-Produkte (520, 521, 528) – ein älteres Textmuster, das
konsequent auf Du umgestellt wurde. `_min_age` und alle sonstigen Post-Meta-
Felder blieben unangetastet, da ausschließlich `wp_replace_in_post`
(Feld-Scope `post_content`) und `wp_yoast_update_post_seo` verwendet wurden.
