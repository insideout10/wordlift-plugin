<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class is created to handle the entity links on the content
 * filter for the terms.
 */

namespace Wordlift\Link;

use Wordlift\Term\Synonyms_Service;
use Wordlift\Term\Uri_Service;
use Wordlift_Schema_Service;

class Term_Link extends Default_Link {

	/**
	 * @var Uri_Service
	 */
	private $term_uri_service;

	/**
	 * @var Synonyms_Service
	 */
	private $synonyms_service;

	public function __construct() {
		parent::__construct();
		$this->term_uri_service = Uri_Service::get_instance();
		$this->synonyms_service = Synonyms_Service::get_instance();
	}

	public function get_same_as_uris( $id ) {
		return array_merge(
			(array) $this->term_uri_service->get_uri_by_term( $id ),
			get_term_meta( $id, Wordlift_Schema_Service::FIELD_SAME_AS )
		);
	}

	public function get_id( $uri ) {
		$term = $this->term_uri_service->get_term( $uri );
		if ( ! $term ) {
			return false;
		}

		return $term->term_id;
	}

	public function get_synonyms( $id ) {
		return $this->synonyms_service->get_synonyms( $id );
	}

	public function get_permalink( $id ) {
		return get_term_link( $id );
	}

	public function get_edit_page_link( $id ) {
		return get_edit_term_link( $id );
	}
}
