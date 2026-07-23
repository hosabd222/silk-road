/**
 * Silken Memories — Modern Cinematic: scroll-reveal scenes + reading-progress rail.
 * Loaded only on template-silken-modern-cinematic.php.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var scenes = document.querySelectorAll( '.silken-cine__scene' );

		if ( 'IntersectionObserver' in window && scenes.length ) {
			var revealObserver = new IntersectionObserver(
				function ( entries ) {
					entries.forEach( function ( entry ) {
						if ( entry.isIntersecting ) {
							entry.target.classList.add( 'is-in' );
						}
					} );
				},
				{ threshold: 0.35 }
			);

			scenes.forEach( function ( scene ) {
				revealObserver.observe( scene );
			} );

			// Only decode/play the video for the scene currently in view.
			var videos = document.querySelectorAll( '.silken-cine__video' );
			var playObserver = new IntersectionObserver(
				function ( entries ) {
					entries.forEach( function ( entry ) {
						if ( entry.isIntersecting ) {
							entry.target.play().catch( function () {} );
						} else {
							entry.target.pause();
						}
					} );
				},
				{ threshold: 0.25 }
			);

			videos.forEach( function ( video ) {
				playObserver.observe( video );
			} );
		}

		var progressFill = document.querySelector( '.silken-cine__progress-fill' );

		function updateProgress() {
			if ( ! progressFill ) {
				return;
			}

			var scrollTop    = window.scrollY || document.documentElement.scrollTop;
			var docHeight    = document.documentElement.scrollHeight - window.innerHeight;
			var ratio        = docHeight > 0 ? Math.min( 1, Math.max( 0, scrollTop / docHeight ) ) : 0;

			progressFill.style.height = ( ratio * 100 ) + '%';
		}

		window.addEventListener( 'scroll', updateProgress, { passive: true } );
		updateProgress();

		if ( typeof GLightbox === 'function' ) {
			GLightbox( {
				selector: '.glightbox',
				touchNavigation: true,
				loop: true,
				autoplayVideos: true,
			} );
		}
	} );
} )();
