<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class returns the term by URI.
 */

namespace Wordlift\Term;

use Wordlift\Common\Singleton;

class Synonyms_Service extends Singleton {

	public function get_synonyms( $term_id ) {
		return get_term_meta( $term_id, \Wordlift_Entity_Service::ALTERNATIVE_LABEL_META_KEY );
	}

}
