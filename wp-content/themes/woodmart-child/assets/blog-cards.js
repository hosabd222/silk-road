/**
 * Blog index — Cards: staggered fade/scale-in for tiles as they scroll into view.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var tiles = document.querySelectorAll( '.silken-blog__tile' );

		if ( ! tiles.length || ! ( 'IntersectionObserver' in window ) ) {
			return;
		}

		tiles.forEach( function ( tile ) {
			tile.style.opacity = '0';
			tile.style.transform = 'translateY(20px) scale(.97)';
			tile.style.transition = 'opacity .6s ease, transform .6s ease';
		} );

		var observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry, i ) {
					if ( entry.isIntersecting ) {
						window.setTimeout( function () {
							entry.target.style.opacity = '1';
							entry.target.style.transform = 'translateY(0) scale(1)';
						}, i * 60 );
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
