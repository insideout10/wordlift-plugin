<?php
/**
 * An adapter to access and manipulate single post data for WordLift needs
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the {@link Wordlift_Post_Adatpter}.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */
class Wordlift_Post_Adapter {

	/**
	 * The post id to which the adopter relates.
	 *
	 * @since 3.14.0
	 *
	 * @var integer $post_id .
	 */
	private $post_id;

	/**
	 * Create the {@link Wordlift_Post_Adatpter} instance.
	 *
	 * @since 3.14.0
	 *
	 * @param integer $post_id the post ID of the post the adopter relates to.
	 */
	public function __construct( $post_id ) {

		$this->post_id = $post_id;

	}

	/**
	 * Get the word count of the post content.
	 *
	 * The count is calculated over the post content after stripping shortcodes and html tags.
	 *
	 * @since 3.14.0
	 *
	 * @return integer the number of words in the content after stripping shortcodes and html tags..
	 */
	public function word_count() {

		$post = get_post( $this->post_id );

		return str_word_count( strip_tags( strip_shortcodes( $post->post_content ) ) );
	}

	/**
	 * Get the {@link WP_Post} permalink allowing 3rd parties to alter the URL.
	 *
	 * @since 3.20.0
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return string The post permalink.
	 */
	public static function get_production_permalink( $post_id ) {

		/**
		 * The `wl_production_permalink` filter allows to change the permalink, this is useful in contexts
		 * when the production environment is copied over from a staging environment with staging
		 * URLs.
		 *
		 * @since 3.20.0
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/850
		 *
		 * @param string $permalink_url The default permalink.
		 * @param int    $post_id The post id.
		 */
		return apply_filters( 'wl_production_permalink', get_permalink( $post_id ), $post_id );
	}

}
