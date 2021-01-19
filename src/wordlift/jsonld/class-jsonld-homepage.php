<?php

namespace Wordlift\Jsonld;

/**
 * @since 3.27.9
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Jsonld_Homepage {
	/**
	 * @var \Wordlift_Relation_Service
	 */
	private $relation_service;

	/**
	 * Jsonld_Homepage constructor.
	 *
	 * @param $relation_service \Wordlift_Relation_Service
	 */
	public function __construct( $relation_service ) {

		$this->relation_service = $relation_service;

		add_filter( 'wl_website_jsonld', array( $this, 'add_mentions_if_singular' ) );

	}

	public function add_mentions_if_singular( $jsonld ) {

		if ( is_singular() && get_post_type( get_the_ID() ) !== 'entity' ) {
			$jsonld['mentions'] = $this->relation_service->get_objects( get_the_ID(), 'ids', null, 'publish' );
		}

		return $jsonld;
	}

}