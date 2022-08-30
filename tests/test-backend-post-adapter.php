<?php
/**
 * Test the {@link Wordlift_Post_Adapter} class.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 3.23.0
 *
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

use Wordlift\Post\Post_Adapter;

/**
 * Define the Post_Adapter_Test.
 *
 * @since 3.23.0
 * @group backend
 */
class Post_Adapter_Test extends Wordlift_Unit_Test_Case {

	public function test_entity_not_linked_to_a_term() {

		// Create an entity.
		$entity_id = $this->factory->post->create( array(
			'post_type'   => 'entity',
			'post_status' => 'publish',
			'post_title'  => 'Test Entity not Linked to a Term',
		) );

		// Add the same as.
		$same_as = "http://example.org/$entity_id";
		add_post_meta( $entity_id, Wordlift_Schema_Service::FIELD_SAME_AS, $same_as );

		// Create a term.
		register_taxonomy( 'wltests_tax', 'post' );
		$term_id = $this->factory->term->create( array(
			'taxonomy' => 'wltests_tax',
		) );

		// We expect the entity permalink.
		$expected = get_permalink( $entity_id );

		// Get the permalink.
		$permalink = Wordlift_Post_Adapter::get_production_permalink( $entity_id );

		$this->assertEquals( $expected, $permalink, 'Permalink should be the entity permalink.' );

	}

	public function test_entity_linked_to_a_term_via_sameas() {

		// Create an entity.
		$entity_id = $this->factory->post->create( array(
			'post_type'   => 'entity',
			'post_status' => 'publish',
			'post_title'  => 'Test Entity not Linked to a Term',
		) );

		// Add the same as.
		$same_as = "http://example.org/$entity_id";
		add_post_meta( $entity_id, Wordlift_Schema_Service::FIELD_SAME_AS, $same_as );

		// Create a term.
		register_taxonomy( 'wltests_tax', 'post' );
		$term_id = $this->factory->term->create( array(
			'taxonomy' => 'wltests_tax',
		) );

		// Add same as.
		add_term_meta( $term_id, '_wl_entity_id', $same_as );

		// Get the permalink.
		$permalink = Wordlift_Post_Adapter::get_production_permalink( $entity_id );

		global $wp_version;
		if ( version_compare( $wp_version, '4.5', '>=' ) ) {
			// We expect the term link.
			$expected = get_term_link( $term_id );
			$this->assertEquals( $expected, $permalink, 'Permalink should be the term permalink.' );
		} else {
			// We expect the entity link.
			$expected = get_permalink( $entity_id );
			$this->assertEquals( $expected, $permalink, 'Permalink should be the entity permalink.' );
		}

	}

	public function test_get_labels__no_data() {

		$post_adapter = new Wordlift\Post\Post_Adapter();

		$result = $post_adapter->get_labels( array() );

		$this->assertEquals( array(), $result, 'Expect an empty array since we provided no data.' );
	}

	public function test_get_labels__label_only() {

		$post_adapter = new Wordlift\Post\Post_Adapter();

		$result = $post_adapter->get_labels( array(
			'label' => 'Label',
		) );

		$this->assertEquals( array( 'Label' ), $result, 'Expect an array with `Label`.' );

	}

	public function test_get_labels__label_and_synonyms() {

		$post_adapter = new Wordlift\Post\Post_Adapter();

		$result = $post_adapter->get_labels( array(
			'label'    => 'Label 1',
			'synonyms' => array( 'Synonym 1', 'Synonym 2' ),
		) );

		$this->assertEquals( array( 'Label 1', 'Synonym 1', 'Synonym 2' ), $result, 'Expect an array with 3 labels.' );

	}

	public function test_get_labels__label_and_synonyms_and_annotations() {

		$post_adapter = new Wordlift\Post\Post_Adapter();

		$result = $post_adapter->get_labels( array(
			'label'       => 'Label 1',
			'synonyms'    => array( 'Synonym 1', 'Synonym 2' ),
			'annotations' => array(
				'annotation_1' => array( 'text' => 'Annotation 1' ),
				'annotation_2' => array( 'text' => 'Annotation 2' ),
				'annotation_3' => array(),
			),
			'occurrences' => array( 'annotation_1', 'annotation_3', 'annotation_4' ),
		) );

		$this->assertEquals( array(
			'Label 1',
			'Synonym 1',
			'Synonym 2',
			'Annotation 1',
		), $result, 'Expect an array with 4 labels.' );

	}

