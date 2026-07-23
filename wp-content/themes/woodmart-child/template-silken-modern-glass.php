<?php
/**
 * Template Name: خاطرات ابریشمی — مدرن شیشه‌ای (Modern Glass)
 *
 * Light, minimal glassmorphism take on the Silken Memories concept: soft
 * blurred color blobs, frosted-glass cards, a sticky pill timeline. Shares
 * its data with the other Silken Memories templates via
 * silken_memories_get_items() in functions.php.
 *
 * @package Woodmart_Child
 */

get_header();

$silken_items = silken_memories_get_items();
?>

<div class="silken-glass">

	<span class="silken-glass__blob silken-glass__blob--a" aria-hidden="true"></span>
	<span class="silken-glass__blob silken-glass__blob--b" aria-hidden="true"></span>
	<span class="silken-glass__blob silken-glass__blob--c" aria-hidden="true"></span>

	<header class="silken-glass__intro">
		<h1><?php the_title(); ?></h1>
		<p><?php esc_html_e( 'روایتی سبک، شفاف و مدرن از میراث فرش دستباف ابریشم.', 'woodmart-child' ); ?></p>
	</header>

	<?php if ( ! empty( $silken_items ) ) : ?>

		<nav class="silken-glass__timeline" aria-label="<?php esc_attr_e( 'تایم‌لاین خاطرات ابریشمی', 'woodmart-child' ); ?>">
			<?php foreach ( $silken_items as $index => $item ) : ?>
				<a href="#silken-glass-card-<?php echo esc_attr( $index ); ?>" class="silken-glass__pill<?php echo 0 === $index ? ' is-active' : ''; ?>" data-card-index="<?php echo esc_attr( $index ); ?>">
					<?php echo esc_html( $item['period'] ); ?>
				</a>
			<?php endforeach; ?>
		</nav>

		<div class="silken-glass__stack">
			<?php foreach ( $silken_items as $index => $item ) : ?>
				<article class="silken-glass__card" id="silken-glass-card-<?php echo esc_attr( $index ); ?>" data-card-index="<?php echo esc_attr( $index ); ?>">
					<div class="silken-glass__media">
						<a
							href="<?php echo esc_url( $item['video'] ); ?>"
							class="glightbox"
							data-type="video"
							data-title="<?php echo esc_attr( $item['title'] ); ?>"
						>
							<video
								poster="<?php echo esc_url( $item['poster'] ); ?>"
								autoplay
								muted
								loop
								playsinline
								preload="metadata"
							>
								<source src="<?php echo esc_url( $item['video'] ); ?>" type="video/mp4">
							</video>
						</a>
					</div>
					<div class="silken-glass__body">
						<span class="silken-glass__period"><?php echo esc_html( $item['period'] ); ?></span>
						<h2><?php echo esc_html( $item['title'] ); ?></h2>
					</div>
				</article>
			<?php endforeach; ?>
		</div>

	<?php else : ?>

		<p style="text-align: center; padding: 80px 24px;">
			<?php esc_html_e( 'هنوز موردی برای نمایش ثبت نشده است.', 'woodmart-child' ); ?>
		</p>

	<?php endif; ?>

</div>

<?php get_footer(); ?>
