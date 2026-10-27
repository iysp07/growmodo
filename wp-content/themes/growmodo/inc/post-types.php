<?php

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		$cpt = function ( $slug, $singular, $plural, $icon, $extra = array() ) {
			register_post_type(
				$slug,
				array_merge(
					array(
						'labels'       => array(
							'name'               => $plural,
							'singular_name'      => $singular,
							'add_new_item'       => "Add New $singular",
							'edit_item'          => "Edit $singular",
							'all_items'          => "All $plural",
							'search_items'       => "Search $plural",
							'not_found'          => "No $plural found.",
							'featured_image'     => 'Photo',
							'set_featured_image' => 'Set photo',
						),
						'public'       => false,
						'show_ui'      => true,
						'show_in_rest' => true,
						'menu_icon'    => $icon,
						'supports'     => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
						'has_archive'  => false,
					),
					$extra
				)
			);
		};

		register_post_type(
			'property',
			array(
				'labels'        => array(
					'name'          => 'Properties',
					'singular_name' => 'Property',
					'add_new_item'  => 'Add New Property',
					'edit_item'     => 'Edit Property',
					'all_items'     => 'All Properties',
					'featured_image'     => 'Cover photo',
					'set_featured_image' => 'Set cover photo',
				),
				'public'        => true,
				'show_in_rest'  => true,
				'menu_icon'     => 'dashicons-admin-home',
				'menu_position' => 5,
				'has_archive'   => false,
				'rewrite'       => array( 'slug' => 'property', 'with_front' => false ),
				'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
			)
		);
		register_taxonomy( 'property_location', 'property', array( 'label' => 'Locations', 'hierarchical' => true, 'show_in_rest' => true, 'show_admin_column' => true, 'rewrite' => false ) );
		register_taxonomy( 'property_type', 'property', array( 'label' => 'Property Types', 'hierarchical' => true, 'show_in_rest' => true, 'show_admin_column' => true, 'rewrite' => false ) );

		$cpt( 'testimonial', 'Testimonial', 'Testimonials', 'dashicons-format-quote', array( 'supports' => array( 'title', 'editor', 'thumbnail' ) ) );
		$cpt( 'faq', 'FAQ', 'FAQs', 'dashicons-editor-help', array( 'supports' => array( 'title', 'editor', 'excerpt', 'page-attributes' ) ) );
		$cpt( 'team_member', 'Team Member', 'Team', 'dashicons-groups', array( 'supports' => array( 'title', 'thumbnail', 'page-attributes' ) ) );
		$cpt( 'client', 'Client', 'Clients', 'dashicons-businessperson', array( 'supports' => array( 'title', 'editor', 'page-attributes' ) ) );
		$cpt( 'service', 'Service', 'Services', 'dashicons-hammer', array( 'supports' => array( 'title', 'excerpt', 'page-attributes' ) ) );
		$cpt( 'about_item', 'About Item', 'About Items', 'dashicons-info-outline', array( 'supports' => array( 'title', 'excerpt', 'page-attributes' ) ) );
		$cpt( 'office', 'Office', 'Offices', 'dashicons-location-alt', array( 'supports' => array( 'title', 'editor', 'page-attributes' ) ) );

		register_taxonomy( 'service_group', 'service', array( 'label' => 'Service Groups', 'hierarchical' => true, 'show_admin_column' => true, 'show_in_rest' => true, 'rewrite' => false ) );
		register_taxonomy( 'about_group', 'about_item', array( 'label' => 'About Groups', 'hierarchical' => true, 'show_admin_column' => true, 'show_in_rest' => true, 'rewrite' => false ) );
		register_taxonomy( 'office_type', 'office', array( 'label' => 'Office Types', 'hierarchical' => true, 'show_admin_column' => true, 'show_in_rest' => true, 'rewrite' => false ) );

		register_post_type(
			'inquiry',
			array(
				'labels'          => array( 'name' => 'Inquiries', 'singular_name' => 'Inquiry', 'all_items' => 'All Inquiries' ),
				'public'          => false,
				'show_ui'         => true,
				'menu_icon'       => 'dashicons-email-alt',
				'menu_position'   => 26,
				'supports'        => array( 'title', 'editor' ),
				'capability_type' => 'post',
				'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'    => true,
			)
		);
	}
);

add_filter(
	'manage_inquiry_posts_columns',
	function ( $cols ) {
		return array( 'cb' => $cols['cb'], 'title' => 'Subject', 'gm_type' => 'Form', 'gm_email' => 'Email', 'date' => $cols['date'] );
	}
);
add_action(
	'manage_inquiry_posts_custom_column',
	function ( $col, $id ) {
		if ( 'gm_type' === $col ) {
			echo esc_html( get_post_meta( $id, '_gm_form', true ) );
		} elseif ( 'gm_email' === $col ) {
			echo esc_html( get_post_meta( $id, '_gm_email', true ) );
		}
	},
	10,
	2
);

foreach ( array( 'service_group' ) as $gm_tax ) {
	$gm_fields = function ( $term = null ) {
		$title = $term ? get_term_meta( $term->term_id, 'cta_title', true ) : '';
		$text  = $term ? get_term_meta( $term->term_id, 'cta_text', true ) : '';
		wp_nonce_field( 'gm_term', 'gm_term_nonce' );
		?>
		<tr class="form-field"><th><label for="gm_cta_title">CTA card title</label></th><td><input type="text" id="gm_cta_title" name="gm_cta_title" value="<?php echo esc_attr( $title ); ?>"></td></tr>
		<tr class="form-field"><th><label for="gm_cta_text">CTA card text</label></th><td><textarea id="gm_cta_text" name="gm_cta_text" rows="3"><?php echo esc_textarea( $text ); ?></textarea></td></tr>
		<?php
	};
	add_action( "{$gm_tax}_edit_form_fields", $gm_fields );
	add_action(
		"saved_{$gm_tax}",
		function ( $term_id ) {
			if ( ! isset( $_POST['gm_term_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['gm_term_nonce'] ), 'gm_term' ) || ! current_user_can( 'manage_categories' ) ) {
				return;
			}
			update_term_meta( $term_id, 'cta_title', sanitize_text_field( wp_unslash( $_POST['gm_cta_title'] ?? '' ) ) );
			update_term_meta( $term_id, 'cta_text', sanitize_textarea_field( wp_unslash( $_POST['gm_cta_text'] ?? '' ) ) );
		}
	);
}
unset( $gm_tax, $gm_fields );
