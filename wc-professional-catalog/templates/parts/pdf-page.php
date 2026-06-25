<?php
/**
 * One PDF page (section) for the batched catalog-pdf renderer.
 *
 * Expects in scope:
 *   - $page_products (WC_Product[])
 *   - $page_index (1-based)
 *   - $total_pages (int)
 *   - $columns (int)
 *   - $logo_url (string)
 *   - $pdf_img_size (string)
 *   - $settings (array)
 *
 * @package WCProfessionalCatalog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="wcpc-pdf-page">
	<div class="wcpc-pdf-header">
		<div class="l">
			<?php if ( ! empty( $logo_url ) ) : ?>
				<img src="<?php echo esc_url( $logo_url ); ?>" alt="" />
			<?php endif; ?>
		</div>
		<div class="r">
			<?php
			printf(
				/* translators: 1: current page, 2: total pages */
				esc_html__( 'Seite %1$d von %2$d', 'wc-professional-catalog' ),
				(int) $page_index,
				(int) $total_pages
			);
			?>
		</div>
	</div>
	<table class="wcpc-pdf-grid">
		<?php
		$chunked = array_chunk( $page_products, max( 1, (int) $columns ) );
		foreach ( $chunked as $row ) :
			?>
			<tr>
			<?php
			foreach ( $row as $product ) :
				if ( ! $product instanceof WC_Product ) {
					continue;
				}
				$prices      = WCPC_Price::get_prices( $product );
				$unit_price  = WCPC_Price::get_unit_price( $product );
				$brand       = WCPC_Catalog::get_brand_label( $product );
				$image       = wp_get_attachment_image_url( $product->get_image_id(), $pdf_img_size );
				$short       = wp_strip_all_tags( (string) $product->get_short_description() );
				$link        = (string) get_permalink( $product->get_id() );
				$pdf_gallery = ! empty( $settings['pdf_show_gallery'] ) ? (array) $product->get_gallery_image_ids() : array();
				if ( $pdf_gallery ) {
					$pdf_gallery = array_slice( $pdf_gallery, 0, max( 1, (int) ( $settings['pdf_gallery_count'] ?? 2 ) ) );
				}
				?>
				<td>
					<div class="wcpc-pdf-item">
						<?php if ( $image ) : ?>
							<a class="img-link" href="<?php echo esc_url( $link ); ?>">
								<img src="<?php echo esc_url( $image ); ?>" alt="" />
							</a>
						<?php endif; ?>
						<?php if ( '' !== $brand ) : ?>
							<div class="brand"><?php echo esc_html( $brand ); ?></div>
						<?php endif; ?>
						<div class="title">
							<a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
						</div>
						<?php if ( $pdf_gallery ) : ?>
							<div class="pdf-gallery">
								<?php
								foreach ( $pdf_gallery as $gid ) :
									$thumb = wp_get_attachment_image_url( (int) $gid, 'thumbnail' );
									if ( ! $thumb ) {
										continue;
									}
									?>
									<img src="<?php echo esc_url( $thumb ); ?>" alt="" />
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
						<?php if ( ! empty( $settings['show_sku'] ) && $product->get_sku() ) : ?>
							<div class="sku">Art.-Nr.: <?php echo esc_html( $product->get_sku() ); ?></div>
						<?php endif; ?>
						<?php if ( ! empty( $settings['show_short_desc'] ) && '' !== $short ) : ?>
							<div class="desc"><?php echo esc_html( wp_trim_words( $short, 22, '…' ) ); ?></div>
						<?php endif; ?>
						<?php if ( ! empty( $settings['show_price_gross'] ) ) : ?>
							<div class="price"><?php echo $prices['gross']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wc_price. ?> <small><?php esc_html_e( 'inkl. MwSt.', 'wc-professional-catalog' ); ?></small></div>
						<?php endif; ?>
						<?php if ( ! empty( $settings['show_price_net'] ) ) : ?>
							<div class="price"><small><?php echo $prices['net']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wc_price. ?> <?php esc_html_e( 'netto', 'wc-professional-catalog' ); ?></small></div>
						<?php endif; ?>
						<?php if ( ! empty( $settings['show_unit_price'] ) && '' !== $unit_price ) : ?>
							<div class="unit"><?php echo $unit_price; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wc_price. ?></div>
						<?php endif; ?>
						<?php if ( ! empty( $settings['enable_buy_link'] ) ) : ?>
							<a class="buy" href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $settings['buy_button_text'] ?? '' ); ?></a>
						<?php endif; ?>
					</div>
				</td>
			<?php endforeach; ?>
			<?php
			$pad = (int) $columns - count( $row );
			for ( $p = 0; $p < $pad; $p++ ) {
				echo '<td></td>';
			}
			?>
			</tr>
		<?php endforeach; ?>
	</table>
</section>
