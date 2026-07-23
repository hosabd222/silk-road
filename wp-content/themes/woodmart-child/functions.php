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
 * Premium luxury Mega Menu for "دسته‌ها" (#245) — full rebuild per client
 * brief (Dior / Louis Vuitton / Hermès style): fixed-height, no-scroll,
 * 3-column layout (category list / large preview image / products or a
 * decorative luxury filler when a category has none yet).
 *
 * Woodmart's own Mega_Menu_Walker renders a deep nested category tree,
 * which doesn't fit a curated 3-column boutique layout — so this replaces
 * the dropdown's inner content via JS instead of restyling the walker's
 * output. The trigger link itself ("دسته‌ها") and the show/hide-on-hover
 * behavior are untouched; only what's inside the panel changes.
 *
 * @return array
 */
function silken_luxury_megamenu_data() {
	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
			'parent'     => 0,
			'exclude'    => array( get_option( 'default_product_cat', 0 ) ),
		)
	);

	if ( is_wp_error( $terms ) || ! $terms ) {
		return array();
	}

	$data = array();

	foreach ( $terms as $term ) {
		$thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
		$image        = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'large' ) : '';
		if ( ! $image && function_exists( 'wc_placeholder_img_src' ) ) {
			$image = wc_placeholder_img_src( 'large' );
		}

		$products = get_posts(
			array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => 6,
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

		$product_data = array();
		foreach ( $products as $product_id ) {
			$product = wc_get_product( $product_id );
			if ( ! $product ) {
				continue;
			}
			$product_data[] = array(
				'name'  => $product->get_name(),
				'price' => $product->get_price_html(),
				'url'   => get_permalink( $product_id ),
				'image' => get_the_post_thumbnail_url( $product_id, 'medium' ),
			);
		}

		$data[] = array(
			'id'          => $term->term_id,
			'name'        => $term->name,
			'url'         => get_term_link( $term ),
			'description' => $term->description,
			'image'       => $image,
			'has_photo'   => (bool) $thumbnail_id,
			'is_silk'     => ( false !== mb_strpos( $term->name, 'ابریشم' ) ),
			'products'    => $product_data,
		);
	}

	// Categories with a real standout photo first — the most visually
	// complete ones lead, rather than an arbitrary/alphabetical order.
	usort(
		$data,
		function ( $a, $b ) {
			if ( $a['has_photo'] !== $b['has_photo'] ) {
				return $a['has_photo'] ? -1 : 1;
			}
			return strcmp( $a['name'], $b['name'] );
		}
	);

	return $data;
}

/**
 * @return void
 */
