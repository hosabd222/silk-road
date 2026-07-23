/**
 * About Us — Workshop: staggered fade-in for the process steps.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var steps = document.querySelectorAll( '.silken-about-ws__step' );

		if ( ! steps.length || ! ( 'IntersectionObserver' in window ) ) {
			return;
		}

		steps.forEach( function ( step ) {
			step.style.opacity = '0';
			step.style.transform = 'translateY(16px)';
			step.style.transition = 'opacity .5s ease, transform .5s ease';
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

		steps.forEach( function ( step ) {
			observer.observe( step );
		} );
	} );
} )();
