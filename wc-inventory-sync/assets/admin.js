/* WC Inventory Sync – Admin-Skript */
( function ( $ ) {
	'use strict';

	$( function () {
		// Neues Secret erzeugen.
		$( '#wcis-gen-secret' ).on( 'click', function () {
			$( '#wcis-secret' ).val( $( this ).data( 'secret' ) );
		} );

		// Shop-Zeile hinzufügen.
		$( '#wcis-add-shop' ).on( 'click', function () {
			var $tbody = $( '#wcis-shops tbody' );
			var $row = $tbody.find( '.wcis-shop-row' ).first().clone();
			$row.find( 'input' ).val( '' );
			$row.find( '.wcis-test-result' ).removeClass( 'ok err' ).text( '' );
			$tbody.append( $row );
		} );

		// Shop-Zeile entfernen.
		$( '#wcis-shops' ).on( 'click', '.wcis-remove-shop', function () {
			var $rows = $( '#wcis-shops .wcis-shop-row' );
			if ( $rows.length > 1 ) {
				$( this ).closest( 'tr' ).remove();
			} else {
				$( this ).closest( 'tr' ).find( 'input' ).val( '' );
			}
		} );

		// Verbindungstest.
		$( '#wcis-shops' ).on( 'click', '.wcis-test', function () {
			var $btn = $( this );
			var $row = $btn.closest( 'tr' );
			var url = $row.find( '.wcis-shop-url' ).val();
			var $result = $row.find( '.wcis-test-result' );

			if ( ! url ) {
				$result.removeClass( 'ok' ).addClass( 'err' ).text( '✗ URL fehlt' );
				return;
			}

			$btn.prop( 'disabled', true );
			$result.removeClass( 'ok err' ).text( WCIS.i18n.testing );

			$.post( WCIS.ajaxUrl, {
				action: 'wcis_test_connection',
				nonce: WCIS.nonce,
				url: url
			} ).done( function ( resp ) {
				if ( resp && resp.success ) {
					$result.removeClass( 'err' ).addClass( 'ok' ).text( '✓ ' + resp.data.message );
				} else {
					$result.removeClass( 'ok' ).addClass( 'err' ).text( '✗ ' + ( resp.data ? resp.data.message : 'Fehler' ) );
				}
			} ).fail( function () {
				$result.removeClass( 'ok' ).addClass( 'err' ).text( '✗ Anfrage fehlgeschlagen' );
			} ).always( function () {
				$btn.prop( 'disabled', false );
			} );
		} );

		// --- Voll-Synchronisation mit Fortschrittsbalken (AJAX) ---
		var $form = $( '#wcis-fullsync-form' );
		var $btn = $( '#wcis-full-sync' );
		var $cancel = $( '#wcis-full-sync-cancel' );
		var $wrap = $( '#wcis-progress-wrap' );
		var $fill = $( '#wcis-progress-fill' );
		var $label = $( '#wcis-progress-label' );
		var $text = $( '#wcis-progress-text' );
		var polling = false;

		function setBar( pct ) {
			$fill.css( 'width', pct + '%' );
			$label.text( pct + '%' );
		}

		function renderStatus( d ) {
			setBar( d.percent );
			var msg;
			if ( d.status === 'running' ) {
				msg = WCIS.i18n.syncing + ' ' + d.percent + '% – ' +
					( d.current_peer ? ( WCIS.i18n.toShop + ' ' + d.current_peer + ' ' ) : '' ) +
					'(' + d.sent + ' ' + WCIS.i18n.batchesSent +
					( d.failed ? ', ' + d.failed + ' ' + WCIS.i18n.failedUnit : '' ) + ')';
			} else if ( d.status === 'done' ) {
				msg = '✓ ' + WCIS.i18n.done + ': ' + d.total_items + ' ' + WCIS.i18n.itemsUnit +
					', ' + d.sent + ' ' + WCIS.i18n.batchesSent +
					( d.failed ? ', ' + d.failed + ' ' + WCIS.i18n.failedUnit : '' ) + '.';
			} else if ( d.status === 'cancelled' ) {
				msg = WCIS.i18n.cancelled + '.';
			} else {
				msg = '';
			}
			$text.text( msg );
		}

		function finish() {
			polling = false;
			$btn.prop( 'disabled', false );
			$cancel.hide();
		}

		function tick() {
			if ( ! polling ) {
				return;
			}
			$.post( WCIS.ajaxUrl, { action: 'wcis_fullsync_tick', nonce: WCIS.nonce } )
				.done( function ( resp ) {
					if ( ! resp || ! resp.success ) {
						$text.text( '✗ ' + ( resp && resp.data ? resp.data.message : WCIS.i18n.genericError ) );
						finish();
						return;
					}
					renderStatus( resp.data );
					if ( resp.data.status === 'running' ) {
						setTimeout( tick, 300 );
					} else {
						finish();
					}
				} )
				.fail( function () {
					// Netzwerk-Hänger: kurz warten und erneut versuchen.
					setTimeout( tick, 1500 );
				} );
		}

		function startPolling() {
			polling = true;
			$btn.prop( 'disabled', true );
			$cancel.show();
			$wrap.show();
			tick();
		}

		// Start per Formular-Submit (JS übernimmt, Fallback = synchron ohne JS).
		$form.on( 'submit', function ( e ) {
			e.preventDefault();
			if ( ! window.confirm( WCIS.i18n.confirmFull ) ) {
				return;
			}
			$btn.prop( 'disabled', true );
			$wrap.show();
			setBar( 0 );
			$text.text( WCIS.i18n.starting );

			var batch = $( '#wcis-batch-size' ).val();
			$.post( WCIS.ajaxUrl, { action: 'wcis_fullsync_start', nonce: WCIS.nonce, batch_size: batch } )
				.done( function ( resp ) {
					if ( ! resp || ! resp.success ) {
						$text.text( '✗ ' + ( resp && resp.data ? resp.data.message : WCIS.i18n.genericError ) );
						$btn.prop( 'disabled', false );
						return;
					}
					renderStatus( resp.data );
					if ( resp.data.status === 'running' ) {
						startPolling();
					} else {
						$btn.prop( 'disabled', false );
					}
				} )
				.fail( function () {
					$text.text( '✗ ' + WCIS.i18n.genericError );
					$btn.prop( 'disabled', false );
				} );
		} );

		// Abbrechen.
		$cancel.on( 'click', function () {
			$.post( WCIS.ajaxUrl, { action: 'wcis_fullsync_cancel', nonce: WCIS.nonce } )
				.always( function () {
					finish();
					$text.text( WCIS.i18n.cancelled + '.' );
				} );
		} );

		// Laufenden Job nach Seiten-Reload automatisch fortsetzen.
		if ( $wrap.length && $wrap.is( ':visible' ) ) {
			startPolling();
		}
	} );
} )( jQuery );
