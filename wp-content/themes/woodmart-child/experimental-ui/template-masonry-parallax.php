<?php
/**
 * Template Name: تجربه ۰۲ — گرید نامتقارن با پارالکس (Masonry & Parallax)
 *
 * Isolated experiment — see experimental-ui/experimental-loader.php.
 * Real products laid out in an asymmetric multi-column grid; vanilla JS
 * moves alternating columns at different speeds on scroll.
 *
 * @package Woodmart_Child
 */

get_header();

$columns_count = 3;
$masonry_query = new WP_Query(
	array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => 15,
		'orderby'        => 'rand',
	)
);

$columns = array_fill( 0, $columns_count, array() );
$i       = 0;

if ( $masonry_query->have_posts() ) {
	while ( $masonry_query->have_posts() ) {
		$masonry_query->the_post();
		$product = wc_get_product( get_the_ID() );
		if ( ! $product ) {
			continue;
		}
		$columns[ $i % $columns_count ][] = array(
			'name'    => $product->get_name(),
			'price'   => $product->get_price_html(),
			'image'   => get_the_post_thumbnail_url( $product->get_id(), 'large' ),
			'url'     => get_permalink( $product->get_id() ),
			'is_tall' => ( 0 === $i % 2 ),
		);
		$i++;
	}
	wp_reset_postdata();
}
?>

<div class="silk-masonry-page">

	<header class="silk-masonry-page__intro">
		<span class="silk-masonry-page__eyebrow"><?php esc_html_e( 'تجربه تعاملی ۰۲', 'woodmart-child' ); ?></span>
		<h1><?php esc_html_e( 'گرید نامتقارن و اسکرول پارالکس', 'woodmart-child' ); ?></h1>
		<p><?php esc_html_e( 'هنگام اسکرول، ستون‌ها با سرعت‌های متفاوت حرکت می‌کنند.', 'woodmart-child' ); ?></p>
	</header>

	<div class="silk-masonry">
		<?php foreach ( $columns as $col_index => $items ) : ?>
			<div class="silk-masonry__col" data-col="<?php echo esc_attr( $col_index ); ?>">
				<?php foreach ( $items as $item ) : ?>
					<a class="silk-masonry__card <?php echo $item['is_tall'] ? 'is-tall' : 'is-short'; ?>" href="<?php echo esc_url( $item['url'] ); ?>">
						<img src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['name'] ); ?>" loading="lazy" />
						<span class="silk-masonry__card-overlay">
							<span class="silk-masonry__card-name"><?php echo esc_html( $item['name'] ); ?></span>
							<span class="silk-masonry__card-price"><?php echo wp_kses_post( $item['price'] ); ?></span>
						</span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>
	</div>

</div>

<?php get_footer(); ?>
