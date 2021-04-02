<?php

/**
 * This file provides function that enhance the post edit screen.
 */

/**
 * Add custom buttons to the buttons below the post title.
 *
 * @param string $html The current html.
 * @param int    $post_id The post ID.
 * @param string $new_title Optional. New title.
 * @param string $new_slug Optional. New slug.
 *
 * @since 3.20.1 remove lodview since it's basically offline most of the time.
 *
 * @return string The enhanced html.
 */
function wl_admin_permalink_html( $html, $post_id, $new_title, $new_slug ) {

	// Get the entity service instance.
	$entity_service = Wordlift_Entity_Service::get_instance();

	// Show the View Linked Data button only for entities.
	//
	// See https://github.com/insideout10/wordlift-plugin/issues/668.
	if ( 'publish' === get_post_status( $post_id )
	     && $uri = $entity_service->get_uri( $post_id ) ) {

		$lod_view_href = esc_attr( $uri );
		/*
		 * Add the `.html` extension to the link to have Chrome open the html version instead of RDF one.
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/931
		 * @since 3.21.1
		 */
		$html          .= apply_filters( 'wl_feature__enable__view-linked-data', true ) ? "<span id='view-post-btn'><a href='$lod_view_href.html' class='button button-small wl-button' target='_blank'>" .
		                  esc_html__( 'View Linked Data', 'wordlift' ) .
		                  "</a></span>\n" : '';

	}

	$html .= apply_filters( 'wl_feature__enable__test-sd', true ) ? "<span id='view-post-btn'><a href='" . WL_CONFIG_TEST_GOOGLE_RICH_SNIPPETS_URL .
	         urlencode( get_permalink( $post_id ) ) .
	         "' class='button button-small wl-button' target='_blank'>" .
	         esc_html__( 'Test Google Rich Snippets', 'wordlift' ) .
	         "</a></span>\n" : '';

	return $html;
}

add_filter( 'get_sample_permalink_html', 'wl_admin_permalink_html', PHP_INT_MAX, 4 );
