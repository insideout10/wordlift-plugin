<?php
/**
 * This class is created to provide relation service for terms
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.32.0
 *
 * @package Wordlift
 * @subpackage Wordlift\Relation
 */

namespace Wordlift\Relation;

use Wordlift\Content\Wordpress\Wordpress_Content_Id;

class Relation_Instances_Relation_Service extends Abstract_Relation_Service {

	private static $instance = null;

	/**
	 * The singleton instance.
	 *
	 * @return Relation_Service_Interface
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * @param Wordpress_Content_Id $content_id
	 * @param Relations_Interface  $relations
	 */
	public function add_relations( $content_id, $relations ) {
		global $wpdb;

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"
			SELECT subject_id, subject_type, predicate, object_id, object_type
				FROM {$wpdb->prefix}wl_relation_instances
				WHERE subject_id = %d AND subject_type = %s
			",
				$content_id->get_id(),
				$content_id->get_type()
			)
		);

		$new_relations = array_map(
			'Wordlift\Relation\Relation::from_relation_instances',
			$results
		);

		$relations->add( ...$new_relations );
	}

}
