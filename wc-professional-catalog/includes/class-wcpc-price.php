<?php
/**
 * Price helpers: gross/net + unit/base price (Grundpreis).
 *
 * Supports German "Grundpreis" calculation, e.g. a 750 ml product gets the
 * unit price per 1 L automatically. Volume/weight reference values are
 * configurable in the settings.
 *
 * @package WCProfessionalCatalog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCPC_Price {

	/**
	 * Returns gross/net display strings for a product.
	 *
	 * @param WC_Product $product Product.
	 * @return array{net:string, gross:string, raw_net:float, raw_gross:float}
	 */
	public static function get_prices( $product ) {
		$price = (float) $product->get_price();
		if ( $price <= 0 && method_exists( $product, 'get_regular_price' ) ) {
			$price = (float) $product->get_regular_price();
		}

		$tax_display = get_option( 'woocommerce_tax_display_shop', 'incl' );

		if ( wc_tax_enabled() ) {
			$gross = (float) wc_get_price_including_tax( $product, array( 'price' => $price ) );
			$net   = (float) wc_get_price_excluding_tax( $product, array( 'price' => $price ) );
		} else {
			$gross = $price;
			$net   = $price;
			$rate  = (float) WCPC_Plugin::get_setting( 'tax_rate_fallback', 19 );
			if ( $rate > 0 ) {
				if ( 'incl' === $tax_display ) {
					$net = $gross / ( 1 + ( $rate / 100 ) );
				} else {
					$gross = $net * ( 1 + ( $rate / 100 ) );
				}
			}
		}

		return array(
			'raw_net'   => $net,
			'raw_gross' => $gross,
			'net'       => wc_price( $net ),
			'gross'     => wc_price( $gross ),
		);
	}

	/**
	 * Returns the Grundpreis (base/unit price) string.
	 *
	 * Tries multiple sources in order:
	 *   1. Product meta `_unit_amount` + `_unit` (Germanized).
	 *   2. Meta `_wcpc_unit_amount` + `_wcpc_unit` (plugin custom).
	 *   3. Parse the product title / attributes for volume / weight (ml, l, g, kg).
	 *
	 * @param WC_Product $product Product.
	 * @return string Formatted unit-price string, e.g. "12,00 € / 1 L", or empty.
	 */
	public static function get_unit_price( $product ) {
		$prices = self::get_prices( $product );
		$gross  = (float) $prices['raw_gross'];
		if ( $gross <= 0 ) {
			return '';
		}

		$amount = 0.0;
		$unit   = '';

		// 1. Germanized.
		$g_amount = $product->get_meta( '_unit_amount' );
		$g_unit   = $product->get_meta( '_unit' );
		if ( $g_amount && $g_unit ) {
			$amount = (float) $g_amount;
			$unit   = (string) $g_unit;
		}

		// 2. Plugin custom meta.
		if ( ! $amount ) {
			$c_amount = $product->get_meta( '_wcpc_unit_amount' );
			$c_unit   = $product->get_meta( '_wcpc_unit' );
			if ( $c_amount && $c_unit ) {
				$amount = (float) $c_amount;
				$unit   = (string) $c_unit;
			}
		}

		// 3. Try to detect from product title.
		if ( ! $amount ) {
			$detected = self::detect_unit_from_text( $product->get_name() );
			if ( $detected ) {
				$amount = $detected['amount'];
				$unit   = $detected['unit'];
			}
		}

		if ( ! $amount || ! $unit ) {
			return '';
		}

		return self::format_unit_price( $gross, $amount, $unit );
	}

	/**
	 * Detect a unit from arbitrary text (e.g. "Gin 750 ml" -> 750 ml).
	 *
	 * @param string $text Source text.
	 * @return array{amount:float, unit:string}|null
	 */
	public static function detect_unit_from_text( $text ) {
		if ( ! is_string( $text ) || '' === trim( $text ) ) {
			return null;
		}

		// Normalise.
		$haystack = str_replace( ',', '.', strtolower( $text ) );

		// Try common patterns: "750 ml", "0.7 l", "500g", "1 kg".
		$patterns = array(
			'/(\d+(?:\.\d+)?)\s*(ml|cl|dl|l|liter|litre|g|gr|kg|mg)\b/i',
		);

		foreach ( $patterns as $p ) {
			if ( preg_match( $p, $haystack, $m ) ) {
				$amount = (float) $m[1];
				$unit   = strtolower( $m[2] );
				if ( 'liter' === $unit || 'litre' === $unit ) {
					$unit = 'l';
				}
				if ( 'gr' === $unit ) {
					$unit = 'g';
				}
				return array(
					'amount' => $amount,
					'unit'   => $unit,
				);
			}
		}

		return null;
	}

	/**
	 * Build the formatted unit-price string given a gross price, amount and unit.
	 *
	 * @param float  $gross  Gross price.
	 * @param float  $amount Amount.
	 * @param string $unit   Unit (ml, cl, dl, l, mg, g, kg).
	 * @return string
	 */
	public static function format_unit_price( $gross, $amount, $unit ) {
		$unit = strtolower( trim( $unit ) );

		$volume_map = array(
			'ml' => 1,
			'cl' => 10,
			'dl' => 100,
			'l'  => 1000,
		);
		$weight_map = array(
			'mg' => 1,
			'g'  => 1000,
			'kg' => 1000000,
		);

		$ref_volume_ml = (float) WCPC_Plugin::get_setting( 'reference_volume_ml', 1000 );
		$ref_weight_g  = (float) WCPC_Plugin::get_setting( 'reference_weight_g', 1000 );

		if ( isset( $volume_map[ $unit ] ) ) {
			$ml_total = $amount * $volume_map[ $unit ];
			if ( $ml_total <= 0 ) {
				return '';
			}
			$unit_price   = $gross / $ml_total * $ref_volume_ml;
			$ref_label    = self::format_reference_volume( $ref_volume_ml );
			return sprintf( '%s / %s', wc_price( $unit_price ), $ref_label );
		}

		if ( isset( $weight_map[ $unit ] ) ) {
			$g_total = $amount * $weight_map[ $unit ];
			if ( $g_total <= 0 ) {
				return '';
			}
			$unit_price = $gross / $g_total * $ref_weight_g;
			$ref_label  = self::format_reference_weight( $ref_weight_g );
			return sprintf( '%s / %s', wc_price( $unit_price ), $ref_label );
		}

		return '';
	}

	private static function format_reference_volume( $ml ) {
		if ( $ml >= 1000 ) {
			$l = $ml / 1000;
			return ( fmod( $l, 1.0 ) === 0.0 ? (int) $l : $l ) . ' L';
		}
		return ( (int) $ml ) . ' ml';
	}

	private static function format_reference_weight( $g ) {
		if ( $g >= 1000 ) {
			$kg = $g / 1000;
			return ( fmod( $kg, 1.0 ) === 0.0 ? (int) $kg : $kg ) . ' kg';
		}
		return ( (int) $g ) . ' g';
	}
}
