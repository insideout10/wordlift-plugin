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
		       wp_generate_tag_cloud( $tags ) .
		       '</div>';
	}

	/**
	 * Find the related entities to the currently displayed post and
	 * calculate the "tags" for them as wp_generate_tag_cloud expects to get.
	 *
	 * @since 3.11.0
	 *
	 * @return array    Array of tags. Empty array in case we re not in a context
	 *                  of a post, or it has no related entities.
	 */
	public function get_related_entities_tags() {

		// Define the supported types list.
		$supported_types = array( 'post', Wordlift_Entity_Service::TYPE_NAME );

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

		foreach ( $related_entities as $entity_id ) {

			$connected_entities = count( wl_core_get_related_entity_ids( $entity_id, array( 'status' => 'publish' ) ) );
			$connected_posts    = count( wl_core_get_related_posts( $entity_id, array( 'status' => 'publish' ) ) );

			$tags[] = (object) array(
				'id'    => $entity_id,
				// Used to give a unique class on the tag.
				'name'  => get_the_title( $entity_id ),
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

}
