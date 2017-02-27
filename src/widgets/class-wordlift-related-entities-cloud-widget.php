<?php
/**
 * The Related entities tag cloud code.
 *
 * @since   3.11.0
 * @package WordLift.
 */

/**
 * Control the related entities cloud tag widget
 *
 * @since 3.11.0
 */
class Wordlift_Related_Entities_Cloud_Widget extends WP_Widget {

	public function __construct() {

		parent::__construct('wl_related_entities_cloud', __( 'Related Entities Cloud', 'wordlift' ), array(
			'classname' => 'wl_related_entities_cloud',
			'description' => __( 'Display entities related to the current post/entity in a tag cloud', 'wordlift' ),
		) );
	}

	/**
	 * Outputs the settings form.
	 *
	 * @since 3.11.0
	 * @access public
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$title_id = $this->get_field_id( 'title' );
		$instance['title'] = ! empty( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';

		echo '<p><label for="' . $title_id . '">' . __( 'Title:' ) . '</label>
			<input type="text" class="widefat" id="' . $title_id . '" name="' . $this->get_field_name( 'title' ) . '" value="' . $instance['title'] . '" />
		</p>';
	}

	/**
	 * Handles updating settings for the current Tag Cloud widget instance.
	 *
	 * @since 3.11.0
	 * @access public
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		return $instance;
	}

	function widget( $args, $instance ) {

		// Show nothing if not on a post or entity page.
		if ( ! is_singular( array( 'post', Wordlift_Entity_Service::TYPE_NAME ) ) ) {
			return;
		}

		// Get the IDs of entities related to current post.
		$related_entities = wl_core_get_related_entity_ids( get_the_ID(), array( 'status' => 'publish' ) );

		// Bail out if there are no associated entities.
		if ( empty( $related_entities ) ) {
			return;
		}

		$title = empty( $instance['title'] ) ? __( 'Related Entities', 'wordlift' ) : $instance['title'];

		// standard filter all widgets should apply
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		/*
		 * Create an array of "tags" to feed to wp_generate_tag_cloud.
		 * Use the number of posts and entities connected to the entity as a weight.
		 */
		$tags = array();
		foreach ( $related_entities as $entity_id ) {
			 $connected_entities = count( wl_core_get_related_entity_ids( $entity_id, array( 'status' => 'publish' ) ) );
			 $connected_posts = count( wl_core_get_related_posts( $entity_id, array( 'status' => 'publish' ) ) );

			 $tags[] = (object) array(
				 'id' => $entity_id,	// Used to give a unique class on the tag.
			 	 'name' => get_the_title( $entity_id ),	// The text of the tag.
				 'slug' => get_the_title( $entity_id ),	// Required but not seem to be relevant
				 'link' => get_permalink( $entity_id ),	// the url the tag links to.
				 'count' => $connected_entities + $connected_posts, // The weight.
			 );

		}

		$cloud = wp_generate_tag_cloud( $tags );
		
		/*
		 * Need to have the same class as the core tagcloud widget, to easily
		 * inherit its styling.
		 */
		echo '<div class="tagcloud wl_related_entities_cloud">';

		echo $cloud;

		echo "</div>\n";
		echo $args['after_widget'];

	}
}

/**
 * Register the related entities cloud widget
 *
 * @since 3.11.0
 */
function wl_register_entity_cloud_widget() {

	register_widget( 'Wordlift_Related_Entities_Cloud_Widget' );
}

add_action( 'widgets_init', 'wl_register_entity_cloud_widget' );
