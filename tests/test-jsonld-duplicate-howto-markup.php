<?php

use Wordlift\Jsonld\Jsonld_Context_Enum;

/**
 * Class Duplicate_Faq_Markup_Test
 * @group jsonld
 */
class Duplicate_How_To_Markup_Test extends Wordlift_Unit_Test_Case {

	private $mock_how_to_data;

	function setUp() {
		parent::setUp();
		$this->mock_how_to_data = array(
			'estimatedCost' =>
				array(
					'@type'    => 'MonetaryAmount',
					'currency' => 'USD',
					'value'    => '100',
				),
			'supply'        =>
				array(
					0 =>
						array(
							'@type' => 'HowToSupply',
							'name'  => 'tiles',
						),
					1 =>
						array(
							'@type' => 'HowToSupply',
							'name'  => 'thin-set mortar',
						),
					2 =>
						array(
							'@type' => 'HowToSupply',
							'name'  => 'tile grout',
						),
					3 =>
						array(
							'@type' => 'HowToSupply',
							'name'  => 'grout sealer',
						),
				),
			'tool'          =>
				array(
					0 =>
						array(
							'@type' => 'HowToTool',
							'name'  => 'notched trowel',
						),
					1 =>
						array(
							'@type' => 'HowToTool',
							'name'  => 'bucket',
						),
					2 =>
						array(
							'@type' => 'HowToTool',
							'name'  => 'large sponge',
						),
				),
			'step'          =>
				array(
					0 =>
						array(
							'@type'           => 'HowToStep',
							'url'             => 'https://example.com/kitchen#step1',
							'name'            => 'Prepare the surfaces',
							'itemListElement' =>
								array(
									0 =>
										array(
											'@type' => 'HowToDirection',
											'text'  => 'Turn off the power to the kitchen and then remove everything that is on the wall, such as outlet covers, switchplates, and any other item in the area that is to be tiled.',
										),
									1 =>
										array(
											'@type' => 'HowToDirection',
											'text'  => 'Then clean the surface thoroughly to remove any grease or other debris and tape off the area.',
										),
								),
							'image'           =>
								array(
									'@type'  => 'ImageObject',
									'url'    => 'https://example.com/photos/1x1/photo-step1.jpg',
									'height' => '406',
									'width'  => '305',
								),
						),
					1 =>
						array(
							'@type'           => 'HowToStep',
							'name'            => 'Plan your layout',
							'url'             => 'https://example.com/kitchen#step2',
							'itemListElement' =>
								array(
									0 =>
										array(
											'@type' => 'HowToTip',
											'text'  => 'The creases created up until this point will be guiding lines for creating the four walls of your planter box.',
										),
									1 =>
										array(
											'@type' => 'HowToDirection',
											'text'  => 'Lift one side at a 90-degree angle, and fold it in place so that the point on the paper matches the other two points already in the center.',
										),
									2 =>
										array(
											'@type' => 'HowToDirection',
											'text'  => 'Repeat on the other side.',
										),
								),
							'image'           =>
								array(
									'@type'  => 'ImageObject',
									'url'    => 'https://example.com/photos/1x1/photo-step2.jpg',
									'height' => '406',
									'width'  => '305',
								),
						),
					2 =>
						array(
							'@type'           => 'HowToStep',
							'name'            => 'Prepare your and apply mortar (or choose adhesive tile)',
							'url'             => 'https://example.com/kitchen#step3',
							'itemListElement' =>
								array(
									0 =>
										array(
											'@type' => 'HowToDirection',
											'text'  => 'Follow the instructions on your thin-set mortar to determine the right amount of water to fill in your bucket. Once done, add the powder gradually and make sure it is thoroughly mixed.',
										),
									1 =>
										array(
											'@type' => 'HowToDirection',
											'text'  => 'Once mixed, let it stand for a few minutes before mixing it again. This time do not add more water. Double check your thin-set mortar instructions to make sure the consistency is right.',
										),
									2 =>
										array(
											'@type' => 'HowToDirection',
											'text'  => 'Spread the mortar on a small section of the wall with a trowel.',
										),
									3 =>
										array(
											'@type' => 'HowToTip',
											'text'  => 'Thinset and other adhesives set quickly so make sure to work in a small area.',
										),
									4 =>
										array(
											'@type' => 'HowToDirection',
											'text'  => 'Once it\'s applied, comb over it with a notched trowel.',
										),
								),
							'image'           =>
								array(
									'@type'  => 'ImageObject',
									'url'    => 'https://example.com/photos/1x1/photo-step3.jpg',
									'height' => '406',
									'width'  => '305',
								),
						),
					3 =>
						array(
							'@type'           => 'HowToStep',
							'name'            => 'Add your tile to the wall',
							'url'             => 'https://example.com/kitchen#step4',
							'itemListElement' =>
								array(
									0 =>
										array(
											'@type' => 'HowToDirection',
											'text'  => 'Place the tile sheets along the wall, making sure to add spacers so the tiles remain lined up.',
										),
									1 =>
										array(
											'@type' => 'HowToDirection',
											'text'  => 'Press the first piece of tile into the wall with a little twist, leaving a small (usually one-eight inch) gap at the countertop to account for expansion. use a rubber float to press the tile and ensure it sets in the adhesive.',
										),
									2 =>
										array(
											'@type' => 'HowToDirection',
											'text'  => 'Repeat the mortar and tiling until your wall is completely tiled, Working in small sections.',
										),
								),
							'image'           =>
								array(
									'@type'  => 'ImageObject',
									'url'    => 'https://example.com/photos/1x1/photo-step4.jpg',
									'height' => '406',
									'width'  => '305',
								),
						),
					4 =>
						array(
							'@type'           => 'HowToStep',
							'name'            => 'Apply the grout',
							'url'             => 'https://example.com/kitchen#step5',
							'itemListElement' =>
								array(
									0 =>
										array(
											'@type' => 'HowToDirection',
											'text'  => 'Allow the thin-set mortar to set. This usually takes about 12 hours. Don\'t mix the grout before the mortar is set, because you don\'t want the grout to dry out!',
										),
									1 =>
										array(
											'@type' => 'HowToDirection',
											'text'  => 'To apply, cover the area thoroughly with grout and make sure you fill all the joints by spreading it across the tiles vertically, horizontally, and diagonally. Then fill any remaining voids with grout.',
										),
									2 =>
										array(
											'@type' => 'HowToDirection',
											'text'  => 'Then, with a moist sponge, sponge away the excess grout and then wipe clean with a towel. For easier maintenance in the future, think about applying a grout sealer.',
										),
								),
							'image'           =>
								array(
									'@type'  => 'ImageObject',
									'url'    => 'https://example.com/photos/1x1/photo-step5.jpg',
									'height' => '406',
									'width'  => '305',
								),
						),
				),
			'totalTime'     => 'P2D',
		);
	}


