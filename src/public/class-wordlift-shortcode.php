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
	 * The {@link Wordlift} plugin instance.
	 *
	 * @since  3.12.0
	 * @access protected
	 * @var \Wordlift $plugin The {@link Wordlift} plugin instance.
	 */
	protected $plugin;

	/**
	 * Create a shortcode instance by registering the shortcode with the render
	 * function.
	 *
	 * @since 3.5.4
	 *
	 * @param \Wordlift $plugin The {@link Wordlift} plugin instance.
	 */
	public function __construct( $plugin ) {

		$this->plugin = $plugin;

		add_shortcode( static::SHORTCODE, array( $this, 'render' ) );

	}

	/**
	 * Render the shortcode.
	 *
	 * @since 3.5.4
	 *
	 * @param array $atts An array of shortcode attributes as set by the editor.
	 *
	 * @return string The output html code.
	 */
	public abstract function render( $atts );

	/**
	 * Enqueue scripts. Called by the shortcode implementations in their render
	 * method.
	 *
	 * @since 3.5.4
	 */
	protected function enqueue_scripts() {

		wp_enqueue_script( 'angularjs', 'https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.3.11/angular.min.js' );
		wp_enqueue_script( 'angularjs-touch', 'https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.3.11/angular-touch.min.js', array( 'angularjs' ) );
		wp_enqueue_script( 'wordlift-ui', dirname( plugin_dir_url( __FILE__ ) ) . '/js/wordlift-ui' . ( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ? '.min' : '' ) . '.js', array(
			'jquery',
			'angularjs',
			'angularjs-touch',
		), $this->plugin->get_version() );

	}

}
