<?php
/**
 * This class is created to provide relation service for terms
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.32.0
 *
 * @package Wordlift
 * @subpackage Wordlift\Relation
 */

namespace Wordlift\Relation;

use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Object_Type_Enum;

class Post_Terms_Relation_Service extends Abstract_Relation_Service {

	private static $instance = null;

	/**
	 * The singleton instance.
	 *
	 * @return Relation_Service_Interface
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	// **
	// * @param $term_id int Term id.
	// */
	// public function get_relation_type( $term_id ) {
	// $schema = $this->term_entity_type_service->get_schema(
	// $term_id
	// );
//		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
	// $classification_boxes = unserialize( WL_CORE_POST_CLASSIFICATION_BOXES );
	//
	// $entity_type = str_replace( 'wl-', '', $schema['css_class'] );
	// foreach ( $classification_boxes as $cb ) {
	// if ( in_array( $entity_type, $cb['registeredTypes'], true ) ) {
	// return $cb['id'];
	// }
	// }
	//
	// return WL_WHAT_RELATION;
	// }

	/**
	 * @param $content_id
	 * @param $relations
	 *
	 * @return void
	 */
	public function add_relations( $content_id, $relations ) {
		if ( $content_id->get_type() !== Object_Type_Enum::POST ) {
			return;
		}

		$post_id = $content_id->get_id();
		// @@todo, add a filter here?
		$taxonomies = array( 'category', 'post_tag' );
		$term_ids   = wp_get_object_terms( $post_id, $taxonomies, array( 'fields' => 'ids' ) );

		$new_relations = array_map(
			function ( $term_id ) use ( $content_id ) {
				$object_id = Wordpress_Content_Id::create_term( $term_id );

				// @@todo Entity Service works only for posts, we default to `WHAT` as predicate
				// for terms.
				return new Relation( $content_id, $object_id, WL_WHAT_RELATION );
			},
			$term_ids
		);

		$relations->add( ...$new_relations );
	}

}
