<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Vocabulary_Terms\Hooks;

use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Term_Content_Service;

/**
 * This class assigns the entity_url meta to the term when it is
 * created or edited, we need to check if it already has entity_url before
 * assigning one because we dont want to generate it again if we have it
 * assigned before.
 *
 * Class Term_Save
 *
 * @package Wordlift\Vocabulary_Terms\Hooks
 */
class Term_Save {

	public function init() {
		add_action( 'create_term', array( $this, 'saved_term' ) );
		add_action( 'edited_term', array( $this, 'saved_term' ) );
	}

	public function saved_term( $term_id ) {

		// check if entity url already exists.

		Wordpress_Term_Content_Service::get_instance()
									  ->get_entity_id( Wordpress_Content_Id::create_term( $term_id ) );

	}

}
