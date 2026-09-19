# Samen / Automatisch (Kategorie ID 547) – Seite 3, Stil-Check

Teilbereich der Kategorie **Automatisch** (Samen-Baum, Kategorie ID 547,
insgesamt ca. 268 Produkte). Dieser Agent hat ausschließlich **Seite 3**
der nach ID aufsteigend sortierten Produktliste bearbeitet
(`wp_wc_list_products(category="547", per_page=100, orderby="id",
order="asc", page=3)`) – die 94 Produkte mit den höchsten IDs, von
**33299 (RQS Blue Cheese Auto)** bis **34986 (Ethos Genetics Zweet RBX2
Auto)**. Seite 4 war leer, Seite 3 war damit das Ende der Kategorie.
Ein anderer Agent bearbeitet parallel Seite 1–2 (niedrigere IDs).

Geprüft wurde ausschließlich gegen die 6-Punkte-Hausstil-Checkliste (kein
H1 im Fließtext, keine Hanfjack-Marken-CTA-Phrasen in Fließtext/
Kurzbeschreibung/Yoast-Metadescription, durchgehende Du-Anrede,
EU-Konformität, saubere Yoast-SEO-Felder, befüllte Beschreibungen). Kein
Komplett-Rewrite-Projekt. Rechtlicher Kontext: Cannabis-Samen sind
Vermehrungsmaterial nach § 1 Nr. 8c KCanG; Genetik-/Terpen-/THC-Angaben
sind bei Seedbanks normaler, legaler Content und kein Verstoß – nicht
angefasst. Keine Produkte mit "HEMPER", "Goody Glass" oder
"Smoke Friends" im Namen vorhanden.

## Befund: 94/94 geprüft, 94/94 mit H1-Fix — zwei klar unterschiedliche Content-Batches

Die 94 Produkte stammen erkennbar aus zwei verschiedenen
Content-Erstellungsdurchläufen mit unterschiedlichem Verstoßmuster:

### Batch 1 — RQS-Altbestand (66 Produkte, IDs 33299–33746)

Alle 66 Royal-Queen-Seeds-Produkte (RQS Autoflower- und F1-Sortiment)
folgen exakt derselben Vorlage:

1. **H1-Tag im Fließtext (66/66):** `description` beginnt mit
   `<h1>{Produktname} – Autoflowering Cannabis Samen von Royal Queen
   Seeds</h1>` vor dem eigentlichen Absatztext. Fix: Regex
   `^<h1[^>]*>.*?</h1>\s*` per `wp_replace_in_post` entfernt (nicht zu H2
   downgegradet).
2. **Yoast-`meta_description`-CTA (66/66):** Jede Meta-Description endete
   mit "Jetzt bei Hanfjack sichern!". Fix: CTA-Halbsatz entfernt. Wo die
   verbleibende Beschreibung dadurch unter 120 Zeichen fiel (häufig bei
   kurzen Aroma-/Wirkungslisten), wurde eine sachliche Ergänzung aus dem
   Content selbst angehängt (meist `Genetik: X x Y.`, bei CBD-Sorten auch
   THC-/CBD-Werte) – keine neue CTA, nur Fakteninfo aus der Steckbrief-
   Tabelle des jeweiligen Produkts.
3. **Kurzbeschreibung (`short_description`):** Endet durchgehend mit
   `<strong>Jetzt sichern!</strong>` – **ohne** Hanfjack-Bezug. Laut
   Aufgabenstellung nur Hanfjack-Marken-CTA-Phrasen fixen; da hier keine
   Markennennung vorliegt, bewusst **nicht** angefasst (deckungsgleich
   mit dem bereits abgeschlossenen Vermehrungsmaterial-Projekt, wo dieselbe
   Unterscheidung gemacht wurde).
4. Du-Anrede, EU-Konformität, Yoast-Titel: bereits durchgehend korrekt.

### Batch 2 — Neuere Boutique-Marken (28 Produkte, IDs 34270–34986)

Ace Seeds (1), James Loud Genetics (6), The Cali Connection (3) und Ethos
Genetics (18) – erkennbar aus einem neueren, bereits einmal
qualitätsgeprüften Content-Batch (Artikeltext in sauberem Absatzformat
mit `<p>`/`<h3>`-Struktur, durchgehende Du-Anrede, keine Hanfjack-CTA
irgendwo, Yoast-SEO-Felder bereits vollständig und im 120–160-Zeichen-
Rahmen). Diese Produkte hatten **ausschließlich** den H1-Verstoß:

