/**
 * Category showcase — Cinematic: scroll-reveal for the filmstrip and related cards.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var reveal = document.querySelectorAll( '.silken-cat__filmstrip-item, .silken-cat__related-card' );

		if ( ! reveal.length || ! ( 'IntersectionObserver' in window ) ) {
			reveal.forEach( function ( el ) {
				el.classList.add( 'is-in' );
			} );
			return;
		}

		var observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						entry.target.classList.add( 'is-in' );
					}
				} );
			},
			{ threshold: 0.2 }
		);

		reveal.forEach( function ( el ) {
			observer.observe( el );
		} );
	} );
} )();
