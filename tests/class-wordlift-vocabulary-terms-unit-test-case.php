<?php
/**
 * @since 3.31.7
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Content_Service;

abstract class Wordlift_Vocabulary_Terms_Unit_Test_Case extends Wordlift_Unit_Test_Case {

	const NO_VOCABULARY_TERM_TAXONOMY = 'no_vocabulary_terms';

	function setUp() {
		parent::setUp();

		// taxonomy to be used on the no_vocabulary_terms test.
		if ( ! taxonomy_exists( 'no_vocabulary_terms' ) ) {
			register_taxonomy( 'no_vocabulary_terms', 'post' );
		}
	}


	/**
	 * @return int|WP_Error
	 * @throws Exception
	 */
	protected function create_post_with_term_reference( $term_name ) {

		$term_data = wp_insert_term( $term_name, self::NO_VOCABULARY_TERM_TAXONOMY );
		$term      = get_term( $term_data['term_id'] );
		$term_uri  = Wordpress_Content_Service::get_instance()
		                                      ->get_entity_id( Wordpress_Content_Id::create_term( $term->term_id ) );

		$this->assertNotEmpty( $term_uri, 'Term URI cannot be empty.' );

		$post_content = <<<EOF
		<span class="textannotation disambiguated" itemid="$term_uri">test</span>
EOF;

		$post_id = wp_insert_post( array(
			'post_content' => $post_content
		) );

		wl_linked_data_save_post_and_related_entities( $post_id );

		return $post_id;
	}

}
