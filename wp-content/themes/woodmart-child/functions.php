<?php
add_action( 'wp_enqueue_scripts', 'woodmart_child_enqueue_styles', 10010 );
function woodmart_child_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'woodmart-style' ), woodmart_get_theme_info( 'Version' ) );
}

/**
 * Register @font-face rules for the Persian font files bundled in the parent
 * theme's /fonts/ folder. They were never wired up anywhere, so the browser
 * had no way to load them even though Theme Settings > Typography points to
 * "iranyekan" by name — it just silently fell back to system fonts.
 *
 * @return void
 */
function silk_road_enqueue_local_fonts() {
	wp_enqueue_style( 'silk-road-fonts', get_stylesheet_directory_uri() . '/fonts.css', array(), '1.0.0' );
}
add_action( 'wp_enqueue_scripts', 'silk_road_enqueue_local_fonts', 5 );

/**
 * Remove unnecessary billing fields from the WooCommerce checkout.
 *
 * @param array $fields Checkout fields.
 * @return array
 */
function silk_road_remove_checkout_billing_fields( $fields ) {
	unset( $fields['billing']['billing_company'] );
	unset( $fields['billing']['billing_address_2'] );

	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'silk_road_remove_checkout_billing_fields', 20 );

/**
 * Validate and normalize the Iranian mobile phone number at checkout.
 *
 * @return void
 */
function silk_road_validate_billing_phone() {
	$phone = isset( $_POST['billing_phone'] )
		? wc_clean( wp_unslash( $_POST['billing_phone'] ) )
		: '';

	// Convert Persian and Arabic numerals to English numerals before validation.
	$phone = strtr(
		$phone,
		array(
			'۰' => '0',
			'۱' => '1',
			'۲' => '2',
			'۳' => '3',
			'۴' => '4',
			'۵' => '5',
			'۶' => '6',
			'۷' => '7',
			'۸' => '8',
			'۹' => '9',
			'٠' => '0',
			'١' => '1',
			'٢' => '2',
			'٣' => '3',
			'٤' => '4',
			'٥' => '5',
			'٦' => '6',
			'٧' => '7',
			'٨' => '8',
			'٩' => '9',
		)
	);

	// Allow common visual separators, but reject other invalid characters.
	$phone = preg_replace( '/[\s\-\(\)]+/', '', $phone );

	if ( ! preg_match( '/^09\d{9}$/', $phone ) ) {
		wc_add_notice(
			__( 'لطفاً یک شماره موبایل معتبر ۱۱ رقمی که با 09 شروع می‌شود وارد کنید.', 'woodmart-child' ),
			'error'
		);

		return;
	}

	// Store the normalized value with the order.
	$_POST['billing_phone'] = $phone;
}
add_action( 'woocommerce_checkout_process', 'silk_road_validate_billing_phone' );

/**
 * Enqueue client-side Iranian mobile validation assets on the classic checkout.
 *
 * Server-side validation remains in place and must not be removed.
 *
 * @return void
 */
