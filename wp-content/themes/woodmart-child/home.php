<?php
/**
 * Blog posts index (the page set as "Posts page" in Reading settings).
 *
 * Dispatches to one of three designs in template-parts/blog/
 * (magazine / editorial / cards) — see silken_blog_get_style() in
 * functions.php. Preview any of them with ?blog_style=magazine|editorial|cards.
 *
 * @package Woodmart_Child
 */

get_header();

$silken_blog_style = silken_blog_get_style();
$part_path          = get_stylesheet_directory() . '/template-parts/blog/' . $silken_blog_style . '.php';
if ( ! file_exists( $part_path ) ) {
	$part_path = get_stylesheet_directory() . '/template-parts/blog/magazine.php';
}

include $part_path;

get_footer();
