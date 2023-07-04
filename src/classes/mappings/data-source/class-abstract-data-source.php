<?php
/**
 * @since      3.27.0
 * @package    Wordlift
 * @subpackage Wordlift/Mappings/Data_Source
 */

namespace Wordlift\Mappings\Data_Source;

/**
 * Interface Abstract_Data_Source
 *
 * @package Wordlift\Mappings\Data_Source
 */
interface Abstract_Data_Source {

	public function get_data( $identifier, $property, $type );

}
