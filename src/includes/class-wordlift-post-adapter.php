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
class Wordlift_Post_Adatpter {

	/**
	 * The post id to which the adopter relates.
	 *
	 * @since 3.14.0
	 *
	 * @var integer $post_id.
	 */
	private $post_id;

	/**
	 * Create the {@link Wordlift_Post_Adatpter} instance.
	 *
	 * @since 3.14.0
	 *
	 * @param integer $post_id the post ID of the post the adopter relates to.
	 */
	function __construct( $post_id ) {

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
		$wordcount = str_word_count( strip_tags( strip_shortcodes( $post->post_content ) ) );

		return $wordcount;
	}
}
