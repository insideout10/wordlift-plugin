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
	 * @since 3.2.0
	 *
	 * @param string $element_id The button element id.
	 * @param string $label The button (translated) label.
	 *
	 * @return string The button HTML code.
	 */
	public function get_button_html( $element_id, $label ) {

		return sprintf( self::BUTTON_HTML, $element_id, esc_html( $label ) );
	}

	/**
	 * Echo the button HTML.
	 *
	 * @since 3.2.0
	 *
	 * @param string $element_id The button element id.
	 * @param string $label The button (translated) label.
	 *
	 * @return string The button HTML code.
	 */
	public function print_button( $element_id, $label ) {

		echo( $this->get_button_html( $element_id, $label ) );

	}

	/**
	 * Get the HTML code for a template tag.
	 *
	 * @since 3.2.0
	 *
	 * @param string $element_id The element id.
	 * @param string $body The element content.
	 *
	 * @return string The HTML code.
	 */
	public function get_template_html( $element_id, $body ) {

		return sprintf( self::TEMPLATE_HTML, $element_id, $body );
	}

	/**
	 * Echo the HTML code for a template tag.
	 *
	 * @since 3.2.0
	 *
	 * @param string $element_id The element id.
	 * @param string $body The element content.
	 *
	 * @return string The HTML code.
	 */
	public function print_template( $element_id, $body ) {

		echo( $this->get_template_html( $element_id, $body ) );

	}

}
