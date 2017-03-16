<?php

class Wordlift_Timeline_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		// widget actual processes
		parent::__construct(
			'wl_timeline_widget', // Base ID
			__( 'WordLift Timeline Widget', 'wordlift' ), // Name
			array( 'description' => __( 'Displays entities of type event using an interactive timeline.', 'wordlift' ), ) // Args
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		// outputs the content of the widget	
		echo do_shortcode( '[wl_timeline global=true]' );
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// outputs the options form on admin
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
	}
}

function wl_register_timeline_widget() {

	register_widget( 'WordLift_Timeline_Widget' );
}

add_action( 'widgets_init', 'wl_register_timeline_widget' );
add_filter( 'widget_text', 'do_shortcode' );
