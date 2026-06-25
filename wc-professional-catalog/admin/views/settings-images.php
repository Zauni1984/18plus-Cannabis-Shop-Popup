<?php
/**
 * Image settings tab.
 *
 * @package WCProfessionalCatalog
 * @var array $settings
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$cover_id  = (int) $settings['cover_image_id'];
$cover_url = $cover_id ? wp_get_attachment_image_url( $cover_id, 'medium' ) : '';

$sizes      = array();
$registered = function_exists( 'wp_get_registered_image_subsizes' )
	? wp_get_registered_image_subsizes()
	: array(
		'thumbnail' => array(),
		'medium'    => array(),
		'large'     => array(),
	);
foreach ( array_keys( $registered ) as $size ) {
	$sizes[ $size ] = $size;
}
$sizes['woocommerce_thumbnail'] = 'woocommerce_thumbnail';
$sizes['woocommerce_single']    = 'woocommerce_single';
$sizes['full']                  = 'full (original)';
?>
<table class="form-table">
	<tr>
		<th><label><?php esc_html_e( 'Cover-Bild (Katalog-Titelseite)', 'wc-professional-catalog' ); ?></label></th>
		<td>
			<div class="wcpc-logo-picker">
				<img src="<?php echo esc_url( $cover_url ); ?>" id="wcpc-cover-preview" style="max-height:120px;<?php echo $cover_url ? '' : 'display:none;'; ?>" />
				<input type="hidden" id="wcpc_cover_image_id" name="wcpc[cover_image_id]" value="<?php echo (int) $cover_id; ?>" />
				<button type="button" class="button" id="wcpc-cover-pick"><?php esc_html_e( 'Cover-Bild wählen', 'wc-professional-catalog' ); ?></button>
				<button type="button" class="button" id="wcpc-cover-remove"><?php esc_html_e( 'Entfernen', 'wc-professional-catalog' ); ?></button>
			</div>
			<p class="description"><?php esc_html_e( 'Vollflächiges Hintergrundbild der Titelseite (Online-Flipbook, PDF, Druckkatalog). Wenn leer, wird nur das Logo angezeigt.', 'wc-professional-catalog' ); ?></p>
		</td>
	</tr>

	<tr>
		<th><label for="wcpc_image_size_online"><?php esc_html_e( 'Bildgröße (Online)', 'wc-professional-catalog' ); ?></label></th>
		<td>
			<select id="wcpc_image_size_online" name="wcpc[image_size_online]">
				<?php foreach ( $sizes as $k => $label ) : ?>
					<option value="<?php echo esc_attr( $k ); ?>" <?php selected( $settings['image_size_online'], $k ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
			<p class="description"><?php esc_html_e( 'Welche WordPress/WooCommerce-Bildgröße im Online-Katalog verwendet wird.', 'wc-professional-catalog' ); ?></p>
		</td>
	</tr>
	<tr>
		<th><label for="wcpc_image_size_pdf"><?php esc_html_e( 'Bildgröße (PDF)', 'wc-professional-catalog' ); ?></label></th>
		<td>
			<select id="wcpc_image_size_pdf" name="wcpc[image_size_pdf]">
				<?php foreach ( $sizes as $k => $label ) : ?>
					<option value="<?php echo esc_attr( $k ); ?>" <?php selected( $settings['image_size_pdf'], $k ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
			<p class="description"><?php esc_html_e( 'Größere Bilder = schärferer Druck, aber größere PDF-Datei.', 'wc-professional-catalog' ); ?></p>
		</td>
	</tr>

	<tr>
		<th><label for="wcpc_image_aspect"><?php esc_html_e( 'Seitenverhältnis', 'wc-professional-catalog' ); ?></label></th>
		<td>
			<select id="wcpc_image_aspect" name="wcpc[image_aspect]">
				<option value="4-3"      <?php selected( $settings['image_aspect'], '4-3' ); ?>>4:3</option>
				<option value="1-1"      <?php selected( $settings['image_aspect'], '1-1' ); ?>>1:1 (Quadrat)</option>
				<option value="16-9"     <?php selected( $settings['image_aspect'], '16-9' ); ?>>16:9</option>
				<option value="3-4"      <?php selected( $settings['image_aspect'], '3-4' ); ?>>3:4 (Portrait)</option>
				<option value="natural"  <?php selected( $settings['image_aspect'], 'natural' ); ?>><?php esc_html_e( 'Original', 'wc-professional-catalog' ); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<th><label for="wcpc_image_style"><?php esc_html_e( 'Bildstil', 'wc-professional-catalog' ); ?></label></th>
		<td>
			<select id="wcpc_image_style" name="wcpc[image_style]">
				<option value="flat"    <?php selected( $settings['image_style'], 'flat' ); ?>><?php esc_html_e( 'Flat', 'wc-professional-catalog' ); ?></option>
				<option value="rounded" <?php selected( $settings['image_style'], 'rounded' ); ?>><?php esc_html_e( 'Abgerundete Ecken', 'wc-professional-catalog' ); ?></option>
				<option value="circle"  <?php selected( $settings['image_style'], 'circle' ); ?>><?php esc_html_e( 'Kreis', 'wc-professional-catalog' ); ?></option>
				<option value="shadow"  <?php selected( $settings['image_style'], 'shadow' ); ?>><?php esc_html_e( 'Mit Schatten', 'wc-professional-catalog' ); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<th><label for="wcpc_image_position"><?php esc_html_e( 'Bildposition', 'wc-professional-catalog' ); ?></label></th>
		<td>
			<select id="wcpc_image_position" name="wcpc[image_position]">
				<option value="top"  <?php selected( $settings['image_position'], 'top' ); ?>><?php esc_html_e( 'Oben', 'wc-professional-catalog' ); ?></option>
				<option value="left" <?php selected( $settings['image_position'], 'left' ); ?>><?php esc_html_e( 'Links neben Text', 'wc-professional-catalog' ); ?></option>
			</select>
		</td>
	</tr>

	<tr>
		<th colspan="2"><h3 style="margin:18px 0 0"><?php esc_html_e( 'Galerie-Thumbnails', 'wc-professional-catalog' ); ?></h3></th>
	</tr>
	<tr>
		<th><?php esc_html_e( 'Online', 'wc-professional-catalog' ); ?></th>
		<td>
			<label><input type="checkbox" name="wcpc[show_gallery]" value="1" <?php checked( $settings['show_gallery'], 1 ); ?> /> <?php esc_html_e( 'Galerie-Thumbnails unter dem Hauptbild anzeigen', 'wc-professional-catalog' ); ?></label>
			<br/>
			<label><?php esc_html_e( 'Anzahl', 'wc-professional-catalog' ); ?>: <input type="number" min="1" max="6" name="wcpc[gallery_count]" value="<?php echo (int) $settings['gallery_count']; ?>" style="width:80px;" /></label>
		</td>
	</tr>
	<tr>
		<th><?php esc_html_e( 'PDF', 'wc-professional-catalog' ); ?></th>
		<td>
			<label><input type="checkbox" name="wcpc[pdf_show_gallery]" value="1" <?php checked( $settings['pdf_show_gallery'], 1 ); ?> /> <?php esc_html_e( 'Galerie-Thumbnails im PDF', 'wc-professional-catalog' ); ?></label>
			<br/>
			<label><?php esc_html_e( 'Anzahl', 'wc-professional-catalog' ); ?>: <input type="number" min="1" max="4" name="wcpc[pdf_gallery_count]" value="<?php echo (int) $settings['pdf_gallery_count']; ?>" style="width:80px;" /></label>
		</td>
	</tr>

	<tr>
		<th colspan="2"><h3 style="margin:18px 0 0"><?php esc_html_e( 'Interaktion', 'wc-professional-catalog' ); ?></h3></th>
	</tr>
	<tr>
		<th><?php esc_html_e( 'Lightbox', 'wc-professional-catalog' ); ?></th>
		<td>
			<label><input type="checkbox" name="wcpc[enable_lightbox]" value="1" <?php checked( $settings['enable_lightbox'], 1 ); ?> /> <?php esc_html_e( 'Klick auf Produktbild öffnet Vollbild mit Vor-/Zurück und Galerie', 'wc-professional-catalog' ); ?></label>
		</td>
	</tr>
</table>

<script>
jQuery(function($){
	var frame;
	$('#wcpc-cover-pick').on('click', function(e){
		e.preventDefault();
		if (frame){ frame.open(); return; }
		frame = wp.media({title:'Cover-Bild wählen', button:{text:'Verwenden'}, multiple:false, library:{type:'image'}});
		frame.on('select', function(){
			var a = frame.state().get('selection').first().toJSON();
			$('#wcpc_cover_image_id').val(a.id);
			$('#wcpc-cover-preview').attr('src', a.url).show();
		});
		frame.open();
	});
	$('#wcpc-cover-remove').on('click', function(e){
		e.preventDefault();
		$('#wcpc_cover_image_id').val(0);
		$('#wcpc-cover-preview').attr('src','').hide();
	});
});
</script>
