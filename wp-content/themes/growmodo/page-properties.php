<?php

defined( 'ABSPATH' ) || exit;

function gm_price_ranges() {
	return array(
		'0-500000'       => array( 'Under $500,000', 0, 500000 ),
		'500000-1000000' => array( '$500,000 – $1,000,000', 500000, 1000000 ),
		'1000000-0'      => array( '$1,000,000 +', 1000000, 0 ),
	);
}
function gm_size_ranges() {
	return array(
		'0-1500'    => array( 'Under 1,500 sq ft', 0, 1500 ),
		'1500-3000' => array( '1,500 – 3,000 sq ft', 1500, 3000 ),
		'3000-0'    => array( '3,000 sq ft +', 3000, 0 ),
	);
}

function gm_range_clause( $key, $range ) {
	list( , $min, $max ) = $range;
	if ( $min && $max ) {
		return array( 'key' => $key, 'value' => array( $min, $max ), 'type' => 'NUMERIC', 'compare' => 'BETWEEN' );
	}
	return $max
		? array( 'key' => $key, 'value' => $max, 'type' => 'NUMERIC', 'compare' => '<=' )
		: array( 'key' => $key, 'value' => $min, 'type' => 'NUMERIC', 'compare' => '>=' );
}

get_header();
the_post();

$gm_get = function ( $k ) {
	return isset( $_GET[ $k ] ) ? sanitize_text_field( wp_unslash( $_GET[ $k ] ) ) : '';
};
$gm_q     = $gm_get( 'q' );
$gm_loc   = $gm_get( 'location' );
$gm_type  = $gm_get( 'ptype' );
$gm_price = $gm_get( 'price' );
$gm_size  = $gm_get( 'size' );
$gm_year  = $gm_get( 'year' );
$gm_paged = max( 1, (int) $gm_get( 'pg' ) );

$gm_args = array(
	'post_type'      => 'property',
	'posts_per_page' => 6,
	'paged'          => $gm_paged,
	's'              => $gm_q,
	'meta_query'     => array(),
	'tax_query'      => array(),
);
$gm_prices = gm_price_ranges();
$gm_sizes  = gm_size_ranges();
if ( isset( $gm_prices[ $gm_price ] ) ) {
	$gm_args['meta_query'][] = gm_range_clause( '_gm_price', $gm_prices[ $gm_price ] );
}
if ( isset( $gm_sizes[ $gm_size ] ) ) {
	$gm_args['meta_query'][] = gm_range_clause( '_gm_area', $gm_sizes[ $gm_size ] );
}
if ( $gm_year ) {
	$gm_args['meta_query'][] = array( 'key' => '_gm_year', 'value' => (int) $gm_year, 'type' => 'NUMERIC' );
}
if ( $gm_loc ) {
	$gm_args['tax_query'][] = array( 'taxonomy' => 'property_location', 'field' => 'slug', 'terms' => $gm_loc );
}
if ( $gm_type ) {
	$gm_args['tax_query'][] = array( 'taxonomy' => 'property_type', 'field' => 'slug', 'terms' => $gm_type );
}
$gm_query = new WP_Query( $gm_args );

global $wpdb;
$gm_years = $wpdb->get_col( "SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_gm_year' AND meta_value <> '' ORDER BY meta_value DESC" );
$gm_locs  = get_terms( array( 'taxonomy' => 'property_location', 'hide_empty' => false ) );
$gm_types = get_terms( array( 'taxonomy' => 'property_type', 'hide_empty' => false ) );

$gm_select = function ( $name, $label, $icon, $opts, $current ) {
	echo '<div class="filter"><span class="filter__icon">' . gm_icon( $icon, 22 ) . '</span>';
	echo '<label class="screen-reader-text" for="f-' . esc_attr( $name ) . '">' . esc_html( $label ) . '</label>';
	echo '<select id="f-' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '" data-autosubmit><option value="">' . esc_html( $label ) . '</option>';
	foreach ( $opts as $v => $l ) {
		printf( '<option value="%s"%s>%s</option>', esc_attr( $v ), selected( (string) $current, (string) $v, false ), esc_html( $l ) );
	}
	echo '</select>' . gm_icon( 'chevron', 20 ) . '</div>';
};

gm_page_hero();
?>

<section class="finder" id="finder" aria-label="<?php esc_attr_e( 'Search properties', 'growmodo' ); ?>">
	<form class="container finder__form" method="get" action="<?php echo esc_url( get_permalink() . '#portfolio' ); ?>" role="search">
		<div class="finder__search">
			<label class="screen-reader-text" for="f-q"><?php esc_html_e( 'Search For A Property', 'growmodo' ); ?></label>
			<input type="search" id="f-q" name="q" value="<?php echo esc_attr( $gm_q ); ?>" placeholder="<?php esc_attr_e( 'Search For A Property', 'growmodo' ); ?>">
			<button type="submit" class="btn btn--primary"><?php echo gm_icon( 'search', 20 ); ?> <?php esc_html_e( 'Find Property', 'growmodo' ); ?></button>
		</div>
		<div class="finder__filters">
			<?php
			$gm_select( 'location', 'Location', 'pin', wp_list_pluck( (array) $gm_locs, 'name', 'slug' ), $gm_loc );
			$gm_select( 'ptype', 'Property Type', 'store', wp_list_pluck( (array) $gm_types, 'name', 'slug' ), $gm_type );
			$gm_select( 'price', 'Pricing Range', 'money', gm_range_labels( $gm_prices ), $gm_price );
			$gm_select( 'size', 'Property Size', 'cube', gm_range_labels( $gm_sizes ), $gm_size );
			$gm_select( 'year', 'Build Year', 'calendar', array_combine( $gm_years, $gm_years ), $gm_year );
			?>
		</div>
	</form>
