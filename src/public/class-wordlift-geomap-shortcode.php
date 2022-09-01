<?php
/**
 * Shortcodes: Geomap Shortcode.
 *
 * `wl_geomap` implementation.
 *
 * @since      3.5.4
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Define the {@link Wordlift_Geomap_Shortcode} class.
 *
 * @since      3.5.4
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
class Wordlift_Geomap_Shortcode extends Wordlift_Shortcode {

	const SHORTCODE = 'wl_geomap';

	/**
	 * Create a {@link Wordlift_Geomap_Shortcode} instance.
	 *
	 * @since 3.5.4
	 */
	public function __construct() {
		parent::__construct();

		// Hook to the `amp_post_template_css` to hide ourselves when in AMP
		// rendering.
		add_action( 'amp_post_template_css', array( $this, 'amp_post_template_css' ) );
		$this->register_block_type();

	}

	/**
	 * Render the shortcode.
	 *
	 * @param array $atts An array of shortcode attributes as set by the editor.
	 *
	 * @return string The output html code.
	 * @since 3.5.4
	 */
	public function render( $atts ) {

		// Extract attributes and set default values.
		$geomap_atts = shortcode_atts(
			array(
				'width'  => '100%',
				'height' => '300px',
				'global' => false,
			),
			$atts
		);

		// Get id of the post
		$post_id = get_the_ID();

		if ( $geomap_atts['global'] || $post_id === null ) {
			// Global geomap
			$geomap_id = 'wl_geomap_global';
			$post_id   = null;
		} else {
			// Post-specific geomap
			$geomap_id = 'wl_geomap_' . $post_id;
		}

		wl_enqueue_leaflet( true );

		// Use the registered style which define an optional dependency to font-awesome.
		//
		// @see https://github.com/insideout10/wordlift-plugin/issues/699
		// wp_enqueue_style( 'wordlift-ui', dirname( plugin_dir_url( __FILE__ ) ) . '/css/wordlift-ui.min.css' );
		wp_enqueue_style( 'wordlift-ui' );

		$this->enqueue_scripts();

		wp_localize_script(
			'wordlift-ui',
			'wl_geomap_params',
			array(
				'ajax_url'        => admin_url( 'admin-ajax.php' ),    // Global param
				'action'          => 'wl_geomap',            // Global param
				'wl_geomap_nonce' => wp_create_nonce( 'wl_geomap' ),
			)
		);

		// Escaping atts.
		$esc_id      = esc_attr( $geomap_id );
		$esc_width   = esc_attr( $geomap_atts['width'] );
		$esc_height  = esc_attr( $geomap_atts['height'] );
		$esc_post_id = esc_attr( $post_id );

		// Return HTML template.
		return "
<div class='wl-geomap'  id='$esc_id' data-post-id='$esc_post_id'
	style='width:$esc_width; height:$esc_height; background-color: gray;'>
</div>
";
	}

	private function register_block_type() {

		$scope = $this;

		add_action(
			'init',
			function () use ( $scope ) {
				if ( ! function_exists( 'register_block_type' ) ) {
					// Gutenberg is not active.
					return;
				}

				register_block_type(
					'wordlift/geomap',
					array(
						'editor_script'   => 'wl-block-editor',
						'render_callback' => function ( $attributes ) use ( $scope ) {
							$attr_code = '';
							foreach ( $attributes as $key => $value ) {
								$attr_code .= $key . '="' . htmlentities( $value ) . '" ';
							}

							return '[' . $scope::SHORTCODE . ' ' . $attr_code . ']';
						},
						'attributes'      => array(
							'width'       => array(
								'type'    => 'string',
								'default' => '100%',
							),
							'height'      => array(
								'type'    => 'string',
								'default' => '300px',
							),
							'global'      => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'preview'     => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'preview_src' => array(
								'type'    => 'string',
								'default' => WP_CONTENT_URL . '/plugins/wordlift/images/block-previews/geomap.png',
							),
						),
					)
				);
			}
		);
	}

	/**
	 * Customize the CSS when in AMP.
	 *
	 * See https://github.com/Automattic/amp-wp/blob/master/readme.md#custom-css
	 *
	 * @param object $amp_template The template.
	 *
	 * @since 3.13.0
	 */
    // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function amp_post_template_css( $amp_template ) {

		// Hide the `wl-geomap` when in AMP.
		?>
		.wl-geomap { display: none; }
		<?php
	}

}
