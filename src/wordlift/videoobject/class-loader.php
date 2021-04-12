<?php

namespace Wordlift\Videoobject;

use Wordlift\Common\Loader\Default_Loader;
use Wordlift\Videoobject\Data\Video_Storage\Video_Storage_Factory;
use Wordlift\Videoobject\Filters\Post_Filter;
use Wordlift\Videoobject\Jsonld\Jsonld;


/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Loader extends Default_Loader {

	public function init_all_dependencies() {
		$video_storage = Video_Storage_Factory::get_storage();
		new Jsonld( $video_storage );
		// Hook in to save_post to save the videos
		new Post_Filter();
	}

	public function get_feature_slug() {
		return 'videoobject';
	}

	public function get_feature_default_value() {
		return false;
	}
}