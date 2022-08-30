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

use Wordlift\No_Editor_Analysis\No_Editor_Analysis_Feature;

class Object_Relation_Factory {

	/**
	 * @param int $post_id
	 *
	 * @return Relation_Service_Interface
	 */
	public static function get_instance( $post_id ) {
		// The post type doesnt have an editor and no-editor-analysis feature is turned on.
		if ( No_Editor_Analysis_Feature::can_no_editor_analysis_be_used( $post_id ) ) {
			return Object_No_Annotation_Relation_Service::get_instance();
		}

		return Object_Relation_Service::get_instance();

	}

}
