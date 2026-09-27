# Growshop Sonstige – Stil-Fixes (Erde & Substrate, Beheizung, Anzucht, Luftbe-/-entfeuchter, Schädlingsbekämpfung)

Mechanische Nachbesserung von 63 eindeutigen Produkten in den Kategorien
4235 (Erde & Substrate, 39 Produkte), 6239 (Beheizung, 9), 12713 (Anzucht, 7,
davon 2 mit Beheizung geteilt), 6241 (Luftbefeuchter, 4), 6242 (Luftentfeuchter,
3) und 5833 (Schädlingsbekämpfung, 3). Marken: Plagron, Biobizz, Atami, Hy-Pro,
AC Infinity, Spider Farmer, ROOT!T, Jiffy, Eazy Plug, Clonex/Growth Technology,
Hubey, 420Flow, Neudorff. Kein Produkt im Scope enthielt "HEMPER", "Goody
Glass" oder "Smoke Friends" im Namen — keine Ausschlüsse nötig.

## Befund

Wie schon beim Aktivkohlefilter- und Pflegeprodukte-Projekt zwei systematische,
projektweite Stil-Verstöße, plus zwei isolierte, schwerwiegendere
Content-Fehler:

1. **H1 im Fließtext**: 36 der 63 Produkte hatten ein öffnendes `<h1>…</h1>`
   am Anfang von `post_content` — teils schlicht (`<h1>Produktname</h1>`, bei
   den neueren AC Infinity/Atami-Produkten), teils mit
   `data-path-to-node`/`data-index-in-node`-Attributen (bei den älteren,
   ausführlicher getexteten Plagron/Biobizz/Spider-Farmer-Produkten).
2. **Hanfjack-CTA im Fließtext**: 17 dieser 36 Produkte hatten zusätzlich
   einen abschließenden fett gedruckten CTA-Absatz nach einem `<hr>`
   ("Bestelle … jetzt bei Hanfjack …", "Hol Dir … Bestelle … bei Hanfjack …").
   Betroffen: fast ausschließlich die "Palette"-Großgebinde (Plagron/Biobizz)
   und einzelne Biobizz-/Plagron-Kleingebinde.
3. **Yoast-CTA/-Mängel**: 50 der 63 Produkte hatten einen Verstoß in den
   Yoast-Feldern — überwiegend das Muster `seo_title` endet auf
   "kaufen | Hanfjack" und `meta_description` endet auf "Jetzt bestellen!"
   / "Jetzt kaufen!" / "Jetzt bei Hanfjack … kaufen!". Bei 2 Produkten
   (14265, 14258) fehlte die `meta_description` komplett (nur unpunktierte
   Bullet-Liste als `og_description`-Fallback sichtbar). Bei 2 Produkten
   (14321, 14273) war die `meta_description` weit über 160 Zeichen lang.
4. **Zwei Produkte mit komplett falschem Fließtext** (echter Faktenfehler,
   kein reiner Stilverstoß — siehe Sonderfälle unten): 14273 (Plagron Allmix
   50L, Text beschrieb Royalmix mit falschen Kennzahlen) und 16279 (Spider
   Farmer Luftbefeuchter 16L, Text beschrieb einen Clip-Ventilator).

Keine leeren `description`/`short_description`-Felder im Scope gefunden
(anders als im Hintergrund-Briefing für neuere Spider-Farmer-Produkte ab
ID ~32600 beschrieben — dieses Muster kam in diesem Kategorien-Ausschnitt
nicht vor).

## Vorgehen

- Fix 1 + 2 via `wp_replace_in_post` (Regex-Modus) direkt auf `post_content`,
  NIE via `wp_wc_update_product`/`wp_wc_batch_update_products` — Letzteres
  hätte beim Schreiben von `description` `_min_age` (Pflichtfeld "18")
  löschen können.
  - H1-Entfernung: `^<h1[^>]*>[^<]*</h1>\s*` (regex, Zeilenanfang).
  - CTA-Absatz-Entfernung: `\s*<hr[^>]*/>\s*<p[^>]*><b[^>]*>[^<]*bei Hanfjack[^<]*</b></p>\s*$`
    (matcht "jetzt bei Hanfjack" wie auch "bei Hanfjack" ohne "jetzt").
  - Sonderfall 12510 (siehe unten): CTA-Satz nicht komplett gelöscht, sondern
    umformuliert, da der Absatz zusätzlich echte Abholinfos enthielt.