function silk_road_enqueue_checkout_phone_validation_assets() {
	if ( ! function_exists( 'is_checkout' ) || ! is_checkout() || is_order_received_page() ) {
		return;
	}

	wp_enqueue_script(
		'sweetalert2',
		get_stylesheet_directory_uri() . '/assets/js/sweetalert2.all.min.js',
		array( 'jquery' ),
		'11',
		true
	);

	wp_register_style( 'silk-road-checkout-phone-validation', false, array(), null );
	wp_enqueue_style( 'silk-road-checkout-phone-validation' );
	wp_add_inline_style(
		'silk-road-checkout-phone-validation',
		'
			.woocommerce-checkout #billing_phone.silk-road-phone-invalid {
				border-color: #dc2626 !important;
				box-shadow: 0 0 0 3px rgba( 220, 38, 38, 0.14 ) !important;
				animation: silk-road-phone-shake 0.5s ease-in-out;
			}

			.silk-road-phone-toast {
				direction: rtl;
			}

			@keyframes silk-road-phone-shake {
				0%, 100% { transform: translateX( 0 ); }
				20%, 60% { transform: translateX( -7px ); }
				40%, 80% { transform: translateX( 7px ); }
			}
		'
	);

	wp_add_inline_script(
		'sweetalert2',
		<<<'JS'
		jQuery( function( $ ) {
			'use strict';

			var phoneDigits = {
				'۰': '0', '۱': '1', '۲': '2', '۳': '3', '۴': '4',
				'۵': '5', '۶': '6', '۷': '7', '۸': '8', '۹': '9',
				'٠': '0', '١': '1', '٢': '2', '٣': '3', '٤': '4',
				'٥': '5', '٦': '6', '٧': '7', '٨': '8', '٩': '9'
			};

			/**
			 * Convert Persian/Arabic digits and remove common visual separators.
			 *
			 * @param {string} value Raw input value.
			 * @return {string} Normalized phone number.
			 */
			function normalizePhone( value ) {
				return String( value )
					.replace( /[۰-۹٠-٩]/g, function( digit ) {
						return phoneDigits[ digit ];
					} )
					.replace( /[\s\-()]+/g, '' );
			}

			function isValidIranianMobile( phone ) {
				return /^09\d{9}$/.test( phone );
			}

			function showPhoneError() {
				if ( 'undefined' === typeof window.Swal ) {
					return;
				}

				window.Swal.fire( {
					toast: true,
					position: 'top-end',
					icon: 'error',
					title: 'شماره موبایل باید ۱۱ رقم و با 09 شروع شود.',
					showConfirmButton: false,
					timer: 3500,
					timerProgressBar: true,
					customClass: {
						popup: 'silk-road-phone-toast'
					}
				} );
			}

			function markPhoneInvalid( $phoneField ) {
				$phoneField.removeClass( 'silk-road-phone-invalid' );

				// Restart the CSS animation each time checkout is submitted incorrectly.
				void $phoneField[ 0 ].offsetWidth;
				$phoneField.addClass( 'silk-road-phone-invalid' ).attr( 'aria-invalid', 'true' ).trigger( 'focus' );
			}

			var $checkoutForm = $( 'form.checkout' );

			$checkoutForm.on( 'checkout_place_order.silkRoadPhone', function() {
				var $phoneField = $( '#billing_phone' );

				if ( ! $phoneField.length ) {
					return true;
				}

				var phone = normalizePhone( $phoneField.val() );
				$phoneField.val( phone );

				if ( isValidIranianMobile( phone ) ) {
					$phoneField.removeClass( 'silk-road-phone-invalid' ).removeAttr( 'aria-invalid' );
					return true;
				}

				markPhoneInvalid( $phoneField );
				showPhoneError();

				return false;
			} );

			$checkoutForm.on( 'input.silkRoadPhone blur.silkRoadPhone', '#billing_phone', function() {
				var $phoneField = $( this );
				var phone       = normalizePhone( $phoneField.val() );

				if ( isValidIranianMobile( phone ) ) {
					$phoneField.removeClass( 'silk-road-phone-invalid' ).removeAttr( 'aria-invalid' );
				}
			} );
		} );
JS
	);
}
add_action( 'wp_enqueue_scripts', 'silk_road_enqueue_checkout_phone_validation_assets', 20 );

/**
 * Enqueue add-to-cart button animation (ripple) script sitewide.
 *
 * @return void
 */
function silk_road_enqueue_add_to_cart_animation() {
	wp_register_style( 'silk-road-cart-animation', false, array(), null );
	wp_enqueue_style( 'silk-road-cart-animation' );

	wp_add_inline_script(
		'jquery',
		<<<'JS'
		jQuery( function( $ ) {
			'use strict';

			/**
			 * Add a CSS ripple effect and cart-icon bounce on add-to-cart click.
			 */
			function triggerRipple( $btn ) {
				$btn.removeClass( 'ripple-active' );
				void $btn[ 0 ].offsetWidth;           // restart animation
				$btn.addClass( 'ripple-active' );
				setTimeout( function() {
					$btn.removeClass( 'ripple-active' );
				}, 650 );
			}

			// Ripple on loop / archive add-to-cart links & buttons.
			$( document ).on( 'click', '.wd-add-btn a, .wd-add-btn button, .add_to_cart_button, .add-to-cart-loop, .single_add_to_cart_button, .wd-buy-now-btn, .wd-sticky-add-to-cart', function( e ) {
				triggerRipple( $( this ) );
			} );

			// Bounce the header cart icon after a successful AJAX add-to-cart.
			$( document.body ).on( 'added_to_cart', function( e, fragments, cart_hash, $button ) {
				var $cartIcon = $( '.wd-header-cart .wd-cart-icon' );
				if ( $cartIcon.length ) {
					$cartIcon.removeClass( 'added-item' );
					void $cartIcon[ 0 ].offsetWidth;
					$cartIcon.addClass( 'added-item' );
					setTimeout( function() {
						$cartIcon.removeClass( 'added-item' );
					}, 650 );
				}
			} );
		} );
	JS
	);
}
add_action( 'wp_enqueue_scripts', 'silk_road_enqueue_add_to_cart_animation', 30 );

