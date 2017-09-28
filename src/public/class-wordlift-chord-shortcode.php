<?php
/**
 * Shortcodes: Chord Shortcode.
 *
 * @since      3.5.4
 * @package    Wordlift
 * @subpackage Wordlift/public
 */

/**
 * The `wl_chord` shortcode.
 *
 * @since      3.5.4
 * @package    Wordlift
 * @subpackage Wordlift/public
 */
class Wordlift_Chord_Shortcode extends Wordlift_Shortcode {

	/**
	 * {@inheritdoc}
	 */
	const SHORTCODE = 'wl_chord';

	/**
	 * Create a {@link Wordlift_Chord_Shortcode} instance.
	 *
	 * @since      3.5.4
	 */
	public function __construct() {
		parent::__construct();

		// Hook to the `amp_post_template_css` to hide ourselves when in AMP
		// rendering.
		add_action( 'amp_post_template_css', array(
			$this,
			'amp_post_template_css',
			10,
			0,
		) );

	}

	/**
	 * Render shordcode.
	 *
	 * @param array $atts shortcode attributes.
	 *
	 * @return string The HTML output.
	 */
	public function render( $atts ) {

		// extract attributes and set default values.
		$chord_atts = shortcode_atts( array(
			'width'      => '100%',
			'height'     => '500px',
			'main_color' => '000',
			'depth'      => 2,
			'global'     => false,
		), $atts );

		if ( $chord_atts['global'] ) {

			$post_id = wl_shortcode_chord_most_referenced_entity_id();

			if ( null === $post_id ) {
				return 'WordLift Chord: no entities found.';
			}

			// Use the provided height if any, otherwise use a default of 200px.
			//
			// See https://github.com/insideout10/wordlift-plugin/issues/443.
			$chord_atts['height'] = isset( $chord_atts['height'] ) ? $chord_atts['height'] : '200px';

		} else {
			$post_id = get_the_ID();
		}

		// Adding css.
		wp_enqueue_style( 'wordlift-ui', dirname( plugin_dir_url( __FILE__ ) ) . '/css/wordlift-ui.min.css' );

		// Adding javascript code.
		wp_enqueue_script( 'd3', dirname( plugin_dir_url( __FILE__ ) ) . '/bower_components/d3/d3.min.js' );

		$this->enqueue_scripts();

		wp_localize_script( 'wordlift-ui', 'wl_chord_params', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'action'   => 'wl_chord',
		) );

		// Escaping atts.
		$esc_class  = esc_attr( 'wl-chord' );
		$esc_id     = esc_attr( uniqid( 'wl-chord-' ) );
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

	/**
	 * Customize the CSS when in AMP.
	 *
	 * See https://github.com/Automattic/amp-wp/blob/master/readme.md#custom-css
	 *
	 * @since 3.14.0
	 */
	public function amp_post_template_css() {

		// Hide the `wl-chord` when in AMP.
		?>
		.wl-chord { display: none; }
		<?php
	}

}
