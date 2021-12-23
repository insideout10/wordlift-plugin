<?php

namespace Wordlift\Entity_Auto_Publis;

use Wordlift\Common\Loader\Default_Loader;
use Wordlift\Post\Post_Adapter;

class Loader extends Default_Loader {

	public function init_all_dependencies() {
		require_once( '../../admin/wordlift_admin_save_post.php' );
		new Post_Adapter();
	}

	protected function get_feature_slug() {
		return 'entity-auto-publish';
	}

	protected function get_feature_default_value() {
		return true;
	}
}
