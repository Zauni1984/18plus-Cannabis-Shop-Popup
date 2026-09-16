<?php
/**
 * Loescht alle Produkt-Tags (Taxonomie product_tag).
 *
 * Fassung fuer WPCode. Als Snippet einfuegen, Typ "PHP Snippet",
 * Einfuegemethode "Auto Insert", Speicherort "Run Everywhere", aktivieren
 * und eine beliebige Seite im Backend aufrufen.
 *
 * Das Skript laeuft auf init mit Prioritaet 999 - WooCommerce registriert die
 * Taxonomie product_tag auf init mit Prioritaet 5. Frueher gibt es die
 * Taxonomie noch nicht, und get_terms liefert nur einen Fehler.
 *
 * Es laeuft genau einmal. Danach traegt sich ein Merker in die Optionen ein,
 * damit nicht bei jedem Seitenaufruf erneut ueber alle Terme gelaufen wird.
 * Das Ergebnis erscheint als Hinweis im Backend.
 *
 * Wenn du es ein zweites Mal laufen lassen willst, den Merker loeschen:
 *   delete_option('hj_tags_geloescht');
 *
 * Das Loeschen laesst sich nicht rueckgaengig machen.
 */

$hj_tags_loeschen = function () {

    if (get_option('hj_tags_geloescht')) {
        return;                                  // schon erledigt
    }
    if (!taxonomy_exists('product_tag')) {
        return;                                  // WooCommerce nicht aktiv
    }

    $ok = 0;
    $fehler = 0;

    // In Bloecken arbeiten, damit auch mehrere tausend Tags kein Zeitlimit reissen
    do {
        $ids = get_terms([
            'taxonomy'   => 'product_tag',
            'hide_empty' => false,
            'fields'     => 'ids',
            'number'     => 200,
        ]);
        if (is_wp_error($ids) || empty($ids)) {
            break;
        }
        foreach ($ids as $id) {
            $r = wp_delete_term((int) $id, 'product_tag');
            if (is_wp_error($r) || !$r) {
                $fehler++;
            } else {
                $ok++;
            }
        }
    } while (true);

    wp_update_term_count_now([], 'product_tag');
    delete_transient('wc_term_counts');
    if (function_exists('wc_delete_product_transients')) {
        wc_delete_product_transients();
    }

    $ergebnis = "$ok Tags gelöscht, $fehler Fehler.";
    update_option('hj_tags_geloescht', $ergebnis, false);
    error_log('[Tag-Loeschung] ' . $ergebnis);

};

// WPCode fuehrt Snippets je nach Speicherort frueher oder spaeter aus.
// Ist init schon durch, sofort laufen - sonst waere der Hook wirkungslos.
if (did_action('init')) {
    $hj_tags_loeschen();
} else {
    add_action('init', $hj_tags_loeschen, 999);
}

// Ergebnis einmal im Backend anzeigen
add_action('admin_notices', function () {
    $e = get_option('hj_tags_geloescht');
    if ($e) {
        echo '<div class="notice notice-success"><p><strong>Produkt-Tags:</strong> '
             . esc_html($e) . '</p></div>';
    }
});
