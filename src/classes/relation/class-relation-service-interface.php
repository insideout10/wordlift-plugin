<?php
/**
 * This class is created to provide relation service interface which can
 * be used as basic structure for different object types such as term, user.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.32.0
 *
 * @package Wordlift
 * @subpackage Wordlift\Relation
 */

namespace Wordlift\Relation;

use Wordlift\Content\Wordpress\Wordpress_Content_Id;

interface  Relation_Service_Interface {

	/**
	 * Get the relations for the provided {@link Wordpress_Content_Id}
	 *
	 * @param Wordpress_Content_Id $content_id
	 *
	 * @return Relations_Interface
	 */
	public function get_relations( $content_id );

	/**
	 * Add the relations for the provided {@link Wordpress_Content_Id} to the provided {@link Relations_Interface}
	 *
	 * @param Wordpress_Content_Id $content_id
	 */
	public function add_relations( $content_id, $relations );

}
