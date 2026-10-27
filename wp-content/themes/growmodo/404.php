<?php

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="page-hero">
	<div class="container">
		<h1 class="page-hero__title"><?php esc_html_e( 'Page not found', 'growmodo' ); ?></h1>
		<p class="section-text"><?php esc_html_e( 'The page you are looking for has moved or no longer exists.', 'growmodo' ); ?></p>
		<p><a class="btn btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to Home', 'growmodo' ); ?></a>
		<a class="btn" href="<?php echo esc_url( home_url( '/properties/' ) ); ?>"><?php esc_html_e( 'Browse Properties', 'growmodo' ); ?></a></p>
	</div>
</section>
<?php
get_footer();
