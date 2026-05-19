=== Cannabis Age Verifier – 18+ Popup für WooCommerce ===
Contributors: blocksocial
Tags: woocommerce, age verification, cannabis, dsgvo, gdpr, 18+, 21+
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Professionelles, DSGVO-konformes 18+ Altersverifikations-Popup für WooCommerce-Cannabis-Shops. Blockiert Minderjährige vollständig und leitet zur offiziellen Aufklärungsseite des Bundesgesundheitsministeriums weiter.

== Description ==

**Cannabis Age Verifier** ist ein Premium-Plugin der BlockSocial UG (haftungsbeschränkt) zur rechtskonformen Altersverifikation in WooCommerce-Shops mit Cannabis-Produkten.

**Features**

* DSGVO-konform – nur technisch notwendige Cookies, kein Tracking, keine Phone-Home-Lizenzprüfung
* Serverseitige Altersprüfung mit HMAC-signiertem HttpOnly-Cookie
* 18+ / 21+ / individuell – jedoch niemals unter dem gesetzlichen Mindestalter von 18 Jahren (CanG)
* Geburtsdatum-Eingabe (rechtssicher) oder einfache Ja/Nein-Bestätigung
* Bei negativer Prüfung: automatische Weiterleitung zur Aufklärungsseite des Bundesgesundheitsministeriums
* Animiertes Glassmorphism-Design mit konfigurierbaren Farben
* Vollständig auf Vanilla-JS (~3 kB gzipped) – top Google PageSpeed
* XSS-Härtung: Escape überall, Nonce-Schutz, Rate-Limiting, CSP-freundlich
* Respektiert `prefers-reduced-motion` und unterstützt Tastatur-Navigation
* Vollständig auf Deutsch lokalisiert

**Lizenz**

Premium-Plugin, lizenziert auf BlockSocial UG (haftungsbeschränkt), Kratzmühlstraße 14, 92339 Beilngries. Kostenfrei lauffähig auf:

* blocksocial.eu
* hanfjack.de
* moinkiffers.de
* growgarage.de

== Installation ==

1. Plugin-ZIP unter Plugins → Installieren hochladen
2. Aktivieren
3. Im Admin unter „Age Verifier" konfigurieren (Mindestalter, Design, Rechtshinweis)

== Frequently Asked Questions ==

= Werden personenbezogene Daten verarbeitet? =
Nein. Das Geburtsdatum wird nur clientseitig eingegeben und nie gespeichert – lediglich das Ergebnis (volljährig ja/nein) wird als HMAC-signiertes Token im HttpOnly-Cookie hinterlegt.

= Was passiert bei Minderjährigen? =
Diese werden automatisch zur Aufklärungsseite des Bundesgesundheitsministeriums weitergeleitet und für eine konfigurierbare Zeit gesperrt.

== Changelog ==

= 1.0.0 =
* Erstveröffentlichung
