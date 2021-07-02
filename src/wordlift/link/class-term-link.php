<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class is created to handle the entity links on the content
 * filter for the terms.
 */

namespace Wordlift\Link;

use Wordlift\Common\Singleton;
use Wordlift\Term\Uri_Service;
use Wordlift_Schema_Service;

class Term_Link extends Singleton implements Link {

	/**
	 * @var Uri_Service
	 */
	private $term_uri_service;

	public function __construct() {
		parent::__construct();
		$this->term_uri_service = Uri_Service::get_instance();
	}

	public function get_link_title( $id, $label_to_be_ignored ) {
		// TODO: Implement get_link_title() method.
	}

	public function get_same_as_uris( $id ) {
		return array_merge(
			(array) $this->term_uri_service->get_uri_by_term( $id ),
			get_term_meta( $id, Wordlift_Schema_Service::FIELD_SAME_AS )
		);
	}
}
