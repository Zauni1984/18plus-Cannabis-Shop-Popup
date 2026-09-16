<?php
/**
 * Loescht Produkt-Tags (Taxonomie product_tag) eines WooCommerce-Shops.
 *
 * Voreingestellt ist der Trockenlauf: Das Skript zeigt, was es tun wuerde, und
 * aendert nichts. Geloescht wird erst mit --confirm.
 *
 * Vor jedem Loeschlauf wird eine Sicherung als JSON und CSV geschrieben
 * (ID, Name, Slug, Beschreibung, Produktzahl). Damit lassen sich die Tags
 * wieder anlegen - die Zuordnung zu den Produkten ist danach allerdings weg.
 *
 * AUFRUF
 *
 *   WP-CLI (empfohlen):
 *     wp eval-file delete-product-tags.php
 *     wp eval-file delete-product-tags.php -- --confirm
 *     wp eval-file delete-product-tags.php -- --only-empty --confirm
 *     wp eval-file delete-product-tags.php -- --exclude=beilngries,hanfjack --confirm
 *
 *   Ohne WP-CLI, direkt per PHP auf dem Server:
 *     php delete-product-tags.php --confirm
 *   (Die Datei muss dafuer im WordPress-Verzeichnis oder darunter liegen,
 *    damit wp-load.php gefunden wird.)
 *
 *   Ueber den Browser: nur mit Token, das oben in TOKEN gesetzt werden muss,
 *     https://example.com/delete-product-tags.php?token=DEINTOKEN&confirm=1
 *   Nach getaner Arbeit die Datei wieder vom Server loeschen.
 *
 * OPTIONEN
 *
 *   --confirm       loescht wirklich (ohne diese Option nur Trockenlauf)
 *   --only-empty    nur Tags ohne Produkte
 *   --min-count=N   nur Tags mit hoechstens N Produkten
 *   --exclude=a,b   diese Slugs nie loeschen (zusaetzlich zu SCHUTZ_SLUGS)
 *   --batch=N       wie viele Tags je Durchgang, Voreinstellung 200
 *   --no-backup     Sicherung ueberspringen (nicht empfohlen)
 */

// --------------------------------------------------------------------------
// Sicherungen. Vor dem ersten Lauf pruefen!
// --------------------------------------------------------------------------

/** Slugs, die nie geloescht werden - auch nicht mit --confirm. */
const SCHUTZ_SLUGS = ['beilngries', 'hanfjack'];

/**
 * Hosts, auf denen das Skript grundsaetzlich nicht laeuft.
 * hanfjack.de steht hier, weil dort der Tag "Beilngries" den eigenen
 * Lagerbestand markiert und die Tag-Struktur gepflegt ist.
 */
const GESPERRTE_HOSTS = ['hanfjack.de', 'www.hanfjack.de'];

/** Fuer den Browser-Aufruf: leer lassen heisst, der Browser-Aufruf ist gesperrt. */
const TOKEN = '';

// --------------------------------------------------------------------------

const TAXONOMIE = 'product_tag';

if (!defined('ABSPATH')) {
    $pfad = __DIR__;
    for ($i = 0; $i < 8; $i++) {
        if (file_exists($pfad . '/wp-load.php')) {
            require_once $pfad . '/wp-load.php';
            break;
        }
        $pfad = dirname($pfad);
    }
}
if (!defined('ABSPATH')) {
    fwrite(STDERR, "wp-load.php nicht gefunden. Skript ins WordPress-Verzeichnis legen\n"
                 . "oder mit WP-CLI aufrufen: wp eval-file delete-product-tags.php\n");
    exit(1);
}

$cli = (php_sapi_name() === 'cli');
if (!$cli) {
    header('Content-Type: text/plain; charset=utf-8');
    if (TOKEN === '' || !isset($_GET['token']) || !hash_equals(TOKEN, (string) $_GET['token'])) {
        http_response_code(403);
        exit("Browser-Aufruf gesperrt. TOKEN im Skript setzen und ?token=... anhaengen.\n");
    }
    if (!current_user_can('manage_woocommerce') && !current_user_can('manage_options')) {
        http_response_code(403);
        exit("Fehlende Berechtigung.\n");
    }
}

/** Optionen aus der Kommandozeile oder der URL lesen. */
function opt(string $name, $standard = false)
{
    global $argv, $cli;
    if ($cli && !empty($argv)) {
        foreach ($argv as $a) {
            if ($a === "--$name") {
                return true;
            }
            if (strpos($a, "--$name=") === 0) {
                return substr($a, strlen($name) + 3);
            }
        }
        return $standard;
    }
    return $_GET[$name] ?? $standard;
}

function sage(string $text): void
{
    echo $text . "\n";
    if (function_exists('WP_CLI') || class_exists('WP_CLI')) {
        // WP-CLI puffert nicht, echo genuegt
    }
    @flush();
}

