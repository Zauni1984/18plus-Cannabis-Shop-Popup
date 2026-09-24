# Kasse in der Mobilansicht: Bestelluebersicht wird abgeschnitten

## Befund (im Browser nachgestellt, iPhone-Viewport 390 px)

Die Tabelle `woocommerce-checkout-review-order-table` ist **408 px breit in
einem 390-px-Fenster** und beginnt bei x = 20 – sie ragt also rund 38 px ueber
den Rand hinaus und wird abgeschnitten. Genau deshalb steht auf dem Handy
„Versandkost…" statt „Versandkostenpauschale", und die Betraege von
Zwischensumme und Gesamtsumme liegen ausserhalb des sichtbaren Bereichs.

Ursache ist nicht Germanized allein, aber seine Zusaetze verschaerfen es: zur
zweispaltigen Tabelle kommen Lieferzeit, „(inkl. MwSt.)" und das lange Etikett
der Versandart. Die Tabelle bleibt zweispaltig und waechst ueber das Fenster.

## Loesung

`kasse-mobil.css` loest die Tabelle unter 768 px aus dem Tabellenraster und
setzt jede Zeile als Flexzeile: Bezeichnung links, Betrag rechts, Umbruch wenn
es eng wird. Die Versandzeile bekommt die volle Breite, damit
„Versandkostenpauschale: 5,95 €" nicht mitten im Wort bricht.

Gemessen danach: Tabelle **351 px** statt 408 px, Hoehe 352 px statt 510 px,
kein Ueberlauf mehr.

## Einbauen

Design → Customizer → Zusaetzliches CSS, Inhalt von `kasse-mobil.css`
einfuegen. Alternativ als WPCode-Snippet (Typ CSS, „Site Wide Header").

## Nachpruefen

`kasse_test.py` legt ueber die Schnittstelle einen Warenkorb an, oeffnet die
Kasse im Chromium mit Handy-Viewport, misst die Breiten und macht je einen
Screenshot vor und nach dem Einspielen des CSS:

    python3 kasse_test.py kasse-mobil.css
