<?php
/**
 * This file defines the converter to convert the faq data to schema markup.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @package Wordlift\FAQ
 */

namespace Wordlift\Faq;

/**
 * Class Faq_To_Jsonld_Converter helps to convert the
 * Faq data for post id to jsonld
 *
 * @package Wordlift\FAQ
 */
class Faq_To_Jsonld_Converter {

	const FAQ_JSONLD_TYPE = 'FAQPage';

	public function __construct() {
		add_filter( 'wl_post_jsonld', array( $this, 'get_jsonld_for_faq' ), 11, 2 );
		add_filter( 'wl_entity_jsonld', array( $this, 'get_jsonld_for_faq' ), 11, 2 );
	}

	/**
	 * Set the FAQ type to the json ld array
	 *
	 * @param $jsonld array The jsonld array.
	 *
	 * @return array Returns the json ld array with the type set.
	 */
	public function set_faq_type( $jsonld ) {
		if ( array_key_exists( '@type', $jsonld ) ) {
			if ( is_string( $jsonld['@type'] ) ) {
				// If a plain string is present, create an array with previous items.
				$jsonld['@type'] = array( $jsonld['@type'], self::FAQ_JSONLD_TYPE );
				return $jsonld;
			}
			// check if it is a array, then append the type.
			if ( is_array( $jsonld['@type'] ) && ! in_array( self::FAQ_JSONLD_TYPE, $jsonld['@type'], true ) ) {
				array_push( $jsonld['@type'], self::FAQ_JSONLD_TYPE );
				return $jsonld;
			}
		} else {
			$jsonld['@type'] = array( self::FAQ_JSONLD_TYPE );
		}
		return $jsonld;
	}
	/**
	 * @param $post_id int The id of the post.
	 *
	 * @return array Get the converted jsonld data
	 */
	public function get_jsonld_for_faq( $jsonld, $post_id ) {

		$faq_items = get_post_meta( $post_id, Faq_Rest_Controller::FAQ_META_KEY );
		/**
		 * Apply the FAQ mapping only if the FAQ items are present.
		 */
		if ( count( $faq_items ) > 0 ) {
			$faq_data = $this->get_faq_data( $faq_items );
			// Merge the FAQ data with jsonld.
			$jsonld = array_merge( $jsonld, $faq_data );
			// check if the @type is set on json ld
			$jsonld = $this->set_faq_type( $jsonld );
		}
		return $jsonld;
	}

	/**
	 * @param $faq_items array List of FAQ items extracted from the meta.
	 *
	 * @return array Associtative array of type, mainEntity.
	 */
	private function get_faq_data( $faq_items ) {
		$jsonld_data               = array();
		$jsonld_data['mainEntity'] = array();
		foreach ( $faq_items as $faq_item ) {
			if ( 0 === strlen( $faq_item['question'] ) || 0 === strlen( $faq_item['answer'] ) ) {
				// Bail out if question or answer is not present
				continue;
			}
			$faq_data                            = array();
			$faq_data['@type']                   = 'Question';
			$faq_data['name']                    = $faq_item['question'];
			$faq_data['acceptedAnswer']          = array();
			$faq_data['acceptedAnswer']['@type'] = 'Answer';
			$faq_data['acceptedAnswer']['text']  = $faq_item['answer'];
			array_push( $jsonld_data['mainEntity'], $faq_data );
		}

		return $jsonld_data;
	}
}