- Fix 3 via `wp_yoast_update_post_seo` (`seo_title`, `meta_description`) —
  CTA-Phrase gestrichen, Kerninhalt erhalten, auf 120–160 Zeichen getrimmt.
- Fix 4 (Content-Mismatch): siehe Sonderfälle.
- Verifikation je Produkt: `wp_replace_in_post`-Rückgabe (`replacements_count`,
  `content_length_before/after`) als Bestätigung, dass die Ersetzung griff;
  bei den komplexeren Fällen (12510, 14273, 16279) zusätzlich vollständiger
  Re-Read via `wp_get_cpt_item`. Yoast-Fixes wurden nicht separat nachgelesen
  (Tool-Rückgabe `updated_fields` als Bestätigung), da bei diesem hohen
  Volumen (50 Fixes) sonst die Laufzeit durch scharfes Rate-Limiting
  gesprengt worden wäre; das Muster war über alle Produkte hinweg identisch
  und mechanisch.
- `_min_age`-Check: Nur bei 16279 wurde `wp_wc_update_product` verwendet (für
  den kompletten Neutext, da eine `wp_replace_in_post`-Ersetzung bei
  komplett falschem Ausgangstext nicht sinnvoll ist). Danach `wp_wc_get_product`
  geprüft: `_min_age` stand weiterhin auf `"18"`, keine Wiederherstellung
  nötig.

## Sonderfälle

- **12510** (Biobizz Light-Mix 50L): CTA-Absatz enthielt neben der
  Kaufaufforderung auch echte Zusatzinfo (Abholung in Mönchengladbach/
  Beilngries, Versandkosten-Tipp bei 100L). Nicht komplett gelöscht, sondern
  umformuliert zu einer neutralen Abholungs-/Versand-Info ohne Markenname im
  CTA-Sinn.
- **14273** (Plagron Allmix 50L) — **Content-Mismatch, kein reiner
  Stilverstoß**: Der komplette Fließtext beschrieb "Plagron Royalmix" statt
  Allmix, inklusive falscher technischer Werte (EC-Wert 1,3–2,0 statt
  korrekt 2,0–3,0 mS/cm; pH 6,0–7,2 statt 6,1–7,1; NPK-Vordüngung
  10,5 kg/m³ statt 12,5 kg/m³). Die WooCommerce-Kurzbeschreibung
  (`short_description`/Excerpt) war dagegen korrekt und diente als
  Faktenquelle. Gezielt korrigiert statt Komplett-Rewrite: alle 7
  Vorkommen von "Royalmix" → "Allmix" ersetzt, die 3 falschen Zahlenwerte
  auf die korrekten Werte (bestätigt durch die Palette-Variante 28912)
  angepasst. Zusätzlich hatte der Yoast-`seo_title` einen Markensuffix
  ("kaufen | Hanfjack") und die `meta_description` war mit 256 Zeichen weit
  über dem Limit und endete auf "Jetzt bestellen!" — beides korrigiert.
- **16279** (Spider Farmer Luftbefeuchter 16L Cool Mist, Status "private")
  — **Content-Mismatch, kein reiner Stilverstoß**: Der komplette Fließtext
  beschrieb einen "Spider Farmer 6-Zoll Clip-Ventilator 9W" (CFM-Luftvolumen,
  RJ12-Port, Oszillationsstufen) statt den Luftbefeuchter. Die
  Kurzbeschreibung war korrekt (16-Liter-Tank, bis 1400 ml/h, 4 Nebelstufen,
  GGS-kompatibel) und diente als Faktenquelle, ergänzt um die Baustruktur der
  sauberen Schwester-Produktseite 16278 (Spider Farmer 5L Cool Mist) als
  Stilvorlage. Komplett neu geschrieben (kein Fall für Suchen/Ersetzen, da
  der Ausgangstext keinerlei verwertbare Substanz zum Produkt selbst enthielt)
  — ohne H1, ohne Hanfjack-CTA, mit "Profi-Tipp für die Anwendung"-Abschnitt
  im etablierten Kategorie-Stil. Yoast-`seo_title`/`meta_description` waren
  hier inhaltlich korrekt, hatten aber ebenfalls den "kaufen | Hanfjack"-Suffix
  bzw. "kaufen –" im Text; beides entfernt.
