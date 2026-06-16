<?php
/**
 * Shortcodes.
 *
 *  [wcpc_catalog layout="grid|flipbook|list" category="..." tag="..." brand="..." ids="..." columns="3" limit="-1"]
 *  [wcpc_catalog_buttons pdf="1" print="1" flipbook="1"]
 *
 * @package WCProfessionalCatalog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCPC_Shortcode {

	public static function init() {
		add_shortcode( 'wcpc_catalog', array( __CLASS__, 'render_catalog' ) );
		add_shortcode( 'wcpc_catalog_buttons', array( __CLASS__, 'render_buttons' ) );
	}

	public static function render_catalog( $atts ) {
		$atts = shortcode_atts(
			array(
				'layout'   => '',
				'category' => '',
				'tag'      => '',
				'brand'    => '',
				'ids'      => '',
				'columns'  => '',
				'limit'    => -1,
				'orderby'  => 'menu_order',
				'order'    => 'ASC',
				'per_page' => '',
			),
			$atts,
			'wcpc_catalog'
		);

		$settings = WCPC_Plugin::get_settings();
		$layout   = $atts['layout'] ?: $settings['layout'];

		WCPC_Assets::enqueue_frontend();

		if ( 'flipbook' === $layout ) {
			return WCPC_Catalog::render_flipbook( $atts );
		}
		// Grid + list both use the grid template; list just sets columns=1.
		if ( 'list' === $layout ) {
			$atts['columns'] = 1;
		}
		return WCPC_Catalog::render_grid( $atts );
	}

	public static function render_buttons( $atts ) {
		$atts = shortcode_atts(
			array(
				'pdf'      => 1,
				'print'    => 1,
				'flipbook' => 1,
				'category' => '',
				'tag'      => '',
				'brand'    => '',
				'label_pdf'      => __( 'PDF herunterladen', 'wc-professional-catalog' ),
				'label_print'    => __( 'Druckansicht', 'wc-professional-catalog' ),
				'label_flipbook' => __( 'Online-Katalog', 'wc-professional-catalog' ),
			),
			$atts,
			'wcpc_catalog_buttons'
		);

		$base = home_url( '/wc-catalog/' );
		$suffix = '';
		foreach ( array( 'brand', 'category', 'tag' ) as $f ) {
			if ( ! empty( $atts[ $f ] ) ) {
				$suffix = $f . '/' . sanitize_title( $atts[ $f ] ) . '/';
				break;
			}
		}

		WCPC_Assets::enqueue_frontend();

		ob_start();
		echo '<div class="wcpc-catalog-buttons">';
		if ( (int) $atts['flipbook'] ) {
			printf(
				'<a class="wcpc-btn wcpc-btn-primary" href="%s">%s</a>',
				esc_url( $base . 'flipbook/' . $suffix ),
				esc_html( $atts['label_flipbook'] )
			);
		}
		if ( (int) $atts['pdf'] ) {
			printf(
				'<a class="wcpc-btn wcpc-btn-secondary" href="%s">%s</a>',
				esc_url( $base . 'pdf/' . $suffix ),
				esc_html( $atts['label_pdf'] )
			);
		}
		if ( (int) $atts['print'] ) {
			printf(
				'<a class="wcpc-btn wcpc-btn-ghost" target="_blank" href="%s">%s</a>',
				esc_url( $base . 'print/' . $suffix ),
				esc_html( $atts['label_print'] )
			);
		}
		echo '</div>';
		return ob_get_clean();
	}
}
