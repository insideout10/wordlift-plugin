<?php

use Wordlift\Lod_Import\Lod_Import;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
if ( apply_filters( 'wl_feature__enable__lod-import', false ) ) {
	$lod_import = new Lod_Import();
	$lod_import->register_hooks();
}
