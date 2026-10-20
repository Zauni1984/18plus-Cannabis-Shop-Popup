<?php
/**
 * Plugin Name: Hanfjack OG-Hauptbild
 * Description: Gibt auf Produktseiten nur das Hauptbild als Vorschaubild (og:image / twitter:image) aus, damit WhatsApp, Facebook und Co. immer das Hauptbild zeigen.
 * Version: 1.0.0
 * Author: SZM Trade & IT Solutions e.K.
 * Requires Plugins: wordpress-seo
 */

defined( 'ABSPATH' ) || exit;

/**
 * Liefert das Hauptbild eines Produkts im Yoast-Bildformat oder null.
 */
function hj_og_main_image( $post_id ) {
	$thumb_id = (int) get_post_thumbnail_id( $post_id );
	if ( ! $thumb_id ) {
		return null;
	}
	$src = wp_get_attachment_image_src( $thumb_id, 'full' );
	if ( ! $src ) {
		return null;
	}
	return array(
		'id'     => $thumb_id,
		'url'    => $src[0],
		'width'  => (int) $src[1],
		'height' => (int) $src[2],
		'type'   => get_post_mime_type( $thumb_id ),
	);
}

/**
 * Reduziert die Open-Graph-Bilder auf das Hauptbild. Ohne Hauptbild bleibt das erste vorhandene Bild.
 */
add_filter(
	'wpseo_frontend_presentation',
	function ( $presentation ) {
		if ( ! is_singular( 'product' ) ) {
			return $presentation;
		}
		$main   = hj_og_main_image( get_queried_object_id() );
		$images = $presentation->open_graph_images;
		if ( $main ) {
			$presentation->open_graph_images = array( $main['url'] => $main );
		} elseif ( is_array( $images ) && count( $images ) > 1 ) {
			$presentation->open_graph_images = array_slice( $images, 0, 1, true );
		}
		return $presentation;
	},
	20
);

/**
 * Twitter/X-Karte ebenfalls auf das Hauptbild setzen.
 */
add_filter(
	'wpseo_twitter_image',
	function ( $image ) {
		if ( is_singular( 'product' ) ) {
			$main = hj_og_main_image( get_queried_object_id() );
			if ( $main ) {
				return $main['url'];
			}
		}
		return $image;
	}
);
