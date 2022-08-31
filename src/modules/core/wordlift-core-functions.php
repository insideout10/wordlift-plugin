<?php
/**
 * Wordlift wrapper for wl_send_json function
 *
 * @see https://codex.wordpress.org/Function_Reference/wp_send_json
 * Ensure output buffer is cleaned
 *
 * @param mixed $response The response to send to the client as JSON.
 */
function wl_core_send_json( $response ) {
	if ( ob_get_contents() ) {
		ob_clean();
	}

	wp_send_json( $response );
}