	/**
	 * For the post A with howto content, referencing entity B which also has howto content
	 * then it should remove the howto content of entity B.
	 */
	public function test_when_the_referenced_entity_is_howto_markup_should_be_removed() {

		$post_jsonld = array(
			               "@context" => "https://schema.org",
			               '@type'    => 'HowTo',
		               ) + $this->mock_how_to_data;

		$jsonld_array = array(

			// Post jsonld.
			$post_jsonld,
			// entity jsonld.
			array(
				'@type' => 'HowTo',
			) + $this->mock_how_to_data,
		);

		// Apply the filter, and we now should have only one item in the jsonld array.
		$result = apply_filters( 'wl_after_get_jsonld', $jsonld_array, null, Jsonld_Context_Enum::FAQ );

		$this->assertCount( 1, $result );
		$this->assertEquals( array( $post_jsonld ), $result );
	}

	public function test_if_the_referenced_entity_is_of_multiple_type_then_should_only_remove_howto_markup() {
		$howto_element_structure = array(
			'@type'          => 'Question',
			'name'           => 'What is the return policy?',
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => 'Most unopened items in new condition and returned within <strong>90 days</strong> will receive a refund or exchange. Some items have a modified return policy noted on the receipt or packing slip. Items that are opened or damaged or do not have a receipt may be denied a refund or exchange.'
			)
		);


		$post_jsonld = array(
			               "@context" => "https://schema.org",
			               '@type'    => 'HowTo',
		               ) + $this->mock_how_to_data;

		$jsonld_array = array(

			// Post jsonld.
			$post_jsonld,
			// entity jsonld.
			array(
				'@type' => array( 'HowTo', 'Thing' ),
				'foo'   => 'bar',
			) + $this->mock_how_to_data,
		);

		// Apply the filter, and we now should have only one item in the jsonld array.
		$result = apply_filters( 'wl_after_get_jsonld', $jsonld_array, null, Jsonld_Context_Enum::FAQ );

		$this->assertCount( 2, $result );
		$this->assertEquals( array(
			$post_jsonld,
			array(
				'@type' => array( 'Thing' ),
				'foo'   => 'bar',
			)
		), $result );

	}


	public function test_if_referenced_entities_have_howto_page_markup_should_remove_it() {

		$input = array(
			array(
				"@context" => "https://schema.org",
				'@type'    => 'Article',
			),
			array(
				'@type' => 'HowTo',
				'foo1'  => 'bar1'
			) + $this->mock_how_to_data,
			array(
				'@type' => array( 'HowTo', 'Article' ),
				'foo2'  => 'bar2'
			) + $this->mock_how_to_data
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

	public function test_if_referenced_entities_have_name_and_description_property_then_it_should_be_retained() {

		$input = array(
			array(
				"@context" => "https://schema.org",
				'@type'    => 'Article',
			),
			array(
				'@type'       => 'HowTo',
				'name'        => 'name',
				'description' => 'description'
			) + $this->mock_how_to_data,
			array(
				'@type' => array( 'HowTo', 'Article' ),
				'name'        => 'name',
				'description' => 'description'
			) + $this->mock_how_to_data
		);

		$expected_output = array(
			array(
				"@context" => "https://schema.org",
				'@type'    => 'Article',
			),
			array(
				'@type' => array( 'Article' ),
				'name'        => 'name',
				'description' => 'description'
			)
		);
		$result = apply_filters( 'wl_after_get_jsonld', $input, null, Jsonld_Context_Enum::FAQ );
		$this->assertEquals( $expected_output, $result );
	}


}