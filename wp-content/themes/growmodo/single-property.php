<?php


defined( 'ABSPATH' ) || exit;

get_header();
the_post();

$gm_id      = get_the_ID();
$gm_price   = (float) gm_meta( 'price' );
$gm_loc     = gm_meta( 'location_text' );
$gm_gallery = array_filter( array_map( 'absint', explode( ',', gm_meta( 'gallery' ) ) ) );
if ( ! $gm_gallery && has_post_thumbnail() ) {
	$gm_gallery = array( get_post_thumbnail_id() );
}
$gm_gallery = array_values( $gm_gallery );
$gm_feats   = gm_lines( gm_meta( 'features' ) );


$gm_rows = function ( $text ) {
	return array_map(
		function ( $l ) {
			$p = array_map( 'trim', explode( '|', $l ) );
			return array( 'label' => $p[0], 'amount' => $p[1] ?? '', 'note' => $p[2] ?? '' );
		},
		gm_lines( $text )
	);
};
$gm_fees    = $gm_rows( gm_meta( 'fees' ) );
$gm_monthly = $gm_rows( gm_meta( 'monthly_costs' ) );
$gm_expense = $gm_rows( gm_meta( 'monthly_expenses' ) );
$gm_fee_sum = 0;
foreach ( $gm_fees as $f ) {
	$gm_fee_sum += (float) preg_replace( '/[^0-9.]/', '', $f['amount'] );
}
$gm_pct   = (float) ( gm_meta( 'down_pct' ) ?: 20 );
$gm_down  = $gm_price * $gm_pct / 100;
$gm_title = get_the_title();


$gm_fee_html = function ( $r ) {
	echo '<div class="fee"><p class="muted">' . esc_html( $r['label'] ) . '</p><p class="fee__row"><strong>' . esc_html( $r['amount'] ) . '</strong>';
	if ( $r['note'] ) {
		echo '<span class="pill">' . esc_html( $r['note'] ) . '</span>';
	}
	echo '</p></div>';
};
?>

<section class="prop-head">
	<div class="container prop-head__inner">
		<div class="prop-head__title">
			<h1><?php the_title(); ?></h1>
			<?php if ( $gm_loc ) : ?><span class="btn btn--sm btn--static"><?php echo gm_icon( 'pin', 18 ); ?> <?php echo esc_html( $gm_loc ); ?></span><?php endif; ?>
		</div>
		<p class="price"><span><?php esc_html_e( 'Price', 'growmodo' ); ?></span><strong><?php echo esc_html( gm_money( $gm_price ) ); ?></strong></p>
	</div>
</section>

