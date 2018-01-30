<?php

add_action( 'wp_ajax_nopriv_add_keyword', 'add_keyword' );
add_action( 'wp_ajax_add_keyword', 'add_keyword' );
function add_keyword() {

	wp_send_json_success();
}

add_action( 'wp_ajax_nopriv_load_keywords', 'load_keywords' );
add_action( 'wp_ajax_load_keywords', 'load_keywords' );
function load_keywords() {
	$response = array(
		array(
			'keyword' => 'WordLift',
			'trend'   => 'POSITIVE',
			'rank'    => 1,
			'volume'  => 100,
			'urls'    => array(
				'http://example.org/2',
				'http://example.org/N',
			),
		),
		array(
			'keyword' => 'WordLift1',
			'trend'   => 'NEGATIVE',
			'rank'    => 5,
			'volume'  => 115,
			'urls'    => array(
				'http://example.org/',
			),
		),
		array(
			'keyword' => 'WordLift3',
			'trend'   => 'POSITIVE',
			'rank'    => 14,
			'volume'  => 1040,
			'urls'    => array(
				'http://example.org/2',
				'http://example.org/N',
			),
		),
		array(
			'keyword' => 'WordLift4',
			'trend'   => 'NEGATIVE',
			'rank'    => 11,
			'volume'  => 15,
			'urls'    => array(
				'http://example.org/',
			),
		),
		array(
			'keyword' => 'WordLift5',
			'trend'   => 'POSITIVE',
			'rank'    => 12,
			'volume'  => 25,
			'urls'    => array(
				'http://example.org/N',
			),
		),
		array(
			'keyword' => 'WordLift6',
			'trend'   => 'NEGATIVE',
			'rank'    => 55,
			'volume'  => 112,
			'urls'    => array(
				'http://example.org/',
			),
		),
	);

	wp_send_json_success( $response );
}

add_filter( 'allowed_http_origins', 'add_allowed_origins' );
function add_allowed_origins( $origins ) {
	$origins[] = 'http://localhost';
	$origins[] = 'http://localhost:3000';

	return $origins;
}