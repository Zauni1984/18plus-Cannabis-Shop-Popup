# Hanfjack Zahlungsabgleich (N26 via Enable Banking)

Liest Gutschriften vom Geschäftskonto über die PSD2-Schnittstelle (Account Information) des lizenzierten Anbieters **Enable Banking** und setzt passende Vorkasse-Bestellungen automatisch auf „bezahlt“. Der Kunde erhält dabei die normale WooCommerce-/Germanized-Mail „Zahlung eingegangen“.

## Zuordnungsregeln

| Fall | Ergebnis |
|---|---|
| Bestellnummer im Verwendungszweck **und** Betrag exakt | automatisch bezahlt |
| Keine Bestellnummer, Betrag exakt, Nachname/Firma des Zahlers passt, genau ein Treffer | automatisch bezahlt (abschaltbar) |
| Bestellnummer gefunden, Betrag weicht ab (Teil-/Überzahlung) | zur Prüfung, Bestellung vorgeschlagen |
| Mehrere offene Bestellungen mit gleichem Betrag | zur Prüfung mit Vorschlägen |
| Kein Treffer | zur Prüfung, kann zugeordnet oder ignoriert werden |

Berücksichtigt werden offene Bestellungen der Zahlart `bacs` mit Status „In Wartestellung“/„Ausstehend“ der letzten 60 Tage (einstellbar). Jede Buchung wird nur einmal verarbeitet. Zur Prüfung stehende Eingänge kommen per Mail.

## Abruf

PSD2 erlaubt ohne Anwesenheit des Kontoinhabers nur ca. 4 Abrufe pro Tag. Standard: 07:30, 11:30, 15:30, 19:30 Uhr. Der Button „Jetzt abrufen“ im Backend sendet zusätzlich IP und Browser des Admins mit (PSU anwesend) und zählt nicht gegen dieses Limit.

Die Bankfreigabe gilt max. 180 Tage. 14 und 3 Tage vor Ablauf kommt eine Mail, im Backend erscheint ein Hinweis. Verlängern = „Neu verbinden“ + Bestätigung in der N26-App.

## Einrichtung

1. Konto bei https://enablebanking.com anlegen (kostenlos).
2. Control Panel → **API applications** → Environment **Production** → Schlüssel im Browser erzeugen lassen. Angaben: Name „Hanfjack Zahlungsabgleich“, Beschreibung, Datenschutz-E-Mail, Datenschutz- und AGB-URL von hanfjack.de, Redirect-URL: `https://hanfjack.de/wp-json/hj-bank/v1/callback`. Die `.pem`-Datei wird heruntergeladen, die Application-ID angezeigt.
3. In der App **Link accounts** (bzw. „Activate by linking accounts“) wählen, N26 auswählen und in der N26-App bestätigen. Damit läuft die App kostenlos im „Restricted Mode“ für die eigenen Konten.
4. WooCommerce → **Zahlungsabgleich**: Application-ID und Inhalt der `.pem` einfügen, speichern.
5. **Mit Bank verbinden** → N26-Login und Bestätigung in der App → zurück im Shop steht „verbunden“ mit der IBAN.
6. **Jetzt abrufen** mit „letzte 7 Tage“ testen.

Sicherer: `.pem` außerhalb des Webroots ablegen und in `wp-config.php` `define( 'HJB_PRIVATE_KEY_PATH', '/pfad/key.pem' );` setzen, dann bleibt das Feld leer.

## REST (Rechte `manage_woocommerce`)

```
GET  /wp-json/hj-bank/v1/status
GET  /wp-json/hj-bank/v1/aspsps?country=DE&q=n26
POST /wp-json/hj-bank/v1/sync            {"days":7}
GET  /wp-json/hj-bank/v1/transactions?status=review,unmatched
POST /wp-json/hj-bank/v1/assign          {"row_id":12,"order_id":49157}
POST /wp-json/hj-bank/v1/test-match      {"amount":"36.89","remittance":"49157","debtor":"Max Rix"}
POST /wp-json/hj-bank/v1/settings        {"sync_times":"08:00,12:00,16:00,20:00"}
```

## Tests

```
php tests/run-tests.php
```
