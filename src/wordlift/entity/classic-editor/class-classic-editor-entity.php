<?php

namespace Wordlift\Entity\Classic_Editor;

/**
 * @since 3.33.7
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class represents a single entity data posted from classic editor
 */
class Classic_Editor_Entity {

	private $entity_uri;

	private $entity;

	public function __construct( $entity, $entity_uri ) {
		$this->entity     = $entity;
		$this->entity_uri = $entity_uri;
	}

	/**
	 * @return bool
	 */
	public function is_internal_entity() {
		$internal_entity = \Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri( $this->entity_uri );

		return $internal_entity === null && $this->entity_uri_in_current_dataset( $this->entity_uri );
	}

	/**
	 * @return string|null
	 */
	public function build_entity_uri() {
		$label           = $this->entity['label'];
		$entity_type     = ( preg_match( '/^local-entity-.+/', $this->entity_uri ) > 0 ) ?
			$this->entity['main_type'] : null;
		$internal_entity = \Wordlift_Entity_Service::get_instance()->get_entity_post_by_uri( $this->entity_uri );

		return ( null === $internal_entity ) ? $this->build_entity_url_if_entity_not_exists( $label, $entity_type )
			: $this->get_entity_uri_for_existing_entity( $internal_entity );
	}


	/**
	 * @param $label
	 * @param $entity_type
	 *
	 * @return string
	 */
	protected function build_entity_url_if_entity_not_exists( $label, $entity_type ) {
		return \Wordlift_Uri_Service::get_instance()->build_uri(
			$label,
			\Wordlift_Entity_Service::TYPE_NAME,
			$entity_type
		);
	}


	/**
	 * @param $entity_uri
	 *
	 * @return bool|true
	 */
	protected function entity_uri_in_current_dataset( $entity_uri ) {
		return \Wordlift_Entity_Uri_Service::get_instance()->is_internal( $entity_uri );
	}

	/**
	 * @param \WP_Post $internal_entity
	 *
	 * @return string|null
	 */
	protected function get_entity_uri_for_existing_entity( $internal_entity ) {
		return wl_get_entity_uri( $internal_entity->ID );
	}


}