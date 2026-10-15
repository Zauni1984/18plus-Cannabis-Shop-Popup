# Hanfjack TSE (fiskaly) für WCPOS

WordPress-Plugin, das Kassenbelege aus **WCPOS Pro** über die **fiskaly Cloud-TSE (SIGN DE v2)** signiert und damit die KassenSichV (§ 146a AO) erfüllt.

## Was das Plugin macht

| Ereignis in WooCommerce | TSE |
|---|---|
| POS-Bestellung angelegt (`woocommerce_new_order`) | Transaktion gestartet (`ACTIVE`, Revision 1) |
| Bestellung bezahlt / abgeschlossen | Transaktion abgeschlossen (`FINISHED`, `receipt_type` RECEIPT) mit Bruttobeträgen je MwSt-Satz und Zahlart |
| Offene POS-Bestellung storniert | `FINISHED` mit `receipt_type` ABORT (Vorgangsabbruch) |
| Rückerstattung | Eigene Transaktion mit negativen Beträgen (Storno-Beleg), Signatur am Refund-Objekt |
| Bon-Druck (WCPOS) | Filter `woocommerce_pos_receipt_data`: QR-Code und Klartextfelder im Abschnitt `fiscal` |
| TSE-Ausfall | Verkauf läuft weiter, Beleg trägt den Hinweis „TSE ausgefallen“, Fehler wird protokolliert |

Alle Vorgänge landen zusätzlich in der Tabelle `wp_hj_tse_log` (Grundlage für den DSFinV-K-Export).

## Einrichtung

1. Plugin-ZIP hochladen und aktivieren (WooCommerce muss aktiv sein).
2. **WooCommerce → TSE (fiskaly)**: API-Key und Secret aus dem fiskaly HUB eintragen, Kassen-ID prüfen, speichern.
3. **TSS anlegen und Kasse registrieren** klicken. Das legt die TSS an, setzt Admin-PIN/PUK, initialisiert sie und registriert den Client.
4. In WCPOS die Thermo-Bonvorlage um den Block aus `templates/wcpos-thermal-tse-block.xml` ergänzen (vor dem Bestell-Barcode).
5. Testverkauf im POS, Bon prüfen: Kassen-ID, TSE-Seriennummer, Transaktionsnummer, Start/Ende, Signaturzähler, QR-Code.

Mit einem `test_…`-Key läuft alles gegen die fiskaly-Sandbox, der Bon trägt dann den Vermerk „TEST“. Für den Echtbetrieb den LIVE-Key eintragen und die Einrichtung erneut ausführen.

## REST-Schnittstelle (Rechte: `manage_woocommerce`)

```
GET  /wp-json/hj-tse/v1/status?remote=yes
POST /wp-json/hj-tse/v1/settings        {"api_key":"…","api_secret":"…"}
POST /wp-json/hj-tse/v1/setup           {"client_serial":"HANFJACK-INGOLSTADT-KASSE-1"}
POST /wp-json/hj-tse/v1/sign/{order_id} (Nachsignieren nach Ausfall)
GET  /wp-json/hj-tse/v1/order/{order_id}
GET  /wp-json/hj-tse/v1/log?limit=100
POST /wp-json/hj-tse/v1/export          {"start_date":1760000000,"end_date":1760600000}
GET  /wp-json/hj-tse/v1/export/{export_id}?download=yes
```

## Phase 2: Kassenabschluss, Kassenbuch, DSFinV-K

- **Kassenbuch:** Einlagen und Entnahmen (Anfangsbestand, Privateinlage, Privatentnahme, Geldtransit, Auszahlung, Einzahlung) werden als TSE-Vorgang `TRANSFER` signiert und im Abschluss als `AVTransfer` geführt.
- **Kassenabschluss:** WP-Cron erstellt täglich um 23:58 Uhr (Shop-Zeitzone) für jeden Geschäftstag mit Vorgängen einen Abschluss und übermittelt ihn an die fiskaly DSFINVK-API (`PUT /cash_point_closings/{id}`). Beinhaltet Belege, Stornos (negative Beträge), Vorgangsabbrüche, Kassenbuchungen und unsignierte Belege (`security.error_message`). Manuell: Admin-Seite oder `POST /wp-json/hj-tse/v1/closings {"business_date":"YYYY-MM-DD"}`.
- **Z-Bericht:** Jeder Abschluss hat eine druckbare Ansicht (Umsatz je MwSt-Satz und Zahlart, Einlagen/Entnahmen, Vorgangsliste).
- **DSFinV-K-Export:** Admin-Seite oder `POST /wp-json/hj-tse/v1/dsfinvk-export {"start_date":"YYYY-MM-DD","end_date":"YYYY-MM-DD"}`, Status/Download über `GET /wp-json/hj-tse/v1/dsfinvk-export/{id}?download=yes` (ZIP mit index.xml, transactions.csv, tse.csv …).
- **Kassenregistrierung:** Beim ersten Abschluss wird die Kasse bei DSFinV-K als MASTER mit der TSS verknüpft (`PUT /cash_registers/{client_id}`).

## Noch manuell

- ELSTER-Meldung der Kasse über „Mein ELSTER“ (Formular „Mitteilung über elektronische Aufzeichnungssysteme (§ 146a Abs. 4 AO)“) mit TSE-Seriennummer, Kassen-ID und Anschaffungsdatum, innerhalb eines Monats nach Inbetriebnahme.
- Verfahrensdokumentation siehe `VERFAHRENSDOKUMENTATION.md`.
