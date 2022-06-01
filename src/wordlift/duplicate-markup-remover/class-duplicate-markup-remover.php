<?php

namespace Wordlift\Duplicate_Markup_Remover;

class Duplicate_Markup_Remover {

	private $types_to_properties_map = array(
		'HowTo'   => array(
			'estimatedCost',
			'totalTime',
			'supply',
			'tool',
			'step'
		),
		'FAQPage' => array( 'mainEntity' ),
		'Recipe'  => array(
			'cookTime',
			'cookingMethod',
			'nutrition',
			'recipeCategory',
			'recipeCuisine',
			'recipeIngredient',
			'recipeInstructions',
			'recipeYield',
			'suitableForDiet'
		),
		'Product' => array(
			'additionalProperty',
			'aggregateRating',
			'audience',
			'award',
			'brand',
			'category',
			'color',
			'countryOfAssembly',
			'countryOfLastProcessing',
			'countryOfOrigin',
			'depth',
			'funding',
			'gtin',
			'gtin12',
			'gtin13',
			'gtin14',
			'gtin8',
			'hasAdultConsideration',
			'hasEnergyConsumptionDetails',
			'hasMeasurement',
			'hasMerchantReturnPolicy',
			'height',
			'inProductGroupWithID',
			'isAccessoryOrSparePartFor',
			'isConsumableFor',
			'isFamilyFriendly',
			'isRelatedTo',
			'isSimilarTo',
			'isVariantOf',
			'itemCondition',
			'keywords',
			'logo',
			'manufacturer',
			'material',
			'model',
			'mpn',
			'nsn',
			'offers',
			'pattern',
			'productID',
			'productionDate',
			'purchaseDate',
			'releaseDate',
			'review',
			'size',
			'sku',
			'slogan',
			'weight',
			'width'
		)
	);

	public function __construct() {
		add_filter( 'wl_after_get_jsonld', array( $this, 'wl_after_get_jsonld' ), 10, 2 );
	}


	/**
	 * @param $jsonld array The final jsonld.
	 * @param $post_id int The post id.
	 *
	 * @return array Filtered jsonld.
	 */
	public function wl_after_get_jsonld( $jsonld, $post_id ) {

		foreach ( $this->types_to_properties_map as $type_to_remove => $properties_to_remove ) {
			$jsonld = $this->remove_type( $jsonld, $type_to_remove, $properties_to_remove );
		}

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

	/**
	 * @param array $jsonld
	 *
	 * @return array
	 */
	private function remove_type( $jsonld, $type_to_remove, $properties_to_remove ) {


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
			 * 1. The referenced entity has only supplied SchemaType markup, in that case remove the complete entity.
			 * 2. The referenced entity has multiple types, in that case completely remove the supplied SchemaType markup, but
			 * retain the other entity data.
			 */
			// If the referenced entity is purely supplied SchemaType markup, then remove it.

			if ( is_string( $type ) && $type === $type_to_remove ) {
				// Remove the entity completely.
				unset( $jsonld[ $key ] );
			}

			if ( is_array( $type ) && in_array( $type_to_remove, $type ) ) {
				// Remove the supplied SchemaType markup.
				$position = array_search( $type_to_remove, $type );
				// Also update the type.
				if ( $position !== false ) {
					unset( $type[ $position ] );
					$value['@type'] = array_values( $type );
				}

				foreach ( $properties_to_remove as $property ) {
					// Remove keys of supplied SchemaType.
					unset( $value[ $property ] );
				}
			}

		}

		// Add the post jsonld to front of jsonld array.
		array_unshift( $jsonld, $post_jsonld );

		return $jsonld;
	}


}