<?php
/**
 * Template Name: خاطرات ابریشمی (Silken Memories)
 *
 * Immersive photo-album / timeline template: a horizontal timeline drives a
 * Swiper coverflow album of "Live Photo"-style videos pinned like old prints.
 *
 * Video/poster data lives in silken_memories_get_items() in functions.php —
 * swap the placeholder 'video' URLs for ArvanCloud object-storage URLs there
 * once that phase lands; nothing in this file needs to change.
 *
 * @package Woodmart_Child
 */

get_header();

$silken_items      = silken_memories_get_items();
$rotation_classes  = array( 'is-rot-1', 'is-rot-2', 'is-rot-3', 'is-rot-4' );
?>

<div class="silken-memories-page">

	<div class="silken-memories-page__intro">
		<h1><?php the_title(); ?></h1>
		<p><?php esc_html_e( 'روایتی تصویری از اصالت و ظرافت فرش‌های دستباف ابریشم، دوره‌به‌دوره — روی هر نقطه از تایم‌لاین بزنید یا آلبوم را ورق بزنید.', 'woodmart-child' ); ?></p>
	</div>

	<?php if ( ! empty( $silken_items ) ) : ?>

		<nav class="silken-timeline" aria-label="<?php esc_attr_e( 'تایم‌لاین خاطرات ابریشمی', 'woodmart-child' ); ?>">
			<div class="silken-timeline__track">
				<?php foreach ( $silken_items as $index => $item ) : ?>
					<button
						type="button"
						class="silken-timeline__point<?php echo 0 === $index ? ' is-active' : ''; ?>"
						data-slide-index="<?php echo esc_attr( $index ); ?>"
					>
						<span class="silken-timeline__year"><?php echo esc_html( $item['period'] ); ?></span>
					</button>
				<?php endforeach; ?>
			</div>
		</nav>

		<div class="silken-album">
			<div class="swiper silken-swiper">
				<div class="swiper-wrapper">
					<?php foreach ( $silken_items as $index => $item ) : ?>
						<div class="swiper-slide">
							<div class="silken-frame <?php echo esc_attr( $rotation_classes[ $index % count( $rotation_classes ) ] ); ?>">
								<span class="silken-frame__corner silken-frame__corner--tl"></span>
								<span class="silken-frame__corner silken-frame__corner--tr"></span>
								<span class="silken-frame__corner silken-frame__corner--bl"></span>
								<span class="silken-frame__corner silken-frame__corner--br"></span>

								<a
									href="<?php echo esc_url( $item['video'] ); ?>"
									class="silken-frame__lightbox glightbox"
									data-type="video"
									data-title="<?php echo esc_attr( $item['title'] ); ?>"
								>
									<video
										class="silken-frame__video"
										poster="<?php echo esc_url( $item['poster'] ); ?>"
										autoplay
										muted
										loop
										playsinline
										preload="metadata"
									>
										<source src="<?php echo esc_url( $item['video'] ); ?>" type="video/mp4">
									</video>
									<span class="silken-frame__play" aria-hidden="true"></span>
								</a>

								<div class="silken-frame__caption">
									<span class="silken-frame__title"><?php echo esc_html( $item['title'] ); ?></span>
									<span class="silken-frame__period"><?php echo esc_html( $item['period'] ); ?></span>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

	<?php else : ?>

		<p style="text-align: center;">
			<?php esc_html_e( 'هنوز موردی برای نمایش در آلبوم ثبت نشده است.', 'woodmart-child' ); ?>
		</p>

	<?php endif; ?>

</div>

<?php get_footer(); ?>
