<?php
/**
 *
 * This file provides factory for constructing analysis service based on the feature enabled / disabled.
 *
 * @package  Wordlift\Features
 */

namespace Wordlift\Features;

/**
 * Feature_Utils provides static methods to check conditions on the features.
 */
class Feature_Utils {

	/**
	 * Returns true if the feature is enabled.
	 *
	 * @param string $feature_slug Feature slug.
	 *
	 * @return bool
	 */
	public static function is_feature_on( $feature_slug ) {
		$existing_features = get_option( Response_Adapter::WL_FEATURES, array() );
		return array_key_exists( $feature_slug, $existing_features ) && $existing_features[ $feature_slug ];
	}

}