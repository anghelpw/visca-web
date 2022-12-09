<?php
/*
Plugin Name: Sports Book
Description: Show on the front-end the selected *sportsbook*
Author: Dan Anghel
Version: 1.0
License: GPL v3 or later
Text Domain: sportsbook
Domain Path: /languages
*/

defined( 'ABSPATH' ) || exit;

/* helper functions */

if ( ! function_exists('dd') ) {
	function dd($var, $terminate = true) {
		if ( is_bool( $var ) ) {
			$var = 'bool(' . ( $var ? 'true' : 'false' ) . ')';
		}

		highlight_string(
			var_export( $var, true )
		);

		if ( $terminate ) {
			die();
		}
	}
}

if ( ! function_exists('file_get_contents_curl') ) {
	function file_get_contents_curl($url) {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
		curl_setopt($ch, CURLOPT_URL, $url);

		if ( !is_ssl() ) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		}

		$data = curl_exec($ch);
		curl_close($ch);

		return $data;
	}
}

/* plugin hooks */

if ( ! defined( 'SPORTSBOOK_VERSION' ) ) {
	define( 'SPORTSBOOK_VERSION', '1.0' );
}

function sportsbook_load_textdomain() {
	load_plugin_textdomain( 'sportsbook', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'init', 'sportsbook_load_textdomain' ); 

function sportsbook_admin_scripts() {
	wp_register_style( 'sportsbook-admin', plugin_dir_url( __FILE__ ) . 'assets/admin.css', array(), SPORTSBOOK_VERSION );
}
add_action( 'admin_enqueue_scripts', 'sportsbook_admin_scripts', 100 );

/* meta box */

if ( ! defined( 'SPORTSBOOK_JSON_URL' ) ) {
	define( 'SPORTSBOOK_JSON_URL', 'http://www.viscaweb.com/developers/test-front-end/pages/step2-sportsbooks.json' );
}

function sportsbook_add_box() {
	$screens = [ 'post', 'page' ];

	foreach ( $screens as $screen ) {
		add_meta_box(
			'sportsbook_select',
			__( 'Sports Book', 'sportsbook' ),
			'sportsbook_box_html',
			$screen,
			'side'
		);
	}
}
add_action( 'add_meta_boxes', 'sportsbook_add_box' );

function sportsbook_list() {
	$contents = file_get_contents_curl(
		SPORTSBOOK_JSON_URL
	);

	$result = json_decode( $contents );

	if ( json_last_error() !== JSON_ERROR_NONE ) {
		return;
	}

	// var_dump( $result );

	return $result;
}

function sportsbook_is_block_editor() {
	$current_screen = get_current_screen();
	return method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor();
}

function sportsbook_box_html( $post ) {
	$items = sportsbook_list();

	if ( empty($items) ) {
		return;
	}

	if ( ! empty($items) ) {
		$value = get_post_meta( $post->ID, '_sportsbook_meta_key', true );

		?>
			<div class="sportsbook_box_html">
				<select name="sportsbook_field" id="sportsbook_field" class="postbox">
					<option value=""><?= __( 'None', 'sportsbook' ) ?></option>
					<?php foreach ( $items as $k => $v ) : ?>
						<option value="<?= $k ?>" <?php selected( $value, $k ) ?> class="item"><?= $v ?></option>
					<?php endforeach ?>
				</select>
			</div>
		<?php

		if ( sportsbook_is_block_editor() ) {
			wp_enqueue_style( 'sportsbook-admin' );
		}
	}
}

function sportsbook_save_postdata( $post_id ) {
	if ( array_key_exists( 'sportsbook_field', $_POST ) ) {
		update_post_meta(
			$post_id,
			'_sportsbook_meta_key',
			$_POST['sportsbook_field']
		);
	}
}
add_action( 'save_post', 'sportsbook_save_postdata' );

function sportsbook_custom_content( $content ) {
	global $post;

	$items = sportsbook_list();
	$sportsbook = get_post_meta( $post->ID, '_sportsbook_meta_key', true );

	if ( empty( $items->{$sportsbook} ) ) {
		return $content;
	}

	$out  = sprintf( __( '<h4>You choose the sportsbook: <u>%s</u></h4>', 'sportsbook' ), mb_strtoupper( $items->{$sportsbook} ) );
	$out .= $content;

	return  $out;
}
add_filter( 'the_content', 'sportsbook_custom_content' );
