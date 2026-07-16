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

		// Sicherheitsabfrage vor Voll-Sync.
		$( '#wcis-full-sync' ).on( 'click', function ( e ) {
			if ( ! window.confirm( WCIS.i18n.confirmFull ) ) {
				e.preventDefault();
			}
		} );
	} );
} )( jQuery );
