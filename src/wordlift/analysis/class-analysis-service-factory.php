<?php
/**
 *
 * This file provides factory for constructing analysis service based on the feature enabled / disabled.
 *
 * @package  Wordlift\Analysis
 */

namespace Wordlift\Analysis;

use Wordlift\Features\Feature_Utils;
use Wordlift\Features\Features_Registry;

/**
 * Factory class to construct @link \Wordlift_Api_Service
 */
class Analysis_Service_Factory {

	/**
	 * Get the analysis service conditionally.
	 *
	 * @return Analysis_Service
	 */
	public static function get_instance() {

		if ( Feature_Utils::is_feature_on( 'no-editor-analysis' ) ) {
			return No_Editor_Analysis_Service::get_instance();
		}

		return V1_Analysis_Service::get_instance();
	}

}
