<?php
namespace Wordlift\Common\Editor;

/**
 * Edit post loader will run the method only on the edit post screen.
 * @since 3.32.6
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
abstract  class Edit_Post_Loader {

	public function init() {
		add_action( 'load-post.php', array( $this, 'run_on_edit_post_screen') );
		add_action( 'load-post-new.php', array( $this, 'run_on_edit_post_screen') );
	}

	abstract public function run_on_edit_post_screen();


}