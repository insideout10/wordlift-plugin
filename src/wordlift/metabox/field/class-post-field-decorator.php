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

	public function __construct( $post_id, $field ) {
		$this->post_id = $post_id;
		$this->field = $field;
	}

	public function get_data() {
		// TODO: Implement get_data() method.
	}

	public function save_data( $values ) {

		$entity_id = intval( $_POST['post_ID'] );

		// Take away old values.
		delete_post_meta( $entity_id, $this->field->meta_name );

		// insert new values, respecting cardinality.
		$single = ( 1 === $this->field->cardinality );
		foreach ( $this->field->data as $value ) {
			$this->field->log->trace( "Saving $value to $this->field->meta_name for entity $entity_id..." );
			// To avoid duplicate values
			delete_post_meta( $entity_id, $this->field->meta_name, $value );
			$meta_id = add_post_meta( $entity_id, $this->field->meta_name, $value, $single );
			$this->field->log->debug( "$value to $this->field->meta_name for entity $entity_id saved with id $meta_id." );
		}
	}
}