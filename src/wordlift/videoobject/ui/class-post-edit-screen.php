<?php
/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Videoobject\Ui;

use Wordlift\Scripts\Scripts_Helper;

class Post_Edit_Screen {

	public function init() {
		$callback = array( $this, 'enqueue_scripts' );
		add_action( 'enqueue_block_editor_assets', $callback );
		add_action( 'admin_print_scripts-post.php', $callback );
		add_action( 'admin_print_scripts-post-new.php', $callback );
	}

	public function enqueue_scripts() {
		Scripts_Helper::enqueue_based_on_wordpress_version(
			'wl-videoobject',
			plugin_dir_url( dirname( dirname( __DIR__ ) ) ) . '/js/dist/videoobject',
			array( 'react', 'react-dom', 'wp-hooks', 'wp-i18n', 'wp-polyfill' )
		);
		wp_enqueue_style(
			'wl-videoobject',
			plugin_dir_url( dirname( dirname( __DIR__ ) ) ) . '/js/dist/videoobject.css',
			array(),
			WORDLIFT_VERSION
		);
		wp_localize_script(
			'wl-videoobject',
			'_wlVideoobjectConfig',
			array(
				'restUrl' => get_rest_url( null, '/wordlift/v1/videos' ),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
				'postId'  => get_the_ID(),
			)
		);
	}

}
