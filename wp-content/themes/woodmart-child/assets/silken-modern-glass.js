/**
 * Silken Memories — Modern Glass: scroll-reveal cards + sticky pill timeline sync.
 * Loaded only on template-silken-modern-glass.php.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var cards = document.querySelectorAll( '.silken-glass__card' );
		var pills = document.querySelectorAll( '.silken-glass__pill' );

		function setActivePill( index ) {
			pills.forEach( function ( pill ) {
				pill.classList.toggle( 'is-active', parseInt( pill.dataset.cardIndex, 10 ) === index );
			} );
		}

		if ( 'IntersectionObserver' in window && cards.length ) {
			var revealObserver = new IntersectionObserver(
				function ( entries ) {
					entries.forEach( function ( entry ) {
						if ( entry.isIntersecting ) {
							entry.target.classList.add( 'is-in' );
						}
					} );
				},
				{ threshold: 0.2 }
			);

			var activeObserver = new IntersectionObserver(
				function ( entries ) {
					entries.forEach( function ( entry ) {
						if ( entry.isIntersecting ) {
							setActivePill( parseInt( entry.target.dataset.cardIndex, 10 ) );
						}
					} );
				},
				{ threshold: 0.5 }
			);

			cards.forEach( function ( card ) {
				revealObserver.observe( card );
				activeObserver.observe( card );
			} );
		}

		pills.forEach( function ( pill ) {
			pill.addEventListener( 'click', function () {
				setActivePill( parseInt( pill.dataset.cardIndex, 10 ) );
			} );
		} );

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
