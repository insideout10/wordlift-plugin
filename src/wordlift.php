<?php
/*
Plugin Name: WordLift
Plugin URI: http://wordlift.it
Description: Supercharge your WordPress Site with Smart Tagging and #Schemaorg support - a brand new way to write, organise and publish your contents to the Linked Data Cloud.
Version: 3.0.0-SNAPSHOT
Author: InSideOut10
Author URI: http://www.insideout.io
License: APL
*/

function wordlift_mce_buttons($buttons) {	
	/**
	 * Add in a core button that's disabled by default
	 */
	array_push($buttons, 'charmap');

	return $buttons;
}
add_filter('mce_buttons', 'wordlift_mce_buttons');