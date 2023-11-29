<?php

function _wl_override_url__production_site_url( $value ) {
	$override_website_url = Wordlift_Configuration_Service::get_instance()->get_override_website_url();

	return $override_website_url ? $override_website_url : $value;
}

function _wl_override_url__wl_production_permalink( $value ) {
	$override_website_url = Wordlift_Configuration_Service::get_instance()->get_override_website_url();

	if ( $override_website_url === false ) {
		return $value;
	}

	$home_url = untrailingslashit( get_option( 'home' ) );
	$new_url  = untrailingslashit( $override_website_url );

	return $new_url . substr( $value, strlen( $home_url ) );
}

add_filter( 'wl_production_site_url', '_wl_override_url__production_site_url' );

add_filter( 'wl_production_permalink', '_wl_override_url__wl_production_permalink' );
