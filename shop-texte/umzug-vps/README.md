# hanfjack.de auf einen Hostinger-VPS umziehen

Stand 27.09.2026. Ausgangslage: Shared-Hosting bei Hostinger, LiteSpeed,
Hostinger-CDN davor. WooCommerce mit 4.467 Produkten (1.631 davon variabel),
58 aktive Plugins, vier Bestands-Syncs, zwei Feed-Plugins, Zahlungsanbieter mit
Rueckmeldungen von aussen. Die Datenbank stand zweimal am Quota, der Account
wurde wegen Dauerlast gedrosselt. Genau deshalb der Umzug.

## 1. Groesse des VPS

Fuer diesen Shop ist **4 vCPU und 16 GB RAM** die vernuenftige Basis
(Hostinger KVM 4). Zwei Kerne und 8 GB (KVM 2) reichen im Alltag, gehen aber
bei Feed-Erzeugung, Bestands-Sync und Preload gleichzeitig in die Knie – genau
die Kombination, die uns hier umgehauen hat. NVMe ist Pflicht, 200 GB Platte
sind reichlich.

## 2. Aufsatz waehlen

Hostinger bietet Vorlagen. Sinnvoll sind zwei Wege:

- **CyberPanel (OpenLiteSpeed)** – bleibt beim LiteSpeed, den der Shop kennt,
  inklusive LSCache. Vertraute Bedienung, wenig Umgewoehnung.
- **CloudPanel (NGINX + PHP-FPM + MariaDB)** – schlanker und sparsamer, aber
  ohne LiteSpeed-Eigenheiten; WP Rocket uebernimmt das Caching ohnehin.

Beides ist selbst zu betreuen. Wer das nicht will, nimmt Hostingers
verwalteten VPS-Dienst oder bleibt bei einem grossen Shared-Paket.

## 3. Vor dem Umzug aufraeumen

Erst putzen, dann umziehen – das spart Stunden und verhindert, dass der Muell
mitkommt:

```sql
TRUNCATE TABLE wp_woocommerce_sessions;
TRUNCATE TABLE wp_actionscheduler_logs;
DELETE FROM wp_actionscheduler_actions WHERE status IN ('complete','failed','canceled') LIMIT 500;  -- mehrfach
DELETE FROM wp_options WHERE option_name LIKE '\_transient\_%' LIMIT 500;                            -- mehrfach
DELETE a,b,c FROM wp_posts a LEFT JOIN wp_term_relationships b ON a.ID=b.object_id
  LEFT JOIN wp_postmeta c ON a.ID=c.post_id WHERE a.post_type='revision';
```

Danach `OPTIMIZE TABLE` – aber nur mit freiem Platz und nicht im Tagesgeschaeft.

## 4. Umziehen

Reihenfolge, die das Risiko klein haelt:

1. **VPS aufsetzen**, PHP 8.3 oder 8.4, MariaDB, Let's Encrypt, WP-CLI.
2. **Erstkopie ohne Zeitdruck**: Dateien per `rsync`, Datenbank per Dump.
   ```bash
   rsync -avz --delete alt:~/domains/hanfjack.de/public_html/ /var/www/hanfjack.de/
   ssh alt "wp db export - --single-transaction" | mysql hanfjack
   ```
3. **Auf dem VPS testen**, ohne DNS zu aendern: lokale hosts-Datei auf die neue
   IP zeigen lassen, dann Startseite, Kategorie, Produkt, Warenkorb, Kasse und
   eine Testbestellung durchspielen.
4. **Kurzes Fenster nachts**: Shop in den Wartungsmodus, Delta-`rsync`, frischer
   Datenbank-Dump, einspielen. Dauert bei dieser Groesse 20 bis 40 Minuten.
   Bestellungen duerfen in der Zeit nicht verloren gehen – deshalb Wartungsmodus
   statt Parallelbetrieb.
5. **DNS umstellen**. Einen Tag vorher die TTL auf 300 Sekunden senken, danach
   wieder hochsetzen. Den alten Server 48 Stunden laufen lassen.

Hostinger hat auch eine eigene Migrationsfunktion (Shared → VPS). Fuer einen
Shop mit laufenden Bestellungen ist der Weg oben trotzdem der sicherere, weil
man den Zeitpunkt des Umschaltens selbst bestimmt.

## 5. Was auf dem VPS anders eingestellt werden muss

Diese Punkte gehen beim Umzug gern unter und rauben danach wochenlang Nerven:

- **Echter Cron statt WP-Cron.** `define('DISABLE_WP_CRON', true);` und
  `*/5 * * * * cd /var/www/hanfjack.de && wp cron event run --due-now`.
  Der Action Scheduler und die vier Bestands-Syncs haengen daran.
- **PHP**: `memory_limit 512M`, `max_execution_time 300`,
  `upload_max_filesize 64M`, OPcache an.
- **MariaDB**: `innodb_buffer_pool_size` auf 50 bis 60 Prozent des RAM. Das ist
  der groesste einzelne Gewinn gegenueber dem Shared-Paket.
- **Redis als Objekt-Cache.** Bei 4.500 Produkten und vielen Metafeldern der
  zweitgroesste Gewinn.
- **Backups.** Ein VPS sichert sich nicht von selbst. Taegliches Abbild plus
  taeglicher Datenbank-Dump ausserhalb des Servers.
- **Monitoring**: Erreichbarkeit und Ressourcen, damit so ein Tag wie heute
  nicht wieder unbemerkt ueber Stunden laeuft.

## 6. Nach dem Umzug pruefen

- Testbestellung mit echter Zahlung, danach Stornierung.
- **Rueckmeldungen von aussen**: Catalystpay, Crypto Pay, M2E (Amazon, eBay),
  Adcell. Die rufen den Shop von aussen auf – nach dem Wechsel der IP pruefen,
  ob sie ankommen, und IP-Freigaben beim Anbieter nachziehen.
- Feed-Erzeugung von AdTribes, Bestands-Syncs einmal manuell anstossen.
- Yoast-Sitemap abrufen, in der Search Console die neue Erreichbarkeit pruefen.
- E-Mail-Versand (WP Mail SMTP laeuft ueber einen Anbieter, sollte unberuehrt
  bleiben – trotzdem eine Bestellbestaetigung testen).
- Erst danach den WP-Rocket-Preload wieder einschalten, und wenn, dann mit
  kleinerer Stapelgroesse.

## 7. Zeitpunkt

Nicht waehrend der laufenden Drosselung anfangen. Erst wenn der Account wieder
normal antwortet, in Ruhe die Erstkopie ziehen. Das eigentliche Umschalten
gehoert in eine bestellarme Nacht.
