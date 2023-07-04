<?php
/**
 *  This file provides the class to check and validate the feature for post id when it is
 *  enabled.
 *
 * @since 3.32.6
 * @package  Wordlift\No_Editor_Analysis
 */

namespace Wordlift\No_Editor_Analysis;

class No_Editor_Analysis_Feature {

	public static function can_no_editor_analysis_be_used( $post_id ) {
		// If post id is falsy then dont do other checks, this necessary because get_post_type will return false
		// when the post_id is 0  and inverting it would turn on this feature.
		if ( ! $post_id ) {
			return false;
		}

		// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		return apply_filters( 'wl_feature__enable__no-editor-analysis', false )
			&& (
				// If the post doesnt have `editor` attribute
				Post_Type::is_no_editor_analysis_enabled_for_post_type( get_post_type( $post_id ) )
				// check if Divi is enabled, then we can use no editor analysis.
				|| self::is_divi_page_builder_enabled( $post_id )
				// Check if elementor is enabled, then we can use no editor analysis.
				|| self::is_elementor_enabled( $post_id )
				// Check if WP Bakery is enabled, then we can use no editor analysis.
				|| self::is_wp_bakery_enabled( $post_id )
				// Custom builders can hook in to this filter to enable no editor analysis.
				/**
				* @param $post_id
				*
				* @return bool | False by default.
				* @since 3.33.0
				* Filter name : wl_no_editor_analysis_should_be_enabled_for_post_id
				*/
				|| apply_filters( 'wl_no_editor_analysis_should_be_enabled_for_post_id', false, $post_id )
			);
	}

	private static function is_divi_page_builder_enabled( $post_id ) {
		return function_exists( 'et_pb_is_pagebuilder_used' ) && et_pb_is_pagebuilder_used( $post_id );
	}

	private static function is_elementor_enabled( $post_id ) {
		return defined( 'ELEMENTOR_VERSION' ) && get_post_meta( $post_id, '_elementor_edit_mode', true ) === 'builder';
	}

	private static function is_wp_bakery_enabled( $post_id ) {
		$post_type = get_post_type( $post_id );

		if ( ! function_exists( 'vc_editor_post_types' ) ) {
			return false;
		}

		try {
			return in_array( $post_type, vc_editor_post_types(), true );
		} catch ( \Exception $e ) {
			return false;
		}
	}

}
