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
	 *
	 * @return $this|Wordlift_Admin_Element
	 */
	public function render( $args ) {

		/*
		 * Parse the arguments and merge with default values.
		 * Name intentionally do not have a default as it has to be in SyncEvent
		 * with form handling code
		 */
		$params = wp_parse_args( $args, array(
			'id'          => uniqid( 'wl-input-' ),
			'value'       => '',
			'readonly'    => false,
			'css_class'   => '',
			'description' => '',
		) );

		// Set the readonly and class attributes and the description.
		$readonly    = $params['readonly'] ? ' readonly="readonly"' : '';
		$css_class   = ! empty( $params['css_class'] ) ? ' class="' . esc_attr( $params['css_class'] ) . '"' : '';
		$description = ! empty( $params['description'] ) ? '<p>' . wp_kses( $params['description'], array( 'a' => array( 'href' => array() ) ) ) . '</p>' : '';

		?>

		<input type="text"
		       id="<?php echo esc_attr( $params['id'] ); ?>"
		       name="<?php echo esc_attr( $params['name'] ); ?>"
		       value="<?php echo esc_attr( $params['value'] ); ?>"
			<?php echo $readonly; ?>
			<?php echo $css_class; ?>
		/>
		<?php echo $description; ?>

		<?php

		return $this;
	}

}
