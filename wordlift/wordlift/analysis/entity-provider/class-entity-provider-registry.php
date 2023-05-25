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

use Wordlift\Assertions;
use Wordlift_Entity_Type_Service;
use Wordlift_Entity_Uri_Service;
use Wordlift_Post_Image_Storage;

class Entity_Provider_Registry {

	/**
	 * @var Entity_Provider[]
	 */
	private $entity_providers = array();

	protected function __construct() {
	}

	private static $instance = null;

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();

			$providers = array(
				new Post_Entity_Provider(
					Wordlift_Entity_Uri_Service::get_instance(),
					Wordlift_Entity_Type_Service::get_instance(),
					new Wordlift_Post_Image_Storage()
				),
				new Term_Entity_Provider(),
			);

			foreach ( apply_filters( 'wl_analysis_entity_providers', $providers ) as $provider ) {
				self::$instance->register_provider( $provider );
			}
		}

		return self::$instance;
	}

	public function register_provider( $provider ) {
		Assertions::is_a( $provider, 'Wordlift\Analysis\Entity_Provider\Entity_Provider', '`provider` must implement the `Wordlift\Analysis\Entity_Provider` interface.' );

		$this->entity_providers[] = $provider;
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

		foreach ( $this->entity_providers as $entity_provider ) {
			$entity_data = $entity_provider->get_entity( $uri );
			if ( $entity_data ) {
				return $entity_data;
			}
		}

		return false;
	}

}
