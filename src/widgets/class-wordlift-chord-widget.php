<?php
/**
 * This file contains the Chord Widget class.
 *
 * @package Wordlift
 */

/**
 * Class Wordlift_Chord_Widget
 */
class Wordlift_Chord_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		// widget actual processes.
		parent::__construct(
			'wl_chord_widget', // Base ID.
			__( 'Chord Widget', 'wordlift' ), // Name.
			array(
				'description' => __( 'The Chord Widget depicts the main topics of your blog in concise graph.', 'wordlift' ),
			) // Args.
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args widget args.
	 * @param array $instance widget instance.
	 */
	// @codingStandardsIgnoreLine Generic.CodeAnalysis.UnusedFunctionParameter.Found
	public function widget( $args, $instance ) {
		// outputs the content of the widget.
		echo do_shortcode( '[wl_chord global=true]' );
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options.
	 *
	 * @return string|void
	 */
	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function form( $instance ) {
		// outputs the options form on admin.
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options.
	 * @param array $old_instance The previous options.
	 *
	 * @return array|void
	 */
	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved.
	}
}

/**
 * Register Chord Widget
 *
 * @return void
 */
function wl_register_chord_widget() {

	register_widget( 'WordLift_Chord_Widget' );
}
