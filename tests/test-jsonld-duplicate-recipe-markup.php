<?php

use Wordlift\Jsonld\Jsonld_Context_Enum;

/**
 * Class Duplicate_Faq_Markup_Test
 * @group jsonld
 */
class Duplicate_Recipe_Markup_Test extends Wordlift_Unit_Test_Case {

	public function test_when_the_referenced_entity_is_recipe_markup_should_be_removed() {


		$post_jsonld = array(
			"@context"   => "https://schema.org",
			'@type'      => 'FAQPage',
		);

		$jsonld_array = array(

			// Post jsonld.
			$post_jsonld,
			// entity jsonld.
			array (
				'@context' => 'http://schema.org',
				'@id' => 'https://data.wordlift.io/wl111434/entity/mind',
				'@type' => 'Recipe',
				'description' => 'journal',
				'mainEntityOfPage' => 'https://qa-dev.wordlift.it/5.8/vocabulary/mind/',
				'name' => 'Mind',
				'sameAs' => 'http://www.wikidata.org/entity/Q1936338',
				'url' => 'https://qa-dev.wordlift.it/5.8/vocabulary/mind/',
				'recipeInstructions' =>
					array (
						0 =>
							array (
								'url' => '1',
								'text' => '1',
								'name' => '1',
								'@type' => 'HowToStep',
							),
					),
				'aggregateRating' =>
					array (
						'ratingCount' => '5',
						'ratingValue' => '5',
						'@type' => 'AggregateRating',
					),
				'author' =>
					array (
						0 =>
							array (
								'@id' => 'https://data.wordlift.io/wl111434/entity/test',
							),
					),
				'cookTime' => '123',
				'keywords' => 'sad',
				'nutrition' =>
					array (
						'calories' => '31',
						'@type' => 'NutritionInformation',
					),
				'prepTime' => '21',
				'recipeCategory' => 'sda',
				'recipeCuisine' => 'wqe',
				'recipeIngredient' =>
					array (
						0 => 'R1',
						1 => 'R2',
					),
				'recipeYield' => '4',
				'totalTime' => '1',
				'video' =>
					array (
						'description' => 'video description',
						'@type' => 'VideoObject',
					),
			),
		);

		// Apply the filter, and we now should have only one item in the jsonld array.
		$result = apply_filters( 'wl_after_get_jsonld', $jsonld_array, null, Jsonld_Context_Enum::REST );

		$this->assertCount( 1, $result );
		$this->assertEquals( array( $post_jsonld ), $result );
	}




}