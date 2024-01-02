<?php

/**
 * Module: 	Google Organization Knowledge Panel
 * Class: 	Organization_Knowledge_Panel_Service
 *
 * A class that sets and sets the data for the Google Organization Panel API endpoints.
 *
 * @since      
 * @package Wordlift/modules/google-organization-kp
 */

namespace Wordlift\Modules\Google_Organization_Kp;

use Wordlift_Countries;
use Wordlift_Entity_Type_Service;
use Wordlift_Configuration_Service;
use Wordlift_Publisher_Service;
use Wordlift_Storage_Factory;
use Wordlift_Schema_Service;

Class Organization_Knowledge_Panel_Service {

    const PAGINATION_NUM_OF_PAGES = 100;
	
	/**
	 * Get a list of pages
	 * 
	 * @param 	int    	$pagination 		The pagination offset.
	 * @param 	string 	$title_starts_with 	Case instensitive filter for page titles.
	 *
	 * @return 	array	Array of page IDs and titles.
	 * 
	 * @since 
	 */
    public function get_pages( $pagination, $title_starts_with ) {
        // Get a number of pages starting at a given offset.
		$pages = get_pages(
			array(
				'number' => self::PAGINATION_NUM_OF_PAGES,
				'offset' => (int) $pagination * self::PAGINATION_NUM_OF_PAGES
			)
		);
        
		$data = array();

		// Arrange the data.
		foreach( $pages as $page ) {
			$data[] = array(
				'id'	=> $page->ID,
				'title' => $page->post_title
			);
		}

        // Filter the array to only contain items whose title starts with the `title_starts_with` param.
		if ( count($data) > 0 && isset( $title_starts_with ) ) {
			$filter_str = strtolower( $title_starts_with );
			$data = array_filter( 
				$data,
				function( $page ) use ( $filter_str ) {
					// Check that the start of the string matches the filter string.
					return substr( strtolower($page['title']), 0, strlen($filter_str)) === $filter_str;
				}
			);
		}

		return $data;
    }

	/**
	 * Get an associative array of country codes and country names in the language of the website.
	 * 
	 * @return 	array 	Country codes and names.
	 *
	 * @since	
	 */
    public function get_countries() {
		// Get the list of countries in the current site language.
        $countries = Wordlift_Countries::get_countries();

		$data = array();

		// Arrange the data.
		foreach( $countries as $country_code => $country_name ) {
			$data[] = array(
				'code' => $country_code,
				'name' => $country_name
			);
		}

        return $data;
    }

	/**
	 * Get relevant useful data if a publisher already exists
	 * 
	 * @return 	array 	Publisher data.
	 * 
	 * @since
	 */
    public function get_form_data() {
		// Get the publisher ID.
		$publisher_id = Wordlift_Configuration_Service::get_instance()->get_publisher_id();

		$data = array();

		// If a publisher exists set the data.
		if ( isset($publisher_id) && $publisher_id !== "(none)" ) {
			// Add the publisher ID.
			$data['id'] = $publisher_id;
			
			// Add the publisher name.
			$publisher_post = get_post( $publisher_id );
			$data['name'] = $publisher_post->post_title;

			// Add the publisher type.
			$publisher_post_entity = Wordlift_Entity_Type_Service::get_instance()->get( $publisher_id );
			$data['type'] = $publisher_post_entity['label'];

			// Add the publisher logo.
			$publisher_post_logo = Wordlift_Publisher_Service::get_instance()->get_publisher_logo( $publisher_id );
			if ( ! empty( $publisher_post_logo ) ) {
				$data['logo'] = $publisher_post_logo['url'];
			}
			
			// Add the sameAs values associated with the publisher.
			$storage_factory = Wordlift_Storage_Factory::get_instance();
			$sameas          = $storage_factory->post_meta( Wordlift_Schema_Service::FIELD_SAME_AS )->get( $publisher_id );
			if ( ! empty( $sameas ) ) {
				$data['publisher']['sameAs'] = $sameas;
			}
		}

        return $data;
    }

    public function set_form_data(  ) {
		$publisher_id = Wordlift_Configuration_Service::get_instance()->get_publisher_id();

        return;
    }
}