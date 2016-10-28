<?php

/**
 * The `wl_geomap` implementation.
 *
 * @since 3.5.4
 */
class Wordlift_Geomap_Shortcode extends Wordlift_Shortcode {

	const SHORTCODE = 'wl_geomap';

	/**
	 * Render the shortcode.
	 *
	 * @since 3.5.4
	 *
	 * @param array $atts An array of shortcode attributes as set by the editor.
	 *
	 * @return string The output html code.
	 */
	public function render( $atts ) {

		// Extract attributes and set default values.
		$geomap_atts = shortcode_atts( array(
			'width'  => '100%',
			'height' => '300px',
			'global' => FALSE
		), $atts );

		// Get id of the post
		$post_id = get_the_ID();

		if ( $geomap_atts['global'] || is_null( $post_id ) ) {
			// Global geomap
			$geomap_id = 'wl_geomap_global';
			$post_id   = NULL;
		} else {
			// Post-specific geomap
			$geomap_id = 'wl_geomap_' . $post_id;
		}

		// Add leaflet css and library.
		wp_enqueue_style(
			'leaflet',
			dirname( plugin_dir_url( __FILE__ ) ) . '/bower_components/leaflet/dist/leaflet.css'
		);
		wp_enqueue_script(
			'leaflet',
			dirname( plugin_dir_url( __FILE__ ) ) . '/bower_components/leaflet/dist/leaflet.js'
		);

		// Add wordlift-ui css and library.
		wp_enqueue_style( 'wordlift-ui-css', dirname( plugin_dir_url( __FILE__ ) ) . '/css/wordlift-ui.min.css' );

		$this->enqueue_scripts();

		wp_localize_script( 'wordlift-ui', 'wl_geomap_params', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),    // Global param
			'action'   => 'wl_geomap'            // Global param
		) );

		// Escaping atts.
		$esc_class   = esc_attr( 'wl-geomap' );
		$esc_id      = esc_attr( $geomap_id );
		$esc_width   = esc_attr( $geomap_atts['width'] );
		$esc_height  = esc_attr( $geomap_atts['height'] );
		$esc_post_id = esc_attr( $post_id );

		// Return HTML template.
		return <<<EOF
<div class="$esc_class" 
	id="$esc_id"
	data-post-id="$esc_post_id"
	style="width:$esc_width;
        height:$esc_height;
        background-color:gray
        ">
</div>
EOF;
	}

}
