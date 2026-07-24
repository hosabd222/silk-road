<?php
/**
 * Template Name: تجربه ۰۴ — نشانگر موس سفارشی (Custom Cursor)
 *
 * Isolated experiment — see experimental-ui/experimental-loader.php.
 * Native cursor is hidden site-wide only while this specific template is
 * active (via a body class added before get_header(), not a global rule),
 * and a JS-driven circle follows the pointer with requestAnimationFrame
 * smoothing, growing over gallery images.
 *
 * @package Woodmart_Child
 */

add_filter(
	'body_class',
	function ( $classes ) {
		$classes[] = 'silk-exp-cursor-page';
		return $classes;
	}
);

get_header();

$cursor_query = new WP_Query(
	array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => 8,
		'orderby'        => 'rand',
	)
);

$items = array();
if ( $cursor_query->have_posts() ) {
	while ( $cursor_query->have_posts() ) {
		$cursor_query->the_post();
		$product = wc_get_product( get_the_ID() );
		if ( ! $product ) {
			continue;
		}
		$items[] = array(
			'name'  => $product->get_name(),
			'image' => get_the_post_thumbnail_url( $product->get_id(), 'large' ),
			'url'   => get_permalink( $product->get_id() ),
		);
	}
	wp_reset_postdata();
}
?>

<div class="silk-cursor" id="silk-cursor" aria-hidden="true">
	<span class="silk-cursor__label"><?php esc_html_e( 'بزرگ‌نمایی', 'woodmart-child' ); ?></span>
</div>

<div class="silk-cursor-page">

	<header class="silk-cursor-page__intro">
		<span class="silk-cursor-page__eyebrow"><?php esc_html_e( 'تجربه تعاملی ۰۴', 'woodmart-child' ); ?></span>
		<h1><?php esc_html_e( 'نشانگر موس سفارشی', 'woodmart-child' ); ?></h1>
		<p><?php esc_html_e( 'موس را روی هر تصویر ببرید تا نشانگر بزرگ شود.', 'woodmart-child' ); ?></p>
	</header>

	<div class="silk-cursor-grid">
		<?php foreach ( $items as $item ) : ?>
			<a class="silk-cursor-item" href="<?php echo esc_url( $item['url'] ); ?>" data-cursor-zoom>
				<img src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['name'] ); ?>" loading="lazy" />
				<span class="silk-cursor-item__name"><?php echo esc_html( $item['name'] ); ?></span>
			</a>
		<?php endforeach; ?>
	</div>

</div>

<?php get_footer(); ?>
