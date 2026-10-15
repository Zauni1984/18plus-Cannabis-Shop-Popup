# Verfahrensdokumentation Ladenkasse Hanfjack Ingolstadt

Stand: Oktober 2026 · Betreiber: siehe Impressum hanfjack.de · Betriebsstätte: Harderstraße 1, 85049 Ingolstadt · Rechtsträger: SZM Trade & IT Solutions e.K.

## 1. Systemübersicht

| Komponente | Produkt | Aufgabe |
|---|---|---|
| Warenwirtschaft / Shop | WooCommerce auf hanfjack.de | Artikelstamm, Preise, Bestand, Bestellungen (Online und Laden) |
| Kassensoftware | WCPOS Pro (WordPress-Plugin, Bedienung im Browser) | Erfassung der Ladenverkäufe als WooCommerce-Bestellungen, Bondruck, Bestandsabzug |
| Zahlungsterminal | SumUp Solo (Cloud-Reader-API) | Kartenzahlung; erhält nur Betrag und Bestellnummer, keine Artikeldaten |
| Bondrucker | Epson TM-m30III (Server Direct Print) | Druck des Kassenbelegs mit TSE-Daten und QR-Code |
| TSE | fiskaly Cloud-TSE (SIGN DE v2, BSI-zertifiziert) | Signatur aller Kassenvorgänge nach § 146a AO / KassenSichV |
| TSE-Anbindung | Plugin „Hanfjack TSE (fiskaly) für WCPOS“ | Start/Abschluss der Transaktionen, Belegdaten, Kassenbuch, Kassenabschluss, DSFinV-K |
| DSFinV-K / Archiv | fiskaly DSFINVK DE | Tägliche Kassenabschlüsse, Export für Kassen-Nachschau und Betriebsprüfung |

Kassen-ID (Seriennummer des Aufzeichnungssystems): `HANFJACK-INGOLSTADT-KASSE-1`. TSE-Seriennummer: siehe WooCommerce → TSE (fiskaly).

## 2. Ablauf eines Verkaufs

1. Artikel werden im POS gescannt (EAN oder Artikelnummer) oder gesucht. WCPOS legt die Bestellung im Shop an.
2. Mit dem Anlegen startet das TSE-Plugin die Transaktion bei fiskaly (Status ACTIVE, Zeitstempel Start).
3. Zahlung bar oder per SumUp Solo. Bei Kartenzahlung bestätigt SumUp die Zahlung per Webhook an den Shop.
4. Mit der Bezahlung schließt das Plugin die TSE-Transaktion ab (Bruttobeträge je MwSt-Satz, Zahlart Bar/Unbar). Signatur, Signaturzähler, Zeitstempel und QR-Code werden an der Bestellung gespeichert.
5. Der Bon wird automatisch auf dem Epson gedruckt und enthält: Händlerdaten, Belegnummer, Positionen mit Menge und Preis, MwSt-Ausweis, Zahlart, Kassen-ID, TSE-Seriennummer, Transaktionsnummer, Start/Ende, Signaturzähler, Signatur, QR-Code (§ 6 KassenSichV).
6. Der Lagerbestand wird durch WooCommerce reduziert.

Stornos: Rückerstattungen werden über die WooCommerce-Bestellung erfasst, als eigener TSE-Beleg mit negativen Beträgen signiert und erhöhen den Bestand. Abgebrochene, nicht bezahlte Vorgänge werden als Vorgangsabbruch signiert.

## 3. Kassenbuch und Tagesabschluss

- Bareinlagen und -entnahmen (Wechselgeld, Privateinlage, Privatentnahme, Geldtransit zur Bank) werden auf der Seite WooCommerce → TSE (fiskaly) gebucht und ebenfalls signiert.
- Täglich um 23:58 Uhr erstellt das Plugin automatisch den Kassenabschluss (Z-Bericht) für jeden Geschäftstag mit Vorgängen und übermittelt ihn an fiskaly DSFinV-K. Der Abschluss kann auch manuell ausgelöst werden.
- Der Z-Bericht ist jederzeit im Backend abrufbar und druckbar.

## 4. Ausfall der TSE

Ist fiskaly nicht erreichbar, läuft der Verkauf weiter. Der Bon trägt den Hinweis „TSE ausgefallen, Beleg ohne Signatur“, der Fehler wird mit Zeitpunkt protokolliert (Bestellnotiz, Tabelle `wp_hj_tse_log`, WooCommerce-Log „hanfjack-tse“). Betroffene Belege werden im Kassenabschluss mit Fehlermeldung statt Signatur gemeldet. Nach Ende der Störung werden offene Vorgänge nachsigniert (Backend oder REST `sign/{id}`). Dauert die Störung länger als einen Geschäftstag, wird das Finanzamt formlos informiert.

## 5. Aufbewahrung und Export

- Alle Bestellungen, Signaturdaten und Kassenbuchungen liegen in der WooCommerce-Datenbank (tägliches Hosting-Backup).
- fiskaly archiviert die TSE-Protokolle und die Kassenabschlüsse für 10 Jahre.
- Für eine Kassen-Nachschau: DSFinV-K-Export für den angefragten Zeitraum im Backend anstoßen (ZIP mit index.xml und CSV-Dateien nach DSFinV-K 2.x) und TSE-Export (TAR) erzeugen. Beide Dateien werden unter `wp-content/uploads/hj-tse-exports` abgelegt und auf einen Datenträger kopiert.

## 6. Berechtigungen und Änderungen

- Kassierer melden sich mit eigenem WordPress-Konto an (Rolle Kassierer/Shop-Manager); der Kassierer wird in jedem Vorgang gespeichert.
- Änderungen an Plugin, Vorlage oder TSE-Konfiguration werden im Git-Repository versioniert (Branch, Commit, Datum).
- Preise und Artikel werden ausschließlich im WooCommerce-Backend gepflegt; die Kasse hat keinen eigenen Artikelstamm.

## 7. Meldepflicht

Kasse und TSE werden über „Mein ELSTER“ (Mitteilung über elektronische Aufzeichnungssysteme, § 146a Abs. 4 AO) innerhalb eines Monats nach Inbetriebnahme gemeldet: Softwarekasse WCPOS Pro, Kassen-ID, TSE-Art Cloud-TSE, TSE-Seriennummer und BSI-Zertifizierungs-ID laut fiskaly.
