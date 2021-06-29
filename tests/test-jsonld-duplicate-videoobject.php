<?php

use Wordlift\Jsonld\Jsonld_Context_Enum;

/**
 * Class Duplicate_Videoobject_Test
 * @group jsonld
 */
class Duplicate_Videoobject_Test extends Wordlift_Unit_Test_Case {

	/**
	 * when an article is annotated inside another article we should
	 * remove the videoobject markup from the mentioned article
	 */
	public function test_when_the_referenced_article_has_videoobject_markup_should_be_removed() {

		$post_jsonld = array(
			"@context"   => "https://schema.org",
			'@type'      => 'Article',
		);

		$jsonld_array = array(

			// Post jsonld.
			$post_jsonld,
			// entity jsonld.
			array(
				"@context"   => "https://schema.org",
				'@type'      => 'Article',
				'video' => array(
					'name' => 'sample video'
				)
			)
		);

		// Apply the filter, and we now should have only one item in the jsonld array.
		$result = apply_filters( 'wl_after_get_jsonld', $jsonld_array, null, Jsonld_Context_Enum::FAQ );

		$this->assertCount( 2, $result );
		$this->assertFalse( array_key_exists( 'video', $result[1]), 'We should remove the videoobject markup from the annotatated articles' );
	}



	/**
	 * when an entity is annotated with article we should remove it too.
	 */
	public function test_when_the_entity_reference_article_should_be_removed() {

		$post_jsonld = array(
			"@context"   => "https://schema.org",
			'@type'      => 'Thing',
		);

		$jsonld_array = array(

			// Post jsonld.
			$post_jsonld,
			// entity jsonld.
			array(
				"@context"   => "https://schema.org",
				'@type'      => 'Article',
				'video' => array(
					'name' => 'sample video'
				)
			)
		);

		// Apply the filter, and we now should have only one item in the jsonld array.
		$result = apply_filters( 'wl_after_get_jsonld', $jsonld_array, null, Jsonld_Context_Enum::FAQ );

		$this->assertCount( 2, $result );
		$this->assertFalse( array_key_exists( 'video', $result[1]), 'We should remove the videoobject markup from the annotatated articles' );
	}

}