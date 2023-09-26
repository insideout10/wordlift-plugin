<?php
/**
 * Converters: Abstract Post to JSON-LD Converter.
 *
 * An abstract converter which provides basic post conversion.
 *
 * @since      3.10.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Object_Type_Enum;
use Wordlift\Relation\Relation;
use Wordlift\Relation\Relation_Service;
use Wordlift\Relation\Relations;

/**
 * Define the {@link Wordlift_Abstract_Post_To_Jsonld_Converter} class.
 *
 * @since      3.10.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
abstract class Wordlift_Abstract_Post_To_Jsonld_Converter implements Wordlift_Post_Converter {

	/**
	 * The JSON-LD context.
	 *
	 * @since 3.10.0
	 */
	const CONTEXT = 'http://schema.org';

	/**
	 * A {@link Wordlift_Entity_Type_Service} instance.
	 *
	 * @since  3.10.0
	 * @access protected
	 * @var \Wordlift_Entity_Type_Service $entity_type_service A {@link Wordlift_Entity_Type_Service} instance.
	 */
	protected $entity_type_service;

	/**
	 * A {@link Wordlift_User_Service} instance.
	 *
	 * @since  3.10.0
	 * @access private
	 * @var \Wordlift_User_Service $user_service A {@link Wordlift_User_Service} instance.
	 */
	protected $user_service;

	/**
	 * A {@link Wordlift_Attachment_Service} instance.
	 *
	 * @since  3.10.0
	 * @access private
	 * @var \Wordlift_Attachment_Service $attachment_service A {@link Wordlift_Attachment_Service} instance.
	 */
	protected $attachment_service;

	/**
	 * @var Wordlift_Property_Getter
	 */
	private $property_getter;

	/**
	 * Wordlift_Post_To_Jsonld_Converter constructor.
	 *
	 * @param \Wordlift_Entity_Type_Service $entity_type_service A {@link Wordlift_Entity_Type_Service} instance.
	 * @param \Wordlift_User_Service        $user_service A {@link Wordlift_User_Service} instance.
	 * @param \Wordlift_Attachment_Service  $attachment_service A {@link Wordlift_Attachment_Service} instance.
	 * @param \Wordlift_Property_Getter     $property_getter
	 *
	 * @since 3.10.0
	 */
	public function __construct( $entity_type_service, $user_service, $attachment_service, $property_getter ) {
		$this->entity_type_service = $entity_type_service;
		$this->user_service        = $user_service;
		$this->attachment_service  = $attachment_service;
		$this->property_getter     = $property_getter;
	}

	/**
	 * Convert the provided {@link WP_Post} to a JSON-LD array. Any entity reference
	 * found while processing the post is set in the $references array.
	 *
	 * @param int $post_id The post id.
	 * @param array $references An array of entity references.
	 * @param array $relations
	 *
	 * @return array A JSON-LD array.
	 * @since 3.10.0
	 */
	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function convert( $post_id, &$references = array(), &$references_infos = array(), $relations = null ) {

		// Get the post instance.
		$post = get_post( $post_id );
		if ( null === $post ) {
			// Post not found.
			return null;
		}

		// Get the post URI @id.
		$id = Wordlift_Entity_Service::get_instance()->get_uri( $post->ID );
		if ( $id === null ) {
			$id = 'get_uri returned null, dataset is ' . wl_configuration_get_redlink_dataset_uri();
		}

		/*
		 * The `types` variable holds one or more entity types. The `type` variable isn't used anymore.
		 *
		 * @since 3.20.0 We support more than one entity type.
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/835
		 */
		// Get the entity @type. We consider `post` BlogPostings.
		// $type = $this->entity_type_service->get( $post_id );
		$types = $this->entity_type_service->get_names( $post_id );
		$type  = self::make_one( $types );

		// Prepare the response.
		$jsonld = array(
			'@context' => self::CONTEXT,
			'@id'      => $id,
			'@type'    => $type,
		);

		if ( post_type_supports( $post->post_type, 'excerpt' ) ) {
			$text_excerpt_length   = in_array( 'Product', (array) $types, true ) ? 50000 : 55;
			$jsonld['description'] = Wordlift_Post_Excerpt_Helper::get_text_excerpt( $post, $text_excerpt_length );
		}

		// Set the `mainEntityOfPage` property if the post has some contents.
		/*
		 * Apply the `wl_post_content` filter, in case 3rd parties want to change the post content, e.g.
		 * because the content is written elsewhere.
		 *
		 * As of 2023.01.18 we're forcing the mainEntityOfPage on products.
		 *
		 * @since 3.20.0
		 */
		$post_content = apply_filters( 'wl_post_content', $post->post_content, $post );
		if ( ! empty( $post_content ) || in_array( 'Product', (array) $type, true ) ) {
			// We're setting the `mainEntityOfPage` to signal which one is the
			// main entity for the specified URL. It might be however that the
			// post/page is actually about another specific entity. How WL deals
			// with that hasn't been defined yet (see https://github.com/insideout10/wordlift-plugin/issues/451).
			//
			// See http://schema.org/mainEntityOfPage
			//
			// No need to specify `'@type' => 'WebPage'.
			//
			// See https://github.com/insideout10/wordlift-plugin/issues/451.
			$jsonld['mainEntityOfPage'] = get_the_permalink( $post->ID );

			/**
			 * @see https://github.com/insideout10/wordlift-plugin/issues/1207
			 *
			 * @since 3.27.7
			 */
			if ( in_array(
				$type,
				array(
					'Occupation',
					'OccupationAggregationByEmployer',
				),
				true
			) ) {
				$jsonld['mainEntityOfPage'] = array(
					'@type'        => 'WebPage',
					'lastReviewed' => get_post_time( 'Y-m-d\TH:i:sP', true, $post, false ),
				);
			}
		};

		$this->set_images( $this->attachment_service, $post, $jsonld );

		// Naveen: Get the entities referenced by this post and set it to the `references`
		// array so that the caller can do further processing, such as printing out
		// more of those references.
		//
		// David: you can't set this to the `references`, because `references` it's an array of post
		// IDs and client are using this. As of 3.42.1 (2023-04-20) I am refactoring this to use a
		// new variable $relations.
		// ** @var Object_Relation_Service $object_relation_service */
		// $object_relation_service      = Object_Relation_Service::get_instance();
		// $references_without_locations = $object_relation_service->get_references( $post_id, Object_Type_Enum::POST );

		$relation_service = Relation_Service::get_instance();
		$content_id       = Wordpress_Content_Id::create_post( $post_id );
		$relation_service->add_relations( $content_id, $relations );
		$this->add_relations_to_references( $relations, $references );

		$this->expand_references_with_location( $references );

		return $jsonld;
	}

	public function expand_references_with_location( &$references ) {
		// Bail out if ACF isn't available.
		$entity_type_service = Wordlift_Entity_Type_Service::get_instance();

		// check if any of the references has the entity type set to Action, Event or organisation.
		$references_with_location = array_filter(
			$references,
			function ( $post_id ) use ( $entity_type_service ) {
				return ( $entity_type_service->has_entity_type( $post_id, 'http://schema.org/Action' )
						 || $entity_type_service->has_entity_type( $post_id, 'http://schema.org/Event' )
						 || $entity_type_service->has_entity_type( $post_id, 'http://schema.org/Organization' ) );
			}
		);

		// ACF Only
		if ( function_exists( 'get_field' ) ) {
			// If set, then get all the location ids and push them to references.
			foreach ( $references_with_location as $post_id ) {
				// We need to know whether the user chose a `Place` or smtg else (we're not interested in smtg else).
				$target_field = get_field( 'field_wl_18117ac085e38f4fc3b077340a9548ee7c9fb9f5_radio', $post_id );
				if ( 1 !== preg_match( '@^.*_as_place$@', $target_field ) ) {
					continue;
				}

				// Get the target posts
				$target_posts = get_field( $target_field, $post_id );
				if ( ! is_array( $target_posts ) || empty( $target_posts ) ) {
					continue;
				}

				// Add the target posts to the references
				foreach ( $target_posts as $target_post ) {
					if ( ! is_a( $target_post, '\WP_Post' ) ) {
						continue;
					}

					$references[] = $target_post->ID;
				}
			}
		}

		// Location as WP custom meta
		foreach ( $references_with_location as $post_id ) {
			$post_location_ids = get_post_meta( $post_id, Wordlift_Schema_Service::FIELD_LOCATION );
			$post_location_ids = is_array( $post_location_ids ) ? $post_location_ids : array();

			// Only numeric values here pls
			$post_location_ids = array_filter( $post_location_ids, 'is_numeric' );
			$post_location_ids = array_map( 'intval', $post_location_ids );
			$references        = array_merge( $post_location_ids, $references );
		}

	}

	/**
	 * Copies the Post relations to referenced post IDs (legacy).
	 *
	 * @param Relations $relations
	 */
	private function add_relations_to_references( $relations, &$references ) {
		/** @var Relation $relation */
		foreach ( $relations->toArray() as $relation ) {
			$object    = $relation->get_object();
			$object_id = $object->get_id();
			if ( $object->get_type() === Object_Type_Enum::POST && ! in_array( $object_id, $references, true ) ) {
				$references[] = $object_id;
			}
		}
	}

	// private function add_location_reference_objects() {
	// array_reduce(
	// $references_without_locations,
	// function ( $carry, $reference ) use ( $entity_type_service ) {
	// **
	// * @var $reference Reference
	// */
	// @see https://schema.org/location for the schema.org types using the `location` property.
	// if ( ! $entity_type_service->has_entity_type( $reference->get_id(), 'http://schema.org/Action' )
	// && ! $entity_type_service->has_entity_type( $reference->get_id(), 'http://schema.org/Event' )
	// && ! $entity_type_service->has_entity_type( $reference->get_id(), 'http://schema.org/Organization' ) ) {
	//
	// return $carry;
	// }
	// $post_location_ids        = get_post_meta( $reference->get_id(), Wordlift_Schema_Service::FIELD_LOCATION );
	// $post_location_references = array_map(
	// function ( $post_id ) {
	// return new Post_Reference( $post_id );
	// },
	// $post_location_ids
	// );
	//
	// return array_merge( $carry, $post_location_references );
	// },
	// array()
	// );
	// }

	/**
	 * If the provided value starts with the schema.org context, we remove the schema.org
	 * part since it is set with the '@context'.
	 *
	 * @param string $value The property value.
	 *
	 * @return string The property value without the context.
	 * @since 3.10.0
	 */
	public function relative_to_context( $value ) {
		return ! is_array( $value ) && 0 === strpos( $value, self::CONTEXT . '/' ) ? substr( $value, strlen( self::CONTEXT ) + 1 ) : $value;
	}

	/**
	 * Set the images, by looking for embedded images, for images loaded via the
	 * gallery and for the featured image.
	 *
	 * Uses the cache service to store the results of this function for a day.
	 *
	 * @param $attachment_service Wordlift_Attachment_Service
	 * @param WP_Post                                        $post The target {@link WP_Post}.
	 * @param array                                          $jsonld The JSON-LD array.
	 *
	 * @since 3.10.0
	 */
	public static function set_images( $attachment_service, $post, &$jsonld ) {

		// Prepare the attachment ids array.
		$ids = array();

		// Set the thumbnail id as first attachment id, if any.
		$thumbnail_id = get_post_thumbnail_id( $post->ID );
		if ( '' !== $thumbnail_id && 0 !== $thumbnail_id ) {
			$ids[] = $thumbnail_id;
		}

		// For the time being the following is being removed since the query
		// initiated by `get_image_embeds` is consuming lots of CPU.
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/689.
		//
		// Get the embeds, removing existing ids.
		// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		if ( apply_filters( 'wl_feature__enable__image-embeds', false ) ) {
			$embeds = array_diff( $attachment_service->get_image_embeds( $post->post_content ), $ids );
		} else {
			$embeds = array();
		}

		// Get the gallery, removing existing ids.
		$gallery = array_diff( $attachment_service->get_gallery( $post ), $ids, $embeds );

		// Map the attachment ids to images' data structured for schema.org use.
		$images_with_sizes = array_filter(
			array_reduce(
				array_merge( $ids, $embeds, $gallery ),
				function ( $carry, $item ) {
					/*
					* @todo: we're not sure that we're getting attachment data here, we
					* should filter `false`s.
					*/

					$sources = array_merge(
						Wordlift_Image_Service::get_sources( $item ),
						array( wp_get_attachment_image_src( $item, 'full' ) )
					);

					$sources_with_image = array_filter(
						$sources,
						function ( $source ) {
							return ! empty( $source[0] );
						}
					);

					// Get the attachment data.
					// $attachment = wp_get_attachment_image_src( $item, 'full' );

					// var_dump( array( "sources-$item" => Wordlift_Image_Service::get_sources( $item ) ) );

					// Bail if image is not found.
					// In some cases, you can delete the image from the database
					// or from uploads dir, but the image id still exists as featured image
					// or in [gallery] shortcode.
					// if ( empty( $attachment[0] ) ) {
					if ( empty( $sources_with_image ) ) {
						return $carry;
					}

					// Merge the arrays.
					return array_merge(
						$carry,
						$sources_with_image
					);
				},
				// Initial array.

				array()
			)
		);

		// Refactor data as per schema.org specifications.
		$images = array_map(
			function ( $attachment ) {
				return Wordlift_Abstract_Post_To_Jsonld_Converter::set_image_size(
					array(
						'@type' => 'ImageObject',
						'url'   => $attachment[0],
					),
					$attachment
				);
			},
			$images_with_sizes
		);

		// Add images if present.
		if ( 0 < count( $images ) ) {
			$jsonld['image'] = $images;
		}

	}

	/**
	 * If the provided array of values contains only one value, then one single
	 * value is returned, otherwise the original array is returned.
	 *
	 * @param array $value An array of values.
	 *
	 * @return mixed|array A single value or the original array.
	 * @since 3.20.0 The function has been moved from {@link Wordlift_Entity_Post_To_Jsonld_Converter} to
	 *  {@link Wordlift_Abstract_Post_To_Jsonld_Converter}.
	 * @since  3.8.0
	 * @access private
	 */
	protected static function make_one( $value ) {

		return 1 === count( $value ) ? $value[0] : $value;
	}

	/**
	 * Process the provided array by adding the width / height if the values
	 * are available and are greater than 0.
	 *
	 * @param array $image The `ImageObject` array.
	 * @param array $attachment The attachment array.
	 *
	 * @return array The enriched `ImageObject` array.
	 * @since 3.14.0
	 */
	public static function set_image_size( $image, $attachment ) {

		// If you specify a "width" or "height" value you should leave out
		// 'px'. For example: "width":"4608px" should be "width":"4608".
		//
		// See https://github.com/insideout10/wordlift-plugin/issues/451.
		if ( isset( $attachment[1] ) && is_numeric( $attachment[1] ) && 0 < $attachment[1] ) {
			$image['width'] = $attachment[1];
		}

		if ( isset( $attachment[2] ) && is_numeric( $attachment[2] ) && 0 < $attachment[2] ) {
			$image['height'] = $attachment[2];
		}

		return $image;
	}

	/**
	 * Add data to the JSON-LD using the `custom_fields` array which contains the definitions of property
	 * for the post entity type.
	 *
	 * @param array   $jsonld The JSON-LD array.
	 * @param array   $fields The entity types field array.
	 * @param WP_Post $post The target {@link WP_Post} instance.
	 * @param array   $references The references array.
	 *
	 * @since 3.20.0 This code moved from the above function `convert`, used for entity types defined in
	 *  the {@link Wordlift_Schema_Service} class.
	 */
	protected function process_type_custom_fields( &$jsonld, $fields, $post, &$references, &$references_infos ) {

		// Set a reference to use in closures.
		$converter = $this;

		// Try each field on the entity.
		foreach ( $fields as $key => $value ) {

			// Get the predicate.
			$name = $this->relative_to_context( $value['predicate'] );

			// Get the value, the property service will get the right extractor
			// for that property.
			$value = $this->property_getter->get( $post->ID, $key, Object_Type_Enum::POST );

			if ( empty( $value ) ) {
				continue;
			}

			// Map the value to the property name.
			// If we got an array with just one value, we return that one value.
			// If we got a Wordlift_Property_Entity_Reference we get the URL.
			$jsonld[ $name ] = self::make_one(
				array_map(
					function ( $item ) use ( $converter, &$references, &$references_infos ) {

						if ( $item instanceof Wordlift_Property_Entity_Reference ) {

							$url = $item->get_url();

							// The refactored converters require the entity id.
							// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
							$references[] = $item->get_id();

							// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
							$references_infos[] = array( 'reference' => $item );

							return array( '@id' => $url );
						}

						return $converter->relative_to_context( $item );
					},
					$value
				)
			);

		}

	}

}
