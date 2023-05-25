<?php
/**
 * This file hooks to WordPress' post-related events in order to store entities.
 *
 * In particular we register the `wordlift/classification` block type and hook to the `wp_insert_post_data` hook
 * to parse the block type, retrieve the entities data and store it to the database.
 *
 * @authod David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 *
 * @package Wordlift
 * @subpackage Wordlift\Post
 */

namespace Wordlift\Post;

use Wordlift\Content\Wordpress\Wordpress_Content_Service;
use Wordlift\Entity\Entity_Store;
use Wordlift_Entity_Uri_Service;

class Post_Adapter {

	/**
	 * A {@link Wordlift_Log_Service} logging instance.
	 *
	 * @access private
	 * @var \Wordlift_Log_Service A {@link Wordlift_Log_Service} logging instance.
	 */
	private $log;

	public function __construct() {

		// Bail out if block editor's functions aren't available.
		if ( ! function_exists( 'register_block_type' ) || ! function_exists( 'parse_blocks' ) ) {
			return;
		}

		$this->log = \Wordlift_Log_Service::get_logger( get_class() );

		add_action( 'init', array( $this, 'init' ) );
		add_filter( 'wp_insert_post_data', array( $this, 'wp_insert_post_data' ), 10 );

	}

	/**
	 * Initialize by registering our block type `wordlift/classification`, required for {@link parse_blocks) to work
	 * correctly.
	 */
	public function init() {

		register_block_type(
			'wordlift/classification',
			array(
				'editor_script' => 'wl-block-editor',
				'attributes'    => array(
					'entities' => array( 'type' => 'array' ),
				),
			)
		);

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
	 * @param array $data An array of slashed post data.
	 *
	 * @return array The data array.
	 */
	public function wp_insert_post_data( $data ) {
		$post_status = $data['post_status'];
		if ( 'auto-draft' === $post_status || 'inherit' === $post_status ) {
			return $data;
		}

		try {
			$entities = $this->parse_content( wp_unslash( $data['post_content'] ) );

			foreach ( $entities as $entity ) {

				$entity_id = array_key_exists( 'id', $entity ) ? $entity['id'] : '';

				if ( ! $this->entity_id_valid( $entity_id ) ) {
					continue;
				}

				$entity_uris = $this->get_entity_uris( $entity );

				if ( $this->get_first_matching_entity_by_uri( $entity_uris ) === null &&
					 Post_Entities_Validator::is_local_entity_uri_exist( Wordlift_Entity_Uri_Service::get_instance(), $entity_uris ) ) {
					// Skip the entity
					continue;
				}

				// If 'entity auto publish' is false, we set the status to `draft` by default.
				// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
				$post_status = apply_filters( 'wl_feature__enable__entity-auto-publish', true )
					? $data['post_status'] : 'draft';
				$this->create_or_update_entity( $entity, $post_status );

			}
		} catch ( \Exception $e ) {
			$this->log->error( $e->getMessage() );
		}

		return $data;
	}

	/**
	 * Parse the post content to find the `wordlift/classification` block and return the entities' data.
	 *
	 * @param string $post_content The post content.
	 *
	 * @return array An array of entities' structures.
	 * @throws \Exception when an error occurs.
	 */
	private function parse_content( $post_content ) {

		$all_blocks = parse_blocks( $post_content );

		$blocks = array_filter(
			$all_blocks,
			function ( $item ) {
				return ! empty( $item['blockName'] ) && 'wordlift/classification' === $item['blockName'];
			}
		);

		// Bail out if the blocks' array is empty.
		if ( empty( $blocks ) ) {
			return array();
		}

		$block = current( $blocks );

		// Bail out if the entities array is empty.
		if ( empty( $block['attrs'] ) && empty( $block['attrs']['entities'] ) ) {
			return array();
		}

		return $block['attrs']['entities'];
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
	 * @type array $synonyms The entity synonyms.
	 * @type array $occurrences The selected occurrences.
	 * @type array $annotations The annotations.
	 * }
	 *
	 * @return array An array of labels.
	 */
	public function get_labels( $entity ) {

		$args = wp_parse_args(
			$entity,
			array(
				'label'       => array(),
				'synonyms'    => array(),
				'annotations' => array(),
				'occurrences' => array(),
			)
		);

		// We gather all the labels, occurrences texts and synonyms into one array.
		$initial = array_merge(
			(array) $args['label'],
			(array) $args['synonyms']
		);

		$annotations = $args['annotations'];

		return array_reduce(
			$args['occurrences'],
			function ( $carry, $item ) use ( $annotations ) {

				// Bail out if occurrences->$item->text isn't set or its contents are already
				// in `$carry`.
				if ( ! isset( $annotations[ $item ]['text'] )
				 || in_array( $annotations[ $item ]['text'], $carry, true ) ) {
					return $carry;
				}

				// Push the label.
				$carry[] = $annotations[ $item ]['text'];

				return $carry;
			},
			$initial
		);
	}

	/**
	 * Create or update the entity.
	 *
	 * An entity lookup is performed on the local vocabulary using the `id` and `sameAs` URIs. If an entity is found
	 * the {@link Entity_Store} update function is called to update the `labels` and the `sameAs` values.
	 *
	 * If an entity is not found the {@link Entity_Store} create function is called to create a new entity.
	 *
	 * @param array  $entity {
	 *  The entity parameters.
	 *
	 * @type string The entity item id URI.
	 * @type string|array The entity sameAs URI(s).
	 * @type string $description The entity description.
	 * }
	 *
	 * @param       $string $post_status The post status, default 'draft'.
	 *
	 * @return int|\WP_Error
	 * @throws \Exception when an error occurs.
	 */
	private function create_or_update_entity( $entity, $post_status = 'draft' ) {

		// Get only valid IDs.
		$uris = $this->get_entity_uris( $entity );

		$post = $this->get_first_matching_entity_by_uri( $uris );

		// Get the labels.
		$labels = $this->get_labels( $entity );

		if ( empty( $post ) ) {
			$entity_description = array_key_exists( 'description', $entity ) ? $entity['description'] : '';
			// Create the entity if it doesn't exist.
			$post_id = Entity_Store::get_instance()->create(
				array(
					'labels'      => $labels,
					'description' => $entity_description,
					'same_as'     => $uris,
				),
				$post_status
			);

			// Return the WP_Error if we got one.
			if ( is_wp_error( $post_id ) ) {
				return $post_id;
			}

			// Add the entity type.
			if ( isset( $entity['mainType'] ) ) {
				wp_set_object_terms( $post_id, $entity['mainType'], \Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
			}

			if ( isset( $entity['properties'] ) && isset( $entity['properties']['latitude'] ) && isset( $entity['properties']['longitude'] ) ) {
				add_post_meta( $post_id, \Wordlift_Schema_Service::FIELD_GEO_LATITUDE, $entity['properties']['latitude'] );
				add_post_meta( $post_id, \Wordlift_Schema_Service::FIELD_GEO_LONGITUDE, $entity['properties']['longitude'] );
			}
		} else {
			// Update the entity otherwise.
			$post_id = Entity_Store::get_instance()->update(
				array(
					'ID'      => $post->ID,
					'labels'  => $labels,
					'same_as' => $uris,
				)
			);

			// Add the entity type.
			if ( isset( $entity['mainType'] ) ) {
				wp_add_object_terms( $post_id, $entity['mainType'], \Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
			}

			// see https://github.com/insideout10/wordlift-plugin/issues/1304
			// Set the post status, we need to set that in order to support entities
			// created using rest endpoint on block editor, so that they get published
			// when the post is published.
			// Once the entity is published don't update the post status.
			if ( 'publish' !== $post->post_status ) {
				wp_update_post(
					array(
						'ID'          => $post->ID,
						'post_status' => $post_status,
					)
				);
			}
		}

		return $post_id;
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
	private function get_first_matching_entity_by_uri( $uris ) {

		foreach ( $uris as $uri ) {

			$content = Wordpress_Content_Service::get_instance()->get_by_entity_id_or_same_as( $uri );

			if ( ! $content ) {
				continue;
			}

			$existing_entity = $content->get_bag();

			if ( is_a( $existing_entity, 'WP_Post' ) ) {
				return $existing_entity;
			}
		}

		return null;
	}

	/**
	 * @param $entity
	 *
	 * @return array
	 */
	private function filter_valid_entity_ids( $entity ) {
		$id = $entity['id'];

		return array_filter(
			(array) $id,
			function ( $id ) {
				return preg_match( '|^https?://|', $id );
			}
		);
	}

	/**
	 * @param array $entity
	 *
	 * @return array
	 */
	private function get_entity_uris( $entity ) {
		$ids     = $this->filter_valid_entity_ids( $entity );
		$same_as = array_key_exists( 'sameAs', $entity ) ? $entity['sameAs'] : array();

		return array_merge(
			(array) $ids,
			(array) $same_as
		);
	}

	private function entity_id_valid( $entity_id ) {
		return preg_match( '#^https?://#i', $entity_id ) === 1;
	}

}
