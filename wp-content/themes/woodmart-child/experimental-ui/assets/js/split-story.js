( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var sections = document.querySelectorAll( '.silk-story__section' );
		var imgA     = document.getElementById( 'silk-story-img-a' );
		var imgB     = document.getElementById( 'silk-story-img-b' );

		if ( ! sections.length || ! imgA || ! imgB ) {
			return;
		}

		var activeIsA  = true;
		var currentSrc = imgA.getAttribute( 'src' );

		function crossfadeTo( src ) {
			if ( ! src || src === currentSrc ) {
				return;
			}
			currentSrc = src;

			var incoming = activeIsA ? imgB : imgA;
			var outgoing = activeIsA ? imgA : imgB;

			incoming.onload = function () {
				incoming.classList.add( 'is-active' );
				outgoing.classList.remove( 'is-active' );
				activeIsA = ! activeIsA;
			};
			incoming.src = src;
		}

		var observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						crossfadeTo( entry.target.getAttribute( 'data-image' ) );
					}
				} );
			},
			{ rootMargin: '-45% 0px -45% 0px', threshold: 0 }
		);

		sections.forEach( function ( section ) {
			observer.observe( section );
		} );
	} );
} )();
