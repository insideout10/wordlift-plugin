<?php

/**
 * The `wl_chord` shortcode.
 *
 * @since 3.5.4
 */
class Wordlift_Chord_Shortcode extends Wordlift_Shortcode {

	/**
	 * {@inheritdoc}
	 */
	const SHORTCODE = 'wl_chord';

	/**
	 * {@inheritdoc}
	 */
	public function render( $atts ) {

		//extract attributes and set default values
		$chord_atts = shortcode_atts( array(
			'width'      => '100%',
			'height'     => '500px',
			'main_color' => '000',
			'depth'      => 2,
			'global'     => false,
		), $atts );

		if ( $chord_atts['global'] ) {

			if ( null === $post_id = wl_shortcode_chord_most_referenced_entity_id() ) {
				return "WordLift Chord: no entities found.";
			}

			$widget_id = 'wl_chord_global';

			// Use the provided height if any, otherwise use a default of 200px.
			//
			// See https://github.com/insideout10/wordlift-plugin/issues/443
			$chord_atts['height'] = isset( $chord_atts['height'] ) ? $chord_atts['height'] : '200px';

		} else {

			$post_id   = get_the_ID();
			$widget_id = 'wl_chord_' . $post_id;

		}

		// Adding css
		wp_enqueue_style( 'wordlift-ui', dirname( plugin_dir_url( __FILE__ ) ) . '/css/wordlift-ui.min.css' );

		// Adding javascript code
		wp_enqueue_script( 'd3', dirname( plugin_dir_url( __FILE__ ) ) . '/bower_components/d3/d3.min.js' );

		$this->enqueue_scripts();

		wp_localize_script( 'wordlift-ui', 'wl_chord_params', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'action'   => 'wl_chord',
			)
		);

		// Escaping atts.
		$esc_class  = esc_attr( 'wl-chord' );
		$esc_id     = esc_attr( $widget_id );
		$esc_width  = esc_attr( $chord_atts['width'] );
		$esc_height = esc_attr( $chord_atts['height'] );

		$esc_post_id    = esc_attr( $post_id );
		$esc_depth      = esc_attr( $chord_atts['depth'] );
		$esc_main_color = esc_attr( $chord_atts['main_color'] );

		// Building template.
		// TODO: in the HTML code there are static CSS rules. Move them to the CSS file.
		return <<<EOF
<div class="$esc_class" 
	id="$esc_id"
	data-post-id="$esc_post_id"
    data-depth="$esc_depth"
    data-main-color="$esc_main_color"
	style="width:$esc_width;
        height:$esc_height;
        background-color:white;
        margin-top:10px;
        margin-bottom:10px">
</div>
EOF;
	}

}