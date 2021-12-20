<?php

use Wordlift\Post\Post_Adapter;

/**
 * Define the Wordlift_Entity_Post_Adapter_Test class.
 *
 * @since 3.29.0
 * @group entity
 */
class Wordlift_Entity_Block_Editor_Post_Adapter extends Wordlift_Unit_Test_Case {

	/**
	 * @var Post_Adapter
	 */
	private $post_adapter;

	function setUp() {
		parent::setUp();
		$this->post_adapter = new Post_Adapter();
		if ( ! function_exists( 'register_block_type' ) || ! function_exists( 'parse_blocks' ) ) {
			$this->markTestSkipped("Tests require wp >= 5.0 to run");
		}
	}


	public function test_when_post_status_is_set_to_auto_draft_or_inherit_should_not_process_data() {
		$this->assertCount(
			0,
			get_posts( array( 'post_type' => Wordlift_Entity_Service::TYPE_NAME ) ),
			'Before tests no entity should be present'
		);
		$this->post_adapter->wp_insert_post_data(
			array( 'post_status' => 'auto-draft' ),
			array()
		);
		$this->post_adapter->wp_insert_post_data(
			array( 'post_status' => 'inherit' ),
			array()
		);
		$this->assertCount(
			0,
			get_posts( array( 'post_type' => Wordlift_Entity_Service::TYPE_NAME ) ),
			'0 entities should be created since the post status is set to auto draft or inherit'
		);
	}

	public function test_when_post_content_has_entities_annotated_should_create_them_from_store() {

		$this->assertCount(
			0,
			get_posts( array( 'post_type' => Wordlift_Entity_Service::TYPE_NAME ) ),
			'Before tests no entity should be present'
		);

		$post_content = <<<EOF
<!-- wp:wordlift/classification {"entities":[{"annotations":{"annotation-0b17d3de-7877-4e56-8b07-aef8c1dd09b9":{"start":297,"end":306,"text":"worldwide"}},"description":"published or operating in multiple or all jurisdictions on Earth; special value for \u0022place of publication\u0022 (P291) and \u0022operating area\u0022 (P2541)","id":"http://www.wikidata.org/entity/Q13780930","label":"worldwide","mainType":"place","occurrences":["annotation-0b17d3de-7877-4e56-8b07-aef8c1dd09b9"],"properties":{},"sameAs":[],"synonyms":["worldwide"],"types":["place"]},{"annotations":{"annotation-7fba2bfa-38df-44be-a286-430b8e65bef9":{"start":236,"end":247,"text":"the world's"}},"description":"third planet from the Sun in the Solar System","id":"http://www.wikidata.org/entity/Q2","label":"the world's","mainType":"other","occurrences":["annotation-7fba2bfa-38df-44be-a286-430b8e65bef9"],"properties":{},"sameAs":[],"synonyms":["Earth"],"types":["other"]},{"annotations":{"annotation-aef37f68-f4e2-45aa-bb54-3aeabd3e272a":{"start":253,"end":260,"text":"popular"}},"description":"Ghanaian boxer","id":"http://www.wikidata.org/entity/Q302500","label":"popular","mainType":"person","occurrences":["annotation-aef37f68-f4e2-45aa-bb54-3aeabd3e272a"],"properties":{},"sameAs":[],"synonyms":["Aaron Popoola"],"types":["person"]}]} /-->
<!-- wp:paragraph -->
<p><span id="urn:enhancement-d479c88a-abd0-4ebb-8c7b-ae755cac9e21" class="textannotation">Chess</span> is a board <span id="urn:enhancement-cd1d2cd1-a92b-423f-a448-267bac3f3176" class="textannotation">game</span> played between two players. The current form of the <span id="urn:enhancement-7e186142-5522-4b31-bcfd-11c027a6e317" class="textannotation">game</span> emerged in Southern <span id="urn:enhancement-3162c04e-c6cc-491a-8da9-889d34480f14" class="textannotation">Europe</span> during the second half of the 15th <span id="urn:enhancement-489346a8-8917-4131-9726-a9121763d8d0" class="textannotation">century</span> after evolving from similar, much older games of <span id="urn:enhancement-1411cb40-086a-44e3-afdd-9d806a1488d3" class="textannotation">Indian</span> origin. <span id="urn:enhancement-37dc208c-7aa1-43e9-aa4c-acb95d8829d6" class="textannotation">Today</span>, <span id="urn:enhancement-dfa55f48-e18f-4cb4-8112-c921ef041798" class="textannotation">chess</span> is one of <span id="annotation-7fba2bfa-38df-44be-a286-430b8e65bef9" class="textannotation disambiguated wl-other" itemid="http://www.wikidata.org/entity/Q2">the world's</span> most <span id="annotation-aef37f68-f4e2-45aa-bb54-3aeabd3e272a" class="textannotation disambiguated wl-person" itemid="http://www.wikidata.org/entity/Q302500">popular</span> games, played by millions of people <span id="annotation-0b17d3de-7877-4e56-8b07-aef8c1dd09b9" class="textannotation disambiguated wl-place" itemid="http://www.wikidata.org/entity/Q13780930">worldwide</span>.</p>
<!-- /wp:paragraph -->
EOF;
		$this->post_adapter->wp_insert_post_data(
			array( 'post_status' => 'publish', 'post_content' => $post_content ),
			array()
		);

		$this->assertCount(
			3,
			get_posts( array( 'post_type' => Wordlift_Entity_Service::TYPE_NAME ) ),
			'3 entities should be created from post content'
		);


	}


}