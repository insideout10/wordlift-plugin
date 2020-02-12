<?php
/**
 * This file defines the converter to convert the faq data to schema markup.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @package Wordlift\FAQ
 */

namespace Wordlift\FAQ;

class Faq_To_Jsonld_Converter {
	public function get_jsonld_for_faq() {
		return array('@type' => 'FAQPage');
	}
}