<?php
/**
 * Template Name: تجربه ۰۱ — دکوراسیون تعاملی (Shoppable Hotspots)
 *
 * Isolated experiment — see experimental-ui/experimental-loader.php.
 * A wide interior shot with pulsing hotspot pins; hovering/tapping a pin
 * opens a glassmorphism tooltip with a real product from the catalog.
 *
 * @package Woodmart_Child
 */

get_header();

$hotspot_query = new WP_Query(
	array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => 4,
		'orderby'        => 'rand',
	)
);

$hotspot_positions = array(
	array( 'x' => 27, 'y' => 80 ),
	array( 'x' => 44, 'y' => 63 ),
	array( 'x' => 63, 'y' => 48 ),
	array( 'x' => 82, 'y' => 66 ),
);

$hotspots = array();
$i        = 0;
if ( $hotspot_query->have_posts() ) {
	while ( $hotspot_query->have_posts() ) {
		$hotspot_query->the_post();
		$product = wc_get_product( get_the_ID() );
		if ( ! $product || ! isset( $hotspot_positions[ $i ] ) ) {
			continue;
		}
		$hotspots[] = array(
			'x'     => $hotspot_positions[ $i ]['x'],
			'y'     => $hotspot_positions[ $i ]['y'],
			'name'  => $product->get_name(),
			'price' => $product->get_price_html(),
			'image' => get_the_post_thumbnail_url( $product->get_id(), 'medium' ),
			'url'   => get_permalink( $product->get_id() ),
		);
		$i++;
	}
	wp_reset_postdata();
}
?>

<div class="silk-hotspots">

	<header class="silk-hotspots__intro">
		<span class="silk-hotspots__eyebrow"><?php esc_html_e( 'تجربه تعاملی ۰۱', 'woodmart-child' ); ?></span>
		<h1><?php esc_html_e( 'دکوراسیون تعاملی', 'woodmart-child' ); ?></h1>
		<p><?php esc_html_e( 'روی نقاط طلایی حرکت کنید یا لمس‌شان کنید تا فرش‌های این فضا را ببینید.', 'woodmart-child' ); ?></p>
	</header>

	<section class="silk-hotspots__stage">
		<img
			class="silk-hotspots__bg"
			src="<?php echo esc_url( SILK_EXP_URI . '/assets/img/luxury-interior.jpg' ); ?>"
			alt="<?php esc_attr_e( 'دکوراسیون داخلی لوکس', 'woodmart-child' ); ?>"
		/>
		<div class="silk-hotspots__vignette"></div>

		<?php foreach ( $hotspots as $index => $spot ) : ?>
			<button
				type="button"
				class="silk-hotspot"
				style="--x: <?php echo esc_attr( $spot['x'] ); ?>%; --y: <?php echo esc_attr( $spot['y'] ); ?>%; --delay: <?php echo esc_attr( $index * 120 ); ?>ms;"
				aria-label="<?php echo esc_attr( sprintf( /* translators: %s: product name */ __( 'مشاهده %s', 'woodmart-child' ), $spot['name'] ) ); ?>"
			>
				<span class="silk-hotspot__pulse" aria-hidden="true"></span>
				<span class="silk-hotspot__dot" aria-hidden="true"></span>

				<span class="silk-hotspot__tooltip">
					<img class="silk-hotspot__tooltip-img" src="<?php echo esc_url( $spot['image'] ); ?>" alt="" loading="lazy" />
					<span class="silk-hotspot__tooltip-body">
						<strong class="silk-hotspot__tooltip-name"><?php echo esc_html( $spot['name'] ); ?></strong>
						<span class="silk-hotspot__tooltip-price"><?php echo wp_kses_post( $spot['price'] ); ?></span>
						<a class="silk-hotspot__tooltip-cta" href="<?php echo esc_url( $spot['url'] ); ?>">
							<?php esc_html_e( 'مشاهده جزئیات', 'woodmart-child' ); ?>
						</a>
					</span>
				</span>
			</button>
		<?php endforeach; ?>
	</section>

</div>

<?php get_footer(); ?>
