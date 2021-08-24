<?php
namespace Wordlift\No_Editor_Analysis;

class Post_Type {

	static function is_no_editor_analysis_enabled_for_post_type( $post_type ) {

		// Enable it on post types which doesn't have editor by default.
		return ! post_type_supports( $post_type, 'editor' );

	}

}