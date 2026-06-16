<?php
/**
 * Catalog query + rendering.
 *
 * @package WCProfessionalCatalog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCPC_Catalog {

	/**
	 * Query products honoring filter args.
	 *
	 * @param array $args {
	 *     @type string $category Comma list of category slugs or IDs.
	 *     @type string $tag      Comma list of tag slugs or IDs.
	 *     @type string $brand    Comma list of brand slugs or IDs.
	 *     @type string $ids      Comma list of product IDs.
	 *     @type int    $limit    Optional max amount (-1 = all).
	 *     @type string $orderby  Sort field.
	 *     @type string $order    ASC | DESC.
	 * }
	 * @return WC_Product[]
	 */
	public static function query( $args = array() ) {
		$defaults = array(
			'category' => '',
			'tag'      => '',
			'brand'    => '',
			'ids'      => '',
			'limit'    => -1,
			'orderby'  => 'menu_order',
			'order'    => 'ASC',
			'status'   => 'publish',
		);
		$args = wp_parse_args( $args, $defaults );

		$query_args = array(
			'limit'   => (int) $args['limit'],
			'status'  => $args['status'],
			'orderby' => $args['orderby'],
			'order'   => $args['order'],
			'return'  => 'objects',
		);

		if ( ! empty( $args['ids'] ) ) {
			$query_args['include'] = wp_parse_id_list( $args['ids'] );
		}

		if ( ! empty( $args['category'] ) ) {
			$query_args['category'] = self::split_terms( $args['category'] );
		}

		if ( ! empty( $args['tag'] ) ) {
			$query_args['tag'] = self::split_terms( $args['tag'] );
		}

		if ( ! empty( $args['brand'] ) ) {
			$query_args['tax_query'] = self::build_brand_tax_query( $args['brand'] );
		}

		$products = wc_get_products( $query_args );

		return is_array( $products ) ? $products : array();
	}

	/**
	 * Render catalog grid HTML (used by shortcode + frontend).
	 *
	 * @param array $args Same as query() + render options.
	 * @return string
	 */
	public static function render_grid( $args = array() ) {
		$products = self::query( $args );
		$settings = WCPC_Plugin::get_settings();
		$columns  = isset( $args['columns'] ) && (int) $args['columns'] > 0
			? (int) $args['columns']
			: (int) $settings['columns'];

		ob_start();
		include WCPC_PLUGIN_DIR . 'templates/catalog-grid.php';
		return ob_get_clean();
	}

	/**
	 * Render flipbook HTML (online catalog with page turn).
	 *
	 * @param array $args Render args.
	 * @return string
	 */
	public static function render_flipbook( $args = array() ) {
		$products = self::query( $args );
		$settings = WCPC_Plugin::get_settings();
		$per_page = isset( $args['per_page'] ) && (int) $args['per_page'] > 0
			? (int) $args['per_page']
			: (int) $settings['products_per_page'];

		$pages = array_chunk( $products, max( 1, $per_page ) );

		ob_start();
		include WCPC_PLUGIN_DIR . 'templates/catalog-flipbook.php';
		return ob_get_clean();
	}

	/**
	 * Render a standalone flipbook page (own HTML doc) for the endpoint.
	 *
	 * @param array $args Query args.
	 */
	public static function render_flipbook_page( $args = array() ) {
		WCPC_Assets::enqueue_frontend();
		$content = self::render_flipbook( $args );

		get_header();
		echo '<div class="wcpc-endpoint-wrap">';
		// $content is built from internal templates; every dynamic value is escaped
		// at the source (esc_html/esc_attr/esc_url/wc_price). wp_kses_post would strip
		// the data-* attributes the flipbook JS relies on.
		echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- pre-escaped template.
		echo '</div>';
		get_footer();
	}

	/**
	 * Render the print-ready catalog as its own minimal HTML page.
	 *
	 * @param array $args Query args.
	 */
	public static function render_print_page( $args = array() ) {
		$products = self::query( $args );
		$settings = WCPC_Plugin::get_settings();
		$columns  = isset( $args['columns'] ) && (int) $args['columns'] > 0
			? (int) $args['columns']
			: (int) $settings['pdf_columns'];

		nocache_headers();
		header( 'Content-Type: text/html; charset=' . get_bloginfo( 'charset' ) );

		include WCPC_PLUGIN_DIR . 'templates/catalog-print.php';
	}

	/**
	 * Helper - split a CSV term list, accept IDs or slugs.
	 *
	 * @param string $value CSV input.
	 * @return array
	 */
	private static function split_terms( $value ) {
		$items = array_filter( array_map( 'trim', explode( ',', (string) $value ) ) );
		$out   = array();
		foreach ( $items as $item ) {
			$out[] = is_numeric( $item ) ? (int) $item : sanitize_title( $item );
		}
		return $out;
	}

	/**
	 * Build a tax_query for brand taxonomies. We try the most common brand
	 * taxonomies in order until we find one that exists.
	 *
	 * @param string $value CSV brand IDs/slugs.
	 * @return array
	 */
	private static function build_brand_tax_query( $value ) {
		$brands     = self::split_terms( $value );
		$candidates = array(
			'product_brand',   // YITH / Perfect Brands.
			'pwb-brand',       // Perfect WooCommerce Brands legacy.
			'pa_brand',        // Custom product attribute.
		);

		$tax = '';
		foreach ( $candidates as $c ) {
			if ( taxonomy_exists( $c ) ) {
				$tax = $c;
				break;
			}
		}

		if ( ! $tax ) {
			return array();
		}

		$field = '';
		foreach ( $brands as $b ) {
			if ( is_string( $b ) ) {
				$field = 'slug';
				break;
			}
		}
		if ( ! $field ) {
			$field = 'term_id';
		}

		return array(
			array(
				'taxonomy' => $tax,
				'field'    => $field,
				'terms'    => $brands,
				'operator' => 'IN',
			),
		);
	}

	/**
	 * Resolve a brand label for a product (first matching taxonomy term).
	 *
	 * @param WC_Product $product Product.
	 * @return string
	 */
	public static function get_brand_label( $product ) {
		$candidates = array( 'product_brand', 'pwb-brand', 'pa_brand' );
		foreach ( $candidates as $tax ) {
			if ( ! taxonomy_exists( $tax ) ) {
				continue;
			}
			$terms = get_the_terms( $product->get_id(), $tax );
			if ( is_array( $terms ) && ! empty( $terms ) ) {
				return (string) $terms[0]->name;
			}
		}
		return '';
	}

	/**
	 * Build inline CSS variables based on settings, for live theming.
	 *
	 * @param array $settings Plugin settings.
	 * @return string
	 */
	public static function build_css_variables( $settings ) {
		$vars = array(
			'--wcpc-color-primary'   => self::safe_color( $settings['color_primary'] ),
			'--wcpc-color-secondary' => self::safe_color( $settings['color_secondary'] ),
			'--wcpc-color-accent'    => self::safe_color( $settings['color_accent'] ),
			'--wcpc-color-bg'        => self::safe_color( $settings['color_bg'] ),
			'--wcpc-color-text'      => self::safe_color( $settings['color_text'] ),
			'--wcpc-color-muted'     => self::safe_color( $settings['color_muted'] ),
			'--wcpc-price-color'     => self::safe_color( $settings['price_color'] ),

			'--wcpc-font-title'      => self::safe_css_word( $settings['font_family_title'] ),
			'--wcpc-size-title'      => (int) $settings['font_size_title'] . 'px',
			'--wcpc-weight-title'    => self::safe_css_word( $settings['font_weight_title'] ),
			'--wcpc-style-title'     => self::safe_css_word( $settings['font_style_title'] ),

			'--wcpc-font-body'       => self::safe_css_word( $settings['font_family_body'] ),
			'--wcpc-size-body'       => (int) $settings['font_size_body'] . 'px',
			'--wcpc-weight-body'     => self::safe_css_word( $settings['font_weight_body'] ),
			'--wcpc-style-body'      => self::safe_css_word( $settings['font_style_body'] ),

			'--wcpc-font-price'      => self::safe_css_word( $settings['font_family_price'] ),
			'--wcpc-size-price'      => (int) $settings['font_size_price'] . 'px',
			'--wcpc-weight-price'    => self::safe_css_word( $settings['font_weight_price'] ),
			'--wcpc-style-price'     => self::safe_css_word( $settings['font_style_price'] ),
		);

		$css = ':root, .wcpc-catalog, .wcpc-pdf{';
		foreach ( $vars as $k => $v ) {
			$css .= $k . ':' . $v . ';';
		}
		$css .= '}';
		return $css;
	}

	/**
	 * Restrict a value to characters legal in a hex color (defense-in-depth on top of
	 * the admin sanitizer in WCPC_Admin::sanitize_color()).
	 *
	 * @param string $val Stored color value.
	 * @return string
	 */
	private static function safe_color( $val ) {
		return preg_match( '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', (string) $val ) ? $val : '#000000';
	}

	/**
	 * Strip a value down to characters that are legal in a CSS identifier / font name.
	 * Used only as a fallback - the admin form ships these via <select> with a
	 * whitelist - but we never want stored text to leak control characters into the
	 * stylesheet (no semicolons, braces, angle brackets, quotes).
	 *
	 * @param string $val Stored value.
	 * @return string
	 */
	private static function safe_css_word( $val ) {
		$clean = preg_replace( '/[^A-Za-z0-9 _\-]/', '', (string) $val );
		return '' === $clean ? 'Helvetica' : $clean;
	}
}
