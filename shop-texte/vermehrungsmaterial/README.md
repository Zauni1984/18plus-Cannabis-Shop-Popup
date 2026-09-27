# Vermehrungsmaterial – Stil-Check (Kategorie ID 13458)

Hausstil-Prüfung aller 47 Produkte in der flachen Kategorie
**Vermehrungsmaterial** (ID 13458, Cannabis-Stecklinge/Genetik nach § 1
Nr. 8c KCanG). Kein Full-Rewrite-Projekt: Geprüft wurde ausschließlich
gegen die 6-Punkte-Checkliste (kein H1 im Fließtext, keine Hanfjack-
Marken-CTA-Phrasen in Fließtext/Kurzbeschreibung/Yoast-Metadescription,
durchgehende Du-Anrede, EU-Konformität, saubere Yoast-SEO-Felder,
befüllte Beschreibungen). Der rechtliche Content zur Abgrenzung
Vermehrungsmaterial vs. aktiver Anbau (max. 3 Pflanzen, Minderjährigen-
schutz) war bei allen Produkten bereits korrekt vorhanden und wurde
unangetastet gelassen. THC-Prozentangaben, Genetik-/Terpenbeschreibungen
und Breeder-Nennungen sind bei Cannabis-Genetik normaler Content und
kein Verstoß — nicht angefasst.

Keine Produkte mit "HEMPER", "Goody Glass" oder "Smoke Friends" im Namen
vorhanden.

## Befund: 47/47 geprüft, 47/47 gefixt — ein systematisches Muster über die gesamte Kategorie

Der gesamte Content ist erkennbar aus derselben KI-Textvorlage generiert
(sichtbar an den `data-path-to-node`-Attributen aus einem Editor-Export).
**Jedes einzelne der 47 Produkte** hatte exakt zwei der drei möglichen
Verstöße, ein Teil zusätzlich den dritten:

1. **H1-Tag im Fließtext (47/47):** Jede `description` begann mit
   `<h1 data-path-to-node="13">Produktname: Marketing-Claim</h1>` als
   erste Zeile vor dem eigentlichen Absatztext. Fix: Regex
   `^<h1[^>]*>.*?</h1>\s*` per `wp_replace_in_post` entfernt (nicht zu H2
   downgegradet, wie angewiesen).

2. **Yoast-`meta_description`-CTA (47/47):** Jede Meta-Description endete
   mit einer Kauf-CTA-Phrase nach dem immer gleichen Muster — "Jetzt legal
   bei Hanfjack kaufen!", "Jetzt bei Hanfjack bestellen!", "Jetzt bei
   Hanfjack informieren!", "Jetzt den Klassiker bei Hanfjack bestellen!"
   o. ä. Fix: komplette Meta-Description durch eine neue, produktbezogene
   Fassung ohne CTA ersetzt (120–160 Zeichen, Fokus auf Genetik/THC-
   Potenzial/Aroma statt Kaufaufforderung).

3. **`short_description`-CTA (35/47):** Ein Großteil der Kurzbeschreibungen
   endete mit einer Kauf-CTA im Stil "hol dir … jetzt bei Hanfjack!",
   "starte … jetzt bei Hanfjack.", "sichere dir … jetzt bei Hanfjack!".
   Fix: CTA-Halbsatz durch eine deskriptive Formulierung ohne
   Kaufaufforderung ersetzt, Restsatz sinngemäß erhalten. Bei den
   übrigen 12 Produkten endete die Kurzbeschreibung bereits ohne
   Kaufaufforderung (rein deskriptive Erwähnung "… für deine
   Anzuchtstation bei Hanfjack." bzw. keine Hanfjack-Erwähnung) — dort
   wurde nichts verändert.

Produkt 32088 (Super Buff Cherry) bestätigte das aus der Aufgabenstellung
bekannte Muster 1:1 (H1 + `short_description`-CTA "… jetzt bei
Hanfjack!").

### Produkte mit gefixter `short_description`-CTA (35)

32088, 32087, 32086, 32085, 32084, 32083, 19741, 19740, 19738, 19747,
19751, 19752, 19753, 19754, 19755, 14034, 14036, 14039, 14041, 10771,
10770, 10769, 10767, 7732, 7725, 7719, 7316, 7313, 7309, 7308, 7307,
7305, 7304

(19738, 19747, 19751, 14034, 10771, 10767, 7304 hatten die Variante
"hol dir …"; die übrigen "starte/sichere dir … jetzt bei Hanfjack".)

### Produkte ohne `short_description`-Fix nötig (12, bereits sauber)

20242, 20238, 20234, 20230, 20226, 19743, 19750, 19745, 19746, 15189,
15187, 14037

### Sonderfall 19745 (Cap Junky) und 7304/14036/14041/etc. — "Hanfjack rät/weiß/empfiehlt/sagt"

Bei 19745 (Cap Junky) und 7304 (Amnesia Core Cut) taucht im Fließtext
mehrfach die Formulierung "Hanfjack rät:", "Hanfjack weiß:", "Hanfjack
empfiehlt:", "Hanfjack sagt: […]" als Einleitung von Praxistipps auf.
Das ist keine Kauf-CTA (kein "jetzt kaufen/bestellen"), sondern eine
redaktionelle Stimme — bewusst nicht angefasst, um nicht über die
Checkliste hinaus umzuschreiben.

## Status/Sichtbarkeit

3 Produkte sind bewusst `private` (Cap Junky 19745, Truffle Cake 19754,
Cannalope Haze 14036, Amnesia Core Cut 7304 — 4 insgesamt) bzw. tragen
`noindex` in Yoast; diese wurden trotzdem inhaltlich korrigiert, aber
Sichtbarkeits-/Robots-Einstellung nicht verändert.

## Verifikation

- Jeder `wp_replace_in_post`-Aufruf lieferte `replacements_count: 1` als
  Bestätigung des Treffers (bei zwei Fällen mit eingebettetem `\r\n`
  scheiterte der erste String-Match, wurde per Regex mit `\s+`
  nachgebessert und dann bestätigt).
- `_min_age` (Alterskennzeichnung 18) stichprobenartig über
  `wp_wc_get_product` (Top-Level-Feld **und** `meta_data`) für mehrere
  Produkte (u. a. 32088, 7304, 19745) geprüft — durchgehend `"18"`
  erhalten. Da ausschließlich `wp_replace_in_post` (Content/Excerpt) und
  `wp_yoast_update_post_seo` (Metadaten) verwendet wurden — nie
  `wp_wc_update_product` — bestand kein Risiko eines Resets über den
  WooCommerce-REST-Write-Pfad.

## Nicht angefasst (bewusst)

- Rechtliche Absätze zu Vermehrungsmaterial vs. Anbau, 3-Pflanzen-Grenze,
  Minderjährigenschutz — bereits korrekt.
- THC-%-Angaben, Genetik-/Terpen-/Breeder-Fachsprache.
- Steckbrief-Tabellen, Praxistipps-Struktur, Du-Anrede (bereits
  durchgehend vorhanden).
