<?php
namespace Wordlift\Jsonld;
/**
 * This class represents an abstract reference
 *
 * @since 3.31.7
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

	abstract function get_type();

	public function get_id() {
		return $this->id;
	}
}