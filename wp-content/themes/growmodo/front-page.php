<?php

defined( 'ABSPATH' ) || exit;

get_header();

$gm_props = new WP_Query( array( 'post_type' => 'property', 'posts_per_page' => 12, 'no_found_rows' => true ) );
$gm_tests = new WP_Query( array( 'post_type' => 'testimonial', 'posts_per_page' => 12, 'no_found_rows' => true ) );
$gm_faqs  = new WP_Query( array( 'post_type' => 'faq', 'posts_per_page' => 12, 'orderby' => 'menu_order title', 'order' => 'ASC', 'no_found_rows' => true ) );
$gm_total = (int) wp_count_posts( 'property' )->publish;
?>

<section class="hero" id="hero">
	<div class="container hero__grid">
		<div class="hero__copy">
			<h1 class="hero__title"><?php echo esc_html( gm_opt( 'hero_title' ) ); ?></h1>
			<p class="hero__text"><?php echo esc_html( gm_opt( 'hero_text' ) ); ?></p>
			<div class="hero__actions">
				<a class="btn" href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>"><?php esc_html_e( 'Learn More', 'growmodo' ); ?></a>
				<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/properties/' ) ); ?>"><?php esc_html_e( 'Browse Properties', 'growmodo' ); ?></a>
			</div>
			<?php get_template_part( 'template-parts/stats' ); ?>
		</div>

		<div class="hero__media">
			<a class="spin-badge" href="#properties" aria-label="<?php esc_attr_e( 'Discover Your Dream Property – jump to featured properties', 'growmodo' ); ?>">
				<svg viewBox="0 0 140 140" aria-hidden="true" focusable="false" class="spin-badge__ring">
					<defs><path id="spin-path" d="M70 70m-52 0a52 52 0 1 1 104 0a52 52 0 1 1 -104 0"/></defs>
					<text><textPath href="#spin-path" startOffset="0">✨ Discover Your Dream Property ✨ Discover Your Dream Property</textPath></text>
				</svg>
				<span class="spin-badge__core"><?php echo gm_icon( 'arrow-ur', 30 ); ?></span>
			</a>
			<?php echo gm_img( 'hero-building.webp', 'Modern blue glass towers', 920, 814, 'hero__img', true ); ?>
		</div>
	</div>
</section>

<?php get_template_part( 'template-parts/feature-tiles' ); ?>

<section class="section" id="properties" aria-labelledby="props-title">
	<div class="container">
		<?php
		gm_section_head(
			array(
				'title'     => 'Featured Properties',
				'id'        => 'props-title',
				'text'      => 'Explore our handpicked selection of featured properties. Each listing offers a glimpse into exceptional homes and investments available through Estatein. Click "View Details" for more information.',
				'btn_label' => 'View All Properties',
				'btn_url'   => '/properties/',
			)
		);
		?>
		<div data-slider>
			<div class="slider__track" tabindex="0" role="group" aria-label="<?php esc_attr_e( 'Featured properties', 'growmodo' ); ?>">
				<?php
				while ( $gm_props->have_posts() ) {
					$gm_props->the_post();
					get_template_part( 'template-parts/card-property' );
				}
				wp_reset_postdata();
				?>
			</div>
			<?php gm_slider_controls( $gm_total ); ?>
		</div>
	</div>
</section>

<section class="section" id="testimonials" aria-labelledby="test-title">
	<div class="container">
		<?php
		gm_section_head(
			array(
				'title'     => 'What Our Clients Say',
				'id'        => 'test-title',
				'text'      => 'Read the success stories and heartfelt testimonials from our valued clients. Discover why they chose Estatein for their real estate needs.',
				'btn_label' => 'View All Testimonials',
				'btn_url'   => '/about-us/#clients',
			)
		);
		?>
		<div data-slider>
			<div class="slider__track" tabindex="0" role="group" aria-label="<?php esc_attr_e( 'Testimonials', 'growmodo' ); ?>">
				<?php
				while ( $gm_tests->have_posts() ) {
					$gm_tests->the_post();
					$gm_rating = max( 1, min( 5, (int) gm_meta( 'rating' ) ?: 5 ) );
					?>
					<figure class="card testi" data-slide>
						<div class="stars" role="img" aria-label="<?php echo esc_attr( sprintf(  __( 'Rated %d out of 5', 'growmodo' ), $gm_rating ) ); ?>">
							<?php for ( $s = 0; $s < $gm_rating; $s++ ) : ?><span><?php echo gm_icon( 'star', 18 ); ?></span><?php endfor; ?>
						</div>
						<h3 class="testi__title"><?php the_title(); ?></h3>
						<blockquote class="testi__text"><?php echo esc_html( wp_strip_all_tags( get_the_content() ) ); ?></blockquote>
						<figcaption class="testi__who">
							<?php
							if ( has_post_thumbnail() ) {
								the_post_thumbnail( 'thumbnail', array( 'alt' => '', 'class' => 'avatar', 'loading' => 'lazy' ) );
							}
							?>
							<span><strong><?php echo esc_html( gm_meta( 'person_name' ) ); ?></strong><small><?php echo esc_html( gm_meta( 'person_location' ) ); ?></small></span>
						</figcaption>
					</figure>
					<?php
				}
				wp_reset_postdata();
				?>
			</div>
			<?php gm_slider_controls( $gm_tests->post_count ); ?>
		</div>
	</div>
</section>

<section class="section" id="faq" aria-labelledby="faq-title">
	<div class="container">
		<?php
		gm_section_head(
			array(
				'title'     => 'Frequently Asked Questions',
				'id'        => 'faq-title',
				'text'      => "Find answers to common questions about Estatein's services, property listings, and the real estate process. We're here to provide clarity and assist you every step of the way.",
				'btn_label' => 'View All FAQ’s',
				'btn_url'   => '/contact-us/',
			)
		);
		?>
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
						<button type="button" class="btn btn--sm" aria-expanded="false" aria-controls="<?php echo esc_attr( $gm_fid ); ?>" data-faq-toggle data-more="<?php esc_attr_e( 'Read More', 'growmodo' ); ?>" data-less="<?php esc_attr_e( 'Show Less', 'growmodo' ); ?>"><?php esc_html_e( 'Read More', 'growmodo' ); ?></button>
					</article>
					<?php
				}
				wp_reset_postdata();
				?>
			</div>
			<?php gm_slider_controls( $gm_faqs->post_count ); ?>
		</div>
	</div>
</section>

<?php
get_footer();
