<?php

namespace WordLift\Cleanup;

class Entity_Annotation_Cleanup_Post_Handler {

	/**
	 * @var Entity_Annotation_Cleanup_Post_Handler
	 */
	private static $instance = null;

	/**
	 * Entity_Annotation_Cleanup_Post_Handler constructor.
	 */
	private function __construct() {

	}

	/**
	 * @return Entity_Annotation_Cleanup_Post_Handler|null
	 */
	public static function get_instance() {
		if ( self::$instance === null ) {
			self::$instance = new Entity_Annotation_Cleanup_Post_Handler();
		}

		return self::$instance;
	}

	public function process_post( $post_content ) {
		//$post_content = get_post( $post_id )->post_content;
		$pattern      = '|<span\s+id="([^"]+)"\s+class="textannotation(?=[\s"])[^"]*"\s+itemid="([^"]*)">(.*?)</span>|';
		// Match pattern against post content.
		preg_match( $pattern, $post_content, $matches );

		if ( str_contains( $matches[2], 'https' ) || str_contains( $matches[2], 'http' ) ) {
			return false;

		}

		return $this->relative_to_absolute_url( $matches[2], get_home_url() . '/vocabulary' . $matches[2], $post_content );

	}

	public function relative_to_absolute_url( $search, $replace, $subject ) {
		return str_replace( $search, $replace, $subject );
	}
}

