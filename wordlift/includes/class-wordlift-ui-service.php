<?php

/**
 * Provides functions to draw the UI.
 *
 * @since 3.2.0
 */
class Wordlift_UI_Service {

	/**
	 * The button element HTML code.
	 *
	 * @since 3.2.0
	 */
	const BUTTON_HTML = '<a id="%s" class="button wl-button">%s</a>';

	/**
	 * The template HTML code.
	 *
	 * @since 3.2.0
	 */
	const TEMPLATE_HTML = '<script id="%s" type="text/template">%s</script>';

	/**
	 * Get the button HTML.
	 *
	 * @param string $element_id The button element id.
	 * @param string $label The button (translated) label.
	 *
	 * @return string The button HTML code.
	 * @since 3.2.0
	 */
	public static function get_button_html( $element_id, $label ) {

		return sprintf( self::BUTTON_HTML, $element_id, esc_html( $label ) );
	}

	/**
	 * Echo the button HTML.
	 *
	 * @param string $element_id The button element id.
	 * @param string $label The button (translated) label.
	 *
	 * @since 3.2.0
	 */
	public static function print_button( $element_id, $label ) {

		echo wp_kses(
			self::get_button_html( $element_id, $label ),
			array(
				'a' => array(
					'id'    => array(),
					'class' => array(),
				),
			)
		);

	}

	/**
	 * Get the HTML code for a template tag.
	 *
	 * @param string $element_id The element id.
	 * @param string $body The element content.
	 *
	 * @return string The HTML code.
	 * @since 3.2.0
	 */
	public static function get_template_html( $element_id, $body ) {

		return sprintf( self::TEMPLATE_HTML, $element_id, $body );
	}

	/**
	 * Echo the HTML code for a template tag.
	 *
	 * @param string $element_id The element id.
	 * @param string $body The element content.
	 *
	 * @since 3.2.0
	 */
	public static function print_template( $element_id, $body ) {

		echo wp_kses(
			self::get_template_html( $element_id, $body ),
			self::get_template_allowed_html()
		);
	}

	/**
	 * @return array[]
	 */
	public static function get_template_allowed_html() {
		return array(
			'div'    => array( 'class' => array() ),
			'label'  => array(
				'class' => array(),
				'id'    => array(),
				'for'   => array(),
			),
			'input'  => array(
				'name'  => array(),
				'size'  => array(),
				'value' => array(),
				'id'    => array(),
				'type'  => array(),
			),
			'button' => array( 'class' => array() ),
			'script' => array(
				'id'   => array(),
				'type' => array(),
			),
		);
	}

}
