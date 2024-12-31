<?php

namespace Wordlift\Modules\Pods\FieldDefinition;


/**
 * @since 3.38.3
 * Registers all the pods for post_type and taxonomies.
 */
class AllPodsDefiniton extends AbstractFieldDefiniton {

	public function register() {

		add_action( 'init', array( $this, 'register_on_all_valid_post_types' ) );
		add_action( 'setup_theme', array( $this, 'register_on_all_supported_taxonomies' ) );
	}

	public function register_on_all_supported_taxonomies() {
		$context    = $this->schema->get();
		$taxonomies = get_taxonomies( array( 'public' => true ) );
		foreach ( $taxonomies as $taxonomy ) {
			$this->register_pod( $taxonomy, 'taxonomy', $context );

		}
	}

	public function register_on_all_valid_post_types() {
		$context         = $this->schema->get();
		$supported_types = \Wordlift_Entity_Service::valid_entity_post_types();
		foreach ( $supported_types as $supported_type ) {
			$this->register_pod( $supported_type, 'post_type', $context );
		}
	}

}
