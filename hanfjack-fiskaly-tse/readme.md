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
POST /wp-json/hj-tse/v1/setup           {"client_serial":"HANFJACK-BEILNGRIES-KASSE-1"}
POST /wp-json/hj-tse/v1/sign/{order_id} (Nachsignieren nach Ausfall)
GET  /wp-json/hj-tse/v1/order/{order_id}
GET  /wp-json/hj-tse/v1/log?limit=100
POST /wp-json/hj-tse/v1/export          {"start_date":1760000000,"end_date":1760600000}
GET  /wp-json/hj-tse/v1/export/{export_id}?download=yes
```

## Noch offen (Phase 2)

- Täglicher Kassenabschluss und DSFinV-K-Export über die fiskaly DSFINVK-API.
- Kassenbuch-Funktionen (Einlage/Entnahme) als TSE-Vorgänge PAY_IN/PAY_OUT.
- ELSTER-Meldung der Kasse (manuell über „Mein ELSTER“ mit TSE-Seriennummer und Kassen-ID).
