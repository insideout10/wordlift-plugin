<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class is created to handle the entity links on the content
 * filter for the terms.
 */

namespace Wordlift\Link;

use Exception;
use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Term_Content_Service;
use Wordlift\Term\Synonyms_Service;
use Wordlift_Schema_Service;

class Term_Link extends Default_Link {

	/**
	 * @var Synonyms_Service
	 */
	private $synonyms_service;

	public function __construct() {
		parent::__construct();
		$this->synonyms_service = Synonyms_Service::get_instance();
	}

	/**
	 * @throws Exception when the {@link Wordpress_Term_Content_Service} throws one, i.e. when passing a non term content id.
	 */
	public function get_same_as_uris( $id ) {
		return array_merge(
			(array) Wordpress_Term_Content_Service::get_instance()
														 ->get_entity_id( Wordpress_Content_Id::create_term( $id ) ),
			get_term_meta( $id, Wordlift_Schema_Service::FIELD_SAME_AS )
		);
	}

	public function get_id( $uri ) {
		$content = Wordpress_Term_Content_Service::get_instance()
														->get_by_entity_id_or_same_as( $uri );

		if ( ! isset( $content ) ) {
			return false;
		}

		return $content->get_bag()->term_id;
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
