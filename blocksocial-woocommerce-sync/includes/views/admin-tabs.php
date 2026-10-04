<?php
/**
 * View: zusätzliche Reiter des Admin-Plugins – Partner, Shopify, Preisregeln.
 * Wird von settings-page.php eingebunden (außerhalb des Haupt-Formulars).
 *
 * @package BlockSocial_WooCommerce_Sync
 * @var array $s Einstellungen.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wcis_is_master = WCIS_Settings::is_master();
$wcis_show_code = isset( $_GET['wcis_show_code'] ) ? sanitize_key( wp_unslash( $_GET['wcis_show_code'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
?>

<!-- TAB: Partner -->
<section class="wcis-tab" data-tab="partners">
	<?php if ( ! $wcis_is_master ) : ?>
		<div class="wcis-card"><div class="wcis-card-body">
			<p class="wcis-hint"><?php esc_html_e( 'Partnershops werden auf dem Hauptshop verwaltet. Dieser Shop ist ein Neben-Shop.', 'blocksocial-woocommerce-sync' ); ?></p>
		</div></div>
	<?php else : ?>

		<?php
		$wcis_new = $wcis_show_code ? WCIS_Partners::get( $wcis_show_code ) : null;
		if ( $wcis_new ) :
			?>
			<div class="wcis-card wcis-card--highlight">
				<div class="wcis-card-head"><h2><?php echo esc_html( sprintf( __( 'Verbindungscode für „%s"', 'blocksocial-woocommerce-sync' ), $wcis_new['name'] ) ); ?></h2>
					<p><?php esc_html_e( 'Diesen Code an den Partner geben. Er installiert das Partner-Plugin und fügt den Code unter WooCommerce → BlockSocial Partner → Verbindung ein. Der Code enthält einen geheimen Schlüssel – wie ein Passwort behandeln.', 'blocksocial-woocommerce-sync' ); ?></p></div>
				<div class="wcis-card-body">
					<textarea class="wcis-code" id="wcis-new-code" rows="3" readonly><?php echo esc_textarea( WCIS_Partners::connection_code( $wcis_new ) ); ?></textarea>
					<p><button type="button" class="wcis-btn wcis-btn--primary wcis-copy" data-target="#wcis-new-code"><?php esc_html_e( 'Code kopieren', 'blocksocial-woocommerce-sync' ); ?></button></p>
				</div>
			</div>
		<?php endif; ?>

		<div class="wcis-card">
			<div class="wcis-card-head"><h2><?php esc_html_e( 'Partnershops', 'blocksocial-woocommerce-sync' ); ?></h2>
				<p><?php esc_html_e( 'Jeder Partner hat einen eigenen Zugangsschlüssel und kann nur mit diesem Hauptshop sprechen: Er erhält Produkte und Bestände, meldet seine Verkäufe – kann aber weder Produkte, Preise noch Einstellungen des Verbunds verändern.', 'blocksocial-woocommerce-sync' ); ?></p></div>
			<div class="wcis-card-body">
				<?php $wcis_partners = WCIS_Partners::all(); ?>
				<?php if ( empty( $wcis_partners ) ) : ?>
					<p class="wcis-empty"><?php esc_html_e( 'Noch keine Partner angelegt.', 'blocksocial-woocommerce-sync' ); ?></p>
				<?php else : ?>
					<div class="wcis-logwrap">
						<table class="wcis-log wcis-table">
							<thead><tr>
								<th><?php esc_html_e( 'Partner', 'blocksocial-woocommerce-sync' ); ?></th>
								<th><?php esc_html_e( 'Status', 'blocksocial-woocommerce-sync' ); ?></th>
								<th><?php esc_html_e( 'Letzter Kontakt', 'blocksocial-woocommerce-sync' ); ?></th>
								<th><?php esc_html_e( 'Sortiment', 'blocksocial-woocommerce-sync' ); ?></th>
								<th><?php esc_html_e( 'Aktionen', 'blocksocial-woocommerce-sync' ); ?></th>
							</tr></thead>
							<tbody>
							<?php foreach ( $wcis_partners as $wcis_p ) : ?>
								<tr>
									<td><strong><?php echo esc_html( $wcis_p['name'] ); ?></strong><br /><code><?php echo esc_html( $wcis_p['url'] ); ?></code></td>
									<td>
										<?php if ( ! empty( $wcis_p['active'] ) ) : ?>
											<span class="wcis-lvl wcis-lvl-info"><?php esc_html_e( 'aktiv', 'blocksocial-woocommerce-sync' ); ?></span>
										<?php else : ?>
											<span class="wcis-lvl wcis-lvl-error"><?php esc_html_e( 'gesperrt', 'blocksocial-woocommerce-sync' ); ?></span>
										<?php endif; ?>
									</td>
									<td>
										<?php
										if ( ! empty( $wcis_p['last_seen'] ) ) {
											echo esc_html( sprintf( __( 'vor %s', 'blocksocial-woocommerce-sync' ), human_time_diff( (int) $wcis_p['last_seen'], time() ) ) );
											if ( ! empty( $wcis_p['last_version'] ) ) {
												echo '<br /><small>v' . esc_html( $wcis_p['last_version'] ) . '</small>';
											}
										} else {
											esc_html_e( 'noch nie', 'blocksocial-woocommerce-sync' );
										}
										?>
									</td>
									<td><?php echo esc_html( WCIS_Partners::managed_config( $wcis_p )['scope_label'] ); ?></td>
									<td class="wcis-partner-actions">
										<button type="button" class="wcis-btn wcis-btn--ghost wcis-partner-test" data-key="<?php echo esc_attr( $wcis_p['key'] ); ?>"><?php esc_html_e( 'Testen', 'blocksocial-woocommerce-sync' ); ?></button>
										<a class="wcis-btn wcis-btn--ghost" href="<?php echo esc_url( add_query_arg( array( 'page' => WCIS_Admin::SLUG, 'wcis_tab' => 'partners', 'wcis_show_code' => $wcis_p['key'] ), admin_url( 'admin.php' ) ) ); ?>"><?php esc_html_e( 'Code anzeigen', 'blocksocial-woocommerce-sync' ); ?></a>
										<?php
										$wcis_actions = array(
											( ! empty( $wcis_p['active'] ) ? 'block' : 'unblock' ) => ( ! empty( $wcis_p['active'] ) ? __( 'Sperren', 'blocksocial-woocommerce-sync' ) : __( 'Entsperren', 'blocksocial-woocommerce-sync' ) ),
											'rotate' => __( 'Neuer Schlüssel', 'blocksocial-woocommerce-sync' ),
											'delete' => __( 'Löschen', 'blocksocial-woocommerce-sync' ),
										);
										$wcis_confirm = array(
											'rotate' => __( 'Neuen Schlüssel erzeugen? Der bisherige Verbindungscode wird sofort ungültig – der Partner muss den neuen Code eingeben.', 'blocksocial-woocommerce-sync' ),
											'delete' => __( 'Partner endgültig löschen? Er erhält danach keine Daten mehr.', 'blocksocial-woocommerce-sync' ),
											'block'  => __( 'Partner sperren? Er kann bis zur Entsperrung weder Daten abrufen noch Verkäufe melden.', 'blocksocial-woocommerce-sync' ),
										);
										foreach ( $wcis_actions as $wcis_do => $wcis_label ) :
											?>
											<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="wcis-inline-form" <?php echo isset( $wcis_confirm[ $wcis_do ] ) ? 'data-confirm="' . esc_attr( $wcis_confirm[ $wcis_do ] ) . '"' : ''; ?>>
												<input type="hidden" name="action" value="wcis_partner_action" />
												<input type="hidden" name="partner_key" value="<?php echo esc_attr( $wcis_p['key'] ); ?>" />
												<input type="hidden" name="partner_do" value="<?php echo esc_attr( $wcis_do ); ?>" />
												<?php wp_nonce_field( 'wcis_partner_action' ); ?>
												<button type="submit" class="wcis-btn wcis-btn--ghost<?php echo 'delete' === $wcis_do ? ' wcis-btn--danger' : ''; ?>"><?php echo esc_html( $wcis_label ); ?></button>
											</form>
										<?php endforeach; ?>
										<span class="wcis-test-result"></span>
										<details class="wcis-details">
											<summary><?php esc_html_e( 'Bearbeiten', 'blocksocial-woocommerce-sync' ); ?></summary>
											<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
												<input type="hidden" name="action" value="wcis_partner_update" />
												<input type="hidden" name="partner_key" value="<?php echo esc_attr( $wcis_p['key'] ); ?>" />
												<?php wp_nonce_field( 'wcis_partner_update' ); ?>
												<div class="wcis-field">
													<label><?php esc_html_e( 'Name', 'blocksocial-woocommerce-sync' ); ?></label>
													<input type="text" name="partner_name" value="<?php echo esc_attr( $wcis_p['name'] ); ?>" />
												</div>
												<div class="wcis-field">
													<label><?php esc_html_e( 'Sortiment', 'blocksocial-woocommerce-sync' ); ?></label>
													<label class="wcis-radio"><input type="radio" name="partner_scope" value="all" <?php checked( $wcis_p['scope'], 'all' ); ?> /> <span><?php esc_html_e( 'Gesamtes Sortiment', 'blocksocial-woocommerce-sync' ); ?></span></label>
													<label class="wcis-radio"><input type="radio" name="partner_scope" value="categories" <?php checked( $wcis_p['scope'], 'categories' ); ?> /> <span><?php esc_html_e( 'Nur diese Kategorien (inkl. Unterkategorien):', 'blocksocial-woocommerce-sync' ); ?></span></label>
													<?php WCIS_View::category_multiselect( 'partner_categories', $wcis_p['categories'] ); ?>
												</div>
												<button type="submit" class="wcis-btn wcis-btn--primary"><?php esc_html_e( 'Speichern', 'blocksocial-woocommerce-sync' ); ?></button>
											</form>
										</details>
									</td>
								</tr>
							<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<div class="wcis-card">
			<div class="wcis-card-head"><h2><?php esc_html_e( 'Partner hinzufügen', 'blocksocial-woocommerce-sync' ); ?></h2></div>
			<div class="wcis-card-body">
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="wcis_partner_add" />
					<?php wp_nonce_field( 'wcis_partner_add' ); ?>
					<div class="wcis-grid">
						<div class="wcis-field">
							<label for="wcis-partner-name"><?php esc_html_e( 'Name des Partners', 'blocksocial-woocommerce-sync' ); ?></label>
							<input type="text" id="wcis-partner-name" name="partner_name" placeholder="<?php esc_attr_e( 'z. B. Growshop Müller', 'blocksocial-woocommerce-sync' ); ?>" />
						</div>
						<div class="wcis-field">
							<label for="wcis-partner-url"><?php esc_html_e( 'Shop-URL des Partners', 'blocksocial-woocommerce-sync' ); ?></label>
							<input type="url" id="wcis-partner-url" class="code" name="partner_url" placeholder="https://partner-shop.de" required />
						</div>
					</div>
					<div class="wcis-field">
						<label><?php esc_html_e( 'Sortiment', 'blocksocial-woocommerce-sync' ); ?></label>
						<label class="wcis-radio"><input type="radio" name="partner_scope" value="all" checked /> <span><?php esc_html_e( 'Gesamtes Sortiment', 'blocksocial-woocommerce-sync' ); ?></span></label>
						<label class="wcis-radio"><input type="radio" name="partner_scope" value="categories" /> <span><?php esc_html_e( 'Nur diese Kategorien (inkl. Unterkategorien):', 'blocksocial-woocommerce-sync' ); ?></span></label>
						<?php WCIS_View::category_multiselect( 'partner_categories', array(), 'wcis-partner-cats' ); ?>
					</div>
					<button type="submit" class="wcis-btn wcis-btn--primary"><?php esc_html_e( 'Partner anlegen & Verbindungscode erzeugen', 'blocksocial-woocommerce-sync' ); ?></button>
				</form>
			</div>
		</div>

		<?php $wcis_pol = WCIS_Partners::policy(); ?>
		<div class="wcis-card">
			<div class="wcis-card-head"><h2><?php esc_html_e( 'Vorgaben für alle Partner', 'blocksocial-woocommerce-sync' ); ?></h2>
				<p><?php esc_html_e( 'Diese Regeln gelten in jedem Partnershop und können dort nicht geändert werden.', 'blocksocial-woocommerce-sync' ); ?></p></div>
			<div class="wcis-card-body">
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="wcis_partner_policy" />
					<?php wp_nonce_field( 'wcis_partner_policy' ); ?>
					<?php
					$wcis_switches = array(
						'update_existing'     => array( __( 'Produktdaten aktuell halten', 'blocksocial-woocommerce-sync' ), __( 'Texte, Bilder-Zuordnung, Pflichtangaben (Grundpreis, Hersteller, EAN …) werden bei Änderungen im Hauptshop beim Partner überschrieben. Empfohlen – so bleiben rechtliche Angaben korrekt.', 'blocksocial-woocommerce-sync' ) ),
						'update_prices'       => array( __( 'Preisänderungen übernehmen', 'blocksocial-woocommerce-sync' ), __( 'Neue Preise des Hauptshops werden beim Partner übernommen (seine Preisregeln werden danach automatisch wieder angewendet).', 'blocksocial-woocommerce-sync' ) ),
						'images'              => array( __( 'Bilder übertragen', 'blocksocial-woocommerce-sync' ), __( 'Produktbilder beim Neuanlegen mitliefern.', 'blocksocial-woocommerce-sync' ) ),
						'sync_status'         => array( __( 'Lagerstatus übertragen', 'blocksocial-woocommerce-sync' ), __( 'Auch für Produkte ohne Mengenverwaltung.', 'blocksocial-woocommerce-sync' ) ),
						'allow_price_rules'   => array( __( 'Eigene Preisregeln erlauben', 'blocksocial-woocommerce-sync' ), __( 'Partner dürfen Auf-/Abschläge in % festlegen (im unten festgelegten Rahmen).', 'blocksocial-woocommerce-sync' ) ),
						'stock_decrease_only' => array( __( 'Partner dürfen Bestand nur verringern', 'blocksocial-woocommerce-sync' ), __( 'Gemeldete Verkäufe reduzieren den Bestand; Erhöhungen durch Partner (z. B. Stornos) werden ignoriert. Schützt vor Fehlbedienung und Manipulation.', 'blocksocial-woocommerce-sync' ) ),
					);
					foreach ( $wcis_switches as $wcis_k => $wcis_l ) :
						?>
						<div class="wcis-field wcis-field--switch">
							<div class="wcis-field-main">
								<label class="wcis-switch"><input type="checkbox" name="policy_<?php echo esc_attr( $wcis_k ); ?>" value="1" <?php checked( ! empty( $wcis_pol[ $wcis_k ] ) ); ?> /><span class="wcis-slider"></span></label>
								<div><strong><?php echo esc_html( $wcis_l[0] ); ?></strong><p><?php echo esc_html( $wcis_l[1] ); ?></p></div>
							</div>
						</div>
					<?php endforeach; ?>
					<div class="wcis-grid">
						<div class="wcis-field">
							<label><?php esc_html_e( 'Preisregeln: Minimum', 'blocksocial-woocommerce-sync' ); ?></label>
							<span class="wcis-pct"><input type="number" step="0.01" min="-99" max="1000" name="policy_price_min" value="<?php echo esc_attr( $wcis_pol['price_min'] ); ?>" /> %</span>
							<small><?php esc_html_e( 'z. B. 0 = Partner dürfen nicht unter den Preis des Hauptshops gehen.', 'blocksocial-woocommerce-sync' ); ?></small>
						</div>
						<div class="wcis-field">
							<label><?php esc_html_e( 'Preisregeln: Maximum', 'blocksocial-woocommerce-sync' ); ?></label>
							<span class="wcis-pct"><input type="number" step="0.01" min="-99" max="1000" name="policy_price_max" value="<?php echo esc_attr( $wcis_pol['price_max'] ); ?>" /> %</span>
						</div>
					</div>
					<button type="submit" class="wcis-btn wcis-btn--primary"><?php esc_html_e( 'Vorgaben speichern & an alle Partner verteilen', 'blocksocial-woocommerce-sync' ); ?></button>
				</form>
			</div>
		</div>
	<?php endif; ?>
</section>

<!-- TAB: Shopify -->
<section class="wcis-tab" data-tab="shopify">
	<?php if ( ! $wcis_is_master ) : ?>
		<div class="wcis-card"><div class="wcis-card-body">
			<p class="wcis-hint"><?php esc_html_e( 'Shopify-Shops werden auf dem Hauptshop angebunden. Dieser Shop ist ein Neben-Shop.', 'blocksocial-woocommerce-sync' ); ?></p>
		</div></div>
	<?php else : ?>
		<?php
		$wcis_stores = WCIS_Shopify::all();

		/**
		 * Formularfelder eines Shopify-Shops.
		 *
		 * @param array $st Shop.
		 */
		$wcis_store_form = static function ( array $st ) {
			$is_new = '' === $st['id'];
			?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="wcis-shopify-form">
				<input type="hidden" name="action" value="wcis_shopify_save" />
				<input type="hidden" name="shopify_id" value="<?php echo esc_attr( $st['id'] ); ?>" />
				<?php wp_nonce_field( 'wcis_shopify_save' ); ?>
				<div class="wcis-grid">
					<div class="wcis-field">
						<label><?php esc_html_e( 'Name', 'blocksocial-woocommerce-sync' ); ?></label>
						<input type="text" name="shopify_name" value="<?php echo esc_attr( $st['name'] ); ?>" placeholder="<?php esc_attr_e( 'z. B. Shopify Partner XY', 'blocksocial-woocommerce-sync' ); ?>" />
					</div>
					<div class="wcis-field">
						<label><?php esc_html_e( 'Shopify-Adresse', 'blocksocial-woocommerce-sync' ); ?></label>
						<input type="text" class="code" name="shopify_domain" value="<?php echo esc_attr( $st['domain'] ); ?>" placeholder="meinshop.myshopify.com" required />
					</div>
				</div>
				<div class="wcis-field">
					<label><?php esc_html_e( 'Zugang', 'blocksocial-woocommerce-sync' ); ?></label>
					<label class="wcis-radio"><input type="radio" name="shopify_auth" value="client" class="wcis-auth-toggle" <?php checked( $st['auth'], 'client' ); ?> /> <span><?php esc_html_e( 'App aus dem Shopify Dev Dashboard (Client-ID + Client-Secret) – empfohlen', 'blocksocial-woocommerce-sync' ); ?></span></label>
					<label class="wcis-radio"><input type="radio" name="shopify_auth" value="token" class="wcis-auth-toggle" <?php checked( $st['auth'], 'token' ); ?> /> <span><?php esc_html_e( 'Bestehende Legacy-Custom-App (Admin-API-Token shpat_…)', 'blocksocial-woocommerce-sync' ); ?></span></label>
				</div>
				<div class="wcis-grid wcis-auth-client">
					<div class="wcis-field">
						<label><?php esc_html_e( 'Client-ID', 'blocksocial-woocommerce-sync' ); ?></label>
						<input type="text" class="code" name="shopify_client_id" value="<?php echo esc_attr( $st['client_id'] ); ?>" autocomplete="off" />
					</div>
					<div class="wcis-field">
						<label><?php esc_html_e( 'Client-Secret', 'blocksocial-woocommerce-sync' ); ?></label>
						<input type="password" class="code" name="shopify_client_secret" value="" autocomplete="new-password" placeholder="<?php echo $st['client_secret'] ? esc_attr__( '•••••• (gespeichert – leer lassen = unverändert)', 'blocksocial-woocommerce-sync' ) : ''; ?>" />
					</div>
				</div>
				<div class="wcis-grid wcis-auth-token">
					<div class="wcis-field">
						<label><?php esc_html_e( 'Admin-API-Zugriffstoken', 'blocksocial-woocommerce-sync' ); ?></label>
						<input type="password" class="code" name="shopify_token" value="" autocomplete="new-password" placeholder="<?php echo $st['token'] ? esc_attr__( '•••••• (gespeichert – leer lassen = unverändert)', 'blocksocial-woocommerce-sync' ) : 'shpat_…'; ?>" />
					</div>
					<div class="wcis-field">
						<label><?php esc_html_e( 'API-Geheimschlüssel (für Webhooks)', 'blocksocial-woocommerce-sync' ); ?></label>
						<input type="password" class="code" name="shopify_webhook_secret" value="" autocomplete="new-password" placeholder="<?php echo $st['webhook_secret'] ? esc_attr__( '•••••• (gespeichert)', 'blocksocial-woocommerce-sync' ) : ''; ?>" />
						<small><?php esc_html_e( 'Nötig, damit Verkäufe in Shopify per Webhook zurückgemeldet werden.', 'blocksocial-woocommerce-sync' ); ?></small>
					</div>
				</div>
				<?php
				$sw = array(
					'shopify_active'        => array( 'active', __( 'Aktiv', 'blocksocial-woocommerce-sync' ), __( 'Shop wird beliefert.', 'blocksocial-woocommerce-sync' ) ),
					'shopify_sync_stock'    => array( 'sync_stock', __( 'Bestände synchronisieren', 'blocksocial-woocommerce-sync' ), __( 'Bestände in Echtzeit übertragen; Verkäufe in Shopify reduzieren den Bestand aller Shops.', 'blocksocial-woocommerce-sync' ) ),
					'shopify_sync_products' => array( 'sync_products', __( 'Produkte übertragen', 'blocksocial-woocommerce-sync' ), __( 'Produkte anlegen und aktuell halten (Titel, Beschreibung, Preise, Varianten, EAN, Gewicht; Bilder beim Anlegen).', 'blocksocial-woocommerce-sync' ) ),
				);
				foreach ( $sw as $name => $def ) :
					?>
					<div class="wcis-field wcis-field--switch">
						<div class="wcis-field-main">
							<label class="wcis-switch"><input type="checkbox" name="<?php echo esc_attr( $name ); ?>" value="1" <?php checked( ! empty( $st[ $def[0] ] ) ); ?> /><span class="wcis-slider"></span></label>
							<div><strong><?php echo esc_html( $def[1] ); ?></strong><p><?php echo esc_html( $def[2] ); ?></p></div>
						</div>
					</div>
				<?php endforeach; ?>
				<div class="wcis-grid">
					<div class="wcis-field">
						<label><?php esc_html_e( 'Status neuer Produkte', 'blocksocial-woocommerce-sync' ); ?></label>
						<select name="shopify_product_status">
							<option value="draft" <?php selected( $st['product_status'], 'DRAFT' ); ?>><?php esc_html_e( 'Entwurf (erst prüfen, dann veröffentlichen)', 'blocksocial-woocommerce-sync' ); ?></option>
							<option value="active" <?php selected( $st['product_status'], 'ACTIVE' ); ?>><?php esc_html_e( 'Aktiv (sofort sichtbar)', 'blocksocial-woocommerce-sync' ); ?></option>
						</select>
					</div>
					<div class="wcis-field">
						<label><?php esc_html_e( 'Sortiment', 'blocksocial-woocommerce-sync' ); ?></label>
						<label class="wcis-radio"><input type="radio" name="shopify_scope" value="all" <?php checked( $st['scope'], 'all' ); ?> /> <span><?php esc_html_e( 'Gesamtes Sortiment', 'blocksocial-woocommerce-sync' ); ?></span></label>
						<label class="wcis-radio"><input type="radio" name="shopify_scope" value="categories" <?php checked( $st['scope'], 'categories' ); ?> /> <span><?php esc_html_e( 'Nur diese Kategorien:', 'blocksocial-woocommerce-sync' ); ?></span></label>
						<?php WCIS_View::category_multiselect( 'shopify_categories', $st['categories'] ); ?>
					</div>
				</div>
				<details class="wcis-details" <?php echo WCIS_Pricing::has_active_rules( WCIS_Pricing::sanitize_rules( $st['price_rules'] ) ) ? 'open' : ''; ?>>
					<summary><?php esc_html_e( 'Preisregeln für diesen Shopify-Shop', 'blocksocial-woocommerce-sync' ); ?></summary>
					<p class="wcis-hint"><?php esc_html_e( 'Auf-/Abschläge gelten nur für die an Shopify übertragenen Preise. Preise werden automatisch brutto oder netto übertragen – passend zur Shopify-Einstellung „Preise inkl. Steuern".', 'blocksocial-woocommerce-sync' ); ?></p>
					<?php WCIS_View::rules_editor( $st['price_rules'] ); ?>
				</details>
				<p><button type="submit" class="wcis-btn wcis-btn--primary"><?php echo $is_new ? esc_html__( 'Shopify-Shop hinzufügen', 'blocksocial-woocommerce-sync' ) : esc_html__( 'Speichern', 'blocksocial-woocommerce-sync' ); ?></button></p>
			</form>
			<?php
		};
		?>

		<div class="wcis-card">
			<div class="wcis-card-head"><h2><?php esc_html_e( 'Angebundene Shopify-Shops', 'blocksocial-woocommerce-sync' ); ?></h2>
				<p><?php esc_html_e( 'Shopify-Shops werden direkt vom Hauptshop beliefert (kein Plugin in Shopify nötig). Zuordnung per SKU.', 'blocksocial-woocommerce-sync' ); ?></p></div>
			<div class="wcis-card-body">
				<?php if ( empty( $wcis_stores ) ) : ?>
					<p class="wcis-empty"><?php esc_html_e( 'Noch kein Shopify-Shop angebunden.', 'blocksocial-woocommerce-sync' ); ?></p>
				<?php endif; ?>
				<?php foreach ( $wcis_stores as $wcis_st ) : ?>
					<div class="wcis-store">
						<div class="wcis-store-head">
							<div>
								<strong><?php echo esc_html( $wcis_st['name'] ); ?></strong> <code><?php echo esc_html( $wcis_st['domain'] ); ?></code>
								<?php if ( empty( $wcis_st['active'] ) ) : ?>
									<span class="wcis-lvl wcis-lvl-error"><?php esc_html_e( 'inaktiv', 'blocksocial-woocommerce-sync' ); ?></span>
								<?php endif; ?>
								<br /><small>
									<?php
									if ( $wcis_st['location_id'] ) {
										echo esc_html(
											sprintf(
												/* translators: 1: Lagerort, 2: Währung, 3: brutto/netto */
												__( 'Lagerort: %1$s · %2$s · Preise %3$s · Webhook: %4$s', 'blocksocial-woocommerce-sync' ),
												$wcis_st['location_name'],
												$wcis_st['currency'],
												$wcis_st['taxes_included'] ? __( 'brutto', 'blocksocial-woocommerce-sync' ) : __( 'netto', 'blocksocial-woocommerce-sync' ),
												$wcis_st['webhook_id'] ? __( 'aktiv', 'blocksocial-woocommerce-sync' ) : __( 'fehlt', 'blocksocial-woocommerce-sync' )
											)
										);
									} else {
										esc_html_e( 'Noch nicht getestet – bitte „Verbindung testen" ausführen.', 'blocksocial-woocommerce-sync' );
									}
									if ( $wcis_st['last_error'] ) {
										echo '<br /><span class="wcis-err-text">' . esc_html( $wcis_st['last_error'] ) . '</span>';
									}
									?>
								</small>
							</div>
							<div class="wcis-store-actions">
								<button type="button" class="wcis-btn wcis-btn--ghost wcis-shopify-test" data-store="<?php echo esc_attr( $wcis_st['id'] ); ?>"><?php esc_html_e( 'Verbindung testen', 'blocksocial-woocommerce-sync' ); ?></button>
								<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="wcis-inline-form" data-confirm="<?php esc_attr_e( 'Shopify-Shop entfernen? Produkte in Shopify bleiben erhalten, werden aber nicht mehr synchronisiert.', 'blocksocial-woocommerce-sync' ); ?>">
									<input type="hidden" name="action" value="wcis_shopify_delete" />
									<input type="hidden" name="shopify_id" value="<?php echo esc_attr( $wcis_st['id'] ); ?>" />
									<?php wp_nonce_field( 'wcis_shopify_delete' ); ?>
									<button type="submit" class="wcis-btn wcis-btn--ghost wcis-btn--danger"><?php esc_html_e( 'Entfernen', 'blocksocial-woocommerce-sync' ); ?></button>
								</form>
							</div>
						</div>
						<p class="wcis-test-result"></p>
						<details class="wcis-details">
							<summary><?php esc_html_e( 'Einstellungen bearbeiten', 'blocksocial-woocommerce-sync' ); ?></summary>
							<?php $wcis_store_form( $wcis_st ); ?>
						</details>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<?php if ( ! empty( $wcis_stores ) ) : ?>
			<?php
			$wcis_sjob = WCIS_Shopify::job_state();
			$wcis_srun = $wcis_sjob && 'running' === $wcis_sjob['status'];
			?>
			<div class="wcis-card">
				<div class="wcis-card-head"><h2><?php esc_html_e( 'Übertragung an Shopify', 'blocksocial-woocommerce-sync' ); ?></h2>
					<p><?php esc_html_e( 'Erstbefüllung oder kompletter Abgleich. Laufende Änderungen werden danach automatisch übertragen.', 'blocksocial-woocommerce-sync' ); ?></p></div>
				<div class="wcis-card-body">
					<form id="wcis-shopify-job-form" class="wcis-actionrow" onsubmit="return false;">
						<select id="wcis-shopify-store">
							<?php foreach ( $wcis_stores as $wcis_st ) : ?>
								<option value="<?php echo esc_attr( $wcis_st['id'] ); ?>"><?php echo esc_html( $wcis_st['name'] ); ?></option>
							<?php endforeach; ?>
						</select>
						<select id="wcis-shopify-mode">
							<option value="products"><?php esc_html_e( 'Produkte (inkl. Bestand)', 'blocksocial-woocommerce-sync' ); ?></option>
							<option value="stock"><?php esc_html_e( 'Nur Bestände', 'blocksocial-woocommerce-sync' ); ?></option>
						</select>
						<button type="submit" class="wcis-btn wcis-btn--primary" id="wcis-shopify-start"><?php esc_html_e( 'Übertragung starten', 'blocksocial-woocommerce-sync' ); ?></button>
						<button type="button" class="wcis-btn wcis-btn--ghost" id="wcis-shopify-cancel" style="display:none;"><?php esc_html_e( 'Abbrechen', 'blocksocial-woocommerce-sync' ); ?></button>
					</form>
					<?php WCIS_View::progress( 'wcis-shopify', WCIS_Shopify::job_percent( $wcis_sjob ), $wcis_srun ); ?>
				</div>
			</div>
		<?php endif; ?>

		<div class="wcis-card">
			<div class="wcis-card-head"><h2><?php esc_html_e( 'Shopify-Shop anbinden', 'blocksocial-woocommerce-sync' ); ?></h2></div>
			<div class="wcis-card-body">
				<details class="wcis-details">
					<summary><?php esc_html_e( 'Anleitung: Zugangsdaten in Shopify erstellen', 'blocksocial-woocommerce-sync' ); ?></summary>
					<ol class="wcis-steps">
						<li><?php esc_html_e( 'Im Shopify Dev Dashboard (dev.shopify.com) mit dem Konto des Shop-Inhabers eine App erstellen.', 'blocksocial-woocommerce-sync' ); ?></li>
						<li><?php esc_html_e( 'Unter „Versionen" folgende Admin-API-Berechtigungen (Scopes) wählen: read_products, write_products, read_inventory, write_inventory, read_locations. Version freigeben.', 'blocksocial-woocommerce-sync' ); ?></li>
						<li><?php esc_html_e( 'App im Shopify-Shop installieren.', 'blocksocial-woocommerce-sync' ); ?></li>
						<li><?php esc_html_e( 'Unter „Einstellungen" der App Client-ID und Client-Secret kopieren und hier eintragen. Der Zugriffstoken wird automatisch geholt und alle 24 Stunden erneuert.', 'blocksocial-woocommerce-sync' ); ?></li>
						<li><?php esc_html_e( 'Speichern, dann „Verbindung testen" – dabei werden Lagerort, Währung und Brutto/Netto erkannt und der Webhook für Verkaufsmeldungen eingerichtet.', 'blocksocial-woocommerce-sync' ); ?></li>
					</ol>
					<p class="wcis-hint"><?php esc_html_e( 'Ältere „Legacy Custom Apps" (vor 2026 im Shopify-Admin erstellt) funktionieren weiterhin mit ihrem Admin-API-Token (shpat_…) und dem API-Geheimschlüssel.', 'blocksocial-woocommerce-sync' ); ?></p>
					<p class="wcis-hint"><?php echo esc_html( sprintf( __( 'Webhook-Adresse dieses Shops: %s – muss per HTTPS aus dem Internet erreichbar sein.', 'blocksocial-woocommerce-sync' ), rest_url( WCIS_REST_NS . '/shopify/webhook/…' ) ) ); ?></p>
				</details>
				<?php $wcis_store_form( WCIS_Shopify::defaults() ); ?>
			</div>
		</div>
	<?php endif; ?>
</section>

<!-- TAB: Preise -->
<section class="wcis-tab" data-tab="pricing">
	<?php WCIS_View::pricing_card(); ?>
</section>
