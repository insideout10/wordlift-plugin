<?php

/**
 * Add WordLift custom styles to the TinyMCE editor.
 *
 * @param $mce_css The existing comma-separated list of styles.
 *
 * @return The updated list of styles, including the custom style provided by WordLift.
 */
function wordlift_mce_css( $mce_css ) {
	if ( ! empty( $mce_css ) ) {
		$mce_css .= ',';
	}

	$mce_css .= plugin_dir_url( __FILE__ ) . 'css/wordlift-reloaded.min.css';

	return $mce_css;
}

// hook the TinyMCE custom styles function.
add_filter( 'mce_css', 'wordlift_mce_css' );

/**
 * Set TinyMCE options, in particular enable microdata tagging.
 *
 * @param $options
 *
 * @return mixed
 */
function wordlift_filter_tiny_mce_before_init( $options ) {

	if ( ! isset( $options['extended_valid_elements'] ) ) {
		$options['extended_valid_elements'] = '';
	}

	$options['extended_valid_elements'] .= ",span[*]";

	return $options;
}

add_filter( 'tiny_mce_before_init', 'wordlift_filter_tiny_mce_before_init' );
