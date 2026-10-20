<?php
defined( 'ABSPATH' ) || exit;

class HJB_Admin {

	public static function init(): void {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ), 61 );
		foreach ( array( 'save', 'connect', 'disconnect', 'sync', 'assign', 'ignore' ) as $a ) {
			add_action( 'admin_post_hjb_' . $a, array( __CLASS__, $a ) );
		}
		add_action( 'admin_notices', array( __CLASS__, 'expiry_notice' ) );
	}

	public static function menu(): void {
		add_submenu_page( 'woocommerce', 'Zahlungsabgleich', 'Zahlungsabgleich', 'manage_woocommerce', 'hjb', array( __CLASS__, 'page' ) );
	}

	private static function guard( string $action ): void {
		check_admin_referer( 'hjb_' . $action );
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( 'Keine Berechtigung.' );
		}
	}

	private static function back( string $msg, string $type = 'updated' ): void {
		wp_safe_redirect( add_query_arg( array( 'page' => 'hjb', 'hjb_msg' => rawurlencode( $msg ), 'hjb_type' => $type ), admin_url( 'admin.php' ) ) );
		exit;
	}

	public static function save(): void {
		self::guard( 'save' );
		$v = array();
		foreach ( array( 'app_id', 'aspsp_name', 'aspsp_country', 'psu_type', 'gateways', 'statuses', 'order_days', 'sync_times', 'notify_email', 'iban_filter' ) as $k ) {
			if ( isset( $_POST[ $k ] ) ) {
				$v[ $k ] = sanitize_text_field( wp_unslash( $_POST[ $k ] ) );
			}
		}
		foreach ( array( 'enabled', 'auto_by_number', 'auto_by_name' ) as $k ) {
			$v[ $k ] = isset( $_POST[ $k ] ) ? 'yes' : 'no';
		}
		$key = isset( $_POST['private_key'] ) ? trim( (string) wp_unslash( $_POST['private_key'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- PEM must stay byte-exact.
		if ( '' !== $key ) {
			if ( false === openssl_pkey_get_private( $key ) ) {
				self::back( 'Der Private Key ist kein gültiger PEM-Schlüssel.', 'error' );
			}
			$v['private_key'] = $key;
		}
		HJB_Settings::update( $v );
		HJB_Sync::schedule();
		self::back( 'Einstellungen gespeichert.' );
	}

	public static function connect(): void {
		self::guard( 'connect' );
		try {
			$url = HJB_Sync::start_connect();
			wp_redirect( $url ); // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect -- external bank consent page.
			exit;
		} catch ( HJB_Exception $e ) {
			self::back( 'Verbinden fehlgeschlagen: ' . $e->getMessage(), 'error' );
		}
	}

	public static function disconnect(): void {
		self::guard( 'disconnect' );
		HJB_Sync::disconnect();
		self::back( 'Bankverbindung getrennt.' );
	}

	public static function sync(): void {
		self::guard( 'sync' );
		try {
			$days = isset( $_POST['days'] ) ? (int) $_POST['days'] : 0;
			$s    = HJB_Sync::run( HJB_Sync::psu_from_request(), $days );
			self::back( sprintf( 'Abruf fertig: %d Umsätze gelesen, %d neue Gutschriften, %d automatisch zugeordnet, %d zur Prüfung, %d ohne Treffer.%s', $s['fetched'] ?? 0, $s['new_credits'] ?? 0, $s['matched'] ?? 0, $s['review'] ?? 0, $s['unmatched'] ?? 0, ! empty( $s['errors'] ) ? ' Fehler: ' . implode( '; ', $s['errors'] ) : '' ), empty( $s['errors'] ) ? 'updated' : 'error' );
		} catch ( HJB_Exception $e ) {
			self::back( 'Abruf fehlgeschlagen: ' . $e->getMessage(), 'error' );
		}
	}

	public static function assign(): void {
		self::guard( 'assign' );
		$row   = isset( $_POST['row_id'] ) ? (int) $_POST['row_id'] : 0;
		$order = isset( $_POST['order_id'] ) ? (int) preg_replace( '/\D/', '', (string) wp_unslash( $_POST['order_id'] ) ) : 0;
		try {
			HJB_Sync::assign( $row, $order );
			self::back( 'Zahlung Bestellung #' . $order . ' zugeordnet, Bestellung ist als bezahlt markiert.' );
		} catch ( HJB_Exception $e ) {
			self::back( $e->getMessage(), 'error' );
		}
	}

	public static function ignore(): void {
		self::guard( 'ignore' );
		$row = isset( $_POST['row_id'] ) ? (int) $_POST['row_id'] : 0;
		HJB_Store::update( $row, array( 'status' => 'ignored' ) );
		self::back( 'Buchung ausgeblendet.' );
	}

	public static function expiry_notice(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) || ! HJB_Settings::get( 'session_id' ) ) {
			return;
		}
		$until = strtotime( (string) HJB_Settings::get( 'valid_until' ) );
		if ( $until && $until - time() < 14 * DAY_IN_SECONDS ) {
			echo '<div class="notice notice-warning"><p><strong>Zahlungsabgleich:</strong> Die Bankfreigabe endet am ' . esc_html( wp_date( 'd.m.Y', $until ) ) . '. <a href="' . esc_url( admin_url( 'admin.php?page=hjb' ) ) . '">Jetzt neu verbinden</a>.</p></div>';
		}
	}

	private static function form_open( string $action ): void {
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="display:inline">';
		wp_nonce_field( 'hjb_' . $action );
		echo '<input type="hidden" name="action" value="hjb_' . esc_attr( $action ) . '" />';
	}

	public static function page(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}
		$s = HJB_Settings::all();
		if ( isset( $_GET['hjb_msg'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$cls = ( isset( $_GET['hjb_type'] ) && 'error' === $_GET['hjb_type'] ) ? 'notice-error' : 'notice-success'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			echo '<div class="notice ' . esc_attr( $cls ) . '"><p>' . esc_html( rawurldecode( sanitize_text_field( wp_unslash( $_GET['hjb_msg'] ) ) ) ) . '</p></div>'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}
		$connected = HJB_Settings::is_connected();
		$last      = json_decode( (string) $s['last_sync_result'], true ) ?: array();
		$next      = wp_next_scheduled( 'hjb_sync' );
		$open      = HJB_Sync::open_orders();
		?>
		<div class="wrap">
			<h1>Zahlungsabgleich Vorkasse</h1>
			<p>Liest Gutschriften vom Geschäftskonto über PSD2 (Enable Banking) und markiert passende Vorkasse-Bestellungen automatisch als bezahlt. Der Kunde erhält dabei die normale „Zahlung eingegangen“-Mail.</p>

			<table class="widefat striped" style="max-width:900px"><tbody>
				<tr><th style="width:220px">Bankverbindung</th><td>
					<?php if ( $connected ) : ?>
						<strong style="color:#008a20">verbunden</strong> –
						<?php echo esc_html( implode( ', ', array_map( static fn( $a ) => trim( ( $a['name'] ? $a['name'] . ' ' : '' ) . HJB_Settings::mask_iban( (string) $a['iban'] ) ), (array) $s['accounts'] ) ) ); ?>,
						Freigabe gültig bis <strong><?php echo esc_html( $s['valid_until'] ? wp_date( 'd.m.Y', strtotime( $s['valid_until'] ) ) : '?' ); ?></strong>
					<?php else : ?>
						<strong style="color:#b32d2e">nicht verbunden</strong>
					<?php endif; ?>
				</td></tr>
				<tr><th>Letzter Abruf</th><td><?php echo $s['last_sync'] ? esc_html( wp_date( 'd.m.Y H:i', (int) $s['last_sync'] ) ) . ' – ' . esc_html( sprintf( '%d Gutschriften neu, %d zugeordnet, %d zur Prüfung', $last['new_credits'] ?? 0, $last['matched'] ?? 0, $last['review'] ?? 0 ) ) : 'noch nie'; ?><?php echo ! empty( $last['errors'] ) ? ' <span style="color:#b32d2e">' . esc_html( implode( '; ', $last['errors'] ) ) . '</span>' : ''; ?></td></tr>
				<tr><th>Nächster automatischer Abruf</th><td><?php echo $next ? esc_html( wp_date( 'd.m.Y H:i', $next ) ) : '–'; ?> (max. 4× täglich, PSD2-Grenze)</td></tr>
				<tr><th>Offene Vorkasse-Bestellungen</th><td><?php echo count( $open ); ?></td></tr>
			</tbody></table>

			<p style="margin-top:12px">
				<?php self::form_open( 'connect' ); ?><?php submit_button( $connected ? 'Neu verbinden (Freigabe verlängern)' : 'Mit Bank verbinden', 'primary', 'submit', false ); ?></form>
				<?php if ( $connected ) : ?>
					<?php self::form_open( 'sync' ); ?> <select name="days"><option value="0">seit letztem Abruf</option><option value="7">letzte 7 Tage</option><option value="30">letzte 30 Tage</option><option value="90">letzte 90 Tage</option></select> <?php submit_button( 'Jetzt abrufen', 'secondary', 'submit', false ); ?></form>
					<?php self::form_open( 'disconnect' ); ?> <?php submit_button( 'Trennen', 'delete', 'submit', false, array( 'onclick' => "return confirm('Bankverbindung trennen?')" ) ); ?></form>
				<?php endif; ?>
			</p>

			<?php $review = HJB_Store::by_status( array( 'review', 'unmatched' ), 60 ); ?>
			<h2>Zur Prüfung (<?php echo count( $review ); ?>)</h2>
			<?php if ( ! $review ) : ?>
				<p>Keine offenen Zahlungseingänge.</p>
			<?php else : ?>
				<table class="widefat striped"><thead><tr><th>Datum</th><th>Betrag</th><th>Auftraggeber</th><th>Verwendungszweck</th><th>Hinweis</th><th>Zuordnen</th></tr></thead><tbody>
				<?php foreach ( $review as $r ) : ?>
					<tr>
						<td><?php echo esc_html( $r['booking_date'] ? wp_date( 'd.m.Y', strtotime( $r['booking_date'] ) ) : '' ); ?></td>
						<td><strong><?php echo esc_html( number_format( (float) $r['amount'], 2, ',', '.' ) ); ?> €</strong></td>
						<td><?php echo esc_html( $r['debtor_name'] ); ?></td>
						<td><?php echo esc_html( $r['remittance'] ); ?></td>
						<td><?php echo esc_html( $r['reason'] ); ?>
							<?php foreach ( array_filter( explode( ',', (string) $r['suggestions'] ) ) as $sid ) : $so = wc_get_order( (int) $sid ); ?>
								<?php if ( $so ) : ?><br><a href="<?php echo esc_url( $so->get_edit_order_url() ); ?>">#<?php echo esc_html( $so->get_order_number() ); ?></a> <?php echo esc_html( number_format( (float) $so->get_total(), 2, ',', '.' ) ); ?> € – <?php echo esc_html( $so->get_formatted_billing_full_name() ); ?><?php endif; ?>
							<?php endforeach; ?>
						</td>
						<td style="white-space:nowrap">
							<?php self::form_open( 'assign' ); ?>
								<input type="hidden" name="row_id" value="<?php echo (int) $r['id']; ?>" />
								<input type="text" name="order_id" size="8" placeholder="Best.-Nr." value="<?php echo esc_attr( (string) ( explode( ',', (string) $r['suggestions'] )[0] ?? '' ) ); ?>" />
								<?php submit_button( 'Bezahlt', 'primary small', 'submit', false ); ?>
							</form>
							<?php self::form_open( 'ignore' ); ?>
								<input type="hidden" name="row_id" value="<?php echo (int) $r['id']; ?>" />
								<?php submit_button( 'Ignorieren', 'small', 'submit', false ); ?>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody></table>
			<?php endif; ?>

			<?php $done = HJB_Store::by_status( array( 'matched' ), 30 ); ?>
			<h2>Zuletzt zugeordnet</h2>
			<table class="widefat striped"><thead><tr><th>Datum</th><th>Betrag</th><th>Auftraggeber</th><th>Bestellung</th><th>Zuordnung</th></tr></thead><tbody>
			<?php foreach ( $done as $r ) : $o = wc_get_order( (int) $r['order_id'] ); ?>
				<tr>
					<td><?php echo esc_html( $r['booking_date'] ? wp_date( 'd.m.Y', strtotime( $r['booking_date'] ) ) : '' ); ?></td>
					<td><?php echo esc_html( number_format( (float) $r['amount'], 2, ',', '.' ) ); ?> €</td>
					<td><?php echo esc_html( $r['debtor_name'] ); ?></td>
					<td><?php echo $o ? '<a href="' . esc_url( $o->get_edit_order_url() ) . '">#' . esc_html( $o->get_order_number() ) . '</a>' : '#' . (int) $r['order_id']; ?></td>
					<td><?php echo esc_html( $r['reason'] ); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody></table>

			<h2>Einstellungen</h2>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'hjb_save' ); ?>
				<input type="hidden" name="action" value="hjb_save" />
				<table class="form-table">
					<tr><th>Aktiv</th><td><label><input type="checkbox" name="enabled" <?php checked( 'yes', $s['enabled'] ); ?> /> automatisch abrufen und zuordnen</label></td></tr>
					<tr><th>Enable Banking Application-ID</th><td><input type="text" class="regular-text" name="app_id" value="<?php echo esc_attr( $s['app_id'] ); ?>" /></td></tr>
					<tr><th>Private Key (.pem)</th><td><textarea name="private_key" rows="4" cols="70" placeholder="<?php echo '' !== HJB_Settings::private_key() ? 'gespeichert – zum Ersetzen neuen Schlüssel einfügen' : '-----BEGIN PRIVATE KEY----- …'; ?>"></textarea><p class="description">Inhalt der beim Registrieren heruntergeladenen .pem-Datei. Alternativ in wp-config.php: <code>define( 'HJB_PRIVATE_KEY_PATH', '/pfad/außerhalb/webroot/key.pem' );</code></p></td></tr>
					<tr><th>Redirect-URL für Enable Banking</th><td><code><?php echo esc_html( HJB_Sync::callback_url() ); ?></code><p class="description">Genau so in der Enable-Banking-App als erlaubte Redirect-URL eintragen.</p></td></tr>
					<tr><th>Bank / Land</th><td><input type="text" name="aspsp_name" value="<?php echo esc_attr( $s['aspsp_name'] ); ?>" /> <input type="text" name="aspsp_country" size="3" value="<?php echo esc_attr( $s['aspsp_country'] ); ?>" /></td></tr>
					<tr><th>Kontotyp</th><td><select name="psu_type"><option value="business" <?php selected( 'business', $s['psu_type'] ); ?>>Geschäftskonto</option><option value="personal" <?php selected( 'personal', $s['psu_type'] ); ?>>Privatkonto</option></select></td></tr>
					<tr><th>Nur Konto mit IBAN</th><td><input type="text" class="regular-text" name="iban_filter" value="<?php echo esc_attr( $s['iban_filter'] ); ?>" placeholder="leer = alle freigegebenen Konten" /></td></tr>
					<tr><th>Zahlarten</th><td><input type="text" name="gateways" value="<?php echo esc_attr( $s['gateways'] ); ?>" /> <span class="description">Gateway-IDs, kommagetrennt (Vorkasse = bacs)</span></td></tr>
					<tr><th>Bestellstatus</th><td><input type="text" name="statuses" value="<?php echo esc_attr( $s['statuses'] ); ?>" /> <span class="description">kommagetrennt</span></td></tr>
					<tr><th>Bestellungen der letzten</th><td><input type="number" name="order_days" min="1" max="365" value="<?php echo (int) $s['order_days']; ?>" /> Tage berücksichtigen</td></tr>
					<tr><th>Abrufzeiten</th><td><input type="text" name="sync_times" value="<?php echo esc_attr( $s['sync_times'] ); ?>" /> <span class="description">max. 4 Uhrzeiten, kommagetrennt</span></td></tr>
					<tr><th>Automatisch bezahlt setzen</th><td>
						<label><input type="checkbox" name="auto_by_number" <?php checked( 'yes', $s['auto_by_number'] ); ?> /> wenn Bestellnummer im Verwendungszweck und Betrag exakt stimmt</label><br>
						<label><input type="checkbox" name="auto_by_name" <?php checked( 'yes', $s['auto_by_name'] ); ?> /> wenn Betrag exakt und Nachname des Zahlers passt (nur bei genau einem Treffer)</label>
					</td></tr>
					<tr><th>Benachrichtigung an</th><td><input type="email" class="regular-text" name="notify_email" value="<?php echo esc_attr( $s['notify_email'] ); ?>" placeholder="<?php echo esc_attr( (string) get_option( 'admin_email' ) ); ?>" /></td></tr>
				</table>
				<?php submit_button( 'Speichern' ); ?>
			</form>
		</div>
		<?php
	}
}
