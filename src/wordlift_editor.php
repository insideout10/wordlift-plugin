<?php

/**
 * Add WordLift custom styles to the TinyMCE editor.
 * @param $mce_css The existing comma-separated list of styles.
 * @return The updated list of styles, including the custom style provided by WordLift.
 */
function wordlift_mce_css( $mce_css ) {
    if ( ! empty( $mce_css ) )
        $mce_css .= ',';

    $mce_css .= wordlift_get_url('/css/wordlift-editor.min.css');

    return $mce_css;
}

// hook the TinyMCE custom styles function.
add_filter('mce_css', 'wordlift_mce_css');
