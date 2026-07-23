<?php
/**
 * Override for WooCommerce product-category archives.
 *
 * Dispatches to one of three showcase designs in template-parts/category/
 * (cinematic / editorial / gallery) — see silken_category_get_style() in
 * functions.php. Preview any of them on a live category URL with
 * ?cat_style=cinematic|editorial|gallery.
 *
 * @package Woodmart_Child
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

$term = get_queried_object();

if ( $term instanceof WP_Term ) {
	$silken_style   = silken_category_get_style();
	$gallery_images = silken_category_gallery_images( $term );
	$related_terms  = silken_category_related_terms( $term );
	$child_terms    = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'parent'     => $term->term_id,
			'hide_empty' => false,
		)
	);
	if ( is_wp_error( $child_terms ) ) {
		$child_terms = array();
	}

	$part_path = get_stylesheet_directory() . '/template-parts/category/' . $silken_style . '.php';
	if ( ! file_exists( $part_path ) ) {
		$part_path = get_stylesheet_directory() . '/template-parts/category/cinematic.php';
	}

	include $part_path;
}

get_footer( 'shop' );
