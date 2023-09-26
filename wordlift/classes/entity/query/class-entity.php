<?php

namespace Wordlift\Entity\Query;

use Wordlift\Content\Wordpress\Wordpress_Content;
use Wordlift\Object_Type_Enum;

class Entity {

	private $schema_type;
	/**
	 * @var Wordpress_Content
	 */
	private $content;

	/**
	 * @param $schema_type
	 * @param $content Wordpress_Content
	 */
	public function __construct( $schema_type, $content ) {
		$this->schema_type = $schema_type;
		$this->content     = $content;
	}

	/**
	 * @return Wordpress_Content
	 */
	public function get_content() {
		return $this->content;
	}

	public function get_schema_type() {
		return $this->schema_type;
	}

	public function get_title() {
		if ( Object_Type_Enum::POST === $this->content->get_object_type_enum() ) {
			return $this->content->get_bag()->post_title;
		}
		if ( Object_Type_Enum::TERM === $this->content->get_object_type_enum() ) {
			return $this->content->get_bag()->name;
		}
		return '';
	}

}
