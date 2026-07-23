/**
 * Silken Memories — timeline-driven Swiper album + GLightbox video playback.
 * Loaded only on template-silken-memories.php (see functions.php).
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var swiperEl = document.querySelector( '.silken-swiper' );

		if ( ! swiperEl || typeof Swiper === 'undefined' ) {
			return;
		}

		var swiper = new Swiper( swiperEl, {
			effect: 'coverflow',
			grabCursor: true,
			centeredSlides: true,
			slidesPerView: 'auto',
			loop: false,
			coverflowEffect: {
				rotate: 30,
				stretch: 0,
				depth: 160,
				modifier: 1,
				slideShadows: false,
			},
			speed: 550,
			keyboard: { enabled: true },
		} );

		var timelinePoints = document.querySelectorAll( '.silken-timeline__point' );

		function setActiveTimelinePoint( index ) {
			timelinePoints.forEach( function ( point ) {
				var isActive = parseInt( point.dataset.slideIndex, 10 ) === index;
				point.classList.toggle( 'is-active', isActive );
			} );
		}

		timelinePoints.forEach( function ( point ) {
			point.addEventListener( 'click', function () {
				swiper.slideTo( parseInt( point.dataset.slideIndex, 10 ) );
			} );
		} );

		swiper.on( 'slideChange', function () {
			setActiveTimelinePoint( swiper.activeIndex );
		} );

		// The album can hold many autoplaying clips at once; only decode/play the
		// ones currently on screen so scrolling the page doesn't tank performance.
		var videos = document.querySelectorAll( '.silken-frame__video' );

		if ( 'IntersectionObserver' in window && videos.length ) {
			var observer = new IntersectionObserver(
				function ( entries ) {
					entries.forEach( function ( entry ) {
						var video = entry.target;

						if ( entry.isIntersecting ) {
							video.play().catch( function () {} );
						} else {
							video.pause();
						}
					} );
				},
				{ threshold: 0.4 }
			);

			videos.forEach( function ( video ) {
				observer.observe( video );
			} );
		}

		if ( typeof GLightbox === 'function' ) {
			var lightbox = GLightbox( {
				selector: '.glightbox',
				touchNavigation: true,
				loop: true,
				autoplayVideos: true,
			} );

			// Mute the album preview while its lightbox is open so the two
			// copies of the same clip don't play over each other audibly.
			lightbox.on( 'open', function () {
				videos.forEach( function ( video ) {
					video.pause();
				} );
			} );

			lightbox.on( 'close', function () {
				videos.forEach( function ( video ) {
					video.play().catch( function () {} );
				} );
			} );
		}
	} );
} )();
