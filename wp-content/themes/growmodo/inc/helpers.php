<?php


defined( 'ABSPATH' ) || exit;


function gm_option_defaults() {
	return array(
		'banner_text'      => '✨Discover Your Dream Property with Estatein',
		'banner_link_text' => 'Learn More',
		'banner_link'      => '/about-us/',
		'hero_title'       => 'Discover Your Dream Property with Estatein',
		'hero_text'        => 'Your journey to finding the perfect property begins here. Explore our listings to find the home that matches your dreams.',
		'stat1_value'      => '200+',
		'stat1_label'      => 'Happy Customers',
		'stat2_value'      => '10k+',
		'stat2_label'      => 'Properties For Clients',
		'stat3_value'      => '16+',
		'stat3_label'      => 'Years of Experience',
		'cta_title'        => 'Start Your Real Estate Journey Today',
		'cta_text'         => "Your dream property is just a click away. Whether you're looking for a new home, a strategic investment, or expert real estate advice, Estatein is here to assist you every step of the way. Take the first step towards your real estate goals and explore our available properties or get in touch with our team for personalized assistance.",
		'cta_button'       => 'Explore Properties',
		'email'            => 'info@estatein.com',
		'phone'            => '+1 (123) 456-7890',
		'address'          => 'Main Headquarters',
		'notify_email'     => '',
		'facebook'         => 'https://facebook.com/',
		'linkedin'         => 'https://linkedin.com/',
		'twitter'          => 'https://twitter.com/',
		'youtube'          => 'https://youtube.com/',
		'instagram'        => 'https://instagram.com/',
		'copyright'        => '@2023 Estatein. All Rights Reserved.',
	);
}


function gm_opt( $key ) {
	static $opts = null;
	if ( null === $opts ) {
		$opts = wp_parse_args( array_filter( (array) get_option( 'gm_options', array() ), 'strlen' ), gm_option_defaults() );
	}
	return isset( $opts[ $key ] ) ? $opts[ $key ] : '';
}


function gm_url( $path ) {
	return preg_match( '#^(https?:)?//#', $path ) ? $path : home_url( $path );
}


function gm_meta( $key, $post_id = 0 ) {
	return trim( (string) get_post_meta( $post_id ? $post_id : get_the_ID(), '_gm_' . $key, true ) );
}


function gm_lines( $text ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $text ) ), 'strlen' ) );
}


function gm_money( $n ) {
	return '$' . number_format( (float) $n );
}


function gm_img( $file, $alt = '', $w = 0, $h = 0, $class = '', $eager = false ) {
	$path = GM_DIR . '/assets/images/' . $file;
	if ( ! $w && file_exists( $path ) ) {
		$size = getimagesize( $path );
		$w    = $size[0];
		$h    = $size[1];
	}
	return sprintf(
		'<img src="%s" alt="%s" width="%d" height="%d"%s loading="%s" decoding="async"%s>',
		esc_url( GM_URI . '/assets/images/' . $file ),
		esc_attr( $alt ),
		$w,
		$h,
		$class ? ' class="' . esc_attr( $class ) . '"' : '',
		$eager ? 'eager' : 'lazy',
		$eager ? ' fetchpriority="high"' : ''
	);
}


