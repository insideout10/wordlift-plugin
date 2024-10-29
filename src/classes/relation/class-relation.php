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

use JsonSerializable;
use Wordlift\Assertions;
use Wordlift\Content\Wordpress\Wordpress_Content_Id;

class Relation implements Hashable_Interface, JsonSerializable {
	/**
	 * @var Wordpress_Content_Id
	 */
	private $subject;

	/**
	 * @var string
	 */
	private $predicate;

	/**
	 * @param Wordpress_Content_Id $subject
	 * @param Wordpress_Content_Id $object
	 * @param string               $predicate
	 */
	public function __construct( $subject, $object, $predicate ) {
		$this->subject   = $subject;
		$this->predicate = $predicate;
		$this->object    = $object;
	}

	/**
	 * @var Wordpress_Content_Id
	 */
	private $object;

	public static function from_json( $item ) {
		$subject   = Wordpress_Content_Id::from_json( $item['subject'] );
		$object    = Wordpress_Content_Id::from_json( $item['object'] );
		$predicate = $item['predicate'];

		return new self( $subject, $object, $predicate );
	}

	/**
	 * @return Wordpress_Content_Id
	 */
	public function get_subject() {
		return $this->subject;
	}

	/**
	 * @return string
	 */
	public function get_predicate() {
		return $this->predicate;
	}

	/**
	 * @return Wordpress_Content_Id
	 */
	public function get_object() {
		return $this->object;
	}

	public function hash() {
		// Define the hash algorithm for your object
		// Here's an example using the md5 hash function
		return md5(
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
			serialize(
				array(
					$this->get_subject(),
					$this->get_predicate(),
					$this->get_object(),
				)
			)
		);
	}

	public function equals( Hashable_Interface $obj ) {
		return $this->hash() === $obj->hash();
	}

	/**
	 * @return string
	 * @deprecated used for legacy compatibilty
	 */
	public function get_relation_type() {
		return $this->predicate;
	}

	/**
	 * @return int
	 * @deprecated used for legacy compatibility
	 */
	public function get_object_id() {
		return $this->object->get_id();
	}

	/**
	 * Subject type.
	 *
	 * @deprecated used for legacy compatibility
	 */
	public function get_subject_type() {
		return $this->subject->get_type();
	}

	/**
	 * Object type.
	 *
	 * @deprecated used for legacy compatibility
	 */
	public function get_object_type() {
		return $this->object->get_type();
	}

	public static function from_relation_instances( $instance ) {
		Assertions::is_set( $instance->subject_id );
		Assertions::is_set( $instance->subject_type );
		Assertions::is_set( $instance->predicate );
		Assertions::is_set( $instance->object_id );
		Assertions::is_set( $instance->object_type );

		return new self(
		// Subject.
			new Wordpress_Content_Id(
				$instance->subject_id,
				$instance->subject_type
			),
			// Object.
			new Wordpress_Content_Id(
				$instance->object_id,
				$instance->object_type
			),
			// Predicate.
			$instance->predicate
		);
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		return array(
			'subject'   => $this->get_subject(),
			'object'    => $this->get_object(),
			'predicate' => $this->get_predicate(),
		);
	}
}