/**
 * WoodMart defers several header/layout scripts (header builder spacing, menu offsets, etc.)
 * until the visitor's first mouse move, scroll, key press, or touch — it dispatches its own
 * `wdEventStarted` event only then. On this site that caused a visible layout jump (the hero
 * content shifting down away from the header) right on the very first interaction. Fire the
 * same trigger automatically shortly after page load instead of waiting for a real interaction.
 *
 * @return void
 */
function silk_road_trigger_woodmart_deferred_init() {
	wp_register_script( 'silk-road-early-wd-event', false, array(), null, true );
	wp_enqueue_script( 'silk-road-early-wd-event' );

	wp_add_inline_script(
		'silk-road-early-wd-event',
		<<<'JS'
		window.addEventListener( 'load', function () {
			setTimeout( function () {
				window.dispatchEvent( new Event( 'scroll' ) );
			}, 150 );
		} );
		JS
	);
}
add_action( 'wp_enqueue_scripts', 'silk_road_trigger_woodmart_deferred_init', 40 );

/**
 * Render a scrolling ticker of rotating trust/notice messages via [dynamic_product_notice].
 *
 * @return string
 */
function product_dynamic_notice_shortcode() {
	ob_start();
	?>
	<style>
		#dynamic-product-message {
			overflow: hidden;
			height: 30px;
			position: relative;
			display: flex;
			align-items: center;
			background: #f8f9fa;
			padding: 5px 15px;
			border-radius: 8px;
			border-right: 3px solid #007cba;
			font-size: 14px;
		}

		#product-message-content {
			display: inline-block;
			transition: transform 0.25s ease, opacity 0.25s ease;
			will-change: transform, opacity;
			width: 100%;
		}

		.scroll-out {
			transform: translateY(-100%);
			opacity: 0;
		}

		.scroll-in {
			transform: translateY(100%);
			opacity: 0;
		}

		.scroll-reset {
			transform: translateY(0%);
			opacity: 1;
		}

		/* استایل برای موبایل */
		@media (max-width: 768px) {
			#dynamic-product-message {
				font-size: 12px;
				padding: 5px 10px;
				height: 28px;
			}
		}
	</style>

	<div id="dynamic-product-message">
		<span id="product-message-content">در حال بارگذاری...</span>
	</div>

	<script>
		document.addEventListener("DOMContentLoaded", function () {
			const messages = [
				"🏵️ دارای شناسنامه معتبر و اصالت کالا",
				"🎁 ارسال با پکیجینگ فاخر و اختصاصی",
				"🧵 بافته شده از خالص‌ترین ابریشم طبیعی",
				"✋ صد در صد دستباف، اثری منحصربه‌فرد از هنر ایرانی",
				"🚚 ارسال ایمن و بیمه‌شده به سراسر ایران"
			];

			let index = 0;
			const msgEl = document.getElementById("product-message-content");

			function changeMessage() {
				msgEl.style.transition = 'transform 0.2s ease, opacity 0.2s ease';
				msgEl.style.transform = 'translateY(-100%)';
				msgEl.style.opacity = '0';

				setTimeout(() => {
					msgEl.textContent = messages[index];
					index = (index + 1) % messages.length;
					msgEl.style.transform = 'translateY(100%)';
					msgEl.style.opacity = '0';

					setTimeout(() => {
						msgEl.style.transition = 'transform 0.2s ease, opacity 0.2s ease';
						msgEl.style.transform = 'translateY(0%)';
						msgEl.style.opacity = '1';
					}, 50);
				}, 200);
			}

			setTimeout(() => {
				msgEl.textContent = messages[0];
				msgEl.style.opacity = '1';
				msgEl.style.transform = 'translateY(0%)';
				index = 1;
			}, 100);

			setInterval(changeMessage, 2500);
		});
	</script>
	<?php
	return ob_get_clean();
}
add_shortcode( 'dynamic_product_notice', 'product_dynamic_notice_shortcode' );