function gm_icon( $name, $size = 24 ) {
	static $icons = null;
	if ( null === $icons ) {
		$s = 'fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"';
		$icons = array(
			'arrow-ur'    => "<path d='M6 18 18 6M8 6h10v10' $s stroke-width='1.5'/>",
			'arrow-left'  => "<path d='M19 12H5m6-6-6 6 6 6' $s stroke-width='1.8'/>",
			'arrow-right' => "<path d='M5 12h14m-6-6 6 6-6 6' $s stroke-width='1.8'/>",
			'close'       => "<path d='m6 6 12 12M18 6 6 18' $s stroke-width='1.8'/>",
			'chevron'     => "<path d='m6 9 6 6 6-6' $s stroke-width='1.8'/>",
			'menu'        => "<path d='M4 7h16M4 12h16M4 17h16' $s stroke-width='1.8'/>",
			'search'      => "<circle cx='11' cy='11' r='6.5' $s stroke-width='1.8'/><path d='m16 16 4 4' $s stroke-width='1.8'/>",
			'pin'         => "<path d='M12 21s-6.5-5.6-6.5-11a6.5 6.5 0 0 1 13 0c0 5.4-6.5 11-6.5 11Z' fill='currentColor'/><circle cx='12' cy='10' r='2.3' fill='#1a1a1a'/>",
			'calendar'    => "<rect x='4' y='5.5' width='16' height='14.5' rx='2' $s stroke-width='1.8'/><path d='M4 10h16M8.5 3.5v4M15.5 3.5v4' $s stroke-width='1.8'/>",
			'bed'         => "<path d='M3 18V8m0 6h18v4M21 14v-2.5A2.5 2.5 0 0 0 18.5 9H11v5' $s stroke-width='1.7'/><circle cx='7.5' cy='10.5' r='1.5' $s stroke-width='1.7'/>",
			'bath'        => "<path d='M4 12h16v2.5a4.5 4.5 0 0 1-4.5 4.5h-7A4.5 4.5 0 0 1 4 14.5V12Zm2 0V6.5A2.5 2.5 0 0 1 8.5 4 2.5 2.5 0 0 1 11 6.5M7 19l-1 2m11-2 1 2' $s stroke-width='1.7'/>",
			'building'    => "<path d='M5 21V4.5A1.5 1.5 0 0 1 6.5 3h7A1.5 1.5 0 0 1 15 4.5V21M15 9h3.5a1.5 1.5 0 0 1 1.5 1.5V21M3 21h18M8.5 7h3M8.5 11h3M8.5 15h3' $s stroke-width='1.7'/>",
			'area'        => "<rect x='4' y='4' width='16' height='16' rx='2' $s stroke-width='1.7'/><path d='m4 14 10-10M4 20 20 4m-8 16 8-8' $s stroke-width='1.5'/>",
			'store'       => "<path d='M4 9.5 5.5 4h13L20 9.5a2.7 2.7 0 0 1-5.3 0 2.7 2.7 0 0 1-5.4 0A2.7 2.7 0 0 1 4 9.5ZM5.500 12.500V20h13v-7.500' fill='currentColor' stroke='currentColor' stroke-width='1.5' stroke-linejoin='round'/><path d='M10 20v-4.500h4V20' fill='#1a1a1a'/>",
			'money'       => "<rect x='3' y='6' width='18' height='12' rx='2' fill='currentColor'/><circle cx='12' cy='12' r='2.6' fill='#1a1a1a'/>",
			'office'      => "<path d='M4 21V5l9-2v18M13 8l7 2v11M2 21h20' fill='currentColor' stroke='currentColor' stroke-width='1.6' stroke-linejoin='round'/><path d='M8 8h1m-1 4h1m-1 4h1m7-5h1m-1 4h1m-1 4h1' stroke='#1a1a1a' stroke-width='1.6' stroke-linecap='round'/>",
			'sun'         => "<circle cx='12' cy='12' r='3.500' fill='currentColor'/><path d='M12 3v2.500M12 18.500V21M3 12h2.500M18.500 12H21M5.600 5.600l1.800 1.800m9.200 9.200 1.800 1.800m0-12.800-1.800 1.800M7.400 16.600l-1.800 1.800' $s stroke-width='1.6'/>",
			'cube'        => "<path d='m12 3 8 4.500v9L12 21l-8-4.500v-9L12 3Z' fill='currentColor' fill-opacity='.85' stroke='currentColor' stroke-width='1.5' stroke-linejoin='round'/><path d='m12 12 8-4.500M12 12 4 7.500M12 12v9' stroke='#1a1a1a' stroke-width='1.2'/>",
			'mail'        => "<rect x='3' y='5' width='18' height='14' rx='2.500' fill='currentColor'/><path d='m4 8 8 5.500L20 8' $s stroke='#1a1a1a' stroke-width='1.6'/>",
			'phone'       => "<path d='M6.600 3.500 9 3l1.600 4.200-2 1.400a11 11 0 0 0 5.800 5.800l1.400-2L20 13.900l-.5 2.400a2.500 2.500 0 0 1-2.500 2A13.500 13.500 0 0 1 4.700 6a2.500 2.500 0 0 1 1.900-2.500Z' fill='currentColor'/>",
			'logo-mark'   => "<path d='M12 3a8 8 0 0 0-8 8v6a4 4 0 0 0 4 4h4V3Z' fill='currentColor'/><path d='M12 8h4a4 4 0 0 1 4 4v5a4 4 0 0 1-4 4h-4V8Z' fill='currentColor' fill-opacity='.7'/>",
			'star'        => "<path d='m12 3 2.600 5.600 6.100.7-4.500 4.200 1.200 6L12 16.400 6.600 19.500l1.200-6-4.500-4.200 6.100-.7L12 3Z' fill='currentColor'/>",
			'bolt'        => "<path d='M13.500 2 5 13.500h6L10 22l9-12h-6l.5-8Z' fill='currentColor'/>",
			'send'        => "<path d='M21 3 3 10.500l6.500 2.500L12 21l9-18Z' fill='currentColor'/>",
			'facebook'    => "<path d='M14 8.500V7a1 1 0 0 1 1-1h2V3h-3a4 4 0 0 0-4 4v1.500H7V12h3v9h4v-9h3l.6-3.500H14Z' fill='currentColor'/>",
			'linkedin'    => "<path d='M4 4h16v16H4V4Zm3 5.500V17h2.400V9.500H7Zm1.200-3.200a1.400 1.400 0 1 0 0 2.800 1.400 1.400 0 0 0 0-2.800ZM11 9.500V17h2.400v-4c0-1 .5-1.700 1.400-1.700s1.300.7 1.300 1.700v4h2.400v-4.500c0-2-1.100-3.200-2.800-3.200-1 0-1.700.5-2.200 1.200v-1H11Z' fill='currentColor' fill-rule='evenodd'/>",
			'twitter'     => "<path d='M21 6.500a7 7 0 0 1-2 .6 3.400 3.400 0 0 0 1.500-1.900c-.7.400-1.500.7-2.200.9a3.500 3.500 0 0 0-6 3.200A10 10 0 0 1 5 5.600a3.500 3.500 0 0 0 1.100 4.700c-.6 0-1.100-.2-1.600-.4 0 1.700 1.200 3.100 2.800 3.500-.5.100-1 .2-1.600.1.500 1.400 1.800 2.400 3.300 2.400A7 7 0 0 1 4 17.300 10 10 0 0 0 9.400 19c6.500 0 10-5.500 9.800-10.400.7-.5 1.300-1.100 1.800-2.100Z' fill='currentColor'/>",
			'youtube'     => "<path d='M21.500 7.700a2.500 2.500 0 0 0-1.800-1.800C18.100 5.500 12 5.500 12 5.500s-6.100 0-7.700.4A2.500 2.500 0 0 0 2.500 7.700C2 9.300 2 12 2 12s0 2.700.5 4.300a2.500 2.500 0 0 0 1.800 1.800c1.600.4 7.700.4 7.700.4s6.100 0 7.700-.4a2.500 2.500 0 0 0 1.800-1.800c.5-1.600.5-4.300.5-4.300s0-2.700-.5-4.300ZM10 15V9l5.200 3L10 15Z' fill='currentColor' fill-rule='evenodd'/>",
			'instagram'   => "<rect x='4' y='4' width='16' height='16' rx='4.500' $s stroke-width='1.8'/><circle cx='12' cy='12' r='3.600' $s stroke-width='1.8'/><circle cx='16.800' cy='7.200' r='1' fill='currentColor'/>",
			'chart'       => "<path d='M5 20v-7h3.500v7H5Zm5.500 0V5H14v15h-3.500Zm5.500 0V10h3.500v10H16Z' fill='currentColor'/>",
			'pie'         => "<path d='M11 3.200V13h9.800A9 9 0 0 0 11 3.200Z' fill='currentColor'/><path d='M9.500 5A8.500 8.500 0 1 0 19 14.500H9.500V5Z' fill='currentColor' fill-opacity='.8'/>",
			'database'    => "<ellipse cx='12' cy='6' rx='7' ry='3' fill='currentColor'/><path d='M5 9.500c0 1.700 3.100 3 7 3s7-1.300 7-3V13c0 1.700-3.100 3-7 3s-7-1.300-7-3V9.500Zm0 6.500c0 1.700 3.100 3 7 3s7-1.300 7-3v3c0 1.700-3.100 3-7 3s-7-1.300-7-3v-3Z' fill='currentColor' fill-opacity='.85'/>",
			'megaphone'   => "<path d='M3 10v4a1 1 0 0 0 1 1h2.500l1 4.500H10L9 15h1l8 4V6l-8 4H4a1 1 0 0 0-1 1Zm16-.500v5a2.500 2.500 0 0 0 0-5Z' fill='currentColor'/>",
			'apps'        => "<circle cx='7.500' cy='7.500' r='3.500' fill='currentColor'/><rect x='13' y='4' width='7' height='7' rx='2' fill='currentColor'/><rect x='4' y='13' width='7' height='7' rx='2' fill='currentColor'/><path d='M16.500 13v7M13 16.500h7' $s stroke-width='2'/>",
			'tag'         => "<path d='M4 4h8l8 8-8 8-8-8V4Zm5 3.500a1.500 1.500 0 1 0 0 3 1.500 1.500 0 0 0 0-3Z' fill='currentColor' fill-rule='evenodd'/>",
			'sparkles'    => "<path d='m10 4 1.800 5.200L17 11l-5.200 1.800L10 18l-1.800-5.200L3 11l5.200-1.800L10 4Zm8 8 .9 2.600 2.600.9-2.600.9L18 19l-.9-2.600-2.600-.9 2.600-.9.9-2.600Z' fill='currentColor'/>",
			'cap'         => "<path d='m12 4 10 5-10 5L2 9l10-5Zm-6 8.500V16c0 1.500 2.700 3 6 3s6-1.500 6-3v-3.500l-6 3-6-3Z' fill='currentColor'/>",
			'users'       => "<circle cx='12' cy='8' r='3.200' fill='currentColor'/><circle cx='5.500' cy='10' r='2.500' fill='currentColor' fill-opacity='.8'/><circle cx='18.500' cy='10' r='2.500' fill='currentColor' fill-opacity='.8'/><path d='M6 20c0-3.500 2.700-5.500 6-5.500s6 2 6 5.500H6Z' fill='currentColor'/>",
			'domain'      => "<rect x='4' y='4' width='6' height='6' rx='1.500' $s stroke-width='1.500'/><rect x='14' y='4' width='6' height='6' rx='1.500' $s stroke-width='1.500'/><rect x='4' y='14' width='6' height='6' rx='1.500' $s stroke-width='1.500'/><rect x='14' y='14' width='6' height='6' rx='1.500' $s stroke-width='1.500'/>",
		);
	}
	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}
	return sprintf( '<svg class="icon icon-%1$s" width="%2$d" height="%2$d" viewBox="0 0 24 24" aria-hidden="true" focusable="false">%3$s</svg>', esc_attr( $name ), (int) $size, $icons[ $name ] );
}


