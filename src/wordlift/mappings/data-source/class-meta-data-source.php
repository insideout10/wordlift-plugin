<?php
/**
 * @since      3.27.0
 * @package    Wordlift
 * @subpackage Wordlift/Mappings/Data_Source
 */
namespace Wordlift\Mappings\Data_Source;

/**
 * This class fetch the data from  post meta or term meta based on the current page.
 * Class Meta_Data_Source
 * @package Wordlift\Mappings\Data_Source
 */
class Meta_Data_Source implements Abstract_Data_Source {

	// @todo Check usage of get_queried_object
	public function get_data( $post_id, $property ) {

		$value = $property['field_name'];

		if ( get_queried_object() instanceof \WP_Term ) {
			return array_map( 'wp_strip_all_tags', get_term_meta( get_queried_object_id(), $value ) );
		} else {
			return array_map( 'wp_strip_all_tags', get_post_meta( $post_id, $value ) );
		}
	}
}
