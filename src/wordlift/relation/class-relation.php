<?php
/**
 * This interface represents a single entry on the relations table.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.32.0
 *
 * @package Wordlift
 * @subpackage Wordlift\Relation
 */

namespace Wordlift\Relation;

abstract class Relation {

	/**
	 * @var int
	 */
	private $id;

	public function __construct( $id ) {
		$this->id = $id;
	}

	/**
	 * @return int Represents the {@link Object_Type_Enum}
	 */
	abstract function get_type();

}