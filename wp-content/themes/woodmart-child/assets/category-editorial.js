/**
 * Category showcase — Editorial: scroll-reveal for the image mosaic tiles.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var tiles = document.querySelectorAll( '.silken-cat-ed__mosaic-item' );

		if ( ! tiles.length ) {
			return;
		}

		if ( ! ( 'IntersectionObserver' in window ) ) {
			tiles.forEach( function ( t ) {
				t.classList.add( 'is-in' );
			} );
			return;
		}

		var observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry, i ) {
					if ( entry.isIntersecting ) {
						window.setTimeout( function () {
							entry.target.classList.add( 'is-in' );
						}, i * 90 );
						observer.unobserve( entry.target );
					}
				} );
			},
			{ threshold: 0.15 }
		);

		tiles.forEach( function ( tile ) {
			observer.observe( tile );
		} );
	} );
} )();