/**
 * Data source for the "Silken Memories" timeline/album template.
 *
 * The 'video' URLs below are public placeholder clips standing in for the
 * real footage. Once videos move to ArvanCloud object storage, replace each
 * 'video' value with its object-storage URL — the template and JS only ever
 * read from this array, so nothing else needs to change. The 'silken_memories_items'
 * filter lets a future meta box / CPT swap this array out entirely.
 *
 * @return array[] List of { title, period, poster, video }.
 */
function silken_memories_get_items() {
	$placeholder_poster = function_exists( 'wc_placeholder_img_src' ) ? wc_placeholder_img_src( 'woocommerce_single' ) : '';

	$items = array(
		array(
			'title'  => 'ریشه‌های استاد نساج',
			'period' => '۱۳۵۰',
			'poster' => $placeholder_poster,
			'video'  => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4',
		),
		array(
			'title'  => 'شکوفایی کارگاه خانوادگی',
			'period' => '۱۳۷۰',
			'poster' => $placeholder_poster,
			'video'  => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerJoyrides.mp4',
		),
		array(
			'title'  => 'اولین نمایشگاه بین‌المللی',
			'period' => '۱۳۸۵',
			'poster' => $placeholder_poster,
			'video'  => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerMeltdowns.mp4',
		),
		array(
			'title'  => 'ثبت میراث فرهنگی',
			'period' => '۱۳۹۵',
			'poster' => $placeholder_poster,
			'video'  => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4',
		),
		array(
			'title'  => 'نسل جدید بافندگان',
			'period' => '۱۴۰۲',
			'poster' => $placeholder_poster,
			'video'  => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4',
		),
		array(
			'title'  => 'کارگاه امروز',
			'period' => '۱۴۰۴',
			'poster' => $placeholder_poster,
			'video'  => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/Sintel.mp4',
		),
	);

	return apply_filters( 'silken_memories_items', $items );
}

/**
 * Enqueue the right Silken Memories assets for whichever variant page
 * template is active — the original album, or one of the three "modern"
 * concepts (cinematic / glass / bento). Swiper and GLightbox have no reason
 * to load anywhere else on the site.
 *
 * @return void
 */
