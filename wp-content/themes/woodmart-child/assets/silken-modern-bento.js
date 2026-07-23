/**
 * Silken Memories — Modern Bento: timeline tags scroll/pulse the matching cell.
 * Loaded only on template-silken-modern-bento.php.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var tags = document.querySelectorAll( '.silken-bento__tag' );

		tags.forEach( function ( tag ) {
			tag.addEventListener( 'click', function () {
				var target = document.getElementById( tag.dataset.target );

				if ( ! target ) {
					return;
				}

				tags.forEach( function ( t ) {
					t.classList.remove( 'is-active' );
				} );
				tag.classList.add( 'is-active' );

				target.scrollIntoView( { behavior: 'smooth', block: 'center' } );
				target.classList.add( 'is-pulse' );
				window.setTimeout( function () {
					target.classList.remove( 'is-pulse' );
				}, 1000 );
			} );
		} );

		// Only decode/play the clips currently on screen.
		var videos = document.querySelectorAll( '.silken-bento__video' );

		if ( 'IntersectionObserver' in window && videos.length ) {
			var observer = new IntersectionObserver(
				function ( entries ) {
					entries.forEach( function ( entry ) {
						if ( entry.isIntersecting ) {
							entry.target.play().catch( function () {} );
						} else {
							entry.target.pause();
						}
					} );
				},
				{ threshold: 0.3 }
			);

			videos.forEach( function ( video ) {
				observer.observe( video );
			} );
		}

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
