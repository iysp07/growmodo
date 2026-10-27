<?php

defined( 'ABSPATH' ) || exit;

$gm_type  = get_the_terms( get_the_ID(), 'property_type' );
$gm_type  = $gm_type && ! is_wp_error( $gm_type ) ? $gm_type[0]->name : 'Villa';
$gm_beds  = gm_meta( 'bedrooms' );
$gm_baths = gm_meta( 'bathrooms' );
$gm_link  = get_permalink();
?>
<article class="card prop-card" data-slide>
	<a class="prop-card__img" href="<?php echo esc_url( $gm_link ); ?>" tabindex="-1" aria-hidden="true">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'gm-card', array( 'alt' => '', 'loading' => 'lazy' ) );
		}
		?>
	</a>
	<h3 class="prop-card__title"><a href="<?php echo esc_url( $gm_link ); ?>"><?php the_title(); ?></a></h3>
	<p class="prop-card__text"><?php echo esc_html( get_the_excerpt() ); ?> <a href="<?php echo esc_url( $gm_link ); ?>"><?php esc_html_e( 'Read More', 'growmodo' ); ?><span class="screen-reader-text"> <?php esc_html_e( 'about', 'growmodo' ); ?> <?php the_title(); ?></span></a></p>
	<ul class="chips">
		<?php if ( $gm_beds ) : ?><li class="chip"><?php echo gm_icon( 'bed', 20 ); ?> <?php echo esc_html( (int) $gm_beds ); ?>-Bedroom</li><?php endif; ?>
		<?php if ( $gm_baths ) : ?><li class="chip"><?php echo gm_icon( 'bath', 20 ); ?> <?php echo esc_html( (float) $gm_baths ); ?>-Bathroom</li><?php endif; ?>
		<li class="chip"><?php echo gm_icon( 'building', 20 ); ?> <?php echo esc_html( $gm_type ); ?></li>
	</ul>
	<div class="prop-card__foot">
		<p class="price"><span><?php esc_html_e( 'Price', 'growmodo' ); ?></span><strong><?php echo esc_html( gm_money( gm_meta( 'price' ) ) ); ?></strong></p>
		<a class="btn btn--primary" href="<?php echo esc_url( $gm_link ); ?>"><?php esc_html_e( 'View Property Details', 'growmodo' ); ?></a>
	</div>
</article>
