/**
 * Blog index — Editorial: fade-in rows as they scroll into view.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var rows = document.querySelectorAll( '.silken-blog__row' );

		if ( ! rows.length || ! ( 'IntersectionObserver' in window ) ) {
			return;
		}

		rows.forEach( function ( row ) {
			row.style.opacity = '0';
			row.style.transform = 'translateY(12px)';
			row.style.transition = 'opacity .5s ease, transform .5s ease';
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
			{ threshold: 0.1 }
		);

		rows.forEach( function ( row ) {
			observer.observe( row );
		} );
	} );
} )();
