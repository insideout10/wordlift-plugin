<?php
/**
 * Widgets: Abstract Widget.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/public
 */

/**
 * Define the {@link Wordlift_Widget} class.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/public
 */
abstract class Wordlift_Widget extends WP_Widget {

	/**
	 * @inheritdoc
	 */
	public function __construct( $id_base, $name, array $widget_options = array(), array $control_options = array() ) {
		parent::__construct( $id_base, $name, $widget_options, $control_options );

		// Initialize the Related Entities Cloud Widget.
		add_action( 'widgets_init', array( $this, 'widget_init' ) );

	}

	/**
	 * Register the related entities cloud widget
	 *
	 * @since 3.11.0
	 */
	public function widget_init() {

		register_widget( get_class( $this ) );

	}

}
