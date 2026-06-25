<?php
/**
 * Typography tab.
 *
 * @package WCProfessionalCatalog
 * @var array $settings
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$fonts = array(
	'Helvetica'       => 'Helvetica / Arial',
	'Times'           => 'Times New Roman',
	'Courier'         => 'Courier',
	'Georgia'         => 'Georgia',
	'Verdana'         => 'Verdana',
	'Tahoma'          => 'Tahoma',
	'Trebuchet MS'    => 'Trebuchet MS',
	'Palatino'        => 'Palatino',
	'Garamond'        => 'Garamond',
);

$weights = array(
	'300' => 'Light',
	'400' => 'Normal',
	'500' => 'Medium',
	'600' => 'Semibold',
	'700' => 'Bold',
	'800' => 'Extrabold',
);

$styles = array(
	'normal'  => 'normal',
	'italic'  => 'italic',
	'oblique' => 'oblique',
);

$blocks = array(
	'title' => __( 'Produkt-Titel', 'wc-professional-catalog' ),
	'body'  => __( 'Kurzbeschreibung / Fließtext', 'wc-professional-catalog' ),
	'price' => __( 'Preis', 'wc-professional-catalog' ),
);
?>
<p class="description"><?php esc_html_e( 'Schriftart, Größe, Gewicht und Stil können für Titel, Beschreibung und Preis getrennt eingestellt werden.', 'wc-professional-catalog' ); ?></p>

<table class="form-table">
	<?php foreach ( $blocks as $slug => $label ) : ?>
		<tr>
			<th colspan="2"><h3 style="margin:0"><?php echo esc_html( $label ); ?></h3></th>
		</tr>
		<tr>
			<th><label><?php esc_html_e( 'Schriftart', 'wc-professional-catalog' ); ?></label></th>
			<td>
				<select name="wcpc[font_family_<?php echo esc_attr( $slug ); ?>]">
					<?php foreach ( $fonts as $k => $v ) : ?>
						<option value="<?php echo esc_attr( $k ); ?>" <?php selected( $settings[ 'font_family_' . $slug ], $k ); ?>><?php echo esc_html( $v ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<th><label><?php esc_html_e( 'Schriftgröße', 'wc-professional-catalog' ); ?></label></th>
			<td><input type="number" min="6" max="72" name="wcpc[font_size_<?php echo esc_attr( $slug ); ?>]" value="<?php echo (int) $settings[ 'font_size_' . $slug ]; ?>" /> px</td>
		</tr>
		<tr>
			<th><label><?php esc_html_e( 'Schriftstärke', 'wc-professional-catalog' ); ?></label></th>
			<td>
				<select name="wcpc[font_weight_<?php echo esc_attr( $slug ); ?>]">
					<?php foreach ( $weights as $k => $v ) : ?>
						<option value="<?php echo esc_attr( $k ); ?>" <?php selected( $settings[ 'font_weight_' . $slug ], $k ); ?>><?php echo esc_html( $v ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<th><label><?php esc_html_e( 'Stil', 'wc-professional-catalog' ); ?></label></th>
			<td>
				<select name="wcpc[font_style_<?php echo esc_attr( $slug ); ?>]">
					<?php foreach ( $styles as $k => $v ) : ?>
						<option value="<?php echo esc_attr( $k ); ?>" <?php selected( $settings[ 'font_style_' . $slug ], $k ); ?>><?php echo esc_html( $v ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
	<?php endforeach; ?>
</table>
