<?php

/**
 * This class is responsible for setting up the plug-in in WordPress.
 * @requires WordPressFramework [01].
 */
class WordLiftSetup {

	// a reference to the logger.
	private static $logger;

	/*
	 * Sets up the WordLift plug-in pre-requisites.
	*/
	function load() {
		global $entities_box_service, $wordlift, $entity_service;

		self::$logger = Logger::getLogger(__CLASS__);

		self::$logger->debug('Setting up WordLift.');

		if (NULL == $entities_box_service) {
			self::$logger->error('EntitiesBoxService is not defined.');
			return;
		}
		if (NULL == $wordlift) {
			self::$logger->error('WordLift is not defined.');
			return;
		}
		if (NULL == $entity_service) {
			self::$logger->error('EntityService is not defined.');
			return;
		}

		self::create_post_type();
		self::create_all_entities_page();
		self::create_entities_map_page();
		self::kill_autosave_on_entities();

		// administration area
		add_action('admin_enqueue_scripts',	array('wordliftsetup', 			'admin_enqueue_scripts'));

		// adds the meta-boxes for the admin area
		add_action('add_meta_boxes', 		array( $entities_box_service, 	'create_custom_box'));

		add_action('edit_post', 			array( $wordlift, 				'analyze_text'));
		add_action('edit_post', 			array( $entity_service, 		'save_entity_from_post_edit'));



		// front-end
		add_action('wp_enqueue_scripts',	array('wordliftsetup', 			'enqueue_scripts'));
		add_filter('the_content', 			'display_the_content' );
		// uncomment this to enable a custom menu.
		// add_action('admin_menu', 			'create_admin_menu');
		add_action( 'admin_menu', create_function('', "OptionsPageService::load('SettingsPageView');"));

        // from WordPress 3.1 onward...
        /*
		add_filter( 'manage_'.WORDLIFT_20_ENTITY_CUSTOM_POST_TYPE.'_posts_columns' , 		array('WordLiftSetup', 'manage_entities_columns'));
		add_action( 'manage_'.WORDLIFT_20_ENTITY_CUSTOM_POST_TYPE.'_posts_custom_column' , 	array('WordLiftSetup', 'manage_entities_custom_column'), 10, 2);
		*/

        // before WordPress 3.1
		add_action( 'manage_edit-' . WordLiftPlugin::POST_TYPE . '_columns' , 	array('WordLiftSetup', 'manage_entities_columns'), 10, 2);
		add_action( "manage_posts_custom_column" , 	array('WordLiftSetup', 'manage_entities_custom_column'), 10, 2);
		
		// add short-codes.
		ShortCodeService::registerShortCodes();

		self::$logger->debug('Set-up complete.');
	}
	
	function create_all_entities_page() {
		$page = get_page_by_path(WORDLIFT_20_ENTITIES_PAGE_NAME);

		if (NULL == $page) {
			self::$logger->debug('Creating the All Entities page ['.WORDLIFT_20_ENTITIES_PAGE_NAME.'].');
				
			$page = wp_insert_post(	array(
					'post_title' => 'All Entities',
					'post_status' => 'publish',
					'post_name' => WORDLIFT_20_ENTITIES_PAGE_NAME,
					'post_content' => '[entities.treemap]',
					'post_type' => 'page')
					,$wp_error);
				
			if (NULL != $wp_error) {
				self::$logger->error('An error occurred: '.var_export($wp_error,true));
			}
		}
	}

	function create_entities_map_page() {
		$page = get_page_by_path(WORDLIFT_20_ENTITIES_MAP_PAGE_NAME);

		if (NULL == $page) {
			self::$logger->debug('Creating the Entities Map page ['.WORDLIFT_20_ENTITIES_MAP_PAGE_NAME.'].');

			$page = wp_insert_post(	array(
					'post_title' => 'Entities Map',
					'post_status' => 'publish',
					'post_name' => WORDLIFT_20_ENTITIES_MAP_PAGE_NAME,
					'post_content' => '[entities.geomap]',
					'post_type' => 'page')
					,$wp_error);

			if (NULL != $wp_error) {
				self::$logger->error('An error occurred: '.var_export($wp_error,true));
			}
		}
	}

	function manage_entities_custom_column($column, $post_id) {
		switch ( $column ) {
			case 'type':
				echo get_post_meta( $post_id , WORDLIFT_20_FIELD_SCHEMA_TYPE , true );
				break;
			case 'thumbnail':
				$image = get_post_meta( $post_id , WORDLIFT_20_FIELD_IMAGE , true );
				echo '<img src="'.$image.'" onerror="jQuery(this).remove();" style="width: 80px;" />';
				break;
			case 'posts':
				$posts = get_post_meta( $post_id , WORDLIFT_20_ENTITY_POSTS , false );
				echo count($posts);
				break;
		}
	}

	function manage_entities_columns($columns) {
		return array(
				'cb' 		=> '<input type="checkbox" />',
				'title' 	=> __('Title'),
				'thumbnail'	=> __('Thumbnail'),
				'type' 		=> __('Type'),
				'posts' 	=> __('Posts'),
				'date' 		=> __('Date')
		);
	}

	function kill_autosave_on_entities() {
		add_filter( 'script_loader_src', array('WordLiftSetup', 'kill_autosave_on_entities_callback'), 10, 2 );
	}

	function kill_autosave_on_entities_callback( $src, $handle ) {
		global $typenow;
		if( 'autosave' != $handle || $typenow != WORDLIFT_20_ENTITY_CUSTOM_POST_TYPE )
			return $src;
		return '';
	}


