<?php

namespace Wordlift\Vocabulary;

use Wordlift\Api\Default_Api_Service;
use Wordlift\Vocabulary\Api\Background_Analysis_Endpoint;
use Wordlift\Vocabulary\Api\Entity_Rest_Endpoint;
use Wordlift\Vocabulary\Api\Reconcile_Progress_Endpoint;
use Wordlift\Vocabulary\Api\Search_Entity_Rest_Endpoint;
use Wordlift\Vocabulary\Api\Tag_Rest_Endpoint;
use Wordlift\Vocabulary\Cache\Cache_Service_Factory;
use Wordlift\Vocabulary\Dashboard\Term_Matches_Widget;
use Wordlift\Vocabulary\Data\Term_Count\Cached_Term_Count_Manager;
use Wordlift\Vocabulary\Data\Term_Count\Term_Count_Factory;
use Wordlift\Vocabulary\Data\Term_Data\Term_Data_Factory;
use Wordlift\Vocabulary\Hooks\Tag_Created_Hook;
use Wordlift\Vocabulary\Hooks\Term_Page_Hook;
use Wordlift\Vocabulary\Jsonld\Post_Jsonld;
use Wordlift\Vocabulary\Jsonld\Term_Jsonld;
use Wordlift\Vocabulary\Pages\Match_Terms;
use Wordlift\Vocabulary\Tabs\Settings_Tab;

class Vocabulary_Loader {

	public function init_vocabulary() {

		$api_service   = Default_Api_Service::get_instance();
		$cache_service = Cache_Service_Factory::get_cache_service();

		$analysis_service = new Analysis_Service( $api_service, $cache_service );

		$term_data_factory = new Term_Data_Factory( $analysis_service );

		$tag_rest_endpoint = new Tag_Rest_Endpoint( $term_data_factory );
		$tag_rest_endpoint->register_routes();

		new Search_Entity_Rest_Endpoint( $analysis_service, $cache_service );

		$entity_rest_endpoint = new Entity_Rest_Endpoint();
		$entity_rest_endpoint->register_routes();

		$post_jsonld = new Post_Jsonld();
		$post_jsonld->enhance_post_jsonld();

		$term_jsonld = new Term_Jsonld();
		$term_jsonld->init();

		$term_count = Term_Count_Factory::get_instance( Term_Count_Factory::CACHED_TERM_COUNT );
		new Match_Terms( $term_count );

		$analysis_background_service = new Analysis_Background_Service( $analysis_service );

		new Tag_Created_Hook( $analysis_background_service );

		new Background_Analysis_Endpoint( $analysis_background_service, $cache_service );

		$reconcile_progress_endpoint = new Reconcile_Progress_Endpoint();
		$reconcile_progress_endpoint->register_routes();

		$term_page_hook = new Term_Page_Hook( $term_data_factory );
		$term_page_hook->connect_hook();

		$dashboard_widget = new Term_Matches_Widget( $term_count );
		$dashboard_widget->connect_hook();

		$cached_term_count_manager = new Cached_Term_Count_Manager();
		$cached_term_count_manager->connect_hook();

		$settings_tab = new Settings_Tab();
		$settings_tab->connect_hook();

	}

}