function silken_memories_enqueue_assets() {
	$variants = array(
		'template-silken-memories.php'         => 'silken-memories',
		'template-silken-modern-cinematic.php' => 'silken-modern-cinematic',
		'template-silken-modern-glass.php'     => 'silken-modern-glass',
		'template-silken-modern-bento.php'     => 'silken-modern-bento',
	);

	$active_slug = null;

	foreach ( $variants as $template_file => $slug ) {
		if ( is_page_template( $template_file ) ) {
			$active_slug = $slug;
			break;
		}
	}

	if ( ! $active_slug ) {
		return;
	}

	wp_enqueue_style( 'glightbox', get_stylesheet_directory_uri() . '/assets/css/glightbox.min.css', array(), '3.3.1' );
	wp_enqueue_script( 'glightbox', get_stylesheet_directory_uri() . '/assets/js/glightbox.min.js', array(), '3.3.1', true );

	$script_deps = array( 'glightbox' );

	// Only the original album view uses Swiper. Elementor and Woodmart both already
	// register a global 'swiper' handle (bundled v8) that silently wins over a
	// same-named re-registration, so this loads under a distinct handle to
	// deterministically get the version it was built against.
	if ( 'silken-memories' === $active_slug ) {
		wp_enqueue_style( 'silken-swiper', get_stylesheet_directory_uri() . '/assets/css/swiper-bundle.min.css', array(), '11' );
		wp_enqueue_script( 'silken-swiper', get_stylesheet_directory_uri() . '/assets/js/swiper-bundle.min.js', array(), '11', true );
		$script_deps[] = 'silken-swiper';
	}

	wp_enqueue_style( $active_slug, get_stylesheet_directory_uri() . '/assets/' . $active_slug . '.css', array(), '1.0.0' );
	wp_enqueue_script( $active_slug, get_stylesheet_directory_uri() . '/assets/' . $active_slug . '.js', $script_deps, '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'silken_memories_enqueue_assets', 50 );

/**
 * Resolve which category-showcase design to render on a product_cat archive.
 * Preview a specific one via ?cat_style=cinematic|editorial|gallery on any
 * category URL; change $default below once one is picked for good.
 *
 * @return string
 */
function silken_category_get_style() {
	$styles  = array( 'cinematic', 'editorial', 'gallery' );
	$default = 'cinematic';

	if ( isset( $_GET['cat_style'] ) ) {
		$requested = sanitize_key( wp_unslash( $_GET['cat_style'] ) );
		if ( in_array( $requested, $styles, true ) ) {
			return $requested;
		}
	}

	return $default;
}

/**
 * A handful of standout images for a category showcase: the category's own
 * thumbnail first (set via term meta 'thumbnail_id'), then featured images
 * from products inside it, deduplicated.
 *
 * @param WP_Term $term
 * @param int     $limit
 * @return string[] Image URLs.
 */
function silken_category_gallery_images( $term, $limit = 5 ) {
	$images = array();

	$thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
	if ( $thumbnail_id ) {
		$src = wp_get_attachment_image_url( $thumbnail_id, 'large' );
		if ( $src ) {
			$images[] = $src;
		}
	}

	$product_ids = get_posts(
		array(
			'post_type'      => 'product',
			'posts_per_page' => $limit + 4,
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => $term->term_id,
				),
			),
		)
	);

	foreach ( $product_ids as $product_id ) {
		if ( count( $images ) >= $limit ) {
			break;
		}
		$src = get_the_post_thumbnail_url( $product_id, 'large' );
		if ( $src && ! in_array( $src, $images, true ) ) {
			$images[] = $src;
		}
	}

	if ( empty( $images ) && function_exists( 'wc_placeholder_img_src' ) ) {
		$images[] = wc_placeholder_img_src( 'large' );
	}

	return $images;
}

/**
 * Sibling categories (or, for a top-level term, other top-level categories)
 * to surface as "related categories" at the end of a showcase page.
 *
 * @param WP_Term $term
 * @param int     $limit
 * @return WP_Term[]
 */
function silken_category_related_terms( $term, $limit = 6 ) {
	$related = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
			'parent'     => $term->parent,
			'exclude'    => array( $term->term_id ),
			'number'     => $limit,
		)
	);

	return is_wp_error( $related ) ? array() : $related;
}

/**
 * Enqueue the matching CSS/JS for the active category-showcase style, only
 * on product_cat taxonomy archives.
 *
 * @return void
 */
