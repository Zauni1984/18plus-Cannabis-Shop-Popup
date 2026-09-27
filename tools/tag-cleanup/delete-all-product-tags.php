<?php
/**
 * Loescht alle Produkt-Tags (Taxonomie product_tag).
 *
 * Aufruf:
 *   wp eval-file delete-all-product-tags.php
 * oder, wenn die Datei im WordPress-Verzeichnis liegt:
 *   php delete-all-product-tags.php
 *
 * Das Loeschen laesst sich nicht rueckgaengig machen.
 */

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
    exit("wp-load.php nicht gefunden.\n");
}

$terme = get_terms([
    'taxonomy'   => 'product_tag',
    'hide_empty' => false,
    'fields'     => 'ids',
]);

if (is_wp_error($terme)) {
    exit('Fehler: ' . $terme->get_error_message() . "\n");
}

$ok = 0;
$fehler = 0;
foreach ($terme as $id) {
    $r = wp_delete_term((int) $id, 'product_tag');
    if (is_wp_error($r) || !$r) {
        $fehler++;
    } else {
        $ok++;
    }
}

wp_update_term_count_now([], 'product_tag');
delete_transient('wc_term_counts');
if (function_exists('wc_delete_product_transients')) {
    wc_delete_product_transients();
}

echo "$ok Tags gelöscht, $fehler Fehler.\n";
