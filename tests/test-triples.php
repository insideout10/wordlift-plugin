<?php
/**
 * Tests: WordLift Triples Test.
 * Test that insert/delete triples are generated correctly.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Test the {@link WordLift_Triples_Test} class.
 *
 * @since 3.18.0
 */
class WordLift_Triples_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The Schema service.
	 *
	 * @since  3.18.0
	 * @access protected
	 * @var \Wordlift_Schema_Service $schema_service The Schema service.
	 */
	private $schema_service;

	/**
	 * The Entity service.
	 *
	 * @since  3.18.0
	 * @access protected
	 * @var \Wordlift_Entity_Service $entity_service The Entity service.
	 */
	private $entity_service;

	/**
	 * Set up the test.
	 */
	public function setUp() {
		parent::setUp();
		$this->schema_service = $this->get_wordlift_test()->get_schema_service();
		$this->entity_service = $this->get_wordlift_test()->get_entity_service();
	}

	/**
	 * Check that place triples doens't contain address triples.
	 *
	 * @since 3.18.0
	 *
	 * @return void
	 */
	public function test_place_triples_without_address() {

		// Create an entity.
		$entity_id = $this->factory->post->create( array(
			'post_type'   => 'entity',
			'post_status' => 'publish',
		) );

		// Add address post meta, to ensure that even with them
		// the address triples will not exists.
		update_post_meta( $entity_id, 'wl_address', 'Sunshine str 15' );
		update_post_meta( $entity_id, 'wl_address_postal_code', '9001' );

		// Set place entity type to the entity.
		$term = get_term_by( 'slug', 'event', Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );
		wp_set_post_terms( $entity_id, $term->term_id, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );

		// Get the entity uri.
		$uri = $this->entity_service->get_uri( $entity_id );

		// Get place insert triples.
		$schema         = $this->schema_service->get_schema( 'event' );
		$renditions     = $schema['linked_data'];
		$insert_triples = $this->get_insert_triples( $renditions, $entity_id );
		$delete_triples = $this->get_delete_triples( $renditions, $entity_id );

		// Check that delete triples doens't contain address triples.
		$this->assertNotContains( "<$uri/address> <http://schema.org/streetAddress> ?o", $delete_triples );
		$this->assertNotContains( "<$uri/address> <http://schema.org/postOfficeBoxNumber> ?o", $delete_triples );
		$this->assertNotContains( "<$uri/address> <http://schema.org/postalCode> ?o", $delete_triples );
		$this->assertNotContains( "<$uri/address> <http://schema.org/addressLocality> ?o", $delete_triples );
		$this->assertNotContains( "<$uri/address> <http://schema.org/addressRegion> ?o", $delete_triples );
		$this->assertNotContains( "<$uri/address> <http://schema.org/addressCountry> ?o", $delete_triples );
	}

	/**
	 * Test that place entity triples contain the address insert/delete triples.
	 *
	 * @since 3.18.0
	 *
	 * @return void
	 */
	public function test_place_triples_with_address_triples() {

		// Create an entity.
		$entity_id = $this->factory->post->create( array(
			'post_type'   => 'entity',
			'post_status' => 'publish',
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

		// Set place entity type to the entity.
		$term = get_term_by( 'slug', 'place', Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );
		wp_set_post_terms( $entity_id, $term->term_id, Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME );

		// Get the entity uri.
		$uri = $this->entity_service->get_uri( $entity_id );

		// Get place insert triples.
		$schema     = $this->schema_service->get_schema( 'place' );
		$renditions = $schema['linked_data'];

		$insert_triples = $this->get_insert_triples( $renditions, $entity_id );
		$delete_triples = $this->get_delete_triples( $renditions, $entity_id );

		// Check that there are delete triples for address.
		$this->assertContains( "<$uri/address> <http://schema.org/streetAddress> ?o", $delete_triples );
		$this->assertContains( "<$uri/address> <http://schema.org/postOfficeBoxNumber> ?o", $delete_triples );
		$this->assertContains( "<$uri/address> <http://schema.org/postalCode> ?o", $delete_triples );
		$this->assertContains( "<$uri/address> <http://schema.org/addressLocality> ?o", $delete_triples );
		$this->assertContains( "<$uri/address> <http://schema.org/addressRegion> ?o", $delete_triples );
		$this->assertContains( "<$uri/address> <http://schema.org/addressCountry> ?o", $delete_triples );

		// Test that address insert triples exists.
		$this->assertContains( "<$uri/address> <http://schema.org/streetAddress> \"$address_meta\"@en . ", $insert_triples );
		$this->assertContains( "<$uri/address> <http://schema.org/postalCode> \"$postal_code_meta\" . ", $insert_triples );
		$this->assertContains( "<$uri/address> <http://schema.org/postOfficeBoxNumber> \"$post_office_box_meta\" . ", $insert_triples );
		$this->assertContains( "<$uri/address> <http://schema.org/addressLocality> \"$locality_meta\"@en . ", $insert_triples );
		$this->assertContains( "<$uri/address> <http://schema.org/addressCountry> \"$country_meta\"@en . ", $insert_triples );
		$this->assertContains( "<$uri> <http://schema.org/address> <$uri/address> . ", $insert_triples );
		$this->assertContains( "<$uri/address> a <http://schema.org/PostalAddress> . ", $insert_triples );

	}


	/**
	 * Get delete triples from renditions.
	 *
	 * @since 3.18.0
	 *
	 * @param array $renditions Array of all renditions.
	 * @param int   $post_id    The post ID.
	 *
	 * @return array $delete_triples The delete triples for current post_id.
	 */
	public function get_delete_triples( $renditions, $post_id ) {
		$delete_triples = array();

		// Loop through all renditions and get the triples.
		foreach ( $renditions as $rendition ) {
			// Push the rendition delete triple to $delete_triples.
			$delete_triples = array_merge(
				$delete_triples,
				(array) $rendition->get_delete_triples( $post_id )
			);
		}

		return $delete_triples;
	}

	/**
	 * Get insert triples from renditions.
	 *
	 * @since 3.18.0
	 *
	 * @param array $renditions Array of all renditions.
	 * @param int   $post_id    The post ID.
	 *
	 * @return array $insert_triples The insert triples for current post_id.
	 */
	public function get_insert_triples( $renditions, $post_id ) {
		$insert_triples = array();

		// Loop through all renditions and get the triples.
		foreach ( $renditions as $rendition ) {
			// Push the rendition insert triple to $insert_triples.
			$insert_triples = array_merge(
				$insert_triples,
				(array) $rendition->get_insert_triples( $post_id )
			);
		}

		return $insert_triples;
	}
}
