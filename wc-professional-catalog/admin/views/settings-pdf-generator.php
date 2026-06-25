<?php
/**
 * PDF Generator admin tab.
 *
 * @package WCProfessionalCatalog
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Build the scope dropdown options from the same section tree the renderer uses.
$sections = WCPC_Catalog::collect_sections();

$main_cats = array();
$sub_cats  = array();
foreach ( $sections as $s ) {
	if ( '' !== $s['main'] && ! isset( $main_cats[ $s['main'] ] ) ) {
		$main_cats[ $s['main'] ] = $s['main'];
	}
	if ( '' !== $s['sub'] ) {
		$sub_cats[] = array(
			'cat_id' => (int) $s['cat_id'],
			'label'  => $s['main'] . ' / ' . $s['sub'],
		);
	}
}

$jobs       = WCPC_PDF_Job::all();
$active_job = null;
foreach ( $jobs as $j ) {
	if ( in_array( $j['status'], array( 'queued', 'processing', 'finalizing' ), true ) ) {
		$active_job = $j;
		break;
	}
}

$upload   = wp_upload_dir();
$catalogs = trailingslashit( $upload['basedir'] ) . WCPC_PDF_Job::DIR_FINAL;
$writable = wp_mkdir_p( $catalogs ) && is_writable( $catalogs );
?>
<div class="wcpc-pdf-generator" data-nonce="<?php echo esc_attr( wp_create_nonce( 'wcpc_pdf_nonce' ) ); ?>">
	<h2><?php esc_html_e( 'PDF-Generator', 'wc-professional-catalog' ); ?></h2>
	<p class="description">
		<?php esc_html_e( 'Erstellt PDFs im Hintergrund in kleinen Batches, damit auch große Kataloge das Memory-Limit nicht sprengen. Die fertigen PDFs werden im Uploads-Ordner gespeichert.', 'wc-professional-catalog' ); ?>
	</p>

	<?php if ( ! $writable ) : ?>
		<div class="notice notice-error inline">
			<p><?php printf( esc_html__( 'Der Upload-Ordner %s ist nicht beschreibbar.', 'wc-professional-catalog' ), '<code>' . esc_html( $catalogs ) . '</code>' ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( ! WCPC_PDF::is_dompdf_available() ) : ?>
		<div class="notice notice-warning inline">
			<p><?php esc_html_e( 'Dompdf ist nicht installiert - bitte zuerst per Composer einrichten (siehe Tab PDF / Druck).', 'wc-professional-catalog' ); ?></p>
		</div>
	<?php endif; ?>

	<table class="form-table">
		<tr>
			<th><label for="wcpc-pdf-scope"><?php esc_html_e( 'Umfang', 'wc-professional-catalog' ); ?></label></th>
			<td>
				<select id="wcpc-pdf-scope" class="regular-text">
					<option value="all|"><?php esc_html_e( 'Gesamter Katalog', 'wc-professional-catalog' ); ?></option>
					<?php if ( $main_cats ) : ?>
						<optgroup label="<?php esc_attr_e( 'Hauptkategorie', 'wc-professional-catalog' ); ?>">
							<?php foreach ( $main_cats as $name ) : ?>
								<option value="main_cat|<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $name ); ?></option>
							<?php endforeach; ?>
						</optgroup>
					<?php endif; ?>
					<?php if ( $sub_cats ) : ?>
						<optgroup label="<?php esc_attr_e( 'Unterkategorie', 'wc-professional-catalog' ); ?>">
							<?php foreach ( $sub_cats as $sc ) : ?>
								<option value="sub_cat|<?php echo (int) $sc['cat_id']; ?>" data-label="<?php echo esc_attr( $sc['label'] ); ?>"><?php echo esc_html( $sc['label'] ); ?></option>
							<?php endforeach; ?>
						</optgroup>
					<?php endif; ?>
				</select>
			</td>
		</tr>
		<tr>
			<th></th>
			<td>
				<button type="button" class="button button-primary" id="wcpc-pdf-start" <?php disabled( $active_job || ! $writable || ! WCPC_PDF::is_dompdf_available() ); ?>>
					<?php esc_html_e( 'PDF erstellen', 'wc-professional-catalog' ); ?>
				</button>
				<button type="button" class="button" id="wcpc-pdf-cancel" style="display:none;"><?php esc_html_e( 'Abbrechen', 'wc-professional-catalog' ); ?></button>
			</td>
		</tr>
	</table>

	<div id="wcpc-pdf-progress" class="wcpc-pdf-progress" <?php echo $active_job ? '' : 'hidden'; ?>>
		<div class="wcpc-pdf-progress__label">
			<span id="wcpc-pdf-progress-status"><?php echo $active_job ? esc_html( $active_job['status'] ) : '—'; ?></span>
			<span class="wcpc-pdf-progress__counts">
				<span id="wcpc-pdf-progress-processed"><?php echo $active_job ? (int) $active_job['processed'] : 0; ?></span>
				/
				<span id="wcpc-pdf-progress-total"><?php echo $active_job ? (int) $active_job['total_products'] : 0; ?></span>
				<?php esc_html_e( 'Produkte', 'wc-professional-catalog' ); ?>
			</span>
		</div>
		<div class="wcpc-pdf-progress__section" id="wcpc-pdf-progress-section"><?php echo $active_job ? esc_html( $active_job['current_section'] ?: '' ) : ''; ?></div>
		<div class="wcpc-pdf-progress__bar"><div class="wcpc-pdf-progress__fill" id="wcpc-pdf-progress-fill" style="width: <?php echo $active_job && $active_job['total_products'] ? (int) ( 100 * $active_job['processed'] / $active_job['total_products'] ) : 0; ?>%;"></div></div>
		<div class="wcpc-pdf-progress__error" id="wcpc-pdf-progress-error" hidden></div>
	</div>

	<h3 style="margin-top:30px;"><?php esc_html_e( 'Erstellte PDFs', 'wc-professional-catalog' ); ?></h3>
	<table class="widefat striped wcpc-pdf-list" id="wcpc-pdf-list">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Datum', 'wc-professional-catalog' ); ?></th>
				<th><?php esc_html_e( 'Umfang', 'wc-professional-catalog' ); ?></th>
				<th><?php esc_html_e( 'Status', 'wc-professional-catalog' ); ?></th>
				<th><?php esc_html_e( 'Produkte', 'wc-professional-catalog' ); ?></th>
				<th><?php esc_html_e( 'Größe', 'wc-professional-catalog' ); ?></th>
				<th><?php esc_html_e( 'Aktionen', 'wc-professional-catalog' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $jobs ) ) : ?>
				<tr><td colspan="6"><em><?php esc_html_e( 'Noch keine PDFs erstellt.', 'wc-professional-catalog' ); ?></em></td></tr>
			<?php else : ?>
				<?php foreach ( $jobs as $j ) : ?>
					<tr data-job-id="<?php echo esc_attr( $j['id'] ); ?>">
						<td><?php echo esc_html( date_i18n( 'd.m.Y H:i', (int) $j['created'] ) ); ?></td>
						<td><?php echo esc_html( $j['scope']['label'] ?? '' ); ?></td>
						<td><span class="wcpc-pdf-status wcpc-pdf-status--<?php echo esc_attr( $j['status'] ); ?>"><?php echo esc_html( $j['status'] ); ?></span></td>
						<td><?php echo (int) $j['processed']; ?> / <?php echo (int) $j['total_products']; ?></td>
						<td><?php echo $j['file_size'] ? esc_html( size_format( (int) $j['file_size'] ) ) : '—'; ?></td>
						<td>
							<?php if ( 'done' === $j['status'] && $j['file_url'] ) : ?>
								<a class="button button-small" href="<?php echo esc_url( $j['file_url'] ); ?>" download><?php esc_html_e( 'Download', 'wc-professional-catalog' ); ?></a>
							<?php endif; ?>
							<button type="button" class="button button-small button-link-delete wcpc-pdf-delete" data-id="<?php echo esc_attr( $j['id'] ); ?>"><?php esc_html_e( 'Löschen', 'wc-professional-catalog' ); ?></button>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>
