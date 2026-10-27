<?php

defined( 'ABSPATH' ) || exit;

function gm_settings_map() {
	return array(
		'Announcement banner'  => array(
			'banner_text'      => 'Banner text',
			'banner_link_text' => 'Banner link label',
			'banner_link'      => 'Banner link URL or path',
		),
		'Home hero'            => array(
			'hero_title'  => 'Hero title',
			'hero_text'   => 'Hero text',
			'stat1_value' => 'Stat 1 value',
			'stat1_label' => 'Stat 1 label',
			'stat2_value' => 'Stat 2 value',
			'stat2_label' => 'Stat 2 label',
			'stat3_value' => 'Stat 3 value',
			'stat3_label' => 'Stat 3 label',
		),
		'Call-to-action band'  => array(
			'cta_title'  => 'Title',
			'cta_text'   => 'Text',
			'cta_button' => 'Button label',
		),
		'Contact details'      => array(
			'email'        => 'Public email',
			'phone'        => 'Phone',
			'address'      => 'Address label',
			'notify_email' => 'Send form submissions to (defaults to the admin email)',
		),
		'Social links'         => array(
			'facebook'  => 'Facebook URL',
			'linkedin'  => 'LinkedIn URL',
			'twitter'   => 'Twitter / X URL',
			'youtube'   => 'YouTube URL',
			'instagram' => 'Instagram URL',
		),
		'Footer'               => array( 'copyright' => 'Copyright line' ),
	);
}

add_action(
	'admin_menu',
	function () {
		add_menu_page( 'Growmodo Options', 'Growmodo', 'manage_options', 'gm-options', 'gm_render_options', 'dashicons-admin-customizer', 3 );
		add_submenu_page( 'gm-options', 'Site Options', 'Site Options', 'manage_options', 'gm-options', 'gm_render_options' );
	}
);

add_action(
	'admin_init',
	function () {
		register_setting(
			'gm_options_group',
			'gm_options',
			array(
				'sanitize_callback' => function ( $in ) {
					$out = array();
					foreach ( gm_settings_map() as $fields ) {
						foreach ( $fields as $key => $label ) {
							$v = isset( $in[ $key ] ) ? wp_unslash( $in[ $key ] ) : '';
							if ( in_array( $key, array( 'email', 'notify_email' ), true ) ) {
								$out[ $key ] = sanitize_email( $v );
							} elseif ( in_array( $key, array( 'facebook', 'linkedin', 'twitter', 'youtube', 'instagram' ), true ) ) {
								$out[ $key ] = esc_url_raw( $v );
							} elseif ( in_array( $key, array( 'hero_text', 'cta_text' ), true ) ) {
								$out[ $key ] = sanitize_textarea_field( $v );
							} else {
								$out[ $key ] = sanitize_text_field( $v );
							}
						}
					}
					return $out;
				},
			)
		);
	}
);

function gm_render_options() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$saved = (array) get_option( 'gm_options', array() );
	$def   = gm_option_defaults();
	echo '<div class="wrap"><h1>Growmodo – Site Options</h1><p>Leave a field empty to use the built-in default shown as placeholder.</p><form method="post" action="options.php">';
	settings_fields( 'gm_options_group' );
	foreach ( gm_settings_map() as $section => $fields ) {
		echo '<h2>' . esc_html( $section ) . '</h2><table class="form-table" role="presentation">';
		foreach ( $fields as $key => $label ) {
			$val = $saved[ $key ] ?? '';
			echo '<tr><th scope="row"><label for="gm_o_' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label></th><td>';
			if ( in_array( $key, array( 'hero_text', 'cta_text' ), true ) ) {
				printf( '<textarea id="gm_o_%1$s" name="gm_options[%1$s]" rows="4" class="large-text" placeholder="%3$s">%2$s</textarea>', esc_attr( $key ), esc_textarea( $val ), esc_attr( $def[ $key ] ?? '' ) );
			} else {
				printf( '<input type="text" id="gm_o_%1$s" name="gm_options[%1$s]" value="%2$s" class="large-text" placeholder="%3$s">', esc_attr( $key ), esc_attr( $val ), esc_attr( $def[ $key ] ?? '' ) );
			}
			echo '</td></tr>';
		}
		echo '</table>';
	}
	submit_button();
	echo '</form></div>';
}
