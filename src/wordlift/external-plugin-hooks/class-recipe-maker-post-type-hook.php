<?php
/**
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.27.2
 */

namespace Wordlift\External_Plugin_Hooks;
/**
 * Class Recipe_Maker_Post_Type_Hook
 * @package Wordlift\External_Plugin_Hooks
 */
class Recipe_Maker_Post_Type_Hook {

	private $recipe_maker_post_type = 'wprm_recipe';

	public function __construct() {

		add_filter( 'wl_valid_entity_post_types', array( $this, 'add_post_type' ) );

		add_filter( 'wl_default_entity_type_for_post_type', 'http://schema.org/Recipe',
			$this->recipe_maker_post_type );
	}

	public function add_post_type( $post_types ) {

		$post_types[] = $this->recipe_maker_post_type;

		return $post_types;
	}


	public function wl_default_entity_type_for_post_type( $entity_type, $post_type ) {
		if ( $this->recipe_maker_post_type === $post_type ) {
			return 'http://schema.org/Recipe';
		}

		return $entity_type;
	}
}
