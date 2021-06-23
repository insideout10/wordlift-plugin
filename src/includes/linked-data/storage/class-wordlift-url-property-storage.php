<?php
/**
 * Storage: Property Storage.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/linked-data/storage
 */

use Wordlift\Object_Type_Enum;

/**
 * Define the {@link Wordlift_Property_Storage} class.
 *
 * @since      3.15.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/linked-data/storage
 */
class Wordlift_Url_Property_Storage extends Wordlift_Storage {

	/**
	 * The {@link Wordlift_Property_Getter} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Property_Getter The {@link Wordlift_Property_Getter}
	 *                                     instance.
	 */
	private $property_getter;

	/**
	 * Create a {@link Wordlift_Property_Storage} instance.
	 *
	 * @since 3.15.0
	 *
	 * @param \Wordlift_Property_Getter $property_getter The {@link Wordlift_Property_Getter}
	 *                                                   instance.
	 */
	public function __construct( $property_getter ) {

		$this->property_getter = $property_getter;

	}

	/**
	 * Get the values for the property of the {@link WP_Post}.
	 *
	 * @since 3.15.0
	 *
	 * @param int $post_id The {@link WP_Post}'s id.
	 *
	 * @return array
	 */
	public function get( $post_id ) {

		return $this->property_getter->get( $post_id, Wordlift_Schema_Url_Property_Service::META_KEY, Object_Type_Enum::POST );
	}

}
