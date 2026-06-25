<?php
/**
 * Design tab.
 *
 * @package WCProfessionalCatalog
 * @var array $settings
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$logo_id  = (int) $settings['logo_id'];
$logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';
?>
<table class="form-table">
	<tr>
		<th><label><?php esc_html_e( 'Logo', 'wc-professional-catalog' ); ?></label></th>
		<td>
			<div class="wcpc-logo-picker">
				<img src="<?php echo esc_url( $logo_url ); ?>" id="wcpc-logo-preview" style="max-height:80px;<?php echo $logo_url ? '' : 'display:none;'; ?>" />
				<input type="hidden" id="wcpc_logo_id" name="wcpc[logo_id]" value="<?php echo (int) $logo_id; ?>" />
				<button type="button" class="button" id="wcpc-logo-pick"><?php esc_html_e( 'Logo wählen', 'wc-professional-catalog' ); ?></button>
				<button type="button" class="button" id="wcpc-logo-remove"><?php esc_html_e( 'Entfernen', 'wc-professional-catalog' ); ?></button>
			</div>
		</td>
	</tr>
	<tr>
		<th><label for="wcpc_cover_title"><?php esc_html_e( 'Titel auf der Cover-Seite', 'wc-professional-catalog' ); ?></label></th>
		<td><input type="text" id="wcpc_cover_title" name="wcpc[cover_title]" value="<?php echo esc_attr( $settings['cover_title'] ); ?>" class="regular-text" /></td>
	</tr>
	<tr>
		<th><label for="wcpc_cover_subtitle"><?php esc_html_e( 'Untertitel', 'wc-professional-catalog' ); ?></label></th>
		<td><input type="text" id="wcpc_cover_subtitle" name="wcpc[cover_subtitle]" value="<?php echo esc_attr( $settings['cover_subtitle'] ); ?>" class="regular-text" /></td>
	</tr>
	<tr>
		<th><label for="wcpc_footer_text"><?php esc_html_e( 'Fußzeile', 'wc-professional-catalog' ); ?></label></th>
		<td><input type="text" id="wcpc_footer_text" name="wcpc[footer_text]" value="<?php echo esc_attr( $settings['footer_text'] ); ?>" class="regular-text" /></td>
	</tr>
	<?php
	$color_fields = array(
		'color_primary'   => __( 'Primärfarbe', 'wc-professional-catalog' ),
		'color_secondary' => __( 'Sekundärfarbe', 'wc-professional-catalog' ),
		'color_accent'    => __( 'Akzentfarbe', 'wc-professional-catalog' ),
		'color_bg'        => __( 'Hintergrund', 'wc-professional-catalog' ),
		'color_text'      => __( 'Textfarbe', 'wc-professional-catalog' ),
		'color_muted'     => __( 'Dezenter Text', 'wc-professional-catalog' ),
		'price_color'     => __( 'Preisfarbe', 'wc-professional-catalog' ),
	);
	foreach ( $color_fields as $key => $label ) : ?>
		<tr>
			<th><label for="wcpc_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
			<td><input type="text" class="wcpc-color" id="wcpc_<?php echo esc_attr( $key ); ?>" name="wcpc[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $settings[ $key ] ); ?>" /></td>
		</tr>
	<?php endforeach; ?>
</table>
