<?php
/**
 * Template Name: خاطرات ابریشمی — مدرن سینمایی (Modern Cinematic)
 *
 * Full-bleed, dark scrollytelling take on the Silken Memories concept: one
 * full-viewport section per memory, video as background, big type, scroll
 * reveals. Shares its data with the other Silken Memories templates via
 * silken_memories_get_items() in functions.php.
 *
 * @package Woodmart_Child
 */

get_header();

$silken_items = silken_memories_get_items();
?>

<div class="silken-cine">

	<section class="silken-cine__hero">
		<div class="silken-cine__hero-inner">
			<span class="silken-cine__eyebrow"><?php esc_html_e( 'یک روایت تصویری', 'woodmart-child' ); ?></span>
			<h1><?php the_title(); ?></h1>
			<p><?php esc_html_e( 'برای مرور خاطرات، پایین را بگردید.', 'woodmart-child' ); ?></p>
			<span class="silken-cine__scroll-cue" aria-hidden="true"></span>
		</div>
	</section>

	<?php if ( ! empty( $silken_items ) ) : ?>

		<div class="silken-cine__progress" aria-hidden="true">
			<span class="silken-cine__progress-fill"></span>
		</div>

		<?php foreach ( $silken_items as $index => $item ) : ?>
			<section class="silken-cine__scene" data-scene-index="<?php echo esc_attr( $index ); ?>">
				<div class="silken-cine__media">
					<video
						class="silken-cine__video"
						poster="<?php echo esc_url( $item['poster'] ); ?>"
						autoplay
						muted
						loop
						playsinline
						preload="metadata"
					>
						<source src="<?php echo esc_url( $item['video'] ); ?>" type="video/mp4">
					</video>
					<span class="silken-cine__overlay" aria-hidden="true"></span>
				</div>

				<div class="silken-cine__caption">
					<span class="silken-cine__period"><?php echo esc_html( $item['period'] ); ?></span>
					<h2><?php echo esc_html( $item['title'] ); ?></h2>
					<a
						href="<?php echo esc_url( $item['video'] ); ?>"
						class="silken-cine__watch glightbox"
						data-type="video"
						data-title="<?php echo esc_attr( $item['title'] ); ?>"
					>
						<?php esc_html_e( 'تماشای کامل', 'woodmart-child' ); ?>
					</a>
				</div>
			</section>
		<?php endforeach; ?>

	<?php else : ?>

		<p style="text-align: center; color: #fff; padding: 80px 24px;">
			<?php esc_html_e( 'هنوز موردی برای نمایش ثبت نشده است.', 'woodmart-child' ); ?>
		</p>

	<?php endif; ?>

</div>

<?php get_footer(); ?>
