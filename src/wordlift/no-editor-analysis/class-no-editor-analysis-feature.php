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
		return Feature_Utils::is_feature_on( 'no-editor-analysis', false ) &&
		       ! post_type_supports( get_post_type( $post_id ), 'editor' );
	}

}