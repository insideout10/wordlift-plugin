<?php
/**
 * Query entities by title
 *
 * @see https://github.com/insideout10/wordlift-plugin/issues/1574
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.38.0
 */

namespace Wordlift\Entity\Query;



class Entity_Query_Service {


	private static $instance = null;

	/**
	 * The singleton instance.
	 *
	 * @return Entity_Query_Service
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	public function query( $entity_title, $limit = 10, $schema_types = array() ) {

	}


}