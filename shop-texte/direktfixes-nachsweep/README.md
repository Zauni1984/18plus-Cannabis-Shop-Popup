# Direktfixes: 2 offene Folge-Punkte aus dem Growshop-/Headshop-Sweep

Diese beiden Funde waren als separate Task-Vorschläge angelegt worden und wurden
nach Bestätigung durch den Nutzer direkt in der Hauptsession erledigt (kein
Subagent), da der Umfang klein und der Fix eindeutig war.

## 1. Gizeh Papers: ChatGPT-Web-UI-HTML-Reste (Produkte 520, 521)

Fund aus dem Papers-Sweep (Task #22): die `description` von zwei Gizeh-Produkten
enthielt keinen normalen Produkttext, sondern einen kompletten HTML-Export der
ChatGPT-Web-Oberfläche (`react-scroll-to-bottom--css-*`, `data-testid="conversation-turn-*"`,
`data-message-author-role="assistant"`, `class="markdown prose ..."` usw.) —
11 verschachtelte `<div>`-Wrapper vor und nach dem eigentlichen Text. Nur bei
diesen beiden Produkten (528, 524, 525 waren bereits sauber).

**Fix:** `description` beider Produkte per `wp_wc_update_product` komplett neu
gesetzt — identischer Textinhalt wie zuvor (nur die Wrapper-Divs entfernt,
Bullet-Listen/Absätze sauber mit `<p>`/`<ul>` strukturiert). Bei 521 zusätzlich
"Hanfjack empfiehlt diese Papers für Fortgeschrittene und Profi Dreher" leicht
umformuliert (keine CTA, nur Formatbereinigung), da der Satz in der
Wrapper-Version ohnehin vorhanden war.

**Verifikation:** `_min_age` nach dem Schreibvorgang bei beiden Produkten auf
`""` zurückgesetzt vorgefunden (bekanntes Verhalten von `wp_wc_update_product`)
und über `wp_wc_batch_update_products(update=[{"id": <ID>, "min_age": "18"}])`
(Top-Level-Feld) wiederhergestellt — bei beiden Produkten per erneutem
`wp_wc_get_product` bestätigt (`_min_age: "18"` in `meta_data` und `min_age: "18"`
top-level).

## 2. Kaputte Mathe/LaTeX-Syntax bei EC-Werten (Produkte 28916, 19470, 14323)

Fund aus dem Growshop-Sweep ("Sonstige Kleinkategorien"-Agent): in drei
Kokos-Substrat-Produkten (Plagron Cocos Brix 6x7L, UGro Coco Small Basic 11L,
Plagron Cocos Slab 12L) wurde der EC-Wert nicht als Klartext, sondern als
kaputtes LaTeX-Snippet gespeichert, z. B.
`<span class="math-inline" data-math="&lt; 0,2">$&lt; 0,2$</span>` — ohne
MathJax/KaTeX-Rendering zeigt das dem Kunden wörtlich `$< 0,2$` inklusive
Dollarzeichen an, statt "< 0,2".

**Fix:** alle 5 betroffenen Stellen (2× in 28916, 2× in 19470, 1× in 14323,
je einmal im Fließtext bzw. in der Spezifikationstabelle) per `wp_replace_in_post`
chirurgisch durch reinen Text ersetzt (`&lt; 0,2`, `&lt; 0,6` bzw. `≤ 0,2` für
das `\leq`-Snippet in 14323). Kein sonstiger Text verändert.

**Verifikation:** alle 3 Produkte nach dem Fix erneut per `wp_get_cpt_item`
gelesen — kein `math-inline`/`$`-Artefakt mehr vorhanden, EC-Werte werden als
normaler Text angezeigt. Da ausschließlich `wp_replace_in_post` verwendet
wurde, bestand kein `_min_age`-Risiko.
