<?php

namespace Wordlift\External_Plugin_Hooks\Yoast;

use Wordlift\External_Plugin_Hooks\Recipe_Maker\Recipe_Maker_Validation_Service;

/**
 * Hooks in to yoast jsonld, if a recipe is present for the post,
 * then remove the types Article,WebPage, WebSite.
 * @since ?.??.?
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Yoast_Jsonld {

	/**
	 * @var Recipe_Maker_Validation_Service
	 */
	private $recipe_maker_validation_service;

	public function __construct( $recipe_maker_validation_service ) {
		$this->recipe_maker_validation_service = $recipe_maker_validation_service;
		add_action( 'wpseo_json_ld', array( $this, 'disable_schema_types' ) );
	}

	public function disable_schema_types() {
		if ( ! get_post() instanceof \WP_Post ) {
			return false;
		}
		$post_id = get_the_ID();
		if ( $this->recipe_maker_validation_service
			->is_atleast_once_recipe_present_in_the_post( $post_id ) ) {
			add_filter( 'wpseo_schema_needs_website', '__return_false' );
			add_filter( 'wpseo_schema_needs_webpage', '__return_false' );
			add_filter( 'wpseo_schema_needs_article', '__return_false' );
		}
	}
}
