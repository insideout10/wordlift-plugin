<?php
/**
 * Elements: Radio Input.
 *
 * Represents an Radio Input text box.
 *
 * @since      3.13.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the {@link Wordlift_Admin_Radio_Input_Element} class.
 *
 * @since      3.13.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */
class Wordlift_Admin_Radio_Input_Element implements Wordlift_Admin_Element {

	/**
	 * Output the HTML for an input box type settings_page.
	 *
	 * @param array $args {
	 *                           An array of arguments.
	 *
	 * @type string $name The name attribute of the input element. Mandatory.
	 * @type string $id The id attribute of the input element. Optional,
	 *                           randomly generated one is used if not supplied.
	 * @type string $value The value of the input element. Optional, defaults
	 *                           to empty string.
	 * @type string $css_class The class attribute for the input element. If empty
	 *                           string no class attribute will be added. Optional,
	 *                           defaults to empty string.
	 * @type string $description The description text to be displayed below the element.
	 *                           Can include some HTML element. If empty string no
	 *                           description will be displayed. Optional, defaults to
	 *                           empty string.
	 * }
	 * @return $this|Wordlift_Admin_Element
	 * @since      3.13.0
	 */
	public function render( $args ) {
		/*
		 * Parse the arguments and merge with default values.
		 * Name intentionally do not have a default as it has to be in SyncEvent
		 * with form handling code
		 */
		$params = wp_parse_args(
			$args,
			array(
				'id'          => uniqid( 'wl-input-' ),
				'value'       => '',
				'css_class'   => '',
				'description' => '',
			)
		);

		// Set the readonly and class attributes and the description.
		$value = $params['value'];
		?>

		<input type="radio" id="<?php echo esc_attr( $params['id'] ); ?>"
			   name="<?php echo esc_attr( $params['name'] ); ?>"
			   value="yes" 
			   <?php
				if ( ! empty( $params['css_class'] ) ) {
					?>
					 class="<?php echo esc_attr( $params['css_class'] ); ?>" <?php } ?>
			<?php checked( $value, 'yes' ); ?>
		/> Yes
		<input type="radio" id="<?php echo esc_attr( $params['id'] ); ?>"
			   name="<?php echo esc_attr( $params['name'] ); ?>"
			   value="no" 
			   <?php
				if ( ! empty( $params['css_class'] ) ) {
					?>
					 class="<?php echo esc_attr( $params['css_class'] ); ?>" <?php } ?>
			<?php checked( $value, 'no' ); ?>
		/> No
		<?php if ( ! empty( $params['description'] ) ) { ?>
			<p><?php echo wp_kses( $params['description'], array( 'a' => array( 'href' => array() ) ) ); ?></p><?php } ?>
		<?php

		return $this;
	}

}
