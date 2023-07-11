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
		if ( ! No_Editor_Analysis_Feature::can_no_editor_analysis_be_used( get_the_ID() ) ) {
			return;
		}

		add_meta_box(
			self::META_BOX_ID,
			__( 'WordLift', 'wordlift' ),
			array( $this, 'render_meta_box' ),
			$post_type,
			'side',
			'high'
		);

	}

	public function render_meta_box() {
		echo sprintf(
			"<div id='%s'></div><div id='%s'></div>",
			esc_attr( 'wl-no-editor-analysis-meta-box-content' ),
			// Div to store the entities.
			esc_attr( 'wl-no-editor-analysis-meta-box-storage' )
		);
	}

}
