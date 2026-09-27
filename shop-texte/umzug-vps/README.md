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

---

# Durchfuehrung auf srv2014751.hstgr.cloud

Stand 27.09.2026. Der VPS steht und ist geprueft: Ubuntu 24.04, KVM 4
(4 Kerne, 15 GB RAM, 186 GB frei), CloudPanel 6.0.8, NGINX 1.30.4,
PHP 7.1 bis 8.5, **Percona Server 8.4.11**, Redis antwortet, ufw aktiv,
rsync und WP-CLI vorhanden.

Quelle: Shared-Paket `u842511985`, Datenbank `u842511985_nccnk`
(1.458 MB von 9.216 MB, Host `srv1808.hstgr.io`, Port 3306), PHP 8.5.4.
Der Shop bleibt die ganze Zeit online – alle Schritte bis zum DNS-Wechsel
lesen die Quelle nur.

## 8. Warum der Dump umgebaut werden muss

Die Quelle ist MariaDB 11.8, das Ziel Percona/MySQL 8.4. MariaDB schreibt
seit 11.4 Dinge in den Dump, die MySQL nicht kennt:

| in MariaDB 11.8 | auf dem VPS |
| --- | --- |
| `/*!999999\- enable the sandbox mode */` | Zeile entfernen |
| `utf8mb4_uca1400_ai_ci` | `utf8mb4_unicode_ci` |
| `utf8mb4_uca1400_as_cs` | `utf8mb4_0900_as_cs` |
| `utf8mb3_uca1400_ai_ci` | `utf8mb3_unicode_ci` |
| `*_nopad_*` | Variante ohne `nopad` |
| `ENGINE=Aria` samt `PAGE_CHECKSUM`, `TRANSACTIONAL`, `PAGE_COMPRESSED` | `ENGINE=InnoDB`, Optionen streichen |
| `DEFINER=` auf Shared-Benutzer | streichen, den Benutzer gibt es hier nicht |

Zeichensatz und Tabellenpraefix bleiben unangetastet: `utf8mb4` behaelt seine
Schluessellaengen, `utf8mb3` wird **nicht** auf `utf8mb4` hochgezogen – das
sprengt bei alten Plugin-Tabellen die Indexlaenge. Wer es will, macht es
spaeter einzeln mit `ALTER TABLE`, nicht im Dump.

Das machen die drei Skripte in diesem Ordner, in dieser Reihenfolge:

```bash
./01-quelle-pruefen.sh                 # Kollationen, Engines, Groessen protokollieren
./02-dump-ziehen.sh                    # Dump ziehen und umbauen, mit Nachkontrolle
ZIELDB=hanfjack ./03-import.sh /root/umzug/hanfjack-vps-*.sql
```

`02` bricht ab, wenn der Dump keine Schlusszeile hat (abgebrochene Verbindung)
oder nach dem Umbau noch MariaDB-Eigenheiten drin stehen. `03` vergleicht
danach Tabellenliste und Zeilenzahlen mit der Quelle.

## 9. Zugangsdaten

Beide Zugangsdateien werden **auf dem Server** angelegt, nie im Chat oder im
Repository. Das Passwort der Quelle ist das vorhandene Datenbank-Passwort des
Shared-Pakets: `DB_PASSWORD` in
`/home/u842511985/domains/hanfjack.de/public_html/wp-config.php`, Benutzer
`u842511985_nccnk`. Es im hPanel neu zu setzen wuerde hanfjack.de sofort
lahmlegen, bis `wp-config.php` nachgezogen ist – also abschreiben, nicht
zuruecksetzen.

```bash
umask 077
install -m 600 /dev/null /root/.hj-quelle.cnf
cat > /root/.hj-quelle.cnf <<'CNF'
[client]
host=srv1808.hstgr.io
port=3306
user=u842511985_nccnk
password=DAS_PASSWORT_AUS_WP-CONFIG
CNF
mysql --defaults-extra-file=/root/.hj-quelle.cnf -e 'SELECT 1;'   # Probe
```

Der Fernzugriff auf die Shared-Datenbank ist fuer beide Adressen des VPS
freigegeben (`2a02:4780:7e:a26d::1` und `179.198.213.117`). **Nach dem Umzug
beide Regeln wieder entfernen** – hPanel, Datenbanken, Fernzugriff.

## 10. Ziel anlegen (CloudPanel)

PHP 8.4 fuer den vhost: breitere Plugin-Abdeckung als 8.5, und der Sprung von
8.5.4 zurueck ist unkritisch. Passwoerter erzeugt der Server, sie wandern
nirgends hin:

```bash
DBPW=$(openssl rand -base64 24); SITEPW=$(openssl rand -base64 24)
clpctl site:add:php --domainName=hanfjack.de --phpVersion=8.4 \
  --vhostTemplate='WordPress' --siteUser=hanfjack --siteUserPassword="$SITEPW"
clpctl db:add --domainName=hanfjack.de --databaseName=hanfjack \
  --databaseUserName=hanfjack --databaseUserPassword="$DBPW"
printf '[client]\nhost=127.0.0.1\nuser=hanfjack\npassword=%s\n' "$DBPW" > /root/.hj-ziel.cnf
chmod 600 /root/.hj-ziel.cnf
```

Die Datenbank danach auf `utf8mb4 / utf8mb4_unicode_ci` stellen, damit neue
Tabellen zur alten Struktur passen:

```sql
ALTER DATABASE `hanfjack` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## 11. Percona 8.4 auf diese Maschine einstellen

Die Vorgabe von CloudPanel (`innodb_buffer_pool_size = 512M`) ist fuer 15 GB
RAM und eine 1,5-GB-Datenbank viel zu klein. In
`/etc/mysql/mysql.conf.d/` eine eigene Datei, damit Panel-Updates sie nicht
ueberschreiben:

```ini
[mysqld]
innodb_buffer_pool_size      = 4G
innodb_redo_log_capacity     = 1G
innodb_flush_method          = O_DIRECT
innodb_flush_neighbors       = 0
innodb_io_capacity           = 2000
innodb_io_capacity_max       = 4000
max_connections              = 150
tmp_table_size               = 64M
max_heap_table_size          = 64M
```

`max_connections = 512` ist bei 4 Kernen keine Reserve, sondern eine Falle:
so viele gleichzeitige Abfragen bringen die Maschine eher um, als dass sie
Last abfedern. 150 reicht fuer PHP-FPM mit sinnvoller `pm.max_children`.
Danach `systemctl restart mysql` und mit
`SELECT @@innodb_buffer_pool_size;` nachsehen.
