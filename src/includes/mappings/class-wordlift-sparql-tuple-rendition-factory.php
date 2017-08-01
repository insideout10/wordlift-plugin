<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 01/08/2017
 * Time: 17:17
 */

class Wordlift_Sparql_Tuple_Rendition_Factory {

	public function __construct( $entity_service ) {

		$this->entity_service = $entity_service;

	}

	public function create( $storage, $predicate, $data_type = null, $language = null ) {

		return new Wordlift_Sparql_Tuple_Rendition( $this->entity_service, $storage, $predicate, $data_type, $language );
	}

}
