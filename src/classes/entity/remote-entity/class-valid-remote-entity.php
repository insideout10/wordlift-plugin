<?php

namespace Wordlift\Entity\Remote_Entity;

class Valid_Remote_Entity implements Remote_Entity {

	/**
	 * @var array<string>
	 */
	private $types;

	/**
	 * Title or name of the entity.
	 *
	 * @var string
	 */
	private $name;
	/**
	 * The entity description.
	 *
	 * @var string
	 */
	private $description;
	/**
	 * An array of sameAs urls.
	 *
	 * @var array<string>
	 */
	private $same_as;

	public function __construct( $types, $name, $description, $same_as ) {
		$this->types       = $types;
		$this->name        = $name;
		$this->description = $description;
		$this->same_as     = $same_as;
	}

	public function get_name() {
		return $this->name;
	}

	public function get_description() {
		return $this->description;
	}

	public function get_same_as() {
		return $this->same_as;
	}

	public function get_types() {
		return $this->types;
	}
}
