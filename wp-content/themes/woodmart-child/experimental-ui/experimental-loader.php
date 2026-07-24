<?php
/**
 * Loader for wp-content/themes/woodmart-child/experimental-ui/.
 *
 * Zero-residue architecture: every enqueue below is gated on
 * is_page_template() for that exact experiment's own template file, so
 * nothing here ever fires on a WooCommerce/Woodmart page. To remove any
 * (or all) of these experiments later, delete the corresponding
 * template/CSS/JS files (or the whole experimental-ui/ folder) and the
 * single `require` line for this file in functions.php — no other hook,
 * option, or enqueue anywhere else in the theme references this folder,
 * so nothing is left behind: no dead code, no orphaned requests, no
 * overhead on the core shop.
 */

defined( 'ABSPATH' ) || exit;

define( 'SILK_EXP_URI', get_stylesheet_directory_uri() . '/experimental-ui' );
define( 'SILK_EXP_VER', '1.0.0' );

/**
 * Idea 1 — Shoppable Hotspots.
 *
 * @return void
 */
function silk_exp_enqueue_hotspots() {
	if ( ! is_page_template( 'experimental-ui/template-hotspots.php' ) ) {
		return;
	}
	wp_enqueue_style( 'silk-exp-hotspots', SILK_EXP_URI . '/assets/css/hotspots.css', array(), SILK_EXP_VER );
	wp_enqueue_script( 'silk-exp-hotspots', SILK_EXP_URI . '/assets/js/hotspots.js', array(), SILK_EXP_VER, true );
}
add_action( 'wp_enqueue_scripts', 'silk_exp_enqueue_hotspots' );

/**
 * Idea 2 — Masonry & Parallax Grid.
 *
 * @return void
 */
function silk_exp_enqueue_masonry_parallax() {
	if ( ! is_page_template( 'experimental-ui/template-masonry-parallax.php' ) ) {
		return;
	}
	wp_enqueue_style( 'silk-exp-masonry-parallax', SILK_EXP_URI . '/assets/css/masonry-parallax.css', array(), SILK_EXP_VER );
	wp_enqueue_script( 'silk-exp-masonry-parallax', SILK_EXP_URI . '/assets/js/masonry-parallax.js', array(), SILK_EXP_VER, true );
}
add_action( 'wp_enqueue_scripts', 'silk_exp_enqueue_masonry_parallax' );

/**
 * Idea 3 — Texture Reveal Hover (pure CSS, no JS file needed).
 *
 * @return void
 */
function silk_exp_enqueue_texture_hover() {
	if ( ! is_page_template( 'experimental-ui/template-texture-hover.php' ) ) {
		return;
	}
	wp_enqueue_style( 'silk-exp-texture-hover', SILK_EXP_URI . '/assets/css/texture-hover.css', array(), SILK_EXP_VER );
}
add_action( 'wp_enqueue_scripts', 'silk_exp_enqueue_texture_hover' );

/**
 * Idea 4 — Custom Cursor.
 *
 * @return void
 */
function silk_exp_enqueue_custom_cursor() {
	if ( ! is_page_template( 'experimental-ui/template-custom-cursor.php' ) ) {
		return;
	}
	wp_enqueue_style( 'silk-exp-custom-cursor', SILK_EXP_URI . '/assets/css/custom-cursor.css', array(), SILK_EXP_VER );
	wp_enqueue_script( 'silk-exp-custom-cursor', SILK_EXP_URI . '/assets/js/custom-cursor.js', array(), SILK_EXP_VER, true );
}
add_action( 'wp_enqueue_scripts', 'silk_exp_enqueue_custom_cursor' );

/**
 * Idea 5 — Sticky Split-Screen Storytelling.
 *
 * @return void
 */
function silk_exp_enqueue_split_story() {
	if ( ! is_page_template( 'experimental-ui/template-split-story.php' ) ) {
		return;
	}
	wp_enqueue_style( 'silk-exp-split-story', SILK_EXP_URI . '/assets/css/split-story.css', array(), SILK_EXP_VER );
	wp_enqueue_script( 'silk-exp-split-story', SILK_EXP_URI . '/assets/js/split-story.js', array(), SILK_EXP_VER, true );
}
add_action( 'wp_enqueue_scripts', 'silk_exp_enqueue_split_story' );

/**
 * Idea 6 — Masked Typography (pure CSS, no JS file needed).
 *
 * @return void
 */
function silk_exp_enqueue_masked_type() {
	if ( ! is_page_template( 'experimental-ui/template-masked-type.php' ) ) {
		return;
	}
	wp_enqueue_style( 'silk-exp-masked-type', SILK_EXP_URI . '/assets/css/masked-type.css', array(), SILK_EXP_VER );
}
add_action( 'wp_enqueue_scripts', 'silk_exp_enqueue_masked_type' );
