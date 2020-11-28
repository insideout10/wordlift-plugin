<?php
/**
 * Class EntityTest
 * @group entity
 */
class EntityTest extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  1.11.0
	 * @access private
	 * @var \Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * Set up the test.
	 */
	function setUp() {
		parent::setUp();

		$this->entity_service = $this->get_wordlift_test()->get_entity_service();

	}

	function testSaveEntity1() {

		$entity_props = array(
			'uri'             => 'http://dbpedia.org/resource/Tim_Berners-Lee',
			'label'           => 'Tim Berners-Lee',
			'main_type'       => 'http://schema.org/Person',
			'type'            => array(
				'http://rdf.freebase.com/ns/people.person',
				'http://rdf.freebase.com/ns/music.artist',
			),
			'related_post_id' => null,
			'description'     => file_get_contents( dirname( __FILE__ ) . '/assets/tim_berners-lee.txt' ),
			'images'          => array(
				'http://upload.wikimedia.org/wikipedia/commons/f/ff/Tim_Berners-Lee-Knight.jpg',
			),
			'same_as'         => array(
				'http://es.dbpedia.org/resource/Tim_Berners-Lee',
				'http://el.dbpedia.org/resource/Τιμ_Μπέρνερς_Λι',
				'http://it.dbpedia.org/resource/Tim_Berners-Lee',
				'http://ja.dbpedia.org/resource/ティム・バーナーズ＝リー',
				'http://pt.dbpedia.org/resource/Tim_Berners-Lee',
				'http://rdf.freebase.com/ns/m.07d5b',
				'http://www4.wiwiss.fu-berlin.de/dblp/resource/person/100007',
				'http://de.dbpedia.org/resource/Tim_Berners-Lee',
				'http://fr.dbpedia.org/resource/Tim_Berners-Lee',
				'http://ru.dbpedia.org/resource/Бернерс-Ли,_Тим',
				'http://cs.dbpedia.org/resource/Tim_Berners-Lee',
				'http://ko.dbpedia.org/resource/팀_버너스리',
				'http://pl.dbpedia.org/resource/Tim_Berners-Lee',
				'http://sw.cyc.com/concept/Mx4r3THFqbCtSyOa3bvfYXUhWg',
				'http://nl.dbpedia.org/resource/Tim_Berners-Lee',
				'http://eu.dbpedia.org/resource/Tim_Berners-Lee',
				'http://www.wikidata.org/entity/Q80',
				'http://yago-knowledge.org/resource/Tim_Berners-Lee',
				'http://zh.dbpedia.org/resource/蒂姆·伯纳斯-李',
				'http://af.dbpedia.org/resource/Tim_Berners-Lee',
				'http://an.dbpedia.org/resource/Tim_Berners-Lee',
				'http://ar.dbpedia.org/resource/تيم_بيرنرز_لي',
				'http://arz.dbpedia.org/resource/تيم_بيرنرز_لى',
				'http://az.dbpedia.org/resource/Tim_Berners-Li',
				'http://be.dbpedia.org/resource/Цім_Бернерс-Лі',
				'http://bg.dbpedia.org/resource/Тим_Бърнърс-Лий',
				'http://bn.dbpedia.org/resource/টিম_বার্নার্স-লি',
				'http://br.dbpedia.org/resource/Tim_Berners-Lee',
				'http://bs.dbpedia.org/resource/Tim_Berners-Lee',
				'http://ca.dbpedia.org/resource/Tim_Berners-Lee',
				'http://cy.dbpedia.org/resource/Tim_Berners-Lee',
				'http://da.dbpedia.org/resource/Tim_Berners-Lee',
				'http://eo.dbpedia.org/resource/Tim_Berners-Lee',
				'http://et.dbpedia.org/resource/Tim_Berners-Lee',
				'http://fa.dbpedia.org/resource/تیم_برنرز_لی',
				'http://fi.dbpedia.org/resource/Tim_Berners-Lee',
				'http://fy.dbpedia.org/resource/Tim_Berners-Lee',
				'http://ga.dbpedia.org/resource/Tim_Berners-Lee',
				'http://gd.dbpedia.org/resource/Tim_Berners-Lee',
				'http://gl.dbpedia.org/resource/Tim_Berners-Lee',
				'http://he.dbpedia.org/resource/טים_ברנרס-לי',
				'http://hi.dbpedia.org/resource/टिम_बर्नर्स_ली',
				'http://hif.dbpedia.org/resource/Tim_Berners-Lee',
				'http://hr.dbpedia.org/resource/Tim_Berners-Lee',
				'http://hu.dbpedia.org/resource/Tim_Berners-Lee',
				'http://id.dbpedia.org/resource/Tim_Berners-Lee',
				'http://ilo.dbpedia.org/resource/Tim_Berners-Lee',
				'http://is.dbpedia.org/resource/Tim_Berners-Lee',
				'http://jv.dbpedia.org/resource/Tim_Berners-Lee',
				'http://ka.dbpedia.org/resource/ტიმ_ბერნერს-ლი',
				'http://kk.dbpedia.org/resource/Тим_Бернерс-Ли',
				'http://kn.dbpedia.org/resource/ಟಿಮ್_ಬರ್ನರ್ಸ್_ಲೀ',
				'http://la.dbpedia.org/resource/Timotheus_Ioannes_Berners-Lee',
				'http://lb.dbpedia.org/resource/Tim_Berners-Lee',
				'http://lt.dbpedia.org/resource/Tim_Berners-Lee',
				'http://lv.dbpedia.org/resource/Tims_Bērnerss-Lī',
				'http://mk.dbpedia.org/resource/Тим_Бернерс-Ли',
				'http://ml.dbpedia.org/resource/ടിം_ബർണേഴ്സ്_ലീ',
				'http://ms.dbpedia.org/resource/Tim_Berners-Lee',
				'http://nn.dbpedia.org/resource/Tim_Berners-Lee',
				'http://no.dbpedia.org/resource/Tim_Berners-Lee',
				'http://oc.dbpedia.org/resource/Tim_Berners-Lee',
				'http://pms.dbpedia.org/resource/Tim_Berners-Lee',
				'http://pnb.dbpedia.org/resource/ٹم_برنرز_لی',
				'http://ro.dbpedia.org/resource/Tim_Berners-Lee',
				'http://rue.dbpedia.org/resource/Тім_Бернерс-Лі',
				'http://scn.dbpedia.org/resource/Tim_Berners-Lee',
				'http://sh.dbpedia.org/resource/Tim_Berners-Lee',
				'http://simple.dbpedia.org/resource/Tim_Berners-Lee',
				'http://sk.dbpedia.org/resource/Tim_Berners-Lee',
				'http://sl.dbpedia.org/resource/Tim_Berners-Lee',
				'http://sq.dbpedia.org/resource/Tim_Berners-Lee',
				'http://sr.dbpedia.org/resource/Тим_Бернерс-Ли',
				'http://sv.dbpedia.org/resource/Tim_Berners-Lee',
				'http://sw.dbpedia.org/resource/Tim_Berners-Lee',
				'http://ta.dbpedia.org/resource/டிம்_பேர்னேர்ஸ்-லீ',
				'http://te.dbpedia.org/resource/టిమ్_బెర్నర్స్_లీ',
				'http://tg.dbpedia.org/resource/Тим_Бернерс-Ли',
				'http://th.dbpedia.org/resource/ทิม_เบอร์เนิร์ส-ลี',
				'http://tl.dbpedia.org/resource/Tim_Berners-Lee',
				'http://tr.dbpedia.org/resource/Tim_Berners-Lee',
				'http://uk.dbpedia.org/resource/Тім_Бернерс-Лі',
				'http://ur.dbpedia.org/resource/ٹم_برنرز_لی',
				'http://uz.dbpedia.org/resource/Tim_Berners-Lee',
				'http://vi.dbpedia.org/resource/Tim_Berners-Lee',
				'http://war.dbpedia.org/resource/Tim_Berners-Lee',
				'http://yi.dbpedia.org/resource/טים_בערנערס-לי',
				'http://yo.dbpedia.org/resource/Tim_Berners-Lee',
				'http://za.dbpedia.org/resource/Tim_Berners-Lee',
				'http://als.dbpedia.org/resource/Tim_Berners-Lee',
				'http://lmo.dbpedia.org/resource/Tim_Berners-Lee',
				'http://bat_smg.dbpedia.org/resource/Tim_Berners-Lee',
				'http://be_x_old.dbpedia.org/resource/Тым_Бэрнэрз-Лі',
				'http://ce.dbpedia.org/resource/Бернерс-Ли,_Тим',
				'http://ckb.dbpedia.org/resource/تیم_بێرنەرز_لی',
				'http://ksh.dbpedia.org/resource/Tim_Berners-Lee',
				'http://li.dbpedia.org/resource/Tim_Berners-Lee',
				'http://mn.dbpedia.org/resource/Тим_Бернерс-Ли',
				'http://mt.dbpedia.org/resource/Tim_Berners-Lee',
				'http://new.dbpedia.org/resource/टिम_बर्नर्स_ली',
				'http://sah.dbpedia.org/resource/Тим_Бернерс-Ли',
				'http://vec.dbpedia.org/resource/Tim_Berners-Lee',
				'http://vo.dbpedia.org/resource/Tim_Berners-Lee',
				'http://zh_min_nan.dbpedia.org/resource/Tim_Berners-Lee',
			),
			'synonym'         => array( 'TBL' ),
		);
		$entity_post  = wl_save_entity( $entity_props );
		$this->assertNotNull( $entity_post );

		// Get the synonyms and check that they match the provided number of synonyms.
		$synonyms = $this->entity_service->get_alternative_labels( $entity_post->ID );
		$this->assertCount( 1, $synonyms );

		// Check that creating a post for the same entity does create a duplicate post.
		$entity_post_2 = wl_save_entity( $entity_props );
		$this->assertEquals( $entity_post->ID, $entity_post_2->ID );

		$entity_props_working_copy = $entity_props; // in PHP arrays are copied, not referenced
		foreach ( $entity_props['same_as'] as $same_as_uri ) {
			// Check that creating a post for the same entity does create a duplicate post.
			$entity_props_working_copy['same_as'] = array( $same_as_uri );
			$same_as_entity_post                  = wl_save_entity( $entity_props_working_copy );
			$this->assertEquals( $entity_post->ID, $same_as_entity_post->ID );
		}

		// Check that the type is set correctly.
		$types = wl_get_entity_rdf_types( $entity_post->ID );
		$this->assertEquals( 2, count( $types ) );
		$this->assertEquals( array( 'Person' ),
			Wordlift_Entity_Type_Service::get_instance()->get_names( $entity_post->ID ) );
	}

	function testSavePlaceWithCoordinates() {

		$entity_props = array(
			'uri'         => 'http://dbpedia.org/resource/Frattocchie',
			'label'       => 'Frattocchie',
			'main_type'   => 'http://schema.org/Place',
			'description' => 'best place on hearth, where the porchetta freely flows',
			'same_as'     => array(
				'http://dbpedia.org/resource/Frattocchie',
				'http://frattocchie.com/love',
			),
			'properties'  => array(
				'latitude'  => array( 43.21 ),
				// array
				'longitude' => 12.34,
				// single value
				'fake'      => array( 'must', 'not', 'be', 'saved' )
				// non-schema property
			),
		);
		$entity_post  = wl_save_entity( $entity_props );
		$this->assertNotNull( $entity_post );

		// Get the synonyms and check that they match the provided number of synonyms.
		$synonyms = $this->entity_service->get_alternative_labels( $entity_post->ID );
		$this->assertCount( 0, $synonyms );

		// Check that the type is set correctly.
		$this->assertEquals( array( 'Place' ),
			Wordlift_Entity_Type_Service::get_instance()->get_names( $entity_post->ID ) );

		// Check coordinates
		$this->assertEquals( array( 43.21 ), wl_schema_get_value( $entity_post->ID, 'latitude' ) );
		$this->assertEquals( array( 12.34 ), wl_schema_get_value( $entity_post->ID, 'longitude' ) );

		// Check invalid property
		$this->assertEquals( null, wl_schema_get_value( $entity_post->ID, 'fake' ) );
	}

	function create_World_Wide_Web_Foundation( $related_post_id ) {

		$uri         = 'http://data.redlink.io/353/wordlift-tests-php-5-4-wp-3-8-ms-0/entity/World_Wide_Web_Foundation';
		$label       = 'World Wide Web Foundation';
		$type        = 'http://schema.org/Organization';
		$description = file_get_contents( dirname( __FILE__ ) . '/assets/world_wide_web_foundation.txt' );
		$images      = array();
		$same_as     = array();
//            'http://rdf.freebase.com/ns/m.04myd3k',
//            'http://yago-knowledge.org/resource/World_Wide_Web_Foundation'
//        );
		$entity_post = wl_save_entity( $uri, $label, $type, $description, array(), $images, $related_post_id, $same_as );

		$this->assertNotNull( $entity_post );

		// Check that the type is set correctly.
		$types = wl_get_entity_rdf_types( $entity_post->ID );
		$this->assertEquals( 0, count( $types ) );
		//$this->assertEquals( 'organization', $types[0]->slug );

		// Check that Tim Berners-Lee is related to this resource.
		$related_entities = wl_core_get_related_entity_ids( $entity_post->ID );
		$this->assertEquals( 1, count( $related_entities ) );
		$this->assertEquals( $related_post_id, $related_entities[0] );

		return $entity_post->ID;
	}

	function create_MIT_Center_for_Collective_Intelligence( $related_post_id ) {

		$uri         = 'http://dbpedia.org/resource/MIT_Center_for_Collective_Intelligence';
		$label       = 'MIT Center for Collective Intelligence';
		$type        = 'http://schema.org/Organization';
		$description = file_get_contents( dirname( __FILE__ ) . '/assets/mit_center_for_cognitive_intelligence.txt' );
		$images      = array();
		$same_as     = array(
			'http://rdf.freebase.com/ns/m.04n2n64',
		);
		$entity_post = wl_save_entity( $uri, $label, $type, $description, array(), $images, $related_post_id, $same_as );

		// Check that the type is set correctly.
		$types = wl_get_entity_rdf_types( $entity_post->ID );
		$this->assertEquals( 0, count( $types ) );
//        $this->assertEquals( 'organization', $types[0]->slug );

		// Check that Tim Berners-Lee is related to this resource.
		$related_entities = wl_core_get_related_entity_ids( $entity_post->ID );
		$this->assertEquals( 1, count( $related_entities ) );
		$this->assertEquals( $related_post_id, $related_entities[0] );

		return $entity_post->ID;
	}

}
