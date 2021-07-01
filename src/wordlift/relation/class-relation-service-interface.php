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
use Wordlift\Relation\Types\Relation;

interface  Relation_Service_Interface {

	/**
	 * @param $subject_id int
	 * @return array<Reference>
	 */
	public function get_references( $subject_id );


	/**
	 * @param $post_content string
	 * @return array<Relation>
	 * Extracts the relations from the post content.
	 */
	public function get_relations_from_content( $post_content );


	/**
	 * @param $post_content string
	 * @return array<Relation>
	 * Extracts the relations for all object types.
	 */
	public function get_relations( $post_id );

}