<?php

namespace Wordlift\Content\Wordpress;

use Exception;
use Wordlift\Assertions;
use Wordlift\Content\Content_Service;

class Wordpress_Content_Service implements Content_Service {

	/**
	 * @var Content_Service[]
	 */
	private $delegates;

	private static $instance;

	/**
	 * The singleton instance. We use this only to provide this instance to those classes where we have no access to
	 * the constructor.
	 *
	 * @return Wordpress_Content_Service
	 * @deprecated
	 */
	public static function get_instance() {
		return self::$instance;
	}

	/**
	 * Create an instance of the {@link Content_Service}.
	 *
	 * @param Content_Service[] $delegates
	 */
	public function __construct( $delegates = array() ) {
		$this->delegates = $delegates;

		self::$instance = $this;
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
