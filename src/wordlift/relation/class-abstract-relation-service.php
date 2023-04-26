<?php

namespace Wordlift\Relation;

use Wordlift\Content\Wordpress\Wordpress_Content_Id;

abstract class Abstract_Relation_Service implements Relation_Service_Interface {

	protected function __construct() {

	}

	/**
	 * A default implementation for the `get_relations` method that creates `Relations` instance
	 * and populates it with `Relation`s.
	 *
	 * @param Wordpress_Content_Id $content_id The WordPress content id.
	 *
	 * @return Relations_Interface A `Relations` instance.
	 */
	public function get_relations( $content_id ) {
		$relations = new Relations();

		$this->add_relations( $content_id, $relations );

		return $relations;
	}

}