function silken_category_enqueue_assets() {
	if ( ! is_tax( 'product_cat' ) ) {
		return;
	}

	$style  = silken_category_get_style();
	$handle = 'silken-category-' . $style;

	wp_enqueue_style( $handle, get_stylesheet_directory_uri() . '/assets/category-' . $style . '.css', array(), '1.0.0' );
	wp_enqueue_script( $handle, get_stylesheet_directory_uri() . '/assets/category-' . $style . '.js', array(), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'silken_category_enqueue_assets', 50 );

/**
 * Resolve which blog-index design to render. Preview via
 * ?blog_style=magazine|editorial|cards on the blog page; change $default
 * below once one is picked for good.
 *
 * @return string
 */
function silken_blog_get_style() {
	$styles  = array( 'magazine', 'editorial', 'cards' );
	$default = 'magazine';

	if ( isset( $_GET['blog_style'] ) ) {
		$requested = sanitize_key( wp_unslash( $_GET['blog_style'] ) );
		if ( in_array( $requested, $styles, true ) ) {
			return $requested;
		}
	}

	return $default;
}

/**
 * Enqueue the matching CSS/JS for the active blog-index style, only on the
 * blog posts index itself.
 *
 * @return void
 */
function silken_blog_enqueue_assets() {
	if ( ! is_home() ) {
		return;
	}

	$style  = silken_blog_get_style();
	$handle = 'silken-blog-' . $style;

	wp_enqueue_style( $handle, get_stylesheet_directory_uri() . '/assets/blog-' . $style . '.css', array(), '1.0.0' );
	wp_enqueue_script( $handle, get_stylesheet_directory_uri() . '/assets/blog-' . $style . '.js', array(), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'silken_blog_enqueue_assets', 50 );

/**
 * The current post's primary category name, for the blog index designs.
 *
 * @return string
 */
function silken_blog_primary_category() {
	$categories = get_the_category();

	return ! empty( $categories ) ? $categories[0]->name : __( 'عمومی', 'woodmart-child' );
}

/**
 * Resolve which footer skin to apply. Preview via ?footer_style=classic-gold
 * |aurora-dark|ornate-heritage on ANY page (the footer is site-wide); change
 * $default below once one is picked for good.
 *
 * These are pure CSS reskins of Woodmart's own footer markup/widget areas —
 * intentionally not a footer.php override, since that file also closes the
 * page wrapper and body/html tags opened in header.php and wires up
 * wp_footer(); replacing it would risk breaking every page on the site for
 * a component that only needs a new look, not new structure.
 *
 * @return string
 */
function silken_footer_get_style() {
	$styles  = array( 'classic-gold', 'aurora-dark', 'ornate-heritage' );
	$default = 'aurora-dark'; // picked by the client on 2026-07-23

	if ( isset( $_GET['footer_style'] ) ) {
		$requested = sanitize_key( wp_unslash( $_GET['footer_style'] ) );
		if ( in_array( $requested, $styles, true ) ) {
			return $requested;
		}
	}

	return $default;
}

/**
 * Enqueue the active footer skin's CSS site-wide.
 *
 * @return void
 */
function silken_footer_enqueue_assets() {
	$style = silken_footer_get_style();

	wp_enqueue_style( 'silken-footer-' . $style, get_stylesheet_directory_uri() . '/assets/footer-' . $style . '.css', array(), '1.0.0' );
}
add_action( 'wp_enqueue_scripts', 'silken_footer_enqueue_assets', 60 );

/**
 * Render the designed footer content.
 *
 * Woodmart's own footer widget areas (footer-1..footer-7) are currently
 * empty — nothing is configured in the Customizer — so there is no existing
 * markup to reskin with CSS alone. Rather than overriding footer.php (which
 * also closes the page wrapper and the body/html tags opened in header.php,
 * and wires up wp_footer() for cart fragments/analytics/etc — replacing it
 * risks breaking every page for a component that just needs real content),
 * this hooks into wp_footer additively: it prints AFTER Woodmart's own
 * (empty) <footer> closes, which is visually indistinguishable from being
 * the page footer, with zero risk to the surrounding template.
 *
 * @return void
 */
function silken_footer_render() {
	if ( is_admin() ) {
		return;
	}

	$style     = silken_footer_get_style();
	$part_path = get_stylesheet_directory() . '/template-parts/footer/' . $style . '.php';
	if ( ! file_exists( $part_path ) ) {
		$part_path = get_stylesheet_directory() . '/template-parts/footer/classic-gold.php';
	}

	$top_categories = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'parent'     => 0,
			'hide_empty' => false,
			'number'     => 6,
			'exclude'    => array( get_option( 'default_product_cat', 0 ) ),
		)
	);
	if ( is_wp_error( $top_categories ) ) {
		$top_categories = array();
	}

	include $part_path;
}
add_action( 'wp_footer', 'silken_footer_render', 5 );

/**
 * Enqueue the matching CSS/JS for whichever "About Us" page template is
 * active (heritage / workshop / values).
 *
 * @return void
 */
function silken_about_enqueue_assets() {
	$variants = array(
		'template-about-heritage.php' => 'about-heritage',
		'template-about-workshop.php' => 'about-workshop',
		'template-about-values.php'   => 'about-values',
	);

	foreach ( $variants as $template_file => $slug ) {
		if ( is_page_template( $template_file ) ) {
			wp_enqueue_style( $slug, get_stylesheet_directory_uri() . '/assets/' . $slug . '.css', array(), '1.0.0' );
			wp_enqueue_script( $slug, get_stylesheet_directory_uri() . '/assets/' . $slug . '.js', array(), '1.0.0', true );
			return;
		}
	}
}
add_action( 'wp_enqueue_scripts', 'silken_about_enqueue_assets', 50 );

