<?php
/**
 * Shortcodes: Navigator Shorcode.
 *
 * @since      3.5.4
 * @package    Wordlift
 * @subpackage Wordlift/public
 */

/**
 * Define the {@link Wordlift_Navigator_Shortcode} class which provides the
 * `wl_navigator` implementation.
 *
 * @since      3.5.4
 * @package    Wordlift
 * @subpackage Wordlift/public
 */
class Wordlift_Navigator_Shortcode extends Wordlift_Shortcode {

	/**
	 * {@inheritdoc}
	 */
	const SHORTCODE = 'wl_navigator';

	/**
	 * {@inheritdoc}
	 */
	public function render( $atts ) {

		// Extract attributes and set default values.
		$shortcode_atts = shortcode_atts( array(
			'title'          => __( 'Related articles', 'wordlift' ),
			'with_carousel'  => true,
			'squared_thumbs' => false,
		), $atts );

		foreach (
			array(
				'with_carousel',
				'squared_thumbs',
			) as $att
		) {

			// See http://wordpress.stackexchange.com/questions/119294/pass-boolean-value-in-shortcode
			$shortcode_atts[ $att ] = filter_var(
				$shortcode_atts[ $att ], FILTER_VALIDATE_BOOLEAN
			);
		}

		/*
		 * Display the navigator only on the single post/page.
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/831
		 *
		 * @since 3.19.3 we're using `is_singular` instead of `is_single` to allow the navigator also on pages.
		 */
		// avoid building the widget when there is a list of posts.
		if ( ! is_singular() ) {
			return '';
		}

		$current_post = get_post();

		// Enqueue common shortcode scripts.
		$this->enqueue_scripts();

		// Use the registered style which define an optional dependency to font-awesome.
		//
		// @see https://github.com/insideout10/wordlift-plugin/issues/699
		//		wp_enqueue_style( 'wordlift-ui', dirname( plugin_dir_url( __FILE__ ) ) . '/css/wordlift-ui.min.css' );
		wp_enqueue_style( 'wordlift-ui' );

		$navigator_id = uniqid( 'wl-navigator-widget-' );

		wp_localize_script( 'wordlift-ui', 'wl_navigator_params', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'action'   => 'wl_navigator',
				'post_id'  => $current_post->ID,
				'attrs'    => $shortcode_atts,
			)
		);

		return "<div id='$navigator_id' class='wl-navigator-widget'></div>";
	}

}
