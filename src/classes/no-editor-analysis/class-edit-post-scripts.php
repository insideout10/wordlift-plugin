<?php

namespace Wordlift\No_Editor_Analysis;

use Wordlift\Common\Editor\Edit_Post_Loader;
use Wordlift\Scripts\Scripts_Helper;

class Edit_Post_Scripts extends Edit_Post_Loader {

	const HANDLE = 'wl-no-editor-analysis-deps';

	public function run_on_edit_post_screen() {

		// Dont load this script if the post doesnt support it.
		if ( ! No_Editor_Analysis_Feature::can_no_editor_analysis_be_used( get_the_ID() ) ) {
			return;
		}

		Scripts_Helper::enqueue_based_on_wordpress_version(
			self::HANDLE,
			plugin_dir_url( dirname( __DIR__ ) ) . 'js/dist/no-editor-analysis',
			array(
				'react',
				'react-dom',
				'wp-api-fetch',
				'wp-blocks',
				'wp-components',
				'wp-data',
				'wp-element',
				'wp-hooks',
				'wp-polyfill',
				'wp-rich-text',
			),
			true
		);

		wp_enqueue_style(
			self::HANDLE,
			plugin_dir_url( dirname( __DIR__ ) ) . 'js/dist/no-editor-analysis.full.css',
			array(),
			WORDLIFT_VERSION
		);

	}

}
