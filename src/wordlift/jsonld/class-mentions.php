<?php
/**
 * This file adds the mentions property for all the entities which are descendant of creativework.
 *
 * @see https://github.com/insideout10/wordlift-plugin/issues/1557
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @package Wordlift\Jsonld
 * @since 3.37.1
 */

namespace Wordlift\Jsonld;

class Mentions {

	public function __construct() {
		add_filter( 'wl_entity_jsonld_array', array( $this, 'wl_entity_jsonld_array' ) );
	}

	public function wl_entity_jsonld_array( $arr ) {
		$jsonld     = $arr['jsonld'];
		$references = $arr['references'];

		$type = $jsonld['@type'];

		if ( !  $this->entity_is_descendant_of_creative_work( $type ) && ! $this->entity_is_creative_work( $type ) ) {
			return $arr;
		}

		return array(
			'jsonld'     => $jsonld,
			'references' => $references
		);
	}

}