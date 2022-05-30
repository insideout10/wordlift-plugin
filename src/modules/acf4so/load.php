<?php
/**
 * Module Name: ACF4SO
 * Description: Manages the lifecycle of the ACF4SO plugin
 * Experimental: Yes
 *
 * @since   3.35.9
 * @package wordlift
 */

//add_action( 'update_plugins_wordlift.io', function ( $update, $plugin_data, $plugin_file, $locales ) {
//	$response = wp_remote_get( 'https://wordlift.io/wp-content/uploads/advanced-custom-fields-for-schema-org/package.json?nocache=1' );
//
//	if ( is_wp_error( $response ) ) {
//		return $update;
//
//	}
//
//	try {
//		return json_decode( wp_remote_retrieve_body( $response ) );
//	} catch ( Exception $e ) {
//		return $update;
//	}
//}, 10, 4 );