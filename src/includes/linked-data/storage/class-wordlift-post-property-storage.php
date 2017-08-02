<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 01/08/2017
 * Time: 17:10
 */

class Wordlift_Post_Property_Storage extends Wordlift_Storage {

	const TITLE = 'title';

	const DESCRIPTION_NO_TAGS_NO_SHORTCODES = 'description_no_tags_no_shortcodes';

	private $property;

	/**
	 * Wordlift_Post_Property_Storage constructor.
	 */
	public function __construct( $property ) {

		$this->property = $property;

	}

	public function get( $post_id ) {

		$post = get_post( $post_id );

		switch ( $this->property ) {

			case self::TITLE:
				return $post->post_title;

			case self::DESCRIPTION_NO_TAGS_NO_SHORTCODES:
				return wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
		}

		return null;
	}

}
