<?php

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) :
	the_post();
	?>
	<section class="page-hero">
		<div class="container"><h1 class="page-hero__title"><?php the_title(); ?></h1></div>
	</section>
	<section class="section">
		<div class="container prose"><?php the_content(); ?></div>
	</section>
	<?php
endwhile;
get_footer();
