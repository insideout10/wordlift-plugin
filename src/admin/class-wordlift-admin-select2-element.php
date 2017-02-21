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
class Wordlift_Admin_Select2_Element implements Wordlift_Admin_Element {

	/**
	 * @inheritdoc
	 */
	public function render( $args ) {

		// Enqueue select2 library js and css.
		// Underscore is needed for Select2's `templateResult` and `templateSelection` templates.
		wp_enqueue_script( 'wordlift-select2', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/select2/js/select2.full' . ( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ? '.min' : '' ) . '.js', array(
			'jquery',
			'underscore',
		), '4.0.3' );
		wp_enqueue_style( 'wordlift-select2', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/select2/css/select2' . ( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ? '.min' : '' ) . '.css', array(), '4.0.3' );


		// Parse the arguments and merge with default values.
		$params = wp_parse_args( $args, array(
			'id'                 => uniqid( 'wl-input-' ),
			'name'               => uniqid( 'wl-input-' ),
			'value'              => null,
			'options'            => array(),
			'description'        => false,
			'data'               => array(),
			'template-result'    => '<%= text %>',
			'template-selection' => '<%= text %>',
		) );

		$description = $params['description'] ? '<p>' . wp_kses( $params['description'], array( 'a' => array( 'href' => array() ) ) ) . '</p>' : '';

		// For data attributes we define our own "namespace" `wl-select2-` to avoid
		// Select2 preloading configuration on its own. In fact we need to support
		// underscore templates.
		?>
		<select id="<?php echo esc_attr( $params['id'] ); ?>"
		        name="<?php echo esc_attr( $params['name'] ); ?>"
		        class="wl-select2-element"
		        data-wl-select2-data="<?php echo esc_attr( json_encode( $params['data'] ) ); ?>"
		        data-wl-select2-template-result="<?php echo esc_attr( $params['template-result'] ); ?>"
		        data-wl-select2-template-selection="<?php echo esc_attr( $params['template-selection'] ); ?>">
			<?php foreach ( $params['options'] as $value => $label ) { ?>
				<option value="<?php echo esc_attr( $value ); ?>"
					<?php selected( $params['value'], $value ) ?>><?php
					echo esc_html( $label ); ?></option>
			<?php } ?>
		</select>
		<?php echo $description; ?>

		<?php

		return $this;
	}

}
