<?php
namespace Wordlift\Vocabulary\Hooks;
/**
 * This class is used to show the entity match component on the
 * term page.
 */
class Term_Page_Hook {

	public function connect_hook() {

		add_action('edit_post_tag_form_fields', array( $this, 'load_scripts'));

	}

}