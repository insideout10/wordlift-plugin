<?php

namespace Wordlift\Content\Wordpress;

use Wordlift\Assertions;
use Wordlift\Object_Type_Enum;

abstract class Abstract_Wordpress_Content_Legacy_Service extends Abstract_Wordpress_Content_Service {


	private $expected_object_type;
	private $get_meta_fn;

	protected function __construct( $expected_object_type, $get_meta_fn ) {
		parent::__construct();

		$this->expected_object_type = $expected_object_type;
		$this->get_meta_fn          = $get_meta_fn;
	}

	public function get_entity_id( $content_id ) {
		Assertions::equals( $content_id->get_type(), $this->expected_object_type,
			sprintf( '`content_id` must be of type `%s`.', Object_Type_Enum::to_string( $this->expected_object_type ) ) );

		return call_user_func( $this->get_meta_fn, $content_id->get_id(), 'entity_url', true ) ?: null;
	}

}