- **H1-Tag im Fließtext (28/28):** `<h1>{Marketing-Claim}</h1>` als
  erste Zeile, danach folgt bereits sauberer `<p>`-Absatztext. Fix:
  gleiche Regex-Entfernung wie oben.
- Keine CTA-Phrasen (weder Fließtext noch Kurzbeschreibung noch Yoast),
  keine EU-Compliance-Auffälligkeiten, keine leeren Beschreibungen.
  Yoast-Meta-Descriptions waren bereits sauber formuliert und im
  Zielkorridor – nicht verändert.

## Vollständige Liste der 94 bearbeiteten Produkte

**RQS (H1 entfernt + Meta-Description-CTA getrimmt/ergänzt), 66 Produkte:**
33299, 33305, 33311, 33316, 33322, 33328, 33334, 33340, 33346, 33352,
33358, 33364, 33369, 33375, 33381, 33387, 33393, 33398, 33404, 33410,
33416, 33422, 33428, 33434, 33440, 33446, 33452, 33458, 33464, 33470,
33476, 33482, 33488, 33494, 33500, 33506, 33511, 33517, 33522, 33528,
33534, 33540, 33546, 33552, 33558, 33564, 33570, 33575, 33579, 33611,
33617, 33629, 33641, 33647, 33653, 33677, 33694, 33699, 33704, 33710,
33716, 33722, 33728, 33734, 33740, 33746

**Ace Seeds / James Loud Genetics / The Cali Connection / Ethos Genetics
(nur H1 entfernt, SEO bereits sauber), 28 Produkte:**
34270, 34472, 34473, 34481, 34483, 34484, 34490, 34536, 34559, 34562,
34842, 34844, 34849, 34850, 34858, 34896, 34901, 34907, 34928, 34934,
34940, 34941, 34945, 34947, 34951, 34957, 34969, 34986

66 + 28 = 94 Produkte gesamt.

## Verifikation

- Jeder `wp_replace_in_post`-Aufruf lieferte `replacements_count: 1` als
  Bestätigung des H1-Treffers.
- Stichprobe am ersten (33299) und letzten (34986) Produkt der Liste per
  `wp_get_cpt_item` erneut gelesen: `description` beginnt in beiden
  Fällen sauber mit dem ersten Absatz, kein H1 mehr vorhanden.
- `_min_age` (Alterskennzeichnung 18) für 33299 über `wp_wc_get_product`
  geprüft (Top-Level-Feld **und** `meta_data`) – weiterhin `"18"`. Da für
  die gesamte Seite 3 ausschließlich `wp_replace_in_post` (Content) und
  `wp_yoast_update_post_seo` (Yoast-Metadaten) verwendet wurden – nie
  `wp_wc_update_product` oder `wp_wc_batch_update_products` – bestand für
  keines der 94 Produkte ein Risiko eines `_min_age`-Resets über den
  WooCommerce-REST-Write-Pfad.
- Neue Meta-Descriptions wurden jeweils per Zeichenzahl-Check (Python)
  gegen den 120–160-Zeichen-Zielkorridor geprüft, bevor sie geschrieben
  wurden.

## Nicht angefasst (bewusst)

- `short_description`-CTA "Jetzt sichern!" bei den 66 RQS-Produkten
  (kein Hanfjack-Bezug, daher laut Checkliste kein Verstoß).
- Yoast-SEO-Titel (`| Hanfjack` als Marken-Suffix im Title-Tag ist
  Standardpraxis, keine Kauf-CTA).
- THC-/CBD-Prozentangaben, Genetik-/Terpen-/Züchter-Fachsprache,
  Steckbrief-Tabellen, Anbau-Tipps-Struktur – bei Cannabis-Samen
  legitimer, legaler Content nach § 1 Nr. 8c KCanG.
- Variantenprodukte selbst (Packungsgrößen 3/5/10/25 Stück etc.) – der
  Haupttext liegt auf dem Elternprodukt, das reicht laut Vorgabe.
