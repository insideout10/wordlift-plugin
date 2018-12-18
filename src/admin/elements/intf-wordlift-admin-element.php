<?php
/**
 * Elements: Element interface.
 *
 * The interface for Elements.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the {@link Wordlift_Admin_Element} interface.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */
interface Wordlift_Admin_Element {

	/**
	 * Render the element.
	 *
	 * @since 3.11.0
	 *
	 * @param array $args An array of parameters.
	 *
	 * @return \Wordlift_Admin_Element The element instance.
	 */
	public function render( $args );

}
