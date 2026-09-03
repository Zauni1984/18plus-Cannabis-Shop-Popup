# Kategorie-Texte Rewrite (alle 91 aktiven Produktkategorien)

## Technischer Befund (wichtig für alle weiteren Kategorien!)

Das WordPress-Standardfeld `description` einer Taxonomie-Term (geschrieben via
`wp_update_term`) läuft durch WordPress' restriktiven KSES-Filter für Terms und
**verliert dabei alle Block-Tags** (`<p>`, `<h2>`, `<ul>`, `<li>` usw.) – nur
einfache Inline-Tags wie `<strong>`, `<em>`, `<a>` würden überleben, aber selbst
die lohnen sich nicht, da ohne Absätze der Text zu einem einzigen Fließtext-Block
verschmilzt. Bestätigt durch Vergleich Schreiben → Rücklesen (wp_get_term).

Das Feld `below_category_content` (Term-Meta, von einem Theme/Plugin unterhalb
der Produktliste gerendert) hat **keine** solche Beschränkung – volles HTML
(H2, UL/LI, P, STRONG) bleibt erhalten. Bestätigt via wp_get_term_meta.

**Strategie pro Kategorie:**
- `description` (WC-Standardfeld, oberhalb der Produkte): 1 kurzer Absatz
  Klartext (250–500 Zeichen), keine Tags nötig/möglich.
- `below_category_content` (Term-Meta, unterhalb der Produkte): der
  eigentliche strukturierte Inhalt mit `<h2>`, `<ul><li>`, `<p>`, `<strong>`.
- Yoast SEO: Term-Meta-Keys `_yoast_wpseo_title`, `_yoast_wpseo_metadesc`,
  `_yoast_wpseo_focuskw` (gleiches Präfix-Schema wie bei Posts, aber auf
  Term-Ebene). Verifikation über `wp_yoast_get_head` mit der Live-URL, da
  `wp_yoast_get_post_seo` nur für Posts funktioniert.

## Umfang

96 Kategorien insgesamt, 91 aktiv (count > 0), 5 übersprungen (0 Produkte):
CBD Vapes, Produktarchiv, Schneidbretter, Süßigkeiten und Snacks, Uncategorized.

Siehe `aktive_kategorien.json` für die vollständige Liste (id, name, slug,
parent, count).

## Fortschritt

- [x] Filter (608) + Aktivkohlefilter (4550) + 6 Größen-Subkats — 7/7
- [x] Headshop-Baum (inkl. Headshop selbst) — 25/25
- [x] Growshop-Baum — 31/31
- [x] Samen-Baum — 6/6
- [ ] Lebensmittel-Baum — 0/9
- [ ] CBD-Baum — 0/4
- [ ] Standalone (Hanfprodukte, Pflegeprodukte, Bundles, Mystery Boxen, Merch, Angebote, Vermehrungsmaterial) — 0/8
