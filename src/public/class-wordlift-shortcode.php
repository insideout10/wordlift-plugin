<?php

/**
 * A base abstract class for shortcodes which registers the shortcode binding it
 * to the render method and provides a function to enqueue the scripts.
 *
 * @since 3.5.4
 */
abstract class Wordlift_Shortcode {

	/**
	 * The shortcode, set by extending classes.
	 */
	const SHORTCODE = null;

	/**
	 * Create a shortcode instance by registering the shortcode with the render
	 * function.
	 *
	 * @since 3.5.4
	 */
	public function __construct() {

		add_shortcode( static::SHORTCODE, array( $this, 'render' ) );

	}

	/**
	 * Render the shortcode.
	 *
	 * @param array $atts An array of shortcode attributes as set by the editor.
	 *
	 * @return string The output html code.
	 * @since 3.5.4
	 */
	abstract public function render( $atts );

	/**
	 * Enqueue scripts. Called by the shortcode implementations in their render
	 * method.
	 *
	 * @since 3.5.4
	 */
	protected function enqueue_scripts() {

		wp_enqueue_script( 'angularjs', plugin_dir_url( __DIR__ ) . '/js/3rdparty/angular.min.js', array(), '1.3.11', false );
		wp_enqueue_script( 'angularjs-touch', plugin_dir_url( __DIR__ ) . '/js/3rdparty/angular-touch.min.js', array( 'angularjs' ), '1.3.11', false );
		wp_enqueue_script(
			'wordlift-ui',
			dirname( plugin_dir_url( __FILE__ ) ) . '/js/wordlift-ui' . ( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ? '.min' : '' ) . '.js',
			array(
				'jquery',
				'angularjs',
				'angularjs-touch',
			),
			WORDLIFT_VERSION,
			false
		);

	}

}
