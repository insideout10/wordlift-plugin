<?php
/**
 * This factory provides Relation_Service based on the feature active.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.32.6
 *
 * @package Wordlift
 * @subpackage Wordlift\Relation
 */

namespace Wordlift\Relation;

use Wordlift\Features\Feature_Utils;

class Object_Relation_Factory {

	/**
	 * @param int $post_id
	 *
	 * @return Relation_Service_Interface
	 */
	public static function get_instance( $post_id ) {
		// The post type doesnt have an editor and no-editor-analysis feature is turned on.
		if ( Feature_Utils::is_feature_on( 'no-editor-analysis', false ) &&
		     ! post_type_supports( get_post_type( $post_id ), 'editor' ) ) {
			return Object_No_Annotation_Relation_Service::get_instance();
		}

		return Object_Relation_Service::get_instance();

	}


}