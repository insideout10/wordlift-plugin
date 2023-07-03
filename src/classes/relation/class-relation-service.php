<?php

namespace Wordlift\Relation;

use Wordlift\Assertions;
use Wordlift\Content\Content;
use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Content_Service;

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
			// Disabled, as of now terms are not expanded by default,it happens only when
			// no-vocabulary-term feature is enabled  and it has specific rules
			// on which term to expand, so this delegate is disabled to retain the behaviour.
			// self::$instance->register_delegate( Post_Terms_Relation_Service::get_instance() );
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

	/**
	 * This helper method is used to create relations from entity_uris.
	 * Its only purpose as of now is to process the entity_uris emitted
	 * from disambiguation widget.
	 *
	 * @param $subject Wordpress_Content_Id
	 * @param $uris
	 *
	 * @return array<Relation>
	 */
	public static function get_relations_from_uris( $subject, $uris ) {

		$entity_service = \Wordlift_Entity_Service::get_instance();

		return array_filter(
			array_map(
				function ( $uri ) use ( $entity_service, $subject ) {
					/**
					 * @var $content Content|null
					 */
					$content = Wordpress_Content_Service::get_instance()->get_by_entity_id_or_same_as( $uri );
					if ( ! is_a( $content, 'Wordlift\Content\Content' ) ) {
						return false;
					}

					$bag = $content->get_bag();
					if ( ! is_a( $bag, '\WP_Post' ) && ! is_a( $bag, '\WP_Term' ) ) {
						return false;
					}
					$predicate = is_a( $bag, '\WP_Term' ) ? WL_WHAT_RELATION : $entity_service->get_classification_scope_for( $content->get_id() );

					return new Relation( $subject, new Wordpress_Content_Id( $content->get_id(), $content->get_object_type_enum() ), $predicate );
				},
				$uris
			)
		);

	}

}
