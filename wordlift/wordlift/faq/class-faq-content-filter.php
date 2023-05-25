<?php
/**
 * This file defines content filter which removes all the Faq question and answer tags
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @package Wordlift\Faq
 */

namespace Wordlift\Faq;

class Faq_Content_Filter {
	/**
	 * Constants used for replacing the tags in the html string.
	 */
	const FAQ_QUESTION_TAG_NAME = 'wl-faq-question';
	const FAQ_ANSWER_TAG_NAME   = 'wl-faq-answer';
	/**
	 * Replaces all the html tags inserted by Faq highlighting code in the front end
	 *
	 * @param $content string Post content
	 * @return string String after replacing all the opening and closing tags.
	 */
	public function remove_all_faq_question_and_answer_tags( $content ) {
		/**
		 * Replace all the question tags.
		 */
		$faq_question_closing_tag = '</' . self::FAQ_QUESTION_TAG_NAME . '>';
		$content                  = preg_replace( '/<wl-faq-question class=".+?">/m', '', $content );
		$content                  = str_replace( $faq_question_closing_tag, '', $content );
		/**
		 * Replace all the answer tags.
		 */
		$faq_answer_closing_tag = '</' . self::FAQ_ANSWER_TAG_NAME . '>';
		$content                = preg_replace( '/<wl-faq-answer class=".+?">/m', '', $content );
		$content                = str_replace( $faq_answer_closing_tag, '', $content );

		/** Return all the replaced content */
		return $content;
	}

}
