<?php

class Wordlift_Chord_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		// widget actual processes
		parent::__construct(
			'wl_chord_widget', // Base ID
			__( 'Chord Widget', 'wordlift' ), // Name
			array( 'description' => __( 'The Chord Widget depicts the main topics of your blog in concise graph.', 'wordlift' ), ) // Args
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
		echo do_shortcode( '[wl_chord global=true]' );
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 *
	 * @return string|void
	 */
	public function form( $instance ) {
		// outputs the options form on admin
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 *
	 * @return array|void
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
	}
}

function wl_register_chord_widget() {

	register_widget( 'WordLift_Chord_Widget' );
}

add_action( 'widgets_init', 'wl_register_chord_widget' );
add_filter( 'widget_text', 'do_shortcode' );
