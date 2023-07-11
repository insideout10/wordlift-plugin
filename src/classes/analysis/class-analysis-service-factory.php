<?php
/**
 *
 * This file provides factory for constructing analysis service based on the feature enabled / disabled.
 *
 * @package  Wordlift\Analysis
 */

namespace Wordlift\Analysis;

use Wordlift\No_Editor_Analysis\No_Editor_Analysis_Feature;

/**
 * Factory class to construct @link \Wordlift_Api_Service
 */
class Analysis_Service_Factory {

	/**
	 * Get the analysis service conditionally.
	 *
	 * @return Analysis_Service
	 */
	public static function get_instance( $post_id ) {
		// We want this analysis to happen only when the editor is not present.
		if ( No_Editor_Analysis_Feature::can_no_editor_analysis_be_used( $post_id ) ) {
			return No_Editor_Analysis_Service::get_instance();
		}

		return V1_Analysis_Service::get_instance();
	}

}
