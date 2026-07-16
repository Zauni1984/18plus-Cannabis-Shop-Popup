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

		<p class="submit">
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Einstellungen speichern', 'wc-inventory-sync' ); ?></button>
		</p>
	</form>

	<hr />

	<h2 class="title"><?php esc_html_e( '5. Aktionen', 'wc-inventory-sync' ); ?></h2>
	<div class="wcis-actions">
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="wcis-action-form">
			<input type="hidden" name="action" value="wcis_full_sync" />
			<?php wp_nonce_field( 'wcis_full_sync' ); ?>
			<button type="submit" class="button button-secondary" id="wcis-full-sync">
				<?php esc_html_e( 'Erste Voll-Synchronisation starten (von diesem Shop)', 'wc-inventory-sync' ); ?>
			</button>
			<span class="description"><?php esc_html_e( 'Sendet den gesamten Bestand dieses Shops an alle verbundenen Shops. Idealerweise vom Hauptshop ausführen.', 'wc-inventory-sync' ); ?></span>
		</form>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="wcis-action-form">
			<input type="hidden" name="action" value="wcis_push_config" />
			<?php wp_nonce_field( 'wcis_push_config' ); ?>
			<button type="submit" class="button button-secondary">
				<?php esc_html_e( 'Konfiguration an alle Shops verteilen', 'wc-inventory-sync' ); ?>
			</button>
			<span class="description"><?php esc_html_e( 'Überträgt Shop-Liste und Hauptshop-Auswahl an alle Peers (Secret & eigene URL bleiben unverändert).', 'wc-inventory-sync' ); ?></span>
		</form>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="wcis-action-form">
			<input type="hidden" name="action" value="wcis_retry_queue" />
			<?php wp_nonce_field( 'wcis_retry_queue' ); ?>
			<button type="submit" class="button">
				<?php esc_html_e( 'Warteschlange jetzt verarbeiten', 'wc-inventory-sync' ); ?>
			</button>
		</form>
	</div>

	<h2 class="title"><?php esc_html_e( '6. Protokoll', 'wc-inventory-sync' ); ?></h2>
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
