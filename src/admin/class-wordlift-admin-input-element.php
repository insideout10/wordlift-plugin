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
	 * @inheritdoc
	 */
	public function render( $args ) {

		// Parse the arguments and merge with default values.
		$params = wp_parse_args( $args, array(
			'id'          => uniqid( 'wl-input-' ),
			'name'        => uniqid( 'wl-input-' ),
			'value'       => '',
			'readonly'    => false,
			'css_class'   => false,
			'description' => false,
		) );

		// Set the readonly and class attributes and the description.
		$readonly    = $params['readonly'] ? ' readonly="readonly"' : '';
		$css_class   = $params['css_class'] ? ' class="' . esc_attr( $params['css_class'] ) . '"' : '';
		$description = $params['description'] ? '<p>' . wp_kses( $params['description'], array( 'a' => array( 'href' => array() ) ) ) . '</p>' : '';

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
