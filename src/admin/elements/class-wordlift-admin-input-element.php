<?php
/**
 * Elements: Input.
 *
 * Represents an Input text box.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the {@link Wordlift_Admin_Input_Element} class.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */
class Wordlift_Admin_Input_Element implements Wordlift_Admin_Element {

	/**
	 * Output the HTML for an input box type settings_page
	 *
	 * @param array $args An array with the following keys:
	 *                    Parameters controlling the result.
	 *
	 * @type string name The name attribute of the input element. Mandatory.
	 *
	 * @type string id    The id attribute of the input element. Optional.
	 * @type string id    The id attribute of the input element.
	 *                            Optional, randomly generated one is used if not supplied.
	 * @type string value    The value of the input element.
	 *                            Optional, defaults to empty string.
	 * @type bool readonly    Indicates whether the input is read only.
	 *                            Optional, defaults to read-write
	 * @type string css_class    The class attribute for the input element.
	 *                            If empty string no class attribute will be added.
	 *                            Optional, defaults to empty string.
	 * @type string description    The descriptio text to be displayed below the element.
	 *                            Can include some HTML element.
	 *                            If empty string no description will be displayed.
	 *                            Optional, defaults to empty string.
	 * @return $this|Wordlift_Admin_Element
	 * @since 3.21.0 added the ability to use a $type arg.
	 */
	public function render( $args ) {

		/*
		 * Parse the arguments and merge with default values.
		 * Name intentionally do not have a default as it has to be in SyncEvent
		 * with form handling code
		 */
		$pre_params = wp_parse_args(
			$args,
			array(
				'id'          => uniqid( 'wl-input-' ),
				'value'       => '',
				'readonly'    => false,
				'css_class'   => '',
				'description' => '',
				'pattern'     => false,
				'placeholder' => false,
			)
		);
		$params     = apply_filters( 'wl_admin_input_element_params', $pre_params );
		// allow different types of input - default to 'text'.
		$input_type = ! empty( $params['type'] ) ? $params['type'] : 'text';
		?>

		<input type="<?php echo esc_attr( $input_type ); ?>"
			   id="<?php echo esc_attr( $params['id'] ); ?>"
			   name="<?php echo esc_attr( $params['name'] ); ?>"
			   value="<?php echo esc_attr( $params['value'] ); ?>"
			<?php
			if ( $params['pattern'] ) {
				echo ' pattern="';
				echo esc_attr( $params['pattern'] );
				echo '"';
			}

			if ( $params['placeholder'] ) {
				echo ' placeholder="';
				echo esc_attr( $params['placeholder'] );
				echo '"';
			}

			if ( ! empty( $params['data'] ) && is_array( $params['data'] ) ) {
				foreach ( $params['data'] as $key => $value ) {
					echo 'data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
				}
			}

			if ( ! empty( $params['readonly'] ) ) {
				echo ' readonly="readonly"';
			}

			if ( ! empty( $params['css_class'] ) ) {
				echo ' class="';
				echo esc_attr( $params['css_class'] );
				echo '"';
			}
			?>
		/>
		<?php
		if ( ! empty( $params['description'] ) ) {
			?>
			<p><?php echo wp_kses( $params['description'], array( 'a' => array( 'href' => array() ) ) ); ?></p><?php } ?>

		<?php

		return $this;
	}

}
