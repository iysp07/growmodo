<?php

defined( 'ABSPATH' ) || exit;

define( 'GM_VERSION', '1.0.0' );
define( 'GM_DIR', get_template_directory() );
define( 'GM_URI', get_template_directory_uri() );

foreach ( array( 'helpers', 'setup', 'post-types', 'meta-boxes', 'settings', 'forms', 'seed' ) as $gm_file ) {
	require_once GM_DIR . '/inc/' . $gm_file . '.php';
}
unset( $gm_file );
