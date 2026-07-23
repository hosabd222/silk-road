/**
 * Login/Register — Card: tab switch between the login and register panes.
 * Both forms stay in the DOM and submit exactly as WooCommerce expects —
 * this only toggles which one is visible.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var tabs  = document.querySelectorAll( '.silken-auth__tab' );
		var panes = document.querySelectorAll( '.silken-auth__pane' );

		tabs.forEach( function ( tab ) {
			tab.addEventListener( 'click', function () {
				var target = tab.dataset.tab;

				tabs.forEach( function ( t ) {
					var isActive = t === tab;
					t.classList.toggle( 'is-active', isActive );
					t.setAttribute( 'aria-selected', isActive ? 'true' : 'false' );
				} );

				panes.forEach( function ( pane ) {
					pane.classList.toggle( 'is-active', pane.dataset.pane === target );
				} );
			} );
		} );
	} );
} )();
