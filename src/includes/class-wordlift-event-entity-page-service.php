<?php
/**
 * Services: Set the order in which entities are displayed on the archive
 * page of the event entity.
 *
 * Sorts the order of the entities being displayed by reverse event start time
 *
 * @since   3.12.0
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_Event_Entity_Page_Service} class.
 *
 * @since   3.12.0
 * @package Wordlift
 */
class Wordlift_Event_Entity_Page_Service {

	/**
	 * Set the entity post types as one to be included in archive pages.
	 *
	 * In order to have entities show up in standard WP categories (Posts categories)
	 * we configure the `entity` post type, but we also need to alter the main
	 * WP query (which by default queries posts only) to include the `entities`.
	 *
	 * @since 3.12.0
	 *
	 * @param WP_Query $query WP's {@link WP_Query} instance.
	 */
	public function pre_get_posts( $query ) {

		// Only for the main query, avoid problems with widgets and what not.
		if ( ! $query->is_main_query() ) {
			return;
		}

		// We don't want to alter the query if we're in the admin UI, if this is
		// not a event achieve query, or if the `suppress_filters` is set.
		//
		// Note that it is unlikely for `suppress_filter` to be set on the front
		// end, but let's be safe if it is set the calling code assumes no
		// modifications of queries.
		//
		// is_admin is needed, otherwise category based post filters will show
		// both types and at the current release (4.7) it causes PHP errors.
		if ( is_admin() ||
		     ! is_tax( Wordlift_Entity_Types_Taxonomy_Service::TAXONOMY_NAME, 'event' ) ||
		     ! empty( $query->query_vars['suppress_filters'] )
		) {
			return;
		}

		// Update the query to use the start time meta and desc order.
		$meta_query[] = array(
			'key' => Wordlift_Schema_Service::FIELD_DATE_START,
		);
		$query->set( 'meta_query', $meta_query );
		$query->set( 'orderby', 'meta_value' );
		$query->set( 'order', 'DESC' );
		
	}

}
