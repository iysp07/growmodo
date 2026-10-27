<?php


defined( 'ABSPATH' ) || exit;


function gm_form_schema( $form ) {
	$common = array(
		'first_name' => array( 'First Name', 'text', true ),
		'last_name'  => array( 'Last Name', 'text', true ),
		'email'      => array( 'Email', 'email', true ),
		'phone'      => array( 'Phone', 'tel', false ),
	);
	$schemas = array(
		'contact'          => $common + array(
			'inquiry_type' => array( 'Inquiry Type', 'select', false ),
			'heard_from'   => array( 'How Did You Hear About Us?', 'select', false ),
			'message'      => array( 'Message', 'textarea', true ),
		),
		'property_request' => $common + array(
			'location'       => array( 'Preferred Location', 'select', false ),
			'property_type'  => array( 'Property Type', 'select', false ),
			'bathrooms'      => array( 'No. of Bathrooms', 'select', false ),
			'bedrooms'       => array( 'No. of Bedrooms', 'select', false ),
			'budget'         => array( 'Budget', 'select', false ),
			'contact_number' => array( 'Preferred contact: phone', 'tel', false ),
			'contact_email'  => array( 'Preferred contact: email', 'email', false ),
			'message'        => array( 'Message', 'textarea', false ),
		),
		'property_inquiry' => $common + array(
			'property' => array( 'Selected Property', 'text', false ),
			'message'  => array( 'Message', 'textarea', true ),
		),
		'newsletter'       => array( 'email' => array( 'Email', 'email', true ) ),
	);
	return $schemas[ $form ] ?? null;
}


function gm_form_open( $form, $id, $class = 'form' ) {
	$sent  = isset( $_GET['gm_form'], $_GET['gm_status'] ) && $id === sanitize_key( wp_unslash( $_GET['gm_form'] ) ); 
	$state = $sent ? sanitize_key( wp_unslash( $_GET['gm_status'] ) ) : ''; 
	printf( '<form class="%s" id="%s" method="post" action="%s" data-gm-form novalidate>', esc_attr( $class ), esc_attr( $id ), esc_url( admin_url( 'admin-post.php' ) ) );
	echo '<input type="hidden" name="action" value="gm_submit"><input type="hidden" name="gm_form" value="' . esc_attr( $form ) . '"><input type="hidden" name="gm_form_id" value="' . esc_attr( $id ) . '">';
	wp_nonce_field( 'gm_submit_' . $form, 'gm_nonce', false );
	echo '<div class="hp" aria-hidden="true"><label>Leave empty<input type="text" name="gm_website" tabindex="-1" autocomplete="off"></label></div>';
	echo '<div class="form__status" role="status" aria-live="polite">';
	if ( 'ok' === $state ) {
		echo '<p class="notice notice--ok">' . esc_html__( 'Thank you! Your message has been sent. We will be in touch shortly.', 'growmodo' ) . '</p>';
	} elseif ( $state ) {
		echo '<p class="notice notice--error">' . esc_html__( 'Sorry, we could not send your message. Please check the form and try again.', 'growmodo' ) . '</p>';
	}
	echo '</div>';
}


function gm_field( $form_id, $name, $label, $type = 'text', $ph = '', $required = false, $opts = array(), $value = '', $half = true ) {
	$id = $form_id . '-' . $name;
	echo '<div class="field' . ( $half ? '' : ' field--full' ) . '"><label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . ( $required ? ' <span class="req" aria-hidden="true">*</span>' : '' ) . '</label>';
	$req = $required ? ' required aria-required="true"' : '';
	if ( 'textarea' === $type ) {
		printf( '<textarea id="%s" name="%s" rows="5" placeholder="%s"%s></textarea>', esc_attr( $id ), esc_attr( $name ), esc_attr( $ph ), $req ); 
	} elseif ( 'select' === $type ) {
		printf( '<div class="select"><select id="%s" name="%s"%s><option value="">%s</option>', esc_attr( $id ), esc_attr( $name ), $req, esc_html( $ph ) ); 
		foreach ( $opts as $o ) {
			printf( '<option value="%1$s">%1$s</option>', esc_attr( $o ) );
		}
		echo '</select>' . gm_icon( 'chevron', 20 ) . '</div>'; 
	} else {
		printf( '<input type="%s" id="%s" name="%s" placeholder="%s" value="%s"%s%s>', esc_attr( $type ), esc_attr( $id ), esc_attr( $name ), esc_attr( $ph ), esc_attr( $value ), $req, 'email' === $type ? ' autocomplete="email"' : '' ); 
	}
	echo '</div>';
}


function gm_form_close( $id, $btn = 'Send Your Message' ) {
	?>
	<div class="form__foot">
		<label class="check">
			<input type="checkbox" name="consent" value="1" required aria-required="true">
			<span class="check__box" aria-hidden="true"></span>
			<span><?php esc_html_e( 'I agree with', 'growmodo' ); ?> <a href="<?php echo esc_url( home_url( '/terms-conditions/' ) ); ?>"><?php esc_html_e( 'Terms of Use', 'growmodo' ); ?></a> <?php esc_html_e( 'and', 'growmodo' ); ?> <a href="<?php echo esc_url( home_url( '/terms-conditions/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'growmodo' ); ?></a></span>
		</label>
		<button type="submit" class="btn btn--primary"><?php echo esc_html( $btn ); ?></button>
	</div>
	</form>
	<?php
}


