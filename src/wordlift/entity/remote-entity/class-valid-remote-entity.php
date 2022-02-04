<?php

namespace Wordlift\Entity\Remote_Entity;

class Valid_Remote_Entity implements Remote_Entity {

	/**
	 * @var array<string>
	 */
	private $types;

	/**
	 * Title or name of the entity.
	 * @var string
	 */
	private $name;

	public function __construct( $types, $name ) {
		$this->types = $types;
		$this->name  = $name;
	}


	function getName() {
		return $this->name;
	}

	function getDescription() {
		// TODO: Implement getDescription() method.
	}

	function getSameAs() {
		// TODO: Implement getSameAs() method.
	}

	function getTypes() {
		return $this->types;
	}
}