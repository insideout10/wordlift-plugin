<?php

namespace Wordlift\Vocabulary\Hooks;

use Wordlift\Scripts\Scripts_Helper;

/**
 * This class is used to show the entity match component on the
 * term page.
 */
class Term_Page_Hook {

	const HANDLE = 'wl-vocabulary-term-page-handle';

	const LOCALIZED_KEY = '_wlVocabularyTermPageSettings';

	public function connect_hook() {

		add_action( 'edit_post_tag_form_fields', array( $this, 'load_scripts' ) );

	}

	public function load_scripts() {

		Scripts_Helper::enqueue_based_on_wordpress_version(
			self::HANDLE,
			plugin_dir_url( dirname( dirname( __DIR__ ) ) ) . 'js/dist/vocabulary-term-page',
			array( 'react', 'react-dom', 'wp-polyfill' ),
			true
		);

		wp_enqueue_style( self::HANDLE, plugin_dir_url( dirname( dirname( __DIR__ ) ) ) . 'js/dist/vocabulary-term-page.full.css' );

		wp_localize_script( self::HANDLE, self::LOCALIZED_KEY, array('FOO' => 'BAR') );
	}

}