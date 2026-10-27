<?php

defined( 'ABSPATH' ) || exit;

function gm_about_items( $slug ) {
	return get_posts(
		array(
			'post_type'      => 'about_item',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
			'tax_query'      => array( array( 'taxonomy' => 'about_group', 'field' => 'slug', 'terms' => $slug ) ),
		)
	);
}

get_header();
the_post();

$gm_group = function ( $slug ) {
	$t = get_term_by( 'slug', $slug, 'about_group' );
	return array( 'title' => $t ? $t->name : '', 'text' => $t ? $t->description : '' );
};
$gm_team    = get_posts( array( 'post_type' => 'team_member', 'posts_per_page' => 8, 'orderby' => 'menu_order', 'order' => 'ASC' ) );
$gm_clients = get_posts( array( 'post_type' => 'client', 'posts_per_page' => 12, 'orderby' => 'menu_order', 'order' => 'ASC' ) );
?>

<section class="section section--first" id="story" aria-labelledby="story-title">
	<div class="container split">
		<div>
			<?php echo gm_sparkle(); ?>
			<h1 class="section-title" id="story-title"><?php the_title(); ?></h1>
			<div class="section-text"><?php the_content(); ?></div>
			<?php get_template_part( 'template-parts/stats' ); ?>
		</div>
		<div class="card frame">
			<?php echo gm_img( 'about-house.webp', 'A hand holding a model of a modern house', 1200, 834, '', true ); ?>
		</div>
	</div>
</section>

<?php $gm_v = $gm_group( 'values' ); ?>
<section class="section" id="values" aria-labelledby="values-title">
	<div class="container split split--values">
		<div>
			<?php gm_section_head( array( 'title' => $gm_v['title'], 'text' => $gm_v['text'], 'id' => 'values-title' ) ); ?>
		</div>
		<div class="card values">
			<?php foreach ( gm_about_items( 'values' ) as $gm_i ) : ?>
				<div class="value">
					<h3 class="value__title"><span class="icon-ring"><?php echo gm_icon( get_post_meta( $gm_i->ID, '_gm_icon', true ) ?: 'star', 26 ); ?></span><?php echo esc_html( get_the_title( $gm_i ) ); ?></h3>
					<p><?php echo esc_html( $gm_i->post_excerpt ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php $gm_a = $gm_group( 'achievements' ); ?>
<section class="section" aria-labelledby="ach-title">
	<div class="container">
		<?php gm_section_head( array( 'title' => $gm_a['title'], 'text' => $gm_a['text'], 'id' => 'ach-title' ) ); ?>
		<div class="grid grid--3">
			<?php foreach ( gm_about_items( 'achievements' ) as $gm_i ) : ?>
				<div class="card info-card">
					<h3><?php echo esc_html( get_the_title( $gm_i ) ); ?></h3>
					<p><?php echo esc_html( $gm_i->post_excerpt ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php $gm_s = $gm_group( 'steps' ); ?>
<section class="section" id="process" aria-labelledby="steps-title">
	<div class="container">
		<?php gm_section_head( array( 'title' => $gm_s['title'], 'text' => $gm_s['text'], 'id' => 'steps-title' ) ); ?>
		<ol class="grid grid--3 steps">
			<?php foreach ( gm_about_items( 'steps' ) as $gm_i ) : ?>
				<li class="step">
					<span class="step__label"><?php echo esc_html( gm_meta( 'step_label', $gm_i->ID ) ); ?></span>
					<div class="card info-card">
						<h3><?php echo esc_html( get_the_title( $gm_i ) ); ?></h3>
						<p><?php echo esc_html( $gm_i->post_excerpt ); ?></p>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>

<section class="section" id="team" aria-labelledby="team-title">
	<div class="container">
		<?php gm_section_head( array( 'title' => 'Meet the Estatein Team', 'text' => 'At Estatein, our success is driven by the dedication and expertise of our team. Get to know the people behind our mission to make your real estate dreams a reality.', 'id' => 'team-title' ) ); ?>
		<ul class="grid grid--4">
			<?php foreach ( $gm_team as $gm_m ) : ?>
				<li class="card member">
					<div class="member__photo">
						<?php echo get_the_post_thumbnail( $gm_m, 'gm-card', array( 'alt' => esc_attr( $gm_m->post_title ), 'loading' => 'lazy' ) ); ?>
						<?php $gm_tw = gm_meta( 'twitter', $gm_m->ID ); ?>
						<a class="member__social" href="<?php echo esc_url( $gm_tw ? $gm_tw : gm_opt( 'twitter' ) ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $gm_m->post_title ); ?> on Twitter"><?php echo gm_icon( 'twitter', 22 ); ?></a>
					</div>
					<h3><?php echo esc_html( $gm_m->post_title ); ?></h3>
					<p><?php echo esc_html( gm_meta( 'role', $gm_m->ID ) ); ?></p>
					<a class="say-hello" href="<?php echo esc_url( home_url( '/contact-us/#contact-form' ) ); ?>"><span>Say Hello 👋</span><span class="say-hello__btn"><?php echo gm_icon( 'send', 18 ); ?></span></a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<section class="section" id="clients" aria-labelledby="clients-title">
	<div class="container">
		<?php gm_section_head( array( 'title' => 'Our Valued Clients', 'text' => "At Estatein, we have had the privilege of working with a diverse range of clients across various industries. Here are some of the clients we've had the pleasure of serving", 'id' => 'clients-title' ) ); ?>
		<div data-slider>
			<div class="slider__track slider__track--2" tabindex="0" role="group" aria-label="<?php esc_attr_e( 'Clients', 'growmodo' ); ?>">
				<?php foreach ( $gm_clients as $gm_c ) : ?>
					<article class="card client" data-slide>
						<div class="client__head">
							<div>
								<p class="muted">Since <?php echo esc_html( gm_meta( 'since', $gm_c->ID ) ); ?></p>
								<h3><?php echo esc_html( $gm_c->post_title ); ?></h3>
							</div>
							<a class="btn btn--sm" href="<?php echo esc_url( gm_meta( 'url', $gm_c->ID ) ?: '#' ); ?>" target="_blank" rel="noopener noreferrer">Visit Website<span class="screen-reader-text"> – <?php echo esc_html( $gm_c->post_title ); ?></span></a>
						</div>
						<dl class="client__meta">
							<div><dt><?php echo gm_icon( 'domain', 20 ); ?> Domain</dt><dd><?php echo esc_html( gm_meta( 'domain', $gm_c->ID ) ); ?></dd></div>
							<div><dt><?php echo gm_icon( 'bolt', 20 ); ?> Category</dt><dd><?php echo esc_html( gm_meta( 'category', $gm_c->ID ) ); ?></dd></div>
						</dl>
						<blockquote class="client__quote"><p class="muted">What They Said 🤗</p><?php echo esc_html( wp_strip_all_tags( $gm_c->post_content ) ); ?></blockquote>
					</article>
				<?php endforeach; ?>
			</div>
			<?php gm_slider_controls( count( $gm_clients ) ); ?>
		</div>
	</div>
</section>

<?php
get_footer();
