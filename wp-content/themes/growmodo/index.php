<?php

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="page-hero">
	<div class="container">
		<h1 class="page-hero__title">
			<?php
			if ( is_search() ) {

				printf( esc_html__( 'Search results for “%s”', 'growmodo' ), esc_html( get_search_query() ) );
			} elseif ( is_home() && ! is_front_page() ) {
				single_post_title();
			} else {
				the_archive_title();
			}
			?>
		</h1>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="grid grid--3">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article <?php post_class( 'card post-card' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<a href="<?php the_permalink(); ?>" class="prop-card__img" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail( 'gm-card', array( 'alt' => '' ) ); ?></a>
						<?php endif; ?>
						<h2 class="prop-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p class="prop-card__text"><?php echo esc_html( get_the_excerpt() ); ?></p>
						<a class="btn btn--primary" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read More', 'growmodo' ); ?></a>
					</article>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination( array( 'prev_text' => '←', 'next_text' => '→' ) ); ?>
		<?php else : ?>
			<p class="empty card"><?php esc_html_e( 'Nothing found.', 'growmodo' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
