<?php
/**
 * Template Name: درباره ما — کارگاه (About — Workshop)
 *
 * Process-journey story: full-bleed image steps from raw silk to finished
 * carpet, plus a craftsmen spotlight row.
 *
 * @package Woodmart_Child
 */

get_header();

$poster = function_exists( 'wc_placeholder_img_src' ) ? wc_placeholder_img_src( 'large' ) : '';

$steps = array(
	array(
		'num'   => '۰۱',
		'title' => 'انتخاب ابریشم خام',
		'text'  => 'خالص‌ترین نخ‌های ابریشم به‌دقت انتخاب و برای رنگرزی آماده می‌شوند.',
	),
	array(
		'num'   => '۰۲',
		'title' => 'رنگرزی طبیعی',
		'text'  => 'رنگ‌های گیاهی و طبیعی، به‌آرامی به الیاف ابریشم جان می‌بخشند.',
	),
	array(
		'num'   => '۰۳',
		'title' => 'طراحی نقشه',
		'text'  => 'هر فرش با نقشه‌ای دست‌طراحی‌شده، منحصر به‌فرد و بی‌تکرار است.',
	),
	array(
		'num'   => '۰۴',
		'title' => 'بافت دستی گره‌به‌گره',
		'text'  => 'ماه‌ها صبر و ظرافت دست استادکاران، گره به گره فرش را کامل می‌کند.',
	),
);
?>

<div class="silken-about silken-about--workshop">

	<section class="silken-about-ws__hero" style="background-image:url(<?php echo esc_url( $poster ); ?>)">
		<span class="silken-about-ws__scrim" aria-hidden="true"></span>
		<div class="silken-about-ws__hero-inner">
			<h1><?php the_title(); ?></h1>
			<p><?php esc_html_e( 'از کارگاهی کوچک تا کارگاهی که هزاران گره ابریشم را با دست بافته است.', 'woodmart-child' ); ?></p>
		</div>
	</section>

	<section class="silken-about-ws__steps">
		<?php foreach ( $steps as $step ) : ?>
			<div class="silken-about-ws__step">
				<span class="silken-about-ws__step-num"><?php echo esc_html( $step['num'] ); ?></span>
				<h3><?php echo esc_html( $step['title'] ); ?></h3>
				<p><?php echo esc_html( $step['text'] ); ?></p>
			</div>
		<?php endforeach; ?>
	</section>

	<?php if ( have_posts() ) : ?>
		<section class="silken-about-ws__content">
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
