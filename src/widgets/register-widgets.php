<?php

/**
 * Register Chord Widget
 *
 * @return void
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function wl_register_chord_widget() {

	register_widget( 'WordLift_Chord_Widget' );
}

function wl_register_geo_widget() {
	register_widget( 'WordLift_Geo_Widget' );
}

function wl_register_timeline_widget() {

	register_widget( 'WordLift_Timeline_Widget' );
}
