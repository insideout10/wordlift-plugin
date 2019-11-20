<?php


namespace Wordlift\Post;

use Wordlift\Entity\Entity_Factory;

class Post_Adapter {

	/**
	 * @var \Wordlift_Log_Service
	 */
	private $log;

	/**
	 * @var \Wordlift_Entity_Service
	 */
	private $entity_service;
	private $entity_factory;

	public function __construct() {

		$this->log = \Wordlift_Log_Service::get_logger( get_class() );

		$this->entity_service = \Wordlift_Entity_Service::get_instance();
		$this->entity_factory = Entity_Factory::get_instance();

		add_action( 'wp_insert_post', array( $this, 'wp_insert_post' ), 10, 3 );
	}

	/**
	 * A sample structure:
	 *
	 * {
	 *   "entities": [
	 *     {
	 *       "annotations": {
	 *         "urn:enhancement-7e8e66fc": {
	 *           "start": 3480,
	 *           "end": 3486,
	 *           "text": "libero"
	 *         }
	 *       },
	 *       "description": "Le libero ou libéro est un poste défensif du volley-ball. Des règles particulières le concernant ont été introduites à la fin des années 1990. De par sa spécificité, le libéro a un statut à part au sein d’une équipe de volley-ball. Pour être identifié, il doit porter un uniforme qui contraste avec ceux des autres membres de son équipe, titulaires ou remplaçants.",
	 *       "id": "http://fr.dbpedia.org/resource/Libero_(volley-ball)",
	 *       "label": "Libero (volley-ball)",
	 *       "mainType": "other",
	 *       "occurrences": ["urn:enhancement-7e8e66fc"],
	 *       "sameAs": null,
	 *       "synonyms": [],
	 *       "types": ["other"]
	 *     }
	 *   ]
	 * }
	 *
	 * @param int     $post_ID Post ID.
	 * @param WP_Post $post Post object.
	 * @param bool    $update Whether this is an existing post being updated or not.
	 *
	 * @throws \Exception
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

			$entity_id = $this->create_or_update_entity( $entity );

			wl_core_add_relation_instance( $post_ID, 'what', $entity_id );

			$this->log->trace( "Entity $entity_id created and relations added with post $post_ID." );

		}

	}

	/**
	 * Collect entity labels from the entity array.
	 *
	 * This function expects an array with the following keys:
	 *
	 * array(
	 *   'label'       => ...,
	 *   'synonyms'    => array( ... ),
	 *   'annotations' => array(
	 *     ...id...      => array( text => ... ),
	 *   ),
	 *   'occurrences' => array( ... ),
	 * )
	 *
	 * and it is going to output an array with all the labels, keeping the `label` at first position:
	 *
	 * array(
	 *   ...label...,
	 *   ...synonyms...,
	 *   ...texts...,
	 * )
	 *
	 * This function is going to collect the label from the `label` property, from the `synonyms` property and from
	 * `annotations` property. Since the `annotations` property contains all the annotations including those that
	 * haven't been selected, this function is going to only get the `text` for the annotations property listed in
	 * `occurrences`.
	 *
	 * @param array $entity {
	 *  The entity data.
	 *
	 * @type string $label The entity label.
	 * @type array  $synonyms The entity synonyms.
	 * @type array  $occurrences The selected occurrences.
	 * @type array  $annotations The annotations.
	 * }
	 *
	 * @return array An array of labels.
	 */
	public function get_labels( $entity ) {

		$args = wp_parse_args( $entity, array(
			'label'       => array(),
			'synonyms'    => array(),
			'annotations' => array(),
			'occurrences' => array(),
		) );

		// We gather all the labels, occurrences texts and synonyms into one array.
		$initial = array_merge(
			(array) $args['label'],
			(array) $args['synonyms']
		);

		$annotations = $args['annotations'];

		return array_reduce( $args['occurrences'], function ( $carry, $item ) use ( $annotations ) {

			// Bail out if occurrences->$item->text isn't set or its contents are already
			// in `$carry`.
			if ( ! isset( $annotations[ $item ]['text'] )
			     || in_array( $annotations[ $item ]['text'], $carry ) ) {
				return $carry;
			}

			// Push the label.
			$carry[] = $annotations[ $item ]['text'];

			return $carry;
		}, $initial );
	}

	/**
	 * @param $post_ID
	 * @param $entity
	 *
	 * @throws \Exception
	 */
	private function create_or_update_entity( $entity ) {

		$uris = array_merge(
			(array) $entity['id'],
			(array) $entity['sameAs']
		);

		$post = $this->get_first_matching_entity_by_uri( $uris );

		// Get the labels.
		$labels = $this->get_labels( $entity );

		// Create the entity if it doesn't exist.
		if ( isset( $post ) ) {
			return $this->entity_factory->create( array(
				'labels'      => $labels,
				'description' => $entity['description'],
				'same_as'     => (array) $entity['sameAs'],
			) );
		}

		// Update the entity otherwise.
		return $this->entity_factory->update( array(
			'ID'          => $post->ID,
			'labels'      => $labels,
			'same_as'     => (array) $entity['sameAs'],
		) );

	}


	/**
	 * Get the first matching entity for the provided URI array.
	 *
	 * Entities IDs and sameAs are searched.
	 *
	 * @param array $uris An array of URIs.
	 *
	 * @return \WP_Post|null The entity WP_Post if found or null if not found.
	 */
	private function get_first_matching_entity_by_uri( array $uris ) {

		foreach ( $uris as $uri ) {
			$existing_entity = $this->entity_service->get_entity_post_by_uri( $uri );
			if ( isset( $existing_entity ) ) {
				return $existing_entity;
			}
		}

		return null;
	}


}
