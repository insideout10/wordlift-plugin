<?php
namespace Wordlift\No_Editor_Analysis;

class Post_Type {

	public static function is_no_editor_analysis_enabled_for_post_type( $post_type ) {

		if ( ! $post_type ) {
			return false;
		}

		if ( ! post_type_exists( $post_type ) ) {
			return false;
		}

		// Enable it on post types which doesn't have editor by default.
		return ! post_type_supports( $post_type, 'editor' );

	}

}
