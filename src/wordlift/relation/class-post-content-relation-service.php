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
use Wordlift\Content\Wordpress\Wordpress_Content_Service;
use Wordlift\Object_Type_Enum;
use Wordlift_Entity_Service;

class Post_Content_Relation_Service extends Abstract_Relation_Service {

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

	/**
	 * @param Wordpress_Content_Id $content_id
	 * @param Relations_Interface  $relations
	 */
	public function add_relations( $content_id, $relations ) {
		if ( $content_id->get_type() !== Object_Type_Enum::POST ) {
			return;
		}

		$post = get_post( $content_id->get_id() );
		if ( ! is_a( $post, 'WP_Post' ) ) {
			return;
		}

		$content_filter_service = \Wordlift_Content_Filter_Service::get_instance();

		$post_content    = wp_unslash( $post->post_content );
		$entity_uris     = $content_filter_service->get_entity_uris( $post_content );
		$content_service = Wordpress_Content_Service::get_instance();
		$entity_service  = Wordlift_Entity_Service::get_instance();

		foreach ( $entity_uris as $entity_uri ) {
			$content = $content_service->get_by_entity_id( $entity_uri );
			if ( ! isset( $content )
				 || ! is_a( $content, 'Wordlift\Content\Content' )
				 || null === $content->get_id() ) {
				continue;
			}

			$object_id = new Wordpress_Content_Id( $content->get_id(), $content->get_object_type_enum() );

			$predicate = $entity_service->get_classification_scope_for( $content->get_id() );
			$relation  = new Relation( $content_id, $object_id, $predicate );
			$relations->add( $relation );
		}

	}

}