function silken_luxury_megamenu_css() {
	$categories = silken_luxury_megamenu_data();
	if ( ! $categories ) {
		return;
	}
	?>
	<style id="silken-luxury-megamenu">
		#menu-item-245 {
			--sm-gold: #b8894f;
			--sm-gold-bright: #c9a15c;
			--sm-gold-soft: rgba(184, 137, 79, .12);
			--sm-ink: #2a2118;
			--sm-ink-soft: rgba(42, 33, 24, .64);
			--sm-paper: #faf6ef;
			--sm-line: rgba(184, 137, 79, .22);
		}
		#menu-item-245.color-scheme-dark,
		#menu-item-245 > .wd-dropdown-menu.color-scheme-dark {
			--sm-ink: #f3ece0;
			--sm-ink-soft: rgba(243, 236, 224, .64);
			--sm-paper: #1c1712;
			--sm-line: rgba(201, 161, 92, .28);
		}

		/* Panel: exactly the site's own container width, a fixed premium
		   height, no internal scroll anywhere. */
		#menu-item-245 > .wd-dropdown-menu {
			width: min(1320px, calc(100vw - 24px)) !important;
			height: 680px !important;
			border: 1px solid var(--sm-line) !important;
			overflow: hidden !important;
			background-color: var(--sm-paper) !important;
			background-blend-mode: normal !important;
			transition: opacity .3s cubic-bezier(.16, 1, .3, 1), transform .3s cubic-bezier(.16, 1, .3, 1) !important;
		}
		#menu-item-245:not(:hover):not(:focus-within) > .wd-dropdown-menu {
			transform: translateY(-8px);
		}
		#menu-item-245 > .wd-dropdown-menu > .container.wd-entry-content {
			width: 100% !important;
			height: 100%;
			padding: 28px !important;
			box-sizing: border-box;
		}

		.silken-lux {
			display: grid;
			grid-template-columns: 320px 1fr 280px;
			gap: 28px;
			height: 100%;
		}

		/* ---- Column 1: category list, 2 sub-columns so ~25 categories fit
		   without scrolling at a genuinely readable size. ---- */
		.silken-lux__col1 {
			height: 100%;
			overflow: hidden;
			border-inline-end: 1px solid var(--sm-line);
			padding-inline-end: 20px;
		}
		.silken-lux__cats {
			height: 100%;
			display: grid;
			grid-template-columns: repeat(2, 1fr);
			grid-auto-flow: column;
			grid-template-rows: repeat(13, 1fr);
			gap: 2px 18px;
		}
		.silken-lux__cat {
			display: flex;
			align-items: center;
			gap: 10px;
			padding: 4px 8px;
			border-radius: 10px;
			text-decoration: none;
			color: var(--sm-ink);
			transition: background-color .2s ease, color .2s ease;
			min-width: 0;
		}
		.silken-lux__cat:hover,
		.silken-lux__cat:focus-visible,
		.silken-lux__cat.is-active {
			background-color: var(--sm-gold-soft);
			color: var(--sm-gold);
		}
		.silken-lux__cat-icon {
			width: 28px;
			height: 28px;
			border-radius: 8px;
			-o-object-fit: cover;
			object-fit: cover;
			flex: none;
			opacity: .9;
		}
		.silken-lux__cat-name {
			font-size: 13.5px;
			font-weight: 600;
			line-height: 1.3;
			overflow: hidden;
			text-overflow: ellipsis;
			white-space: nowrap;
		}
		.silken-lux__badge {
			display: inline-block;
			flex: none;
			width: 6px;
			height: 6px;
			border-radius: 50%;
			background: var(--sm-gold);
			margin-inline-start: 4px;
		}

		/* ---- Column 2: large fading preview image + info. ---- */
		.silken-lux__col2 {
			height: 100%;
			display: flex;
			flex-direction: column;
			gap: 16px;
			min-width: 0;
		}
		.silken-lux__preview {
			position: relative;
			flex: 1 1 auto;
			border-radius: 20px;
			overflow: hidden;
			box-shadow: 0 22px 44px rgba(0, 0, 0, .18);
			background: var(--sm-ink);
		}
		.silken-lux__preview img {
			position: absolute;
			inset: 0;
			width: 100%;
			height: 100%;
			-o-object-fit: cover;
			object-fit: cover;
			transition: opacity .35s ease;
		}
		.silken-lux__preview.is-swapping img {
			opacity: 0;
		}
		.silken-lux__preview::after {
			content: '';
			position: absolute;
			inset: 0;
			background: linear-gradient(to top, rgba(10, 8, 5, .55), rgba(10, 8, 5, 0) 55%);
		}
		.silken-lux__info {
			flex: none;
		}
		.silken-lux__info h3 {
			font-size: 20px;
			margin: 0 0 6px;
			color: var(--sm-ink);
		}
		.silken-lux__info p {
			font-size: 13px;
			color: var(--sm-ink-soft);
			line-height: 1.8;
			margin: 0 0 12px;
			max-height: 42px;
			overflow: hidden;
			display: -webkit-box;
			-webkit-line-clamp: 2;
			-webkit-box-orient: vertical;
		}
		.silken-lux__cta {
			display: inline-flex;
			align-items: center;
			gap: 6px;
			font-size: 12.5px;
			font-weight: 700;
			padding: 10px 20px;
			border-radius: 999px;
			background: var(--sm-gold);
			color: #fff;
			text-decoration: none;
			transition: background-color .25s ease, transform .2s ease;
		}
		.silken-lux__cta:hover,
		.silken-lux__cta:focus-visible {
			background-color: var(--sm-gold-bright);
			transform: translateY(-1px);
		}

		/* ---- Column 3: products if the category has any, otherwise a
		   decorative "brand promise" panel — never an empty gap. ---- */
		.silken-lux__col3 {
			height: 100%;
			overflow: hidden;
			border-inline-start: 1px solid var(--sm-line);
			padding-inline-start: 20px;
			display: flex;
			flex-direction: column;
		}
		.silken-lux__col3-label {
			font-size: 11px;
			font-weight: 700;
			letter-spacing: .06em;
			text-transform: uppercase;
			color: var(--sm-ink-soft);
			margin-bottom: 14px;
			flex: none;
		}
		.silken-lux__products {
			display: flex;
			flex-direction: column;
			gap: 8px;
			overflow: hidden;
		}
		.silken-lux__product {
			display: flex;
			align-items: center;
			gap: 10px;
			padding: 6px;
			border-radius: 12px;
			text-decoration: none;
			color: var(--sm-ink);
			transition: background-color .2s ease, transform .2s ease;
		}
		.silken-lux__product:hover,
		.silken-lux__product:focus-visible {
			background-color: var(--sm-gold-soft);
			transform: translateX(-2px);
		}
		[dir="rtl"] .silken-lux__product:hover {
			transform: translateX(2px);
		}
		.silken-lux__product img {
			width: 52px;
			height: 52px;
			border-radius: 12px;
			-o-object-fit: cover;
			object-fit: cover;
			flex: none;
		}
		.silken-lux__product-name {
			font-size: 12.5px;
			font-weight: 600;
			display: block;
			overflow: hidden;
			text-overflow: ellipsis;
			white-space: nowrap;
		}
		.silken-lux__product-price {
			font-size: 11.5px;
			color: var(--sm-gold);
			display: block;
		}
		.silken-lux__promises {
			display: flex;
			flex-direction: column;
			gap: 18px;
			flex: 1 1 auto;
			justify-content: center;
		}
		.silken-lux__promise {
			display: flex;
			align-items: flex-start;
			gap: 12px;
		}
		.silken-lux__promise-icon {
			flex: none;
			width: 34px;
			height: 34px;
			border-radius: 50%;
			background: var(--sm-gold-soft);
			color: var(--sm-gold);
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 15px;
		}
		.silken-lux__promise p {
			margin: 0;
			font-size: 12.5px;
			color: var(--sm-ink);
			line-height: 1.7;
		}

		#menu-item-245 a:focus-visible {
			outline: 2px solid var(--sm-gold);
			outline-offset: 2px;
		}

		@media (max-width: 1200px) {
			.silken-lux { grid-template-columns: 260px 1fr 240px; gap: 20px; }
		}
		@media (max-width: 1024px) {
			#menu-item-245 > .wd-dropdown-menu {
				width: calc(100vw - 24px) !important;
				height: auto !important;
				max-height: 82vh !important;
				overflow-y: auto !important;
			}
			.silken-lux {
				grid-template-columns: 1fr;
				grid-auto-rows: auto;
				height: auto;
			}
			.silken-lux__col1,
			.silken-lux__col2,
			.silken-lux__col3 {
				height: auto;
				border: none;
				padding-inline: 0;
			}
			.silken-lux__cats {
				grid-template-rows: none;
				grid-auto-flow: row;
			}
			.silken-lux__preview { aspect-ratio: 16 / 9; }
		}
		@media (prefers-reduced-motion: reduce) {
			#menu-item-245 > .wd-dropdown-menu,
			.silken-lux__preview img {
				transition: none !important;
			}
		}
	</style>
	<?php
}
add_action( 'wp_head', 'silken_luxury_megamenu_css', 100 );

