<?php

namespace Wordlift\Content\Wordpress;

use Exception;
use Wordlift\Assertions;
use Wordlift\Content\Content_Service;

// phpcs:ignore WordPress.WP.CapitalPDangit.MisspelledClassName
class Wordpress_Dataset_Content_Service implements Content_Service {

	/**
	 * @var Content_Service[]
	 */
	private $delegates = array();

	protected function __construct() {

	}

	private static $instance = null;

	/**
	 * The singleton instance.
	 *
	 * @return Content_Service
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();

			self::$instance->register_delegate( Wordpress_Post_Content_Service::get_instance() );
			self::$instance->register_delegate( Wordpress_Term_Content_Service::get_instance() );
			self::$instance->register_delegate( Wordpress_User_Content_Legacy_Service::get_instance() );

			Wordpress_Dataset_Content_Service_Hooks::register();
		}

		return self::$instance;
	}

	public function register_delegate( $delegate ) {
		Assertions::is_a( $delegate, 'Wordlift\Content\Content_Service', 'A `delegate` must implement the `Wordlift\Content\Content_Service` interface.' );

		$this->delegates[] = $delegate;
	}

	public function get_by_entity_id( $uri ) {
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
	 *
	 * @throws Exception in case of error.
	 */
	public function get_by_entity_id_or_same_as( $uri ) {
		foreach ( $this->delegates as $delegate ) {
			$content = $delegate->get_by_entity_id_or_same_as( $uri );
			if ( isset( $content ) ) {
				return $content;
			}
		}

		return null;
	}

	public function get_entity_id( $content_id ) {
		foreach ( $this->delegates as $delegate ) {
			if ( ! $delegate->supports( $content_id ) ) {
				continue;
			}
			$uri = $delegate->get_entity_id( $content_id );
			if ( $uri ) {
				return $uri;
			}
		}

		return null;
	}

	public function set_entity_id( $content_id, $uri ) {
		foreach ( $this->delegates as $delegate ) {
			if ( $delegate->supports( $content_id ) ) {
				$delegate->set_entity_id( $content_id, $uri );

				return;
			}
		}

		throw new Exception( 'Not supported' );
	}

	public function supports( $content_id ) {
		foreach ( $this->delegates as $delegate ) {
			if ( $delegate->supports( $content_id ) ) {
				return true;
			}
		}

		return false;
	}

	public function delete( $content_id ) {
		foreach ( $this->delegates as $delegate ) {
			if ( $delegate->supports( $content_id ) ) {
				$delegate->delete( $content_id );
				break;
			}
		}
	}

	public function get_about_jsonld( $content_id ) {
		foreach ( $this->delegates as $delegate ) {
			if ( $delegate->supports( $content_id ) ) {
				return $delegate->get_about_jsonld( $content_id );
			}
		}

		return null;
	}

	public function set_about_jsonld( $content_id, $value ) {
		foreach ( $this->delegates as $delegate ) {
			if ( $delegate->supports( $content_id ) ) {
				return $delegate->set_about_jsonld( $content_id, $value );
			}
		}

		return false;
	}
}
