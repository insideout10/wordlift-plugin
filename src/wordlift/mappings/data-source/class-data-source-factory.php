<?php
/**
 * @since      3.27.0
 * @package    Wordlift
 * @subpackage Wordlift/Mappings/Data_Source
 */

namespace Wordlift\Mappings\Data_Source;

use Wordlift\Mappings\Jsonld_Converter;

class Data_Source_Factory {

	private static $instance = null;

	private $data_sources = array();

	public function __construct() {
		$this->data_sources = array(
			'acf'  => new Acf_Data_Source(),
			'meta' => new Meta_Data_Source()
		);
	}

	public function get_data( $post_id, $property_data, $type ) {
		switch ( $property_data['field_type'] ) {
			case Jsonld_Converter::FIELD_TYPE_ACF:
				return $this->data_sources['acf']->get_data( $post_id, $property_data, $type );
			case Jsonld_Converter::FIELD_TYPE_CUSTOM_FIELD:
				return $this->data_sources['meta']->get_data( $post_id, $property_data, $type );
			default:
				return $property_data['field_name'];
		}
	}


	/**
	 * @return Data_Source_Factory
	 */
	public static function get_instance() {
		if ( self::$instance === null ) {
			self::$instance = new Data_Source_Factory();
		}

		return self::$instance;
	}

}
