<?php
/**
 * Template Name: تجربه ۰۵ — اسکرول داستان‌سرا (Sticky Split-Screen)
 *
 * Isolated experiment — see experimental-ui/experimental-loader.php.
 * Right half stays sticky (full viewport height) while the left half
 * scrolls through narrative sections; an IntersectionObserver crossfades
 * the sticky image as each section becomes active.
 *
 * Placeholder editorial copy below (the weavers' story) — swap for real
 * brand copy whenever it's ready.
 *
 * @package Woodmart_Child
 */

get_header();

$sections = array(
	array(
		'num'   => '۰۱',
		'title' => 'ریشه‌ها',
		'text'  => 'هر فرش دستباف با یک طرح ذهنی آغاز می‌شود؛ نقشی که نسل به نسل از استاد به شاگرد منتقل شده و پیش از آنکه روی دار بسته شود، در ذهن بافنده شکل گرفته است.',
		'image' => 'carpet-detail.jpg',
	),
	array(
		'num'   => '۰۲',
		'title' => 'دار قالی',
		'text'  => 'ساعت‌ها پای دار نشستن، گره زدن تار به تار، و صبر کردن تا یک متر مربع فرش شکل بگیرد — کاری که ماشین هرگز نمی‌تواند تقلید کند.',
		'image' => 'weaving-loom.jpg',
	),
	array(
		'num'   => '۰۳',
		'title' => 'ابریشم خالص',
		'text'  => 'انتخاب نخ، رنگ‌آمیزی و کیفیت ابریشم، پیش از هر گرهی تعیین می‌شود؛ همین انتخاب‌هاست که درخشش و ماندگاری فرش را برای دهه‌ها تضمین می‌کند.',
		'image' => 'silk-thread.jpg',
	),
	array(
		'num'   => '۰۴',
		'title' => 'خانه‌ای برای فرش شما',
		'text'  => 'فرشی که از دار قالی بیرون می‌آید، سفری طولانی را برای رسیدن به خانه‌ی شما طی کرده — سفری که با یک انتخاب ساده از سوی شما کامل می‌شود.',
		'image' => 'luxury-interior.jpg',
	),
);
?>

<div class="silk-story">

	<div class="silk-story__media">
		<img class="silk-story__img is-active" id="silk-story-img-a" src="<?php echo esc_url( SILK_EXP_URI . '/assets/img/' . $sections[0]['image'] ); ?>" alt="" />
		<img class="silk-story__img" id="silk-story-img-b" src="" alt="" />
		<div class="silk-story__media-overlay"></div>
	</div>

	<div class="silk-story__content">

		<header class="silk-story__intro">
			<span class="silk-story__eyebrow"><?php esc_html_e( 'تجربه تعاملی ۰۵', 'woodmart-child' ); ?></span>
			<h1><?php esc_html_e( 'داستان بافندگان', 'woodmart-child' ); ?></h1>
		</header>

		<?php foreach ( $sections as $index => $section ) : ?>
			<section
				class="silk-story__section"
				data-image="<?php echo esc_url( SILK_EXP_URI . '/assets/img/' . $section['image'] ); ?>"
			>
				<span class="silk-story__num"><?php echo esc_html( $section['num'] ); ?></span>
				<h2><?php echo esc_html( $section['title'] ); ?></h2>
				<p><?php echo esc_html( $section['text'] ); ?></p>
			</section>
		<?php endforeach; ?>

	</div>

</div>

<?php get_footer(); ?>
