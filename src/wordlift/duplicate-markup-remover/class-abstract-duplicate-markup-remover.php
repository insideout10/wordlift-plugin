<?php

namespace Wordlift\Duplicate_Markup_Remover;

abstract class Abstract_Duplicate_Markup_Remover {
	/**
	 * @var string
	 */
	private $type_to_remove;
	/**
	 * @var string[]
	 */
	private $properties_to_remove;

	/**
	 * @param $type_to_remove string The schema type to remove.
	 * @param $properties_to_remove string[] The list of schema property to remove for the particular schema type.
	 */
	public function __construct( $type_to_remove, $properties_to_remove ) {
		$this->type_to_remove = $type_to_remove;
		$this->properties_to_remove = $properties_to_remove;
		add_filter( 'wl_after_get_jsonld', array( $this, 'wl_after_get_jsonld' ), 10, 2 );
	}


	/**
	 * @param $jsonld array The final jsonld.
	 * @param $post_id int The post id.
	 *
	 * @return array Filtered jsonld.
	 */
	public function wl_after_get_jsonld( $jsonld, $post_id ) {

		if ( $this->should_alter_jsonld( $jsonld ) ) {
			// Return early if there are no referenced entities.
			return $jsonld;
		}

		$post_jsonld = array_shift( $jsonld );

		// we need to loop through all the items and remove the faq markup.
		foreach ( $jsonld as $key => &$value ) {
			if ( ! array_key_exists( '@type', $value ) ) {
				continue;
			}
			$type = $value['@type'];

			/**
			 * Two possibilities:
			 * 1. The referenced entity has only FAQ Page markup, in that case remove the complete entity.
			 * 2. The referenced entity has multiple types, in that case completely remove the faq markup, but
			 * retain the other entity data.
			 */


			// If the referenced entity is purely a faq page, the remove it.

			if ( is_string( $type ) && $type === $this->type_to_remove ) {
				// Remove the entity completely.
				unset( $jsonld[ $key ] );
			}

			if ( is_array( $type ) && in_array( $this->type_to_remove, $type ) ) {
				// Remove the faq page type.
				$position = array_search( $this->type_to_remove, $type );
				// Also update the type.
				if ( $position !== false ) {
					unset( $type[ $position ] );
					$value['@type'] = array_values( $type );
				}

				foreach ( $this->properties_to_remove as $property ) {
					// Remove keys of faq page.
					unset( $value[ $property ] );
				}
			}

		}

		// Add the post jsonld to front of jsonld array.
		array_unshift( $jsonld, $post_jsonld );

		return $jsonld;
	}


	/**
	 * @param array $jsonld
	 *
	 * @return bool
	 */
	protected function should_alter_jsonld( $jsonld ) {
		return ! is_array( $jsonld )
		       || ! count( $jsonld ) > 1
		       || ! array_key_exists( 0, $jsonld );
	}


}