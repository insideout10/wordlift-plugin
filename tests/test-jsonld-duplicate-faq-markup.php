<?php

use Wordlift\Jsonld\Jsonld_Context_Enum;

/**
 * Class Duplicate_Faq_Markup_Test
 * @group jsonld
 */
class Duplicate_Faq_Markup_Test extends Wordlift_Unit_Test_Case {

	/**
	 * In JSON-LD only print out the FAQPage markup if the FAQPage is related to the current post.
	 *
	 * When the FAQPage isn't related to the current post, then
	 *
	 * if there are no other entity types, then do not print the entity data.
	 * if there are other entity types, print them (but to not print the FAQPage data).
	 **/


	/**
	 * For the post A with faq content, referencing entity B which also has faq content
	 * then it should remove the faq content of entity B.
	 */
	public function test_when_the_referenced_entity_is_faq_markup_should_be_removed() {

		$faq_element_structure = array(
			'@type'          => 'Question',
			'name'           => 'What is the return policy?',
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => 'Most unopened items in new condition and returned within <strong>90 days</strong> will receive a refund or exchange. Some items have a modified return policy noted on the receipt or packing slip. Items that are opened or damaged or do not have a receipt may be denied a refund or exchange.'
			)
		);


		$post_jsonld = array(
			"@context"   => "https://schema.org",
			'@type'      => 'FAQPage',
			'mainEntity' => array(
				$faq_element_structure,
				$faq_element_structure
			)
		);

		$jsonld_array = array(

			// Post jsonld.
			$post_jsonld,
			// entity jsonld.
			array(
				'@type'      => 'FAQPage',
				'mainEntity' => array(
					$faq_element_structure,
					$faq_element_structure
				)
			),
		);

		// Apply the filter, and we now should have only one item in the jsonld array.
		$result = apply_filters( 'wl_after_get_jsonld', $jsonld_array, null, Jsonld_Context_Enum::FAQ );

		$this->assertCount( 1, $result );
		$this->assertEquals( array( $post_jsonld ), $result );
	}

	public function test_if_the_referenced_entity_is_of_multiple_type_then_should_only_remove_faq_markup() {
		$faq_element_structure = array(
			'@type'          => 'Question',
			'name'           => 'What is the return policy?',
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => 'Most unopened items in new condition and returned within <strong>90 days</strong> will receive a refund or exchange. Some items have a modified return policy noted on the receipt or packing slip. Items that are opened or damaged or do not have a receipt may be denied a refund or exchange.'
			)
		);


		$post_jsonld = array(
			"@context"   => "https://schema.org",
			'@type'      => 'FAQPage',
			'mainEntity' => array(
				$faq_element_structure,
				$faq_element_structure
			)
		);

		$jsonld_array = array(

			// Post jsonld.
			$post_jsonld,
			// entity jsonld.
			array(
				'@type'      => array( 'FAQPage', 'HowTo' ),
				'foo'        => 'bar',
				'mainEntity' => array(
					$faq_element_structure,
					$faq_element_structure
				)
			),
		);

		// Apply the filter, and we now should have only one item in the jsonld array.
		$result = apply_filters( 'wl_after_get_jsonld', $jsonld_array, null, Jsonld_Context_Enum::FAQ );

		$this->assertCount( 2, $result );
		$this->assertEquals( array(
			$post_jsonld,
			array(
				'@type' => array( 'HowTo' ),
				'foo'   => 'bar',
			)
		), $result );

	}


	public function test_if_referenced_entities_have_faq_page_markup_should_remove_it() {
		$faq_element_structure = array(
			'@type'          => 'Question',
			'name'           => 'What is the return policy?',
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => 'Most unopened items in new condition and returned within <strong>90 days</strong> will receive a refund or exchange. Some items have a modified return policy noted on the receipt or packing slip. Items that are opened or damaged or do not have a receipt may be denied a refund or exchange.'
			)
		);
		$input                 = array(
			array(
				"@context" => "https://schema.org",
				'@type'    => 'Article',
			),
			array(
				'@type'      => 'FAQPage',
				'mainEntity' => array(
					$faq_element_structure,
					$faq_element_structure
				),
				'foo1'       => 'bar1'
			),
			array(
				'@type'      => array( 'FAQPage', 'Article' ),
				'mainEntity' => array(
					$faq_element_structure,
					$faq_element_structure
				),
				'foo2'       => 'bar2'
			)
		);

		$expected_output = array(
			array(
				"@context" => "https://schema.org",
				'@type'    => 'Article',
			),
			array(
				'@type' => array( 'Article' ),
				'foo2'  => 'bar2'
			)
		);

		$result = apply_filters( 'wl_after_get_jsonld', $input, null, Jsonld_Context_Enum::FAQ );
		$this->assertEquals( $expected_output, $result );
	}


}