function gm_icon_choices() {
	return array(
		'store' => 'Store', 'money' => 'Money', 'office' => 'Office', 'sun' => 'Sun', 'chart' => 'Bar chart', 'pie' => 'Pie chart',
		'database' => 'Database', 'megaphone' => 'Megaphone', 'apps' => 'Apps', 'tag' => 'Tag', 'sparkles' => 'Sparkles',
		'star' => 'Star', 'cap' => 'Graduation cap', 'users' => 'People', 'bolt' => 'Bolt', 'mail' => 'Mail', 'phone' => 'Phone', 'pin' => 'Pin',
	);
}


function gm_logo() {
	ob_start();
	?>
	<svg class="logo__mark" width="40" height="40" viewBox="0 0 40 40" aria-hidden="true" focusable="false">
		<path d="M20 2C9 2 2 9.500 2 19v1a4 4 0 0 0 4 4h14V2Z" fill="#8A5CFF"/>
		<path d="M20 12h10a8 8 0 0 1 8 8v2a16 16 0 0 1-16 16h-2V12Z" fill="#703BF7"/>
	</svg>
	<span class="logo__text"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
	<?php
	return ob_get_clean();
}


function gm_sparkle() {
	return '<svg class="sparkle" width="56" height="24" viewBox="0 0 56 24" aria-hidden="true" focusable="false"><path d="M12 0c.8 6.500 3.500 9.200 10 10-6.500.8-9.200 3.500-10 10-.8-6.500-3.500-9.200-10-10 6.500-.8 9.200-3.500 10-10Z" fill="#999"/><path d="M32 7c.4 3.200 1.800 4.600 5 5-3.200.4-4.600 1.800-5 5-.4-3.200-1.800-4.600-5-5 3.200-.4 4.600-1.800 5-5Z" fill="#666"/><circle cx="46" cy="12" r="1.500" fill="#555"/></svg>';
}


