<?php
/**
 * @since 3.31.6
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class handles save / get data for all the term fields.
 */
namespace Wordlift\Metabox\Field;

class Term_Field_Decorator extends Field_Decorator {



	public function get_data() {
		$meta_key = $this->field->meta_name;
		return get_post_meta( $this->id, $meta_key );
	}

	public function save_data( $values ) {

		$values    = $this->field->sanitize_data( $values );
		$term_id = $this->id;
		$meta_key = $this->field->meta_name;
		// Take away old values.
		delete_term_meta( $term_id, $meta_key );

		// insert new values, respecting cardinality.
		$single = ( 1 === $this->field->cardinality );
		foreach ( $values as $value ) {
			$this->field->log->trace( "Saving $value to $meta_key for term $term_id..." );
			// To avoid duplicate values
			delete_term_meta( $term_id, $meta_key, $value );
			$meta_id = add_term_meta( $term_id, $meta_key, $value, $single );
			$this->field->log->debug( "$value to $meta_key for term $term_id saved with id $meta_id." );
		}
	}

}