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

namespace Wordlift\Relation\Types;

abstract class Relation {

	/**
	 * @var int
	 */
	private $id;

	private $relation_type;

	public function __construct( $id, $relation_type ) {
		$this->id = $id;
		$this->relation_type = $relation_type;
	}

	/**
	 * Return Object id.
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @return int Represents the {@link Object_Type_Enum}
	 */
	abstract function get_type();


	/**
	 * Returns relation type.
	 * @return string Relation type {@link WL_WHAT_RELATION} etc.
	 */
	function get_relation_type() {
		return $this->relation_type;
	}

}