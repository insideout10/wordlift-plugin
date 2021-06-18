<?php
/**
 * @since 3.31.6
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class handles save / get data for all the post fields.
 */

namespace Wordlift\Metabox\Field;

class Post_Field_Decorator implements Field {

	private $post_id;

	private $field;

	/**
	 * Post_Field_Decorator constructor.
	 *
	 * @param $post_id
	 * @param $field Wl_Metabox_Field
	 */
	public function __construct( $post_id, $field ) {
		$this->post_id = $post_id;
		$this->field   = $field;
	}

	public function get_data() {
		$meta_key = $this->field->meta_name;
		return get_post_meta( $this->post_id, $meta_key );
	}

	public function save_data( $values ) {

		$values    = $this->field->sanitize_data( $values );
		$entity_id = intval( $_POST['post_ID'] );
		$meta_key = $this->field->meta_name;
		// Take away old values.
		delete_post_meta( $entity_id, $meta_key );

		// insert new values, respecting cardinality.
		$single = ( 1 === $this->field->cardinality );
		foreach ( $values as $value ) {
			$this->field->log->trace( "Saving $value to $meta_key for entity $entity_id..." );
			// To avoid duplicate values
			delete_post_meta( $entity_id, $meta_key, $value );
			$meta_id = add_post_meta( $entity_id, $meta_key, $value, $single );
			$this->field->log->debug( "$value to $meta_key for entity $entity_id saved with id $meta_id." );
		}
	}
}