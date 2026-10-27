<?php

defined( 'ABSPATH' ) || exit;

get_header();
the_post();
gm_page_hero();
get_template_part( 'template-parts/feature-tiles' );

$gm_groups = get_terms( array( 'taxonomy' => 'service_group', 'hide_empty' => true, 'orderby' => 'term_id' ) );
foreach ( (array) $gm_groups as $gm_g ) :
	$gm_services = get_posts(
		array(
			'post_type'      => 'service',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
			'tax_query'      => array( array( 'taxonomy' => 'service_group', 'field' => 'term_id', 'terms' => $gm_g->term_id ) ),
		)
	);
	$gm_cta_t = get_term_meta( $gm_g->term_id, 'cta_title', true );
	$gm_cta_x = get_term_meta( $gm_g->term_id, 'cta_text', true );
	?>
	<section class="section" id="<?php echo esc_attr( $gm_g->slug ); ?>" aria-labelledby="<?php echo esc_attr( $gm_g->slug ); ?>-title">
		<div class="container">
			<?php gm_section_head( array( 'title' => $gm_g->name, 'text' => $gm_g->description, 'id' => $gm_g->slug . '-title' ) ); ?>
			<div class="grid grid--3 services">
				<?php foreach ( $gm_services as $gm_s ) : ?>
					<article class="card service">
						<h3 class="service__title"><span class="icon-ring"><?php echo gm_icon( get_post_meta( $gm_s->ID, '_gm_icon', true ) ?: 'star', 26 ); ?></span><?php echo esc_html( $gm_s->post_title ); ?></h3>
						<p><?php echo esc_html( $gm_s->post_excerpt ); ?></p>
					</article>
				<?php endforeach; ?>
				<?php if ( $gm_cta_t ) : ?>
					<article class="card service service--cta">
						<div class="service__cta-head">
							<h3><?php echo esc_html( $gm_cta_t ); ?></h3>
							<a class="btn btn--sm" href="<?php echo esc_url( home_url( '/contact-us/#contact-form' ) ); ?>"><?php esc_html_e( 'Learn More', 'growmodo' ); ?><span class="screen-reader-text"> – <?php echo esc_html( $gm_cta_t ); ?></span></a>
						</div>
						<p><?php echo esc_html( $gm_cta_x ); ?></p>
					</article>
				<?php endif; ?>
			</div>
		</div>
	</section>
<?php endforeach; ?>

<?php
get_footer();