/**
 * Resolve which login/register design to render on the My Account page's
 * logged-out view. Preview via ?login_style=classic|split|card on the
 * My Account page; change $default below once one is picked for good.
 *
 * Only the WRAPPER markup/CSS differs between the three — every form field
 * name/id, nonce, and action hook is identical to WooCommerce's own
 * templates/myaccount/form-login.php, unchanged, so WC_Form_Handler and any
 * plugin hooked into the login/register hooks keep working exactly as before.
 *
 * @return string
 */
function silken_login_get_style() {
	$styles  = array( 'classic', 'split', 'card' );
	$default = 'card'; // picked by the client on 2026-07-23

	if ( isset( $_GET['login_style'] ) ) {
		$requested = sanitize_key( wp_unslash( $_GET['login_style'] ) );
		if ( in_array( $requested, $styles, true ) ) {
			return $requested;
		}
	}

	return $default;
}

/**
 * Enqueue the matching CSS/JS for the active login/register style, only on
 * the (logged-out) My Account page.
 *
 * @return void
 */
function silken_login_enqueue_assets() {
	if ( ! function_exists( 'is_account_page' ) || ! is_account_page() || is_user_logged_in() ) {
		return;
	}

	$style  = silken_login_get_style();
	$handle = 'silken-login-' . $style;

	wp_enqueue_style( $handle, get_stylesheet_directory_uri() . '/assets/login-' . $style . '.css', array(), '1.0.0' );
	wp_enqueue_script( $handle, get_stylesheet_directory_uri() . '/assets/login-' . $style . '.js', array(), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'silken_login_enqueue_assets', 50 );

/**
 * Render the WooCommerce login form fields. Field names/ids/nonce/hooks are
 * copied verbatim from WooCommerce's own templates/myaccount/form-login.php
 * (login half) so WC_Form_Handler processes the POST exactly as it always
 * has — only the three login-*.php wrappers around this differ visually.
 *
 * @return void
 */
function silken_render_wc_login_fields() {
	?>
	<form class="woocommerce-form woocommerce-form-login login" method="post" novalidate>
		<?php do_action( 'woocommerce_login_form_start' ); ?>

		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="username"><?php esc_html_e( 'نام کاربری یا ایمیل', 'woodmart-child' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) && is_string( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" />
		</p>
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="password"><?php esc_html_e( 'رمز عبور', 'woodmart-child' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
			<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" required aria-required="true" />
		</p>

		<?php do_action( 'woocommerce_login_form' ); ?>

		<p class="form-row">
			<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
				<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'مرا به خاطر بسپار', 'woodmart-child' ); ?></span>
			</label>
			<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
			<button type="submit" class="woocommerce-button button woocommerce-form-login__submit" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'ورود', 'woodmart-child' ); ?></button>
		</p>
		<p class="woocommerce-LostPassword lost_password">
			<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'رمز عبور را فراموش کرده‌اید؟', 'woodmart-child' ); ?></a>
		</p>

		<?php do_action( 'woocommerce_login_form_end' ); ?>
	</form>
	<?php
}

/**
 * Render the WooCommerce registration form fields. Field names/ids/nonce/
 * hooks are copied verbatim from WooCommerce's own
 * templates/myaccount/form-login.php (register half) — see
 * silken_render_wc_login_fields() docblock for why.
 *
 * @return void
 */
