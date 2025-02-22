<?php

/**
 * # create table UFeCMp_9_wl_entities( type int not null, id int not null, rel_uri varchar(500) unique not null, unique key uq_9_wl_entities__type__id ( type, id ) );
 * # create table ufecmp_9_wl_entities( type int not null, id int not null, rel_uri varchar(500) unique not null, unique key uq_9_wl_entities__type__id ( type, id ) );
 */

namespace Wordlift\Content\WordPress;

use Wordlift\Content\Content_Migration;
use Wordlift\Content\Content_Service;
use Wordlift\Content\Main_Ingredient_Content_Migration;

// phpcs:ignore WordPress.WP.CapitalPDangit.MisspelledClassName
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

			// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
			if ( ! apply_filters( 'wl_feature__enable__rel-item-id', false ) ) {
				self::$instance = Wordpress_Post_Content_Legacy_Service::get_instance();
			} else {
				// Migrate `entity_url` from post-meta to wl_entities.
				$content_migration = new Content_Migration();
				$content_migration->migrate();

				// Migrate `main_ingredient`.
				$main_ingredient_content_migration = new Main_Ingredient_Content_Migration();
				$main_ingredient_content_migration->migrate();

				// Create the post content service that uses wl_entities.
				self::$instance = Wordpress_Post_Content_Table_Service::get_instance();
			}
		}

		return self::$instance;
	}
}
