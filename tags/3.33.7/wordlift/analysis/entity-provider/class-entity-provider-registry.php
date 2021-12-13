<?php
/**
 * This file provides a interface for entity provider.
 * The entity can be from different sources such as post, comment, term, user etc.
 *
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @since 3.32.0
 * @package Wordlift\Analysis\Entity_Provider
 */

namespace Wordlift\Analysis\Entity_Provider;

class Entity_Provider_Registry {

	/**
	 * @var array<Entity_Provider>
	 */
	private $entity_providers = array();

	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'initialize_registry' ) );
	}

	public function initialize_registry() {
		$this->entity_providers = apply_filters( 'wl_analysis_entity_providers', array() );
	}


	/**
	 * @param $uri
	 *
	 * @return array|bool
	 */
	public function get_local_entity( $uri ) {

		if ( count( $this->entity_providers ) === 0 ) {
			return false;
		}

		$entity_providers = $this->entity_providers;

		foreach ( $entity_providers as $entity_provider ) {
			$entity_data = $entity_provider->get_entity( $uri );
			if ( $entity_data ) {
				return $entity_data;
			}
		}

		return false;
	}


}
