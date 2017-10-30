<?php
/**
 * Services: JSON-LD Cache Service, a singleton.
 *
 * Define the json-ld cache Service.
 *
 * @since      3.16.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * The Wordlift_Jsonld_Cache_Service provides functions to cache information.
 *
 * @since      3.16.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
class Wordlift_Jsonld_Cache_Service extends Wordlift_Abstract_Cache_Service {

	/**
	 * A singleton instance of the json-ld caching service.
	 *
	 * @since 3.16.0
	 * @access private
	 * @var \Wordlift_Notice_Service $instance A singleton instance of the json-ld caching service.
	 */
	private static $instance = null;

	/**
	 * Wordlift_Jsonld_Cache_Service constructor.
	 *
	 * @since 3.16.0
	 */
	public function __construct() {

		if ( null === self::$instance ) {
			parent::__construct( 'jsonld' );

			self::$instance = $this;

			/*
			 * Set hooks to invalidate cache on content change.
			 */

			// Hook on post save to flush relevant cache.
			add_action( 'save_post', 'Wordlift_Jsonld_Cache_Service::save_post', 10, 2 );

			// Hook on meta change to detect when featured image is change which might not
			// always be when the post is saved.
			add_action( 'added_post_meta', 'Wordlift_Jsonld_Cache_Service::updated_meta', 10, 4 );
			add_action( 'updated_post_meta', 'Wordlift_Jsonld_Cache_Service::updated_meta', 10, 4 );
			add_action( 'deleted_post_meta', 'Wordlift_Jsonld_Cache_Service::updated_meta', 10, 4 );

			// Flush cache when wordlift settings were updated.
			add_action( 'update_option_wl_general_settings', 'Wordlift_Jsonld_Cache_Service::update_option_wl_general_settings' );
		}
	}

	/**
	 * Get the singleton instance of the Notice service.
	 *
	 * @since 3.16.0
	 * @return \Wordlift_Jsonld_Cache_Service The singleton instance of the jsonld service.
	 */
	static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new Wordlift_Jsonld_Cache_Service();
		}

		return self::$instance;
	}

	/**
	 * Delete relevant cache when a post content is changed.
	 *
	 * @since 3.16.0
	 *
	 * @param int     $post_id The id of the post.
	 * @param WP_Post $post    The post.
	 */
	static public function save_post( $post_id, $post ) {
		self::$instance->invalidate_post( $post_id );
	}

	/**
	 * Delete the relevant post cache when a featured image is changed.
	 *
	 * @since 3.16.0
	 *
	 * @param int    $meta_id    ID of updated metadata entry.
	 * @param int    $object_id  Object ID.
	 * @param string $meta_key   Meta key.
	 * @param mixed  $meta_value Meta value.
	 */
	static public function updated_meta( $meta_id, $object_id, $meta_key, $meta_value ) {
		if ( '_thumbnail_id' === $meta_key ) {
			self::$instance->invalidate_post( $object_id );
		}
	}

	/**
	 * Delete the cache when wordlift settings have changed.
	 *
	 * @since 3.16.0
	 */
	static public function update_option_wl_general_settings() {
		self::$instance->flush();
	}

	/**
	 * Invalidate the cache for a post and everything that references it.
	 *
	 * @since 3.16.0
	 *
	 * @param int $post_id The id of the post.
	 */
	function invalidate_post( $post_id ) {
		$this->delete( $post_id );
		$this->invalidate_referrers( $post_id );
	}

	/**
	 * Invalidate the cache for everything that references a post/entity.
	 *
	 * @since 3.16.0
	 *
	 * @param int $post_id The id of the post.
	 */
	function invalidate_referrers( $post_id ) {
		$ids = Wordlift_Relation_Service::get_instance()->get_subjects( $post_id, 'ids' );
		foreach ( $ids as $id ) {
			// Since we are doing a recursion here lets be extra carful to avoid
			// circular chains by avoiding further recursion if cache was already
			// invalidated for the post.
			$cache = $this->get( $id );
			if ( $cache ) {
				$this->delete( $id );
				$this->invalidate_referrers( $id );
			}
		}
	}
}
