<?php
/**
 * Template Name: تجربه ۰۳ — هاورِ لمس بافت (Texture Reveal Hover)
 *
 * Isolated experiment — see experimental-ui/experimental-loader.php.
 * Pure-CSS hover: the full product photo fades out to reveal a zoomed-in
 * crop of the same photo underneath, simulating a texture close-up
 * without needing separate macro photography per SKU.
 *
 * @package Woodmart_Child
 */

get_header();

$texture_query = new WP_Query(
	array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => 8,
		'orderby'        => 'rand',
	)
);

$crop_positions = array( '20% 30%', '70% 40%', '40% 70%', '60% 20%', '30% 60%', '80% 65%', '15% 55%', '55% 35%' );
$items          = array();
$i              = 0;

if ( $texture_query->have_posts() ) {
	while ( $texture_query->have_posts() ) {
		$texture_query->the_post();
		$product = wc_get_product( get_the_ID() );
		if ( ! $product ) {
			continue;
		}
		$items[] = array(
			'name'  => $product->get_name(),
			'price' => $product->get_price_html(),
			'image' => get_the_post_thumbnail_url( $product->get_id(), 'large' ),
			'url'   => get_permalink( $product->get_id() ),
			'crop'  => $crop_positions[ $i % count( $crop_positions ) ],
		);
		$i++;
	}
	wp_reset_postdata();
}
?>

<div class="silk-texture-page">

	<header class="silk-texture-page__intro">
		<span class="silk-texture-page__eyebrow"><?php esc_html_e( 'تجربه تعاملی ۰۳', 'woodmart-child' ); ?></span>
		<h1><?php esc_html_e( 'هاورِ لمس بافت', 'woodmart-child' ); ?></h1>
		<p><?php esc_html_e( 'موس را روی هر فرش نگه دارید تا نمای نزدیک بافتش نمایان شود — بدون هیچ جاوااسکریپتی.', 'woodmart-child' ); ?></p>
	</header>

	<div class="silk-texture-grid">
		<?php foreach ( $items as $item ) : ?>
			<a class="silk-texture-card" href="<?php echo esc_url( $item['url'] ); ?>">
				<span class="silk-texture-card__stage">
					<img class="silk-texture-card__full" src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['name'] ); ?>" loading="lazy" />
					<img
						class="silk-texture-card__zoom"
						src="<?php echo esc_url( $item['image'] ); ?>"
						alt=""
						loading="lazy"
						style="object-position: <?php echo esc_attr( $item['crop'] ); ?>;"
					/>
					<span class="silk-texture-card__tag"><?php esc_html_e( 'نمای بافت', 'woodmart-child' ); ?></span>
				</span>
				<span class="silk-texture-card__caption">
					<span class="silk-texture-card__name"><?php echo esc_html( $item['name'] ); ?></span>
					<span class="silk-texture-card__price"><?php echo wp_kses_post( $item['price'] ); ?></span>
				</span>
			</a>
		<?php endforeach; ?>
	</div>

</div>

<?php get_footer(); ?>
