<?php
/**
 *  This file provides the analysis metabox for the posts without editor.
 *
 * @since 3.32.6
 * @package  Wordlift\No_Editor_Analysis
 */

namespace Wordlift\No_Editor_Analysis;

class Meta_Box {

	const META_BOX_ID = 'wl-no-editor-analysis-meta-box';


	public function init() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
	}

	public function add_meta_box( $post_type ) {

		// We enable it for only the post types which doesn't support the editor and
		// enabled on the filter.
		if (  ! wl_post_type_supports_editor( $post_type)
		 && $this->is_no_editor_analysis_enabled_for_post_type( $post_type ) ) {
			return;
		}

		add_meta_box(
			self::META_BOX_ID,
			__( 'WordLift', 'wordlift' ),
			array( $this, 'render_meta_box'),
			$post_type,
			'side',
			'high'
		);

	}

	public function render_meta_box() {
		echo sprintf("<div id='%s'></div>", esc_html('wl-no-editor-analysis-meta-box-content'));
	}

	private function is_no_editor_analysis_enabled_for_post_type( $post_type ) {

		$no_editor_analysis_post_types = apply_filters(
			'wl_no_editor_analysis_post_types',
			array()
		);
		return in_array( $post_type, $no_editor_analysis_post_types );
	}


}