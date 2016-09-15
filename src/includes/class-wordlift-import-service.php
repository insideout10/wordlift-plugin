<?php

/**
 * Define a service class which extends WP's features to support import of linked
 * data content.
 */

/**
 * Define the Wordlift_Import_Service class.
 *
 * @since 3.6.0
 */
class Wordlift_Import_Service {

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since 3.6.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * A {@link Wordlift_Entity_Type_Service} instance.
	 *
	 * @since 3.6.0
	 * @access private
	 * @var \Wordlift_Entity_Type_Service $entity_type_service A {@link Wordlift_Entity_Type_Service} instance.
	 */
	private $entity_type_service;

	/**
	 * The dataset URI for this WordPress web site.
	 *
	 * @since 3.6.0
	 * @access private
	 * @var string $dataset_uri The dataset URI for this WordPress web site.
	 */
	private $dataset_uri;

	/**
	 * Create a Wordlift_Import_Service instance.
	 *
	 * @since 3.6.0
	 *
	 * @param $entity_type_service
	 */
	public function __construct( $entity_type_service, $dataset_uri ) {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Import_Service' );

		$this->entity_type_service = $entity_type_service;
		$this->dataset_uri         = $dataset_uri;
	}

	/**
	 * Handle the `wp_import_post_meta` filter by checking the `entity_url` meta.
	 * If the `entity_url` meta value is not within the WP web site dataset URI,
	 * it is changed into an `entity_same_as` meta key.
	 *
	 * @since 3.6.0
	 *
	 * @param array $postmeta An array of indexed post meta.
	 * @param int $post_id The post ID being imported.
	 * @param array $post An array of post properties.
	 *
	 * @return array An array of indexed post meta.
	 */
	public function wp_import_post_meta( $postmeta, $post_id, $post ) {

		// If we're not dealing with entity posts, return the original post meta.
		if ( $post['post_type'] !== $this->entity_type_service->get_post_type() ) {
			return $postmeta;
		}

		// Get a reference to the entity URL meta.
		$entity_url_meta = NULL;

		foreach ( $postmeta as &$meta ) {
			if ( 'entity_url' === $meta['key'] ) {
				$entity_url_meta = &$meta;
				break;
			}
		}

		// If the entity URI is within the dataset URI, we don't change anything.
		if ( NULL === $entity_url_meta || 0 === strpos( $entity_url_meta['value'], $this->dataset_uri ) ) {
			return $postmeta;
		}

		// Since the entity URL doesn't belong to this WP install, as the dataset
		// URI doesn't match the start of the entity URL, we turn the entity URL
		// meta to an entity sameAs. $entity_url_meta is a reference so it should
		// update the item in the postmeta array directly.
		$entity_url_meta['key'] = 'entity_same_as';

		return $postmeta;
	}

}
