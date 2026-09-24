# Google Ads: „Manipulierte Website" – Pruefung vom 24.09.2026

Richtlinie: *Missbrauch des Werbenetzwerks: Manipulierte Websites*
(support.google.com/adspolicy/answer/15938376). Gemeint ist Code auf der
Website, der einem Dritten nuetzt, ohne dass der Betreiber davon weiss:
eingeschleuste Skripte, Kreditkarten-Skimmer, Malware, Pop-ups,
Weiterleitungen – oder ein CMS mit bekannten, bereits missbrauchten Luecken.

## Was geprueft wurde – und was dabei herauskam

| Pruefung | Ergebnis |
|---|---|
| Cloaking (Chrome, Googlebot, AdsBot, AdsBot-Mobile) | identische Antworten, 12 von 12 Landingpages HTTP 200 |
| Fremde Domains in Skripten, iframes, Links | nur bekannte: Trustindex, Google Tag Manager, Adcell, Facebook, Trustpilot, jsDelivr, Google Fonts |
| Inline-Skripte auf Start- und Produktseite | kein `eval`, kein `atob`, keine Base64-Bloecke, keine Weiterleitungen |
| Kassenseite auf Skimmer | keine Listener auf Zahlungsfelder, keine fremden Endpunkte, keine verschleierten Bloecke |
| Benutzer mit Rechten | genau ein Administrator (`hanfjack`, seit 11/2024), ein Shop-Manager (`balou`), sonst nur Kunden |
| Plugins | 58 aktiv, alle bekannt; kein unbekanntes Plugin, keine fremde Erweiterung |
| WPCode-Snippets | unveraendert; einziges neues ist „Kassen CSS" vom 24.09., 19:20 – das ist unser Mobil-CSS |
| Medienbibliothek (letzte 100) | keine .php/.js/.html-Uploads |
| Systemstand | WordPress 7.1.2, PHP 8.5.4, MariaDB 11.8.9 – aktuell |

**Kein Hinweis auf eine Kompromittierung.**

## Die wahrscheinliche Ursache

Am 21. und 22.09. lief vor der ganzen Seite die **JavaScript-Pruefung des
Hostinger-CDN** („Checking your browser before accessing…"). Gemessen wurde
damals: **HTTP 403 auf alles** – Startseite, Kategorien, Produktseiten,
robots.txt, Sitemaps, wp-json – und zwar auch fuer Googlebot. Ausgeliefert
wurde statt der Seite eine Seite mit **verschleiertem JavaScript**, das einen
Proof-of-Work rechnet und den Besucher per XHR weiterleitet. Zeitweise kam
zusaetzlich die LiteSpeed-Seite „Bot Verification" mit reCAPTCHA.

Genau so sieht aus Sicht der automatischen Pruefung von Google eine
manipulierte Zielseite aus: fremder, verschleierter Code plus Weiterleitung
statt der beworbenen Landingpage.

Heute ist davon nichts mehr zu sehen: AdsBot und AdsBot-Mobile bekommen auf
allen geprueften Landingpages sauberes HTML mit Status 200.

## Naechste Schritte

1. **Ablehnungsgrund im Google-Ads-Konto lesen.** Kampagnen → Anzeigen →
   Filter „Richtliniendetails: Manipulierte Website". Google nennt dort oft die
   beanstandete Domain. Steht dort eine fremde Domain, die hier nicht
   aufgetaucht ist, muss weiter gesucht werden.
2. **Search Console → Sicherheitsprobleme** oeffnen. Das ist Googles zweite,
   unabhaengige Meinung und dauert eine Minute.
3. **CDN-Challenge nicht dauerhaft laufen lassen**, mindestens Googlebot und
   AdsBot ausnehmen.
4. Danach **Einspruch einlegen** und erneute Pruefung anstossen.
5. Was ich nicht pruefen kann: die PHP-Dateien auf der Platte. Ein
   Dateiscanner (Hostinger-Malware-Scan oder Wordfence) schliesst diese Luecke.
