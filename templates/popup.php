<?php
/**
 * Popup template – purely escaped output. Cache-safe: identical for every
 * visitor. Visibility is decided client-side from the companion cookie.
 *
 * Vars in scope:
 *  $opts    array  Settings
 *  $bg      float  0-1 background opacity
 *  $accent  string Hex color
 *  $accent2 string Hex color
 *
 * @package CannabisAgeVerifier
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$animations = ! empty( $opts['enable_animations'] );
$mode       = $opts['verification_mode'];
$min_age    = (int) $opts['min_age'];
?>
<dialog id="cav-root"
	class="cav-root<?php echo $animations ? ' cav-anim' : ''; ?>"
	aria-labelledby="cav-headline"
	aria-describedby="cav-subline"
	data-min-age="<?php echo esc_attr( $min_age ); ?>"
	style="--cav-accent:<?php echo esc_attr( $accent ); ?>;--cav-accent-2:<?php echo esc_attr( $accent2 ); ?>;--cav-bg-opacity:<?php echo esc_attr( $bg ); ?>;">
	<div class="cav-backdrop" aria-hidden="true">
		<div class="cav-orb cav-orb--1"></div>
		<div class="cav-orb cav-orb--2"></div>
		<div class="cav-orb cav-orb--3"></div>
	</div>

	<div class="cav-card" role="document">
		<?php if ( ! empty( $opts['logo_url'] ) ) : ?>
			<img class="cav-logo" src="<?php echo esc_url( $opts['logo_url'] ); ?>" alt="" decoding="async" loading="eager" width="120" height="40">
		<?php else : ?>
			<div class="cav-logo cav-logo--default" aria-hidden="true">
				<svg viewBox="0 0 64 64" width="56" height="56" focusable="false" aria-hidden="true">
					<defs>
						<linearGradient id="cav-leaf-g" x1="0" x2="1" y1="0" y2="1">
							<stop offset="0" stop-color="<?php echo esc_attr( $accent ); ?>"/>
							<stop offset="1" stop-color="<?php echo esc_attr( $accent2 ); ?>"/>
						</linearGradient>
					</defs>
					<path fill="url(#cav-leaf-g)" d="M32 4c1 6 4 10 9 13-4 1-7 3-9 6-2-3-5-5-9-6 5-3 8-7 9-13zm-14 14c2 5 6 8 11 9-3 3-5 6-5 10-4-2-8-5-10-9 1-3 2-7 4-10zm28 0c2 3 3 7 4 10-2 4-6 7-10 9 0-4-2-7-5-10 5-1 9-4 11-9zM10 36c3 4 8 6 13 6-1 4-1 8 1 12-5-1-10-3-13-7-1-3-1-7-1-11zm44 0c0 4 0 8-1 11-3 4-8 6-13 7 2-4 2-8 1-12 5 0 10-2 13-6zM26 50c2 3 4 5 6 8 2-3 4-5 6-8-2 0-4-1-6-2-2 1-4 2-6 2z"/>
				</svg>
			</div>
		<?php endif; ?>

		<div class="cav-badge" aria-hidden="true"><?php echo esc_html( $min_age ); ?>+</div>

		<h2 id="cav-headline" class="cav-headline"><?php echo esc_html( $opts['headline'] ); ?></h2>

		<p id="cav-subline" class="cav-subline">
			<?php echo wp_kses_post( $opts['subline'] ); ?>
		</p>

		<?php if ( 'dob' === $mode ) : ?>
			<form id="cav-form" class="cav-form" novalidate>
				<fieldset class="cav-dob">
					<legend class="screen-reader-text"><?php esc_html_e( 'Geburtsdatum', 'cannabis-age-verifier' ); ?></legend>
					<label class="cav-field">
						<span><?php esc_html_e( 'Tag', 'cannabis-age-verifier' ); ?></span>
						<input type="number" name="day" inputmode="numeric" autocomplete="bday-day" min="1" max="31" placeholder="TT" required>
					</label>
					<label class="cav-field">
						<span><?php esc_html_e( 'Monat', 'cannabis-age-verifier' ); ?></span>
						<input type="number" name="month" inputmode="numeric" autocomplete="bday-month" min="1" max="12" placeholder="MM" required>
					</label>
					<label class="cav-field cav-field--wide">
						<span><?php esc_html_e( 'Jahr', 'cannabis-age-verifier' ); ?></span>
						<input type="number" name="year" inputmode="numeric" autocomplete="bday-year" min="1900" max="<?php echo esc_attr( (int) gmdate( 'Y' ) ); ?>" placeholder="JJJJ" required>
					</label>
				</fieldset>

				<div id="cav-error" class="cav-error" role="alert" aria-live="polite"></div>

				<button type="submit" class="cav-btn cav-btn--primary" data-cav-submit>
					<span class="cav-btn-label"><?php esc_html_e( 'Alter bestätigen & Shop betreten', 'cannabis-age-verifier' ); ?></span>
					<span class="cav-spinner" aria-hidden="true"></span>
				</button>
				<button type="button" class="cav-btn cav-btn--ghost" data-cav-decline>
					<?php esc_html_e( 'Ich bin jünger', 'cannabis-age-verifier' ); ?>
				</button>
			</form>
		<?php else : ?>
			<form id="cav-form" class="cav-form" novalidate data-cav-confirm>
				<div id="cav-error" class="cav-error" role="alert" aria-live="polite"></div>
				<button type="button" class="cav-btn cav-btn--primary" data-cav-confirm-yes>
					<?php
					printf(
						/* translators: %d: minimum age */
						esc_html__( 'Ja, ich bin %d oder älter', 'cannabis-age-verifier' ),
						(int) $min_age
					);
					?>
				</button>
				<button type="button" class="cav-btn cav-btn--ghost" data-cav-confirm-no>
					<?php
					printf(
						/* translators: %d: minimum age */
						esc_html__( 'Nein, jünger als %d', 'cannabis-age-verifier' ),
						(int) $min_age
					);
					?>
				</button>
			</form>
		<?php endif; ?>

		<p class="cav-legal">
			<?php echo wp_kses_post( $opts['legal_note'] ); ?>
		</p>

		<ul class="cav-meta">
			<?php if ( ! empty( $opts['privacy_url'] ) ) : ?>
				<li><a href="<?php echo esc_url( $opts['privacy_url'] ); ?>" rel="noopener"><?php esc_html_e( 'Datenschutz', 'cannabis-age-verifier' ); ?></a></li>
			<?php endif; ?>
			<?php if ( ! empty( $opts['imprint_url'] ) ) : ?>
				<li><a href="<?php echo esc_url( $opts['imprint_url'] ); ?>" rel="noopener"><?php esc_html_e( 'Impressum', 'cannabis-age-verifier' ); ?></a></li>
			<?php endif; ?>
			<li class="cav-meta-brand">
				<?php esc_html_e( 'Powered by', 'cannabis-age-verifier' ); ?>
				<a href="https://blocksocial.eu" target="_blank" rel="noopener noreferrer">BlockSocial</a>
			</li>
		</ul>
	</div>
</dialog>

