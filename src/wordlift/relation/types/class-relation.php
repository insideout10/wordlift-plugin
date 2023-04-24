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

use Wordlift\Relation\Hashable;

abstract class Relation implements Hashable {

	/**
	 * @var int
	 */
	private $id;

	private $relation_type;

	/**
	 * Represents a subject type.
	 *
	 * @var int $subject_type One of Object_Type_Enum
	 */
	private $object_type_enum;

	public function __construct( $id, $classification_scope, $object_type_enum ) {
		$this->id               = $id;
		$this->relation_type    = $classification_scope;
		$this->object_type_enum = $object_type_enum;
	}

	/**
	 * Return Object id.
	 *
	 * @return int
	 */
	public function get_object_id() {
		return $this->id;
	}

	/**
	 * @return int Represents the {@link Object_Type_Enum}
	 */
	public function get_object_type() {
		return $this->object_type_enum;
	}

	/**
	 * @return int Represents the {@link Object_Type_Enum}
	 */
	public function get_subject_type() {
		return $this->subject_type;
	}

	/**
	 * Returns relation type.
	 *
	 * @return string Relation type {@link WL_WHAT_RELATION} etc.
	 */
	public function get_relation_type() {
		return $this->relation_type;
	}

	public function hash() {
		// Define the hash algorithm for your object
		// Here's an example using the md5 hash function
		return md5(
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
			serialize(
				array(
					$this->id,
					$this->relation_type,
					$this->subject_type,
					$this->get_object_type(),
				)
			)
		);
	}

	public function equals( Hashable $obj ) {
		return $this->hash() === $obj->hash();
	}
}
