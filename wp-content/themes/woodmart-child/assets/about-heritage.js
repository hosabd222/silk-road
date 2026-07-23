/**
 * About Us — Heritage: scroll-reveal for timeline milestones.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var items = document.querySelectorAll( '.silken-about__milestone' );

		if ( ! items.length || ! ( 'IntersectionObserver' in window ) ) {
			items.forEach( function ( el ) {
				el.classList.add( 'is-in' );
			} );
			return;
		}

		var observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry, i ) {
					if ( entry.isIntersecting ) {
						window.setTimeout( function () {
							entry.target.classList.add( 'is-in' );
						}, i * 100 );
						observer.unobserve( entry.target );
					}
				} );
			},
			{ threshold: 0.3 }
		);

		items.forEach( function ( item ) {
			observer.observe( item );
		} );
	} );
} )();
