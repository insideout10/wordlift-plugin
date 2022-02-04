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
	/**
	 * The entity description.
	 * @var string
	 */
	private $description;

	public function __construct( $types, $name, $description ) {
		$this->types = $types;
		$this->name  = $name;
		$this->description = $description;
	}


	function getName() {
		return $this->name;
	}

	function getDescription() {
		return $this->description;
	}

	function getSameAs() {
		// TODO: Implement getSameAs() method.
	}

	function getTypes() {
		return $this->types;
	}
}