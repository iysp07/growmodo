<?php

defined( 'ABSPATH' ) || exit;

get_header();
the_post();
gm_page_hero();

$gm_offices = get_posts( array( 'post_type' => 'office', 'posts_per_page' => -1, 'orderby' => 'menu_order', 'order' => 'ASC' ) );
$gm_types   = get_terms( array( 'taxonomy' => 'office_type', 'hide_empty' => true ) );
$gm_photos  = array(
	array( 'office-1.webp', 'Open-plan office with desks and monitors', 1200, 463, 'wide' ),
	array( 'office-2.webp', 'Team of smiling real estate agents', 1200, 389, 'wide' ),
	array( 'office-3.webp', 'Colleagues collaborating around a table', 1200, 380, 'wide' ),
	array( 'office-4.webp', 'Group of agents in suits', 1200, 778, '' ),
	array( 'office-5.webp', 'Smiling agents close up', 1200, 800, '' ),
	array( 'office-6.webp', 'Agents shaking hands in a meeting', 1200, 404, 'wide' ),
);
?>

<section class="tiles" aria-label="<?php esc_attr_e( 'Contact details', 'growmodo' ); ?>">
	<ul class="container tiles__grid">
		<li><a class="tile" href="mailto:<?php echo esc_attr( gm_opt( 'email' ) ); ?>"><span class="tile__arrow"><?php echo gm_icon( 'arrow-ur', 28 ); ?></span><span class="icon-ring"><?php echo gm_icon( 'mail', 28 ); ?></span><span class="tile__label"><?php echo esc_html( gm_opt( 'email' ) ); ?></span></a></li>
		<li><a class="tile" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', gm_opt( 'phone' ) ) ); ?>"><span class="tile__arrow"><?php echo gm_icon( 'arrow-ur', 28 ); ?></span><span class="icon-ring"><?php echo gm_icon( 'phone', 28 ); ?></span><span class="tile__label"><?php echo esc_html( gm_opt( 'phone' ) ); ?></span></a></li>
		<li><a class="tile" href="#offices"><span class="tile__arrow"><?php echo gm_icon( 'arrow-ur', 28 ); ?></span><span class="icon-ring"><?php echo gm_icon( 'pin', 28 ); ?></span><span class="tile__label"><?php echo esc_html( gm_opt( 'address' ) ); ?></span></a></li>
		<li class="tile tile--social">
			<span class="tile__arrow"><?php echo gm_icon( 'arrow-ur', 28 ); ?></span>
			<span class="icon-ring"><?php echo gm_icon( 'logo-mark', 28 ); ?></span>
			<span class="tile__label tile__links">
				<a href="<?php echo esc_url( gm_opt( 'instagram' ) ); ?>" target="_blank" rel="noopener noreferrer">Instagram</a>
				<a href="<?php echo esc_url( gm_opt( 'linkedin' ) ); ?>" target="_blank" rel="noopener noreferrer">LinkedIn</a>
				<a href="<?php echo esc_url( gm_opt( 'facebook' ) ); ?>" target="_blank" rel="noopener noreferrer">Facebook</a>
			</span>
		</li>
	</ul>
</section>

<section class="section" id="contact-form" aria-labelledby="connect-title">
	<div class="container">
		<?php gm_section_head( array( 'title' => "Let's Connect", 'id' => 'connect-title', 'text' => "We're excited to connect with you and learn more about your real estate goals. Use the form below to get in touch with Estatein. Whether you're a prospective client, partner, or simply curious about our services, we're here to answer your questions and provide the assistance you need." ) ); ?>
		<div class="card form-card">
			<?php
			gm_form_open( 'contact', 'contact-form-el' );
			echo '<div class="form__grid form__grid--3">';
			gm_field( 'contact-form-el', 'first_name', 'First Name', 'text', 'Enter First Name', true );
			gm_field( 'contact-form-el', 'last_name', 'Last Name', 'text', 'Enter Last Name', true );
			gm_field( 'contact-form-el', 'email', 'Email', 'email', 'Enter your Email', true );
			gm_field( 'contact-form-el', 'phone', 'Phone', 'tel', 'Enter Phone Number' );
			gm_field( 'contact-form-el', 'inquiry_type', 'Inquiry Type', 'select', 'Select Inquiry Type', false, array( 'Buying a property', 'Selling a property', 'Property management', 'Investment advice', 'Other' ) );
			gm_field( 'contact-form-el', 'heard_from', 'How Did You Hear About Us?', 'select', 'Select', false, array( 'Search engine', 'Social media', 'Friend or family', 'Advertisement', 'Other' ) );
			echo '</div><div class="form__grid">';
			gm_field( 'contact-form-el', 'message', 'Message', 'textarea', 'Enter your Message here..', true, array(), '', false );
			echo '</div>';
			gm_form_close( 'contact-form-el' );
			?>
		</div>
	</div>
