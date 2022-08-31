<?php
/**
 * Elements: Language Select.
 *
 * An Select element with the list of languages.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the {@link Wordlift_Admin_Select_Element} class.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */
class Wordlift_Admin_Select2_Element extends Wordlift_Admin_Select_Element {

	/**
	 * @inheritdoc
	 */
	public function render_options( $params ) {
		// Loop through all params and add the options.
		foreach ( $params['options'] as $value => $label ) :
			?>
			<option
					value="<?php echo esc_attr( $value ); ?>"
				<?php selected( $params['value'], $value ); ?>
			>
				<?php echo esc_html( $label ); ?>
			</option>
			<?php
		endforeach;
	}

	/**
	 * @inheritdoc
	 */
	protected function enqueue_resources() {
		// Enqueue select2 library js and css.
		// Underscore is needed for Select2's `templateResult` and `templateSelection` templates.
		wp_enqueue_script(
			'wordlift-select2',
			plugin_dir_url( __DIR__ ) . 'js/select2/js/select2' . ( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ? '.min' : '' ) . '.js',
			array(
				'jquery',
				'underscore',
			),
			'4.0.3',
			false
		);
		wp_enqueue_style( 'wordlift-select2', plugin_dir_url( __DIR__ ) . 'js/select2/css/select2' . ( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ? '.min' : '' ) . '.css', array(), '4.0.3' );
	}

}
