<?php
/**
 * Storage: Post Meta Storage.
 *
 * A {@link Wordlift_Storage} class which loads data from the post metas.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/linked-data/storage
 */

/**
 * Define the {@link Wordlift_Post_Meta_Storage} class.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/linked-data/storage
 */
class Wordlift_Post_Meta_Storage extends Wordlift_Storage {

	/**
	 * The meta key to load data from.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var string $meta_key The meta key to load data from.
	 */
	private $meta_key;

	/**
	 * Create a {@link Wordlift_Post_Meta_Storage} instance, by providing the
	 * meta key the storage should read from.
	 *
	 * @since 3.15.0
	 *
	 * @param string $meta_key The post meta key.
	 */
	public function __construct( $meta_key ) {

		$this->meta_key = $meta_key;

	}

	/**
	 * Get the value for the specified meta key.
	 *
	 * @since 3.15.0
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 *
	 * @return array An array of values (or an empty array if nothing is set).
	 */
	public function get( $post_id ) {

		return get_post_meta( $post_id, $this->meta_key );
	}

}
