<?php
/**
 * Add Filter
 * @since 3.34.1
 *
 * @see
 */

use Wordlift\Cleanup\Cleanup_Page;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/*
if ( apply_filters( 'wl_feature__enable__entity-annotation-cleanup', false ) ) {
	// Setup entity annotation cleanup admin page.
	new Cleanup_Page();
}*/
new Cleanup_Page();
