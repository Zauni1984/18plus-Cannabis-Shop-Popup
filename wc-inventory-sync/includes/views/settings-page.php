<?php
/**
 * View: Einstellungsseite (modernes App-Layout mit Tab-Navigation).
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

/**
 * Kleiner Inline-SVG-Icon-Helfer (stroke, currentColor).
 *
 * @param string $name Icon-Name.
 * @return string
 */
$wcis_icon = static function ( $name ) {
	$p = array(
		'general'   => '<path d="M3 6h18M3 12h18M3 18h18"/>',
		'network'   => '<circle cx="12" cy="5" r="2.5"/><circle cx="5" cy="19" r="2.5"/><circle cx="19" cy="19" r="2.5"/><path d="M12 7.5v4M12 11.5l-6 5M12 11.5l6 5"/>',
		'reconcile' => '<path d="M21 12a9 9 0 1 1-3-6.7"/><path d="M21 4v4h-4"/>',
		'product'   => '<path d="M3 7l9-4 9 4-9 4-9-4z"/><path d="M3 7v10l9 4 9-4V7"/><path d="M12 11v10"/>',
		'filter'    => '<path d="M3 5h18l-7 8v6l-4-2v-4L3 5z"/>',
		'actions'   => '<path d="M13 2L3 14h7l-1 8 10-12h-7l1-8z"/>',
		'log'       => '<path d="M4 4h16v16H4z"/><path d="M8 8h8M8 12h8M8 16h5"/>',
	);
	$d = isset( $p[ $name ] ) ? $p[ $name ] : '';
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $d . '</svg>';
};

