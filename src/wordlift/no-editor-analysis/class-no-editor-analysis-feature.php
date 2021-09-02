<?php
/**
 *  This file provides the class to check and validate the feature for post id when it is
 *  enabled.
 *
 * @since 3.32.6
 * @package  Wordlift\No_Editor_Analysis
 */
namespace Wordlift\No_Editor_Analysis;


use Wordlift\Features\Feature_Utils;

class No_Editor_Analysis_Feature {

	public static function can_no_editor_analysis_be_used( $post_id ) {
		// If post id is falsy then dont do other checks, this necessary because get_post_type will return false
		// when the post_id is 0  and inverting it would turn on this feature.
		if ( ! $post_id ) {
			return false;
		}
		return Feature_Utils::is_feature_on( 'no-editor-analysis', false )
		       && Post_Type::is_no_editor_analysis_enabled_for_post_type( get_post_type( $post_id ) );
	}

}