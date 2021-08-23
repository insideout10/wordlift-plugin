<?php
namespace Wordlift\No_Editor_Analysis;

class Post_Type {

	static function is_no_editor_analysis_enabled_for_post_type( $post_type ) {

		$no_editor_analysis_post_types = apply_filters(
			'wl_no_editor_analysis_post_types',
			array()
		);

		return in_array( $post_type, $no_editor_analysis_post_types );
	}

}