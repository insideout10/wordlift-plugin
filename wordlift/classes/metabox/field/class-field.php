<?php
/**
 * @since 3.31.6
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
namespace Wordlift\Metabox\Field;

interface Field {
	/**
	 * Return nonce HTML.
	 *
	 * Overwrite this method in a child class to obtain custom behaviour.
	 */
	public function html_nonce();

	/**
	 * Verify nonce.
	 *
	 * Overwrite this method in a child class to obtain custom behaviour.
	 *
	 * @return bool Nonce verification.
	 */
	public function verify_nonce();

	/**
	 * Load data from DB and store the resulting array in $this->data.
	 *
	 * Overwrite this method in a child class to obtain custom behaviour.
	 */
	public function get_data();

	/**
	 * Sanitizes data before saving to DB. Default sanitization trashes empty
	 * values.
	 *
	 * Stores the sanitized values into $this->data so they can be later processed.
	 * Overwrite this method in a child class to obtain custom behaviour.
	 *
	 * @param array $values Array of values to be sanitized and then stored into
	 *                      $this->data.
	 *
	 * @return array | string Return sanitized data.
	 */
	public function sanitize_data( $values );

	/**
	 * Sanitize a single value. Called from $this->sanitize_data. Default
	 * sanitization excludes empty values.
	 *
	 * Overwrite this method in a child class to obtain custom behaviour.
	 *
	 * @param string $value The value to sanitize.
	 *
	 * @return mixed Returns sanitized value, or null.
	 */
	public function sanitize_data_filter( $value );

	/**
	 * Save data to DB.
	 *
	 * Overwrite this method in a child class to obtain custom behaviour.
	 *
	 * @param array $values Array of values to be sanitized and then stored into $this->data.
	 */
	public function save_data( $values );

	/**
	 * Returns the HTML tag that will contain the Field. By default the we
	 * return a <div> with data- attributes on cardinality and expected types.
	 *
	 * It is useful to provide data- attributes for the JS scripts.
	 *
	 * Overwrite this method in a child class to obtain custom behaviour.
	 */
	public function html_wrapper_open();

	/**
	 * Returns Field HTML (nonce included).
	 *
	 * Overwrite this method (or methods called from this method) in a child
	 * class to obtain custom behaviour.
	 *
	 * The HTML fragment includes the following parts:
	 * * html wrapper open.
	 * * heading.
	 * * nonce.
	 * * stored values.
	 * * an empty input when there are no stored values.
	 * * an add button to add more values.
	 * * html wrapper close.
	 */
	public function html();

	/**
	 * Return a single <input> tag for the Field.
	 *
	 * @param mixed $value Input value.
	 *
	 * @return string The html code fragment.
	 */
	public function html_input( $value );

	/**
	 * Returns closing for the wrapper HTML tag.
	 */
	public function html_wrapper_close();

}
