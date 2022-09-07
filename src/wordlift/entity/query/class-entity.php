<?php

namespace Wordlift\Entity\Query;

use Wordlift\Content\Wordpress\Wordpress_Content;

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


}