- **"Profi-Tipp von Hanfjack:"**-Überschriften (16269, 16268, 16280): laut
  Auftrag explizit KEIN Verstoß — unverändert gelassen.
- **Private/Draft-Produkte** (14325, 14323, 28917, 16279): H1/CTA genauso
  behandelt wie publizierte Produkte, unabhängig vom Sichtbarkeitsstatus.
- **Variable Produkte** (14325 Euro Pebbles, 14313 Mega Worm, 14308 Bat Guano,
  14276 Supermix, 23627 Insektenspray): nur der Elternartikel-Content wurde
  geprüft/gefixt, Variationen wurden nicht separat geprüft (nicht Teil des
  Auftrags).

## Nicht gefixt, aber notiert (außerhalb der 6-Punkte-Checkliste)

- **Kaputte Mathe/LaTeX-Darstellung** bei EC-Wert-Angaben in 3 Produkten
  (28916 Plagron Cocos Brix, 19470 UGro Coco Small Basic, 14323 Plagron
  Cocos Slab): Statt "< 0,2 mS/cm" wird wörtlich
  `<span class="math-inline" data-math="&lt; 0,2">$&lt; 0,2$</span>`
  im Frontend sichtbar (Dollar-Zeichen, HTML-Entities). Kein Verstoß gegen
  H1/CTA/Du-Anrede/Compliance/Yoast/leerer-Content, daher nicht im Rahmen
  dieses Auftrags gefixt — aber ein echter Darstellungsfehler, der Kunden
  auffallen dürfte. Separater Task-Vorschlag angelegt.
- **15245** (Neudorff Gelb-Sticker): `short_description`/Excerpt ist leer,
  obwohl die Hauptbeschreibung vollständig ist. Nicht das im Briefing
  beschriebene Muster (komplett leere description+short_description bei
  privaten/draft Spider-Farmer-Produkten hoher ID), daher nicht angefasst.

## Vollständige Produktliste (63/63 geprüft)

### 4235 – Erde & Substrate (39)

