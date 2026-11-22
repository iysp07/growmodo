<?php

defined( 'ABSPATH' ) || exit;

function gm_recaptcha_ready() {
	return defined( 'GM_RECAPTCHA_SITE_KEY' ) && defined( 'GM_RECAPTCHA_SECRET_KEY' ) && GM_RECAPTCHA_SITE_KEY && GM_RECAPTCHA_SECRET_KEY;
}

function gm_recaptcha_login_assets() {
	if ( ! gm_recaptcha_ready() ) {
		return;
	}
	$key = rawurlencode( GM_RECAPTCHA_SITE_KEY );
	wp_enqueue_script( 'gm-recaptcha', 'https://www.google.com/recaptcha/api.js?render=' . $key, array(), null, true );
	wp_add_inline_script(
		'gm-recaptcha',
		'document.addEventListener("DOMContentLoaded",function(){var f=document.getElementById("loginform");var t=document.getElementById("gm_recaptcha_token");if(!f||!t){return;}var busy=false;f.addEventListener("submit",function(e){if(t.value||busy){return;}e.preventDefault();busy=true;grecaptcha.ready(function(){grecaptcha.execute(' . wp_json_encode( GM_RECAPTCHA_SITE_KEY ) . ',{action:"login"}).then(function(v){t.value=v;HTMLFormElement.prototype.submit.call(f);},function(){busy=false;HTMLFormElement.prototype.submit.call(f);});});});});'
	);
}
add_action( 'login_enqueue_scripts', 'gm_recaptcha_login_assets' );

function gm_recaptcha_login_field() {
	if ( gm_recaptcha_ready() ) {
		echo '<input type="hidden" name="gm_recaptcha_token" id="gm_recaptcha_token" value="">';
	}
}
add_action( 'login_form', 'gm_recaptcha_login_field' );

function gm_recaptcha_verify_login( $user ) {
	if ( ! gm_recaptcha_ready() || 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) || 'wp-login.php' !== ( $GLOBALS['pagenow'] ?? '' ) ) {
		return $user;
	}
	if ( ! isset( $_POST['log'] ) ) {
		return $user;
	}

	$token = isset( $_POST['gm_recaptcha_token'] ) ? sanitize_text_field( wp_unslash( $_POST['gm_recaptcha_token'] ) ) : '';
	$fail  = new WP_Error( 'gm_recaptcha', '<strong>Error:</strong> Security check failed. Please reload the page and try again.' );
	if ( '' === $token ) {
		return $fail;
	}

	$res = wp_remote_post(
		'https://www.google.com/recaptcha/api/siteverify',
		array(
			'timeout' => 10,
			'body'    => array(
				'secret'   => GM_RECAPTCHA_SECRET_KEY,
				'response' => $token,
				'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
			),
		)
	);
	if ( is_wp_error( $res ) ) {
		return $fail;
	}

	$body = json_decode( wp_remote_retrieve_body( $res ), true );
	if ( empty( $body['success'] ) || ( isset( $body['action'] ) && 'login' !== $body['action'] ) || ( $body['score'] ?? 0 ) < 0.5 ) {
		return $fail;
	}

	return $user;
}
add_filter( 'authenticate', 'gm_recaptcha_verify_login', 5 );