	/*
	 * enqueus the JavaScript and style-sheets for WordPress inclusion.
	 */
	function admin_enqueue_scripts() {
	    self::$logger->debug("Enqueuing admin scripts.");
	    
		self::common_scripts();

		wp_enqueue_style('wordlift-admin.css',
				plugins_url('/css/admin.css', WORDLIFT_20_ROOT_PATH));

		wp_enqueue_script('wordlift-admin',
				plugins_url('/js/wordlift-admin.js', WORDLIFT_20_ROOT_PATH),
				array('backbone'),
				false,
				false);

	}

	/**
	 * the JavaScript libraries needed for WordPress front-end. 
	 */
	function enqueue_scripts() {
		
		// declare scripts in common with the admin area.
		self::common_scripts();

		// the WordLift core JavaScript functions.
		wp_enqueue_script('wordlift',
				plugins_url('/js/wordlift.js', WORDLIFT_20_ROOT_PATH),
				array('jquery'),
				false,
				false);
	}

	/**
	 * Registers scripts and style-sheets common to the WordPress front-end and back-end.
	 */
	function common_scripts() {
	    
	    self::$logger->debug("Enqueuing common scripts.");
		
		// jQuery UI scripts and style-sheets.
		wp_enqueue_style('jquery-ui-lightness-css',
// 						'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/smoothness/jquery-ui.css'
						'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/base/jquery-ui.css'
// 						,plugins_url('/js/jquery-ui/css/ui-lightness/jquery-ui-1.8.18.custom.css',WORDLIFT_20_ROOT_PATH)
			);
		
     wp_enqueue_script('jquery-ui',
             'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js',
//               plugins_url('/js/jquery-ui/js/jquery-ui-1.8.18.custom.min.js', WORDLIFT_20_ROOT_PATH),
             array('jquery'),
             false,
             false);

		wp_enqueue_style('wordlift-screen.css',
				plugins_url('/css/screen.css', WORDLIFT_20_ROOT_PATH));

		wp_enqueue_style('jquery.isotope.css',
				plugins_url('/css/jquery.isotope.css', WORDLIFT_20_ROOT_PATH));

        wp_enqueue_script('underscore',
             plugins_url('/js/underscore-min.js', WORDLIFT_20_ROOT_PATH),
             array(),
             false,
             true);

		wp_enqueue_script('backbone',
				plugins_url('/js/backbone-min.js', WORDLIFT_20_ROOT_PATH),
				array('underscore','jquery'),
				false,
				true);

        wp_enqueue_script('jquery.isotope',
             plugins_url('/js/jquery.isotope.min.js', WORDLIFT_20_ROOT_PATH),
             array('jquery'),
             false,
             true);

		wp_enqueue_script('openlayers',
				'http://www.openlayers.org/api/OpenLayers.js',
				array(),
				false,
				false);

        // wp_enqueue_script('insideout-wordlift',
        //     plugins_url('/js/insideout-wordlift.js', WORDLIFT_20_ROOT_PATH),
        //     array('backbone'),
        //     false,
        //     false);

		echo '<script type="text/javascript"> var WORDLIFT_20_URL = \''.plugins_url('/', WORDLIFT_20_ROOT_PATH).'\'; var WORDLIFT_20_POST_ID = \''.get_the_ID().'\'; var WORDLIFT_20_NO_PREVIEW_URL = WORDLIFT_20_URL+\'images/nopreview.jpg\';</script>';
	}

	function create_post_type() {
		global $entity_meta_box;

		if (NULL == $entity_meta_box) {
			self::$logger->error('The EntityMetaBox does not exist. Please set-up an EntityMetaBox before setting up WordLift.');
			return;
		}

		self::$logger->debug('Registering post-type ['.WORDLIFT_20_ENTITY_CUSTOM_POST_TYPE.'].');

		// array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields')
		$supports 	= array('title');

		$return  	= register_post_type( WORDLIFT_20_ENTITY_CUSTOM_POST_TYPE,
				array(
						'labels' => array(
								'name' 			=> _x('Entities', ''),
								'singular_name' => _x('Entity', ''),
								'search_items'  => _x('Search Entities', ''),
								'popular_items' => _x('Popular Entities', ''),
								'all_items' 	=> _x('All Entities', ''),
								'parent_item'   => _x('Parent', ''),
								'parent_item_colon' => _x('Parent:', ''),
								'edit_item'		=> _x('Edit Entity', ''),
								'update_item'	=> _x('Update Entity', ''),
								'add_new_item'  => _x('Add New Entity', ''),
								'new_item_name' => _x('New Entity Name', ''),
								'separate_items_with_commas' => _x('Separate Entities with Commas', ''),
								'add_or_remove_items' => _x('Add or Remove Entities', ''),
								'choose_from_most_used' => _x('Choose from the most used Entities', ''),
								'menu_name'		=> _x('Entities', '')
						),
						'exclude_from_search'   => false,
						'publicly_queryable'	=> true,
						'description' 			=> 'The entities found in this blog.',
						'public' 				=> true,
						'has_archive' 			=> true,
						'menu_icon'				=> plugins_url('images/semantic-box-14x16.png', WORDLIFT_20_ROOT_PATH),
						'menu_position' 		=> WORDLIFT_20_ADMIN_MENU_POSITION,
						'supports' 				=> $supports,
						'register_meta_box_cb' 	=> array( $entity_meta_box, 'register_meta_box_cb'),
						'rewrite' 				=> array( 'slug' => WORDLIFT_20_ENTITIES_SLUG ),
						'taxonomies'			=> array(
														'category',
														'post_tag'
													)
				)
		);

		flush_rewrite_rules(true);

	}
}

?>