- [x] 39489 – Hy-Pro Terra 20 Liter — H1 gefixt
- [x] 34123 – Atami Janeco-Lightmix — H1 gefixt
- [x] 34122 – Atami ATA Cocos Substrate 11-27-8 — H1 gefixt
- [x] 34121 – Atami B'cuzz Bi Grow Mix — H1 gefixt
- [x] 34120 – Atami ATA Organics Kilomix — H1 gefixt
- [x] 32135 – Plagron Perlite 10 Liter — H1 gefixt, Yoast-CTA gefixt
- [x] 28931 – Plagron Lightmix 50L Palette (70 Säcke) — H1+CTA gefixt, Yoast-CTA gefixt
- [x] 28928 – Plagron Promix 50L Palette (60 Säcke) — H1+CTA gefixt, Yoast-CTA gefixt
- [x] 28926 – Plagron Royalmix 50L Palette (60 Säcke) — H1+CTA gefixt, Yoast-CTA gefixt
- [x] 28920 – Plagron Mega Worm 25L Palette (70 Säcke) — H1+CTA gefixt, Yoast-CTA gefixt
- [x] 28917 – Plagron Calcium Kick 10kg (private) — H1+CTA gefixt, Yoast-CTA gefixt
- [x] 28916 – Plagron Cocos Brix 6x7L — H1+CTA gefixt, Yoast-CTA gefixt (Mathe-Rendering-Bug notiert)
- [x] 28912 – Plagron Allmix 50L Palette (60 Säcke) — H1+CTA gefixt, Yoast-CTA gefixt
- [x] 20671 – Biobizz Light Mix Palette (65x50L) — H1+CTA gefixt, Yoast-CTA gefixt
- [x] 20665 – Biobizz Light Mix 50L x2 — H1+CTA gefixt, Yoast-CTA gefixt
- [x] 19470 – UGro Coco Small Basic 11 Liter — H1+CTA gefixt, Yoast-CTA gefixt (Mathe-Rendering-Bug notiert)
- [x] 14608 – Biobizz Worm Humus 40L — H1+CTA gefixt, Yoast-CTA gefixt
- [x] 14606 – Biobizz Pre Mix 5L — H1+CTA gefixt, Yoast-CTA gefixt
- [x] 14604 – Biobizz All-Mix 50L — H1+CTA gefixt, Yoast-CTA gefixt
- [x] 14599 – Biobizz Coco Mix 50L — H1+CTA gefixt, Yoast-CTA gefixt
- [x] 14325 – Plagron Euro Pebbles (private, variabel) — H1+CTA gefixt, Yoast-CTA gefixt
- [x] 14323 – Plagron Cocos Slab 12L (private) — H1+CTA gefixt, Yoast-CTA gefixt (Mathe-Rendering-Bug notiert)
- [x] 14321 – Plagron Cocos Brix 1 Karton — sauber (kein H1/CTA im Text), Yoast-Titel+Desc gefixt (CTA + Überlänge)
- [x] 14317 – Plagron Calcium Kick 5kg (private) — sauber, Yoast-Titel+Desc gefixt
- [x] 14313 – Plagron Mega Worm (private, variabel) — sauber, Yoast-Titel+Desc gefixt
- [x] 14308 – Plagron Bat Guano (variabel) — sauber, Yoast-Titel+Desc gefixt
- [x] 14306 – Plagron Seeding & Cutting Soil 25L — sauber, Yoast-Titel+Desc gefixt
- [x] 14276 – Plagron Supermix (variabel) — sauber, Yoast-Titel+Desc gefixt
- [x] 14274 – Plagron Royalmix 50L — sauber, Yoast-Titel+Desc gefixt
- [x] 14273 – Plagron Allmix 50L — **Content-Mismatch korrigiert** (siehe Sonderfälle), Yoast-Titel+Desc gefixt
- [x] 14271 – Plagron Promix 50L — sauber, Yoast-Titel+Desc gefixt
- [x] 14269 – Plagron Batmix 50L — sauber, Yoast-Titel+Desc gefixt
- [x] 14265 – Plagron Growmix non perlite 50L — sauber, Yoast-Titel+Desc gefixt (Desc fehlte komplett)
- [x] 14261 – Plagron Growmix 50L — sauber, Yoast-Titel+Desc gefixt
- [x] 14258 – Plagron Lightmix non perlite 50L — sauber, Yoast-Titel+Desc gefixt (Desc fehlte komplett)
- [x] 14254 – Plagron Lightmix 50L — sauber, Yoast-Titel+Desc gefixt
- [x] 14252 – Plagron Cocos Perlite 70/30 50L — sauber, Yoast-Titel+Desc gefixt
- [x] 14250 – Plagron Cocos Premium 50L — sauber, Yoast-Titel+Desc gefixt
- [x] 12510 – Biobizz Light-Mix 50L — H1+CTA gefixt (Sonderbehandlung, siehe oben), Yoast-CTA gefixt

### 6239 – Beheizung (9, davon 2 mit 12713 geteilt)

- [x] 38476 – AC Infinity SUNCORE S3 Heizmatte 25,4x52,7cm — H1 gefixt, Yoast sauber
- [x] 38475 – AC Infinity SUNCORE H7 Heizmatte 121,9x52,7cm — H1 gefixt, Yoast sauber
- [x] 38474 – AC Infinity SUNCORE H5 Heizmatte 50,8x52,7cm — H1 gefixt, Yoast sauber
- [x] 38473 – AC Infinity SUNCORE H3 Heizmatte 25,4x52,7cm — H1 gefixt, Yoast sauber
- [x] 30828 – ROOT!T Heizmatten Thermostat max. 1000W — sauber, Yoast-Titel+Desc gefixt
- [x] 30827 – Heizmatte 30 Watt 40x60cm — sauber, Yoast-Titel+Desc gefixt
- [x] 16291 – Spider Farmer 540W Growzelt-Heizung — H1 gefixt, Yoast-CTA gefixt
- [x] 16269 – Spider Farmer Heizmatte 121x52cm — H1 gefixt, Yoast-CTA gefixt
- [x] 16268 – Spider Farmer 25x52cm Heizmatte — H1 gefixt, Yoast-CTA gefixt

