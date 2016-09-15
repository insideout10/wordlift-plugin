<?php

/**
 * This class handle post thumbnails.
 *
 * @since 3.1.5
 */
class Wordlift_Thumbnail_Service {

	/**
	 * The Thumbnail id meta key.
	 *
	 * @since 3.1.5
	 */
	const THUMBNAIL_ID_META_KEY = '_thumbnail_id';

	/**
	 * The predicate used in RDF to describe the thumbnail.
	 *
	 * @since 3.1.5
	 */
	const THUMBNAIL_RDF_PREDICATE = 'http://schema.org/image';

	/**
	 * The Log service.
	 *
	 * @since 3.1.5
	 * @access private
	 * @var \Wordlift_Log_Service The Log service.
	 */
	private $log_service;

	/**
	 * Create an instance of the Thumbnail service.
	 *
	 * @since 3.1.5
	 */
	public function __construct() {

		$this->log_service = Wordlift_Log_Service::get_logger( 'Wordlift_Thumbnail_Service' );

	}

	/**
	 * Receive post meta events immediately after a post metadata has been deleted.
	 *
	 * @since 3.1.5
	 *
	 * @param array $meta_ids An array of deleted metadata entry IDs.
	 * @param int $object_id Object ID.
	 * @param string $meta_key Meta key.
	 * @param mixed $_meta_value Meta value.
	 */
	public function deleted_post_meta( $meta_ids, $object_id, $meta_key, $_meta_value ) {

		// Return if it's not the Thumbnail id meta key.
		if ( self::THUMBNAIL_ID_META_KEY !== $meta_key ) {
			return;
		}

		// Do not perform any action is the post is not published.
		if ( 'publish' !== get_post_status( $object_id ) ) {
			return;
		}

		// Get the post uri and return if it's null.
		if ( null === ( $uri = wl_get_entity_uri( $object_id ) ) ) {
			return;
		}

		// Prepare the query and execute it. We don't buffer the query since we're not going to reindex.
		$query = sprintf( 'DELETE { <%s> <%s> ?o . } WHERE  { <%1$s> <%2$s> ?o . };', $uri, self::THUMBNAIL_RDF_PREDICATE );
		if ( false === rl_execute_sparql_update_query( $query, false ) ) {

			$this->log_service->error( "An error occurred removing the post thumbnail [ meta ids :: " . ( is_array( $meta_ids ) ? implode( ',', $meta_ids ) : $meta_ids ) . " ][ object id :: $object_id ][ meta key :: $meta_key ][ meta value :: " . ( is_array( $_meta_value ) ? implode( ',', $_meta_value ) : $_meta_value ) . " ][ query :: $query ]" );

		}

	}

	/**
	 * Receive post meta events immediately after a post metadata has been added.
	 *
	 * @since 3.1.5
	 *
	 * @param int $mid The meta ID after successful update.
	 * @param int $object_id Object ID.
	 * @param string $meta_key Meta key.
	 * @param mixed $_meta_value Meta value.
	 */
	public function added_or_updated_post_meta( $mid, $object_id, $meta_key, $_meta_value ) {

		// $this->log_service->trace( "A post meta has been updated [ meta id :: $mid ][ object id :: $object_id ][ meta key :: $meta_key ][ meta value :: " . var_export( $_meta_value, true ) . " ]" );

		// Return if it's not the Thumbnail id meta key.
		if ( self::THUMBNAIL_ID_META_KEY !== $meta_key ) {
			return;
		}

		// The meta value must be the numeric id of the attachment. If it isn't, return.
		if ( ! is_numeric( $_meta_value ) ) {
			return;
		}

		// Do not perform any action is the post is not published.
		if ( 'publish' !== get_post_status( $object_id ) ) {
			return;
		}

		// Get the post uri and return if it's null.
		if ( null === ( $uri = wl_get_entity_uri( $object_id ) ) ) {
			return;
		}

		// Get the attachment url and return if not found.
		if ( false === ( $attachment_url = wp_get_attachment_url( $_meta_value ) ) ) {
			return;
		};

		// Prepare the query and execute it. We don't buffer the query since we're not going to reindex.
		$query = sprintf( 'DELETE { <%1$s> <%2$s> ?o . } WHERE  { <%1$s> <%2$s> ?o . }; INSERT DATA { <%1$s> <%2$s> <%3$s> . };', $uri, self::THUMBNAIL_RDF_PREDICATE, $attachment_url );
		if ( false === rl_execute_sparql_update_query( $query, false ) ) {

			$this->log_service->error( "An error occurred removing the post thumbnail [ meta ids :: $mid ][ object id :: $object_id ][ meta key :: $meta_key ][ meta value :: " . var_export( $_meta_value, true ) . " ][ query :: $query ]" );

		}

	}

}
