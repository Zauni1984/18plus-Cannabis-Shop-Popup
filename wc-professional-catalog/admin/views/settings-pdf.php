<?php
/**
 * PDF tab.
 *
 * @package WCProfessionalCatalog
 * @var array $settings
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<table class="form-table">
	<tr>
		<th><label for="wcpc_pdf_page_size"><?php esc_html_e( 'Papierformat', 'wc-professional-catalog' ); ?></label></th>
		<td>
			<select id="wcpc_pdf_page_size" name="wcpc[pdf_page_size]">
				<?php foreach ( array( 'A4', 'A5', 'Letter' ) as $size ) : ?>
					<option value="<?php echo esc_attr( $size ); ?>" <?php selected( $settings['pdf_page_size'], $size ); ?>><?php echo esc_html( $size ); ?></option>
				<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<tr>
		<th><label for="wcpc_pdf_orientation"><?php esc_html_e( 'Ausrichtung', 'wc-professional-catalog' ); ?></label></th>
		<td>
			<select id="wcpc_pdf_orientation" name="wcpc[pdf_orientation]">
				<option value="portrait" <?php selected( $settings['pdf_orientation'], 'portrait' ); ?>><?php esc_html_e( 'Hochformat', 'wc-professional-catalog' ); ?></option>
				<option value="landscape" <?php selected( $settings['pdf_orientation'], 'landscape' ); ?>><?php esc_html_e( 'Querformat', 'wc-professional-catalog' ); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<th><label for="wcpc_pdf_columns"><?php esc_html_e( 'Spalten pro Seite', 'wc-professional-catalog' ); ?></label></th>
		<td><input type="number" min="1" max="4" id="wcpc_pdf_columns" name="wcpc[pdf_columns]" value="<?php echo (int) $settings['pdf_columns']; ?>" /></td>
	</tr>
	<tr>
		<th><label for="wcpc_pdf_per_page"><?php esc_html_e( 'Produkte pro PDF-Seite', 'wc-professional-catalog' ); ?></label></th>
		<td><input type="number" min="1" max="20" id="wcpc_pdf_per_page" name="wcpc[pdf_per_page]" value="<?php echo (int) $settings['pdf_per_page']; ?>" /></td>
	</tr>
	<tr>
		<th><label for="wcpc_pdf_margin"><?php esc_html_e( 'Seitenrand (mm)', 'wc-professional-catalog' ); ?></label></th>
		<td><input type="number" min="0" max="40" id="wcpc_pdf_margin" name="wcpc[pdf_margin]" value="<?php echo (int) $settings['pdf_margin']; ?>" /></td>
	</tr>
	<tr>
		<th><?php esc_html_e( 'Extras', 'wc-professional-catalog' ); ?></th>
		<td>
			<label><input type="checkbox" name="wcpc[pdf_show_qr]" value="1" <?php checked( $settings['pdf_show_qr'], 1 ); ?> /> <?php esc_html_e( 'QR-Code zu jedem Produkt (Druckkatalog) - erfordert Add-on', 'wc-professional-catalog' ); ?></label>
		</td>
	</tr>
	<tr>
		<th><?php esc_html_e( 'PDF-Bibliothek', 'wc-professional-catalog' ); ?></th>
		<td>
			<?php if ( WCPC_PDF::is_dompdf_available() ) : ?>
				<span style="color:#1f7a3a;">&#10003; <?php esc_html_e( 'Dompdf erkannt - PDF wird nativ erzeugt.', 'wc-professional-catalog' ); ?></span>
			<?php else : ?>
				<p>
					<?php esc_html_e( 'Dompdf nicht gefunden - PDF-Endpoint liefert eine druckbare HTML-Seite mit Autostart-Druck.', 'wc-professional-catalog' ); ?><br/>
					<?php
					printf(
						/* translators: %s: composer command */
						esc_html__( 'Im Plugin-Ordner ausführen: %s', 'wc-professional-catalog' ),
						'<code>composer require dompdf/dompdf</code>'
					);
					?>
				</p>
			<?php endif; ?>
		</td>
	</tr>
</table>
