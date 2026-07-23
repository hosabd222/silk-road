<?php
/**
 * Template Name: درباره ما — میراث (About — Heritage)
 *
 * Timeline-style brand story: hero statement, a history timeline, stat
 * counters, and a values row.
 *
 * @package Woodmart_Child
 */

get_header();

$stats = array(
	array( 'value' => '۴۰+', 'label' => 'سال تجربه بافندگی' ),
	array( 'value' => '۱۲۰۰+', 'label' => 'فرش دستباف تحویل‌شده' ),
	array( 'value' => '۳۵', 'label' => 'استاد نساج همکار' ),
	array( 'value' => '۱۰۰٪', 'label' => 'ابریشم خالص و اصیل' ),
);

$milestones = array(
	array( 'year' => '۱۳۵۰', 'text' => 'شروع کار کارگاه خانوادگی با اولین دار قالی.' ),
	array( 'year' => '۱۳۷۰', 'text' => 'گسترش تولید و آموزش نسل دوم بافندگان.' ),
	array( 'year' => '۱۳۹۵', 'text' => 'ثبت آثار به‌عنوان میراث فرهنگی ناملموس.' ),
	array( 'year' => '۱۴۰۴', 'text' => 'حضور آنلاین و عرضه‌ی جهانی فرش‌های دستباف.' ),
);
?>

<div class="silken-about silken-about--heritage">

	<section class="silken-about__hero">
		<div class="silken-about__hero-inner">
			<span class="silken-about__eyebrow"><?php esc_html_e( 'درباره ما', 'woodmart-child' ); ?></span>
			<h1><?php the_title(); ?></h1>
			<p><?php esc_html_e( 'چهار دهه بافتن ابریشم با دست، صبر و عشق — داستانی که هر تار و پودش روایتی از اصالت ایرانی دارد.', 'woodmart-child' ); ?></p>
		</div>
	</section>

	<section class="silken-about__stats">
		<?php foreach ( $stats as $stat ) : ?>
			<div class="silken-about__stat">
				<span class="silken-about__stat-value"><?php echo esc_html( $stat['value'] ); ?></span>
				<span class="silken-about__stat-label"><?php echo esc_html( $stat['label'] ); ?></span>
			</div>
		<?php endforeach; ?>
	</section>

	<section class="silken-about__timeline">
		<h2><?php esc_html_e( 'مسیر ما', 'woodmart-child' ); ?></h2>
		<div class="silken-about__timeline-track">
			<?php foreach ( $milestones as $m ) : ?>
				<div class="silken-about__milestone">
					<span class="silken-about__milestone-year"><?php echo esc_html( $m['year'] ); ?></span>
					<p><?php echo esc_html( $m['text'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</section>

	<?php if ( have_posts() ) : ?>
		<section class="silken-about__content">
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
