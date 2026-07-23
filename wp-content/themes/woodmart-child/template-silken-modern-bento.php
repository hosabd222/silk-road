<?php
/**
 * Template Name: خاطرات ابریشمی — مدرن بنتو (Modern Bento)
 *
 * Asymmetric bento-grid gallery take on the Silken Memories concept: varied
 * card footprints, hover reveal, no scroll story. Shares its data with the
 * other Silken Memories templates via silken_memories_get_items() in
 * functions.php.
 *
 * @package Woodmart_Child
 */

get_header();

$silken_items = silken_memories_get_items();

// Cycles through a handful of footprints so the grid reads as bento, not a
// uniform table. Index 0 and every 5th item after it gets the "hero" cell.
$footprints = array( 'is-hero', 'is-tall', 'is-wide', 'is-normal', 'is-normal' );
?>

<div class="silken-bento">

	<header class="silken-bento__intro">
		<h1><?php the_title(); ?></h1>
		<p><?php esc_html_e( 'آلبومی مدرن از خاطرات ابریشمی — روی هر قاب بزنید تا کامل ببینید.', 'woodmart-child' ); ?></p>
	</header>

	<?php if ( ! empty( $silken_items ) ) : ?>

		<nav class="silken-bento__timeline" aria-label="<?php esc_attr_e( 'تایم‌لاین خاطرات ابریشمی', 'woodmart-child' ); ?>">
			<?php foreach ( $silken_items as $index => $item ) : ?>
				<button type="button" class="silken-bento__tag" data-target="silken-bento-cell-<?php echo esc_attr( $index ); ?>">
					<?php echo esc_html( $item['period'] ); ?>
				</button>
			<?php endforeach; ?>
		</nav>

		<div class="silken-bento__grid">
			<?php foreach ( $silken_items as $index => $item ) : ?>
				<?php $footprint = $footprints[ $index % count( $footprints ) ]; ?>
				<div class="silken-bento__cell <?php echo esc_attr( $footprint ); ?>" id="silken-bento-cell-<?php echo esc_attr( $index ); ?>">
					<a
						href="<?php echo esc_url( $item['video'] ); ?>"
						class="silken-bento__link glightbox"
						data-type="video"
						data-title="<?php echo esc_attr( $item['title'] ); ?>"
					>
						<video
							class="silken-bento__video"
							poster="<?php echo esc_url( $item['poster'] ); ?>"
							autoplay
							muted
							loop
							playsinline
							preload="metadata"
						>
							<source src="<?php echo esc_url( $item['video'] ); ?>" type="video/mp4">
						</video>
						<span class="silken-bento__scrim" aria-hidden="true"></span>
						<span class="silken-bento__info">
							<span class="silken-bento__period"><?php echo esc_html( $item['period'] ); ?></span>
							<span class="silken-bento__title"><?php echo esc_html( $item['title'] ); ?></span>
						</span>
					</a>
				</div>
			<?php endforeach; ?>
		</div>

	<?php else : ?>

		<p style="text-align: center; padding: 80px 24px;">
			<?php esc_html_e( 'هنوز موردی برای نمایش ثبت نشده است.', 'woodmart-child' ); ?>
		</p>

	<?php endif; ?>

</div>

<?php get_footer(); ?>
