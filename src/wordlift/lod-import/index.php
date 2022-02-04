<?php

use Wordlift\Lod_Import\Lod_Import;

if ( apply_filters( 'wl_feature__enable__lod-import', true ) ) {
	$lod_import = new Lod_Import();
	$lod_import->register_hooks();
}