<?php if ( $gm_gallery ) : $gm_n = count( $gm_gallery ); ?>
<section class="container" aria-label="<?php esc_attr_e( 'Gallery', 'growmodo' ); ?>">
	<div class="card gallery" data-gallery data-count="<?php echo esc_attr( $gm_n ); ?>">
		<div class="gallery__thumbs" role="tablist" aria-label="<?php esc_attr_e( 'Gallery thumbnails', 'growmodo' ); ?>">
			<?php foreach ( $gm_gallery as $i => $aid ) : ?>
				<button type="button" role="tab" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr( sprintf(  __( 'Show image %d', 'growmodo' ), $i + 1 ) ); ?>" data-thumb="<?php echo esc_attr( $i ); ?>">
					<?php echo wp_get_attachment_image( $aid, 'gm-thumb', false, array( 'alt' => '', 'loading' => 'lazy' ) ); ?>
				</button>
			<?php endforeach; ?>
		</div>
		<div class="gallery__stage">
			<?php foreach ( $gm_gallery as $i => $aid ) : ?>
				<figure data-img="<?php echo esc_attr( $i ); ?>" <?php echo $i < 2 ? 'class="is-on"' : ''; ?>>
					<?php echo wp_get_attachment_image( $aid, 'gm-gallery', false, array( 'alt' => esc_attr( $gm_title . ' – photo ' . ( $i + 1 ) ), 'loading' => $i < 2 ? 'eager' : 'lazy' ) ); ?>
				</figure>
			<?php endforeach; ?>
		</div>
		<div class="gallery__nav">
			<button type="button" class="round-btn" data-gallery-prev aria-label="<?php esc_attr_e( 'Previous image', 'growmodo' ); ?>"><?php echo gm_icon( 'arrow-left', 22 ); ?></button>
			<div class="gallery__dots" aria-hidden="true"><?php for ( $i = 0; $i < $gm_n; $i++ ) : ?><span<?php echo 0 === $i ? ' class="is-on"' : ''; ?>></span><?php endfor; ?></div>
			<button type="button" class="round-btn round-btn--light" data-gallery-next aria-label="<?php esc_attr_e( 'Next image', 'growmodo' ); ?>"><?php echo gm_icon( 'arrow-right', 22 ); ?></button>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="container prop-info">
	<div class="card info-block">
		<h2><?php esc_html_e( 'Description', 'growmodo' ); ?></h2>
		<div class="muted prop-info__desc"><?php the_content(); ?></div>
		<dl class="specs">
			<div><dt><?php echo gm_icon( 'bed', 20 ); ?> Bedrooms</dt><dd><?php echo esc_html( str_pad( (string) (int) gm_meta( 'bedrooms' ), 2, '0', STR_PAD_LEFT ) ); ?></dd></div>
			<div><dt><?php echo gm_icon( 'bath', 20 ); ?> Bathrooms</dt><dd><?php echo esc_html( str_pad( (string) (int) gm_meta( 'bathrooms' ), 2, '0', STR_PAD_LEFT ) ); ?></dd></div>
			<div><dt><?php echo gm_icon( 'area', 20 ); ?> Area</dt><dd><?php echo esc_html( number_format( (float) gm_meta( 'area' ) ) ); ?> Square Feet</dd></div>
		</dl>
	</div>
	<div class="card info-block">
		<h2><?php esc_html_e( 'Key Features and Amenities', 'growmodo' ); ?></h2>
		<ul class="features">
			<?php foreach ( $gm_feats as $f ) : ?>
				<li><?php echo gm_icon( 'bolt', 20 ); ?> <?php echo esc_html( $f ); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<section class="section" id="inquire" aria-labelledby="inq-title">
	<div class="container split split--form">
		<?php
		gm_section_head(
			array(
				'title' => 'Inquire About ' . $gm_title,
				'id'    => 'inq-title',
				'text'  => 'Interested in this property? Fill out the form below, and our real estate experts will get back to you with more details, including scheduling a viewing and answering any questions you may have.',
			)
		);
		?>
		<div class="card form-card">
			<?php
			gm_form_open( 'property_inquiry', 'inquiry-form' );
			echo '<div class="form__grid">';
			gm_field( 'inquiry-form', 'first_name', 'First Name', 'text', 'Enter First Name', true );
			gm_field( 'inquiry-form', 'last_name', 'Last Name', 'text', 'Enter Last Name', true );
			gm_field( 'inquiry-form', 'email', 'Email', 'email', 'Enter your Email', true );
			gm_field( 'inquiry-form', 'phone', 'Phone', 'tel', 'Enter Phone Number' );
			gm_field( 'inquiry-form', 'property', 'Selected Property', 'text', '', false, array(), trim( $gm_title . ( $gm_loc ? ', ' . $gm_loc : '' ) ), false );
			gm_field( 'inquiry-form', 'message', 'Message', 'textarea', 'Enter your Message here..', true, array(), '', false );
			echo '</div>';
			gm_form_close( 'inquiry-form' );
			?>
		</div>
	</div>
</section>

