<?php
/**
 * Flipbook (online catalog) - section-by-section rendering.
 *
 * Top-level grouping: Main category A-Z. Inside each main category:
 * its own products first (if any sit directly under it), then each
 * subcategory A-Z. Products within a section are pre-sorted by
 * Brand A-Z, then Name A-Z by the SQL in WCPC_Catalog::collect_sections().
 *
 * Each section gets a big banner on its first page; subsequent pages of
 * the same section carry the small section label in the page header so
 * the reader always knows where they are.
 *
 * Expects in scope:
 *   - $settings (array)
 *   - $per_page (int)
 *
 * @package WCProfessionalCatalog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! isset( $settings ) || ! is_array( $settings ) ) {
	$settings = WCPC_Plugin::get_settings();
}
if ( ! isset( $per_page ) || (int) $per_page <= 0 ) {
	$per_page = max( 1, (int) ( $settings['products_per_page'] ?? 12 ) );
}

$sections = WCPC_Catalog::collect_sections();

// Pre-calculate total product pages so the toolbar can show "X / Y" correctly.
$total_product_pages = 0;
$total_products      = 0;
foreach ( $sections as $s ) {
	$pages_for_section    = (int) ceil( count( $s['ids'] ) / max( 1, $per_page ) );
	$total_product_pages += $pages_for_section;
	$total_products      += count( $s['ids'] );
}
$total_pages = max( 1, $total_product_pages );

$logo_url  = ! empty( $settings['logo_id'] ) ? (string) wp_get_attachment_image_url( (int) $settings['logo_id'], 'medium' ) : '';
$cover_url = ! empty( $settings['cover_image_id'] ) ? (string) wp_get_attachment_image_url( (int) $settings['cover_image_id'], 'large' ) : '';
?>
<section class="wcpc-catalog wcpc-flipbook" aria-label="<?php esc_attr_e( 'Online-Katalog', 'wc-professional-catalog' ); ?>">
	<div class="wcpc-flipbook__toolbar">
		<button type="button" class="wcpc-btn wcpc-btn-ghost" data-wcpc-prev>&laquo; <?php esc_html_e( 'Zurück', 'wc-professional-catalog' ); ?></button>
		<span class="wcpc-flipbook__pageinfo">
			<span data-wcpc-current>1</span> / <span data-wcpc-total><?php echo (int) ( $total_pages + 1 ); ?></span>
		</span>
		<button type="button" class="wcpc-btn wcpc-btn-ghost" data-wcpc-next><?php esc_html_e( 'Weiter', 'wc-professional-catalog' ); ?> &raquo;</button>
	</div>

	<div class="wcpc-flipbook__stage">
		<div class="wcpc-flipbook__pages">

			<article class="wcpc-flipbook__page wcpc-flipbook__page--cover is-active" data-page="0">
				<div class="wcpc-cover<?php echo $cover_url ? ' wcpc-cover--has-image' : ''; ?>"
					<?php if ( $cover_url ) : ?>style="background-image:url('<?php echo esc_url( $cover_url ); ?>');"<?php endif; ?>>
					<?php if ( $logo_url ) : ?>
						<img class="wcpc-cover__logo" src="<?php echo esc_url( $logo_url ); ?>" alt="" />
					<?php endif; ?>
					<h1 class="wcpc-cover__title"><?php echo esc_html( $settings['cover_title'] ); ?></h1>
					<?php if ( ! empty( $settings['cover_subtitle'] ) ) : ?>
						<p class="wcpc-cover__subtitle"><?php echo esc_html( $settings['cover_subtitle'] ); ?></p>
					<?php endif; ?>
					<p class="wcpc-cover__date"><?php echo esc_html( date_i18n( get_option( 'date_format' ) ) ); ?></p>
				</div>
			</article>

			<?php
			$page_index   = 0;
			$truncated    = false;
			$rendered_ids = 0;

			foreach ( $sections as $section_i => $section ) {
				$section_main = $section['main'];
				$section_sub  = $section['sub'];
				$section_ids  = $section['ids'];
				$is_first     = true;

				for ( $offset = 0; $offset < count( $section_ids ); $offset += $per_page ) {
					if ( WCPC_Catalog::memory_pressure() ) {
						$truncated = true;
						break 2;
					}

					$page_products = WCPC_Catalog::batch_by_ids( $section_ids, $offset, $per_page );
					if ( empty( $page_products ) ) {
						break;
					}

					$page_index++;
					$show_banner = $is_first;
					include WCPC_PLUGIN_DIR . 'templates/parts/flipbook-page.php';
					$rendered_ids += count( $page_products );
					$is_first      = false;

					unset( $page_products );

					if ( ( $page_index % 4 ) === 0 ) {
						WCPC_Catalog::free_batch_memory();
					}
				}
			}

			if ( $truncated ) {
				?>
				<article class="wcpc-flipbook__page" data-page="<?php echo (int) ( $page_index + 1 ); ?>">
					<div class="wcpc-truncated" style="text-align:center;padding:60px 20px;color:var(--wcpc-color-muted,#777);">
						<h2 style="margin:0 0 10px;color:var(--wcpc-color-primary,#1f7a3a);"><?php esc_html_e( 'Katalog gekürzt', 'wc-professional-catalog' ); ?></h2>
						<p>
							<?php
							printf(
								/* translators: 1: rendered count, 2: total */
								esc_html__( '%1$d von %2$d Produkten wurden geladen. Memory-Limit auf dem Server erreicht.', 'wc-professional-catalog' ),
								(int) $rendered_ids,
								(int) $total_products
							);
							?>
						</p>
					</div>
				</article>
				<?php
			}
			?>

		</div>
	</div>
</section>
