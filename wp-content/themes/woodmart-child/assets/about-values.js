/**
 * About Us — Values: staggered fade-in for the value cards.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var cards = document.querySelectorAll( '.silken-about-val__card' );

		if ( ! cards.length || ! ( 'IntersectionObserver' in window ) ) {
			return;
		}

		cards.forEach( function ( card ) {
			card.style.opacity = '0';
			card.style.transform = 'translateY(16px)';
			card.style.transition = 'opacity .5s ease, transform .5s ease';
		} );

		var observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry, i ) {
					if ( entry.isIntersecting ) {
						window.setTimeout( function () {
							entry.target.style.opacity = '1';
							entry.target.style.transform = 'translateY(0)';
						}, i * 90 );
						observer.unobserve( entry.target );
					}
				} );
			},
			{ threshold: 0.2 }
		);

		cards.forEach( function ( card ) {
			observer.observe( card );
		} );
	} );
} )();
