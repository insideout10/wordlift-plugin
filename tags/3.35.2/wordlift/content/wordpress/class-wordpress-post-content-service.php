<?php

/**
 * # create table UFeCMp_9_wl_entities( type int not null, id int not null, rel_uri varchar(500) unique not null, unique key uq_9_wl_entities__type__id ( type, id ) );
 * # create table ufecmp_9_wl_entities( type int not null, id int not null, rel_uri varchar(500) unique not null, unique key uq_9_wl_entities__type__id ( type, id ) );
 */

namespace Wordlift\Content\Wordpress;

use Wordlift\Content\Content_Migration;
use Wordlift\Content\Content_Service;

class Wordpress_Post_Content_Service {

	private static $instance = null;

	/**
	 * The singleton instance. We use this only to provide this instance to those classes where we have no access to
	 * the constructor.
	 *
	 * @return Content_Service
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {

			if ( ! apply_filters( 'wl_feature__enable__rel-item-id', false ) ) {
				self::$instance = Wordpress_Post_Content_Legacy_Service::get_instance();
			} else {
				// Migrate `entity_url` from post-meta to wl_entities.
				$content_migration = new Content_Migration();
				$content_migration->migrate();

				// Create the post content service that uses wl_entities.
				self::$instance = Wordpress_Post_Content_Table_Service::get_instance();
			}

		}

		return self::$instance;
	}

}
