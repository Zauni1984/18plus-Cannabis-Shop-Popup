#!/usr/bin/env bash
# Auf dem VPS ausfuehren. Zieht den Dump der Shared-Datenbank und baut ihn auf
# die VPS-Struktur um (MariaDB 11.8 -> Percona/MySQL 8.4).
#
# Laeuft mit --single-transaction, sperrt also nichts: hanfjack.de bleibt
# waehrend des Dumps online. Aendert an der Quelle nichts.
set -euo pipefail

CNF=${CNF:-/root/.hj-quelle.cnf}
DB=${DB:-u842511985_nccnk}
ZIEL=${ZIEL:-/root/umzug}
STEMPEL=$(date +%Y%m%d-%H%M)
ROH="$ZIEL/hanfjack-roh-$STEMPEL.sql"
FERTIG="$ZIEL/hanfjack-vps-$STEMPEL.sql"

KLIENT=$(command -v mariadb || command -v mysql || true)
DUMP=$(command -v mariadb-dump || command -v mysqldump || true)
[ -n "$KLIENT" ] && [ -n "$DUMP" ] || { echo "Kein mariadb/mysql-Klient gefunden."; exit 1; }
[ -f "$CNF" ] || { echo "Fehlt: $CNF - siehe README, Abschnitt 9."; exit 1; }
[ "$(stat -c %a "$CNF")" = 600 ] || { echo "$CNF muss chmod 600 sein."; exit 1; }
mkdir -p "$ZIEL"

frage() { "$KLIENT" --defaults-extra-file="$CNF" -N -B -e "$1"; }

# ---------------------------------------------------------------- 1. Dump ----
# Routinen, Events und Trigger nur anfordern, wenn es welche gibt: auf dem
# Shared-Paket fehlen dafuer gern die Rechte, und WordPress hat ueblich keine.
OPT=(--single-transaction --quick --hex-blob --default-character-set=utf8mb4)
ANZ_ROUTINE=$(frage "SELECT COUNT(*) FROM information_schema.routines WHERE routine_schema='$DB';")
ANZ_EVENT=$(frage "SELECT COUNT(*) FROM information_schema.events WHERE event_schema='$DB';")
ANZ_TRIGGER=$(frage "SELECT COUNT(*) FROM information_schema.triggers WHERE trigger_schema='$DB';")
[ "$ANZ_ROUTINE" -gt 0 ] && OPT+=(--routines)
[ "$ANZ_EVENT" -gt 0 ] && OPT+=(--events)
[ "$ANZ_TRIGGER" -gt 0 ] || OPT+=(--skip-triggers)

echo "Routinen $ANZ_ROUTINE / Events $ANZ_EVENT / Trigger $ANZ_TRIGGER"
echo "Dump laeuft: $ROH"
"$DUMP" --defaults-extra-file="$CNF" "${OPT[@]}" "$DB" > "$ROH"

# Vollstaendigkeit pruefen - ein abgebrochener Dump sieht sonst brauchbar aus.
if ! tail -c 400 "$ROH" | grep -q 'Dump completed'; then
  echo "ABBRUCH: Der Dump hat keine Schlusszeile. Netzverbindung pruefen, neu ziehen."
  exit 1
fi
echo "Dump vollstaendig: $(du -h "$ROH" | cut -f1), $(grep -c '^CREATE TABLE' "$ROH") Tabellen"

# ---------------------------------------------------- 2. Umbau auf 8.4 ------
# Was MariaDB 11.8 schreibt und MySQL 8.4 nicht kennt:
#  - Sandbox-Zeile am Dateianfang
#  - uca1400-Kollationen (MariaDB-Standard seit 11.4) und nopad-Varianten
#  - Aria-Engine samt PAGE_CHECKSUM/TRANSACTIONAL/PAGE_COMPRESSED
#  - DEFINER auf Benutzer, die es auf dem VPS nicht gibt
{
  echo "-- Umgebaut fuer Percona/MySQL 8.4 am $(date -Is)"
  echo "SET SESSION foreign_key_checks=0;"
  echo "SET SESSION unique_checks=0;"
  sed -E \
    -e '/^\/\*!999999.*sandbox/d' \
    -e 's/utf8mb4_uca1400_ai_ci/utf8mb4_unicode_ci/g' \
    -e 's/utf8mb4_uca1400_as_ci/utf8mb4_unicode_ci/g' \
    -e 's/utf8mb4_uca1400_as_cs/utf8mb4_0900_as_cs/g' \
    -e 's/utf8mb3_uca1400_ai_ci/utf8mb3_unicode_ci/g' \
    -e 's/utf8mb4_general_nopad_ci/utf8mb4_general_ci/g' \
    -e 's/utf8mb4_nopad_bin/utf8mb4_bin/g' \
    -e 's/ENGINE=Aria/ENGINE=InnoDB/g' \
    -e 's/ PAGE_CHECKSUM=[0-9]+//g' \
    -e 's/ TRANSACTIONAL=[0-9]+//g' \
    -e 's/ `?PAGE_COMPRESSED`?=[^ ,;]+//g' \
    -e 's/DEFINER=`[^`]+`@`[^`]+` ?//g' \
    "$ROH"
} > "$FERTIG"

# ------------------------------------------------------- 3. Nachkontrolle ---
echo
echo "== Reste, die MySQL 8.4 nicht kennt =="
if grep -n -m 10 -E 'uca1400|_nopad_|ENGINE=Aria|PAGE_COMPRESSED|PAGE_CHECKSUM|TRANSACTIONAL=|DEFINER=|enable the sandbox' "$FERTIG"; then
  echo "ABBRUCH: oben stehende Stellen von Hand klaeren."
  exit 1
fi
echo "keine"

echo
echo "== Kollationen im fertigen Dump =="
grep -oE 'COLLATE[= ]+[a-z0-9_]+' "$FERTIG" | sort | uniq -c | sort -rn
grep -oE 'DEFAULT CHARSET=[a-z0-9]+' "$FERTIG" | sort | uniq -c | sort -rn

echo
echo "Fertig: $FERTIG ($(du -h "$FERTIG" | cut -f1))"
echo "Roh behalten bis der Import steht: $ROH"
