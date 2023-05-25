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
 * Define the {@link Wordlift_Entity_Page_Service} class.
 *
 * @since   3.12.0
 * @package Wordlift
 */
class Wordlift_Entity_Page_Service {

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
		// not a entity type achieve query, or if the `suppress_filters` is set.
		//
		// Note that it is unlikely for `suppress_filter` to be set on the front
		// end, but let's be safe if it is set the calling code assumes no
		// modifications of queries.

		// Ignore admin side request, requests for which filters should be
		// suppressed, and when we are not on a entity type archive page.
		if ( is_admin() ||
			 ! is_tax( Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME ) ||
			 ! empty( $query->query_vars['suppress_filters'] )
		) {
			return;
		}

		// Events should be sorted by start date in descending order.
		if ( is_tax( Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME, 'event' ) ) {

			// Update the query to use the start time meta and desc order.
			$meta_query = array(
				array(
					'key' => Wordlift_Schema_Service::FIELD_DATE_START,
				),
			);
			$query->set( 'meta_query', $meta_query );
			$query->set( 'orderby', 'meta_value' );
			$query->set( 'order', 'DESC' );
		} else {
			/*
			 * All other entity types should be sorted by their connectivity.
			 * For this we need to query the relationship table which has
			 * to be done by manipulating the SQL generated for the query.
			 * As this is impossible to be done by changing the query, we Set
			 * additional filters to handle it.
			 */

			add_filter( 'posts_join', array( $this, 'posts_join' ) );
			add_filter( 'posts_groupby', array( $this, 'posts_groupby' ) );
			add_filter( 'posts_orderby', array( $this, 'posts_orderby' ) );
		}
	}

	/**
	 * Filter handler that sets the join part of a query to include the
	 * relationship table to be able to use it in the sorting.
	 *
	 * @since 3.15.0
	 *
	 * @param string $join_statement The join part of the SQL statement which is used for the query.
	 *
	 * @return string An join SQL which add the relationships table to the join.
	 */
	public function posts_join( $join_statement ) {

		global $wpdb;

		$join_statement .= " LEFT JOIN {$wpdb->prefix}wl_relation_instances ri "
						   . " ON (ri.object_id = {$wpdb->posts}.ID)";

		// Remove to make sure it will not run agan in other context.
		remove_filter( 'posts_join', array( $this, 'posts_join' ) );

		return $join_statement;
	}

	/**
	 * Filter handler that sets the groupby part of a query to include the
	 * relationship table to be able to use it in the sorting.
	 *
	 * @since 3.15.0
	 *
	 * @param string $groupby_statement The groupby part of the SQL statement which is used for the query.
	 *
	 * @return string A groupby SQL which add the relationships table to the join.
	 */
	public function posts_groupby( $groupby_statement ) {

		$groupby_statement = 'ri.object_id, ' . $groupby_statement;

		// Remove to make sure it will not run agan in other context.
		remove_filter( 'posts_groupby', array( $this, 'posts_groupby' ) );

		return $groupby_statement;
	}

	/**
	 * Filter handler that sets the orderby part of a query to sort by number of
	 * relationships.
	 *
	 * @since 3.15.0
	 *
	 * @param string $orderby_statement The orderby part of the SQL statement which is used for the query.
	 *
	 * @return string An orderby SQL which sorts by the number of relationships
	 */
	public function posts_orderby( $orderby_statement ) {

		$orderby_statement = 'COUNT( ri.object_id ) DESC, ' . $orderby_statement;

		// Remove to make sure it will not run agan in other context.
		remove_filter( 'posts_orderby', array( $this, 'posts_orderby' ) );

		return $orderby_statement;
	}

}
