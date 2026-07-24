<?php
/**
 * Template Name: تجربه ۰۶ — تایپوگرافی غول‌پیکر (Masked Typography)
 *
 * Isolated experiment — see experimental-ui/experimental-loader.php.
 * Pure CSS: a giant bold headline masks a detailed carpet pattern photo
 * via background-clip: text, with a slow ambient background-position pan.
 *
 * @package Woodmart_Child
 */

get_header();
?>

<div class="silk-masked-page">

	<section class="silk-masked">
		<span class="silk-masked__eyebrow"><?php esc_html_e( 'تجربه تعاملی ۰۶', 'woodmart-child' ); ?></span>
		<h1 class="silk-masked__text"><?php esc_html_e( 'هنر دست', 'woodmart-child' ); ?></h1>
		<p class="silk-masked__caption"><?php esc_html_e( 'هر نقش، حاصل هزاران گره‌ای‌ست که با دست، نه ماشین، شکل گرفته‌اند.', 'woodmart-child' ); ?></p>
	</section>

</div>

<?php get_footer(); ?>
