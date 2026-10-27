<?php

defined( 'ABSPATH' ) || exit;

$gm_nav     = gm_primary_nav();
$gm_cta     = array_pop( $gm_nav );
$gm_here    = untrailingslashit( home_url( add_query_arg( null, null ) ) );
$gm_here    = untrailingslashit( strtok( $gm_here, '?' ) );
$gm_is_cur  = function ( $item ) use ( $gm_here ) {
	if ( ! empty( $item['current'] ) ) {
		return true;
	}
	$u = untrailingslashit( $item['url'] );
	if ( untrailingslashit( home_url() ) === $u ) {
		return is_front_page();
	}
	return $u === $gm_here || ( is_singular( 'property' ) && false !== strpos( $u, '/properties' ) );
};
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'growmodo' ); ?></a>

<div class="announce" data-announce>
	<p class="announce__text">
		<?php echo esc_html( gm_opt( 'banner_text' ) ); ?>
		<a href="<?php echo esc_url( gm_url( gm_opt( 'banner_link' ) ) ); ?>"><?php echo esc_html( gm_opt( 'banner_link_text' ) ); ?></a>
	</p>
	<button type="button" class="announce__close" data-announce-close aria-label="<?php esc_attr_e( 'Dismiss announcement', 'growmodo' ); ?>"><?php echo gm_icon( 'close', 20 ); ?></button>
</div>

<header class="site-header" data-header>
	<div class="container site-header__inner">
		<a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?> – <?php esc_attr_e( 'Home', 'growmodo' ); ?>">
			<?php echo gm_logo(); ?>
		</a>

		<button type="button" class="nav-toggle" aria-expanded="false" aria-controls="site-nav" data-nav-toggle>
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'growmodo' ); ?></span>
			<?php echo gm_icon( 'menu', 26 ); ?>
		</button>

		<div class="site-nav" id="site-nav" data-nav>
			<nav aria-label="<?php esc_attr_e( 'Primary', 'growmodo' ); ?>">
				<ul class="nav-list">
					<?php foreach ( $gm_nav as $item ) : ?>
						<li><a href="<?php echo esc_url( $item['url'] ); ?>"<?php echo $gm_is_cur( $item ) ? ' class="is-current" aria-current="page"' : ''; ?>><?php echo esc_html( $item['title'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>
			<?php if ( $gm_cta ) : ?>
				<a class="btn nav-cta<?php echo $gm_is_cur( $gm_cta ) ? ' btn--primary' : ''; ?>" href="<?php echo esc_url( $gm_cta['url'] ); ?>"><?php echo esc_html( $gm_cta['title'] ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</header>

<main id="main" class="site-main">
