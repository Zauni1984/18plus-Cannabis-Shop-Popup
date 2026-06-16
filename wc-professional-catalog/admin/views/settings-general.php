<?php
/**
 * General settings tab.
 *
 * @package WCProfessionalCatalog
 * @var array $settings
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<table class="form-table">
	<tr>
		<th><label for="wcpc_layout"><?php esc_html_e( 'Standard-Layout', 'wc-professional-catalog' ); ?></label></th>
		<td>
			<select id="wcpc_layout" name="wcpc[layout]">
				<option value="grid" <?php selected( $settings['layout'], 'grid' ); ?>><?php esc_html_e( 'Raster', 'wc-professional-catalog' ); ?></option>
				<option value="flipbook" <?php selected( $settings['layout'], 'flipbook' ); ?>><?php esc_html_e( 'Online-Flipbook', 'wc-professional-catalog' ); ?></option>
				<option value="list" <?php selected( $settings['layout'], 'list' ); ?>><?php esc_html_e( 'Liste', 'wc-professional-catalog' ); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<th><label for="wcpc_columns"><?php esc_html_e( 'Spalten (Online-Raster)', 'wc-professional-catalog' ); ?></label></th>
		<td><input type="number" min="1" max="6" id="wcpc_columns" name="wcpc[columns]" value="<?php echo (int) $settings['columns']; ?>" /></td>
	</tr>
	<tr>
		<th><label for="wcpc_products_per_page"><?php esc_html_e( 'Produkte pro Seite (Flipbook)', 'wc-professional-catalog' ); ?></label></th>
		<td><input type="number" min="1" max="40" id="wcpc_products_per_page" name="wcpc[products_per_page]" value="<?php echo (int) $settings['products_per_page']; ?>" /></td>
	</tr>
	<tr>
		<th><?php esc_html_e( 'Anzeigen', 'wc-professional-catalog' ); ?></th>
		<td>
			<label><input type="checkbox" name="wcpc[show_brand_filter]" value="1" <?php checked( $settings['show_brand_filter'], 1 ); ?> /> <?php esc_html_e( 'Marken-Filter', 'wc-professional-catalog' ); ?></label><br/>
			<label><input type="checkbox" name="wcpc[show_category_filter]" value="1" <?php checked( $settings['show_category_filter'], 1 ); ?> /> <?php esc_html_e( 'Kategorie-Filter', 'wc-professional-catalog' ); ?></label><br/>
			<label><input type="checkbox" name="wcpc[show_tag_filter]" value="1" <?php checked( $settings['show_tag_filter'], 1 ); ?> /> <?php esc_html_e( 'Schlagwort-Filter', 'wc-professional-catalog' ); ?></label><br/>
			<label><input type="checkbox" name="wcpc[show_sku]" value="1" <?php checked( $settings['show_sku'], 1 ); ?> /> <?php esc_html_e( 'Artikelnummer', 'wc-professional-catalog' ); ?></label><br/>
			<label><input type="checkbox" name="wcpc[show_short_desc]" value="1" <?php checked( $settings['show_short_desc'], 1 ); ?> /> <?php esc_html_e( 'Kurzbeschreibung', 'wc-professional-catalog' ); ?></label><br/>
			<label><input type="checkbox" name="wcpc[show_price_gross]" value="1" <?php checked( $settings['show_price_gross'], 1 ); ?> /> <?php esc_html_e( 'Bruttopreis', 'wc-professional-catalog' ); ?></label><br/>
			<label><input type="checkbox" name="wcpc[show_price_net]" value="1" <?php checked( $settings['show_price_net'], 1 ); ?> /> <?php esc_html_e( 'Nettopreis', 'wc-professional-catalog' ); ?></label><br/>
			<label><input type="checkbox" name="wcpc[show_unit_price]" value="1" <?php checked( $settings['show_unit_price'], 1 ); ?> /> <?php esc_html_e( 'Grundpreis (z.B. €/L, €/kg)', 'wc-professional-catalog' ); ?></label><br/>
			<label><input type="checkbox" name="wcpc[enable_buy_link]" value="1" <?php checked( $settings['enable_buy_link'], 1 ); ?> /> <?php esc_html_e( 'Kauf-Link unter jedem Produkt', 'wc-professional-catalog' ); ?></label>
		</td>
	</tr>
	<tr>
		<th><label for="wcpc_buy_button_text"><?php esc_html_e( 'Kauf-Button Beschriftung', 'wc-professional-catalog' ); ?></label></th>
		<td><input type="text" id="wcpc_buy_button_text" name="wcpc[buy_button_text]" value="<?php echo esc_attr( $settings['buy_button_text'] ); ?>" class="regular-text" /></td>
	</tr>
	<tr>
		<th><label for="wcpc_ref_vol"><?php esc_html_e( 'Grundpreis Volumen-Referenz', 'wc-professional-catalog' ); ?></label></th>
		<td>
			<input type="number" min="1" id="wcpc_ref_vol" name="wcpc[reference_volume_ml]" value="<?php echo (int) $settings['reference_volume_ml']; ?>" /> ml
			<p class="description"><?php esc_html_e( 'Beispiel 1000 = Grundpreis pro 1 Liter (750 ml wird automatisch hochgerechnet).', 'wc-professional-catalog' ); ?></p>
		</td>
	</tr>
	<tr>
		<th><label for="wcpc_ref_weight"><?php esc_html_e( 'Grundpreis Gewicht-Referenz', 'wc-professional-catalog' ); ?></label></th>
		<td>
			<input type="number" min="1" id="wcpc_ref_weight" name="wcpc[reference_weight_g]" value="<?php echo (int) $settings['reference_weight_g']; ?>" /> g
			<p class="description"><?php esc_html_e( 'Beispiel 1000 = Grundpreis pro 1 kg.', 'wc-professional-catalog' ); ?></p>
		</td>
	</tr>
	<tr>
		<th><label for="wcpc_tax_fallback"><?php esc_html_e( 'MwSt.-Fallback %', 'wc-professional-catalog' ); ?></label></th>
		<td>
			<input type="number" min="0" max="30" id="wcpc_tax_fallback" name="wcpc[tax_rate_fallback]" value="<?php echo (int) $settings['tax_rate_fallback']; ?>" />
			<p class="description"><?php esc_html_e( 'Wird verwendet, falls in WooCommerce keine Steuer aktiviert ist, um brutto/netto auszuweisen.', 'wc-professional-catalog' ); ?></p>
		</td>
	</tr>
</table>