function gm_handle_submit() {
	$ajax = wp_doing_ajax();
	$fail = function ( $code, $msg ) use ( $ajax ) {
		if ( $ajax ) {
			wp_send_json_error( array( 'message' => $msg, 'code' => $code ), 400 );
		}
		gm_redirect_back( 'error' );
	};

	$form   = isset( $_POST['gm_form'] ) ? sanitize_key( wp_unslash( $_POST['gm_form'] ) ) : '';
	$schema = gm_form_schema( $form );
	if ( ! $schema ) {
		$fail( 'form', __( 'Unknown form.', 'growmodo' ) );
	}
	if ( ! isset( $_POST['gm_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['gm_nonce'] ) ), 'gm_submit_' . $form ) ) {
		$fail( 'nonce', __( 'Your session expired. Please reload the page and try again.', 'growmodo' ) );
	}
	
	if ( ! empty( $_POST['gm_website'] ) ) {
		$ajax ? wp_send_json_success( array( 'message' => __( 'Thank you!', 'growmodo' ) ) ) : gm_redirect_back( 'ok' );
	}
	
	$key   = 'gm_rl_' . md5( isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'x' );
	$count = (int) get_transient( $key );
	if ( $count >= 5 ) {
		$fail( 'rate', __( 'Too many submissions. Please try again in a few minutes.', 'growmodo' ) );
	}

	$data   = array();
	$errors = array();
	foreach ( $schema as $name => $def ) {
		list( $label, $type, $required ) = $def;
		$raw = isset( $_POST[ $name ] ) ? wp_unslash( $_POST[ $name ] ) : ''; 
		$val = 'textarea' === $type ? sanitize_textarea_field( $raw ) : sanitize_text_field( $raw );
		if ( 'email' === $type ) {
			$val = sanitize_email( $val );
		}
		if ( $required && '' === $val ) {
			
			$errors[ $name ] = sprintf( __( '%s is required.', 'growmodo' ), $label );
		} elseif ( 'email' === $type && '' !== $val && ! is_email( $val ) ) {
			$errors[ $name ] = __( 'Please enter a valid email address.', 'growmodo' );
		}
		$data[ $name ] = $val;
	}
	if ( 'newsletter' !== $form && empty( $_POST['consent'] ) ) {
		$errors['consent'] = __( 'Please accept the terms to continue.', 'growmodo' );
	}
	if ( $errors ) {
		$fail( 'validation', implode( ' ', $errors ) );
	}
	set_transient( $key, $count + 1, 10 * MINUTE_IN_SECONDS );

	$names = array(
		'contact' => 'Contact', 'property_request' => 'Property request', 'property_inquiry' => 'Property inquiry', 'newsletter' => 'Newsletter',
	);
	$who   = trim( ( $data['first_name'] ?? '' ) . ' ' . ( $data['last_name'] ?? '' ) );
	$title = $names[ $form ] . ' – ' . ( $who ? $who : $data['email'] );
	$post  = wp_insert_post(
		array(
			'post_type'    => 'inquiry',
			'post_status'  => 'private',
			'post_title'   => $title,
			'post_content' => $data['message'] ?? '',
		)
	);
	if ( $post && ! is_wp_error( $post ) ) {
		update_post_meta( $post, '_gm_form', $names[ $form ] );
		foreach ( $data as $k => $v ) {
			update_post_meta( $post, '_gm_' . $k, $v );
		}
	}

	$to   = gm_opt( 'notify_email' ) ? gm_opt( 'notify_email' ) : get_option( 'admin_email' );
	$body = '';
	foreach ( $data as $k => $v ) {
		if ( '' !== $v ) {
			$body .= $schema[ $k ][0] . ': ' . $v . "\n";
		}
	}
	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	if ( ! empty( $data['email'] ) ) {
		$headers[] = 'Reply-To: ' . $data['email'];
	}
	wp_mail( $to, '[' . get_bloginfo( 'name' ) . '] ' . $title, $body, $headers );

	if ( $ajax ) {
		wp_send_json_success( array( 'message' => __( 'Thank you! Your message has been sent. We will be in touch shortly.', 'growmodo' ) ) );
	}
	gm_redirect_back( 'ok' );
}
add_action( 'admin_post_nopriv_gm_submit', 'gm_handle_submit' );
add_action( 'admin_post_gm_submit', 'gm_handle_submit' );
add_action( 'wp_ajax_nopriv_gm_submit', 'gm_handle_submit' );
add_action( 'wp_ajax_gm_submit', 'gm_handle_submit' );


function gm_redirect_back( $status ) {
	$back = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$id   = isset( $_POST['gm_form_id'] ) ? sanitize_key( wp_unslash( $_POST['gm_form_id'] ) ) : sanitize_key( wp_unslash( $_POST['gm_form'] ?? '' ) ); 
	$back = remove_query_arg( array( 'gm_status', 'gm_form' ), $back );
	wp_safe_redirect( add_query_arg( array( 'gm_status' => $status, 'gm_form' => $id ), $back ) . '#' . $id );
	exit;
}
