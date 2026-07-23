/**
 * Blog index — Magazine: subtle fade-up reveal for cards as they scroll into view.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var cards = document.querySelectorAll( '.silken-blog__card' );

		if ( ! cards.length || ! ( 'IntersectionObserver' in window ) ) {
			return;
		}

		cards.forEach( function ( card ) {
			card.style.opacity = '0';
			card.style.transform = 'translateY(16px)';
			card.style.transition = 'opacity .6s ease, transform .6s ease';
		} );

		var observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						entry.target.style.opacity = '1';
						entry.target.style.transform = 'translateY(0)';
						observer.unobserve( entry.target );
					}
				} );
			},
			{ threshold: 0.15 }
		);

		cards.forEach( function ( card ) {
			observer.observe( card );
		} );
	} );
} )();
