<?php


defined( 'ABSPATH' ) || exit;


function gm_meta_fields() {
	return array(
		'property'    => array(
			'title'  => 'Property Details',
			'fields' => array(
				array( 'tagline', 'Category heading (Properties page)', 'text', 'Coastal Escapes - Where Waves Beckon' ),
				array( 'location_text', 'Location (shown under the title)', 'text', 'Malibu, California' ),
				array( 'price', 'Price (USD, numbers only)', 'number', '1250000' ),
				array( 'bedrooms', 'Bedrooms', 'number', '4' ),
				array( 'bathrooms', 'Bathrooms', 'number', '3' ),
				array( 'area', 'Area (sq ft)', 'number', '2500' ),
				array( 'year', 'Build year', 'number', '2020' ),
				array( 'gallery', 'Gallery images', 'gallery' ),
				array( 'features', 'Key features & amenities (one per line)', 'textarea' ),
				array( 'fees', 'Additional fees – one per line: Label | Amount | Note', 'textarea', 'Property Transfer Tax | $25,000 | Based on the sale price and local regulations' ),
				array( 'monthly_costs', 'Monthly costs – one per line: Label | Amount | Note', 'textarea', 'Property Taxes | $1,250 | Approximate monthly property tax' ),
				array( 'monthly_expenses', 'Monthly expenses – one per line: Label | Amount | Note', 'textarea' ),
				array( 'down_pct', 'Down payment (%)', 'number', '20' ),
			),
		),
		'testimonial' => array(
			'title'  => 'Client',
			'fields' => array(
				array( 'person_name', 'Name', 'text' ),
				array( 'person_location', 'Location', 'text', 'USA, California' ),
				array( 'rating', 'Rating (1-5)', 'number', '5' ),
			),
		),
		'team_member' => array(
			'title'  => 'Team Member',
			'fields' => array(
				array( 'role', 'Role', 'text', 'Founder' ),
				array( 'twitter', 'Twitter / X URL', 'url' ),
			),
		),
		'client'      => array(
			'title'  => 'Client Details',
			'fields' => array(
				array( 'since', 'Client since (year)', 'text', '2019' ),
				array( 'domain', 'Domain', 'text', 'Commercial Real Estate' ),
				array( 'category', 'Category', 'text', 'Luxury Home Development' ),
				array( 'url', 'Website URL', 'url' ),
			),
		),
		'service'     => array(
			'title'  => 'Service',
			'fields' => array(
				array( 'icon', 'Icon', 'select', '', gm_icon_choices() ),
			),
		),
		'about_item'  => array(
			'title'  => 'About Item',
			'fields' => array(
				array( 'icon', 'Icon (values / achievements)', 'select', '', gm_icon_choices() ),
				array( 'step_label', 'Step label (steps only)', 'text', 'Step 01' ),
			),
		),
		'office'      => array(
			'title'  => 'Office Details',
			'fields' => array(
				array( 'address', 'Address', 'text', '123 Estatein Plaza, City Center, Metropolis' ),
				array( 'email', 'Email', 'email' ),
				array( 'phone', 'Phone', 'text' ),
				array( 'city', 'City label', 'text', 'Metropolis' ),
				array( 'map_url', 'Directions URL', 'url', 'https://maps.google.com/' ),
			),
		),
	);
}

add_action(
	'add_meta_boxes',
	function () {
		foreach ( gm_meta_fields() as $type => $group ) {
			add_meta_box( 'gm_' . $type, $group['title'], 'gm_render_meta_box', $type, 'normal', 'high' );
		}
		add_meta_box( 'gm_inquiry', 'Submission details', 'gm_render_inquiry_box', 'inquiry', 'normal', 'high' );
	}
);


