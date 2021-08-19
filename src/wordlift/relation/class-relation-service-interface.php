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
use Wordlift\Object_Type_Enum;
use Wordlift\Relation\Types\Relation;

interface  Relation_Service_Interface {

	/**
	 * @param $subject_id int
	 * @param int $subject_type {@link Object_Type_Enum}
	 * @return array<Reference>
	 */
	public function get_references( $subject_id, $subject_type );


	/**
	 * @param $content string
	 * @param int $subject_type {@link Object_Type_Enum}
	 * @return array<Relation>
	 * Extracts the relations from the post content.
	 */
	public function get_relations_from_content( $content, $subject_type );


	/**
	 * @param $subject_type
	 * @param $entity_uris
	 *
	 * @return Relation[] | false[]
	 */
	public function get_relations_from_entity_uris( $subject_type, $entity_uris );


}