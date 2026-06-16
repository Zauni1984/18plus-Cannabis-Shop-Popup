<?php
/**
 * PDF generation. Tries Dompdf via Composer autoload, falls back to a
 * printable HTML page if the library is missing.
 *
 * @package WCProfessionalCatalog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WCPC_PDF {

	/**
	 * Check whether Dompdf is reachable through Composer autoload.
	 *
	 * @return bool
	 */
	public static function is_dompdf_available() {
		$autoload = WCPC_PLUGIN_DIR . 'vendor/autoload.php';
		if ( ! file_exists( $autoload ) ) {
			return false;
		}
		require_once $autoload;
		return class_exists( '\\Dompdf\\Dompdf' );
	}

	/**
	 * Renders the catalog HTML used for both the PDF and the print page.
	 *
	 * @param array $args Query args.
	 * @return string
	 */
	public static function build_html( $args = array() ) {
		$products = WCPC_Catalog::query( $args );
		$settings = WCPC_Plugin::get_settings();
		$columns  = (int) $settings['pdf_columns'] > 0 ? (int) $settings['pdf_columns'] : 2;
		$per_page = (int) $settings['pdf_per_page'] > 0 ? (int) $settings['pdf_per_page'] : 4;
		$pages    = array_chunk( $products, $per_page );

		ob_start();
		include WCPC_PLUGIN_DIR . 'templates/catalog-pdf.php';
		return ob_get_clean();
	}

	/**
	 * Stream a PDF (or the print fallback) to the browser.
	 *
	 * @param array $args Query args.
	 */
	public static function stream_catalog( $args = array() ) {
		$html     = self::build_html( $args );
		$settings = WCPC_Plugin::get_settings();
		$filename = sanitize_file_name( 'katalog-' . gmdate( 'Y-m-d' ) . '.pdf' );

		if ( self::is_dompdf_available() ) {
			self::stream_dompdf( $html, $filename, $settings );
			return;
		}

		// Fallback: serve a printable HTML page with auto-print JS.
		nocache_headers();
		header( 'Content-Type: text/html; charset=' . get_bloginfo( 'charset' ) );
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- pre-escaped template.
		echo "<script>window.addEventListener('load',function(){setTimeout(function(){window.print();},250);});</script>";
	}

	/**
	 * Stream PDF using Dompdf.
	 *
	 * @param string $html     Catalog HTML.
	 * @param string $filename Suggested filename.
	 * @param array  $settings Plugin settings.
	 */
	private static function stream_dompdf( $html, $filename, $settings ) {
		$dompdf = new \Dompdf\Dompdf(
			array(
				'isHtml5ParserEnabled' => true,
				'isRemoteEnabled'      => true,
				'defaultFont'          => $settings['font_family_body'],
				'chroot'               => array(
					WP_CONTENT_DIR,
					ABSPATH,
				),
			)
		);
		$dompdf->loadHtml( $html, 'UTF-8' );

		$paper = in_array( $settings['pdf_page_size'], array( 'A4', 'A5', 'Letter' ), true )
			? $settings['pdf_page_size']
			: 'A4';
		$orientation = ( 'landscape' === $settings['pdf_orientation'] ) ? 'landscape' : 'portrait';

		$dompdf->setPaper( $paper, $orientation );
		$dompdf->render();
		$dompdf->stream(
			$filename,
			array(
				'Attachment' => 0, // 0 = inline, 1 = force download.
			)
		);
	}
}
