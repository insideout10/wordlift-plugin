<?php

namespace Wordlift\Videoobject;

use Wordlift\Cache\Ttl_Cache;
use Wordlift\Common\Loader\Default_Loader;
use Wordlift\Videoobject\Api\Rest_Controller;
use Wordlift\Videoobject\Data\Video_Storage\Video_Storage_Factory;
use Wordlift\Videoobject\Filters\Post_Filter;
use Wordlift\Videoobject\Jsonld\Jsonld;
use Wordlift\Videoobject\Sitemap\Video_Sitemap;
use Wordlift\Videoobject\Tabs\Settings_Tab;
use Wordlift\Videoobject\Ui\Post_Edit_Screen;


/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Loader extends Default_Loader {

	public function init_all_dependencies() {
		$video_storage = Video_Storage_Factory::get_storage();
		new Jsonld( $video_storage );

		$sitemap_cache = new Ttl_Cache( "wl_video_sitemap", 86400 );

		// Hook in to save_post to save the videos
		$post_filter = new Post_Filter();
		$post_filter->init();
		// Add entry to wordlift admin tabs
		$settings_tab = new Settings_Tab();
		$settings_tab->init();


		$video_sitemap = new Video_Sitemap( $sitemap_cache );
		$video_sitemap->init();
		$rest_controller = new Rest_Controller();
		$rest_controller->register_all_routes();

		$post_edit_screen = new Post_Edit_Screen();
		$post_edit_screen->init();
	}

	public function get_feature_slug() {
		return 'videoobject';
	}

	public function get_feature_default_value() {
		return false;
	}
}