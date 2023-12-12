<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Bail out if the feature isn't enabled.
if ( ! apply_filters( 'wl_feature__enable__google-organization-kp', true ) ) { // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
	return;
}
