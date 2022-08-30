<?php
/**
 * Shortcodes: Entities Cloud Shortcode, `wl_cloud`.
 *
 * @since      3.12.0
 * @package    Wordlift
 * @subpackage Wordlift/public
 */

/**
 * The `wl_cloud` shortcode.
 *
 * @since      3.12.0
 * @package    Wordlift
 * @subpackage Wordlift/public
 */
class Wordlift_Related_Entities_Cloud_Shortcode extends Wordlift_Shortcode {

	/**
	 * {@inheritdoc}
	 */
	const SHORTCODE = 'wl_cloud';

	/**
	 * The {@link Wordlift_Relation_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Relation_Service $relation_service The {@link Wordlift_Relation_Service} instance.
	 */
	private $relation_service;
	/**
	 * @var Wordlift_Entity_Service
	 */
	private $entity_service;

	/**
	 * Create a {@link Wordlift_Related_Entities_Cloud_Shortcode} instance.
	 *
	 * @param \Wordlift_Relation_Service $relation_service The {@link Wordlift_Relation_Service} instance.
	 * @param \Wordlift_Entity_Service   $entity_service
	 *
	 * @since 3.15.0
	 */
	public function __construct( $relation_service, $entity_service ) {
		parent::__construct();

		$this->relation_service = $relation_service;

		$this->entity_service = $entity_service;

		$this->register_block_type();

	}

	/**
	 * {@inheritdoc}
	 */
	public function render( $atts ) {

		$tags = $this->get_related_entities_tags();

		// Bail out if there are no associated entities.
		if ( empty( $tags ) ) {
			return '';
		}

		/*
		 * Since the output is use in the widget as well, we need to have the
		 * same class as the core tagcloud widget, to easily inherit its styling.
		 */

		return '<div class="tagcloud wl-related-entities-cloud">' .
			   wp_generate_tag_cloud( $tags, $atts ) .
			   '</div>';
	}

	private function register_block_type() {

		$scope = $this;

		add_action(
			'init',
			function () use ( $scope ) {
				if ( ! function_exists( 'register_block_type' ) ) {
					// Gutenberg is not active.
					return;
				}

				register_block_type(
					'wordlift/cloud',
					array(
						'editor_script'   => 'wl-block-editor',
						'render_callback' => function ( $attributes ) use ( $scope ) {
							$attr_code = '';
							foreach ( $attributes as $key => $value ) {
								$attr_code .= $key . '="' . htmlentities( $value ) . '" ';
							}

							return '[' . $scope::SHORTCODE . ' ' . $attr_code . ']';
						},
						'attributes'      => array(
							'preview'     => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'preview_src' => array(
								'type'    => 'string',
								'default' => WP_CONTENT_URL . '/plugins/wordlift/images/block-previews/cloud.png',
							),
						),
					)
				);
			}
		);
	}

	/**
	 * Find the related entities to the currently displayed post and
	 * calculate the "tags" for them as wp_generate_tag_cloud expects to get.
	 *
	 * @return array    Array of tags. Empty array in case we re not in a context
	 *                  of a post, or it has no related entities.
	 * @since 3.11.0
	 */
	public function get_related_entities_tags() {

		// Define the supported types list.
		$supported_types = Wordlift_Entity_Service::valid_entity_post_types();

		// Show nothing if not on a post or entity page.
		if ( ! is_singular( $supported_types ) ) {
			return array();
		}

		// Get the IDs of entities related to current post.
		$related_entities = wl_core_get_related_entity_ids( get_the_ID(), array( 'status' => 'publish' ) );

		// Bail out if there are no associated entities.
		if ( empty( $related_entities ) ) {
			return array();
		}

		/*
		 * Create an array of "tags" to feed to wp_generate_tag_cloud.
		 * Use the number of posts and entities connected to the entity as a weight.
		 */
		$tags = array();

		foreach ( array_unique( $related_entities ) as $entity_id ) {

			$connected_entities = count( wl_core_get_related_entity_ids( $entity_id, array( 'status' => 'publish' ) ) );
			$connected_posts    = count( $this->relation_service->get_article_subjects( $entity_id, '*', null, 'publish' ) );
			/**
			 * @since 3.31.5
			 * if synonym exists, use it instead of entity name.
			 */
			$synonyms    = $this->get_synonyms( $entity_id );
			$entity_name = count( $synonyms ) > 0 ? $synonyms[0] : get_the_title( $entity_id );

			$tags[] = (object) array(
				'id'    => $entity_id,
				// Used to give a unique class on the tag.
				'name'  => $entity_name,
				// The text of the tag.
				'slug'  => get_the_title( $entity_id ),
				// Required but not seem to be relevant
				'link'  => get_permalink( $entity_id ),
				// the url the tag links to.
				'count' => $connected_entities + $connected_posts,
				// The weight.
			);

		}

		return $tags;
	}

	private function get_synonyms( $entity_id ) {
		return $this->entity_service->get_alternative_labels( $entity_id );
	}

}
