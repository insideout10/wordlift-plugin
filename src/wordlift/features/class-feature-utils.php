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
		return apply_filters( 'wl_feature__enable__' . $feature_slug, false );
	}

}