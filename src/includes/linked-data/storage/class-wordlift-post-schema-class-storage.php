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
class Wordlift_Post_Schema_Class_Storage extends Wordlift_Storage {

	/**
	 * Get the schema class for the specified {@link WP_Post}.
	 *
	 * @since 3.15.0
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 *
	 * @return string|array An array of schema classes.
	 */
	public function get( $post_id ) {

		// Get the type names (CamelCase).
		$names = Wordlift_Entity_Type_Service::get_instance()->get_names( $post_id );

		// If we don't find any type use the legacy function to get the URI.
		if ( empty( $names ) ) {
			$type = Wordlift_Entity_Type_Service::get_instance()->get( $post_id );

			return $type['uri'];
		}

		// Prepend the `schema.org` base URI.
		$uris = array_map(
			function ( $item ) {
				return "http://schema.org/$item";
			},
			$names
		);

		// Finally return the schema uri.
		return 1 === count( $uris ) ? $uris[0] : $uris;
	}

}
