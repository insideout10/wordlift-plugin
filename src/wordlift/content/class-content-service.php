<?php

/**
 * Define the entity operations which should abstract from the underlying form (post, page, term, user, ...).
 */

namespace Wordlift\Content;

use Exception;

interface Content_Service {

	/**
	 * Get an {@link Content} given a URI.
	 *
	 * @param string $uri The URI.
	 *
	 * @return Content|null The found {@link Content} or null if not found.
	 * @throws Exception if the URI is absolute and not within the dataset URI scope or the dataset URI isn't set.
	 */
	public function get_by_entity_id( $uri );

	/**
	 * Get an {@link Content} given a URI. The search is performed also in sameAs.
	 *
	 * @param string $uri The URI.
	 *
	 * @return Content|null The found {@link Content} or null if not found.
	 */
	public function get_by_entity_id_or_same_as( $uri );

	/**
	 * Get the {@link Content}'s URI given an {@link Content_Id}.
	 *
	 * @param Content_Id $content_id An {@link Content_Id}.
	 *
	 * @return string|null An absolute URI or null if not found.
	 */
	public function get_entity_id( $content_id );

	/**
	 * Set the {@link Content}'s URI for the specified {@link Content_Id}.
	 *
	 * @param Content_Id $content_id An {@link Content_Id}.
	 * @param string     $uri The URI.
	 *
	 * @return void
	 * @throws Exception if the URI is absolute and not within the dataset URI scope or the dataset URI isn't set.
	 */
	public function set_entity_id( $content_id, $uri );

	/**
	 * Whether the {@link Content_Service} supports the provided {@link Content_Id}.
	 *
	 * @param Content_Id $content_id
	 *
	 * @return bool
	 */
	public function supports( $content_id );

	/**
	 * Delete the content with the specified ID.
	 *
	 * @param Content_Id $content_id
	 */
	public function delete( $content_id );

	/**
	 * Get the JSON-LD for the specified Content_Id.
	 *
	 * @param Content_Id $content_id
	 */
	public function get_about_jsonld( $content_id );

	/**
	 * Set the JSON-LD for the specified Content_Id.
	 *
	 * @param Content_Id $content_id
	 * @param string     $value
	 */
	public function set_about_jsonld( $content_id, $value );

}
