<?php

// Enqueue script
function offer_plugin_zhaket_enqueue_script() {

	// CSS
	wp_enqueue_style('offer-plugn-zhaket', plugin_dir_url( __FILE__ ) . '../assets/css/plugin.css');
	wp_enqueue_style( 'swipercss', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/8.4.2/swiper-bundle.min.css' );

	// JS
	wp_enqueue_script( 'offer-plugin-zhaket', plugin_dir_url( __FILE__ ) . '../assets/js/plugin.js', array('jquery'), true );
	wp_enqueue_script( 'offer-plugins-zhaket', plugin_dir_url( __FILE__ ) . '../assets/js/plugins.js', array('jquery'), true );
	wp_register_script( 'swiper', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.1/js/swiper.min.js', array('jquery'), true );
	wp_enqueue_script('swiper');

	$translations = array(
		'countdown_days' => esc_html__('روز', 'studiare'),
		'countdown_hours' => esc_html__('ساعت', 'studiare'),
		'countdown_mins' => esc_html__('دقیقه', 'studiare'),
		'countdown_sec' => esc_html__('ثانیه', 'studiare'),
	);

	wp_localize_script( 'offer-plugin-zhaket', 'sale_options', $translations );

}
add_action('wp_enqueue_scripts', 'offer_plugin_zhaket_enqueue_script');




/*  Sale Product Countdown
/* --------------------------------------------------------------------- */
if( ! function_exists( 'woodmart_sale_product_countdown' ) ) {
	function woodmart_sale_product_countdown() {
		global $product;

		if ( $product->is_on_sale() ) :
			$time_sale_end = get_post_meta( $product->get_id(), '_sale_price_dates_to', true );
			$time_sale_start = get_post_meta( $product->get_id(), '_sale_price_dates_from', true );
		endif;

		/* variable product */
		if( $product->has_child() && $product->is_on_sale()){
			$vsale_end = array();
			
			$pvariables = $product->get_children();
			foreach($pvariables as $pvariable){
				$vsale_end[] = (int)get_post_meta( $pvariable, '_sale_price_dates_to', true );
			}			
			/* get the latest time */
			$time_sale_end = max($vsale_end);				
		}

		?>

		<?php if( $product->is_on_sale() && $time_sale_end ) :?>
			<div class="deal_timer_single">
			<div class="special-offer-time-text">زمان باقی مانده تا پایان تخفیف</div>
				<div class="countdown-timer-holder-loop">
					<div class="countdown-item" data-date="<?php echo date('Y-m-d 00:00:00', $time_sale_end) ;?>"></div>
				</div>
			</div>
		<?php endif;?>

	<?php }
}

add_action( 'woocommerce_single_product_countdown', 'woodmart_sale_product_countdown', 14 );

