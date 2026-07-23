/**
 * Category showcase — Gallery: auto-rotating slideshow with clickable dots.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var slides = document.querySelectorAll( '.silken-cat-gal__slide' );
		var dots   = document.querySelectorAll( '.silken-cat-gal__dot' );

		if ( slides.length < 2 ) {
			return;
		}

		var current = 0;
		var timer   = null;

		function show( index ) {
			slides[ current ].classList.remove( 'is-active' );
			dots[ current ] && dots[ current ].classList.remove( 'is-active' );

			current = ( index + slides.length ) % slides.length;

			slides[ current ].classList.add( 'is-active' );
			dots[ current ] && dots[ current ].classList.add( 'is-active' );
		}

		function startAutoplay() {
			timer = window.setInterval( function () {
				show( current + 1 );
			}, 4500 );
		}

		function stopAutoplay() {
			window.clearInterval( timer );
		}

		dots.forEach( function ( dot ) {
			dot.addEventListener( 'click', function () {
				stopAutoplay();
				show( parseInt( dot.dataset.slideIndex, 10 ) );
				startAutoplay();
			} );
		} );

		startAutoplay();
	} );
} )();
