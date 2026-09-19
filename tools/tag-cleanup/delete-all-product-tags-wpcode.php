<?php
/**
 * Loescht alle Produkt-Tags (Taxonomie product_tag).
 *
 * WPCode: Typ "PHP Snippet", Auto Insert, Run Everywhere.
 *
 * Gegen den 504: Je Seitenaufruf wird nur so lange geloescht, wie das
 * Zeitbudget reicht - danach bricht der Lauf ab und macht beim naechsten
 * Aufruf weiter. Der Hinweis im Backend zeigt, wie viele noch uebrig sind.
 * Einfach die Seite neu laden, bis "fertig" dasteht.
 *
 * Beschleunigt wird das Loeschen durch wp_defer_term_counting: sonst zaehlt
 * WordPress nach jedem einzelnen Term die ganze Taxonomie neu.
 *
 * Fuer einen erneuten Lauf:  delete_option('hj_tags_geloescht');
 *
 * Das Loeschen laesst sich nicht rueckgaengig machen.
 */

// Sekunden je Seitenaufruf. Kleiner setzen, falls weiterhin 504 kommt.
const HJ_ZEITBUDGET = 10;
// Wie viele Terme je Abfrage geholt werden.
const HJ_BLOCK = 50;

$hj_tags_loeschen = function () {

    if (get_option('hj_tags_geloescht') === 'fertig') {
        return;
    }
    if (!taxonomy_exists('product_tag')) {
        return;                                  // WooCommerce nicht aktiv
    }

    $start  = microtime(true);
    $ok     = (int) get_option('hj_tags_ok', 0);
    $fehler = (int) get_option('hj_tags_fehler', 0);

    // Ohne das zaehlt WordPress nach jedem Term die ganze Taxonomie neu
    wp_defer_term_counting(true);

    $fertig = false;
    while (microtime(true) - $start < HJ_ZEITBUDGET) {

        $ids = get_terms([
            'taxonomy'   => 'product_tag',
            'hide_empty' => false,
            'fields'     => 'ids',
            'number'     => HJ_BLOCK,
        ]);

        if (is_wp_error($ids) || empty($ids)) {
            $fertig = true;
            break;
        }

        foreach ($ids as $id) {
            $r = wp_delete_term((int) $id, 'product_tag');
            if (is_wp_error($r) || !$r) {
                $fehler++;
            } else {
                $ok++;
            }
            if (microtime(true) - $start >= HJ_ZEITBUDGET) {
                break;
            }
        }
    }

    wp_defer_term_counting(false);

    update_option('hj_tags_ok', $ok, false);
    update_option('hj_tags_fehler', $fehler, false);

    if ($fertig) {
        wp_update_term_count_now([], 'product_tag');
        delete_transient('wc_term_counts');
        if (function_exists('wc_delete_product_transients')) {
            wc_delete_product_transients();
        }
        update_option('hj_tags_geloescht', 'fertig', false);
        error_log("[Tag-Loeschung] fertig: $ok gelöscht, $fehler Fehler.");
    } else {
        update_option('hj_tags_geloescht', 'laeuft', false);
    }
};

// WooCommerce registriert product_tag erst auf init mit Prioritaet 5.
// Ist init schon durch, sofort laufen - sonst waere der Hook wirkungslos.
if (did_action('init')) {
    $hj_tags_loeschen();
} else {
    add_action('init', $hj_tags_loeschen, 999);
}

add_action('admin_notices', function () {
    $stand = get_option('hj_tags_geloescht');
    if (!$stand) {
        return;
    }
    $ok     = (int) get_option('hj_tags_ok', 0);
    $fehler = (int) get_option('hj_tags_fehler', 0);

    if ($stand === 'fertig') {
        echo '<div class="notice notice-success"><p><strong>Produkt-Tags:</strong> fertig – '
             . (int) $ok . ' gelöscht, ' . (int) $fehler . ' Fehler. '
             . 'Snippet kann deaktiviert werden.</p></div>';
        return;
    }

    $rest = wp_count_terms(['taxonomy' => 'product_tag', 'hide_empty' => false]);
    $rest = is_wp_error($rest) ? '?' : (int) $rest;
    echo '<div class="notice notice-warning"><p><strong>Produkt-Tags:</strong> '
         . (int) $ok . ' gelöscht, noch ' . esc_html((string) $rest)
         . ' übrig. Die Seite lädt sich gleich selbst neu – Tab einfach offen lassen.</p></div>';
    // Laedt weiter, bis nichts mehr uebrig ist. Bei 9000 Tags sonst Dutzende
    // Klicks von Hand.
    echo '<script>setTimeout(function(){location.reload();}, 1500);</script>';
});
