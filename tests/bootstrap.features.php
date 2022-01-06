<?php

if ( ! function_exists( 'tests_add_filter' ) ) {
	throw new \Exception( '`tests_add_filter` is required.' );
}

foreach ( $_ENV as $key => $value ) {
	if ( preg_match( '@^WL_FEATURES__(.*)@', $key, $matches ) ) {
		$feature = strtolower( $matches[1] );
		tests_add_filter( "wl_feature__enable__$feature", $value );
	}
}
