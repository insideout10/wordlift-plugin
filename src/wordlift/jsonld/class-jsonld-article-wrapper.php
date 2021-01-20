<?php

namespace Wordlift\Jsonld;

class Jsonld_Article_Wrapper {

	private $post_to_jsonld_converter;
	private $jsonld_service;

	public function __construct( $post_to_jsonld_converter, $jsonld_service ) {

		$this->post_to_jsonld_converter = $post_to_jsonld_converter;
		$this->jsonld_service           = $jsonld_service;

		add_filter( 'wl_after_get_jsonld', array( $this, 'after_get_jsonld' ), 10, 2 );

	}

	public function after_get_jsonld( ... ) {

//		`Article`--->`mentions`--->`Thing`
//
//		`Article`--->`mentions`--->`Article`--->`Thing`

	}

}