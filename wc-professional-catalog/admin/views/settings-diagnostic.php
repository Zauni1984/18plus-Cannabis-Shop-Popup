<?php
/**
 * Diagnostic / verification page for the batched renderer.
 *
 * @package WCProfessionalCatalog
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mem_limit = WCPC_Catalog::memory_limit_bytes();

$t0    = microtime( true );
$total = WCPC_Catalog::count( array() );
$t1    = microtime( true );

$batch_size = 10;
$batches    = array();
$seen       = array();
$batch_ok   = true;
$batch_err  = '';

try {
	for ( $i = 1; $i <= 3; $i++ ) {
		$bt0 = microtime( true );
		$b   = WCPC_Catalog::batch( array(), $i, $batch_size );
		$bt1 = microtime( true );

		$ids = array();
		foreach ( $b as $p ) {
			if ( $p instanceof WC_Product ) {
				$ids[] = $p->get_id();
			}
		}
		$dupes = array_intersect( $ids, $seen );

		$batches[] = array(
			'page'    => $i,
			'count'   => count( $ids ),
			'ids'     => $ids,
			'dupes'   => $dupes,
			'ms'      => round( ( $bt1 - $bt0 ) * 1000, 1 ),
			'mem_mb'  => round( memory_get_usage( true ) / 1024 / 1024, 1 ),
		);

		foreach ( $ids as $id ) {
			$seen[] = $id;
		}
	}
} catch ( \Throwable $e ) {
	$batch_ok  = false;
	$batch_err = $e->getMessage();
}

$dompdf_ok = class_exists( '\\Dompdf\\Dompdf' ) || WCPC_PDF::is_dompdf_available();
?>
<div class="wcpc-diagnostic">
	<h2><?php esc_html_e( 'Diagnose', 'wc-professional-catalog' ); ?></h2>
	<p class="description"><?php esc_html_e( 'Diese Seite prüft live, ob die batched Produktabfrage korrekt arbeitet. Sie zeigt drei Test-Batches, die Anzahl gefundener Produkte und die Speicherauslastung.', 'wc-professional-catalog' ); ?></p>

	<table class="widefat striped" style="max-width:900px;">
		<tbody>
			<tr><th><?php esc_html_e( 'WordPress', 'wc-professional-catalog' ); ?></th><td><?php echo esc_html( get_bloginfo( 'version' ) ); ?></td></tr>
			<tr><th><?php esc_html_e( 'WooCommerce', 'wc-professional-catalog' ); ?></th><td><?php echo defined( 'WC_VERSION' ) ? esc_html( WC_VERSION ) : '—'; ?></td></tr>
			<tr><th>PHP</th><td><?php echo esc_html( PHP_VERSION ); ?></td></tr>
			<tr><th>memory_limit</th><td><?php echo esc_html( ini_get( 'memory_limit' ) ); ?> (<?php echo esc_html( $mem_limit ? round( $mem_limit / 1024 / 1024, 1 ) . ' MB' : 'unlimited' ); ?>)</td></tr>
			<tr><th>max_execution_time</th><td><?php echo esc_html( ini_get( 'max_execution_time' ) ); ?>s</td></tr>
			<tr><th>WCPC Version</th><td><?php echo esc_html( WCPC_VERSION ); ?></td></tr>
			<tr><th><?php esc_html_e( 'Dompdf', 'wc-professional-catalog' ); ?></th><td><?php echo $dompdf_ok ? '<span style="color:#1f7a3a;">&#10003; ' . esc_html__( 'verfügbar', 'wc-professional-catalog' ) . '</span>' : '<span style="color:#a00;">&#10007; ' . esc_html__( 'fehlt', 'wc-professional-catalog' ) . '</span>'; ?></td></tr>
		</tbody>
	</table>

	<h3 style="margin-top:24px;"><?php esc_html_e( 'Produkt-Count', 'wc-professional-catalog' ); ?></h3>
	<p>
		<strong><?php echo (int) $total; ?></strong> <?php esc_html_e( 'veröffentlichte Produkte', 'wc-professional-catalog' ); ?>
		(<?php echo esc_html( round( ( $t1 - $t0 ) * 1000, 1 ) . ' ms' ); ?>)
	</p>

	<h3 style="margin-top:24px;"><?php esc_html_e( 'Batch-Test (3 × 10 Produkte)', 'wc-professional-catalog' ); ?></h3>
	<?php if ( ! $batch_ok ) : ?>
		<div class="notice notice-error inline"><p><strong><?php esc_html_e( 'Batch-Abruf fehlgeschlagen:', 'wc-professional-catalog' ); ?></strong> <?php echo esc_html( $batch_err ); ?></p></div>
	<?php else : ?>
		<table class="widefat striped" style="max-width:900px;">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Batch', 'wc-professional-catalog' ); ?></th>
					<th><?php esc_html_e( 'Anzahl', 'wc-professional-catalog' ); ?></th>
					<th><?php esc_html_e( 'Produkt-IDs', 'wc-professional-catalog' ); ?></th>
					<th><?php esc_html_e( 'Duplikate', 'wc-professional-catalog' ); ?></th>
					<th><?php esc_html_e( 'Dauer', 'wc-professional-catalog' ); ?></th>
					<th><?php esc_html_e( 'RAM', 'wc-professional-catalog' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $batches as $b ) : ?>
					<tr>
						<td>#<?php echo (int) $b['page']; ?></td>
						<td><?php echo (int) $b['count']; ?></td>
						<td><code><?php echo esc_html( implode( ', ', $b['ids'] ) ); ?></code></td>
						<td>
							<?php if ( $b['dupes'] ) : ?>
								<span style="color:#a00;">&#10007; <?php echo esc_html( implode( ',', $b['dupes'] ) ); ?></span>
							<?php else : ?>
								<span style="color:#1f7a3a;">&#10003; <?php esc_html_e( 'keine', 'wc-professional-catalog' ); ?></span>
							<?php endif; ?>
						</td>
						<td><?php echo esc_html( $b['ms'] ); ?> ms</td>
						<td><?php echo esc_html( $b['mem_mb'] ); ?> MB</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<?php
		$any_dupes = false;
		$total_ids = 0;
		foreach ( $batches as $b ) {
			if ( $b['dupes'] ) {
				$any_dupes = true;
			}
			$total_ids += $b['count'];
		}
		?>
		<p style="margin-top:14px;">
			<?php if ( ! $any_dupes && $total_ids > 0 ) : ?>
				<span style="color:#1f7a3a;font-weight:600;">&#10003; <?php esc_html_e( 'Batch-Paginierung funktioniert: jeder Batch liefert andere Produkt-IDs.', 'wc-professional-catalog' ); ?></span>
			<?php elseif ( 0 === $total_ids ) : ?>
				<span style="color:#777;"><?php esc_html_e( 'Keine Produkte geliefert.', 'wc-professional-catalog' ); ?></span>
			<?php else : ?>
				<span style="color:#a00;font-weight:600;">&#10007; <?php esc_html_e( 'Batches liefern doppelte IDs - die Paginierung ist gestört.', 'wc-professional-catalog' ); ?></span>
			<?php endif; ?>
		</p>
	<?php endif; ?>

	<h3 style="margin-top:24px;"><?php esc_html_e( 'Frontend-URLs', 'wc-professional-catalog' ); ?></h3>
	<ul>
		<li><a href="<?php echo esc_url( home_url( '/wc-catalog/flipbook/' ) ); ?>" target="_blank"><?php echo esc_url( home_url( '/wc-catalog/flipbook/' ) ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/wc-catalog/pdf/' ) ); ?>" target="_blank"><?php echo esc_url( home_url( '/wc-catalog/pdf/' ) ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/wc-catalog/print/' ) ); ?>" target="_blank"><?php echo esc_url( home_url( '/wc-catalog/print/' ) ); ?></a></li>
	</ul>

	<p class="description" style="margin-top:14px;">
		<?php esc_html_e( 'Wenn auf einer dieser URLs ein „Kritischer Fehler" erscheint, schreibt das Plugin den genauen Grund nach wp-content/debug.log (wenn WP_DEBUG_LOG aktiv ist).', 'wc-professional-catalog' ); ?>
	</p>
</div>
