<?php

namespace Wordlift\Jsonld;

use Wordlift\Assertions;
use Wordlift\Content\Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Relation\Relations;

/**
 * This class represents a jsonld graph, This is an abstraction layer over the
 * associative array and the references, relations, reference_infos.
 */
class Graph {

	/**
	 * A single item in jsonld ( associative array )
	 *
	 * @var array
	 */
	private $main_jsonld;

	/**
	 * @var Content_Id
	 */
	private $references = array();

	public function __construct( $main_jsonld ) {
		$this->main_jsonld = $main_jsonld;
	}

	/**
	 * @param $references array<int>
	 *
	 * @return void
	 */
	public function add_references( $refs ) {
		Assertions::is_array( $refs );
		foreach ( $refs as $ref ) {
			$this->references[] = Wordpress_Content_Id::create_post( $ref );
		}
	}

	// public function add_reference_infos( $references ) {
	//
	// }

	/**
	 * @param $relations Relations
	 *
	 * @return void
	 */
	public function add_relations( $relations ) {
		foreach ( $relations->toArray() as $relation ) {
			$this->references[] = $relation->get_object();
		}
	}

}
