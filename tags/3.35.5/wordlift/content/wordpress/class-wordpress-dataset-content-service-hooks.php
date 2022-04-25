<?php

namespace Wordlift\Content\Wordpress;

use Exception;
use Wordlift\Entity\Entity_Uri_Generator;

class Wordpress_Dataset_Content_Service_Hooks {

	public static function register() {
		add_action( 'wp_insert_post', array( get_called_class(), 'insert_post' ) );
		add_action( 'after_delete_post', array( get_called_class(), 'after_delete_post' ) );
		add_action( 'created_term', array( get_called_class(), 'created_term' ) );
		add_action( 'delete_term', array( get_called_class(), 'delete_term' ) );
		add_action( 'user_register', array( get_called_class(), 'user_register' ) );
		add_action( 'deleted_user', array( get_called_class(), 'deleted_user' ) );
	}

	/**
	 * @throws Exception
	 */
	public static function insert_post( $post_id ) {
		if ( ! wp_is_post_revision( $post_id ) ) {
			self::set_entity_id( Wordpress_Content_Id::create_post( $post_id ) );
		}
	}

	public static function after_delete_post( $post_id ) {
		self::delete( Wordpress_Content_Id::create_post( $post_id ) );
	}

	/**
	 * @throws Exception
	 */
	public static function created_term( $term_id ) {
		self::set_entity_id( Wordpress_Content_Id::create_term( $term_id ) );
	}


	public static function delete_term( $term_id ) {
		self::delete( Wordpress_Content_Id::create_term( $term_id ) );
	}

	/**
	 * @throws Exception
	 */
	public static function user_register( $user_id ) {
		self::set_entity_id( Wordpress_Content_Id::create_user( $user_id ) );
	}

	public static function deleted_user( $user_id ) {
		self::delete( Wordpress_Content_Id::create_user( $user_id ) );
	}

	/**
	 * @param Wordpress_Content_Id $content_id
	 *
	 * @return void
	 * @throws Exception
	 */
	private static function set_entity_id( $content_id ) {
		$content_service = Wordpress_Content_Service::get_instance();
		try {
			$existing_id = $content_service->get_entity_id( $content_id );
			if ( empty( $existing_id ) ) {
				$rel_uri = Entity_Uri_Generator::create_uri( $content_id->get_type(), $content_id->get_id() );
				$content_service->set_entity_id( $content_id, $rel_uri );
			}
		} catch ( Exception $e ) {
			//
		}
	}

	private static function delete( $content_id ) {
		$content_service = Wordpress_Content_Service::get_instance();
		$content_service->delete( $content_id );
	}

}
