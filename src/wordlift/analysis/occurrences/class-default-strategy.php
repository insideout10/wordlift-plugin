<?php
/**
 * This file provides a default strategy to add the occurences in analysis service.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.32.6
 */
namespace Wordlift\Analysis\Occurrences;

use Wordlift\Common\Singleton;

class Default_Strategy extends Singleton implements Occurrences {

	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	public function add_occurrences_to_entities( $occurrences, $json, $post_id ) {

		foreach ( $json->entities as $id => $entity ) {

			$json->entities->{$id}->occurrences = isset( $occurrences[ $id ] ) ? $occurrences[ $id ] : array();

			foreach ( $json->entities->{$id}->occurrences as $annotation_id ) {
				$json->entities->{$id}->annotations[ $annotation_id ] = array(
					'id' => $annotation_id,
				);
			}
		}

		return $json;
	}
}
