<?php
/**
 * This file adds the mentions property for all the entities which are descendant of creativework.
 *
 * @see https://github.com/insideout10/wordlift-plugin/issues/1557
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @package Wordlift\Jsonld
 * @since 3.37.1
 */

namespace Wordlift\Jsonld;

class Mentions {

	public function __construct() {
		add_filter( 'wl_entity_jsonld_array', array( $this, 'wl_entity_jsonld_array' ) );
	}


}