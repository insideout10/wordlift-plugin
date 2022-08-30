<?php
/**
 * Storage: Post Taxonomy Storage.
 *
 * Get the schema class of a {@link WP_Post}.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/linked-data/storage
 */

/**
 * Define the {@link Wordlift_Post_Taxonomy_Storage} class.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/linked-data/storage
 */
class Wordlift_Post_Taxonomy_Storage extends Wordlift_Storage {

	/**
	 * The taxonomy name.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var string $taxonomy The taxonomy name.
	 */
	private $taxonomy;

	/**
	 * Create a {@link Wordlift_Post_Taxonomy_Storage} with the specified
	 * taxonomy name.
	 *
	 * @since 3.15.0
	 *
	 * @param string $taxonomy The taxonomy name.
	 */
	public function __construct( $taxonomy ) {

		$this->taxonomy = $taxonomy;
	}

	/**
	 * Get the taxonomy's terms associated with the specified {@link WP_Post}.
	 *
	 * @since 3.15.0
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 *
	 * @return array|WP_Error An array of terms or {@link WP_Error} in case of error.
	 */
	public function get( $post_id ) {

		return wp_get_post_terms(
			$post_id,
			$this->taxonomy,
			array(
				'hide_empty' => false,
				// Because of #334 (and the AAM plugin) we changed fields from 'id=>slug' to 'all'.
				// An issue has been opened with the AAM plugin author as well.
				//
				// see https://github.com/insideout10/wordlift-plugin/issues/334
				// see https://wordpress.org/support/topic/idslug-not-working-anymore?replies=1#post-8806863
				'fields'     => 'all',
			)
		);
	}

}
