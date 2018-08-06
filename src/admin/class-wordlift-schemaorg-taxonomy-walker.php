<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 06.08.18
 * Time: 11:12
 */

class Wordlift_Schemaorg_Taxonomy_Walker extends Walker_Category_Checklist {

	public function __construct() {

		add_filter( 'wp_terms_checklist_args', array( $this, 'terms_checklist_args' ) );

	}

	public function terms_checklist_args( $args ) {

		if ( ! isset( $args['taxonomy'] )
		     || Wordlift_Entity_Type_Taxonomy_Service::TAXONOMY_NAME !== $args['taxonomy'] ) {
			return $args;
		}

		// We override the way WP prints the taxonomy metabox HTML.
		$args['walker'] = $this;

		return $args;
	}

	/**
	 * @inheritdoc
	 */
	public function walk( $elements, $max_depth ) {
		$args   = array_slice( func_get_args(), 2 );
		$output = '';

		//invalid parameter or nothing to walk
		if ( $max_depth < - 1 || empty( $elements ) ) {
			return $output;
		}

		$parent_field = $this->db_fields['parent'];

		// flat display
		if ( - 1 == $max_depth ) {
			$empty_array = array();
			foreach ( $elements as $e ) {
				$this->display_element( $e, $empty_array, 1, 0, $args, $output );
			}

			return $output;
		}

		/*
		 * Need to display in hierarchical order.
		 * Separate elements into two buckets: top level and children elements.
		 * Children_elements is two dimensional array, eg.
		 * Children_elements[10][] contains all sub-elements whose parent is 10.
		 */
		$top_level_elements = array();
		$children_elements  = array();
		foreach ( $elements as $e ) {
			if ( empty( $e->$parent_field ) ) {
				$top_level_elements[] = $e;
			} else {
				$children_elements[ $e->$parent_field ][] = $e;
			}
		}

		/*
		 * When none of the elements is top level.
		 * Assume the first one must be root of the sub elements.
		 */
		if ( empty( $top_level_elements ) ) {

			$first = array_slice( $elements, 0, 1 );
			$root  = $first[0];

			$top_level_elements = array();
			$children_elements  = array();
			foreach ( $elements as $e ) {
				if ( $root->$parent_field == $e->$parent_field ) {
					$top_level_elements[] = $e;
				} else {
					$children_elements[ $e->$parent_field ][] = $e;
				}
			}
		}

		foreach ( $top_level_elements as $e ) {
			$this->display_element( $e, $children_elements, $max_depth, 0, $args, $output );
		}

		/*
		 * If we are displaying all levels, and remaining children_elements is not empty,
		 * then we got orphans, which should be displayed regardless.
		 */
		if ( ( $max_depth == 0 ) && count( $children_elements ) > 0 ) {
			$empty_array = array();
			foreach ( $children_elements as $orphans ) {
				foreach ( $orphans as $op ) {
					$this->display_element( $op, $empty_array, 1, 0, $args, $output );
				}
			}
		}

		return $output;
	}

}