</section>

<section class="section" id="portfolio" aria-labelledby="portfolio-title">
	<div class="container">
		<?php gm_section_head( array( 'title' => 'Discover a World of Possibilities', 'id' => 'portfolio-title', 'text' => 'Our portfolio of properties is as diverse as your dreams. Explore the following categories to find the perfect property that resonates with your vision of home' ) ); ?>

		<?php if ( $gm_query->have_posts() ) : ?>
			<div class="grid grid--3 listing" id="categories">
				<?php
				while ( $gm_query->have_posts() ) {
					$gm_query->the_post();
					$gm_tag = gm_meta( 'tagline' );
					echo '<div class="listing__item">';
					if ( $gm_tag ) {
						echo '<p class="listing__tag">' . esc_html( $gm_tag ) . '</p>';
					}
					get_template_part( 'template-parts/card-property' );
					echo '</div>';
				}
				wp_reset_postdata();
				?>
			</div>
			<?php
			$gm_pages = (int) $gm_query->max_num_pages;
			$gm_base  = remove_query_arg( 'pg' );
			$gm_link  = function ( $n ) use ( $gm_base ) {
				return esc_url( add_query_arg( 'pg', $n, $gm_base ) . '#portfolio' );
			};
			?>
			<nav class="slider-bar" aria-label="<?php esc_attr_e( 'Pagination', 'growmodo' ); ?>">
				<p class="slider-count"><strong><?php echo esc_html( str_pad( (string) $gm_paged, 2, '0', STR_PAD_LEFT ) ); ?></strong> of <?php echo esc_html( str_pad( (string) max( 1, $gm_pages ), 2, '0', STR_PAD_LEFT ) ); ?></p>
				<div class="slider-btns">
					<?php if ( $gm_paged > 1 ) : ?>
						<a class="round-btn" href="<?php echo $gm_link( $gm_paged - 1 ); ?>" aria-label="<?php esc_attr_e( 'Previous page', 'growmodo' ); ?>"><?php echo gm_icon( 'arrow-left', 22 ); ?></a>
					<?php else : ?>
						<span class="round-btn is-disabled" aria-hidden="true"><?php echo gm_icon( 'arrow-left', 22 ); ?></span>
					<?php endif; ?>
					<?php if ( $gm_paged < $gm_pages ) : ?>
						<a class="round-btn round-btn--light" href="<?php echo $gm_link( $gm_paged + 1 ); ?>" aria-label="<?php esc_attr_e( 'Next page', 'growmodo' ); ?>"><?php echo gm_icon( 'arrow-right', 22 ); ?></a>
					<?php else : ?>
						<span class="round-btn round-btn--light is-disabled" aria-hidden="true"><?php echo gm_icon( 'arrow-right', 22 ); ?></span>
					<?php endif; ?>
				</div>
			</nav>
		<?php else : ?>
			<p class="empty card"><?php esc_html_e( 'No properties match your search. Try clearing a filter.', 'growmodo' ); ?> <a href="<?php echo esc_url( get_permalink() . '#portfolio' ); ?>"><?php esc_html_e( 'Reset filters', 'growmodo' ); ?></a></p>
		<?php endif; ?>
	</div>
</section>

<section class="section" id="property-request" aria-labelledby="req-title">
	<div class="container">
		<?php gm_section_head( array( 'title' => "Let's Make it Happen", 'id' => 'req-title', 'text' => "Ready to take the first step toward your dream property? Fill out the form below, and our real estate wizards will work their magic to find your perfect match. Don't wait; let's embark on this exciting journey together." ) ); ?>
		<div class="card form-card">
			<?php
			gm_form_open( 'property_request', 'request-form' );
			echo '<div class="form__grid form__grid--4">';
			gm_field( 'request-form', 'first_name', 'First Name', 'text', 'Enter First Name', true );
			gm_field( 'request-form', 'last_name', 'Last Name', 'text', 'Enter Last Name', true );
			gm_field( 'request-form', 'email', 'Email', 'email', 'Enter your Email', true );
			gm_field( 'request-form', 'phone', 'Phone', 'tel', 'Enter Phone Number' );
			gm_field( 'request-form', 'location', 'Preferred Location', 'select', 'Select Location', false, wp_list_pluck( (array) $gm_locs, 'name' ) );
			gm_field( 'request-form', 'property_type', 'Property Type', 'select', 'Select Property Type', false, wp_list_pluck( (array) $gm_types, 'name' ) );
			gm_field( 'request-form', 'bathrooms', 'No. of Bathrooms', 'select', 'Select no. of Bathrooms', false, array( '1', '2', '3', '4', '5+' ) );
			gm_field( 'request-form', 'bedrooms', 'No. of Bedrooms', 'select', 'Select no. of Bedrooms', false, array( '1', '2', '3', '4', '5+' ) );
			gm_field( 'request-form', 'budget', 'Budget', 'select', 'Select Budget', false, array( 'Under $500,000', '$500,000 – $1,000,000', '$1,000,000 +' ) );
			gm_field( 'request-form', 'contact_number', 'Preferred Contact Method', 'tel', 'Enter Your Number' );
			gm_field( 'request-form', 'contact_email', 'Preferred Contact Method (email)', 'email', 'Enter Your Email' );
			echo '</div><div class="form__grid">';
			gm_field( 'request-form', 'message', 'Message', 'textarea', 'Enter your Message here..', false, array(), '', false );
			echo '</div>';
			gm_form_close( 'request-form' );
			?>
		</div>
	</div>
</section>

<?php
get_footer();

function gm_range_labels( $ranges ) {
	return array_map( fn( $r ) => $r[0], $ranges );
}
