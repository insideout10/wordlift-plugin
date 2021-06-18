<?php
/**
 * @since 3.31.6
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
namespace Wordlift\Metabox\Field;

abstract class Field_Decorator implements  Field {

	/**
	 * @var int
	 */
	protected $id;

	/**
	 * @var Wl_Metabox_Field
	 */
	protected $field;

	/**
	 * Post_Field_Decorator constructor.
	 *
	 * @param $id
	 * @param $field Wl_Metabox_Field
	 */
	public function __construct( $id, $field ) {
		$this->id    = $id;
		$this->field = $field;
	}

	public function html_nonce() {
		return $this->field->html_nonce();
	}

	public function verify_nonce() {
		return $this->field->verify_nonce();
	}

	public function sanitize_data( $values ) {
		return $this->field->sanitize_data( $values );
	}

	public function sanitize_data_filter( $value ) {
		return $this->field->sanitize_data_filter( $value );
	}

	public function html_wrapper_open() {
		return $this->field->html_wrapper_open();
	}

	public function html() {
		return $this->field->html();
	}

	public function html_input( $value ) {
		return $this->field->html_input( $value );
	}

	public function html_wrapper_close() {
		return $this->field->html_wrapper_close();
	}

}