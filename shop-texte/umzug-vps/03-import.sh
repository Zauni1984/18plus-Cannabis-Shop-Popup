#!/usr/bin/env bash
# Auf dem VPS ausfuehren. Spielt den umgebauten Dump in die VPS-Datenbank ein
# und vergleicht danach mit der Quelle. Die Quelle wird nur gelesen.
#
#   ZIELDB=hanfjack ./03-import.sh /root/umzug/hanfjack-vps-20260927-2130.sql
set -euo pipefail

ZCNF=${ZCNF:-/root/.hj-ziel.cnf}     # Zugang zur VPS-Datenbank (127.0.0.1)
QCNF=${QCNF:-/root/.hj-quelle.cnf}   # Zugang zur Shared-Datenbank, nur lesend
ZIELDB=${ZIELDB:?ZIELDB setzen, z. B. ZIELDB=hanfjack}
QDB=${QDB:-u842511985_nccnk}
DATEI=${1:?Dump-Datei angeben}

[ -f "$DATEI" ] || { echo "Nicht gefunden: $DATEI"; exit 1; }
for f in "$ZCNF"; do
  [ -f "$f" ] || { echo "Fehlt: $f - siehe README, Abschnitt 9."; exit 1; }
  [ "$(stat -c %a "$f")" = 600 ] || { echo "$f muss chmod 600 sein."; exit 1; }
done

ZIEL_KLIENT=$(command -v mysql || command -v mariadb)
ziel() { "$ZIEL_KLIENT" --defaults-extra-file="$ZCNF" -N -B -e "$1"; }

echo "== Zielserver =="
ziel "SELECT VERSION(), @@character_set_server, @@collation_server, @@innodb_buffer_pool_size;"
VORHER=$(ziel "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$ZIELDB';")
echo "Tabellen in $ZIELDB vor dem Import: $VORHER"
if [ "$VORHER" != "0" ]; then
  echo "WARNUNG: $ZIELDB ist nicht leer. Bei einem Wiederholungslauf erst leeren:"
  echo "  mysql --defaults-extra-file=$ZCNF -e 'DROP DATABASE \`$ZIELDB\`; CREATE DATABASE \`$ZIELDB\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;'"
  read -r -p "Trotzdem weiter? [j/N] " a; [ "$a" = j ] || exit 1
fi

echo
echo "== Import laeuft =="
START=$(date +%s)
"$ZIEL_KLIENT" --defaults-extra-file="$ZCNF" "$ZIELDB" < "$DATEI"
echo "Import fertig in $(( ($(date +%s) - START) / 60 )) Minuten."

echo
echo "== Tabellen und Groesse im Ziel =="
ziel "SELECT COUNT(*) AS tabellen, ROUND(SUM(data_length+index_length)/1048576) AS mb
      FROM information_schema.tables WHERE table_schema='$ZIELDB';"
echo "== Kollationen im Ziel =="
ziel "SELECT table_collation, COUNT(*) FROM information_schema.tables
      WHERE table_schema='$ZIELDB' GROUP BY table_collation;"

# --------------------------------------------------- Abgleich mit der Quelle --
if [ -f "$QCNF" ]; then
  Q_KLIENT=$(command -v mariadb || command -v mysql)
  quelle() { "$Q_KLIENT" --defaults-extra-file="$QCNF" -N -B -e "$1"; }
  echo
  echo "== Tabellen, die nur auf einer Seite stehen =="
  quelle "SELECT table_name FROM information_schema.tables WHERE table_schema='$QDB' ORDER BY 1;" > /tmp/hj-q.txt
  ziel   "SELECT table_name FROM information_schema.tables WHERE table_schema='$ZIELDB' ORDER BY 1;" > /tmp/hj-z.txt
  diff /tmp/hj-q.txt /tmp/hj-z.txt && echo "identisch"
  echo
  echo "== Zeilen im Vergleich (Quelle / Ziel) =="
  for t in wp_posts wp_options wp_terms wp_term_relationships wp_users wp_wc_orders wp_wc_product_meta_lookup; do
    grep -qx "$t" /tmp/hj-q.txt || continue
    a=$(quelle "SELECT COUNT(*) FROM \`$QDB\`.\`$t\`;")
    b=$(ziel   "SELECT COUNT(*) FROM \`$ZIELDB\`.\`$t\`;")
    [ "$a" = "$b" ] && s=ok || s="ABWEICHUNG"
    printf '%-32s %10s %10s  %s\n' "$t" "$a" "$b" "$s"
  done
  rm -f /tmp/hj-q.txt /tmp/hj-z.txt
else
  echo "Kein $QCNF - Abgleich mit der Quelle uebersprungen."
fi
