<?php


defined( 'ABSPATH' ) || exit;

$gm_cols = array(
	'Home'       => array( 'Hero Section' => '/#hero', 'Features' => '/#features', 'Properties' => '/#properties', 'Testimonials' => '/#testimonials', 'FAQ’s' => '/#faq' ),
	'About Us'   => array( 'Our Story' => '/about-us/#story', 'Our Works' => '/about-us/#values', 'How It Works' => '/about-us/#process', 'Our Team' => '/about-us/#team', 'Our Clients' => '/about-us/#clients' ),
	'Properties' => array( 'Portfolio' => '/properties/#portfolio', 'Categories' => '/properties/#categories' ),
	'Services'   => array( 'Valuation Mastery' => '/services/#selling', 'Strategic Marketing' => '/services/#selling', 'Negotiation Wizardry' => '/services/#selling', 'Closing Success' => '/services/#selling', 'Property Management' => '/services/#management' ),
	'Contact Us' => array( 'Contact Form' => '/contact-us/#contact-form', 'Our Offices' => '/contact-us/#offices' ),
);
$gm_heads = array( 'Home' => '/', 'About Us' => '/about-us/', 'Properties' => '/properties/', 'Services' => '/services/', 'Contact Us' => '/contact-us/' );
$gm_soc   = array( 'facebook' => 'Facebook', 'linkedin' => 'LinkedIn', 'twitter' => 'Twitter', 'youtube' => 'YouTube' );
?>
</main>

<section class="cta-band" aria-labelledby="cta-title">
	<div class="container cta-band__inner">
		<div>
			<h2 class="section-title" id="cta-title"><?php echo esc_html( gm_opt( 'cta_title' ) ); ?></h2>
			<p class="section-text"><?php echo esc_html( gm_opt( 'cta_text' ) ); ?></p>
		</div>
		<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/properties/' ) ); ?>"><?php echo esc_html( gm_opt( 'cta_button' ) ); ?></a>
	</div>
</section>

<footer class="site-footer">
	<div class="container site-footer__grid">
		<div class="site-footer__brand">
			<a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"><?php echo gm_logo(); ?></a>
			<?php gm_form_open( 'newsletter', 'newsletter-form', 'newsletter' ); ?>
				<label class="screen-reader-text" for="newsletter-form-email"><?php esc_html_e( 'Enter Your Email', 'growmodo' ); ?></label>
				<span class="newsletter__icon"><?php echo gm_icon( 'mail', 22 ); ?></span>
				<input type="email" id="newsletter-form-email" name="email" placeholder="<?php esc_attr_e( 'Enter Your Email', 'growmodo' ); ?>" required autocomplete="email">
				<button type="submit" class="newsletter__btn" aria-label="<?php esc_attr_e( 'Subscribe', 'growmodo' ); ?>"><?php echo gm_icon( 'send', 22 ); ?></button>
			</form>
		</div>

		<nav class="site-footer__cols" aria-label="<?php esc_attr_e( 'Footer', 'growmodo' ); ?>">
			<?php foreach ( $gm_cols as $head => $links ) : ?>
				<div class="fcol">
					<h2 class="fcol__title"><a href="<?php echo esc_url( home_url( $gm_heads[ $head ] ) ); ?>"><?php echo esc_html( $head ); ?></a></h2>
					<ul>
						<?php foreach ( $links as $label => $path ) : ?>
							<li><a href="<?php echo esc_url( home_url( $path ) ); ?>"><?php echo esc_html( $label ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>
		</nav>
	</div>

	<div class="site-footer__bar">
		<div class="container site-footer__bar-inner">
			<p><?php echo esc_html( gm_opt( 'copyright' ) ); ?> <a href="<?php echo esc_url( home_url( '/terms-conditions/' ) ); ?>"><?php esc_html_e( 'Terms & Conditions', 'growmodo' ); ?></a></p>
			<ul class="socials">
				<?php foreach ( $gm_soc as $k => $label ) : ?>
					<li><a href="<?php echo esc_url( gm_opt( $k ) ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $label ); ?>"><?php echo gm_icon( $k, 22 ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</footer>

<button type="button" class="to-top" data-to-top aria-label="<?php esc_attr_e( 'Back to top', 'growmodo' ); ?>" hidden><?php echo gm_icon( 'arrow-ur', 22 ); ?></button>

<?php wp_footer(); ?>
</body>
</html>
