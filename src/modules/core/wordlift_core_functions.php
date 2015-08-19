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
