( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var grid = document.querySelector( '.silk-masonry' );
		if ( ! grid ) {
			return;
		}

		var cols = Array.prototype.slice.call( grid.querySelectorAll( '.silk-masonry__col' ) );
		if ( ! cols.length ) {
			return;
		}

		var enabled = window.innerWidth > 900 && ! window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
		var ticking = false;

		function clamp( value, min, max ) {
			return Math.max( min, Math.min( max, value ) );
		}

		function update() {
			ticking = false;

			if ( ! enabled ) {
				return;
			}

			var rect     = grid.getBoundingClientRect();
			var progress = window.innerHeight - rect.top;

			cols.forEach( function ( col, index ) {
				var speed = ( index % 2 === 0 ) ? 0.07 : -0.1;
				var y     = clamp( progress * speed, -140, 140 );
				col.style.transform = 'translate3d(0,' + y.toFixed( 1 ) + 'px,0)';
			} );
		}

		function requestUpdate() {
			if ( ! ticking ) {
				window.requestAnimationFrame( update );
				ticking = true;
			}
		}

		function resetCols() {
			cols.forEach( function ( col ) {
				col.style.transform = '';
			} );
		}

		window.addEventListener( 'scroll', requestUpdate, { passive: true } );
		window.addEventListener(
			'resize',
			function () {
				enabled = window.innerWidth > 900 && ! window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
				if ( ! enabled ) {
					resetCols();
				}
				requestUpdate();
			},
			{ passive: true }
		);

		requestUpdate();
	} );
} )();
