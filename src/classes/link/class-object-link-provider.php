<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class provides a abstract layer for content filter service to generate a link by entity uri service.
 */

namespace Wordlift\Link;

use Wordlift\Common\Singleton;
use Wordlift\Object_Type_Enum;

class Object_Link_Provider extends Singleton {
	/**
	 * @var array<Link>
	 */
	private $link_providers;

	public function __construct() {
		parent::__construct();
		$this->link_providers = array(
			Object_Type_Enum::POST => Post_Link::get_instance(),
			Object_Type_Enum::TERM => Term_Link::get_instance(),
		);
	}

	/**
	 * @param $id int
	 * @param $label_to_be_ignored string
	 * @param $object_type int
	 *
	 * @return string
	 */
	public function get_link_title( $id, $label_to_be_ignored, $object_type ) {
		$provider = $this->get_provider( $object_type );
		if ( ! $provider ) {
			return '';
		}

		return $provider->get_link_title( $id, $label_to_be_ignored );
	}

	/**
	 * Return the object type by the entity uri.
	 *
	 * @return int which can be any of the {@link Object_Type_Enum} values.
	 */
	public function get_object_type( $uri ) {

		$link_providers = $this->link_providers;
		foreach ( $link_providers as $type => $provider ) {
			/**
			 * @var $provider Link
			 */
			$id = $provider->get_id( $uri );
			if ( $id ) {
				return $type;
			}
		}

		return Object_Type_Enum::UNKNOWN;
	}

	public function get_same_as_uris( $id, $object_type ) {

		$provider = $this->get_provider( $object_type );
		if ( ! $provider ) {
			return array();
		}

		return $provider->get_same_as_uris( $id );
	}

	/**
	 * @param $object_type
	 *
	 * @return mixed|Link
	 */
	private function get_provider( $object_type ) {

		if ( ! array_key_exists( $object_type, $this->link_providers ) ) {
			return false;
		}

		return $this->link_providers[ $object_type ];
	}

	public function get_permalink( $id, $object_type ) {
		$provider = $this->get_provider( $object_type );
		if ( ! $provider ) {
			return false;
		}

		return $provider->get_permalink( $id );
	}

	/**
	 * Return the edit term page link.
	 *
	 * @param $object_id
	 * @param $uri
	 *
	 * @return string | false
	 * @since 3.32.0
	 */
	public function get_edit_page_link( $object_id, $uri ) {

		$object_type = $this->get_object_type( $uri );

		$provider = $this->get_provider( $object_type );

		if ( ! $provider ) {
			return false;
		}

		return $provider->get_edit_page_link( $object_id );

	}

}
