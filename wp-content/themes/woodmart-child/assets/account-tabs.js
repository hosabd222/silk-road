/**
 * Account panel — Tabs: scroll the active tab into view on load (useful when
 * the pill row overflows on narrow screens).
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var active = document.querySelector( '.silken-account__tabs-nav .is-active' );
		if ( active && active.scrollIntoView ) {
			active.scrollIntoView( { inline: 'center', block: 'nearest' } );
		}
	} );
} )();
