<?php

defined( 'ABSPATH' ) || exit;

$gm_tiles = array(
	array( 'Find Your Dream Home', 'store', '/properties/' ),
	array( 'Unlock Property Value', 'money', '/services/#selling' ),
	array( 'Effortless Property Management', 'office', '/services/#management' ),
	array( 'Smart Investments, Informed Decisions', 'sun', '/services/#investment' ),
);
?>
<section class="tiles" id="features" aria-label="<?php esc_attr_e( 'Our services', 'growmodo' ); ?>">
	<ul class="container tiles__grid">
		<?php foreach ( $gm_tiles as $t ) : ?>
			<li>
				<a class="tile" href="<?php echo esc_url( home_url( $t[2] ) ); ?>">
					<span class="tile__arrow"><?php echo gm_icon( 'arrow-ur', 28 ); ?></span>
					<span class="icon-ring"><?php echo gm_icon( $t[1], 28 ); ?></span>
					<span class="tile__label"><?php echo esc_html( $t[0] ); ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
