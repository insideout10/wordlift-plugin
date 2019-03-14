<?php
/**
 * Storage: Post Property Storage.
 *
 * Provides access to {@link WP_Post} properties.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/linked-data/storage
 */

/**
 * Define the {@link Wordlift_Post_Property_Storage} class.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/linked-data/storage
 */
class Wordlift_Post_Property_Storage extends Wordlift_Storage {

	/**
	 * The `post_title` property.
	 */
	const TITLE = 'title';

	/**
	 * The `post_content` property stripped of tags and shortcodes.
	 */
	const DESCRIPTION_NO_TAGS_NO_SHORTCODES = 'description_no_tags_no_shortcodes';

	/**
	 * The `post_author` property.
	 */
	const AUTHOR = 'author';

	/**
	 * The {@link WP_Post} property to retrieve.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var string $property The {@link WP_Post} property to retrieve.
	 */
	private $property;

	/**
	 * Create a {@link Wordlift_Post_Property_Storage} instance.
	 *
	 * @since 3.15.0
	 *
	 * @param string $property A post property.
	 */
	public function __construct( $property ) {

		$this->property = $property;

	}

	/**
	 * Get the property value.
	 *
	 * @since 3.15.0
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 *
	 * @return array|string|null A single string, or an array of values or null
	 *                           if the property isn't recognized.
	 */
	public function get( $post_id ) {

		// Get the post.
		$post = get_post( $post_id );

		// Switch according to the selected property.
		switch ( $this->property ) {

			// Title.
			case self::TITLE:
				return $post->post_title;

			// Description.
			case self::DESCRIPTION_NO_TAGS_NO_SHORTCODES:
				return wp_strip_all_tags( preg_replace( '/\[[^]]+\]/', '', do_shortcode( $post->post_content ) ) );

			// Author.
			case self::AUTHOR:
				return $post->post_author;
		}

		return null;
	}

}
