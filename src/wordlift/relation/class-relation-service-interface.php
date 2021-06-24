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

use Wordlift\Jsonld\Reference;

interface  Relation_Service_Interface {

	/**
	 * @param $subject_id int
	 * @return array<Reference>
	 */
	public function get_references( $subject_id );


//	/**
//	 * @param $post_content string
//	 * @return array<int>
//	 * Note: The Returned ids might not be unique, for example entities and term
//	 */
//	public function get_ids( $post_content );

}