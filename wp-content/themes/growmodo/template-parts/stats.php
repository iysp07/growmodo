<?php

defined( 'ABSPATH' ) || exit;
?>
<ul class="stats">
	<?php for ( $i = 1; $i <= 3; $i++ ) : ?>
		<li class="card stat">
			<strong><?php echo esc_html( gm_opt( "stat{$i}_value" ) ); ?></strong>
			<span><?php echo esc_html( gm_opt( "stat{$i}_label" ) ); ?></span>
		</li>
	<?php endfor; ?>
</ul>