	public function test_get_labels__no_synonyms() {

		$post_adapter = new Wordlift\Post\Post_Adapter();

		$result = $post_adapter->get_labels( array(
			'label'       => 'Label 1',
			'annotations' => array(
				'annotation_1' => array( 'text' => 'Annotation 1' ),
				'annotation_2' => array( 'text' => 'Annotation 2' ),
				'annotation_3' => array(),
			),
			'occurrences' => array( 'annotation_1', 'annotation_3', 'annotation_4' ),
		) );

		$this->assertEquals( array(
			'Label 1',
			'Annotation 1',
		), $result, 'Expect an array with 2 labels.' );

	}

	public function test_should_not_duplicate_the_entities() {

		if ( ! function_exists('parse_blocks') ) {
			$this->markTestSkipped('Skipped because WP < 5.0 doesnt have parse_blocks function');
		}

		$post_content = <<<EOF
<!-- wp:wordlift/classification {"entities":[{"annotations":{"urn:enhancement-19959e42-cda5-4d75-bcd5-e4267010be53":{"start":1267,"end":1277,"text":"en passant"},"urn:enhancement-c874e21b-d69e-4aca-a4b4-3a856db4427e":{"start":148,"end":158,"text":"in passing"},"urn:enhancement-95e04613-9daf-4045-a1ae-50440c9d6ff1":{"start":797,"end":807,"text":"in passing"},"urn:enhancement-22308ee5-e96f-461e-9529-16ec003906c5":{"start":1131,"end":1141,"text":"En passant"},"urn:enhancement-18b49ecf-f6d2-4581-9492-6cd1da49f79f":{"start":956,"end":966,"text":"en passant"},"urn:enhancement-067a2a85-58aa-4c52-b0eb-84e294bcf445":{"start":12,"end":22,"text":"En passant"}},"description":"En passant (from ) is a move in chess. It is a special pawn capture, that can only occur immediately after a pawn moves two ranks forward from its starting position, and an enemy pawn could have captured it had the pawn moved only one square forward. The opponent captures the just-moved pawn \u0022as it passes\u0022 through the first square. The resulting position is the same as if the pawn had moved only one square forward and the enemy pawn had captured it normally. The en passant capture must be made at the very next turn, or the right to do so is lost. It is the only occasion in chess in which a piece is captured but is not replaced on its square by the capturing piece. Like any other move, if an en passant capture is the only legal move available, it must be made.  En passant capture is a common theme in chess compositions. The en passant capture rule was added in the 15th century when the rule that gave pawns an initial double-step move was introduced. It prevents a pawn from using the two-square advance to pass an adjacent enemy pawn without the risk of being captured.","id":"http://dbpedia.org/resource/En_passant","label":"En passant","mainType":"thing","occurrences":["urn:enhancement-067a2a85-58aa-4c52-b0eb-84e294bcf445","urn:enhancement-c874e21b-d69e-4aca-a4b4-3a856db4427e","urn:enhancement-95e04613-9daf-4045-a1ae-50440c9d6ff1","urn:enhancement-18b49ecf-f6d2-4581-9492-6cd1da49f79f","urn:enhancement-22308ee5-e96f-461e-9529-16ec003906c5","urn:enhancement-19959e42-cda5-4d75-bcd5-e4267010be53"],"sameAs":["http://de.dbpedia.org/resource/En_passant"],"types":["thing"]}]} /-->

<!-- wp:paragraph -->
<p><em><strong><span id="urn:enhancement-067a2a85-58aa-4c52-b0eb-84e294bcf445" class="textannotation disambiguated wl-thing" itemid="http://dbpedia.org/resource/En_passant">En passant</span></strong></em> (<small>F</small></p>
<!-- /wp:paragraph -->
EOF;
		$post_adapter = new Post_Adapter();

		// Save once.
		$data = array(
			'post_content' => $post_content,
			'post_status'  => 'publish'
		);

		$post_adapter->wp_insert_post_data(
			$data
		);

		// we should have 1 entity in table.
		$posts = get_posts( array( 'post_type' => 'entity' ) );


		$this->assertCount( 1, $posts
			, 'One entity should be present on the table' );


		// Save again.
		$post_adapter->wp_insert_post_data(
			$data
		);

		// we should have 1 entity in table, there should be no duplication.
		$this->assertCount( 1, get_posts( array( 'post_type' => 'entity' ) )
			, 'One entity should be present on the table' );
	}

}
