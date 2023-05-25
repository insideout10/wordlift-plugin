<?php
/**
 * @since      3.27.0
 * @package    Wordlift
 * @subpackage Wordlift/Mappings/Data_Source
 */

namespace Wordlift\Mappings\Data_Source;

use Wordlift\Mappings\Jsonld_Converter;

/**
 * This class fetch the data from  post meta or term meta based on the current page.
 * Class Meta_Data_Source
 *
 * @package Wordlift\Mappings\Data_Source
 */
class Meta_Data_Source implements Abstract_Data_Source {

	/**
	 * @param int      $identifier Post id or term id
	 * @param $property
	 * @param $type
	 *
	 * @return array
	 */
	public function get_data( $identifier, $property, $type ) {

		$value = $property['field_name'];

		if ( Jsonld_Converter::TERM === $type ) {
			$meta = get_term_meta( $identifier, $value );
		} else {
			$meta = get_post_meta( $identifier, $value );
		}
		$values = ( 1 === count( $meta ) && is_array( $meta[0] ) )
			? $meta[0] : $meta;

		return array_map( 'wp_strip_all_tags', $values );

	}
}