function silken_render_wc_register_fields() {
	?>
	<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?>>
		<?php do_action( 'woocommerce_register_form_start' ); ?>

		<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="reg_username"><?php esc_html_e( 'نام کاربری', 'woodmart-child' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" />
			</p>
		<?php endif; ?>

		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="reg_email"><?php esc_html_e( 'آدرس ایمیل', 'woodmart-child' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
			<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" required aria-required="true" />
		</p>

		<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="reg_password"><?php esc_html_e( 'رمز عبور', 'woodmart-child' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
				<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" required aria-required="true" />
			</p>
		<?php else : ?>
			<p><?php esc_html_e( 'لینک تنظیم رمز عبور جدید به ایمیل شما ارسال خواهد شد.', 'woodmart-child' ); ?></p>
		<?php endif; ?>

		<?php do_action( 'woocommerce_register_form' ); ?>

		<p class="woocommerce-form-row form-row">
			<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
			<button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'ثبت‌نام', 'woodmart-child' ); ?></button>
		</p>

		<?php do_action( 'woocommerce_register_form_end' ); ?>
	</form>
	<?php
}

/**
 * Resolve which logged-in account-panel design to render. Preview via
 * ?account_style=sidebar|tabs|cards on the My Account page while logged
 * in; change $default below once one is picked for good.
 *
 * All three wrap the exact same two WooCommerce hooks
 * (woocommerce_account_navigation / woocommerce_account_content) that
 * myaccount/my-account.php normally calls directly — the actual nav items,
 * capability checks, order/address/downloads endpoints, and nonces are all
 * still rendered by WooCommerce core, completely untouched. Only the
 * surrounding layout/CSS differs.
 *
 * @return string
 */
function silken_account_get_style() {
	$styles  = array( 'sidebar', 'tabs', 'cards' );
	$default = 'tabs'; // picked by the client on 2026-07-23

	if ( isset( $_GET['account_style'] ) ) {
		$requested = sanitize_key( wp_unslash( $_GET['account_style'] ) );
		if ( in_array( $requested, $styles, true ) ) {
			return $requested;
		}
	}

	return $default;
}

/**
 * Enqueue the matching CSS/JS for the active account-panel style, only on
 * the (logged-in) My Account page.
 *
 * @return void
 */
function silken_account_enqueue_assets() {
	if ( ! function_exists( 'is_account_page' ) || ! is_account_page() || ! is_user_logged_in() ) {
		return;
	}

	$style  = silken_account_get_style();
	$handle = 'silken-account-' . $style;

	wp_enqueue_style( $handle, get_stylesheet_directory_uri() . '/assets/account-' . $style . '.css', array(), '1.0.0' );
	wp_enqueue_script( $handle, get_stylesheet_directory_uri() . '/assets/account-' . $style . '.js', array(), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'silken_account_enqueue_assets', 50 );

/**
 * Categories megamenu (#245): fixed-width panel + responsive multi-column
 * grid for its category list.
 *
 * This is also saved in Theme Settings > Custom CSS (xts-woodmart-options),
 * but Woodmart only recompiles that into the actual served CSS file when
 * settings are saved through wp-admin's Theme Settings screen (the
 * Themesettingscss class writes its cache file on the 'xts_after_theme_settings'
 * action, gated on $_GET['page'] === 'xts_theme_settings') — a direct
 * update_option() call doesn't trigger that regeneration, so the saved value
 * alone never reached the page. Printing it directly here guarantees it's
 * live regardless of when/whether that cache gets rebuilt.
 *
 * @return void
 */
function silken_megamenu_css_fix() {
	?>
	<style id="silken-megamenu-fix">
		@media (min-width: 1025px) {
			#menu-item-245 > .wd-dropdown-menu {
				width: min(1180px, calc(100vw - 32px)) !important;
			}
			#menu-item-245 > .wd-dropdown-menu .wd-sub-menu.wd-grid-f-inline {
				display: flex !important;
				flex-wrap: wrap !important;
				align-content: flex-start !important;
				max-height: min(65vh, 480px);
				overflow-y: auto;
				padding-inline-end: 6px;
			}
			#menu-item-245 > .wd-dropdown-menu .wd-sub-menu.wd-grid-f-inline > li.wd-col {
				flex: 0 0 200px !important;
				width: 200px !important;
			}
		}
		@media (max-width: 1024px) {
			#menu-item-245 > .wd-dropdown-menu {
				width: calc(100vw - 32px) !important;
			}
			#menu-item-245 > .wd-dropdown-menu .wd-sub-menu.wd-grid-f-inline {
				display: flex !important;
				flex-wrap: wrap !important;
				align-content: flex-start !important;
				max-height: min(65vh, 480px);
				overflow-y: auto;
			}
			#menu-item-245 > .wd-dropdown-menu .wd-sub-menu.wd-grid-f-inline > li.wd-col {
				flex: 0 0 45% !important;
				width: 45% !important;
			}
		}
	</style>
	<?php
}
add_action( 'wp_head', 'silken_megamenu_css_fix', 100 );
