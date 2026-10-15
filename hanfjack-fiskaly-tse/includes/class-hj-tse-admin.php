<?php
defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce > TSE (fiskaly) settings page.
 */
class HJ_TSE_Admin {

	public static function init(): void {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ), 60 );
		add_action( 'admin_post_hj_tse_save', array( __CLASS__, 'save' ) );
		add_action( 'admin_post_hj_tse_setup', array( __CLASS__, 'setup' ) );
		add_action( 'admin_post_hj_tse_export', array( __CLASS__, 'export' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'meta_box' ) );
	}

	public static function menu(): void {
		add_submenu_page( 'woocommerce', 'TSE (fiskaly)', 'TSE (fiskaly)', 'manage_woocommerce', 'hj-tse', array( __CLASS__, 'page' ) );
	}

	private static function redirect( string $msg, string $type = 'updated' ): void {
		wp_safe_redirect( add_query_arg( array( 'page' => 'hj-tse', 'hj_msg' => rawurlencode( $msg ), 'hj_type' => $type ), admin_url( 'admin.php' ) ) );
		exit;
	}

	public static function save(): void {
		check_admin_referer( 'hj_tse_save' );
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( 'Keine Berechtigung.' );
		}
		$fields = array( 'enabled', 'api_key', 'api_secret', 'tss_id', 'client_id', 'client_serial', 'timeout_tx', 'timeout_admin', 'receipt_label', 'print_fields' );
		$values = array();
		foreach ( $fields as $f ) {
			if ( isset( $_POST[ $f ] ) ) {
				$values[ $f ] = sanitize_text_field( wp_unslash( $_POST[ $f ] ) );
			}
		}
		$values['enabled']      = isset( $_POST['enabled'] ) ? 'yes' : 'no';
		$values['print_fields'] = isset( $_POST['print_fields'] ) ? 'yes' : 'no';
		if ( isset( $values['api_secret'] ) && '' === $values['api_secret'] ) {
			unset( $values['api_secret'] ); // Keep stored secret when the field is left blank.
		}
		HJ_TSE_Settings::update( $values );
		self::redirect( 'Einstellungen gespeichert.' );
	}

	public static function setup(): void {
		check_admin_referer( 'hj_tse_setup' );
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( 'Keine Berechtigung.' );
		}
		try {
			$serial = (string) HJ_TSE_Settings::get( 'client_serial' );
			$res    = ( new HJ_TSE_Client() )->setup( $serial, array( 'shop' => get_bloginfo( 'name' ), 'site' => home_url(), 'pos' => 'WCPOS' ) );
			HJ_TSE_Settings::update(
				array(
					'tss_id'    => $res['tss_id'],
					'client_id' => $res['client_id'],
					'admin_pin' => $res['admin_pin'],
					'admin_puk' => $res['admin_puk'],
				)
			);
			self::redirect( 'TSS angelegt und initialisiert, Kasse registriert. TSE-Seriennummer: ' . $res['tss_serial'] );
		} catch ( HJ_TSE_Exception $e ) {
			self::redirect( 'Einrichtung fehlgeschlagen: ' . $e->getMessage(), 'error' );
		}
	}

	public static function export(): void {
		check_admin_referer( 'hj_tse_export' );
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( 'Keine Berechtigung.' );
		}
		$start = isset( $_POST['start_date'] ) ? sanitize_text_field( wp_unslash( $_POST['start_date'] ) ) : '';
		$end   = isset( $_POST['end_date'] ) ? sanitize_text_field( wp_unslash( $_POST['end_date'] ) ) : '';
		$query = array();
		if ( $start ) {
			$query['start_date'] = (string) strtotime( $start . ' 00:00:00' );
		}
		if ( $end ) {
			$query['end_date'] = (string) strtotime( $end . ' 23:59:59' );
		}
		try {
			$id = wp_generate_uuid4();
			( new HJ_TSE_Client() )->trigger_export( $id, $query );
			update_option( 'hj_tse_last_export', array( 'id' => $id, 'time' => time(), 'query' => $query ), false );
			self::redirect( 'TSE-Export angestoßen (ID ' . $id . '). Der Download erscheint unten, sobald fiskaly fertig ist.' );
		} catch ( HJ_TSE_Exception $e ) {
			self::redirect( 'Export fehlgeschlagen: ' . $e->getMessage(), 'error' );
		}
	}

	public static function page(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}
		$s      = HJ_TSE_Settings::all();
		$counts = HJ_TSE_Log::counts();
		$remote = null;
		$remote_error = '';
		if ( HJ_TSE_Settings::is_configured() ) {
			try {
				$client = new HJ_TSE_Client();
				$tss    = $client->get_tss( $s['tss_id'] );
				$cl     = $client->get_client( $s['tss_id'], $s['client_id'] );
				$remote = array( 'tss_state' => $tss['state'] ?? '?', 'tss_serial' => $tss['serial_number'] ?? '', 'client_state' => $cl['state'] ?? '?', 'env' => $tss['_env'] ?? '' );
			} catch ( HJ_TSE_Exception $e ) {
				$remote_error = $e->getMessage();
			}
		}
		$last_export = get_option( 'hj_tse_last_export' );
		$export_info = null;
		if ( is_array( $last_export ) && ! empty( $last_export['id'] ) && HJ_TSE_Settings::is_configured() ) {
			try {
				$export_info = ( new HJ_TSE_Client() )->get_export( $last_export['id'] );
			} catch ( HJ_TSE_Exception $e ) {
				$export_info = array( 'state' => 'FEHLER', 'error' => $e->getMessage() );
			}
		}
		if ( isset( $_GET['hj_msg'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$type = ( isset( $_GET['hj_type'] ) && 'error' === $_GET['hj_type'] ) ? 'notice-error' : 'notice-success'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			echo '<div class="notice ' . esc_attr( $type ) . '"><p>' . esc_html( rawurldecode( sanitize_text_field( wp_unslash( $_GET['hj_msg'] ) ) ) ) . '</p></div>'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}
		?>
		<div class="wrap">
			<h1>TSE (fiskaly) für die Ladenkasse</h1>
			<p>Umgebung: <strong><?php echo esc_html( HJ_TSE_Settings::environment() ); ?></strong>
				<?php if ( 'TEST' === HJ_TSE_Settings::environment() ) : ?>
					<span style="color:#b32d2e">(Testumgebung, Belege sind nicht rechtsgültig)</span>
				<?php endif; ?>
			</p>
			<?php if ( $remote ) : ?>
				<table class="widefat striped" style="max-width:900px">
					<tbody>
					<tr><th>TSS-Status</th><td><?php echo esc_html( $remote['tss_state'] ); ?> (<?php echo esc_html( $remote['env'] ); ?>)</td></tr>
					<tr><th>TSE-Seriennummer</th><td><code><?php echo esc_html( $remote['tss_serial'] ); ?></code></td></tr>
					<tr><th>Kasse (Client)</th><td><?php echo esc_html( $s['client_serial'] ); ?> – <?php echo esc_html( $remote['client_state'] ); ?></td></tr>
					<tr><th>Signierte Belege</th><td><?php echo (int) ( $counts['FINISHED'] ?? 0 ); ?> abgeschlossen, <?php echo (int) ( $counts['ACTIVE'] ?? 0 ); ?> offen, <?php echo (int) ( $counts['ERROR'] ?? 0 ); ?> Ausfälle</td></tr>
					</tbody>
				</table>
			<?php elseif ( $remote_error ) : ?>
				<div class="notice notice-error inline"><p>fiskaly nicht erreichbar: <?php echo esc_html( $remote_error ); ?></p></div>
			<?php endif; ?>

			<h2>Zugangsdaten</h2>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'hj_tse_save' ); ?>
				<input type="hidden" name="action" value="hj_tse_save" />
				<table class="form-table">
					<tr><th>TSE aktiv</th><td><label><input type="checkbox" name="enabled" <?php checked( 'yes', $s['enabled'] ); ?> /> Belege signieren</label></td></tr>
					<tr><th>fiskaly API-Key</th><td><input type="text" class="regular-text" name="api_key" value="<?php echo esc_attr( $s['api_key'] ); ?>" /></td></tr>
					<tr><th>fiskaly API-Secret</th><td><input type="password" class="regular-text" name="api_secret" value="" placeholder="<?php echo '' !== $s['api_secret'] ? 'gespeichert, zum Ändern neu eingeben' : ''; ?>" /></td></tr>
					<tr><th>Kassen-ID (Client-Seriennummer)</th><td><input type="text" class="regular-text" name="client_serial" value="<?php echo esc_attr( $s['client_serial'] ); ?>" /><p class="description">Erscheint auf jedem Bon und in der ELSTER-Meldung. Nach der Einrichtung nicht mehr ändern.</p></td></tr>
					<tr><th>TSS-ID</th><td><input type="text" class="regular-text" name="tss_id" value="<?php echo esc_attr( $s['tss_id'] ); ?>" /></td></tr>
					<tr><th>Client-ID</th><td><input type="text" class="regular-text" name="client_id" value="<?php echo esc_attr( $s['client_id'] ); ?>" /></td></tr>
					<tr><th>Timeout Signatur (s)</th><td><input type="number" name="timeout_tx" min="1" max="30" value="<?php echo (int) $s['timeout_tx']; ?>" /></td></tr>
					<tr><th>Timeout Verwaltung (s)</th><td><input type="number" name="timeout_admin" min="5" max="120" value="<?php echo (int) $s['timeout_admin']; ?>" /></td></tr>
					<tr><th>Belegbezeichnung</th><td><input type="text" name="receipt_label" value="<?php echo esc_attr( $s['receipt_label'] ); ?>" /></td></tr>
					<tr><th>Klartextfelder drucken</th><td><label><input type="checkbox" name="print_fields" <?php checked( 'yes', $s['print_fields'] ); ?> /> Zusätzlich zum QR-Code Signatur, Zeitformat und Algorithmus drucken</label></td></tr>
				</table>
				<?php submit_button( 'Speichern' ); ?>
			</form>

			<h2>Einrichtung</h2>
			<p>Legt bei fiskaly eine TSS an, initialisiert sie und registriert die Kasse mit der oben eingetragenen Kassen-ID. Nur einmal pro Kasse ausführen.</p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('Neue TSS anlegen? Eine bestehende Konfiguration wird überschrieben.');">
				<?php wp_nonce_field( 'hj_tse_setup' ); ?>
				<input type="hidden" name="action" value="hj_tse_setup" />
				<?php submit_button( '' === $s['tss_id'] ? 'TSS anlegen und Kasse registrieren' : 'Neue TSS anlegen (Vorsicht)', 'secondary' ); ?>
			</form>

			<h2>TSE-Export (TAR)</h2>
			<p>Für Kassen-Nachschau oder Betriebsprüfung. Ohne Datumsangabe wird alles exportiert.</p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'hj_tse_export' ); ?>
				<input type="hidden" name="action" value="hj_tse_export" />
				Von <input type="date" name="start_date" /> bis <input type="date" name="end_date" />
				<?php submit_button( 'Export anstoßen', 'secondary', 'submit', false ); ?>
			</form>
			<?php if ( $export_info ) : ?>
				<p>Letzter Export: Status <strong><?php echo esc_html( $export_info['state'] ?? '' ); ?></strong>
				<?php if ( 'COMPLETED' === ( $export_info['state'] ?? '' ) ) : ?>
					– Download über die REST-Schnittstelle: <code>GET /wp-json/hj-tse/v1/export/<?php echo esc_html( $last_export['id'] ); ?>?download=yes</code> (Datei landet unter wp-content/uploads/hj-tse-exports).
				<?php elseif ( ! empty( $export_info['error'] ) ) : ?>
					– <?php echo esc_html( is_array( $export_info['error'] ) ? wp_json_encode( $export_info['error'] ) : $export_info['error'] ); ?>
				<?php endif; ?>
				</p>
			<?php endif; ?>

			<h2>Letzte TSE-Vorgänge</h2>
			<table class="widefat striped">
				<thead><tr><th>Zeit (UTC)</th><th>Bestellung</th><th>Typ</th><th>Status</th><th>TX-Nr</th><th>Sig-Zähler</th><th>Betrag</th><th>Fehler</th></tr></thead>
				<tbody>
				<?php foreach ( HJ_TSE_Log::recent( 40 ) as $row ) : ?>
					<tr>
						<td><?php echo esc_html( $row['created_gmt'] ); ?></td>
						<td><a href="<?php echo esc_url( admin_url( 'post.php?post=' . (int) $row['order_id'] . '&action=edit' ) ); ?>">#<?php echo (int) $row['order_id']; ?></a><?php echo $row['refund_id'] ? ' (Storno ' . (int) $row['refund_id'] . ')' : ''; ?></td>
						<td><?php echo esc_html( $row['receipt_type'] ); ?></td>
						<td><?php echo esc_html( $row['state'] ); ?></td>
						<td><?php echo (int) $row['tx_number']; ?></td>
						<td><?php echo (int) $row['signature_counter']; ?></td>
						<td><?php echo esc_html( number_format( (float) $row['total'], 2, ',', '.' ) ); ?> €</td>
						<td><?php echo esc_html( (string) $row['error'] ); ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	public static function meta_box(): void {
		foreach ( array( 'shop_order', 'woocommerce_page_wc-orders' ) as $screen ) {
			add_meta_box( 'hj_tse_box', 'TSE-Signatur', array( __CLASS__, 'render_meta_box' ), $screen, 'side', 'default' );
		}
	}

	public static function render_meta_box( $post_or_order ): void {
		$order = $post_or_order instanceof WC_Order ? $post_or_order : wc_get_order( $post_or_order->ID ?? 0 );
		if ( ! $order instanceof WC_Order ) {
			return;
		}
		$state = (string) HJ_TSE_Orders::meta( $order, 'state' );
		if ( '' === $state ) {
			echo '<p>Keine TSE-Daten (keine Kassenbestellung).</p>';
			return;
		}
		echo '<p>Status: <strong>' . esc_html( $state ) . '</strong></p>';
		foreach ( HJ_TSE_Receipt::fields_for( $order ) as $f ) {
			echo '<p style="margin:2px 0;word-break:break-all"><small><strong>' . esc_html( $f['label'] ) . ':</strong> ' . esc_html( $f['value'] ) . '</small></p>';
		}
	}
}
