<?php

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'growmodo', GM_DIR . '/languages' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
		add_theme_support( 'responsive-embeds' );
		add_image_size( 'gm-card', 700, 520, true );
		add_image_size( 'gm-gallery', 1200, 960, true );
		add_image_size( 'gm-thumb', 240, 160, true );
		register_nav_menus( array( 'primary' => __( 'Primary Menu', 'growmodo' ) ) );
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_style( 'gm-fonts', 'https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600&display=swap', array(), null );
		wp_enqueue_style( 'gm-main', GM_URI . "/assets/css/main{$min}.css", array(), GM_VERSION );
		wp_enqueue_script( 'gm-main', GM_URI . "/assets/js/main{$min}.js", array(), GM_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );
		wp_localize_script(
			'gm-main',
			'gmData',
			array(
				'ajax'    => admin_url( 'admin-ajax.php' ),
				'sending' => __( 'Sending…', 'growmodo' ),
			)
		);
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'global-styles' );
	},
	20
);

add_filter(
	'wp_resource_hints',
	function ( $urls, $relation ) {
		if ( 'preconnect' === $relation ) {
			$urls[] = 'https://fonts.googleapis.com';
			$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' => 'anonymous' );
		}
		return $urls;
	},
	10,
	2
);

add_action(
	'admin_enqueue_scripts',
	function ( $hook ) {
		if ( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			wp_enqueue_media();
			wp_enqueue_script( 'gm-admin', GM_URI . '/assets/js/admin.js', array( 'jquery', 'jquery-ui-sortable' ), GM_VERSION, true );
			wp_enqueue_style( 'gm-admin', GM_URI . '/assets/css/admin.css', array(), GM_VERSION );
		}
	}
);

add_action(
	'init',
	function () {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'rsd_link' );
	}
);

add_filter(
	'body_class',
	function ( $classes ) {
		if ( is_page() ) {
			$classes[] = 'page-' . get_post_field( 'post_name', get_queried_object_id() );
		}
		return $classes;
	}
);

function gm_primary_nav() {
	$items = array();
	if ( has_nav_menu( 'primary' ) ) {
		$locations = get_nav_menu_locations();
		$menu      = wp_get_nav_menu_items( $locations['primary'] );
		foreach ( (array) $menu as $m ) {
			if ( ! $m->menu_item_parent ) {
				$items[] = array( 'title' => $m->title, 'url' => $m->url, 'current' => in_array( 'current-menu-item', $m->classes, true ) || (int) $m->object_id === get_queried_object_id() || ( is_singular( 'property' ) && 'properties' === basename( untrailingslashit( $m->url ) ) ) );
			}
		}
	}
	if ( ! $items ) {
		foreach ( array( 'Home' => '/', 'About Us' => '/about-us/', 'Properties' => '/properties/', 'Services' => '/services/', 'Contact Us' => '/contact-us/' ) as $t => $u ) {
			$items[] = array( 'title' => $t, 'url' => home_url( $u ), 'current' => false );
		}
	}
	return $items;
}

add_action(
	'wp_head',
	function () {
		if ( is_singular() ) {
			$desc = has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( get_the_content() ), 30 );
		} else {
			$desc = get_bloginfo( 'description' );
		}
		if ( is_front_page() ) {
			$desc = gm_opt( 'hero_text' );
		}
		$desc  = trim( wp_strip_all_tags( $desc ) );
		$title = wp_get_document_title();
		$image = is_singular() && has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'large' ) : GM_URI . '/assets/images/hero-building.webp';
		if ( $desc ) {
			echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
			echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
		}
		echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
		echo '<meta property="og:type" content="' . ( is_singular( 'property' ) ? 'product' : 'website' ) . '">' . "\n";
		echo '<meta property="og:url" content="' . esc_url( home_url( add_query_arg( null, null ) ) ) . '">' . "\n";
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
		echo '<meta name="theme-color" content="#141414">' . "\n";

		$schema = array(
			'@context' => 'https://schema.org',
			'@type'    => 'RealEstateAgent',
			'name'     => get_bloginfo( 'name' ),
			'url'      => home_url( '/' ),
			'email'    => gm_opt( 'email' ),
			'telephone'=> gm_opt( 'phone' ),
		);
		if ( is_singular( 'property' ) ) {
			$schema = array(
				'@context'    => 'https://schema.org',
				'@type'       => 'Product',
				'name'        => get_the_title(),
				'description' => $desc,
				'image'       => $image,
				'offers'      => array( '@type' => 'Offer', 'priceCurrency' => 'USD', 'price' => (float) gm_meta( 'price' ), 'availability' => 'https://schema.org/InStock' ),
			);
		}
		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
	},
	5
);

add_filter( 'excerpt_length', fn() => 22 );
add_filter( 'excerpt_more', fn() => '…' );

add_action(
	'after_switch_theme',
	function () {
		update_option( 'gm_flush_rewrite', 1 );
	}
);
add_action(
	'init',
	function () {
		if ( get_option( 'gm_flush_rewrite' ) ) {
			flush_rewrite_rules( false );
			delete_option( 'gm_flush_rewrite' );
		}
	},
	99
);
