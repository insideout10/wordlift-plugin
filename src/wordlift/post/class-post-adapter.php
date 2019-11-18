<?php


namespace Wordlift\Post;

class Post_Adapter {

	/**
	 * @var \Wordlift_Log_Service
	 */
	private $log;

	/**
	 * @var \Wordlift_Entity_Service
	 */
	private $entity_service;

	public function __construct() {

		$this->log = \Wordlift_Log_Service::get_logger( get_class() );

		$this->entity_service = \Wordlift_Entity_Service::get_instance();

		add_action( 'wp_insert_post', array( $this, 'wp_insert_post' ), 10, 3 );
	}

	/**
	 *
	 *
	 * @param int     $post_ID Post ID.
	 * @param WP_Post $post Post object.
	 * @param bool    $update Whether this is an existing post being updated or not.
	 */
	public function wp_insert_post( $post_ID, $post, $update ) {

		$data = (array) $post;

		$this->log->trace( "The following data has been received with `wp_insert_post`:\n"
		                   . var_export( $data, true ) . "\n"
		                   . "Called from:\n"
		                   . var_export( debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 10 ), true ) );

		// Bail out if there's no post_content or no `wordlift/classification` block.
		if ( empty( $data['post_content'] )
		     || ! function_exists( 'has_block' )
		     || ! function_exists( 'parse_blocks' )
		     || ! has_block( 'wordlift/classification', $data['post_content'] ) ) {
			return;
		}

		$all_blocks = parse_blocks( $data['post_content'] );
		$this->log->trace( "The following blocks have been parsed while in `wp_insert_post`:\n"
		                   . var_export( $all_blocks, true ) );

		$blocks = array_filter( $all_blocks, function ( $item ) {
			return ! empty( $item['blockName'] ) && 'wordlift/classification' === $item['blockName'];
		} );

		// Bail out if the blocks' array is empty.
		if ( empty( $blocks ) ) {
			return;
		}

		$block = current( $blocks );
		$this->log->trace( "The following block has been found while in `wp_insert_post`:\n"
		                   . var_export( $block, true ) );

		// Bail out if the entities array is empty.
		if ( empty( $block['attrs'] ) && empty( $block['attrs']['entities'] ) ) {
			return;
		}

		$entities = $block['attrs']['entities'];
		foreach ( $entities as $entity ) {

			$uris = array_merge(
				(array) $entity['id'],
				$entity['sameAs'] ?: array()
			);

			foreach ( $uris as $uri ) {
				$existing_entity = $this->entity_service->get_entity_post_by_uri( $uri );
				if ( isset( $existing_entity ) ) {
					break;
				}
			}

			if ( isset( $existing_entity ) ) {
				continue;
			}

			$entity_id = wp_insert_post( array(
				'post_type'    => 'entity',
				'post_status'  => 'draft',
				'post_title'   => $entity['label'],
				'post_content' => $entity['description'],
			), true );

			// Bail out if we've got an error.
			if ( is_wp_error( $entity_id ) ) {
				$this->log->error( $entity_id->get_error_message() );

				return;
			}

			// Bail out if the entity hasn't been created.
			if ( empty( $entity_id ) ) {
				return;
			}

			foreach ( $uris as $uri ) {
				add_post_meta( $entity_id, \Wordlift_Schema_Service::FIELD_SAME_AS, $uri );
			}

			if ( is_array( $entity['label'] ) ) {
				$this->entity_service->set_alternative_labels( $entity_id, $entity['label'] );
			}

			wl_core_add_relation_instance( $post_ID, 'what', $entity_id );

			$this->log->trace( "Entity $entity_id created and relations added with post $post_ID." );

		}

	}


}
