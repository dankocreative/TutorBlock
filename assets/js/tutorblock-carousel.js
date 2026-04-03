/**
 * TutorBlock Carousel — Vanilla JS
 *
 * Lightweight, accessible carousel for the tutorblock/course-carousel block.
 * No external dependencies. Supports:
 *  - Responsive breakpoints (desktop / tablet / mobile)
 *  - Autoplay with pause on hover/focus
 *  - Arrow navigation
 *  - Dot indicator navigation
 *  - Touch/swipe support
 *  - Keyboard navigation (←/→ arrow keys)
 *  - Animated number counters for course-stats block
 */

( function () {
	'use strict';

	/* ===== CAROUSEL ===== */

	/**
	 * Initialise all carousel instances on the page.
	 */
	function initCarousels() {
		document.querySelectorAll( '.tutorblock-course-carousel' ).forEach( function ( el ) {
			// Guard against double-init.
			if ( el.dataset.tbInitialised ) {
				return;
			}
			el.dataset.tbInitialised = '1';

			var carousel = createCarousel( el );
			carousel.init();
		} );
	}

	/**
	 * Create a carousel controller for a single element.
	 *
	 * @param {HTMLElement} el Carousel root element.
	 * @return {object}
	 */
	function createCarousel( el ) {
		var track         = el.querySelector( '.tutorblock-carousel-track' );
		var slides        = Array.from( el.querySelectorAll( '.tutorblock-carousel-slide' ) );
		var prevBtn       = el.querySelector( '.tutorblock-arrow-prev' );
		var nextBtn       = el.querySelector( '.tutorblock-arrow-next' );
		var dots          = Array.from( el.querySelectorAll( '.tutorblock-dot' ) );
		var autoplay      = el.dataset.autoplay === 'true';
		var autoplaySpeed = parseInt( el.dataset.autoplaySpeed, 10 ) || 3000;

		var currentIndex  = 0;
		var timer         = null;
		var slidesVisible = getSlidesVisible();
		var totalSlides   = slides.length;
		var isDragging    = false;
		var startX        = 0;
		var startScrollX  = 0;

		function getSlidesVisible() {
			var w = window.innerWidth;
			if ( w <= 640 ) {
				return parseInt( el.dataset.slidesMobile, 10 ) || 1;
			} else if ( w <= 1024 ) {
				return parseInt( el.dataset.slidesTablet, 10 ) || 2;
			}
			return parseInt( el.dataset.slidesDesktop, 10 ) || 3;
		}

		function getSlideWidth() {
			return 100 / slidesVisible;
		}

		function setSlideWidths() {
			var width = getSlideWidth();
			slides.forEach( function ( slide ) {
				slide.style.width = width + '%';
			} );
		}

		function goTo( index ) {
			var maxIndex = Math.max( 0, totalSlides - slidesVisible );
			currentIndex = Math.min( Math.max( index, 0 ), maxIndex );

			var offset = currentIndex * getSlideWidth();
			track.style.transform = 'translateX(-' + offset + '%)';

			updateArrows();
			updateDots();
		}

		function updateArrows() {
			var maxIndex = Math.max( 0, totalSlides - slidesVisible );
			if ( prevBtn ) {
				prevBtn.disabled = currentIndex <= 0;
				prevBtn.classList.toggle( 'is-disabled', currentIndex <= 0 );
			}
			if ( nextBtn ) {
				nextBtn.disabled = currentIndex >= maxIndex;
				nextBtn.classList.toggle( 'is-disabled', currentIndex >= maxIndex );
			}
		}

		function updateDots() {
			var dotIndex = Math.round( currentIndex / slidesVisible );
			dots.forEach( function ( dot, i ) {
				var active = i === dotIndex;
				dot.classList.toggle( 'is-active', active );
				dot.setAttribute( 'aria-selected', active ? 'true' : 'false' );
			} );
		}

		function startAutoplay() {
			if ( ! autoplay ) return;
			stopAutoplay();
			timer = setInterval( function () {
				var maxIndex = Math.max( 0, totalSlides - slidesVisible );
				if ( currentIndex >= maxIndex ) {
					goTo( 0 );
				} else {
					goTo( currentIndex + 1 );
				}
			}, autoplaySpeed );
		}

		function stopAutoplay() {
			if ( timer ) {
				clearInterval( timer );
				timer = null;
			}
		}

		function onResize() {
			var newVisible = getSlidesVisible();
			if ( newVisible !== slidesVisible ) {
				slidesVisible = newVisible;
				setSlideWidths();
				goTo( currentIndex );
			}
		}

		function bindEvents() {
			if ( prevBtn ) {
				prevBtn.addEventListener( 'click', function () {
					stopAutoplay();
					goTo( currentIndex - 1 );
					startAutoplay();
				} );
			}

			if ( nextBtn ) {
				nextBtn.addEventListener( 'click', function () {
					stopAutoplay();
					goTo( currentIndex + 1 );
					startAutoplay();
				} );
			}

			dots.forEach( function ( dot, i ) {
				dot.addEventListener( 'click', function () {
					stopAutoplay();
					goTo( i * slidesVisible );
					startAutoplay();
				} );
			} );

			// Keyboard navigation.
			el.addEventListener( 'keydown', function ( e ) {
				if ( e.key === 'ArrowLeft' ) {
					stopAutoplay();
					goTo( currentIndex - 1 );
					startAutoplay();
				} else if ( e.key === 'ArrowRight' ) {
					stopAutoplay();
					goTo( currentIndex + 1 );
					startAutoplay();
				}
			} );

			// Pause autoplay on hover / focus.
			el.addEventListener( 'mouseenter', stopAutoplay );
			el.addEventListener( 'mouseleave', startAutoplay );
			el.addEventListener( 'focusin', stopAutoplay );
			el.addEventListener( 'focusout', startAutoplay );

			// Touch / swipe support.
			track.addEventListener( 'touchstart', function ( e ) {
				startX    = e.touches[ 0 ].clientX;
				isDragging = true;
			}, { passive: true } );

			track.addEventListener( 'touchmove', function ( e ) {
				if ( ! isDragging ) return;
				var diff = startX - e.touches[ 0 ].clientX;
				if ( Math.abs( diff ) > 5 ) {
					e.preventDefault();
				}
			}, { passive: false } );

			track.addEventListener( 'touchend', function ( e ) {
				if ( ! isDragging ) return;
				isDragging = false;
				var diff = startX - e.changedTouches[ 0 ].clientX;
				if ( diff > 50 ) {
					goTo( currentIndex + 1 );
				} else if ( diff < -50 ) {
					goTo( currentIndex - 1 );
				}
			} );

			// Resize.
			window.addEventListener( 'resize', debounce( onResize, 200 ) );
		}

		return {
			init: function () {
				setSlideWidths();
				goTo( 0 );
				bindEvents();
				startAutoplay();
			},
		};
	}

	/* ===== ANIMATED COUNTERS (for course-stats) ===== */

	function initCounters() {
		var counters = document.querySelectorAll( '[data-animated="true"] .tutorblock-stat-number' );
		if ( ! counters.length ) return;

		var observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( ! entry.isIntersecting ) return;
					var el     = entry.target;
					var parent = el.closest( '[data-target]' );
					if ( ! parent || el.dataset.counted ) return;
					el.dataset.counted = '1';

					var target = parseInt( parent.dataset.target, 10 ) || 0;
					animateCounter( el, 0, target, 1500 );
					observer.unobserve( el );
				} );
			},
			{ threshold: 0.5 }
		);

		counters.forEach( function ( el ) {
			observer.observe( el );
		} );
	}

	/**
	 * Animate a number counter from `from` to `to` over `duration` ms.
	 */
	function animateCounter( el, from, to, duration ) {
		var startTime = null;
		var easeOut   = function ( t ) { return 1 - Math.pow( 1 - t, 3 ); };

		function step( timestamp ) {
			if ( ! startTime ) startTime = timestamp;
			var progress = Math.min( ( timestamp - startTime ) / duration, 1 );
			var value    = Math.floor( from + ( to - from ) * easeOut( progress ) );
			el.textContent = value.toLocaleString();
			if ( progress < 1 ) {
				requestAnimationFrame( step );
			} else {
				el.textContent = to.toLocaleString();
			}
		}

		requestAnimationFrame( step );
	}

	/* ===== CATEGORY FILTER (for course-grid) ===== */

	function initCategoryFilters() {
		document.querySelectorAll( '.tutorblock-filter-bar' ).forEach( function ( bar ) {
			var buttons = bar.querySelectorAll( '.tutorblock-filter-btn' );
			// Find the closest grid.
			var grid    = bar.closest( '.tutorblock-course-grid' );
			if ( ! grid ) return;
			var items   = Array.from( grid.querySelectorAll( '.tutorblock-grid-item[data-category]' ) );

			buttons.forEach( function ( btn ) {
				btn.addEventListener( 'click', function () {
					buttons.forEach( function ( b ) { b.classList.remove( 'is-active' ); } );
					btn.classList.add( 'is-active' );

					var filter = btn.dataset.filter;

					items.forEach( function ( item ) {
						var cats = ( item.dataset.category || '' ).split( ' ' );
						var show = filter === '*' || cats.indexOf( filter ) !== -1;
						item.style.display = show ? '' : 'none';
					} );
				} );
			} );
		} );
	}

	/* ===== UTILITIES ===== */

	function debounce( fn, delay ) {
		var t;
		return function () {
			clearTimeout( t );
			t = setTimeout( fn, delay );
		};
	}

	/* ===== VIDEO LIGHTBOX ===== */

	/**
	 * Initialise video lightboxes for hero-banner and video-preview blocks.
	 *
	 * Handles:
	 *  - [data-tb-video-trigger] — video-preview play button (opens modal)
	 *  - [data-tb-video]         — hero-banner secondary "Watch" button (opens modal)
	 */
	function initVideoLightbox() {

		/* ── video-preview: click on media area ── */
		document.querySelectorAll( '[data-tb-video-trigger]' ).forEach( function ( el ) {
			if ( el.dataset.tbVideoInit ) {
				return;
			}
			el.dataset.tbVideoInit = '1';

			var modal = el.closest( '.tutorblock-video-preview' )
				? el.closest( '.tutorblock-video-preview' ).querySelector( '.tutorblock-video-modal' )
				: null;

			if ( ! modal ) {
				return;
			}

			function openModal( e ) {
				// Don't fire if user clicked the overlay button (sign-up CTA).
				if ( e && e.target && e.target.closest( '.tutorblock-video-preview__overlay-btn' ) ) {
					return;
				}
				buildAndShowModal( modal );
			}

			el.addEventListener( 'click', openModal );
			el.addEventListener( 'keydown', function ( e ) {
				if ( e.key === 'Enter' || e.key === ' ' ) {
					e.preventDefault();
					openModal( e );
				}
			} );
		} );

		/* ── hero-banner: secondary "Watch Preview" button ── */
		document.querySelectorAll( '[data-tb-video]' ).forEach( function ( el ) {
			if ( el.dataset.tbVideoInit ) {
				return;
			}
			el.dataset.tbVideoInit = '1';

			var modal = el.closest( '.tutorblock-hero-banner' )
				? el.closest( '.tutorblock-hero-banner' ).querySelector( '.tutorblock-video-modal' )
				: null;

			if ( ! modal ) {
				return;
			}

			el.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				// Override embed URL from the button's data attribute.
				var videoUrl = el.dataset.tbVideo;
				var embedEl = modal.querySelector( '.tutorblock-video-modal__embed' );
				if ( embedEl && videoUrl ) {
					embedEl.dataset.tbEmbedUrl = getEmbedUrl( videoUrl );
					embedEl.dataset.tbDirect   = isDirectVideo( videoUrl ) ? '1' : '0';
				}
				buildAndShowModal( modal );
			} );
		} );

		/* ── shared close / backdrop logic ── */
		document.addEventListener( 'click', function ( e ) {
			if ( e.target.classList.contains( 'tutorblock-video-modal' ) ||
				e.target.classList.contains( 'tutorblock-video-modal__close' ) ) {
				closeModal( e.target.closest( '.tutorblock-video-modal' ) ||
					e.target.parentElement.closest( '.tutorblock-video-modal' ) );
			}
		} );

		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' ) {
				document.querySelectorAll( '.tutorblock-video-modal.is-open' ).forEach( closeModal );
			}
		} );
	}

	/**
	 * Build embed content inside modal and open it.
	 *
	 * @param {HTMLElement} modal
	 */
	function buildAndShowModal( modal ) {
		var embedEl  = modal.querySelector( '.tutorblock-video-modal__embed' );
		var embedUrl = embedEl ? embedEl.dataset.tbEmbedUrl : '';
		var isDirect = embedEl ? embedEl.dataset.tbDirect === '1' : false;

		if ( ! embedUrl || ! embedEl ) {
			return;
		}

		// Build the embed HTML.
		var html;
		if ( isDirect ) {
			html = '<video src="' + escAttr( embedUrl ) + '" controls autoplay style="width:100%;aspect-ratio:16/9;border-radius:4px;"></video>';
		} else {
			html = '<iframe src="' + escAttr( embedUrl ) + '" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen style="width:100%;aspect-ratio:16/9;border:0;border-radius:4px;" title="Video preview"></iframe>';
		}

		embedEl.innerHTML = html;
		modal.classList.add( 'is-open' );
		document.body.style.overflow = 'hidden';

		// Focus the close button for accessibility.
		var closeBtn = modal.querySelector( '.tutorblock-video-modal__close' );
		if ( closeBtn ) {
			closeBtn.focus();
		}
	}

	/**
	 * Close a video modal and destroy embed to stop playback.
	 *
	 * @param {HTMLElement} modal
	 */
	function closeModal( modal ) {
		if ( ! modal ) {
			return;
		}
		modal.classList.remove( 'is-open' );
		document.body.style.overflow = '';

		// Destroy the embed to stop video playback.
		var embedEl = modal.querySelector( '.tutorblock-video-modal__embed' );
		if ( embedEl ) {
			embedEl.innerHTML = '';
		}
	}

	/**
	 * Convert a video URL to an embeddable URL with autoplay.
	 *
	 * @param  {string} url
	 * @return {string}
	 */
	function getEmbedUrl( url ) {
		var ytMatch = url.match( /(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/ );
		if ( ytMatch ) {
			return 'https://www.youtube.com/embed/' + ytMatch[ 1 ] + '?autoplay=1&rel=0&modestbranding=1';
		}
		var vimeoMatch = url.match( /vimeo\.com\/(\d+)/ );
		if ( vimeoMatch ) {
			return 'https://player.vimeo.com/video/' + vimeoMatch[ 1 ] + '?autoplay=1';
		}
		return url; // Direct video.
	}

	/**
	 * Detect direct video file URLs.
	 *
	 * @param  {string} url
	 * @return {boolean}
	 */
	function isDirectVideo( url ) {
		return /\.(mp4|webm|ogg)(\?.*)?$/i.test( url );
	}

	/**
	 * Escape a string for use in an HTML attribute.
	 *
	 * @param  {string} str
	 * @return {string}
	 */
	function escAttr( str ) {
		return str
			.replace( /&/g, '&amp;' )
			.replace( /"/g, '&quot;' )
			.replace( /'/g, '&#39;' )
			.replace( /</g, '&lt;' )
			.replace( />/g, '&gt;' );
	}

	/* ===== INIT ===== */

	function init() {
		initCarousels();
		initCounters();
		initCategoryFilters();
		initVideoLightbox();
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
