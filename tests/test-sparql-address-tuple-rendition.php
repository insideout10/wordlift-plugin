<?php
/**
 * Tests: Address Sparql Tuple Rendition Test.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the {@link Wordlift_Address_Sparql_Tuple_Rendition_Test} class.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 * @group sparql
 */
class Wordlift_Address_Sparql_Tuple_Rendition_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Sparql_Tuple_Rendition_Factory} instance.
	 *
	 * @since  3.18.0
	 * @access protected
	 * @var \Wordlift_Sparql_Tuple_Rendition_Factory $rendition_factory The {@link Wordlift_Sparql_Tuple_Rendition_Factory} instance.
	 */
	protected $rendition_factory;

	/**
	 * The {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.18.0
	 * @access protected
	 * @var \Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service} instance.
	 */
	protected $entity_service;

	/**
	 * The {@link Wordlift_Address_Sparql_Tuple_Rendition} instance.
	 *
	 * @since  3.18.0
	 * @access protected
	 * @var \Wordlift_Address_Sparql_Tuple_Rendition $address_rendition The {@link Wordlift_Address_Sparql_Tuple_Rendition} instance.
	 */
	protected $address_rendition;

	/**
	 * @inheritdoc
	 */
	function setUp() {
		parent::setUp();

		$this->rendition_factory = $this->get_wordlift_test()->get_rendition_factory();
		$this->entity_service    = $this->get_wordlift_test()->get_entity_service();

		$this->address_rendition = $this->rendition_factory->create_address(
			$this->get_wordlift_test()->get_storage_factory()
		);

	}

	/**
	 * Test `get_delete_triples` method.
	 *
	 * @since 3.18.0
	 */
	public function test_get_delete_triples() {

		// Create an entity.
		$entity_id = $this->factory()->post->create( array(
			'post_type'   => 'entity',
			'post_status' => 'publish',
			'post_title'  => 'Address Sparql Tuple Rendition test_get_delete_triples',
		) );

		// Set the entity terms.
		$term = get_term_by( 'slug', 'place', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		$this->assertTrue( is_object( $term ), 'The term place must exist: ' . var_export( $term, true ) );

		wp_set_post_terms( $entity_id, $term->term_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		// Get delete triples
		$delete_triples = $this->address_rendition->get_delete_triples( $entity_id );

		// Get the entity uris.
		$uri = $this->entity_service->get_uri( $entity_id );

		// Check that there are delete triples for address.
		$this->assertContains( "<$uri/address> <http://schema.org/streetAddress> ?o", $delete_triples );
		$this->assertContains( "<$uri/address> <http://schema.org/postOfficeBoxNumber> ?o", $delete_triples );
		$this->assertContains( "<$uri/address> <http://schema.org/postalCode> ?o", $delete_triples );
		$this->assertContains( "<$uri/address> <http://schema.org/addressLocality> ?o", $delete_triples );
		$this->assertContains( "<$uri/address> <http://schema.org/addressRegion> ?o", $delete_triples );
		$this->assertContains( "<$uri/address> <http://schema.org/addressCountry> ?o", $delete_triples );
		$this->assertContains( "<$uri> <http://schema.org/address> <$uri/address> . ", $delete_triples );
		$this->assertContains( "<$uri/address> a <http://schema.org/PostalAddress> . ", $delete_triples );
	}

	/**
	 * Test `get_insert_triples` method.
	 *
	 * @return void
	 * @since 3.18.0
	 *
	 */
	public function test_get_insert_triples() {
		// Create an entity.
		$entity_id = $this->factory()->post->create( array(
			'post_type'   => 'entity',
			'post_status' => 'publish',
			'post_title'  => 'Address Sparql Tuple Rendition test_get_insert_triples',
		) );

		// Set the post meta values.
		$address_meta         = 'Sunshine str 15';
		$postal_code_meta     = 9000;
		$country_meta         = 'Italy';
		$locality_meta        = 'Rome';
		$post_office_box_meta = 1234;

		// Add address post meta.
		update_post_meta( $entity_id, 'wl_address', $address_meta );
		update_post_meta( $entity_id, 'wl_address_postal_code', $postal_code_meta );
		update_post_meta( $entity_id, 'wl_address_country', $country_meta );
		update_post_meta( $entity_id, 'wl_address_locality', $locality_meta );
		update_post_meta( $entity_id, 'wl_address_post_office_box', $post_office_box_meta );

		// Set the entity terms.
		$term = get_term_by( 'slug', 'place', Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );
		wp_set_post_terms( $entity_id, $term->term_id, Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME );

		// Get delete triples
		$insert_triples = $this->address_rendition->get_insert_triples( $entity_id );

		// Get the entity uris.
		$uri = $this->entity_service->get_uri( $entity_id );

		// Test that address insert triples exists.
		$this->assertContains( "<$uri/address> <http://schema.org/streetAddress> \"$address_meta\" . ", $insert_triples );
		$this->assertContains( "<$uri/address> <http://schema.org/postalCode> \"$postal_code_meta\" . ", $insert_triples );
		$this->assertContains( "<$uri/address> <http://schema.org/postOfficeBoxNumber> \"$post_office_box_meta\" . ", $insert_triples );
		$this->assertContains( "<$uri/address> <http://schema.org/addressLocality> \"$locality_meta\" . ", $insert_triples );
		$this->assertContains( "<$uri/address> <http://schema.org/addressCountry> \"$country_meta\" . ", $insert_triples );
		$this->assertContains( "<$uri> <http://schema.org/address> <$uri/address> . ", $insert_triples );
		$this->assertContains( "<$uri/address> a <http://schema.org/PostalAddress> . ", $insert_triples );

	}

}