function gm_section_head( $a ) {
	$a   = wp_parse_args( $a, array( 'title' => '', 'text' => '', 'btn_label' => '', 'btn_url' => '', 'tag' => 'h2', 'id' => '' ) );
	$tag = in_array( $a['tag'], array( 'h1', 'h2', 'h3' ), true ) ? $a['tag'] : 'h2';
	?>
	<header class="section-head">
		<div class="section-head__body">
			<?php echo gm_sparkle(); ?>
			<<?php echo $tag; ?> class="section-title"<?php echo $a['id'] ? ' id="' . esc_attr( $a['id'] ) . '"' : ''; ?>><?php echo esc_html( $a['title'] ); ?></<?php echo $tag; ?>>
			<?php if ( $a['text'] ) : ?>
				<p class="section-text"><?php echo esc_html( $a['text'] ); ?></p>
			<?php endif; ?>
		</div>
		<?php if ( $a['btn_label'] ) : ?>
			<a class="btn" href="<?php echo esc_url( gm_url( $a['btn_url'] ) ); ?>"><?php echo esc_html( $a['btn_label'] ); ?></a>
		<?php endif; ?>
	</header>
	<?php
}


function gm_slider_controls( $total ) {
	?>
	<div class="slider-bar">
		<p class="slider-count" aria-live="polite"><strong data-slider-current>01</strong> of <span data-slider-total><?php echo esc_html( str_pad( (string) $total, 2, '0', STR_PAD_LEFT ) ); ?></span></p>
		<div class="slider-btns">
			<button type="button" class="round-btn" data-slider-prev aria-label="<?php esc_attr_e( 'Previous', 'growmodo' ); ?>"><?php echo gm_icon( 'arrow-left', 22 ); ?></button>
			<button type="button" class="round-btn round-btn--light" data-slider-next aria-label="<?php esc_attr_e( 'Next', 'growmodo' ); ?>"><?php echo gm_icon( 'arrow-right', 22 ); ?></button>
		</div>
	</div>
	<?php
}


function gm_page_hero() {
	?>
	<section class="page-hero">
		<div class="container">
			<h1 class="page-hero__title"><?php the_title(); ?></h1>
			<div class="page-hero__text"><?php the_content(); ?></div>
		</div>
	</section>
	<?php
}
