( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var stage = document.querySelector( '.silk-hotspots__stage' );
		if ( ! stage ) {
			return;
		}

		// Staggered entrance once the stage is in view.
		var io = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						stage.classList.add( 'is-ready' );
						io.disconnect();
					}
				} );
			},
			{ threshold: .2 }
		);
		io.observe( stage );

		// Tap-to-toggle for touch devices (hover alone isn't reliable there).
		var hotspots = stage.querySelectorAll( '.silk-hotspot' );
		hotspots.forEach( function ( spot ) {
			spot.addEventListener( 'click', function ( e ) {
				var wasActive = spot.classList.contains( 'is-active' );
				hotspots.forEach( function ( s ) {
					s.classList.remove( 'is-active' );
				} );
				if ( ! wasActive ) {
					spot.classList.add( 'is-active' );
					e.preventDefault();
				}
			} );
		} );

		document.addEventListener( 'click', function ( e ) {
			if ( ! stage.contains( e.target ) ) {
				hotspots.forEach( function ( s ) {
					s.classList.remove( 'is-active' );
				} );
			}
		} );
	} );
} )();
