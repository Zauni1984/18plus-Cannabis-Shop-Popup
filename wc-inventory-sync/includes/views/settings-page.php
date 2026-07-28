<?php
/**
 * View: Einstellungsseite.
 *
 * @package WC_Inventory_Sync
 * @var array $s Einstellungen (von WCIS_Admin::render_page übergeben).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wcis_notice = isset( $_GET['wcis_notice'] ) ? sanitize_key( wp_unslash( $_GET['wcis_notice'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
$wcis_peers  = WCIS_Settings::get_peers();
$wcis_counts = WCIS_Queue::counts();
$wcis_master = WCIS_Settings::is_master();

// Kandidaten für die Hauptshop-Auswahl (dieser Shop + alle konfigurierten Shops).
$wcis_candidates = array();
$wcis_candidates[ WCIS_Settings::normalize_url( $s['this_shop_url'] ) ] = array(
	'name' => $s['this_shop_name'] . ' ' . __( '(dieser Shop)', 'wc-inventory-sync' ),
	'url'  => untrailingslashit( $s['this_shop_url'] ),
);
foreach ( (array) $s['shops'] as $wcis_shop ) {
	if ( empty( $wcis_shop['url'] ) ) {
		continue;
	}
	$wcis_key = WCIS_Settings::normalize_url( $wcis_shop['url'] );
	if ( ! isset( $wcis_candidates[ $wcis_key ] ) ) {
		$wcis_candidates[ $wcis_key ] = array(
			'name' => $wcis_shop['name'] ? $wcis_shop['name'] : $wcis_shop['url'],
			'url'  => untrailingslashit( $wcis_shop['url'] ),
		);
	}
}
?>
<div class="wrap wcis-wrap">
	<h1><?php esc_html_e( 'WooCommerce Lagerbestand-Synchronisation', 'wc-inventory-sync' ); ?></h1>
	<p class="description">
		<?php esc_html_e( 'Synchronisiert Lagerbestände mehrerer Shops in nahezu Echtzeit. Zuordnung per SKU. Einfache und variable Produkte werden unterstützt; Produkte, die nur in einem Shop existieren, werden ignoriert.', 'wc-inventory-sync' ); ?>
	</p>

	<?php
	// --- Statusmeldungen ---
	$wcis_messages = array(
		'saved'          => array( 'updated', __( 'Einstellungen gespeichert.', 'wc-inventory-sync' ) ),
		'fullsync_done'  => array( 'updated', __( 'Voll-Synchronisation ausgeführt.', 'wc-inventory-sync' ) ),
		'fullsync_error' => array( 'error', __( 'Voll-Synchronisation fehlgeschlagen – sind Ziel-Shops und Secret gesetzt?', 'wc-inventory-sync' ) ),
		'config_pushed'  => array( 'updated', __( 'Konfiguration an die Shops verteilt.', 'wc-inventory-sync' ) ),
		'queue_retried'  => array( 'updated', __( 'Warteschlange wird erneut verarbeitet.', 'wc-inventory-sync' ) ),
		'log_cleared'    => array( 'updated', __( 'Protokoll geleert.', 'wc-inventory-sync' ) ),
		'reconciled'     => array( 'updated', __( 'Abgleich ausgeführt.', 'wc-inventory-sync' ) ),
	);
	if ( isset( $wcis_messages[ $wcis_notice ] ) ) {
		$wcis_m = $wcis_messages[ $wcis_notice ];
		printf( '<div class="notice notice-%s is-dismissible"><p>%s</p></div>', esc_attr( $wcis_m[0] ), esc_html( $wcis_m[1] ) );

		if ( 'fullsync_done' === $wcis_notice ) {
			$wcis_r = get_transient( 'wcis_fullsync_result' );
			if ( $wcis_r ) {
				printf(
					'<div class="notice notice-info"><p>%s</p></div>',
					esc_html( sprintf(
						/* translators: 1: items, 2: peers, 3: batches sent, 4: failed */
						__( '%1$d Artikel an %2$d Shop(s): %3$d Batches gesendet, %4$d fehlgeschlagen (in Warteschlange).', 'wc-inventory-sync' ),
						$wcis_r['items'],
						$wcis_r['peers'],
						$wcis_r['sent'],
						$wcis_r['failed']
					) )
				);
			}
		}
		if ( 'config_pushed' === $wcis_notice ) {
			$wcis_r = get_transient( 'wcis_pushconfig_result' );
			if ( $wcis_r ) {
				printf(
					'<div class="notice notice-info"><p>%s</p></div>',
					esc_html( sprintf( __( '%1$d Shop(s) erfolgreich, %2$d fehlgeschlagen.', 'wc-inventory-sync' ), $wcis_r['ok'], $wcis_r['fail'] ) )
				);
			}
		}
		if ( 'reconciled' === $wcis_notice ) {
			$wcis_r = get_transient( 'wcis_reconcile_result' );
			if ( is_array( $wcis_r ) ) {
				printf(
					'<div class="notice notice-info"><p>%s</p></div>',
					esc_html( sprintf(
						/* translators: 1: peers, 2: skus, 3: drift, 4: corrected, 5: queued, 6: unreachable */
						__( 'Abgleich: %1$d Shop(s) geprüft, %2$d SKUs, %3$d Abweichungen, %4$d korrigiert, %5$d nachzureichen (Warteschlange), %6$d nicht erreichbar.', 'wc-inventory-sync' ),
						$wcis_r['checked_peers'],
						$wcis_r['skus_checked'],
						$wcis_r['drift'],
						$wcis_r['corrected'],
						$wcis_r['queued'],
						$wcis_r['unreachable']
					) )
				);
			}
		}
	}
	?>

	<div class="wcis-status-bar">
		<span class="wcis-badge <?php echo $s['enabled'] ? 'wcis-on' : 'wcis-off'; ?>">
			<?php echo $s['enabled'] ? esc_html__( 'Sync aktiv', 'wc-inventory-sync' ) : esc_html__( 'Sync inaktiv', 'wc-inventory-sync' ); ?>
		</span>
		<span class="wcis-badge <?php echo $wcis_master ? 'wcis-master' : 'wcis-spoke'; ?>">
			<?php echo $wcis_master ? esc_html__( 'Rolle: Hauptshop (Master)', 'wc-inventory-sync' ) : esc_html__( 'Rolle: Neben-Shop', 'wc-inventory-sync' ); ?>
		</span>
		<span class="wcis-badge"><?php echo esc_html( sprintf( __( 'Verbundene Shops: %d', 'wc-inventory-sync' ), count( $wcis_peers ) ) ); ?></span>
		<span class="wcis-badge <?php echo $wcis_counts['failed'] > 0 ? 'wcis-off' : ''; ?>">
			<?php echo esc_html( sprintf( __( 'Warteschlange: %1$d offen, %2$d fehlgeschlagen', 'wc-inventory-sync' ), $wcis_counts['pending'], $wcis_counts['failed'] ) ); ?>
		</span>
		<?php
		$wcis_next = wp_next_scheduled( 'wcis_reconcile' );
		if ( 'off' === $s['reconcile_interval'] || ! $wcis_next ) {
			$wcis_recon_txt = __( 'Abgleich: aus', 'wc-inventory-sync' );
		} else {
			$wcis_recon_txt = sprintf(
				/* translators: %s: human time diff */
				__( 'Nächster Abgleich: in %s', 'wc-inventory-sync' ),
				human_time_diff( time(), $wcis_next )
			);
		}
		?>
		<span class="wcis-badge"><?php echo esc_html( $wcis_recon_txt ); ?></span>
	</div>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="wcis_save" />
		<?php wp_nonce_field( 'wcis_save' ); ?>

		<h2 class="title"><?php esc_html_e( '1. Allgemein', 'wc-inventory-sync' ); ?></h2>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><?php esc_html_e( 'Synchronisation aktiv', 'wc-inventory-sync' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="enabled" value="1" <?php checked( $s['enabled'] ); ?> />
						<?php esc_html_e( 'Bestandsänderungen automatisch an alle verbundenen Shops senden.', 'wc-inventory-sync' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Auch Lagerstatus synchronisieren', 'wc-inventory-sync' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="sync_status" value="1" <?php checked( $s['sync_status'] ); ?> />
						<?php esc_html_e( 'Für Produkte ohne Mengenverwaltung „auf Lager / nicht auf Lager" übertragen.', 'wc-inventory-sync' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Protokoll-Umfang', 'wc-inventory-sync' ); ?></th>
				<td>
					<select name="log_level">
						<option value="info" <?php selected( $s['log_level'], 'info' ); ?>><?php esc_html_e( 'Alles (Info + Fehler)', 'wc-inventory-sync' ); ?></option>
						<option value="error" <?php selected( $s['log_level'], 'error' ); ?>><?php esc_html_e( 'Nur Fehler', 'wc-inventory-sync' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="wcis-batch-size"><?php esc_html_e( 'Batchgröße', 'wc-inventory-sync' ); ?></label></th>
				<td>
					<input type="number" id="wcis-batch-size" name="batch_size" class="small-text" min="1" max="500" value="<?php echo esc_attr( (int) $s['batch_size'] ); ?>" />
					<?php esc_html_e( 'Artikel pro Sync-Anfrage (1–500).', 'wc-inventory-sync' ); ?>
					<p class="description"><?php esc_html_e( 'Kleiner = schonender/timeout-sicherer, größer = schneller. Empfehlung: 50–100.', 'wc-inventory-sync' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="wcis-http-timeout"><?php esc_html_e( 'HTTP-Timeout', 'wc-inventory-sync' ); ?></label></th>
				<td>
					<input type="number" id="wcis-http-timeout" name="http_timeout" class="small-text" min="5" max="60" value="<?php echo esc_attr( (int) $s['http_timeout'] ); ?>" />
					<?php esc_html_e( 'Sekunden je Anfrage (5–60).', 'wc-inventory-sync' ); ?>
				</td>
			</tr>
		</table>

		<h2 class="title"><?php esc_html_e( '2. Netzwerk-Verbindung', 'wc-inventory-sync' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Alle Shops im Verbund benötigen dasselbe Netzwerk-Secret. Erzeugen Sie es einmal im Hauptshop und tragen Sie es in jedem Shop identisch ein.', 'wc-inventory-sync' ); ?>
		</p>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="wcis-secret"><?php esc_html_e( 'Netzwerk-Secret', 'wc-inventory-sync' ); ?></label></th>
				<td>
					<input type="text" id="wcis-secret" name="network_secret" class="regular-text code" value="<?php echo esc_attr( $s['network_secret'] ); ?>" autocomplete="off" />
					<button type="button" class="button" id="wcis-gen-secret" data-secret="<?php echo esc_attr( WCIS_Settings::generate_secret() ); ?>"><?php esc_html_e( 'Neu erzeugen', 'wc-inventory-sync' ); ?></button>
					<p class="description"><?php esc_html_e( 'Geheimschlüssel zum Signieren aller Sync-Anfragen (HMAC-SHA256).', 'wc-inventory-sync' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="wcis-this-name"><?php esc_html_e( 'Name dieses Shops', 'wc-inventory-sync' ); ?></label></th>
				<td><input type="text" id="wcis-this-name" name="this_shop_name" class="regular-text" value="<?php echo esc_attr( $s['this_shop_name'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="wcis-this-url"><?php esc_html_e( 'URL dieses Shops', 'wc-inventory-sync' ); ?></label></th>
				<td>
					<input type="url" id="wcis-this-url" name="this_shop_url" class="regular-text code" value="<?php echo esc_attr( $s['this_shop_url'] ); ?>" />
					<p class="description"><?php esc_html_e( 'Basis-URL dieser WordPress-Installation (ohne abschließenden Schrägstrich).', 'wc-inventory-sync' ); ?></p>
				</td>
			</tr>
		</table>

		<h2 class="title"><?php esc_html_e( '3. Verbundene Shops', 'wc-inventory-sync' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Tragen Sie alle Shops des Verbunds ein (mindestens 2, empfohlen 3+). Dieser Shop kann ebenfalls in der Liste stehen und wird beim Senden automatisch übersprungen.', 'wc-inventory-sync' ); ?>
		</p>
		<table class="widefat wcis-shops" id="wcis-shops">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Shop-Name', 'wc-inventory-sync' ); ?></th>
					<th><?php esc_html_e( 'Shop-URL', 'wc-inventory-sync' ); ?></th>
					<th class="wcis-col-action"><?php esc_html_e( 'Status', 'wc-inventory-sync' ); ?></th>
					<th class="wcis-col-action"></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$wcis_rows = ! empty( $s['shops'] ) ? $s['shops'] : array( array( 'name' => '', 'url' => '' ) );
				foreach ( $wcis_rows as $wcis_row ) :
					?>
					<tr class="wcis-shop-row">
						<td><input type="text" name="shop_name[]" class="regular-text" value="<?php echo esc_attr( isset( $wcis_row['name'] ) ? $wcis_row['name'] : '' ); ?>" placeholder="<?php esc_attr_e( 'z. B. Shop B', 'wc-inventory-sync' ); ?>" /></td>
						<td><input type="url" name="shop_url[]" class="regular-text code wcis-shop-url" value="<?php echo esc_attr( isset( $wcis_row['url'] ) ? $wcis_row['url'] : '' ); ?>" placeholder="https://shop-b.de" /></td>
						<td class="wcis-col-action"><button type="button" class="button wcis-test"><?php esc_html_e( 'Verbindung testen', 'wc-inventory-sync' ); ?></button> <span class="wcis-test-result"></span></td>
						<td class="wcis-col-action"><button type="button" class="button-link-delete wcis-remove-shop">&times; <?php esc_html_e( 'Entfernen', 'wc-inventory-sync' ); ?></button></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<p><button type="button" class="button" id="wcis-add-shop">+ <?php esc_html_e( 'Shop hinzufügen', 'wc-inventory-sync' ); ?></button></p>

		<h2 class="title"><?php esc_html_e( '4. Hauptshop (Master)', 'wc-inventory-sync' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Der Hauptshop ist die Quelle für die erste Voll-Synchronisation. Er kann jederzeit geändert werden. Zum Wechseln hier auswählen, speichern und anschließend „Konfiguration an alle Shops verteilen" nutzen.', 'wc-inventory-sync' ); ?>
		</p>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="wcis-master"><?php esc_html_e( 'Hauptshop', 'wc-inventory-sync' ); ?></label></th>
				<td>
					<select name="master_url" id="wcis-master">
						<?php foreach ( $wcis_candidates as $wcis_cand ) : ?>
							<option value="<?php echo esc_attr( $wcis_cand['url'] ); ?>" <?php selected( WCIS_Settings::normalize_url( $wcis_cand['url'] ), WCIS_Settings::normalize_url( $s['master_url'] ) ); ?>>
								<?php echo esc_html( $wcis_cand['name'] . ' — ' . $wcis_cand['url'] ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
		</table>

		<h2 class="title"><?php esc_html_e( '5. Automatischer Abgleich', 'wc-inventory-sync' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Periodischer Konsistenz-Check: erkennt Bestands-Abweichungen zwischen den Shops und reicht Korrekturen nach – z. B. wenn ein Shop kurz nicht erreichbar war. Wird vom Hauptshop koordiniert.', 'wc-inventory-sync' ); ?>
		</p>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="wcis-reconcile-interval"><?php esc_html_e( 'Abgleich-Intervall', 'wc-inventory-sync' ); ?></label></th>
				<td>
					<select name="reconcile_interval" id="wcis-reconcile-interval">
						<option value="off" <?php selected( $s['reconcile_interval'], 'off' ); ?>><?php esc_html_e( 'Aus', 'wc-inventory-sync' ); ?></option>
						<option value="hourly" <?php selected( $s['reconcile_interval'], 'hourly' ); ?>><?php esc_html_e( 'Stündlich', 'wc-inventory-sync' ); ?></option>
						<option value="sixhourly" <?php selected( $s['reconcile_interval'], 'sixhourly' ); ?>><?php esc_html_e( 'Alle 6 Stunden', 'wc-inventory-sync' ); ?></option>
						<option value="daily" <?php selected( $s['reconcile_interval'], 'daily' ); ?>><?php esc_html_e( 'Täglich', 'wc-inventory-sync' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Läuft nur auf dem Hauptshop. Voraussetzung: WordPress-Cron (WP-Cron) ist aktiv.', 'wc-inventory-sync' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Konflikt-Strategie', 'wc-inventory-sync' ); ?></th>
				<td>
					<fieldset>
						<label>
							<input type="radio" name="reconcile_strategy" value="lowest" <?php checked( $s['reconcile_strategy'], 'lowest' ); ?> />
							<?php esc_html_e( 'Niedrigster Bestand gewinnt (empfohlen – schützt vor Überverkauf)', 'wc-inventory-sync' ); ?>
						</label><br />
						<label>
							<input type="radio" name="reconcile_strategy" value="local" <?php checked( $s['reconcile_strategy'], 'local' ); ?> />
							<?php esc_html_e( 'Hauptshop ist maßgeblich (Wert des Hauptshops wird verteilt)', 'wc-inventory-sync' ); ?>
						</label>
						<p class="description"><?php esc_html_e( 'Bei „Niedrigster gewinnt" werden verpasste Verkäufe sicher nachgezogen. Nach einem Wareneingang/Restock im Hauptshop nutze „Voll-Synchronisation", um erhöhte Bestände zu verteilen.', 'wc-inventory-sync' ); ?></p>
					</fieldset>
				</td>
			</tr>
		</table>

		<h2 class="title"><?php esc_html_e( '6. Produkt-Sync (neue Produkte)', 'wc-inventory-sync' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Optional: Überträgt neue Produkte 1:1 an die anderen Shops (einfache und variable Produkte, inkl. Veröffentlichungsstatus). Zuordnung per SKU.', 'wc-inventory-sync' ); ?>
		</p>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><?php esc_html_e( 'Produkt-Sync aktiv', 'wc-inventory-sync' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="product_sync_enabled" value="1" <?php checked( $s['product_sync_enabled'] ); ?> />
						<?php esc_html_e( 'Neue Produkte automatisch an alle verbundenen Shops übertragen.', 'wc-inventory-sync' ); ?>
					</label>
					<p class="description"><?php esc_html_e( 'Muss auf jedem Empfänger-Shop ebenfalls aktiv sein, damit dieser Produkte annimmt.', 'wc-inventory-sync' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Quelle', 'wc-inventory-sync' ); ?></th>
				<td>
					<fieldset>
						<label>
							<input type="radio" name="product_sync_source" value="master" <?php checked( $s['product_sync_source'], 'master' ); ?> />
							<?php esc_html_e( 'Nur der Hauptshop überträgt neue Produkte (empfohlen)', 'wc-inventory-sync' ); ?>
						</label><br />
						<label>
							<input type="radio" name="product_sync_source" value="any" <?php checked( $s['product_sync_source'], 'any' ); ?> />
							<?php esc_html_e( 'Jeder Shop darf neue Produkte übertragen', 'wc-inventory-sync' ); ?>
						</label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Bilder übertragen', 'wc-inventory-sync' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="product_sync_images" value="1" <?php checked( $s['product_sync_images'] ); ?> />
						<?php esc_html_e( 'Produktbilder per URL mitübertragen (nur beim Neuanlegen).', 'wc-inventory-sync' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Bestehende Produkte', 'wc-inventory-sync' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="product_sync_update_existing" value="1" <?php checked( $s['product_sync_update_existing'] ); ?> />
						<?php esc_html_e( 'Vorhandene Produkte (gleiche SKU) mit den Quelldaten aktualisieren – inkl. Status.', 'wc-inventory-sync' ); ?>
					</label>
					<p class="description"><?php esc_html_e( 'Standard aus: bestehende Produkte bleiben unangetastet, nur der Lagerbestand wird weiter synchronisiert. Aktivieren, um auch Titel/Preis/Status laufend zu spiegeln.', 'wc-inventory-sync' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Zu übertragende Felder', 'wc-inventory-sync' ); ?></th>
				<td>
					<fieldset>
						<?php
						$wcis_fields   = is_array( $s['product_fields'] ) ? $s['product_fields'] : array();
						$wcis_all_flds = WCIS_Filter::product_field_labels();
						foreach ( $wcis_all_flds as $wcis_fk => $wcis_flabel ) :
							$wcis_forced = ( 'name' === $wcis_fk );
							?>
							<label style="display:inline-block; min-width:230px; margin-bottom:4px;">
								<input type="checkbox" name="product_fields[]" value="<?php echo esc_attr( $wcis_fk ); ?>"
									<?php checked( $wcis_forced || in_array( $wcis_fk, $wcis_fields, true ) ); ?>
									<?php disabled( $wcis_forced ); ?> />
								<?php echo esc_html( $wcis_flabel ); ?>
							</label>
						<?php endforeach; ?>
						<?php // "name" ist Pflicht und wird immer gesendet – als Hidden absichern. ?>
						<input type="hidden" name="product_fields[]" value="name" />
						<p class="description"><?php esc_html_e( 'Nur die ausgewählten Felder werden beim Produkt-Sync übertragen. Beispiel: Haken bei „Preis" entfernen, damit jeder Shop eigene Preise behalten kann.', 'wc-inventory-sync' ); ?></p>
					</fieldset>
				</td>
			</tr>
		</table>

		<h2 class="title"><?php esc_html_e( '7. Sync-Filter: Welche Produkte werden übertragen?', 'wc-inventory-sync' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Legt fest, welche Produkte dieser Shop synchronisiert (gilt für Bestands- und Produkt-Sync). Ausgeschlossene Produkte werden nie verändert.', 'wc-inventory-sync' ); ?>
		</p>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><?php esc_html_e( 'Umfang', 'wc-inventory-sync' ); ?></th>
				<td>
					<fieldset>
						<label>
							<input type="radio" name="filter_mode" value="all" <?php checked( $s['filter_mode'], 'all' ); ?> />
							<?php esc_html_e( 'Alle Produkte synchronisieren', 'wc-inventory-sync' ); ?>
						</label><br />
						<label>
							<input type="radio" name="filter_mode" value="selected" <?php checked( $s['filter_mode'], 'selected' ); ?> />
							<?php esc_html_e( 'Nur ausgewählte (nach Kategorie, Marke oder Einzelprodukt)', 'wc-inventory-sync' ); ?>
						</label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="wcis-filter-cats"><?php esc_html_e( 'Kategorien', 'wc-inventory-sync' ); ?></label></th>
				<td>
					<?php $wcis_selcats = array_map( 'intval', (array) $s['filter_categories'] ); ?>
					<select id="wcis-filter-cats" name="filter_categories[]" multiple="multiple" class="wc-enhanced-select" style="min-width:400px;" data-placeholder="<?php esc_attr_e( 'Kategorien wählen …', 'wc-inventory-sync' ); ?>">
						<?php
						$wcis_terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) );
						if ( ! is_wp_error( $wcis_terms ) ) {
							foreach ( $wcis_terms as $wcis_t ) {
								printf(
									'<option value="%d" %s>%s</option>',
									(int) $wcis_t->term_id,
									selected( in_array( (int) $wcis_t->term_id, $wcis_selcats, true ), true, false ),
									esc_html( $wcis_t->name )
								);
							}
						}
						?>
					</select>
					<p class="description"><?php esc_html_e( 'Produkte dieser Kategorien werden synchronisiert (im Modus „Nur ausgewählte").', 'wc-inventory-sync' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="wcis-filter-brands"><?php esc_html_e( 'Marken', 'wc-inventory-sync' ); ?></label></th>
				<td>
					<?php
					$wcis_btax   = WCIS_Filter::brand_taxonomy();
					$wcis_selbr  = array_map( 'intval', (array) $s['filter_brands'] );
					if ( $wcis_btax ) :
						?>
						<select id="wcis-filter-brands" name="filter_brands[]" multiple="multiple" class="wc-enhanced-select" style="min-width:400px;" data-placeholder="<?php esc_attr_e( 'Marken wählen …', 'wc-inventory-sync' ); ?>">
							<?php
							$wcis_bterms = get_terms( array( 'taxonomy' => $wcis_btax, 'hide_empty' => false ) );
							if ( ! is_wp_error( $wcis_bterms ) ) {
								foreach ( $wcis_bterms as $wcis_bt ) {
									printf(
										'<option value="%d" %s>%s</option>',
										(int) $wcis_bt->term_id,
										selected( in_array( (int) $wcis_bt->term_id, $wcis_selbr, true ), true, false ),
										esc_html( $wcis_bt->name )
									);
								}
							}
							?>
						</select>
						<p class="description"><?php echo esc_html( sprintf( __( 'Erkannte Marken-Taxonomie: %s.', 'wc-inventory-sync' ), $wcis_btax ) ); ?></p>
					<?php else : ?>
						<p class="description"><?php esc_html_e( 'Keine Marken-Taxonomie gefunden (z. B. WooCommerce Brands / Perfect Brands). Marken-Filter ist daher nicht verfügbar.', 'wc-inventory-sync' ); ?></p>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="wcis-filter-include"><?php esc_html_e( 'Einzelne Produkte einschließen', 'wc-inventory-sync' ); ?></label></th>
				<td>
					<select id="wcis-filter-include" name="filter_include_ids[]" multiple="multiple" class="wc-product-search" style="min-width:400px;" data-placeholder="<?php esc_attr_e( 'Produkte suchen …', 'wc-inventory-sync' ); ?>" data-action="woocommerce_json_search_products_and_variations">
						<?php
						foreach ( array_map( 'intval', (array) $s['filter_include_ids'] ) as $wcis_pid ) {
							$wcis_p = wc_get_product( $wcis_pid );
							if ( $wcis_p ) {
								printf( '<option value="%d" selected>%s</option>', (int) $wcis_pid, esc_html( wp_strip_all_tags( $wcis_p->get_formatted_name() ) ) );
							}
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="wcis-filter-exclude"><?php esc_html_e( 'Einzelne Produkte ausschließen', 'wc-inventory-sync' ); ?></label></th>
				<td>
					<select id="wcis-filter-exclude" name="filter_exclude_ids[]" multiple="multiple" class="wc-product-search" style="min-width:400px;" data-placeholder="<?php esc_attr_e( 'Produkte suchen …', 'wc-inventory-sync' ); ?>" data-action="woocommerce_json_search_products_and_variations">
						<?php
						foreach ( array_map( 'intval', (array) $s['filter_exclude_ids'] ) as $wcis_pid ) {
							$wcis_p = wc_get_product( $wcis_pid );
							if ( $wcis_p ) {
								printf( '<option value="%d" selected>%s</option>', (int) $wcis_pid, esc_html( wp_strip_all_tags( $wcis_p->get_formatted_name() ) ) );
							}
						}
						?>
					</select>
					<p class="description"><?php esc_html_e( 'Diese Produkte werden nie synchronisiert – weder ausgehend noch eingehend (harter Ausschluss).', 'wc-inventory-sync' ); ?></p>
				</td>
			</tr>
		</table>

		<p class="submit">
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Einstellungen speichern', 'wc-inventory-sync' ); ?></button>
		</p>
	</form>

	<hr />

	<h2 class="title"><?php esc_html_e( '8. Aktionen', 'wc-inventory-sync' ); ?></h2>
	<div class="wcis-actions">
		<?php
		$wcis_job     = WCIS_Fullsync::state();
		$wcis_running = $wcis_job && 'running' === $wcis_job['status'];
		$wcis_pct     = WCIS_Fullsync::percent( $wcis_job );
		?>
		<div class="wcis-fullsync">
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="wcis-action-form" id="wcis-fullsync-form">
				<input type="hidden" name="action" value="wcis_full_sync" />
				<?php wp_nonce_field( 'wcis_full_sync' ); ?>
				<button type="submit" class="button button-secondary" id="wcis-full-sync">
					<?php esc_html_e( 'Erste Voll-Synchronisation starten (von diesem Shop)', 'wc-inventory-sync' ); ?>
				</button>
				<button type="button" class="button" id="wcis-full-sync-cancel" style="display:none;">
					<?php esc_html_e( 'Abbrechen', 'wc-inventory-sync' ); ?>
				</button>
				<span class="description"><?php esc_html_e( 'Sendet den gesamten Bestand dieses Shops an alle verbundenen Shops. Idealerweise vom Hauptshop ausführen.', 'wc-inventory-sync' ); ?></span>
			</form>

			<div id="wcis-progress-wrap" class="wcis-progress-wrap" style="<?php echo $wcis_running ? '' : 'display:none;'; ?>">
				<div class="wcis-progress-bar">
					<div class="wcis-progress-fill" id="wcis-progress-fill" style="width:<?php echo esc_attr( $wcis_pct ); ?>%;">
						<span id="wcis-progress-label"><?php echo esc_html( $wcis_pct . '%' ); ?></span>
					</div>
				</div>
				<p class="wcis-progress-text" id="wcis-progress-text">
					<?php
					if ( $wcis_running ) {
						echo esc_html( sprintf( __( 'Läuft … %d %% – bitte diese Seite geöffnet lassen.', 'wc-inventory-sync' ), $wcis_pct ) );
					}
					?>
				</p>
			</div>
		</div>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="wcis-action-form">
			<input type="hidden" name="action" value="wcis_push_config" />
			<?php wp_nonce_field( 'wcis_push_config' ); ?>
			<button type="submit" class="button button-secondary">
				<?php esc_html_e( 'Konfiguration an alle Shops verteilen', 'wc-inventory-sync' ); ?>
			</button>
			<span class="description"><?php esc_html_e( 'Überträgt Shop-Liste und Hauptshop-Auswahl an alle Peers (Secret & eigene URL bleiben unverändert).', 'wc-inventory-sync' ); ?></span>
		</form>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="wcis-action-form">
			<input type="hidden" name="action" value="wcis_reconcile_now" />
			<?php wp_nonce_field( 'wcis_reconcile_now' ); ?>
			<button type="submit" class="button button-secondary">
				<?php esc_html_e( 'Jetzt abgleichen', 'wc-inventory-sync' ); ?>
			</button>
			<span class="description"><?php esc_html_e( 'Vergleicht die Bestände mit allen Shops und korrigiert Abweichungen sofort. Nicht erreichbare Shops werden in die Warteschlange gelegt.', 'wc-inventory-sync' ); ?></span>
		</form>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="wcis-action-form">
			<input type="hidden" name="action" value="wcis_retry_queue" />
			<?php wp_nonce_field( 'wcis_retry_queue' ); ?>
			<button type="submit" class="button">
				<?php esc_html_e( 'Warteschlange jetzt verarbeiten', 'wc-inventory-sync' ); ?>
			</button>
		</form>
	</div>

	<?php $wcis_pjob = WCIS_Product_Sync::bulk_state(); ?>
	<?php $wcis_prunning = $wcis_pjob && 'running' === $wcis_pjob['status']; ?>
	<?php $wcis_ppct = WCIS_Product_Sync::bulk_percent( $wcis_pjob ); ?>
	<div class="wcis-fullsync" style="margin-top:16px;">
		<form method="post" class="wcis-action-form" id="wcis-productsync-form" onsubmit="return false;">
			<button type="submit" class="button button-secondary" id="wcis-product-sync" <?php disabled( ! $s['product_sync_enabled'] ); ?>>
				<?php esc_html_e( 'Alle Produkte jetzt an alle Shops übertragen', 'wc-inventory-sync' ); ?>
			</button>
			<button type="button" class="button" id="wcis-product-sync-cancel" style="display:none;">
				<?php esc_html_e( 'Abbrechen', 'wc-inventory-sync' ); ?>
			</button>
			<span class="description">
				<?php
				if ( $s['product_sync_enabled'] ) {
					esc_html_e( 'Überträgt einfache und variable Produkte 1:1 (inkl. Status). Ideal für die erste Befüllung neuer Shops.', 'wc-inventory-sync' );
				} else {
					esc_html_e( 'Bitte zuerst „Produkt-Sync aktiv" oben einschalten und speichern.', 'wc-inventory-sync' );
				}
				?>
			</span>
		</form>
		<div id="wcis-product-progress-wrap" class="wcis-progress-wrap" style="<?php echo $wcis_prunning ? '' : 'display:none;'; ?>">
			<div class="wcis-progress-bar">
				<div class="wcis-progress-fill" id="wcis-product-progress-fill" style="width:<?php echo esc_attr( $wcis_ppct ); ?>%;">
					<span id="wcis-product-progress-label"><?php echo esc_html( $wcis_ppct . '%' ); ?></span>
				</div>
			</div>
			<p class="wcis-progress-text" id="wcis-product-progress-text"></p>
		</div>
	</div>

	<h2 class="title"><?php esc_html_e( '9. Protokoll', 'wc-inventory-sync' ); ?></h2>
	<?php $wcis_logs = WCIS_Logger::recent( 60 ); ?>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-bottom:8px;">
		<input type="hidden" name="action" value="wcis_clear_log" />
		<?php wp_nonce_field( 'wcis_clear_log' ); ?>
		<button type="submit" class="button"><?php esc_html_e( 'Protokoll leeren', 'wc-inventory-sync' ); ?></button>
	</form>
	<table class="widefat striped wcis-log">
		<thead>
			<tr>
				<th style="width:150px;"><?php esc_html_e( 'Zeit (UTC)', 'wc-inventory-sync' ); ?></th>
				<th style="width:70px;"><?php esc_html_e( 'Ebene', 'wc-inventory-sync' ); ?></th>
				<th style="width:80px;"><?php esc_html_e( 'Richtung', 'wc-inventory-sync' ); ?></th>
				<th><?php esc_html_e( 'Nachricht', 'wc-inventory-sync' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $wcis_logs ) ) : ?>
				<tr><td colspan="4"><?php esc_html_e( 'Noch keine Einträge.', 'wc-inventory-sync' ); ?></td></tr>
			<?php else : ?>
				<?php foreach ( $wcis_logs as $wcis_l ) : ?>
					<tr>
						<td><?php echo esc_html( $wcis_l['created_at'] ); ?></td>
						<td><span class="wcis-lvl wcis-lvl-<?php echo esc_attr( $wcis_l['level'] ); ?>"><?php echo esc_html( $wcis_l['level'] ); ?></span></td>
						<td><?php echo esc_html( $wcis_l['direction'] ); ?></td>
						<td><?php echo esc_html( $wcis_l['message'] ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>