$confirm    = (bool) opt('confirm');
$nurLeere   = (bool) opt('only-empty');
$ohneBackup = (bool) opt('no-backup');
$maxCount   = opt('min-count', false);
$maxCount   = ($maxCount === false) ? null : (int) $maxCount;
$batch      = (int) (opt('batch', 200) ?: 200);
$exclude    = array_filter(array_map('trim', explode(',', (string) opt('exclude', ''))));
$schutz     = array_map('strtolower', array_merge(SCHUTZ_SLUGS, $exclude));

$host = parse_url(home_url(), PHP_URL_HOST);
sage('Shop:      ' . home_url());
sage('Taxonomie: ' . TAXONOMIE);

if (in_array($host, GESPERRTE_HOSTS, true)) {
    sage('');
    sage("ABBRUCH: $host steht in GESPERRTE_HOSTS.");
    sage('Wenn das Absicht ist, den Host oben aus der Liste nehmen.');
    exit(2);
}

if (!taxonomy_exists(TAXONOMIE)) {
    sage('ABBRUCH: Taxonomie ' . TAXONOMIE . ' existiert hier nicht. Ist WooCommerce aktiv?');
    exit(2);
}

$terme = get_terms([
    'taxonomy'   => TAXONOMIE,
    'hide_empty' => false,
]);
if (is_wp_error($terme)) {
    sage('ABBRUCH: ' . $terme->get_error_message());
    exit(2);
}

$zuLoeschen = [];
$behalten   = [];
foreach ($terme as $t) {
    if (in_array(strtolower($t->slug), $schutz, true)
        || in_array(strtolower($t->name), $schutz, true)) {
        $behalten[] = $t;
        continue;
    }
    if ($nurLeere && $t->count > 0) {
        continue;
    }
    if ($maxCount !== null && $t->count > $maxCount) {
        continue;
    }
    $zuLoeschen[] = $t;
}

sage('Tags gesamt:     ' . count($terme));
sage('Zu löschen:      ' . count($zuLoeschen));
sage('Geschützt:       ' . count($behalten)
     . (count($behalten) ? ' (' . implode(', ', wp_list_pluck($behalten, 'slug')) . ')' : ''));

if (!count($zuLoeschen)) {
    sage('Nichts zu tun.');
    exit(0);
}

// ---- Sicherung ------------------------------------------------------------
if (!$ohneBackup) {
    $verz  = wp_upload_dir();
    $stamp = date('Ymd-His');
    $basis = trailingslashit($verz['basedir']) . "product-tags-backup-$stamp";

    $daten = array_map(function ($t) {
        return [
            'term_id'     => (int) $t->term_id,
            'name'        => $t->name,
            'slug'        => $t->slug,
            'description' => $t->description,
            'count'       => (int) $t->count,
        ];
    }, $zuLoeschen);

    file_put_contents($basis . '.json',
        wp_json_encode($daten, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    $fh = fopen($basis . '.csv', 'w');
    fwrite($fh, "\xEF\xBB\xBF");                       // BOM, damit Excel die Umlaute erkennt
    fputcsv($fh, ['term_id', 'name', 'slug', 'description', 'count'], ';');
    foreach ($daten as $z) {
        fputcsv($fh, $z, ';');
    }
    fclose($fh);

    sage('Sicherung:       ' . $basis . '.json');
    sage('                 ' . $basis . '.csv');
}

// ---- Trockenlauf ----------------------------------------------------------
if (!$confirm) {
    sage('');
    sage('TROCKENLAUF - es wurde nichts gelöscht.');
    sage('Beispiele:');
    foreach (array_slice($zuLoeschen, 0, 15) as $t) {
        sage(sprintf('   %5d Produkte  %s  (%s)', $t->count, $t->name, $t->slug));
    }
    if (count($zuLoeschen) > 15) {
        sage('   ... und ' . (count($zuLoeschen) - 15) . ' weitere');
    }
    sage('');
    sage('Zum Löschen mit --confirm erneut aufrufen.');
    exit(0);
}

// ---- Löschen --------------------------------------------------------------
sage('');
sage('Löschen läuft ...');
$ok = 0;
$fehler = 0;
$n = 0;
foreach ($zuLoeschen as $t) {
    $r = wp_delete_term((int) $t->term_id, TAXONOMIE);
    if (is_wp_error($r) || $r === false || $r === 0) {
        $fehler++;
        $grund = is_wp_error($r) ? $r->get_error_message() : 'unbekannt';
        sage("   FEHLER bei {$t->name} (#{$t->term_id}): $grund");
    } else {
        $ok++;
    }
    if (++$n % $batch === 0) {
        sage("   ... $n von " . count($zuLoeschen));
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        usleep(200000);
    }
}

// Zaehler der Taxonomie neu berechnen, sonst zeigt das Backend alte Werte
wp_update_term_count_now([], TAXONOMIE);
if (function_exists('wc_delete_product_transients')) {
    wc_delete_product_transients();
}
delete_transient('wc_term_counts');

sage('');
sage("Fertig: $ok gelöscht, $fehler Fehler.");
if (!$ohneBackup) {
    sage('Die Sicherung liegt im Upload-Verzeichnis.');
}
