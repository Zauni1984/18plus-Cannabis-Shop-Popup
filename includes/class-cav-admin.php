<?php
/**
 * Admin UI for Cannabis Age Verifier.
 *
 * @package CannabisAgeVerifier
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class CAV_Admin {

	const MENU_SLUG = 'cannabis-age-verifier';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_filter( 'plugin_action_links_' . CAV_PLUGIN_BASENAME, array( $this, 'action_links' ) );
		add_action( 'admin_notices', array( $this, 'license_notice' ) );
		add_action( 'admin_post_cav_reset_settings', array( $this, 'handle_reset' ) );
	}

	public function handle_reset() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Keine Berechtigung.', 'cannabis-age-verifier' ), 403 );
		}

		check_admin_referer( 'cav_reset_settings' );

		$scope = isset( $_POST['scope'] ) ? sanitize_key( wp_unslash( $_POST['scope'] ) ) : 'all';
		$tab   = isset( $_POST['tab'] ) ? sanitize_key( wp_unslash( $_POST['tab'] ) ) : 'general';
		$map   = CAV_Settings::tab_fields();

		$defaults = CAV_Settings::defaults();
		$current  = get_option( CAV_OPTION_KEY, array() );
		if ( ! is_array( $current ) ) {
			$current = array();
		}
		$merged = wp_parse_args( $current, $defaults );

		if ( 'tab' === $scope && isset( $map[ $tab ] ) ) {
			foreach ( $map[ $tab ] as $field ) {
				if ( array_key_exists( $field, $defaults ) ) {
					$merged[ $field ] = $defaults[ $field ];
				}
			}
		} else {
			$merged = $defaults;
			$tab    = 'general';
		}

		update_option( CAV_OPTION_KEY, $merged, false );

		$redirect = add_query_arg(
			array(
				'page'         => self::MENU_SLUG,
				'tab'          => $tab,
				'cav-reset'    => 'tab' === $scope ? 'tab' : 'all',
				'_wpnonce_ack' => wp_create_nonce( 'cav_reset_ack' ),
			),
			admin_url( 'admin.php' )
		);

		wp_safe_redirect( $redirect );
		exit;
	}

	public function action_links( $links ) {
		$settings_url = admin_url( 'admin.php?page=' . self::MENU_SLUG );
		array_unshift(
			$links,
			'<a href="' . esc_url( $settings_url ) . '">' . esc_html__( 'Einstellungen', 'cannabis-age-verifier' ) . '</a>'
		);
		return $links;
	}

	public function register_menu() {
		add_menu_page(
			__( 'Cannabis Age Verifier', 'cannabis-age-verifier' ),
			__( 'Age Verifier', 'cannabis-age-verifier' ),
			'manage_options',
			self::MENU_SLUG,
			array( $this, 'render_page' ),
			'dashicons-shield-alt',
			58
		);
	}

	public function register_settings() {
		register_setting(
			'cav_settings_group',
			CAV_OPTION_KEY,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( 'CAV_Settings', 'sanitize' ),
				'default'           => CAV_Settings::defaults(),
			)
		);

		register_setting(
			'cav_license_group',
			CAV_LICENSE_KEY,
			array(
				'type'              => 'string',
				'sanitize_callback' => array( 'CAV_License', 'save_key' ),
				'default'           => '',
			)
		);
	}

	public function license_notice() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( $screen && false !== strpos( (string) $screen->id, self::MENU_SLUG ) ) {
			return;
		}

		if ( CAV_License::is_active() ) {
			return;
		}

		$url = admin_url( 'admin.php?page=' . self::MENU_SLUG . '&tab=license' );
		echo '<div class="notice notice-warning"><p>';
		printf(
			/* translators: %s: settings URL */
			wp_kses(
				__( '<strong>Cannabis Age Verifier:</strong> Bitte aktiviere Deinen Lizenzschlüssel, um das Plugin produktiv zu nutzen. <a href="%s">Jetzt aktivieren</a>.', 'cannabis-age-verifier' ),
				array( 'strong' => array(), 'a' => array( 'href' => array() ) )
			),
			esc_url( $url )
		);
		echo '</p></div>';
	}

	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'general';
		$tab = in_array( $tab, array( 'general', 'design', 'legal', 'license' ), true ) ? $tab : 'general';

		$opts    = CAV_Settings::get_all();
		$status  = CAV_License::status();
		$license = get_option( CAV_LICENSE_KEY, '' );

		wp_enqueue_style(
			'cav-admin',
			CAV_PLUGIN_URL . 'assets/css/cav-admin.css',
			array(),
			CAV_VERSION
		);
		wp_enqueue_script(
			'cav-admin',
			CAV_PLUGIN_URL . 'assets/js/cav-admin.js',
			array(),
			CAV_VERSION,
			true
		);

		?>
		<div class="wrap cav-admin">
			<h1>
				<span class="cav-admin-logo" aria-hidden="true">🌿</span>
				<?php esc_html_e( 'Cannabis Age Verifier', 'cannabis-age-verifier' ); ?>
				<span class="cav-admin-version">v<?php echo esc_html( CAV_VERSION ); ?></span>
			</h1>
			<p class="cav-admin-tagline">
				<?php esc_html_e( 'DSGVO-konformes 18+ Popup für WooCommerce-Cannabis-Shops · BlockSocial UG (haftungsbeschränkt)', 'cannabis-age-verifier' ); ?>
			</p>

			<?php $this->render_save_notice(); ?>

			<h2 class="nav-tab-wrapper">
				<a href="?page=<?php echo esc_attr( self::MENU_SLUG ); ?>&tab=general" class="nav-tab <?php echo 'general' === $tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Allgemein', 'cannabis-age-verifier' ); ?></a>
				<a href="?page=<?php echo esc_attr( self::MENU_SLUG ); ?>&tab=design" class="nav-tab <?php echo 'design' === $tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Design', 'cannabis-age-verifier' ); ?></a>
				<a href="?page=<?php echo esc_attr( self::MENU_SLUG ); ?>&tab=legal" class="nav-tab <?php echo 'legal' === $tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Rechtliches & DSGVO', 'cannabis-age-verifier' ); ?></a>
				<a href="?page=<?php echo esc_attr( self::MENU_SLUG ); ?>&tab=license" class="nav-tab <?php echo 'license' === $tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Lizenz', 'cannabis-age-verifier' ); ?></a>
			</h2>

			<?php if ( 'license' === $tab ) : ?>
				<?php $this->render_license_tab( $license, $status ); ?>
			<?php else : ?>
				<form method="post" action="options.php" class="cav-form">
					<?php settings_fields( 'cav_settings_group' ); ?>
					<input type="hidden" name="<?php echo esc_attr( CAV_OPTION_KEY ); ?>[_tab]" value="<?php echo esc_attr( $tab ); ?>">
					<?php $this->render_tab( $tab, $opts ); ?>
					<p class="submit cav-form-actions">
						<?php submit_button( __( 'Einstellungen speichern', 'cannabis-age-verifier' ), 'primary', 'submit', false ); ?>
					</p>
				</form>

				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="cav-reset-form" data-cav-reset="<?php echo esc_attr( $this->tab_label( $tab ) ); ?>">
					<input type="hidden" name="action" value="cav_reset_settings">
					<input type="hidden" name="scope" value="tab">
					<input type="hidden" name="tab" value="<?php echo esc_attr( $tab ); ?>">
					<?php wp_nonce_field( 'cav_reset_settings' ); ?>
					<button type="submit" class="button button-secondary cav-reset-btn">
						<span class="dashicons dashicons-image-rotate" aria-hidden="true"></span>
						<?php
						/* translators: %s: tab label */
						printf( esc_html__( 'Diesen Tab (%s) auf Standardwerte zurücksetzen', 'cannabis-age-verifier' ), esc_html( $this->tab_label( $tab ) ) );
						?>
					</button>
				</form>
			<?php endif; ?>

			<aside class="cav-admin-aside">
				<h3><?php esc_html_e( 'Status', 'cannabis-age-verifier' ); ?></h3>
				<ul>
					<li>
						<?php esc_html_e( 'Lizenz:', 'cannabis-age-verifier' ); ?>
						<?php if ( CAV_License::STATUS_VENDOR === $status ) : ?>
							<strong class="cav-pill cav-pill--green"><?php esc_html_e( 'Vendor-Domain (kostenfrei)', 'cannabis-age-verifier' ); ?></strong>
						<?php elseif ( CAV_License::STATUS_ACTIVE === $status ) : ?>
							<strong class="cav-pill cav-pill--green"><?php esc_html_e( 'Aktiv', 'cannabis-age-verifier' ); ?></strong>
						<?php else : ?>
							<strong class="cav-pill cav-pill--red"><?php esc_html_e( 'Nicht aktiv', 'cannabis-age-verifier' ); ?></strong>
						<?php endif; ?>
					</li>
					<li><?php esc_html_e( 'WooCommerce:', 'cannabis-age-verifier' ); ?>
						<strong><?php echo class_exists( 'WooCommerce' ) ? esc_html__( 'erkannt', 'cannabis-age-verifier' ) : esc_html__( 'nicht installiert', 'cannabis-age-verifier' ); ?></strong>
					</li>
				</ul>

				<h3><?php esc_html_e( 'Werkseinstellungen', 'cannabis-age-verifier' ); ?></h3>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="cav-reset-form cav-reset-form--all" data-cav-reset-all>
					<input type="hidden" name="action" value="cav_reset_settings">
					<input type="hidden" name="scope" value="all">
					<?php wp_nonce_field( 'cav_reset_settings' ); ?>
					<button type="submit" class="button button-link-delete cav-reset-all">
						<span class="dashicons dashicons-trash" aria-hidden="true"></span>
						<?php esc_html_e( 'Alle Einstellungen zurücksetzen', 'cannabis-age-verifier' ); ?>
					</button>
					<small><?php esc_html_e( 'Setzt sämtliche Tabs auf die Werkseinstellungen zurück. Die Lizenz bleibt unverändert.', 'cannabis-age-verifier' ); ?></small>
				</form>

				<h3><?php esc_html_e( 'Support', 'cannabis-age-verifier' ); ?></h3>
				<p>info@blocksocial.eu<br>BlockSocial UG (haftungsbeschränkt)<br>Kratzmühlstraße 14<br>92339 Beilngries</p>
			</aside>
		</div>
		<?php
	}

	private function tab_label( $tab ) {
		$labels = array(
			'general' => __( 'Allgemein', 'cannabis-age-verifier' ),
			'design'  => __( 'Design', 'cannabis-age-verifier' ),
			'legal'   => __( 'Rechtliches & DSGVO', 'cannabis-age-verifier' ),
			'license' => __( 'Lizenz', 'cannabis-age-verifier' ),
		);
		return isset( $labels[ $tab ] ) ? $labels[ $tab ] : ucfirst( $tab );
	}

	private function render_save_notice() {
		// `settings_errors` from Settings API (success + validation errors).
		settings_errors( 'cav_settings_group' );
		settings_errors( 'cav_license_group' );

		// Settings saved (options.php redirect).
		if ( ! empty( $_GET['settings-updated'] ) && 'true' === $_GET['settings-updated'] ) {
			$tab     = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'general';
			$message = ( 'license' === $tab )
				? __( 'Lizenz gespeichert.', 'cannabis-age-verifier' )
				: __( 'Einstellungen erfolgreich gespeichert.', 'cannabis-age-verifier' );
			$this->print_branded_notice( $message );
		}

		// Reset confirmation.
		if ( ! empty( $_GET['cav-reset'] ) && ! empty( $_GET['_wpnonce_ack'] ) ) {
			$nonce = sanitize_text_field( wp_unslash( $_GET['_wpnonce_ack'] ) );
			if ( wp_verify_nonce( $nonce, 'cav_reset_ack' ) ) {
				$scope   = sanitize_key( wp_unslash( $_GET['cav-reset'] ) );
				$tab     = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'general';
				$message = ( 'all' === $scope )
					? __( 'Alle Einstellungen wurden auf Werkseinstellungen zurückgesetzt.', 'cannabis-age-verifier' )
					: sprintf(
						/* translators: %s: tab label */
						__( 'Tab „%s" wurde auf Standardwerte zurückgesetzt.', 'cannabis-age-verifier' ),
						$this->tab_label( $tab )
					);
				$this->print_branded_notice( $message );
			}
		}
	}

	private function print_branded_notice( $message ) {
		?>
		<div class="notice notice-success is-dismissible cav-saved-notice" role="status">
			<p>
				<span class="cav-saved-icon" aria-hidden="true">✓</span>
				<strong><?php echo esc_html( $message ); ?></strong>
			</p>
		</div>
		<?php
	}

	private function render_tab( $tab, $opts ) {
		switch ( $tab ) {
			case 'design':
				$this->render_design_tab( $opts );
				break;
			case 'legal':
				$this->render_legal_tab( $opts );
				break;
			default:
				$this->render_general_tab( $opts );
		}
	}

	private function render_general_tab( $opts ) {
		?>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><?php esc_html_e( 'Popup aktivieren', 'cannabis-age-verifier' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="<?php echo esc_attr( CAV_OPTION_KEY ); ?>[enabled]" value="1" <?php checked( 1, $opts['enabled'] ); ?>>
						<?php esc_html_e( 'Altersverifikations-Popup aktivieren', 'cannabis-age-verifier' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Mindestalter', 'cannabis-age-verifier' ); ?></th>
				<td>
					<fieldset class="cav-age-presets" data-cav-age-presets>
						<label class="cav-age-preset">
							<input type="radio" name="<?php echo esc_attr( CAV_OPTION_KEY ); ?>[age_preset]" value="18" <?php checked( '18', $opts['age_preset'] ); ?> data-cav-preset="18">
							<span class="cav-age-preset-badge">18+</span>
							<span class="cav-age-preset-label"><?php esc_html_e( 'Cannabis (CanG, Standard)', 'cannabis-age-verifier' ); ?></span>
						</label>
						<label class="cav-age-preset">
							<input type="radio" name="<?php echo esc_attr( CAV_OPTION_KEY ); ?>[age_preset]" value="21" <?php checked( '21', $opts['age_preset'] ); ?> data-cav-preset="21">
							<span class="cav-age-preset-badge cav-age-preset-badge--alt">21+</span>
							<span class="cav-age-preset-label"><?php esc_html_e( 'Erhöhter Jugendschutz (z. B. THC > 10 %)', 'cannabis-age-verifier' ); ?></span>
						</label>
						<label class="cav-age-preset">
							<input type="radio" name="<?php echo esc_attr( CAV_OPTION_KEY ); ?>[age_preset]" value="custom" <?php checked( 'custom', $opts['age_preset'] ); ?> data-cav-preset="custom">
							<span class="cav-age-preset-badge cav-age-preset-badge--custom">★</span>
							<span class="cav-age-preset-label"><?php esc_html_e( 'Individuell', 'cannabis-age-verifier' ); ?></span>
						</label>
					</fieldset>
					<p class="cav-custom-age-wrap" data-cav-custom-age <?php echo 'custom' === $opts['age_preset'] ? '' : 'hidden'; ?>>
						<label for="cav-min-age"><?php esc_html_e( 'Individuelles Mindestalter:', 'cannabis-age-verifier' ); ?></label>
						<input id="cav-min-age" type="number" min="18" max="99" step="1" name="<?php echo esc_attr( CAV_OPTION_KEY ); ?>[min_age]" value="<?php echo esc_attr( $opts['min_age'] ); ?>" class="small-text">
						<span class="description"><?php esc_html_e( 'Minimum 18 Jahre (gesetzliche Anforderung CanG).', 'cannabis-age-verifier' ); ?></span>
					</p>
					<p class="description"><?php esc_html_e( 'In Deutschland gilt für Cannabis-Produkte gemäß Cannabisgesetz (CanG) ein gesetzliches Mindestalter von 18 Jahren. Niedrigere Werte sind nicht zulässig.', 'cannabis-age-verifier' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Verifizierungsmethode', 'cannabis-age-verifier' ); ?></th>
				<td>
					<label><input type="radio" name="<?php echo esc_attr( CAV_OPTION_KEY ); ?>[verification_mode]" value="dob" <?php checked( 'dob', $opts['verification_mode'] ); ?>>
						<?php esc_html_e( 'Geburtsdatum eingeben (empfohlen, rechtssicher)', 'cannabis-age-verifier' ); ?></label><br>
					<label><input type="radio" name="<?php echo esc_attr( CAV_OPTION_KEY ); ?>[verification_mode]" value="confirm" <?php checked( 'confirm', $opts['verification_mode'] ); ?>>
						<?php esc_html_e( 'Einfache Bestätigung (Ja / Nein)', 'cannabis-age-verifier' ); ?></label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Anzeigebereich', 'cannabis-age-verifier' ); ?></th>
				<td>
					<label><input type="radio" name="<?php echo esc_attr( CAV_OPTION_KEY ); ?>[scope]" value="shop" <?php checked( 'shop', $opts['scope'] ); ?>>
						<?php esc_html_e( 'Nur WooCommerce-Seiten (Shop, Produkt, Kategorie, Warenkorb, Kasse)', 'cannabis-age-verifier' ); ?></label><br>
					<label><input type="radio" name="<?php echo esc_attr( CAV_OPTION_KEY ); ?>[scope]" value="site" <?php checked( 'site', $opts['scope'] ); ?>>
						<?php esc_html_e( 'Gesamte Website', 'cannabis-age-verifier' ); ?></label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Cookie-Laufzeit (Tage)', 'cannabis-age-verifier' ); ?></th>
				<td>
					<input type="number" min="1" max="365" step="1" name="<?php echo esc_attr( CAV_OPTION_KEY ); ?>[cookie_lifetime_days]" value="<?php echo esc_attr( $opts['cookie_lifetime_days'] ); ?>" class="small-text">
					<p class="description"><?php esc_html_e( 'Wie lange die positive Altersbestätigung gespeichert wird. Empfehlung: 30 Tage.', 'cannabis-age-verifier' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Minderjährige sperren (Stunden)', 'cannabis-age-verifier' ); ?></th>
				<td>
					<input type="number" min="1" max="720" step="1" name="<?php echo esc_attr( CAV_OPTION_KEY ); ?>[remember_minor_hours]" value="<?php echo esc_attr( $opts['remember_minor_hours'] ); ?>" class="small-text">
					<p class="description"><?php esc_html_e( 'Bei negativer Altersprüfung wird ein technisch notwendiger Cookie gesetzt, der erneutes Eintragen verhindert.', 'cannabis-age-verifier' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Weiterleitungs-URL bei < Mindestalter', 'cannabis-age-verifier' ); ?></th>
				<td>
					<input type="url" name="<?php echo esc_attr( CAV_OPTION_KEY ); ?>[redirect_url]" value="<?php echo esc_attr( $opts['redirect_url'] ); ?>" class="regular-text" required>
					<p class="description"><?php esc_html_e( 'Standard: Aufklärungsseite des Bundesgesundheitsministeriums zum Cannabisgesetz (CanG).', 'cannabis-age-verifier' ); ?></p>
				</td>
			</tr>
		</table>
		<?php
	}

	private function render_design_tab( $opts ) {
		?>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><?php esc_html_e( 'Überschrift', 'cannabis-age-verifier' ); ?></th>
				<td><input type="text" name="<?php echo esc_attr( CAV_OPTION_KEY ); ?>[headline]" value="<?php echo esc_attr( $opts['headline'] ); ?>" class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Untertitel / Erklärung', 'cannabis-age-verifier' ); ?></th>
				<td>
					<textarea name="<?php echo esc_attr( CAV_OPTION_KEY ); ?>[subline]" rows="3" class="large-text"><?php echo esc_textarea( $opts['subline'] ); ?></textarea>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Logo-URL (optional)', 'cannabis-age-verifier' ); ?></th>
				<td><input type="url" name="<?php echo esc_attr( CAV_OPTION_KEY ); ?>[logo_url]" value="<?php echo esc_attr( $opts['logo_url'] ); ?>" class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Akzentfarbe', 'cannabis-age-verifier' ); ?></th>
				<td>
					<input type="text" name="<?php echo esc_attr( CAV_OPTION_KEY ); ?>[accent_color]" value="<?php echo esc_attr( $opts['accent_color'] ); ?>" class="small-text" pattern="^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$">
					<input type="text" name="<?php echo esc_attr( CAV_OPTION_KEY ); ?>[accent_color_2]" value="<?php echo esc_attr( $opts['accent_color_2'] ); ?>" class="small-text" pattern="^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$">
					<p class="description"><?php esc_html_e( 'Zwei Hex-Werte für den animierten Verlauf (#RRGGBB).', 'cannabis-age-verifier' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Hintergrund-Deckkraft (%)', 'cannabis-age-verifier' ); ?></th>
				<td><input type="number" min="30" max="100" name="<?php echo esc_attr( CAV_OPTION_KEY ); ?>[background_opacity]" value="<?php echo esc_attr( $opts['background_opacity'] ); ?>" class="small-text"></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Animationen', 'cannabis-age-verifier' ); ?></th>
				<td>
					<label><input type="checkbox" name="<?php echo esc_attr( CAV_OPTION_KEY ); ?>[enable_animations]" value="1" <?php checked( 1, $opts['enable_animations'] ); ?>>
						<?php esc_html_e( 'Sanfte Animationen aktivieren (respektiert prefers-reduced-motion)', 'cannabis-age-verifier' ); ?></label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Scroll-Sperre', 'cannabis-age-verifier' ); ?></th>
				<td>
					<label><input type="checkbox" name="<?php echo esc_attr( CAV_OPTION_KEY ); ?>[block_scroll]" value="1" <?php checked( 1, $opts['block_scroll'] ); ?>>
						<?php esc_html_e( 'Scrollen blockieren, bis Bestätigung erfolgt', 'cannabis-age-verifier' ); ?></label>
				</td>
			</tr>
		</table>
		<?php
	}

	private function render_legal_tab( $opts ) {
		?>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><?php esc_html_e( 'Rechtshinweis im Popup', 'cannabis-age-verifier' ); ?></th>
				<td><textarea name="<?php echo esc_attr( CAV_OPTION_KEY ); ?>[legal_note]" rows="4" class="large-text"><?php echo esc_textarea( $opts['legal_note'] ); ?></textarea></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Datenschutzerklärung URL', 'cannabis-age-verifier' ); ?></th>
				<td><input type="url" name="<?php echo esc_attr( CAV_OPTION_KEY ); ?>[privacy_url]" value="<?php echo esc_attr( $opts['privacy_url'] ); ?>" class="regular-text"></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Impressum URL', 'cannabis-age-verifier' ); ?></th>
				<td><input type="url" name="<?php echo esc_attr( CAV_OPTION_KEY ); ?>[imprint_url]" value="<?php echo esc_attr( $opts['imprint_url'] ); ?>" class="regular-text"></td>
			</tr>
		</table>
		<div class="cav-callout">
			<h3><?php esc_html_e( 'DSGVO-Hinweise', 'cannabis-age-verifier' ); ?></h3>
			<ul>
				<li><?php esc_html_e( 'Das Plugin speichert ausschließlich technisch notwendige Cookies (Art. 6 Abs. 1 lit. f DSGVO).', 'cannabis-age-verifier' ); ?></li>
				<li><?php esc_html_e( 'Es wird kein Geburtsdatum gespeichert – nur das Ergebnis (volljährig ja/nein) als signiertes Token im HttpOnly-Cookie.', 'cannabis-age-verifier' ); ?></li>
				<li><?php esc_html_e( 'Keine Übermittlung personenbezogener Daten an Drittserver (keine Phone-Home-Lizenzprüfung).', 'cannabis-age-verifier' ); ?></li>
				<li><?php esc_html_e( 'IP-Adressen werden nur kurzfristig gehasht zur Rate-Limit-Prüfung verarbeitet.', 'cannabis-age-verifier' ); ?></li>
			</ul>
		</div>
		<?php
	}

	private function render_license_tab( $license, $status ) {
		?>
		<form method="post" action="options.php" class="cav-form cav-license-form">
			<?php settings_fields( 'cav_license_group' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Lizenzschlüssel', 'cannabis-age-verifier' ); ?></th>
					<td>
						<input type="text"
							name="<?php echo esc_attr( CAV_LICENSE_KEY ); ?>"
							value="<?php echo esc_attr( $license ); ?>"
							placeholder="CAV-XXXX-XXXX-XXXX-XXXX"
							class="regular-text"
							autocomplete="off"
							spellcheck="false">
						<p class="description">
							<?php
							if ( CAV_License::STATUS_VENDOR === $status ) {
								esc_html_e( 'Diese Domain (blocksocial.eu) wird automatisch als Vendor-Installation erkannt – kein Lizenzschlüssel erforderlich.', 'cannabis-age-verifier' );
							} else {
								esc_html_e( 'Lizenz erhalten unter https://blocksocial.eu/plugins/cannabis-age-verifier oder info@blocksocial.eu.', 'cannabis-age-verifier' );
							}
							?>
						</p>
					</td>
				</tr>
			</table>
			<?php submit_button( __( 'Lizenz speichern & prüfen', 'cannabis-age-verifier' ) ); ?>
		</form>
		<?php
	}
}
