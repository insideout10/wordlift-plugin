<?php
/**
 * Widgets: Related Entities Cloud Widget.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/public
 */

/**
 * Define the {@link Wordlift_Related_Entities_Cloud_Widget} class.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/public
 */
class Wordlift_Related_Entities_Cloud_Widget extends Wordlift_Widget {

	/**
	 * Create an {@link Wordlift_Related_Entities_Cloud_Widget} instance.
	 *
	 * @since 3.11.0
	 */
	public function __construct() {
		parent::__construct(
			'wl_related_entities_cloud',
			__( 'Related Entities Cloud', 'wordlift' ),
			array(
				'classname'   => 'wl_related_entities_cloud',
				'description' => __( 'Display entities related to the current post/entity in a tag cloud', 'wordlift' ),
			)
		);

	}

	/**
	 * @inheritdoc
	 */
	public function form( $instance ) {
		$title_id          = $this->get_field_id( 'title' );
		$instance['title'] = ! empty( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		?>

		<p><label for="<?php echo esc_attr( $title_id ); ?>"><?php
				esc_html_e( 'Title:' ); ?></label>
			<input type="text" class="widefat"
			       id="<?php echo esc_attr( $title_id ); ?>"
			       name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
			       value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<?php

		return 'wl_related_entities_cloud_form';
	}

	/**
	 * @inheritdoc
	 */
	public function update( $new_instance, $old_instance ) {

		return array( 'title' => sanitize_text_field( $new_instance['title'] ), );
	}

	/**
	 * @inheritdoc
	 */
	public function widget( $args, $instance ) {

		// Define the supported types list.
		$supported_types = array( 'post', Wordlift_Entity_Service::TYPE_NAME, );

		// Show nothing if not on a post or entity page.
		if ( ! is_singular( $supported_types ) ) {
			return;
		}

		// Get the IDs of entities related to current post.
		$related_entities = wl_core_get_related_entity_ids( get_the_ID(), array( 'status' => 'publish' ) );

		// Bail out if there are no associated entities.
		if ( empty( $related_entities ) ) {
			return;
		}

		// The widget title.
		$title = empty( $instance['title'] ) ? __( 'Related Entities', 'wordlift' ) : $instance['title'];

		// Standard filter all widgets should apply
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

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

		/*
		 * Need to have the same class as the core tagcloud widget, to easily
		 * inherit its styling.
		 */

		echo $args['before_widget'];

		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		?>
		<div class="tagcloud wl-related-entities-cloud"><?php
			echo wp_generate_tag_cloud( $tags ); ?></div>
		<?php

		echo $args['after_widget'];

	}

}
