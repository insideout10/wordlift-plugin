<?php

/**
 * Add WordLift custom styles to the TinyMCE editor.
 *
 * @param $mce_css The existing comma-separated list of styles.
 *
 * @return string The updated list of styles, including the custom style provided by WordLift.
 */
function wordlift_mce_css( $mce_css ) {

	/*
	 * Call the `wl_can_see_classification_box` filter to determine whether we can display the classification box.
	 *
	 * @since 3.20.3
	 *
	 * @see https://github.com/insideout10/wordlift-plugin/issues/914
	 */
	if ( ! apply_filters( 'wl_can_see_classification_box', true ) ) {
		return $mce_css;
	}

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
