<?php

namespace Wordlift\Jsonld;

/**
 * @since 3.27.9
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Jsonld_Homepage {

	public function __construct() {

		add_filter( 'wl_website_jsonld', array( $this, 'add_mentions_if_singular' ) );

	}

	public function add_mentions_if_singular( $jsonld ) {

		if ( is_singular() && get_post_type( get_the_ID() ) !== 'entity' ) {
			$jsonld['mentions'] = array();
		}

		return $jsonld;
	}

}