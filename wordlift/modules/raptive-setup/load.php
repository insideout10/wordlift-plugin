<?php
/**
 * Module Name: Raptive Setup
 * Experimental: No
 *
 * @since   3.47.0
 * @package wordlift
 */

// Exit if this is not a Raptive install.
if ( ! defined( 'WL_RAPTIVE' ) || ! WL_RAPTIVE ) {
	return;
}

function __wl_raptive_admin_setup() {

	wp_print_scripts( WL_ANGULAR_APP_SCRIPT_HANDLE );
	$iframe_src = WL_ANGULAR_APP_URL . '#/raptive/setup/welcome';
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo "
			<style>
			    #wlx-plugin-app {
					border: 0;
					position: fixed;
					top: 0;
					left: 0;
					right: 0;
					bottom: 0;
					width: 100%;
					height: 100%;
			    }
		    </style>
			<iframe id='wlx-plugin-app' src='$iframe_src'></iframe>
            ";

	exit;
}

add_action( 'wl_admin_setup__pre', '__wl_raptive_admin_setup' );
