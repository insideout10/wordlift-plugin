<?php
/**
 * Interfaces: Cache Service interface
 *
 * The interface for Cache Services defines the minimum functions a Cache Service
 * must support.
 *
 * @since      3.16.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/cache
 */

/**
 * Define the {@link Wordlift_Cache_Service} interface.
 *
 * @since      3.16.0
 */
interface Wordlift_Cache_Service {

	/**
	 * Get the cached response for the specified `id`.
	 *
	 * @since 3.16.0
	 *
	 * @param string $id The cache `id`.
	 *
	 * @return mixed|false The cached contents or false if the cache isn't found.
	 */
	public function get_cache( $id );

	/**
	 * Check whether we have cached results for the provided id.
	 *
	 * @since 3.16.3
	 *
	 * @param string $id The cache `id`.
	 *
	 * @return bool True if we have cached results otherwise false.
	 */
	public function has_cache( $id );

	/**
	 * Set the cache contents for the specified `id`.
	 *
	 * @since 3.16.0
	 *
	 * @param string $id       The cache id.
	 * @param mixed  $contents The cache contents.
	 */
	public function set_cache( $id, $contents );

	/**
	 * Delete the cache for the specified `id`.
	 *
	 * @since 3.16.0
	 *
	 * @param string $id The cache `id`.
	 */
	public function delete_cache( $id );

	/**
	 * Flush the whole cache.
	 *
	 * @since 3.16.0
	 */
	public function flush();

}
