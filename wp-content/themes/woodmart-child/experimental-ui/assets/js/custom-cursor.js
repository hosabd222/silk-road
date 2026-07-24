( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var cursor = document.getElementById( 'silk-cursor' );
		if ( ! cursor || ! window.matchMedia( '(hover: hover) and (pointer: fine)' ).matches ) {
			return;
		}

		var targetX = window.innerWidth / 2;
		var targetY = window.innerHeight / 2;
		var currentX = targetX;
		var currentY = targetY;
		var raf = null;

		function onMouseMove( e ) {
			targetX = e.clientX;
			targetY = e.clientY;
			cursor.classList.add( 'is-visible' );
			if ( ! raf ) {
				raf = window.requestAnimationFrame( render );
			}
		}

		function render() {
			// Simple exponential smoothing (lerp) for a fluid trailing motion.
			currentX += ( targetX - currentX ) * 0.18;
			currentY += ( targetY - currentY ) * 0.18;
			cursor.style.transform = 'translate3d(' + currentX.toFixed( 1 ) + 'px,' + currentY.toFixed( 1 ) + 'px,0)';

			if ( Math.abs( targetX - currentX ) > 0.1 || Math.abs( targetY - currentY ) > 0.1 ) {
				raf = window.requestAnimationFrame( render );
			} else {
				raf = null;
			}
		}

		document.addEventListener( 'mousemove', onMouseMove, { passive: true } );

		document.querySelectorAll( '[data-cursor-zoom]' ).forEach( function ( el ) {
			el.addEventListener( 'mouseenter', function () {
				cursor.classList.add( 'is-zoom' );
			} );
			el.addEventListener( 'mouseleave', function () {
				cursor.classList.remove( 'is-zoom' );
			} );
		} );

		document.addEventListener( 'mouseleave', function () {
			cursor.classList.remove( 'is-visible' );
		} );
	} );
} )();
