<?php
/**
 * This file defines the converter to convert the faq data to schema markup.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @package Wordlift\FAQ
 */

namespace Wordlift\FAQ;

/**
 * Class Faq_To_Jsonld_Converter helps to convert the
 * Faq data for post id to jsonld
 *
 * @package Wordlift\FAQ
 */
class Faq_To_Jsonld_Converter {
	/**
	 * @param $post_id int The id of the post.
	 *
	 * @return array Get the converted jsonld data
	 */
	public static function get_jsonld_for_faq( $post_id ) {
		$faq_items = get_post_meta( $post_id, FAQ_Rest_Controller::FAQ_META_KEY);
		$jsonld_data = array(
			'@type' => 'FAQPage'
		);
		$jsonld_data['mainEntity'] = array();
		foreach ( $faq_items as $faq_item ) {
			$faq_data = array();
			$faq_data['@type'] = 'Question';
			$faq_data['name'] = $faq_item['question'];
			$faq_data['acceptedAnswer'] = array();
			$faq_data['acceptedAnswer']['@type'] = 'Answer';
			$faq_data['acceptedAnswer']['text'] = $faq_item['answer'];
			array_push($jsonld_data['mainEntity'], $faq_data );
		}
		return $jsonld_data;
	}
}