function gm_render_meta_box( $post, $box ) {
	$type   = $post->post_type;
	$fields = gm_meta_fields()[ $type ]['fields'];
	wp_nonce_field( 'gm_meta_' . $type, 'gm_meta_nonce' );
	echo '<table class="form-table gm-meta" role="presentation">';
	foreach ( $fields as $f ) {
		list( $key, $label, $kind ) = $f;
		$ph      = $f[3] ?? '';
		$val     = get_post_meta( $post->ID, '_gm_' . $key, true );
		$id      = 'gm_f_' . $key;
		echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label></th><td>';
		switch ( $kind ) {
			case 'textarea':
				printf( '<textarea id="%s" name="gm_f[%s]" rows="5" class="large-text" placeholder="%s">%s</textarea>', esc_attr( $id ), esc_attr( $key ), esc_attr( $ph ), esc_textarea( $val ) );
				break;
			case 'select':
				printf( '<select id="%s" name="gm_f[%s]"><option value="">—</option>', esc_attr( $id ), esc_attr( $key ) );
				foreach ( $f[4] as $v => $l ) {
					printf( '<option value="%s"%s>%s</option>', esc_attr( $v ), selected( $val, $v, false ), esc_html( $l ) );
				}
				echo '</select>';
				break;
			case 'gallery':
				$ids = array_filter( array_map( 'absint', explode( ',', (string) $val ) ) );
				printf( '<div class="gm-gallery" data-target="%s"><ul class="gm-gallery__list">', esc_attr( $id ) );
				foreach ( $ids as $aid ) {
					printf( '<li data-id="%1$d">%2$s<button type="button" class="gm-gallery__remove" aria-label="Remove">&times;</button></li>', $aid, wp_get_attachment_image( $aid, 'thumbnail' ) ); 
				}
				printf( '</ul><input type="hidden" id="%s" name="gm_f[%s]" value="%s"><button type="button" class="button gm-gallery__add">Select images</button> <span class="description">Drag to reorder. The first two images are shown on load.</span></div>', esc_attr( $id ), esc_attr( $key ), esc_attr( implode( ',', $ids ) ) );
				break;
			default:
				printf( '<input type="%s" id="%s" name="gm_f[%s]" value="%s" class="regular-text" placeholder="%s"%s>', esc_attr( $kind ), esc_attr( $id ), esc_attr( $key ), esc_attr( $val ), esc_attr( $ph ), 'number' === $kind ? ' step="any" min="0"' : '' );
		}
		echo '</td></tr>';
	}
	echo '</table>';
}


function gm_render_inquiry_box( $post ) {
	$meta = get_post_meta( $post->ID );
	echo '<table class="widefat striped"><tbody>';
	foreach ( $meta as $k => $v ) {
		if ( 0 !== strpos( $k, '_gm_' ) ) {
			continue;
		}
		printf( '<tr><th style="width:200px">%s</th><td>%s</td></tr>', esc_html( ucwords( str_replace( '_', ' ', substr( $k, 4 ) ) ) ), esc_html( $v[0] ) );
	}
	echo '</tbody></table>';
}


add_action(
	'save_post',
	function ( $post_id, $post ) {
		$groups = gm_meta_fields();
		if ( ! isset( $groups[ $post->post_type ], $_POST['gm_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['gm_meta_nonce'] ), 'gm_meta_' . $post->post_type ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$in = isset( $_POST['gm_f'] ) ? (array) wp_unslash( $_POST['gm_f'] ) : array(); 
		foreach ( $groups[ $post->post_type ]['fields'] as $f ) {
			list( $key, , $kind ) = $f;
			$raw = $in[ $key ] ?? '';
			switch ( $kind ) {
				case 'textarea':
					$val = sanitize_textarea_field( $raw );
					break;
				case 'url':
					$val = esc_url_raw( $raw );
					break;
				case 'email':
					$val = sanitize_email( $raw );
					break;
				case 'number':
					$val = '' === $raw ? '' : (string) floatval( $raw );
					break;
				case 'gallery':
					$val = implode( ',', array_filter( array_map( 'absint', explode( ',', $raw ) ) ) );
					break;
				case 'select':
					$val = array_key_exists( $raw, $f[4] ) ? $raw : '';
					break;
				default:
					$val = sanitize_text_field( $raw );
			}
			update_post_meta( $post_id, '_gm_' . $key, $val );
		}
	},
	10,
	2
);


add_action(
	'pre_get_posts',
	function ( $q ) {
		if ( is_admin() && $q->is_main_query() && ! $q->get( 'orderby' ) && in_array( $q->get( 'post_type' ), array( 'faq', 'team_member', 'client', 'service', 'about_item', 'office' ), true ) ) {
			$q->set( 'orderby', 'menu_order title' );
			$q->set( 'order', 'ASC' );
		}
	}
);
