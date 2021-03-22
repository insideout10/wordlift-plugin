<?php
namespace Wordlift\Vocabulary\Hooks;
/**
 * This class is used to show the entity match component on the
 * term page.
 */
class Term_Page_Hook {

	const HANDLE = 'wl-vocabulary-term-page-handle';

	public function connect_hook() {

		add_action('edit_post_tag_form_fields', array( $this, 'load_scripts'));

	}

	public function load_scripts() {

		wp_enqueue_script(self::HANDLE, "test");
		wp_enqueue_style(self::HANDLE, "test");
	}

}