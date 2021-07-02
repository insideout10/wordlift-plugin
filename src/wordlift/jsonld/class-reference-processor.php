<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * Reference processor helps to serialize and deserialize references before and after getting
 * it from the cache.
 */

namespace Wordlift\Jsonld;

use Wordlift\Common\Singleton;

class Reference_Processor  extends  Singleton {

	/**
	 * @return Reference_Processor
	 */
	public static function get_instance() {
		return parent::get_instance();
	}


	public function serialize_references( $references ) {

		return array_map( function ( $reference ) {
			if ( $reference instanceof Post_Reference ) {
				return 'post_' . $reference->get_id();
			} else if ( $reference instanceof Term_Reference ) {
				return 'term_' . $reference->get_id();
			}
			// Backward compatibility with other hooks pushing
			// references in to the cache.
			return 'post_' . $reference->get_id();
		}, $references );
	}

	public function deserialize_references( $references ) {

		return array_map( function ( $reference ) {

			if ( strpos( $reference, 'post_' ) !== false ) {
				return new Post_Reference( (int) str_replace('post_', '', $reference ) );
			} else if ( strpos( $reference, 'term_' ) !== false  ) {
				return new Term_Reference( (int) str_replace('post_', '', $reference ) );
			}
			// Backward compatibility with other hooks pushing
			// references in to the cache.
			return new Post_Reference( (int) $reference );
		}, $references );


	}

}