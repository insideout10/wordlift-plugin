<?php

namespace Wordlift\Jsonld;

use Wordlift\Object_Type_Enum;

class Jsonld_User_Service {

	/**
	 * @var \Wordlift_User_Service $user_service
	 */
	private $user_service;

	/**
	 * @var Jsonld_Service
	 */
	private $jsonld_service;

	/**
	 * Jsonld_User_Service constructor.
	 *
	 * @param \Wordlift_User_Service $user_service
	 */
	public function __construct( $user_service ) {
		$this->user_service = $user_service;
	}

	/**
	 * @param Jsonld_Service $jsonld_service
	 */
	public function set_jsonld_service( $jsonld_service ) {
		$this->jsonld_service = $jsonld_service;
	}

	public function get( $user_id ) {
		$userdata = get_userdata( $user_id );

		// Bail out if user not found.
		if ( ! ( $userdata instanceof \WP_User ) ) {
			return array();
		}

		// Return the post JSON-LD if a post has been bound to this user.
		$post_id = $this->user_service->get_entity( $user_id );
		if ( ! empty( $post_id ) && isset( $this->jsonld_service ) ) {
			return $this->jsonld_service->get( Object_Type_Enum::POST, $post_id );
		}

		// Bail out if the user doesn't have a valid URI.
		$uri = $this->user_service->get_uri( $user_id );
		if ( empty( $uri ) ) {
			return array();
		}

		// Finally return the user's JSON-LD.
		$data = array(
			'@context' => 'http://schema.org',
			'@id'      => $uri,
			'@type'    => 'Person',
		);

		if ( ! empty( $userdata->display_name ) ) {
			$data['name'] = $userdata->display_name;
		}

		if ( ! empty( $userdata->first_name ) ) {
			$data['givenName'] = $userdata->first_name;
		}

		if ( ! empty( $userdata->last_name ) ) {
			$data['familyName'] = $userdata->last_name;
		}

		if ( ! empty( $userdata->user_url ) ) {
			$data['url'] = $userdata->user_url;
		}

		return array( $data );
	}

}
