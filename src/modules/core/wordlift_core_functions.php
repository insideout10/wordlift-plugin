<?php
/**
 * This file contain utility functions
 *
 * @package    Wordlift
 */

/**
 * Wordlift wrapper for wl_send_json function
 *
 * @see https://codex.wordpress.org/Function_Reference/wp_send_json
 * Ensure output buffer is cleaned
 *
 * @param mixed $response The response to send to the client as JSON.
 */
function wl_core_send_json( $response ) {
	wl_ob_clean();

	wp_send_json( $response );
}

/**
 * Wordlift wrapper around ob_clean used to avoid notices when buffer is empty
 *
 * @since 3.16.0
 */
function wl_ob_clean() {
	if ( ob_get_contents() ) {
		ob_clean();
	}
}