<?php if ( $gm_price ) : ?>
<section class="section" id="pricing" aria-labelledby="pricing-title">
	<div class="container">
		<?php gm_section_head( array( 'title' => 'Comprehensive Pricing Details', 'id' => 'pricing-title', 'text' => 'At Estatein, transparency is key. We want you to have a clear understanding of all costs associated with your property investment. Below, we break down the pricing for ' . $gm_title . ' to help you make an informed decision' ) ); ?>

		<p class="card note"><strong>Note</strong><span>The figures provided above are estimates and may vary depending on the property, location, and individual circumstances.</span></p>

		<div class="price-layout">
			<p class="price price--big"><span>Listing Price</span><strong><?php echo esc_html( gm_money( $gm_price ) ); ?></strong></p>
			<div class="price-stack">
				<?php if ( $gm_fees ) : ?>
				<div class="card price-card">
					<header><h3>Additional Fees</h3><a class="btn btn--sm" href="#inquire">Learn More</a></header>
					<div class="fee-grid"><?php foreach ( $gm_fees as $r ) { $gm_fee_html( $r ); } ?></div>
				</div>
				<?php endif; ?>
				<?php if ( $gm_monthly ) : ?>
				<div class="card price-card">
					<header><h3>Monthly Costs</h3><a class="btn btn--sm" href="#inquire">Learn More</a></header>
					<div class="fee-grid"><?php foreach ( $gm_monthly as $r ) { $gm_fee_html( $r ); } ?></div>
				</div>
				<?php endif; ?>
				<div class="card price-card">
					<header><h3>Total Initial Costs</h3><a class="btn btn--sm" href="#inquire">Learn More</a></header>
					<div class="fee-grid">
						<?php
						$gm_fee_html( array( 'label' => 'Listing Price', 'amount' => gm_money( $gm_price ), 'note' => '' ) );
						$gm_fee_html( array( 'label' => 'Additional Fees', 'amount' => gm_money( $gm_fee_sum ), 'note' => 'Property transfer tax, legal fees, inspection, insurance' ) );
						$gm_fee_html( array( 'label' => 'Down Payment', 'amount' => gm_money( $gm_down ), 'note' => rtrim( rtrim( number_format( $gm_pct, 1 ), '0' ), '.' ) . '%' ) );
						$gm_fee_html( array( 'label' => 'Mortgage Amount', 'amount' => gm_money( $gm_price - $gm_down ), 'note' => 'If applicable' ) );
						?>
					</div>
				</div>
				<?php if ( $gm_expense ) : ?>
				<div class="card price-card">
					<header><h3>Monthly Expenses</h3><a class="btn btn--sm" href="#inquire">Learn More</a></header>
					<div class="fee-grid"><?php foreach ( $gm_expense as $r ) { $gm_fee_html( $r ); } ?></div>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

<?php
$gm_faqs = new WP_Query( array( 'post_type' => 'faq', 'posts_per_page' => 12, 'orderby' => 'menu_order title', 'order' => 'ASC', 'no_found_rows' => true ) );
if ( $gm_faqs->have_posts() ) :
	?>
<section class="section" id="faq" aria-labelledby="faq-title">
	<div class="container">
		<?php gm_section_head( array( 'title' => 'Frequently Asked Questions', 'id' => 'faq-title', 'text' => "Find answers to common questions about Estatein's services, property listings, and the real estate process. We're here to provide clarity and assist you every step of the way.", 'btn_label' => 'View All FAQ’s', 'btn_url' => '/contact-us/' ) ); ?>
		<div data-slider>
			<div class="slider__track" tabindex="0" role="group" aria-label="<?php esc_attr_e( 'Frequently asked questions', 'growmodo' ); ?>">
				<?php
				while ( $gm_faqs->have_posts() ) {
					$gm_faqs->the_post();
					$gm_fid = 'faq-' . get_the_ID();
					?>
					<article class="card faq" data-slide>
						<h3 class="faq__q"><?php the_title(); ?></h3>
						<p class="faq__a"><?php echo esc_html( get_the_excerpt() ); ?></p>
						<div class="faq__more" id="<?php echo esc_attr( $gm_fid ); ?>" hidden><?php echo wp_kses_post( wpautop( get_the_content() ) ); ?></div>
						<button type="button" class="btn btn--sm" aria-expanded="false" aria-controls="<?php echo esc_attr( $gm_fid ); ?>" data-faq-toggle data-more="Read More" data-less="Show Less">Read More</button>
					</article>
				<?php } wp_reset_postdata(); ?>
			</div>
			<?php gm_slider_controls( $gm_faqs->post_count ); ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php
get_footer();
