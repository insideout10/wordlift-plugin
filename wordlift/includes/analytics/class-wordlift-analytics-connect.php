<?php
/**
 * Deals with outputting the required entity and config data frontend for the
 * plugins offsite analytics integration functions.
 *
 * @since      3.21.0
 * @package    Wordlift
 * @subpackage Wordlift/analytics
 */

/**
 * The class that handles outputting data frontend for the analytics tracking
 * to offsite services parts of the plugin.
 */
class Wordlift_Analytics_Connect {

	const HANDLE = 'wordlift';

	/**
	 * Gets an array of related entities with their post IDs and titles.
	 *
	 * @method get_analytics_event_data
	 * @since  3.21.0
	 *
	 * @param int $post_id post id we want related data for.
	 *
	 * @return array
	 */
	public static function get_analytics_event_data( $post_id ) {
		// If no ID was passed get current ID.
		if ( ! $post_id ) {
			$post_id = get_queried_object_id();
		}
		/**
		 * TODO: set/get this from cache.
		 */
		$related_items = array();
		$related_ids   = wl_core_get_related_entity_ids( $post_id );

		$entity_service = Wordlift_Entity_Service::get_instance();
		// If the current item is also an entity then add it to the list of IDs.
		if ( $entity_service->is_entity( $post_id ) ) {
			$related_ids[] = $post_id;
		}
		$entity_type_service = Wordlift_Entity_Type_Service::get_instance();
		// Get the post titles of related items and connect them in an array.
		foreach ( $related_ids as $related_id ) {
			$type  = $entity_type_service->get( $related_id );
			$type  = isset( $type['uri'] ) ? $type['uri'] : 'unknown';
			$label = $entity_service->get_labels( $related_id );
			$label = $label[0];

			$related_items[ $related_id ] = array(
				'uri'   => $entity_service->get_uri( $related_id ),
				'type'  => $type,
				'label' => $label,
			);
		}

		return $related_items;
	}

	/**
	 * Gets the configuration data assosiated with the analytics settings. For
	 * frontend script use primarily.
	 *
	 * @method get_analytics_config_data
	 * @since  3.21.0
	 * @return array
	 */
	public static function get_analytics_config_data() {
		$configuration_service = Wordlift_Configuration_Service::get_instance();
		// get some values from the config service.
		$config = array(
			'entity_uri_dimension'  => $configuration_service->get_analytics_entity_uri_dimension(),
			'entity_type_dimension' => $configuration_service->get_analytics_entity_type_dimension(),
		);

		return $config;
	}

	/**
	 * Enqueues our scripts for the frontend analytics handling and attaches
	 * any data we will want to use there.
	 *
	 * @method enqueue_scripts
	 * @since  3.21.0
	 */
	public function enqueue_scripts() {
		$entity_data = self::get_analytics_event_data( get_the_ID() );
		// Bail early if there is no event data that we would send.
		if ( ! $entity_data ) {
			return;
		}
		$data = self::get_analytics_config_data();

		// Uses the analytics code in the main WordLift plugin.
		wp_localize_script( self::HANDLE, 'wordliftAnalyticsConfigData', $data );
		wp_localize_script( self::HANDLE, 'wordliftAnalyticsEntityData', $entity_data );
	}
}
