<?php
/**
 * Storage: Post Schema Class Storage.
 *
 * Get the schema class of a {@link WP_Post}.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/linked-data/storage
 */

/**
 * Define the {@link Wordlift_Post_Schema_Class_Storage} class.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/linked-data/storage
 */
class Wordlift_Post_Schema_Class_Storage {

	/**
	 * Get the schema class for the specified {@link WP_Post}.
	 *
	 * @since 3.15.0
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 *
	 * @return array An array of schema classes.
	 */
	public function get( $post_id ) {

		$schema = Wordlift_Entity_Type_Service::get_instance()->get( $post_id );

		// Finally return the schema uri.
		return $schema['uri'];
	}

}
