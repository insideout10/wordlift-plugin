<?php

namespace Wordlift\Videoobject;

use Wordlift\Cache\Ttl_Cache;
use Wordlift\Common\Loader\Default_Loader;
use Wordlift\Videoobject\Ajax\Video_Key_Validation_Service;
use Wordlift\Videoobject\Api\Rest_Controller;
use Wordlift\Videoobject\Background_Process\Videoobject_Background_Process;
use Wordlift\Videoobject\Background_Process\Videos_Data_Source;
use Wordlift\Videoobject\Data\Video_Storage\Video_Storage_Factory;
use Wordlift\Videoobject\Filters\Embed_Shortcode_Capture;
use Wordlift\Videoobject\Filters\Jw_Player_Capture;
use Wordlift\Videoobject\Filters\Post_Filter;
use Wordlift\Videoobject\Jsonld\Jsonld;
use Wordlift\Videoobject\Pages\Import_Videos_Page;
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

		$sitemap_cache = new Ttl_Cache( 'wl_video_sitemap', 86400 );

		$video_processor = new Video_Processor();
		// Hook in to save_post to save the videos
		$post_filter = new Post_Filter( $video_processor );
		$post_filter->init();
		// Add entry to wordlift admin tabs
		$settings_tab = new Settings_Tab();
		$settings_tab->init();

		$video_sitemap = new Video_Sitemap( $sitemap_cache );
		$video_sitemap->init();

		$background_process_data_source = new Videos_Data_Source( '__wl_videoobject_import_state' );
		$background_process             = new Videoobject_Background_Process( $video_processor, $background_process_data_source );

		$rest_controller = new Rest_Controller( $background_process );
		$rest_controller->register_all_routes();

		$post_edit_screen = new Post_Edit_Screen();
		$post_edit_screen->init();

		new Import_Videos_Page();

		/**
		 * @since 3.32.0
		 * Allow videoobject to capture embed shortcode.
		 */
		$embed_shortcode_capture = new Embed_Shortcode_Capture();
		$embed_shortcode_capture->init();

		/**
		 * @since 3.32.0
		 * Get videos from jw player.
		 */
		$jw_player_capture_videos = new Jw_Player_Capture();
		$jw_player_capture_videos->init();

		/**
		 * Validate API Key for Youtube, Vimeo.
		 */
		new Video_Key_Validation_Service();

	}

	public function get_feature_slug() {
		return 'videoobject';
	}

	public function get_feature_default_value() {
		return false;
	}
}