/**
 * @return void
 */
function silken_luxury_megamenu_markup() {
	$categories = silken_luxury_megamenu_data();
	if ( ! $categories ) {
		return;
	}
	?>
	<template id="silken-lux-tpl">
		<div class="silken-lux">
			<nav class="silken-lux__col1" aria-label="<?php esc_attr_e( 'دسته‌بندی‌ها', 'woodmart-child' ); ?>">
				<div class="silken-lux__cats">
					<?php foreach ( $categories as $cat ) : ?>
						<a href="<?php echo esc_url( $cat['url'] ); ?>" class="silken-lux__cat" data-cat-id="<?php echo esc_attr( $cat['id'] ); ?>">
							<img class="silken-lux__cat-icon" src="<?php echo esc_url( $cat['image'] ); ?>" alt="" loading="lazy" />
							<span class="silken-lux__cat-name"><?php echo esc_html( $cat['name'] ); ?></span>
							<?php if ( $cat['is_silk'] ) : ?>
								<span class="silken-lux__badge" title="<?php esc_attr_e( 'ابریشم خالص', 'woodmart-child' ); ?>"></span>
							<?php endif; ?>
						</a>
					<?php endforeach; ?>
				</div>
			</nav>

			<div class="silken-lux__col2">
				<div class="silken-lux__preview" id="silken-lux-preview">
					<img id="silken-lux-preview-img" src="" alt="" />
				</div>
				<div class="silken-lux__info">
					<h3 id="silken-lux-title"></h3>
					<p id="silken-lux-desc"></p>
					<a href="#" class="silken-lux__cta" id="silken-lux-cta"><?php esc_html_e( 'مشاهده مجموعه', 'woodmart-child' ); ?></a>
				</div>
			</div>

			<div class="silken-lux__col3" id="silken-lux-col3"></div>
		</div>
	</template>
	<script>
	( function () {
		'use strict';

		var categories = <?php echo wp_json_encode( $categories ); ?>;
		var byId = {};
		categories.forEach( function ( c ) { byId[ c.id ] = c; } );

		var promiseIcons = [ '✋', '📜', '🧵', '🚚' ];
		var promises = [
			'<?php echo esc_js( __( 'صد در صد دستباف توسط استادکاران ایرانی', 'woodmart-child' ) ); ?>',
			'<?php echo esc_js( __( 'همراه با شناسنامه معتبر اصالت کالا', 'woodmart-child' ) ); ?>',
			'<?php echo esc_js( __( 'بافته شده از خالص‌ترین ابریشم و کرک', 'woodmart-child' ) ); ?>',
			'<?php echo esc_js( __( 'ارسال ایمن و بیمه‌شده به سراسر ایران', 'woodmart-child' ) ); ?>'
		];

		function renderCol3( cat ) {
			var col3 = document.getElementById( 'silken-lux-col3' );
			if ( ! col3 ) {
				return;
			}
			col3.innerHTML = '';

			if ( cat.products && cat.products.length ) {
				var label = document.createElement( 'span' );
				label.className = 'silken-lux__col3-label';
				label.textContent = '<?php echo esc_js( __( 'محصولات این مجموعه', 'woodmart-child' ) ); ?>';
				col3.appendChild( label );

				var list = document.createElement( 'div' );
				list.className = 'silken-lux__products';
				cat.products.forEach( function ( p ) {
					var a = document.createElement( 'a' );
					a.className = 'silken-lux__product';
					a.href = p.url;
					a.innerHTML = '<img src="' + p.image + '" alt="" loading="lazy" />' +
						'<span><span class="silken-lux__product-name">' + p.name + '</span>' +
						'<span class="silken-lux__product-price">' + p.price + '</span></span>';
					list.appendChild( a );
				} );
				col3.appendChild( list );
				return;
			}

			// No products in this category yet — fill the space with a
			// genuine, verifiable brand-promise panel instead of an empty gap
			// (or fake products).
			var label = document.createElement( 'span' );
			label.className = 'silken-lux__col3-label';
			label.textContent = '<?php echo esc_js( __( 'تعهد ما به شما', 'woodmart-child' ) ); ?>';
			col3.appendChild( label );

			var wrap = document.createElement( 'div' );
			wrap.className = 'silken-lux__promises';
			promises.forEach( function ( text, i ) {
				var row = document.createElement( 'div' );
				row.className = 'silken-lux__promise';
				row.innerHTML = '<span class="silken-lux__promise-icon">' + promiseIcons[ i ] + '</span><p>' + text + '</p>';
				wrap.appendChild( row );
			} );
			col3.appendChild( wrap );
		}

		function showCategory( cat, skipFade ) {
			var wrap  = document.getElementById( 'silken-lux-preview' );
			var img   = document.getElementById( 'silken-lux-preview-img' );
			var title = document.getElementById( 'silken-lux-title' );
			var desc  = document.getElementById( 'silken-lux-desc' );
			var cta   = document.getElementById( 'silken-lux-cta' );

			if ( ! wrap || ! img || img.src === cat.image ) {
				return;
			}

			function apply() {
				img.src = cat.image;
				title.textContent = cat.name;
				desc.textContent = cat.description || '';
				desc.style.display = cat.description ? '' : 'none';
				cta.href = cat.url;
				renderCol3( cat );
			}

			if ( skipFade ) {
				apply();
				return;
			}

			wrap.classList.add( 'is-swapping' );
			window.setTimeout( function () {
				apply();
				wrap.classList.remove( 'is-swapping' );
			}, 160 );
		}

		document.addEventListener( 'DOMContentLoaded', function () {
			var menuItem = document.getElementById( 'menu-item-245' );
			if ( ! menuItem ) {
				return;
			}

			var container = menuItem.querySelector( '.wd-dropdown-menu > .container.wd-entry-content' );
			var template  = document.getElementById( 'silken-lux-tpl' );
			if ( ! container || ! template ) {
				return;
			}

			container.innerHTML = '';
			container.appendChild( template.content.cloneNode( true ) );

			var items = menuItem.querySelectorAll( '.silken-lux__cat' );

			function activate( catId, el, skipFade ) {
				items.forEach( function ( i ) { i.classList.remove( 'is-active' ); } );
				if ( el ) {
					el.classList.add( 'is-active' );
				}
				if ( byId[ catId ] ) {
					showCategory( byId[ catId ], skipFade );
				}
			}

			items.forEach( function ( el ) {
				var catId = el.getAttribute( 'data-cat-id' );
				el.addEventListener( 'mouseenter', function () { activate( catId, el ); } );
				el.addEventListener( 'focus', function () { activate( catId, el ); } );
			} );

			if ( items.length ) {
				var firstId = items[ 0 ].getAttribute( 'data-cat-id' );
				activate( firstId, items[ 0 ], true );
			}
		} );
	} )();
	</script>
	<?php
}
add_action( 'wp_footer', 'silken_luxury_megamenu_markup', 20 );

