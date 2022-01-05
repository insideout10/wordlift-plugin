<?php

namespace Wordlift\Content\Wordpress;

use Wordlift\Entity\Entity_Uri_Generator;
use Wordlift\Object_Type_Enum;

class Wordpress_Content_Service_Hooks {

	public static function register() {
		add_action( 'wp_insert_post', array( get_called_class(), 'insert_post' ) );
		add_action( 'created_term', array( get_called_class(), 'created_term' ) );
		add_action( 'user_register', array( get_called_class(), 'user_register' ) );
	}

	public static function insert_post( $post_id ) {
		$content_service = Wordpress_Content_Service::get_instance();
		$content_id      = Wordpress_Content_Id::create_post( $post_id );
		try {
			$existing_id = $content_service->get_entity_id( $content_id );
			if ( empty( $existing_id ) ) {
				$rel_uri = Entity_Uri_Generator::create_uri( Object_Type_Enum::POST, $post_id );
				$content_service->set_entity_id( $content_id, $rel_uri );
			}
		} catch ( \Exception $e ) {
			//
		}
	}

	public static function created_term( $term_id ) {
		$content_service = Wordpress_Content_Service::get_instance();
		$content_id      = Wordpress_Content_Id::create_term( $term_id );
		try {
			$existing_id = $content_service->get_entity_id( $content_id );
			if ( empty( $existing_id ) ) {
				$rel_uri = Entity_Uri_Generator::create_uri( Object_Type_Enum::TERM, $term_id );
				$content_service->set_entity_id( $content_id, $rel_uri );
			}
		} catch ( \Exception $e ) {
			//
		}
	}

	public static function user_register( $user_id ) {
		$content_service = Wordpress_Content_Service::get_instance();
		$content_id      = Wordpress_Content_Id::create_user( $user_id );
		try {
			$existing_id = $content_service->get_entity_id( $content_id );
			if ( empty( $existing_id ) ) {
				$rel_uri = Entity_Uri_Generator::create_uri( Object_Type_Enum::USER, $user_id );
				$content_service->set_entity_id( $content_id, $rel_uri );
			}
		} catch ( \Exception $e ) {
			//
		}
	}

}
