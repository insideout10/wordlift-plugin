<?php
namespace Wordlift\Jsonld;

/**
 * This class represents an abstract reference
 *
 * @since 3.32.0
 * @package Wordlift\Jsonld
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
abstract class Abstract_Reference implements Reference {

	/**
	 * @var int
	 */
	private $id;

	/**
	 * Abstract_Reference constructor.
	 *
	 * @param $id int Identifier for the reference.
	 */
	public function __construct( $id ) {
		$this->id = $id;
	}

	abstract public function get_type();

	public function get_id() {
		return $this->id;
	}

	/**
	 * This function is necessary because array_unique can be applied
	 * on the references, we prepend the id with the type, for example
	 * post reference with post id 1 would be equal to {@link Object_Type_Enum::POST."_1"}
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->get_type() . '_' . $this->get_id();
	}

}
