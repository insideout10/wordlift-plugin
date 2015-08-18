<?php
/**
 * Wordlift wrapper for wl_send_json function
 * @see https://codex.wordpress.org/Function_Reference/wp_send_json
 * Ensure output buffer is cleaned
 */
function wl_core_send_json( $response ) {
	if ( ob_get_contents() ) {
		ob_clean();
	}

	return wp_send_json( $response );
}

/**
 * Hide the WordLift Key from the provided text.
 *
 * @since 3.0.0
 *
 * @param $text string A text that may potentially contain a WL key.
 *
 * @return string A text with the key hidden.
 */
function wl_core_hide_key( $text ) {

	return str_ireplace( wl_configuration_get_key(), '<hidden>', $text );
}