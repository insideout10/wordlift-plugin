<?php
/**
 * This file provides a factory to choose a Occurrences strategy based on feature active.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.32.6
 */
namespace Wordlift\Analysis\Occurrences;

use Wordlift\No_Editor_Analysis\No_Editor_Analysis_Feature;

class Occurrences_Factory {

	/**
	 * @param $post_id
	 *
	 * @return Occurrences
	 */
	public static function get_instance( $post_id ) {
		if ( No_Editor_Analysis_Feature::can_no_editor_analysis_be_used( $post_id ) ) {
			return No_Annotation_Strategy::get_instance();
		}
		return Default_Strategy::get_instance();
	}

}
