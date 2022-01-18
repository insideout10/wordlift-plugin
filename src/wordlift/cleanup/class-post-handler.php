<?php

/**
 * Provide a way to clean up entity annotation from post content.
 *
 * @since 3.34.1
 * @see https://github.com/insideout10/wordlift-plugin/issues/1522
 */

namespace WordLift\Cleanup;

class Post_Handler {

	/**
	 * @var Post_Handler
	 */
	private static $instance = null;

	/**
	 * @var Post_Content_Helper
	 */
	private $post_content_helper;

	/**
	 * @var Post_Db_Handler
	 */
	private $post_db_handler;

	/**
	 * Entity_Annotation_Cleanup_Post_Handler constructor.
	 */
	private function __construct( $post_content_helper, $post_db_handler ) {
		$this->post_content_helper = $post_content_helper;
		$this->post_db_handler     = $post_db_handler;
	}

	/**
	 * @return Post_Handler|null
	 */
	public static function get_instance() {
		if ( self::$instance === null ) {
			self::$instance = new Post_Handler(
				new Post_Content_Helper(),
				new Post_Db_Handler()
			);
		}

		return self::$instance;
	}

	/**
	 * Process post.
	 *
	 * @param $post_id
	 *
	 */
	public function process_post( $post_id ) {
		$post_content = get_post( $post_id )->post_content;

		// Check if local entity exists, and perform replacement.
		$post_content = $this->post_content_helper->search_replace_relative_url( $post_content );

		// If local entity doesn't exist, remove annotations.
		$pattern = '|<span\s+id="([^"]+)"\s+class="textannotation(?=[\s"])[^"]*">(.*?)</span>|';

		// Match pattern against post content.
		$matches = array();
		preg_match_all( $pattern, $post_content, $matches, PREG_SET_ORDER );

		foreach ( $matches as $match ) {
			// Perform the replacement;
			$post_content = preg_replace( $pattern, '$2', $post_content );

		}
		var_dump( $post_content );
		// Finally update db.
		$this->post_db_handler->update_post_content(
			$post_content,
			$post_id
		);
	}
}