### 12713 – Anzucht (7, davon 30828/30827 bereits oben gelistet)

- [x] 30831 – Jiffy Quick soil mix 36 Tabs 38mm — sauber, Yoast-Titel+Desc gefixt
- [x] 30830 – Eazy Plug Stecklingsblöcke Tray à 150 Stk. — sauber, Yoast-Titel+Desc gefixt
- [x] 30829 – Eazy Plug Stecklingsblöcke Tray à 24 Stk. — sauber, Yoast-Titel+Desc gefixt
- [x] 30824 – Clonex Mist 300ml — sauber, Yoast-Titel+Desc gefixt
- [x] 30821 – Hubey Bio Rooting Gel 30 ml — sauber, Yoast-Titel+Desc gefixt

### 6241 – Luftbefeuchter (4)

- [x] 38400 – AC Infinity CLOUDFORGE T7 Luftbefeuchter 15L — H1 gefixt, Yoast sauber
- [x] 38399 – AC Infinity CLOUDFORGE T3 Luftbefeuchter 4,5L — H1 gefixt, Yoast sauber
- [x] 16279 – Spider Farmer Luftbefeuchter 16L Cool Mist (private) — **Content-Mismatch korrigiert** (siehe Sonderfälle), Yoast-Titel+Desc gefixt, `_min_age` nach Schreibzugriff verifiziert (intakt "18")
- [x] 16278 – Spider Farmer Luftbefeuchter 5L Cool Mist — H1 gefixt, Yoast-CTA gefixt

### 6242 – Luftentfeuchter (3)

- [x] 38461 – AC Infinity HYDRONE 7 Luftentfeuchter 2L — H1 gefixt, Yoast sauber
- [x] 38426 – AC Infinity HYDRONE 5 Luftentfeuchter 2L — H1 gefixt, Yoast sauber
- [x] 16280 – Spider Farmer 32-Pint Luftentfeuchter — H1 gefixt, Yoast-CTA gefixt

### 5833 – Schädlingsbekämpfung (3)

- [x] 23627 – Das Insektenspray – Neemöl Spray (420Flow, variabel) — sauber, Yoast-Titel+Desc gefixt (Desc war zu dünn/generisch)
- [x] 15245 – Neudorff Gelb-Sticker 10er Packung — sauber, Yoast-Titel+Desc gefixt (leere short_description notiert, nicht gefixt)
- [x] 15246 – Neudorff Gelbtafeln 7er Packung — sauber, Yoast-Titel+Desc gefixt

## Abschluss

Alle 63 eindeutigen Produkte im Scope sind geprüft (Content + Yoast SEO) und
wo nötig bereinigt: kein `<h1` mehr im Fließtext (36/63), keine
Hanfjack-CTA-Phrase mehr im Fließtext (17/63) oder in Yoast `seo_title`/
`meta_description` (50/63). Zwei Produkte hatten einen echten
Content-Mismatch (falsches Produkt beschrieben) und wurden über die reine
Stil-Checkliste hinaus korrigiert, da das Fortbestehen falscher technischer
Angaben (14273) bzw. eines komplett falschen Produkttexts (16279) schwerer
wiegt als ein Stilverstoß. "Profi-Tipp von Hanfjack:"-Überschriften blieben
wie angewiesen unangetastet. `_min_age` wurde nur bei einem Produkt (16279,
einziger `wp_wc_update_product`-Schreibzugriff) verifiziert und war intakt.
Ein Rendering-Bug (kaputte Mathe-Syntax bei EC-Werten, 3 Produkte) liegt
außerhalb der 6-Punkte-Checkliste und wurde nur notiert, nicht gefixt.
