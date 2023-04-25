<?php

namespace Wordlift\Relation;

use Wordlift\Assertions;

class Relation_Service extends Abstract_Relation_Service {

	/**
	 * @var Relation_Service_Interface[]
	 */
	private $delegates = array();

	protected function __construct() {

	}

	private static $instance = null;

	/**
	 * The singleton instance.
	 *
	 * @return Relation_Service_Interface
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();

			self::$instance->register_delegate( Relation_Instances_Relation_Service::get_instance() );
			self::$instance->register_delegate( Post_Terms_Relation_Service::get_instance() );
			self::$instance->register_delegate( Post_Content_Relation_Service::get_instance() );
		}

		return self::$instance;
	}

	public function register_delegate( $delegate ) {
		Assertions::is_a( $delegate, 'Wordlift\Relation\Relation_Service_Interface', 'A `delegate` must implement the `Wordlift\Relation\Relation_Service_Interface` interface.' );

		$this->delegates[] = $delegate;
	}

	public function add_relations( $content_id, $relations ) {
		Assertions::is_set( $relations, '`$relations` should be set to a `Relations` instance.' );

		foreach ( $this->delegates as $delegate ) {
			$delegate->add_relations( $content_id, $relations );
		}
	}

}
