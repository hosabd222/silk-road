<?php
/**
 * Template Name: درباره ما — ارزش‌ها (About — Values)
 *
 * Mission-first story: statement hero, an icon grid of core values, and a
 * pull-quote.
 *
 * @package Woodmart_Child
 */

get_header();

$values = array(
	array(
		'icon'  => '✦',
		'title' => 'اصالت',
		'text'  => 'هر فرش با ابریشم خالص و بدون واسطه بافته می‌شود.',
	),
	array(
		'icon'  => '✋',
		'title' => 'صد در صد دستباف',
		'text'  => 'بدون دخالت ماشین؛ فقط دست‌های استادکار.',
	),
	array(
		'icon'  => '📜',
		'title' => 'شناسنامه‌دار',
		'text'  => 'هر تکه با گواهی اصالت و شناسنامه ارسال می‌شود.',
	),
	array(
		'icon'  => '🌱',
		'title' => 'رنگ طبیعی',
		'text'  => 'رنگرزی با مواد گیاهی، سازگار با محیط‌زیست.',
	),
);
?>

<div class="silken-about silken-about--values">

	<section class="silken-about-val__hero">
		<span class="silken-about-val__eyebrow"><?php esc_html_e( 'درباره ما', 'woodmart-child' ); ?></span>
		<h1><?php the_title(); ?></h1>
		<p><?php esc_html_e( 'ما به بافتنِ چیزی بیش از فرش باور داریم؛ ما اصالت، صبر و هنر ایرانی را می‌بافیم.', 'woodmart-child' ); ?></p>
	</section>

	<section class="silken-about-val__grid">
		<?php foreach ( $values as $value ) : ?>
			<div class="silken-about-val__card">
				<span class="silken-about-val__icon"><?php echo esc_html( $value['icon'] ); ?></span>
				<h3><?php echo esc_html( $value['title'] ); ?></h3>
				<p><?php echo esc_html( $value['text'] ); ?></p>
			</div>
		<?php endforeach; ?>
	</section>

	<section class="silken-about-val__quote">
		<blockquote>
			<?php esc_html_e( '«فرش دستباف، تنها یک کالا نیست؛ خاطره‌ای است که نسل به نسل بافته می‌شود.»', 'woodmart-child' ); ?>
		</blockquote>
	</section>

	<?php if ( have_posts() ) : ?>
		<section class="silken-about-val__content">
			<?php
			while ( have_posts() ) :
				the_post();
				the_content();
			endwhile;
			?>
		</section>
	<?php endif; ?>

</div>

<?php get_footer(); ?>
