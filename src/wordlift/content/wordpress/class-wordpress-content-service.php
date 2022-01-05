<?php

namespace Wordlift\Content\Wordpress;

use Exception;
use Wordlift\Assertions;
use Wordlift\Content\Content_Migration;
use Wordlift\Content\Content_Service;

class Wordpress_Content_Service implements Content_Service {

	/**
	 * @var Content_Service[]
	 */
	private $delegates = array();

	private static $instance = null;

	protected function __constructor() {
	}

	/**
	 * The singleton instance.
	 *
	 * @return Wordpress_Content_Service
	 * @throws Exception
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();

			if ( ! apply_filters( 'wl_feature__enable__rel-item-id', false ) ) {
				$post_content_service = Wordpress_Post_Content_Legacy_Service::get_instance();
			} else {
				// Migrate `entity_url` from post-meta to wl_entities.
				$content_migration = new Content_Migration();
				$content_migration->migrate();

				// Create the post content service that uses wl_entities.
				$post_content_service = Wordpress_Post_Content_Service::get_instance();
			}

			self::$instance->register_delegate( $post_content_service );
			self::$instance->register_delegate( Wordpress_Term_Content_Legacy_Service::get_instance() );
			self::$instance->register_delegate( Wordpress_User_Content_Legacy_Service::get_instance() );

		}

		return self::$instance;
	}

	public function register_delegate( $delegate ) {
		Assertions::is_a( $delegate, 'Wordlift\Content\Content_Service', 'A `delegate` must implement the `Wordlift\Content\Content_Service` interface.' );

		$this->delegates[] = $delegate;
	}

	function get_by_entity_id( $uri ) {
		foreach ( $this->delegates as $delegate ) {
			$content = $delegate->get_by_entity_id_or_same_as( $uri );
			if ( isset( $content ) ) {
				return $content;
			}
		}

		return null;
	}

	/**
	 * Get a
	 * @throws Exception
	 */
	function get_by_entity_id_or_same_as( $uri ) {
		foreach ( $this->delegates as $delegate ) {
			$content = $delegate->get_by_entity_id_or_same_as( $uri );
			if ( isset( $content ) ) {
				return $content;
			}
		}

		return null;
	}

	function get_entity_id( $content_id ) {
		foreach ( $this->delegates as $delegate ) {
			if ( $delegate->supports( $content_id ) && $uri = $delegate->get_entity_id( $content_id ) ) {
				return $uri;
			}
		}

		return null;
	}

	function set_entity_id( $content_id, $uri ) {
		foreach ( $this->delegates as $delegate ) {
			if ( $delegate->supports( $content_id ) ) {
				$delegate->set_entity_id( $content_id, $uri );

				return;
			}
		}

		throw new Exception( 'Not supported' );
	}

	function supports( $content_id ) {
		foreach ( $this->delegates as $delegate ) {
			if ( $delegate->supports( $content_id ) ) {
				return true;
			}
		}

		return false;
	}

}
