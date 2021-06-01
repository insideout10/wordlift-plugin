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
			__( 'WordLift Entities Cloud', 'wordlift' ),
			array(
				'classname'   => 'wl_related_entities_cloud',
				'description' => __( 'Display entities related to the current post/entity in a tag cloud.', 'wordlift' ),
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

		return array( 'title' => sanitize_text_field( $new_instance['title'] ) );
	}

	/**
	 * @inheritdoc
	 */
	public function widget( $args, $instance ) {

		/*
		 * Use the shortcode to calculate the HTML required to show the cloud
		 * if there is no such html do not render the widget at all.
		 */
		$cloud_html = do_shortcode( '[wl_cloud]' );
		if ( empty( $cloud_html ) ) {
			return false;
		}

		// The widget title.
		$title = empty( $instance['title'] ) ? __( 'Related Entities', 'wordlift' ) : $instance['title'];

		// Standard filter all widgets should apply
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		echo $args['before_widget'];

		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		echo $cloud_html;
		echo $args['after_widget'];

	}

}
