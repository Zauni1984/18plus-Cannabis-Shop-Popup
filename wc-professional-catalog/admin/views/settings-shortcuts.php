<?php
/**
 * Shortcuts / shortcodes reference tab.
 *
 * @package WCProfessionalCatalog
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$base = home_url( '/wc-catalog/' );
?>
<div class="wcpc-shortcuts">
	<h2><?php esc_html_e( 'Shortcodes', 'wc-professional-catalog' ); ?></h2>
	<p><?php esc_html_e( 'Diese Shortcodes können auf jeder Seite oder in jedem Beitrag eingefügt werden.', 'wc-professional-catalog' ); ?></p>

	<table class="widefat striped">
		<thead><tr><th><?php esc_html_e( 'Shortcode', 'wc-professional-catalog' ); ?></th><th><?php esc_html_e( 'Wirkung', 'wc-professional-catalog' ); ?></th></tr></thead>
		<tbody>
			<tr><td><code>[wcpc_catalog]</code></td><td><?php esc_html_e( 'Kompletter Katalog im Standard-Layout.', 'wc-professional-catalog' ); ?></td></tr>
			<tr><td><code>[wcpc_catalog layout="flipbook"]</code></td><td><?php esc_html_e( 'Online-Flipbook (blätterbar).', 'wc-professional-catalog' ); ?></td></tr>
			<tr><td><code>[wcpc_catalog category="spirituosen,gin"]</code></td><td><?php esc_html_e( 'Nur ausgewählte Kategorien.', 'wc-professional-catalog' ); ?></td></tr>
			<tr><td><code>[wcpc_catalog tag="bestseller"]</code></td><td><?php esc_html_e( 'Nach Schlagwort filtern.', 'wc-professional-catalog' ); ?></td></tr>
			<tr><td><code>[wcpc_catalog brand="monkey-47"]</code></td><td><?php esc_html_e( 'Nach Marke filtern.', 'wc-professional-catalog' ); ?></td></tr>
			<tr><td><code>[wcpc_catalog ids="12,15,22"]</code></td><td><?php esc_html_e( 'Nur ausgewählte Produkte.', 'wc-professional-catalog' ); ?></td></tr>
			<tr><td><code>[wcpc_catalog columns="4" limit="24"]</code></td><td><?php esc_html_e( 'Layout-Optionen.', 'wc-professional-catalog' ); ?></td></tr>
			<tr><td><code>[wcpc_catalog_buttons]</code></td><td><?php esc_html_e( 'Buttons für PDF / Druck / Online-Katalog.', 'wc-professional-catalog' ); ?></td></tr>
		</tbody>
	</table>

	<h2 style="margin-top:24px;"><?php esc_html_e( 'Direkte Links', 'wc-professional-catalog' ); ?></h2>
	<table class="widefat striped">
		<tbody>
			<tr><td><code><?php echo esc_html( $base . 'flipbook/' ); ?></code></td><td><?php esc_html_e( 'Online-Katalog (Flipbook).', 'wc-professional-catalog' ); ?></td></tr>
			<tr><td><code><?php echo esc_html( $base . 'pdf/' ); ?></code></td><td><?php esc_html_e( 'PDF herunterladen / anzeigen.', 'wc-professional-catalog' ); ?></td></tr>
			<tr><td><code><?php echo esc_html( $base . 'print/' ); ?></code></td><td><?php esc_html_e( 'Druckansicht (mit Auto-Druck).', 'wc-professional-catalog' ); ?></td></tr>
			<tr><td><code><?php echo esc_html( $base . 'pdf/brand/monkey-47/' ); ?></code></td><td><?php esc_html_e( 'PDF gefiltert nach Marke.', 'wc-professional-catalog' ); ?></td></tr>
			<tr><td><code><?php echo esc_html( $base . 'pdf/category/spirituosen/' ); ?></code></td><td><?php esc_html_e( 'PDF gefiltert nach Kategorie.', 'wc-professional-catalog' ); ?></td></tr>
			<tr><td><code><?php echo esc_html( $base . 'pdf/tag/bestseller/' ); ?></code></td><td><?php esc_html_e( 'PDF gefiltert nach Schlagwort.', 'wc-professional-catalog' ); ?></td></tr>
		</tbody>
	</table>

	<p class="description" style="margin-top:14px;">
		<?php esc_html_e( 'Hinweis: Falls die Permalinks unerwartet 404 anzeigen, bitte unter Einstellungen → Permalinks einmal auf "Änderungen speichern" klicken.', 'wc-professional-catalog' ); ?>
	</p>
</div>