$wcis_tabs = array(
	'general'   => __( 'Allgemein', 'wc-inventory-sync' ),
	'network'   => __( 'Netzwerk', 'wc-inventory-sync' ),
	'reconcile' => __( 'Abgleich', 'wc-inventory-sync' ),
	'product'   => __( 'Produkt-Sync', 'wc-inventory-sync' ),
	'filter'    => __( 'Sync-Filter', 'wc-inventory-sync' ),
	'actions'   => __( 'Aktionen', 'wc-inventory-sync' ),
	'log'       => __( 'Protokoll', 'wc-inventory-sync' ),
);
?>
<div class="wrap wcis-wrap">
<div class="wcis-app" id="wcis-app">

	<!-- Topbar -->
	<header class="wcis-topbar">
		<div class="wcis-brand">
			<span class="wcis-logo"><?php echo $wcis_icon( 'reconcile' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<span class="wcis-brand-text">
				<strong><?php esc_html_e( 'Lagerbestand-Sync', 'wc-inventory-sync' ); ?></strong>
				<small><?php esc_html_e( 'Multi-Shop-Synchronisation', 'wc-inventory-sync' ); ?></small>
			</span>
		</div>
		<div class="wcis-pills">
			<span class="wcis-pill <?php echo $s['enabled'] ? 'is-on' : 'is-off'; ?>">
				<i class="wcis-dot"></i><?php echo $s['enabled'] ? esc_html__( 'Sync aktiv', 'wc-inventory-sync' ) : esc_html__( 'Sync inaktiv', 'wc-inventory-sync' ); ?>
			</span>
			<span class="wcis-pill <?php echo $wcis_master ? 'is-master' : ''; ?>">
				<?php echo $wcis_master ? esc_html__( 'Hauptshop', 'wc-inventory-sync' ) : esc_html__( 'Neben-Shop', 'wc-inventory-sync' ); ?>
			</span>
			<span class="wcis-pill"><?php echo esc_html( sprintf( __( '%d Shops', 'wc-inventory-sync' ), count( $wcis_peers ) ) ); ?></span>
			<span class="wcis-pill <?php echo $wcis_counts['failed'] > 0 ? 'is-warn' : ''; ?>">
				<?php echo esc_html( sprintf( __( 'Queue %1$d/%2$d', 'wc-inventory-sync' ), $wcis_counts['pending'], $wcis_counts['failed'] ) ); ?>
			</span>
			<?php
			$wcis_next = wp_next_scheduled( 'wcis_reconcile' );
			if ( 'off' === $s['reconcile_interval'] || ! $wcis_next ) {
				$wcis_recon_txt = __( 'Abgleich: aus', 'wc-inventory-sync' );
			} else {
				$wcis_recon_txt = sprintf( __( 'Abgleich in %s', 'wc-inventory-sync' ), human_time_diff( time(), $wcis_next ) );
			}
			?>
			<span class="wcis-pill"><?php echo esc_html( $wcis_recon_txt ); ?></span>
		</div>
		<button type="submit" form="wcis-settings-form" class="wcis-save">
			<?php echo $wcis_icon( 'reconcile' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<span><?php esc_html_e( 'Speichern', 'wc-inventory-sync' ); ?></span>
		</button>
	</header>

	<!-- Meldungen -->
	<div class="wcis-notices">
		<?php
		$wcis_messages = array(
			'saved'          => array( 'ok', __( 'Einstellungen gespeichert.', 'wc-inventory-sync' ) ),
			'fullsync_done'  => array( 'ok', __( 'Voll-Synchronisation ausgeführt.', 'wc-inventory-sync' ) ),
			'fullsync_error' => array( 'err', __( 'Voll-Synchronisation fehlgeschlagen – sind Ziel-Shops und Secret gesetzt?', 'wc-inventory-sync' ) ),
			'config_pushed'  => array( 'ok', __( 'Konfiguration an die Shops verteilt.', 'wc-inventory-sync' ) ),
			'queue_retried'  => array( 'ok', __( 'Warteschlange wird erneut verarbeitet.', 'wc-inventory-sync' ) ),
			'log_cleared'    => array( 'ok', __( 'Protokoll geleert.', 'wc-inventory-sync' ) ),
			'reconciled'     => array( 'ok', __( 'Abgleich ausgeführt.', 'wc-inventory-sync' ) ),
		);
		if ( isset( $wcis_messages[ $wcis_notice ] ) ) {
			$wcis_m = $wcis_messages[ $wcis_notice ];
			printf( '<div class="wcis-alert is-%s">%s</div>', esc_attr( $wcis_m[0] ), esc_html( $wcis_m[1] ) );

			if ( 'fullsync_done' === $wcis_notice ) {
				$wcis_r = get_transient( 'wcis_fullsync_result' );
				if ( $wcis_r ) {
					printf(
						'<div class="wcis-alert is-info">%s</div>',
						esc_html( sprintf(
							__( '%1$d Artikel an %2$d Shop(s): %3$d Batches gesendet, %4$d fehlgeschlagen (in Warteschlange).', 'wc-inventory-sync' ),
							$wcis_r['items'], $wcis_r['peers'], $wcis_r['sent'], $wcis_r['failed']
						) )
					);
				}
			}
			if ( 'config_pushed' === $wcis_notice ) {
				$wcis_r = get_transient( 'wcis_pushconfig_result' );
				if ( $wcis_r ) {
					printf( '<div class="wcis-alert is-info">%s</div>', esc_html( sprintf( __( '%1$d Shop(s) erfolgreich, %2$d fehlgeschlagen.', 'wc-inventory-sync' ), $wcis_r['ok'], $wcis_r['fail'] ) ) );
				}
			}
			if ( 'reconciled' === $wcis_notice ) {
				$wcis_r = get_transient( 'wcis_reconcile_result' );
				if ( is_array( $wcis_r ) ) {
					printf(
						'<div class="wcis-alert is-info">%s</div>',
						esc_html( sprintf(
							__( 'Abgleich: %1$d Shop(s) geprüft, %2$d SKUs, %3$d Abweichungen, %4$d korrigiert, %5$d nachzureichen (Warteschlange), %6$d nicht erreichbar.', 'wc-inventory-sync' ),
							$wcis_r['checked_peers'], $wcis_r['skus_checked'], $wcis_r['drift'], $wcis_r['corrected'], $wcis_r['queued'], $wcis_r['unreachable']
						) )
					);
				}
			}
		}
		?>
	</div>

	<div class="wcis-shell">

		<!-- Seitliche Navigation -->
		<nav class="wcis-nav" role="tablist">
			<?php $wcis_first = true; foreach ( $wcis_tabs as $wcis_id => $wcis_label ) : ?>
				<button type="button" class="wcis-navitem<?php echo $wcis_first ? ' is-active' : ''; ?>" data-tab="<?php echo esc_attr( $wcis_id ); ?>" role="tab">
					<span class="wcis-navicon"><?php echo $wcis_icon( $wcis_id ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<span><?php echo esc_html( $wcis_label ); ?></span>
				</button>
			<?php $wcis_first = false; endforeach; ?>
		</nav>

		<!-- Inhalt -->
		<div class="wcis-content">

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="wcis-settings-form">
				<input type="hidden" name="action" value="wcis_save" />
				<?php wp_nonce_field( 'wcis_save' ); ?>

				<!-- TAB: Allgemein -->
				<section class="wcis-tab is-active" data-tab="general">
					<div class="wcis-card">
						<div class="wcis-card-head"><h2><?php esc_html_e( 'Allgemein', 'wc-inventory-sync' ); ?></h2></div>
						<div class="wcis-card-body">
							<div class="wcis-field wcis-field--switch">
								<div class="wcis-field-main">
									<label class="wcis-switch"><input type="checkbox" name="enabled" value="1" <?php checked( $s['enabled'] ); ?> /><span class="wcis-slider"></span></label>
									<div><strong><?php esc_html_e( 'Synchronisation aktiv', 'wc-inventory-sync' ); ?></strong><p><?php esc_html_e( 'Bestandsänderungen automatisch an alle verbundenen Shops senden.', 'wc-inventory-sync' ); ?></p></div>
								</div>
							</div>
							<div class="wcis-field wcis-field--switch">
								<div class="wcis-field-main">
									<label class="wcis-switch"><input type="checkbox" name="sync_status" value="1" <?php checked( $s['sync_status'] ); ?> /><span class="wcis-slider"></span></label>
									<div><strong><?php esc_html_e( 'Lagerstatus mitsynchronisieren', 'wc-inventory-sync' ); ?></strong><p><?php esc_html_e( 'Für Produkte ohne Mengenverwaltung „auf Lager / nicht auf Lager" übertragen.', 'wc-inventory-sync' ); ?></p></div>
								</div>
							</div>
							<div class="wcis-grid">
								<div class="wcis-field">
									<label for="wcis-log-level"><?php esc_html_e( 'Protokoll-Umfang', 'wc-inventory-sync' ); ?></label>
									<select id="wcis-log-level" name="log_level">
										<option value="info" <?php selected( $s['log_level'], 'info' ); ?>><?php esc_html_e( 'Alles (Info + Fehler)', 'wc-inventory-sync' ); ?></option>
										<option value="error" <?php selected( $s['log_level'], 'error' ); ?>><?php esc_html_e( 'Nur Fehler', 'wc-inventory-sync' ); ?></option>
									</select>
								</div>
								<div class="wcis-field">
									<label for="wcis-batch-size"><?php esc_html_e( 'Batchgröße', 'wc-inventory-sync' ); ?></label>
									<input type="number" id="wcis-batch-size" name="batch_size" min="1" max="500" value="<?php echo esc_attr( (int) $s['batch_size'] ); ?>" />
									<small><?php esc_html_e( 'Artikel pro Anfrage (1–500). Empfehlung: 50–100.', 'wc-inventory-sync' ); ?></small>
								</div>
								<div class="wcis-field">
									<label for="wcis-http-timeout"><?php esc_html_e( 'HTTP-Timeout', 'wc-inventory-sync' ); ?></label>
									<input type="number" id="wcis-http-timeout" name="http_timeout" min="5" max="60" value="<?php echo esc_attr( (int) $s['http_timeout'] ); ?>" />
									<small><?php esc_html_e( 'Sekunden je Anfrage (5–60).', 'wc-inventory-sync' ); ?></small>
								</div>
							</div>
						</div>
					</div>
				</section>

				<!-- TAB: Netzwerk -->
				<section class="wcis-tab" data-tab="network">
					<div class="wcis-card">
						<div class="wcis-card-head"><h2><?php esc_html_e( 'Netzwerk-Verbindung', 'wc-inventory-sync' ); ?></h2>
							<p><?php esc_html_e( 'Alle Shops im Verbund benötigen dasselbe Netzwerk-Secret.', 'wc-inventory-sync' ); ?></p></div>
						<div class="wcis-card-body">
							<div class="wcis-field">
								<label for="wcis-secret"><?php esc_html_e( 'Netzwerk-Secret', 'wc-inventory-sync' ); ?></label>
								<div class="wcis-inline">
									<input type="text" id="wcis-secret" class="code" name="network_secret" value="<?php echo esc_attr( $s['network_secret'] ); ?>" autocomplete="off" />
									<button type="button" class="wcis-btn wcis-btn--ghost" id="wcis-gen-secret" data-secret="<?php echo esc_attr( WCIS_Settings::generate_secret() ); ?>"><?php esc_html_e( 'Neu erzeugen', 'wc-inventory-sync' ); ?></button>
								</div>
								<small><?php esc_html_e( 'Geheimschlüssel zum Signieren aller Sync-Anfragen (HMAC-SHA256).', 'wc-inventory-sync' ); ?></small>
							</div>
							<div class="wcis-grid">
								<div class="wcis-field">
									<label for="wcis-this-name"><?php esc_html_e( 'Name dieses Shops', 'wc-inventory-sync' ); ?></label>
									<input type="text" id="wcis-this-name" name="this_shop_name" value="<?php echo esc_attr( $s['this_shop_name'] ); ?>" />
								</div>
								<div class="wcis-field">
									<label for="wcis-this-url"><?php esc_html_e( 'URL dieses Shops', 'wc-inventory-sync' ); ?></label>
									<input type="url" id="wcis-this-url" class="code" name="this_shop_url" value="<?php echo esc_attr( $s['this_shop_url'] ); ?>" />
									<small><?php esc_html_e( 'Basis-URL ohne abschließenden Schrägstrich.', 'wc-inventory-sync' ); ?></small>
								</div>
							</div>
						</div>
					</div>

					<div class="wcis-card">
						<div class="wcis-card-head"><h2><?php esc_html_e( 'Verbundene Shops', 'wc-inventory-sync' ); ?></h2>
							<p><?php esc_html_e( 'Alle Shops des Verbunds eintragen (empfohlen 3+). Dieser Shop wird beim Senden automatisch übersprungen.', 'wc-inventory-sync' ); ?></p></div>
						<div class="wcis-card-body">
							<div class="wcis-shops" id="wcis-shops">
								<?php
								$wcis_rows = ! empty( $s['shops'] ) ? $s['shops'] : array( array( 'name' => '', 'url' => '' ) );
								foreach ( $wcis_rows as $wcis_row ) :
									?>
									<div class="wcis-shop-row">
										<input type="text" name="shop_name[]" value="<?php echo esc_attr( isset( $wcis_row['name'] ) ? $wcis_row['name'] : '' ); ?>" placeholder="<?php esc_attr_e( 'z. B. Shop B', 'wc-inventory-sync' ); ?>" />
										<input type="url" name="shop_url[]" class="code wcis-shop-url" value="<?php echo esc_attr( isset( $wcis_row['url'] ) ? $wcis_row['url'] : '' ); ?>" placeholder="https://shop-b.de" />
										<button type="button" class="wcis-btn wcis-btn--ghost wcis-test"><?php esc_html_e( 'Testen', 'wc-inventory-sync' ); ?></button>
										<span class="wcis-test-result"></span>
										<button type="button" class="wcis-iconbtn wcis-remove-shop" title="<?php esc_attr_e( 'Entfernen', 'wc-inventory-sync' ); ?>">&times;</button>
									</div>
								<?php endforeach; ?>
							</div>
							<p><button type="button" class="wcis-btn wcis-btn--ghost" id="wcis-add-shop">+ <?php esc_html_e( 'Shop hinzufügen', 'wc-inventory-sync' ); ?></button></p>
						</div>
					</div>

					<div class="wcis-card">
						<div class="wcis-card-head"><h2><?php esc_html_e( 'Hauptshop (Master)', 'wc-inventory-sync' ); ?></h2>
							<p><?php esc_html_e( 'Quelle für die erste Voll-Synchronisation. Jederzeit änderbar; danach „Konfiguration verteilen" nutzen.', 'wc-inventory-sync' ); ?></p></div>
						<div class="wcis-card-body">
							<div class="wcis-field">
								<label for="wcis-master"><?php esc_html_e( 'Hauptshop', 'wc-inventory-sync' ); ?></label>
								<select name="master_url" id="wcis-master">
									<?php foreach ( $wcis_candidates as $wcis_cand ) : ?>
										<option value="<?php echo esc_attr( $wcis_cand['url'] ); ?>" <?php selected( WCIS_Settings::normalize_url( $wcis_cand['url'] ), WCIS_Settings::normalize_url( $s['master_url'] ) ); ?>>
											<?php echo esc_html( $wcis_cand['name'] . ' — ' . $wcis_cand['url'] ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>
				</section>

				<!-- TAB: Abgleich -->
				<section class="wcis-tab" data-tab="reconcile">
					<div class="wcis-card">
						<div class="wcis-card-head"><h2><?php esc_html_e( 'Automatischer Abgleich', 'wc-inventory-sync' ); ?></h2>
							<p><?php esc_html_e( 'Periodischer Konsistenz-Check: erkennt Abweichungen und reicht Korrekturen nach. Wird vom Hauptshop koordiniert.', 'wc-inventory-sync' ); ?></p></div>
						<div class="wcis-card-body">
							<div class="wcis-field">
								<label for="wcis-reconcile-interval"><?php esc_html_e( 'Abgleich-Intervall', 'wc-inventory-sync' ); ?></label>
								<select name="reconcile_interval" id="wcis-reconcile-interval">
									<option value="off" <?php selected( $s['reconcile_interval'], 'off' ); ?>><?php esc_html_e( 'Aus', 'wc-inventory-sync' ); ?></option>
									<option value="hourly" <?php selected( $s['reconcile_interval'], 'hourly' ); ?>><?php esc_html_e( 'Stündlich', 'wc-inventory-sync' ); ?></option>
									<option value="sixhourly" <?php selected( $s['reconcile_interval'], 'sixhourly' ); ?>><?php esc_html_e( 'Alle 6 Stunden', 'wc-inventory-sync' ); ?></option>
									<option value="daily" <?php selected( $s['reconcile_interval'], 'daily' ); ?>><?php esc_html_e( 'Täglich', 'wc-inventory-sync' ); ?></option>
								</select>
								<small><?php esc_html_e( 'Läuft nur auf dem Hauptshop. Voraussetzung: WP-Cron aktiv.', 'wc-inventory-sync' ); ?></small>
							</div>
							<div class="wcis-field">
								<label><?php esc_html_e( 'Konflikt-Strategie', 'wc-inventory-sync' ); ?></label>
								<label class="wcis-radio"><input type="radio" name="reconcile_strategy" value="lowest" <?php checked( $s['reconcile_strategy'], 'lowest' ); ?> /> <span><?php esc_html_e( 'Niedrigster Bestand gewinnt (empfohlen – schützt vor Überverkauf)', 'wc-inventory-sync' ); ?></span></label>
								<label class="wcis-radio"><input type="radio" name="reconcile_strategy" value="local" <?php checked( $s['reconcile_strategy'], 'local' ); ?> /> <span><?php esc_html_e( 'Hauptshop ist maßgeblich (Wert des Hauptshops wird verteilt)', 'wc-inventory-sync' ); ?></span></label>
								<small><?php esc_html_e( 'Nach einem Wareneingang im Hauptshop „Voll-Synchronisation" nutzen, um erhöhte Bestände zu verteilen.', 'wc-inventory-sync' ); ?></small>
							</div>
						</div>
					</div>
				</section>

				<!-- TAB: Produkt-Sync -->
				<section class="wcis-tab" data-tab="product">
					<div class="wcis-card">
						<div class="wcis-card-head"><h2><?php esc_html_e( 'Produkt-Sync (neue Produkte)', 'wc-inventory-sync' ); ?></h2>
							<p><?php esc_html_e( 'Optional: neue Produkte 1:1 an die anderen Shops übertragen (einfach & variabel, inkl. Status). Zuordnung per SKU.', 'wc-inventory-sync' ); ?></p></div>
						<div class="wcis-card-body">
							<div class="wcis-field wcis-field--switch">
								<div class="wcis-field-main">
									<label class="wcis-switch"><input type="checkbox" name="product_sync_enabled" value="1" <?php checked( $s['product_sync_enabled'] ); ?> /><span class="wcis-slider"></span></label>
									<div><strong><?php esc_html_e( 'Produkt-Sync aktiv', 'wc-inventory-sync' ); ?></strong><p><?php esc_html_e( 'Muss auch auf jedem Empfänger-Shop aktiv sein.', 'wc-inventory-sync' ); ?></p></div>
								</div>
							</div>
							<div class="wcis-field">
								<label><?php esc_html_e( 'Quelle', 'wc-inventory-sync' ); ?></label>
								<label class="wcis-radio"><input type="radio" name="product_sync_source" value="master" <?php checked( $s['product_sync_source'], 'master' ); ?> /> <span><?php esc_html_e( 'Nur der Hauptshop überträgt neue Produkte (empfohlen)', 'wc-inventory-sync' ); ?></span></label>
								<label class="wcis-radio"><input type="radio" name="product_sync_source" value="any" <?php checked( $s['product_sync_source'], 'any' ); ?> /> <span><?php esc_html_e( 'Jeder Shop darf neue Produkte übertragen', 'wc-inventory-sync' ); ?></span></label>
							</div>
							<div class="wcis-field wcis-field--switch">
								<div class="wcis-field-main">
									<label class="wcis-switch"><input type="checkbox" name="product_sync_images" value="1" <?php checked( $s['product_sync_images'] ); ?> /><span class="wcis-slider"></span></label>
									<div><strong><?php esc_html_e( 'Bilder übertragen', 'wc-inventory-sync' ); ?></strong><p><?php esc_html_e( 'Produktbilder per URL mitübertragen (nur beim Neuanlegen).', 'wc-inventory-sync' ); ?></p></div>
								</div>
							</div>
							<div class="wcis-field wcis-field--switch">
								<div class="wcis-field-main">
									<label class="wcis-switch"><input type="checkbox" name="product_sync_update_existing" value="1" <?php checked( $s['product_sync_update_existing'] ); ?> /><span class="wcis-slider"></span></label>
									<div><strong><?php esc_html_e( 'Bestehende Produkte aktualisieren', 'wc-inventory-sync' ); ?></strong><p><?php esc_html_e( 'Vorhandene (gleiche SKU) mit Quelldaten überschreiben – inkl. Status. Standard aus.', 'wc-inventory-sync' ); ?></p></div>
								</div>
							</div>
							<div class="wcis-field">
								<label><?php esc_html_e( 'Zu übertragende Felder', 'wc-inventory-sync' ); ?></label>
								<div class="wcis-chips">
									<?php
									$wcis_fields   = is_array( $s['product_fields'] ) ? $s['product_fields'] : array();
									$wcis_all_flds = WCIS_Filter::product_field_labels();
									foreach ( $wcis_all_flds as $wcis_fk => $wcis_flabel ) :
										$wcis_forced = ( 'name' === $wcis_fk );
										?>
										<label class="wcis-chip">
											<input type="checkbox" name="product_fields[]" value="<?php echo esc_attr( $wcis_fk ); ?>"
												<?php checked( $wcis_forced || in_array( $wcis_fk, $wcis_fields, true ) ); ?>
												<?php disabled( $wcis_forced ); ?> />
											<span><?php echo esc_html( $wcis_flabel ); ?></span>
										</label>
									<?php endforeach; ?>
									<input type="hidden" name="product_fields[]" value="name" />
								</div>
								<small><?php esc_html_e( 'Nur ausgewählte Felder werden übertragen. Tipp: Haken bei „Preis" entfernen, damit jeder Shop eigene Preise behält.', 'wc-inventory-sync' ); ?></small>
							</div>
							<div class="wcis-field">
								<label for="wcis-taxmap"><?php esc_html_e( 'Steuerklassen-Zuordnung (Empfänger)', 'wc-inventory-sync' ); ?></label>
								<textarea id="wcis-taxmap" name="tax_class_map" rows="3" class="code" style="width:100%; font-family:ui-monospace,Menlo,Consolas,monospace;" placeholder="reduzierter-preis=reduced-rate"><?php echo esc_textarea( $s['tax_class_map'] ); ?></textarea>
								<small><?php esc_html_e( 'Übersetzt eingehende Steuerklassen-Slugs auf die hier vorhandenen. Eine Zuordnung pro Zeile, z. B. „reduzierter-preis=reduced-rate". Nötig, wenn zwei Shops für dieselbe Steuer (z. B. 7 %) unterschiedliche Slugs verwenden. Unbekannte, nicht zugeordnete Slugs werden übersprungen (Steuerklasse bleibt unverändert, statt auf Standard zu fallen).', 'wc-inventory-sync' ); ?></small>
							</div>
						</div>
					</div>
				</section>

				<!-- TAB: Sync-Filter -->
				<section class="wcis-tab" data-tab="filter">
					<div class="wcis-card">
						<div class="wcis-card-head"><h2><?php esc_html_e( 'Sync-Filter: Welche Produkte?', 'wc-inventory-sync' ); ?></h2>
							<p><?php esc_html_e( 'Legt fest, welche Produkte dieser Shop synchronisiert (Bestands- und Produkt-Sync). Ausgeschlossene werden nie verändert.', 'wc-inventory-sync' ); ?></p></div>
						<div class="wcis-card-body">
							<div class="wcis-field">
								<label><?php esc_html_e( 'Umfang', 'wc-inventory-sync' ); ?></label>
								<label class="wcis-radio"><input type="radio" name="filter_mode" value="all" <?php checked( $s['filter_mode'], 'all' ); ?> /> <span><?php esc_html_e( 'Alle Produkte synchronisieren', 'wc-inventory-sync' ); ?></span></label>
								<label class="wcis-radio"><input type="radio" name="filter_mode" value="selected" <?php checked( $s['filter_mode'], 'selected' ); ?> /> <span><?php esc_html_e( 'Nur ausgewählte (nach Kategorie, Marke oder Einzelprodukt)', 'wc-inventory-sync' ); ?></span></label>
							</div>
							<div class="wcis-grid">
								<div class="wcis-field">
									<label for="wcis-filter-cats"><?php esc_html_e( 'Kategorien', 'wc-inventory-sync' ); ?></label>
									<?php $wcis_selcats = array_map( 'intval', (array) $s['filter_categories'] ); ?>
									<select id="wcis-filter-cats" name="filter_categories[]" multiple="multiple" class="wc-enhanced-select" data-placeholder="<?php esc_attr_e( 'Kategorien wählen …', 'wc-inventory-sync' ); ?>">
										<?php
										$wcis_terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) );
										if ( ! is_wp_error( $wcis_terms ) ) {
											foreach ( $wcis_terms as $wcis_t ) {
												printf( '<option value="%d" %s>%s</option>', (int) $wcis_t->term_id, selected( in_array( (int) $wcis_t->term_id, $wcis_selcats, true ), true, false ), esc_html( $wcis_t->name ) );
											}
										}
										?>
									</select>
								</div>
								<div class="wcis-field">
									<label for="wcis-filter-brands"><?php esc_html_e( 'Marken', 'wc-inventory-sync' ); ?></label>
									<?php
									$wcis_btax  = WCIS_Filter::brand_taxonomy();
									$wcis_selbr = array_map( 'intval', (array) $s['filter_brands'] );
									if ( $wcis_btax ) :
										?>
										<select id="wcis-filter-brands" name="filter_brands[]" multiple="multiple" class="wc-enhanced-select" data-placeholder="<?php esc_attr_e( 'Marken wählen …', 'wc-inventory-sync' ); ?>">
											<?php
											$wcis_bterms = get_terms( array( 'taxonomy' => $wcis_btax, 'hide_empty' => false ) );
											if ( ! is_wp_error( $wcis_bterms ) ) {
												foreach ( $wcis_bterms as $wcis_bt ) {
													printf( '<option value="%d" %s>%s</option>', (int) $wcis_bt->term_id, selected( in_array( (int) $wcis_bt->term_id, $wcis_selbr, true ), true, false ), esc_html( $wcis_bt->name ) );
												}
											}
											?>
										</select>
										<small><?php echo esc_html( sprintf( __( 'Erkannte Marken-Taxonomie: %s.', 'wc-inventory-sync' ), $wcis_btax ) ); ?></small>
									<?php else : ?>
										<small><?php esc_html_e( 'Keine Marken-Taxonomie gefunden – Marken-Filter nicht verfügbar.', 'wc-inventory-sync' ); ?></small>
									<?php endif; ?>
								</div>
							</div>
							<div class="wcis-field">
								<label for="wcis-filter-include"><?php esc_html_e( 'Einzelne Produkte einschließen', 'wc-inventory-sync' ); ?></label>
								<select id="wcis-filter-include" name="filter_include_ids[]" multiple="multiple" class="wc-product-search" data-placeholder="<?php esc_attr_e( 'Produkte suchen …', 'wc-inventory-sync' ); ?>" data-action="woocommerce_json_search_products_and_variations">
									<?php
									foreach ( array_map( 'intval', (array) $s['filter_include_ids'] ) as $wcis_pid ) {
										$wcis_p = wc_get_product( $wcis_pid );
										if ( $wcis_p ) {
											printf( '<option value="%d" selected>%s</option>', (int) $wcis_pid, esc_html( wp_strip_all_tags( $wcis_p->get_formatted_name() ) ) );
										}
									}
									?>
								</select>
							</div>
							<div class="wcis-field">
								<label for="wcis-filter-exclude"><?php esc_html_e( 'Einzelne Produkte ausschließen', 'wc-inventory-sync' ); ?></label>
								<select id="wcis-filter-exclude" name="filter_exclude_ids[]" multiple="multiple" class="wc-product-search" data-placeholder="<?php esc_attr_e( 'Produkte suchen …', 'wc-inventory-sync' ); ?>" data-action="woocommerce_json_search_products_and_variations">
									<?php
									foreach ( array_map( 'intval', (array) $s['filter_exclude_ids'] ) as $wcis_pid ) {
										$wcis_p = wc_get_product( $wcis_pid );
										if ( $wcis_p ) {
											printf( '<option value="%d" selected>%s</option>', (int) $wcis_pid, esc_html( wp_strip_all_tags( $wcis_p->get_formatted_name() ) ) );
										}
									}
									?>
								</select>
								<small><?php esc_html_e( 'Diese Produkte werden nie synchronisiert – aus- und eingehend (harter Ausschluss).', 'wc-inventory-sync' ); ?></small>
							</div>
							<div class="wcis-field">
								<label for="wcis-filter-excl-cats"><?php esc_html_e( 'Kategorien ausschließen', 'wc-inventory-sync' ); ?></label>
								<?php $wcis_exclcats = array_map( 'intval', (array) $s['filter_exclude_categories'] ); ?>
								<select id="wcis-filter-excl-cats" name="filter_exclude_categories[]" multiple="multiple" class="wc-enhanced-select" data-placeholder="<?php esc_attr_e( 'Kategorien wählen …', 'wc-inventory-sync' ); ?>">
									<?php
									$wcis_terms2 = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) );
									if ( ! is_wp_error( $wcis_terms2 ) ) {
										foreach ( $wcis_terms2 as $wcis_t2 ) {
											printf( '<option value="%d" %s>%s</option>', (int) $wcis_t2->term_id, selected( in_array( (int) $wcis_t2->term_id, $wcis_exclcats, true ), true, false ), esc_html( $wcis_t2->name ) );
										}
									}
									?>
								</select>
								<small><?php esc_html_e( 'Harter Ausschluss – hat Vorrang vor allen Einschluss-Kriterien.', 'wc-inventory-sync' ); ?></small>
							</div>
							<div class="wcis-field">
								<button type="button" class="wcis-btn wcis-btn--ghost" id="wcis-filter-preview-btn"><?php esc_html_e( 'Vorschau: Umfang anzeigen', 'wc-inventory-sync' ); ?></button>
								<div id="wcis-filter-preview" class="wcis-preview" style="display:none;"></div>
							</div>
						</div>
					</div>
				</section>
			</form>

			<!-- TAB: Aktionen -->
			<section class="wcis-tab" data-tab="actions">
				<?php
				$wcis_job     = WCIS_Fullsync::state();
				$wcis_running = $wcis_job && 'running' === $wcis_job['status'];
				$wcis_pct     = WCIS_Fullsync::percent( $wcis_job );
				?>
				<div class="wcis-card">
					<div class="wcis-card-head"><h2><?php esc_html_e( 'Bestand: Voll-Synchronisation', 'wc-inventory-sync' ); ?></h2>
						<p><?php esc_html_e( 'Sendet den gesamten Bestand dieses Shops an alle verbundenen Shops. Idealerweise vom Hauptshop.', 'wc-inventory-sync' ); ?></p></div>
					<div class="wcis-card-body">
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="wcis-fullsync-form" class="wcis-actionrow">
							<input type="hidden" name="action" value="wcis_full_sync" />
							<?php wp_nonce_field( 'wcis_full_sync' ); ?>
							<button type="submit" class="wcis-btn wcis-btn--primary" id="wcis-full-sync"><?php esc_html_e( 'Voll-Synchronisation starten', 'wc-inventory-sync' ); ?></button>
							<button type="button" class="wcis-btn wcis-btn--ghost" id="wcis-full-sync-cancel" style="display:none;"><?php esc_html_e( 'Abbrechen', 'wc-inventory-sync' ); ?></button>
						</form>
						<div id="wcis-progress-wrap" class="wcis-progress-wrap" style="<?php echo $wcis_running ? '' : 'display:none;'; ?>">
							<div class="wcis-progress-bar"><div class="wcis-progress-fill" id="wcis-progress-fill" style="width:<?php echo esc_attr( $wcis_pct ); ?>%;"><span id="wcis-progress-label"><?php echo esc_html( $wcis_pct . '%' ); ?></span></div></div>
							<p class="wcis-progress-text" id="wcis-progress-text"><?php echo $wcis_running ? esc_html( sprintf( __( 'Läuft … %d %%', 'wc-inventory-sync' ), $wcis_pct ) ) : ''; ?></p>
						</div>
					</div>
				</div>

				<?php $wcis_pjob = WCIS_Product_Sync::bulk_state(); $wcis_prunning = $wcis_pjob && 'running' === $wcis_pjob['status']; $wcis_ppct = WCIS_Product_Sync::bulk_percent( $wcis_pjob ); ?>
				<div class="wcis-card">
					<div class="wcis-card-head"><h2><?php esc_html_e( 'Produkte: Massen-Übertragung', 'wc-inventory-sync' ); ?></h2>
						<p><?php esc_html_e( 'Überträgt alle Produkte 1:1 (inkl. Status). Ideal für die erste Befüllung neuer Shops.', 'wc-inventory-sync' ); ?></p></div>
					<div class="wcis-card-body">
						<form method="post" id="wcis-productsync-form" class="wcis-actionrow" onsubmit="return false;">
							<button type="submit" class="wcis-btn wcis-btn--primary" id="wcis-product-sync" <?php disabled( ! $s['product_sync_enabled'] ); ?>><?php esc_html_e( 'Alle Produkte übertragen', 'wc-inventory-sync' ); ?></button>
							<button type="button" class="wcis-btn wcis-btn--ghost" id="wcis-product-sync-cancel" style="display:none;"><?php esc_html_e( 'Abbrechen', 'wc-inventory-sync' ); ?></button>
							<?php if ( ! $s['product_sync_enabled'] ) : ?>
								<span class="wcis-hint"><?php esc_html_e( 'Zuerst „Produkt-Sync aktiv" einschalten und speichern.', 'wc-inventory-sync' ); ?></span>
							<?php endif; ?>
						</form>
						<div id="wcis-product-progress-wrap" class="wcis-progress-wrap" style="<?php echo $wcis_prunning ? '' : 'display:none;'; ?>">
							<div class="wcis-progress-bar"><div class="wcis-progress-fill" id="wcis-product-progress-fill" style="width:<?php echo esc_attr( $wcis_ppct ); ?>%;"><span id="wcis-product-progress-label"><?php echo esc_html( $wcis_ppct . '%' ); ?></span></div></div>
							<p class="wcis-progress-text" id="wcis-product-progress-text"></p>
						</div>
					</div>

					<?php
					$wcis_qjob     = WCIS_Product_Sync::pull_state();
					$wcis_qrunning = $wcis_qjob && 'running' === $wcis_qjob['status'];
					$wcis_qpct     = WCIS_Product_Sync::pull_percent( $wcis_qjob );
					?>
					<div class="wcis-card">
						<div class="wcis-card-head"><h2><?php esc_html_e( 'Produkte vom Hauptshop holen (Pull)', 'wc-inventory-sync' ); ?></h2>
							<p><?php echo esc_html( sprintf( __( 'Dieser Shop holt sich die Produkte des Hauptshops (%s) selbst – ideal für die erste Befüllung eines Neben-Shops, ohne dass der Hauptshop an alle verteilt.', 'wc-inventory-sync' ), untrailingslashit( (string) $s['master_url'] ) ) ); ?></p></div>
						<div class="wcis-card-body">
							<?php if ( $wcis_master ) : ?>
								<p class="wcis-hint"><?php esc_html_e( 'Dieser Shop ist selbst der Hauptshop – nutze oben „Alle Produkte übertragen".', 'wc-inventory-sync' ); ?></p>
							<?php else : ?>
								<form method="post" id="wcis-productpull-form" class="wcis-actionrow" onsubmit="return false;">
									<button type="submit" class="wcis-btn wcis-btn--primary" id="wcis-product-pull" <?php disabled( ! $s['product_sync_enabled'] ); ?>><?php esc_html_e( 'Produkte vom Hauptshop holen', 'wc-inventory-sync' ); ?></button>
									<button type="button" class="wcis-btn wcis-btn--ghost" id="wcis-product-pull-cancel" style="display:none;"><?php esc_html_e( 'Abbrechen', 'wc-inventory-sync' ); ?></button>
									<?php if ( ! $s['product_sync_enabled'] ) : ?>
										<span class="wcis-hint"><?php esc_html_e( 'Zuerst „Produkt-Sync aktiv" einschalten und speichern.', 'wc-inventory-sync' ); ?></span>
									<?php endif; ?>
								</form>
								<div id="wcis-pull-progress-wrap" class="wcis-progress-wrap" style="<?php echo $wcis_qrunning ? '' : 'display:none;'; ?>">
									<div class="wcis-progress-bar"><div class="wcis-progress-fill" id="wcis-pull-progress-fill" style="width:<?php echo esc_attr( $wcis_qpct ); ?>%;"><span id="wcis-pull-progress-label"><?php echo esc_html( $wcis_qpct . '%' ); ?></span></div></div>
									<p class="wcis-progress-text" id="wcis-pull-progress-text"></p>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>

				<div class="wcis-card">
					<div class="wcis-card-head"><h2><?php esc_html_e( 'Weitere Aktionen', 'wc-inventory-sync' ); ?></h2></div>
					<div class="wcis-card-body wcis-actionlist">
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="wcis-actionrow">
							<input type="hidden" name="action" value="wcis_push_config" />
							<?php wp_nonce_field( 'wcis_push_config' ); ?>
							<button type="submit" class="wcis-btn wcis-btn--ghost"><?php esc_html_e( 'Konfiguration an alle Shops verteilen', 'wc-inventory-sync' ); ?></button>
							<span class="wcis-hint"><?php esc_html_e( 'Verteilt Shop-Liste und Hauptshop-Auswahl (Secret & eigene URL bleiben unverändert).', 'wc-inventory-sync' ); ?></span>
						</form>
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="wcis-actionrow">
							<input type="hidden" name="action" value="wcis_reconcile_now" />
							<?php wp_nonce_field( 'wcis_reconcile_now' ); ?>
							<button type="submit" class="wcis-btn wcis-btn--ghost"><?php esc_html_e( 'Jetzt abgleichen', 'wc-inventory-sync' ); ?></button>
							<span class="wcis-hint"><?php esc_html_e( 'Vergleicht Bestände sofort und korrigiert Abweichungen.', 'wc-inventory-sync' ); ?></span>
						</form>
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="wcis-actionrow">
							<input type="hidden" name="action" value="wcis_retry_queue" />
							<?php wp_nonce_field( 'wcis_retry_queue' ); ?>
							<button type="submit" class="wcis-btn wcis-btn--ghost"><?php esc_html_e( 'Warteschlange jetzt verarbeiten', 'wc-inventory-sync' ); ?></button>
						</form>
					</div>
				</div>
			</section>

			<!-- TAB: Protokoll -->
			<section class="wcis-tab" data-tab="log">
				<div class="wcis-card">
					<div class="wcis-card-head">
						<h2><?php esc_html_e( 'Protokoll', 'wc-inventory-sync' ); ?></h2>
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
							<input type="hidden" name="action" value="wcis_clear_log" />
							<?php wp_nonce_field( 'wcis_clear_log' ); ?>
							<button type="submit" class="wcis-btn wcis-btn--ghost"><?php esc_html_e( 'Leeren', 'wc-inventory-sync' ); ?></button>
						</form>
					</div>
					<div class="wcis-card-body">
						<?php $wcis_logs = WCIS_Logger::recent( 80 ); ?>
						<div class="wcis-logwrap">
							<table class="wcis-log">
								<thead>
									<tr>
										<th style="width:160px;"><?php esc_html_e( 'Zeit (UTC)', 'wc-inventory-sync' ); ?></th>
										<th style="width:70px;"><?php esc_html_e( 'Ebene', 'wc-inventory-sync' ); ?></th>
										<th style="width:90px;"><?php esc_html_e( 'Richtung', 'wc-inventory-sync' ); ?></th>
										<th><?php esc_html_e( 'Nachricht', 'wc-inventory-sync' ); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php if ( empty( $wcis_logs ) ) : ?>
										<tr><td colspan="4" class="wcis-empty"><?php esc_html_e( 'Noch keine Einträge.', 'wc-inventory-sync' ); ?></td></tr>
									<?php else : ?>
										<?php foreach ( $wcis_logs as $wcis_l ) : ?>
											<tr>
												<td class="code"><?php echo esc_html( $wcis_l['created_at'] ); ?></td>
												<td><span class="wcis-lvl wcis-lvl-<?php echo esc_attr( $wcis_l['level'] ); ?>"><?php echo esc_html( $wcis_l['level'] ); ?></span></td>
												<td><?php echo esc_html( $wcis_l['direction'] ); ?></td>
												<td><?php echo esc_html( $wcis_l['message'] ); ?></td>
											</tr>
										<?php endforeach; ?>
									<?php endif; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</section>

		</div><!-- /content -->
	</div><!-- /shell -->
</div><!-- /app -->
</div>
