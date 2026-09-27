#!/usr/bin/env bash
# Auf dem VPS ausfuehren. Liest die Shared-Datenbank von hanfjack.de aus und
# protokolliert, was fuer den Umbau des Dumps wichtig ist. Aendert nichts.
#
# Vorher: /root/.hj-quelle.cnf anlegen (siehe README, Abschnitt 9).
set -euo pipefail

CNF=${CNF:-/root/.hj-quelle.cnf}
DB=${DB:-u842511985_nccnk}
ZIEL=${ZIEL:-/root/umzug}

KLIENT=$(command -v mariadb || command -v mysql || true)
[ -n "$KLIENT" ] || { echo "Kein mariadb/mysql-Klient gefunden."; exit 1; }
[ -f "$CNF" ] || { echo "Fehlt: $CNF - siehe README, Abschnitt 9."; exit 1; }
[ "$(stat -c %a "$CNF")" = 600 ] || { echo "$CNF muss chmod 600 sein."; exit 1; }
mkdir -p "$ZIEL"

frage() { "$KLIENT" --defaults-extra-file="$CNF" -N -B -e "$1"; }

echo "== Server der Quelle =="
frage "SELECT VERSION(), @@character_set_server, @@collation_server;"

echo
echo "== Kollationen der Tabellen =="
frage "SELECT table_collation, COUNT(*) AS tabellen
       FROM information_schema.tables WHERE table_schema='$DB'
       GROUP BY table_collation ORDER BY tabellen DESC;" | tee "$ZIEL/kollationen-tabellen.txt"

echo
echo "== Kollationen der Spalten =="
frage "SELECT DISTINCT collation_name FROM information_schema.columns
       WHERE table_schema='$DB' AND collation_name IS NOT NULL
       ORDER BY collation_name;" | tee "$ZIEL/kollationen-spalten.txt"

echo
echo "== Engines und Groesse =="
frage "SELECT engine, COUNT(*) AS tabellen,
              ROUND(SUM(data_length+index_length)/1048576) AS mb
       FROM information_schema.tables WHERE table_schema='$DB'
       GROUP BY engine;"

echo
echo "== Die 15 groessten Tabellen =="
frage "SELECT table_name, table_rows,
              ROUND((data_length+index_length)/1048576) AS mb
       FROM information_schema.tables WHERE table_schema='$DB'
       ORDER BY data_length+index_length DESC LIMIT 15;"

echo
echo "== Routinen, Events, Trigger (wegen DEFINER) =="
frage "SELECT 'routinen', COUNT(*) FROM information_schema.routines WHERE routine_schema='$DB'
       UNION ALL SELECT 'events', COUNT(*) FROM information_schema.events WHERE event_schema='$DB'
       UNION ALL SELECT 'trigger', COUNT(*) FROM information_schema.triggers WHERE trigger_schema='$DB'
       UNION ALL SELECT 'tabellen', COUNT(*) FROM information_schema.tables WHERE table_schema='$DB';"

echo
echo "Protokolle liegen in $ZIEL."