</section>

<section class="section" id="offices" aria-labelledby="offices-title">
	<div class="container">
		<?php gm_section_head( array( 'title' => 'Discover Our Office Locations', 'id' => 'offices-title', 'text' => 'Estatein is here to serve you across multiple locations. Whether you\'re looking to meet our team, discuss real estate opportunities, or simply drop by for a chat, we have offices conveniently located to serve your needs. Explore the categories below to find the Estatein office nearest to you' ) ); ?>

		<div class="tabs" role="group" aria-label="<?php esc_attr_e( 'Filter offices', 'growmodo' ); ?>" data-office-filter>
			<button type="button" class="tab is-active" data-filter="all" aria-pressed="true">All</button>
			<?php foreach ( (array) $gm_types as $gm_t ) : ?>
				<button type="button" class="tab" data-filter="<?php echo esc_attr( $gm_t->slug ); ?>" aria-pressed="false"><?php echo esc_html( $gm_t->name ); ?></button>
			<?php endforeach; ?>
		</div>

		<div class="grid grid--2 offices">
			<?php
			foreach ( $gm_offices as $gm_o ) :
				$gm_terms = wp_list_pluck( (array) get_the_terms( $gm_o, 'office_type' ), 'slug' );
				?>
				<article class="card office" data-type="<?php echo esc_attr( implode( ' ', $gm_terms ) ); ?>">
					<p class="muted"><?php echo esc_html( $gm_o->post_title ); ?></p>
					<h3><?php echo esc_html( gm_meta( 'address', $gm_o->ID ) ); ?></h3>
					<p class="muted"><?php echo esc_html( wp_strip_all_tags( $gm_o->post_content ) ); ?></p>
					<ul class="chips">
						<li class="chip"><?php echo gm_icon( 'mail', 20 ); ?> <?php echo esc_html( gm_meta( 'email', $gm_o->ID ) ); ?></li>
						<li class="chip"><?php echo gm_icon( 'phone', 20 ); ?> <?php echo esc_html( gm_meta( 'phone', $gm_o->ID ) ); ?></li>
						<li class="chip"><?php echo gm_icon( 'pin', 20 ); ?> <?php echo esc_html( gm_meta( 'city', $gm_o->ID ) ); ?></li>
					</ul>
					<a class="btn btn--primary btn--block" href="<?php echo esc_url( gm_meta( 'map_url', $gm_o->ID ) ?: 'https://maps.google.com/' ); ?>" target="_blank" rel="noopener noreferrer">Get Direction<span class="screen-reader-text"> – <?php echo esc_html( $gm_o->post_title ); ?></span></a>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section" aria-labelledby="world-title">
	<div class="container">
		<div class="card world">
			<ul class="world__grid">
				<?php foreach ( $gm_photos as $p ) : ?>
					<li class="<?php echo esc_attr( $p[4] ); ?>"><?php echo gm_img( $p[0], $p[1], $p[2], $p[3] ); ?></li>
				<?php endforeach; ?>
				<li class="world__text">
					<?php gm_section_head( array( 'title' => "Explore Estatein's World", 'id' => 'world-title', 'text' => 'Step inside the world of Estatein, where professionalism meets warmth, and expertise meets passion. Our gallery offers a glimpse into our team and workspaces, inviting you to get to know us better.' ) ); ?>
				</li>
			</ul>
		</div>
	</div>
</section>

<?php
get_footer();
