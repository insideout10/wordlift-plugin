<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wordlift.io
 * @since      1.0.0
 *
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

use Wordlift\Admin\Admin_User_Option;
use Wordlift\Admin\Key_Validation_Notice;
use Wordlift\Admin\Top_Entities;
use Wordlift\Analysis\Response\Analysis_Response_Ops_Factory;
use Wordlift\Autocomplete\All_Autocomplete_Service;
use Wordlift\Autocomplete\Linked_Data_Autocomplete_Service;
use Wordlift\Autocomplete\Local_Autocomplete_Service;
use Wordlift\Cache\Ttl_Cache;
use Wordlift\Configuration\Config;
use Wordlift\Duplicate_Markup_Remover\Faq_Duplicate_Markup_Remover;
use Wordlift\Entity\Entity_Helper;
use Wordlift\Entity\Entity_No_Index_Flag;
use Wordlift\Entity\Entity_Rest_Service;
use Wordlift\External_Plugin_Hooks\Recipe_Maker\Recipe_Maker_After_Get_Jsonld_Hook;
use Wordlift\External_Plugin_Hooks\Recipe_Maker\Recipe_Maker_Jsonld_Hook;
use Wordlift\External_Plugin_Hooks\Recipe_Maker\Recipe_Maker_Post_Type_Hook;
use Wordlift\External_Plugin_Hooks\Recipe_Maker\Recipe_Maker_Validation_Service;
use Wordlift\External_Plugin_Hooks\Recipe_Maker\Recipe_Maker_Warning;
use Wordlift\External_Plugin_Hooks\Yoast\Yoast_Jsonld;
use Wordlift\Faq\Faq_Content_Filter;
use Wordlift\Faq\Faq_Tinymce_Adapter;
use Wordlift\Features\Features_Registry;
use Wordlift\Jsonld\Jsonld_Adapter;
use Wordlift\Jsonld\Jsonld_Article_Wrapper;
use Wordlift\Jsonld\Jsonld_By_Id_Endpoint;
use Wordlift\Jsonld\Jsonld_Endpoint;
use Wordlift\Jsonld\Jsonld_Service;
use Wordlift\Jsonld\Jsonld_User_Service;
use Wordlift\Mappings\Formatters\Acf_Group_Formatter;
use Wordlift\Mappings\Jsonld_Converter;
use Wordlift\Mappings\Mappings_DBO;
use Wordlift\Mappings\Mappings_Transform_Functions_Registry;
use Wordlift\Mappings\Mappings_Validator;
use Wordlift\Mappings\Transforms\Post_Id_To_Entity_Transform_Function;
use Wordlift\Mappings\Transforms\Taxonomy_To_Terms_Transform_Function;
use Wordlift\Mappings\Transforms\Url_To_Entity_Transform_Function;
use Wordlift\Mappings\Validators\Post_Type_Rule_Validator;
use Wordlift\Mappings\Validators\Rule_Groups_Validator;
use Wordlift\Mappings\Validators\Rule_Validators_Registry;
use Wordlift\Mappings\Validators\Taxonomy_Rule_Validator;
use Wordlift\Mappings\Validators\Taxonomy_Term_Rule_Validator;
use Wordlift\Mappings\Validators\Post_Taxonomy_Term_Rule_Validator;
use Wordlift\Post_Excerpt\Post_Excerpt_Meta_Box_Adapter;
use Wordlift\Post_Excerpt\Post_Excerpt_Rest_Controller;
use Wordlift\Templates\Templates_Ajax_Endpoint;
use Wordlift\Videoobject\Loader;
use Wordlift\Vocabulary\Vocabulary_Loader;
use Wordlift\Widgets\Async_Template_Decorator;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 * @author     WordLift <hello@wordlift.io>
 */
class Wordlift {

	//<editor-fold desc="## FIELDS">

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wordlift_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * The {@link Wordlift_Tinymce_Adapter} instance.
	 *
	 * @since  3.12.0
	 * @access protected
	 * @var \Wordlift_Tinymce_Adapter $tinymce_adapter The {@link Wordlift_Tinymce_Adapter} instance.
	 */
	protected $tinymce_adapter;

	/**
	 * The {@link Faq_Tinymce_Adapter} instance
	 * @since 3.26.0
	 * @access protected
	 * @var Faq_Tinymce_Adapter $faq_tinymce_adapter .
	 */
	//protected $faq_tinymce_adapter;

	/**
	 * The Thumbnail service.
	 *
	 * @since  3.1.5
	 * @access private
	 * @var \Wordlift_Thumbnail_Service $thumbnail_service The Thumbnail service.
	 */
	private $thumbnail_service;

	/**
	 * The UI service.
	 *
	 * @since  3.2.0
	 * @access private
	 * @var \Wordlift_UI_Service $ui_service The UI service.
	 */
	private $ui_service;

	/**
	 * The Schema service.
	 *
	 * @since  3.3.0
	 * @access protected
	 * @var \Wordlift_Schema_Service $schema_service The Schema service.
	 */
	protected $schema_service;

	/**
	 * The Entity service.
	 *
	 * @since  3.1.0
	 * @access protected
	 * @var \Wordlift_Entity_Service $entity_service The Entity service.
	 */
	protected $entity_service;

	/**
	 * The Topic Taxonomy service.
	 *
	 * @since  3.5.0
	 * @access private
	 * @var \Wordlift_Topic_Taxonomy_Service The Topic Taxonomy service.
	 */
	private $topic_taxonomy_service;

	/**
	 * The Entity Types Taxonomy service.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var \Wordlift_Entity_Type_Taxonomy_Service The Entity Types Taxonomy service.
	 */
	private $entity_types_taxonomy_service;

	/**
	 * The User service.
	 *
	 * @since  3.1.7
	 * @access protected
	 * @var \Wordlift_User_Service $user_service The User service.
	 */
	protected $user_service;

	/**
	 * The Timeline service.
	 *
	 * @since  3.1.0
	 * @access private
	 * @var \Wordlift_Timeline_Service $timeline_service The Timeline service.
	 */
	private $timeline_service;

	/**
	 * The Redirect service.
	 *
	 * @since  3.2.0
	 * @access private
	 * @var \Wordlift_Redirect_Service $redirect_service The Redirect service.
	 */
	private $redirect_service;

	/**
	 * The Notice service.
	 *
	 * @since  3.3.0
	 * @access private
	 * @var \Wordlift_Notice_Service $notice_service The Notice service.
	 */
	private $notice_service;

	/**
	 * The Entity list customization.
	 *
	 * @since  3.3.0
	 * @access protected
	 * @var \Wordlift_Entity_List_Service $entity_list_service The Entity list service.
	 */
	protected $entity_list_service;

	/**
	 * The Entity Types Taxonomy Walker.
	 *
	 * @since  3.1.0
	 * @access private
	 * @var \Wordlift_Entity_Types_Taxonomy_Walker $entity_types_taxonomy_walker The Entity Types Taxonomy Walker
	 */
	private $entity_types_taxonomy_walker;

	/**
	 * The ShareThis service.
	 *
	 * @since  3.2.0
	 * @access private
	 * @var \Wordlift_ShareThis_Service $sharethis_service The ShareThis service.
	 */
	private $sharethis_service;

	/**
	 * The PrimaShop adapter.
	 *
	 * @since  3.2.3
	 * @access private
	 * @var \Wordlift_PrimaShop_Adapter $primashop_adapter The PrimaShop adapter.
	 */
	private $primashop_adapter;

	/**
	 * The WordLift Dashboard adapter.
	 *
	 * @since  3.4.0
	 * @access private
	 * @var \Wordlift_Dashboard_Service $dashboard_service The WordLift Dashboard service;
	 */
	private $dashboard_service;

	/**
	 * The entity type service.
	 *
	 * @since  3.6.0
	 * @access private
	 * @var \Wordlift_Entity_Post_Type_Service
	 */
	private $entity_post_type_service;

	/**
	 * The entity link service used to mangle links to entities with a custom slug or even w/o a slug.
	 *
	 * @since  3.6.0
	 * @access private
	 * @var \Wordlift_Entity_Link_Service $entity_link_service The {@link Wordlift_Entity_Link_Service} instance.
	 */
	private $entity_link_service;

	/**
	 * A {@link Wordlift_Sparql_Service} instance.
	 *
	 * @since    3.6.0
	 * @access   protected
	 * @var \Wordlift_Sparql_Service $sparql_service A {@link Wordlift_Sparql_Service} instance.
	 */
	protected $sparql_service;

	/**
	 * A {@link Wordlift_Import_Service} instance.
	 *
	 * @since  3.6.0
	 * @access private
	 * @var \Wordlift_Import_Service $import_service A {@link Wordlift_Import_Service} instance.
	 */
	private $import_service;

	/**
	 * A {@link Wordlift_Rebuild_Service} instance.
	 *
	 * @since  3.6.0
	 * @access private
	 * @var \Wordlift_Rebuild_Service $rebuild_service A {@link Wordlift_Rebuild_Service} instance.
	 */
	private $rebuild_service;

	/**
	 * A {@link Wordlift_Jsonld_Service} instance.
	 *
	 * @since  3.7.0
	 * @access protected
	 * @var \Wordlift_Jsonld_Service $jsonld_service A {@link Wordlift_Jsonld_Service} instance.
	 */
	protected $jsonld_service;

	/**
	 * A {@link Wordlift_Website_Jsonld_Converter} instance.
	 *
	 * @since  3.14.0
	 * @access protected
	 * @var \Wordlift_Website_Jsonld_Converter $jsonld_website_converter A {@link Wordlift_Website_Jsonld_Converter} instance.
	 */
	protected $jsonld_website_converter;

	/**
	 * A {@link Wordlift_Property_Factory} instance.
	 *
	 * @since  3.7.0
	 * @access private
	 * @var \Wordlift_Property_Factory $property_factory
	 */
	private $property_factory;

	/**
	 * The 'Download Your Data' page.
	 *
	 * @since  3.6.0
	 * @access private
	 * @var \Wordlift_Admin_Download_Your_Data_Page $download_your_data_page The 'Download Your Data' page.
	 */
	private $download_your_data_page;

	/**
	 * The 'WordLift Settings' page.
	 *
	 * @since  3.11.0
	 * @access protected
	 * @var \Wordlift_Admin_Settings_Page $settings_page The 'WordLift Settings' page.
	 */
	protected $settings_page;

	/**
	 * The install wizard page.
	 *
	 * @since  3.9.0
	 * @access private
	 * @var \Wordlift_Admin_Setup $admin_setup The Install wizard.
	 */
	public $admin_setup;

	/**
	 * The Content Filter Service hooks up to the 'the_content' filter and provides
	 * linking of entities to their pages.
	 *
	 * @since  3.8.0
	 * @access private
	 * @var \Wordlift_Content_Filter_Service $content_filter_service A {@link Wordlift_Content_Filter_Service} instance.
	 */
	private $content_filter_service;

	/**
	 * The Faq Content filter service
	 * @since  3.26.0
	 * @access private
	 * @var Faq_Content_Filter $faq_content_filter_service A {@link Faq_Content_Filter} instance.
	 */
	private $faq_content_filter_service;

	/**
	 * A {@link Wordlift_Key_Validation_Service} instance.
	 *
	 * @since  3.9.0
	 * @access private
	 * @var Wordlift_Key_Validation_Service $key_validation_service A {@link Wordlift_Key_Validation_Service} instance.
	 */
	private $key_validation_service;

	/**
	 * A {@link Wordlift_Rating_Service} instance.
	 *
	 * @since  3.10.0
	 * @access private
	 * @var \Wordlift_Rating_Service $rating_service A {@link Wordlift_Rating_Service} instance.
	 */
	private $rating_service;

	/**
	 * A {@link Wordlift_Post_To_Jsonld_Converter} instance.
	 *
	 * @since  3.10.0
	 * @access protected
	 * @var \Wordlift_Post_To_Jsonld_Converter $post_to_jsonld_converter A {@link Wordlift_Post_To_Jsonld_Converter} instance.
	 */
	protected $post_to_jsonld_converter;

	/**
	 * A {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.10.0
	 * @access protected
	 * @var \Wordlift_Configuration_Service $configuration_service A {@link Wordlift_Configuration_Service} instance.
	 */
	protected $configuration_service;

	/**
	 * A {@link Wordlift_Install_Service} instance.
	 *
	 * @since  3.18.0
	 * @access protected
	 * @var \Wordlift_Install_Service $install_service A {@link Wordlift_Install_Service} instance.
	 */
	protected $install_service;

	/**
	 * A {@link Wordlift_Entity_Type_Service} instance.
	 *
	 * @since  3.10.0
	 * @access protected
	 * @var \Wordlift_Entity_Type_Service $entity_type_service A {@link Wordlift_Entity_Type_Service} instance.
	 */
	protected $entity_type_service;

	/**
	 * A {@link Wordlift_Entity_Post_To_Jsonld_Converter} instance.
	 *
	 * @since  3.10.0
	 * @access protected
	 * @var \Wordlift_Entity_Post_To_Jsonld_Converter $entity_post_to_jsonld_converter A {@link Wordlift_Entity_Post_To_Jsonld_Converter} instance.
	 */
	protected $entity_post_to_jsonld_converter;

	/**
	 * A {@link Wordlift_Postid_To_Jsonld_Converter} instance.
	 *
	 * @since  3.10.0
	 * @access protected
	 * @var \Wordlift_Postid_To_Jsonld_Converter $postid_to_jsonld_converter A {@link Wordlift_Postid_To_Jsonld_Converter} instance.
	 */
	protected $postid_to_jsonld_converter;

	/**
	 * The {@link Wordlift_Admin_Status_Page} class.
	 *
	 * @since  3.9.8
	 * @access private
	 * @var \Wordlift_Admin_Status_Page $status_page The {@link Wordlift_Admin_Status_Page} class.
	 */
	private $status_page;

	/**
	 * The {@link Wordlift_Category_Taxonomy_Service} instance.
	 *
	 * @since  3.11.0
	 * @access protected
	 * @var \Wordlift_Category_Taxonomy_Service $category_taxonomy_service The {@link Wordlift_Category_Taxonomy_Service} instance.
	 */
	protected $category_taxonomy_service;

	/**
	 * The {@link Wordlift_Entity_Page_Service} instance.
	 *
	 * @since  3.11.0
	 * @access protected
	 * @var \Wordlift_Entity_Page_Service $entity_page_service The {@link Wordlift_Entity_Page_Service} instance.
	 */
	protected $entity_page_service;

	/**
	 * The {@link Wordlift_Admin_Settings_Page_Action_Link} class.
	 *
	 * @since  3.11.0
	 * @access protected
	 * @var \Wordlift_Admin_Settings_Page_Action_Link $settings_page_action_link The {@link Wordlift_Admin_Settings_Page_Action_Link} class.
	 */
	protected $settings_page_action_link;

	/**
	 * The {@link Wordlift_Admin_Settings_Page_Action_Link} class.
	 *
	 * @since  3.11.0
	 * @access protected
	 * @var \Wordlift_Admin_Settings_Page_Action_Link $settings_page_action_link The {@link Wordlift_Admin_Settings_Page_Action_Link} class.
	 */
	protected $analytics_settings_page_action_link;

	/**
	 * The {@link Wordlift_Analytics_Connect} class.
	 *
	 * @since  3.11.0
	 * @access protected
	 * @var \Wordlift_Analytics_Connect $analytics_connect The {@link Wordlift_Analytics_Connect} class.
	 */
	protected $analytics_connect;

	/**
	 * The {@link Wordlift_Publisher_Ajax_Adapter} instance.
	 *
	 * @since  3.11.0
	 * @access protected
	 * @var \Wordlift_Publisher_Ajax_Adapter $publisher_ajax_adapter The {@link Wordlift_Publisher_Ajax_Adapter} instance.
	 */
	protected $publisher_ajax_adapter;

	/**
	 * The {@link Wordlift_Admin_Input_Element} element renderer.
	 *
	 * @since  3.11.0
	 * @access protected
	 * @var \Wordlift_Admin_Input_Element $input_element The {@link Wordlift_Admin_Input_Element} element renderer.
	 */
	protected $input_element;

	/**
	 * The {@link Wordlift_Admin_Radio_Input_Element} element renderer.
	 *
	 * @since  3.13.0
	 * @access protected
	 * @var \Wordlift_Admin_Radio_Input_Element $radio_input_element The {@link Wordlift_Admin_Radio_Input_Element} element renderer.
	 */
	protected $radio_input_element;

	/**
	 * The {@link Wordlift_Admin_Language_Select_Element} element renderer.
	 *
	 * @since  3.11.0
	 * @access protected
	 * @var \Wordlift_Admin_Language_Select_Element $language_select_element The {@link Wordlift_Admin_Language_Select_Element} element renderer.
	 */
	protected $language_select_element;

	/**
	 * The {@link Wordlift_Admin_Country_Select_Element} element renderer.
	 *
	 * @since  3.18.0
	 * @access protected
	 * @var \Wordlift_Admin_Country_Select_Element $country_select_element The {@link Wordlift_Admin_Country_Select_Element} element renderer.
	 */
	protected $country_select_element;

	/**
	 * The {@link Wordlift_Admin_Publisher_Element} element renderer.
	 *
	 * @since  3.11.0
	 * @access protected
	 * @var \Wordlift_Admin_Publisher_Element $publisher_element The {@link Wordlift_Admin_Publisher_Element} element renderer.
	 */
	protected $publisher_element;

	/**
	 * The {@link Wordlift_Admin_Select2_Element} element renderer.
	 *
	 * @since  3.11.0
	 * @access protected
	 * @var \Wordlift_Admin_Select2_Element $select2_element The {@link Wordlift_Admin_Select2_Element} element renderer.
	 */
	protected $select2_element;

	/**
	 * The controller for the entity type list admin page
	 *
	 * @since  3.11.0
	 * @access private
	 * @var \Wordlift_Admin_Entity_Taxonomy_List_Page $entity_type_admin_page The {@link Wordlift_Admin_Entity_Taxonomy_List_Page} class.
	 */
	private $entity_type_admin_page;

	/**
	 * The controller for the entity type settings admin page
	 *
	 * @since  3.11.0
	 * @access private
	 * @var \Wordlift_Admin_Entity_Type_Settings $entity_type_settings_admin_page The {@link Wordlift_Admin_Entity_Type_Settings} class.
	 */
	private $entity_type_settings_admin_page;

	/**
	 * The {@link Wordlift_Related_Entities_Cloud_Widget} instance.
	 *
	 * @since  3.11.0
	 * @access protected
	 * @var \Wordlift_Related_Entities_Cloud_Widget $related_entities_cloud_widget The {@link Wordlift_Related_Entities_Cloud_Widget} instance.
	 */
	protected $related_entities_cloud_widget;

	/**
	 * The {@link Wordlift_Admin_Author_Element} instance.
	 *
	 * @since  3.14.0
	 * @access protected
	 * @var \Wordlift_Admin_Author_Element $author_element The {@link Wordlift_Admin_Author_Element} instance.
	 */
	protected $author_element;

	/**
	 * The {@link Wordlift_Sample_Data_Service} instance.
	 *
	 * @since  3.12.0
	 * @access protected
	 * @var \Wordlift_Sample_Data_Service $sample_data_service The {@link Wordlift_Sample_Data_Service} instance.
	 */
	protected $sample_data_service;

	/**
	 * The {@link Wordlift_Sample_Data_Ajax_Adapter} instance.
	 *
	 * @since  3.12.0
	 * @access protected
	 * @var \Wordlift_Sample_Data_Ajax_Adapter $sample_data_ajax_adapter The {@link Wordlift_Sample_Data_Ajax_Adapter} instance.
	 */
	protected $sample_data_ajax_adapter;

	/**
	 * The {@link Wordlift_Relation_Rebuild_Service} instance.
	 *
	 * @since  3.14.3
	 * @access private
	 * @var \Wordlift_Relation_Rebuild_Service $relation_rebuild_service The {@link Wordlift_Relation_Rebuild_Service} instance.
	 */
	private $relation_rebuild_service;

	/**
	 * The {@link Wordlift_Relation_Rebuild_Adapter} instance.
	 *
	 * @since  3.14.3
	 * @access private
	 * @var \Wordlift_Relation_Rebuild_Adapter $relation_rebuild_adapter The {@link Wordlift_Relation_Rebuild_Adapter} instance.
	 */
	private $relation_rebuild_adapter;

	/**
	 * The {@link Wordlift_Reference_Rebuild_Service} instance.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var \Wordlift_Reference_Rebuild_Service $reference_rebuild_service The {@link Wordlift_Reference_Rebuild_Service} instance.
	 */
	private $reference_rebuild_service;

	/**
	 * The {@link Wordlift_Google_Analytics_Export_Service} instance.
	 *
	 * @since  3.16.0
	 * @access protected
	 * @var \Wordlift_Google_Analytics_Export_Service $google_analytics_export_service The {@link Wordlift_Google_Analytics_Export_Service} instance.
	 */
	protected $google_analytics_export_service;

	/**
	 * {@link Wordlift}'s singleton instance.
	 *
	 * @since  3.15.0
	 * @access protected
	 * @var \Wordlift_Entity_Type_Adapter $entity_type_adapter The {@link Wordlift_Entity_Type_Adapter} instance.
	 */
	protected $entity_type_adapter;

	/**
	 * The {@link Wordlift_Storage_Factory} instance.
	 *
	 * @since  3.15.0
	 * @access protected
	 * @var \Wordlift_Storage_Factory $storage_factory The {@link Wordlift_Storage_Factory} instance.
	 */
	protected $storage_factory;

	/**
	 * The {@link Wordlift_Sparql_Tuple_Rendition_Factory} instance.
	 *
	 * @since  3.15.0
	 * @access protected
	 * @var \Wordlift_Sparql_Tuple_Rendition_Factory $rendition_factory The {@link Wordlift_Sparql_Tuple_Rendition_Factory} instance.
	 */
	protected $rendition_factory;

	/**
	 * The {@link Wordlift_Autocomplete_Adapter} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Autocomplete_Adapter $autocomplete_adapter The {@link Wordlift_Autocomplete_Adapter} instance.
	 */
	private $autocomplete_adapter;

	/**
	 * The {@link Wordlift_Relation_Service} instance.
	 *
	 * @since  3.15.0
	 * @access protected
	 * @var \Wordlift_Relation_Service $relation_service The {@link Wordlift_Relation_Service} instance.
	 */
	protected $relation_service;

	/**
	 * The {@link Wordlift_Cached_Post_Converter} instance.
	 *
	 * @since  3.16.0
	 * @access protected
	 * @var  \Wordlift_Cached_Post_Converter $cached_postid_to_jsonld_converter The {@link Wordlift_Cached_Post_Converter} instance.
	 *
	 */
	protected $cached_postid_to_jsonld_converter;

	/**
	 * The {@link Wordlift_Entity_Uri_Service} instance.
	 *
	 * @since  3.16.3
	 * @access protected
	 * @var \Wordlift_Entity_Uri_Service $entity_uri_service The {@link Wordlift_Entity_Uri_Service} instance.
	 */
	protected $entity_uri_service;

	/**
	 * The {@link Wordlift_Publisher_Service} instance.
	 *
	 * @since  3.19.0
	 * @access protected
	 * @var \Wordlift_Publisher_Service $publisher_service The {@link Wordlift_Publisher_Service} instance.
	 */
	protected $publisher_service;

	/**
	 * The {@link Wordlift_Context_Cards_Service} instance.
	 *
	 * @var \Wordlift_Context_Cards_Service The {@link Wordlift_Context_Cards_Service} instance.
	 */
	protected $context_cards_service;

	/**
	 * {@link Wordlift}'s singleton instance.
	 *
	 * @since  3.11.2
	 * @access private
	 * @var Wordlift $instance {@link Wordlift}'s singleton instance.
	 */
	private static $instance;

	/**
	 * A singleton instance of features registry.
	 * @since 3.30.0
	 * @var Features_Registry
	 */
	private $features_registry;

	//</editor-fold>

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		self::$instance = $this;

		$this->plugin_name = 'wordlift';
		$this->version     = '3.31.4';
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		// If we're in `WP_CLI` load the related files.
		if ( class_exists( 'WP_CLI' ) ) {
			$this->load_cli_dependencies();
		}

	}

	/**
	 * Get the singleton instance.
	 *
	 * @return Wordlift The {@link Wordlift} singleton instance.
	 * @since 3.11.2
	 *
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wordlift_Loader. Orchestrates the hooks of the plugin.
	 * - Wordlift_i18n. Defines internationalization functionality.
	 * - Wordlift_Admin. Defines all hooks for the admin area.
	 * - Wordlift_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @throws Exception
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-loader.php';

		// The class responsible for plugin uninstall.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-deactivator-feedback.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-i18n.php';

		/**
		 * WordLift's supported languages.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-languages.php';

		/**
		 * WordLift's supported countries.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-countries.php';

		/**
		 * Provide support functions to sanitize data.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-sanitizer.php';

		/** Services. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-log-service.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-http-api.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-redirect-service.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-configuration-service.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-entity-post-type-service.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-entity-type-service.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-entity-link-service.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-linked-data-service.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-relation-service.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-image-service.php';

		/**
		 * The Query builder.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-query-builder.php';

		/**
		 * The Schema service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-schema-service.php';

		/**
		 * The schema:url property service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-property-service.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-schema-url-property-service.php';

		/**
		 * The UI service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-ui-service.php';

		/**
		 * The Thumbnail service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-thumbnail-service.php';

		/**
		 * The Entity Types Taxonomy service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-entity-type-taxonomy-service.php';

		/**
		 * The Entity service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-entity-uri-service.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-entity-service.php';

		// Add the entity rating service.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-rating-service.php';

		/**
		 * The User service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-user-service.php';

		/**
		 * The Timeline service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-timeline-service.php';

		/**
		 * The Topic Taxonomy service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-topic-taxonomy-service.php';

		/**
		 * The SPARQL service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-sparql-service.php';

		/**
		 * The WordLift import service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-import-service.php';

		/**
		 * The WordLift URI service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-uri-service.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-property-factory.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-sample-data-service.php';

		/**
		 * The WordLift rebuild service, used to rebuild the remote dataset using the local data.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/rebuild/class-wordlift-listable.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/rebuild/class-wordlift-rebuild-service.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/rebuild/class-wordlift-reference-rebuild-service.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/rebuild/class-wordlift-relation-rebuild-service.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/rebuild/class-wordlift-relation-rebuild-adapter.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/properties/class-wordlift-property-getter-factory.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-attachment-service.php';

		/**
		 * Load the converters.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/intf-wordlift-post-converter.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-abstract-post-to-jsonld-converter.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-postid-to-jsonld-converter.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-entity-post-to-jsonld-converter.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-post-to-jsonld-converter.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-jsonld-website-converter.php';

		/**
		 * Load cache-related files.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/cache/require.php';

		/**
		 * Load the content filter.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-content-filter-service.php';

		/*
		 * Load the excerpt helper.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-post-excerpt-helper.php';

		/**
		 * Load the JSON-LD service to publish entities using JSON-LD.s
		 *
		 * @since 3.8.0
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-jsonld-service.php';

		// The Publisher Service and the AJAX adapter.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-publisher-service.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-publisher-ajax-adapter.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-post-adapter.php';

		/**
		 * Load the WordLift key validation service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-key-validation-service.php';

		// Load the `Wordlift_Category_Taxonomy_Service` class definition.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-category-taxonomy-service.php';

		// Load the `Wordlift_Entity_Page_Service` class definition.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-entity-page-service.php';

		/** Linked Data. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/linked-data/storage/class-wordlift-storage.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/linked-data/storage/class-wordlift-post-meta-storage.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/linked-data/storage/class-wordlift-post-property-storage.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/linked-data/storage/class-wordlift-post-taxonomy-storage.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/linked-data/storage/class-wordlift-post-schema-class-storage.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/linked-data/storage/class-wordlift-post-author-storage.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/linked-data/storage/class-wordlift-post-meta-uri-storage.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/linked-data/storage/class-wordlift-post-image-storage.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/linked-data/storage/class-wordlift-post-related-storage.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/linked-data/storage/class-wordlift-url-property-storage.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/linked-data/storage/class-wordlift-storage-factory.php';

		/** Linked Data Rendition. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/linked-data/rendition/intf-wordlift-sparql-tuple-rendition.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/linked-data/rendition/class-wordlift-default-sparql-tuple-rendition.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/linked-data/rendition/class-wordlift-address-sparql-tuple-rendition.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/linked-data/rendition/class-wordlift-sparql-tuple-rendition-factory.php';

		/** Services. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-google-analytics-export-service.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-api-service.php';

		/** Adapters. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-tinymce-adapter.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-newrelic-adapter.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-sample-data-ajax-adapter.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-entity-type-adapter.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-wprocket-adapter.php';

		/** Async Tasks. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/wp-async-task/class-wordlift-async-task.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/wp-async-task/class-wordlift-sparql-query-async-task.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/wp-async-task/class-wordlift-push-references-async-task.php';

		/** Autocomplete. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-autocomplete-adapter.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-remote-image-service.php';

		/** Analytics */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/analytics/class-wordlift-analytics-connect.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin.php';

		/**
		 * The class to customize the entity list admin page.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin-entity-list.php';

		/**
		 * The Entity Types Taxonomy Walker (transforms checkboxes into radios).
		 */
		global $wp_version;
		if ( version_compare( $wp_version, '5.3', '<' ) ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-entity-types-taxonomy-walker.php';
		} else {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-entity-types-taxonomy-walker-5-3.php';
		}

		/**
		 * The Notice service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-notice-service.php';

		/**
		 * The PrimaShop adapter.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-primashop-adapter.php';

		/**
		 * The WordLift Dashboard service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin-dashboard.php';

		/**
		 * The admin 'Install wizard' page.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin-setup.php';

		/**
		 * The WordLift entity type list admin page controller.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin-entity-taxonomy-list-page.php';

		/**
		 * The WordLift entity type settings admin page controller.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-entity-type-settings.php';

		/**
		 * The admin 'Download Your Data' page.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-download-your-data-page.php';

		/**
		 * The admin 'WordLift Settings' page.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/elements/intf-wordlift-admin-element.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/elements/class-wordlift-admin-input-element.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/elements/class-wordlift-admin-input-radio-element.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/elements/class-wordlift-admin-select-element.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/elements/class-wordlift-admin-select2-element.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/elements/class-wordlift-admin-language-select-element.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/elements/class-wordlift-admin-country-select-element.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/elements/class-wordlift-admin-tabs-element.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/elements/class-wordlift-admin-author-element.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/elements/class-wordlift-admin-publisher-element.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin-page.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin-settings-page.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin-settings-analytics-page.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin-settings-page-action-link.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin-settings-analytics-page-action-link.php';

		/** Admin Pages */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin-user-profile-page.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin-status-page.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin-search-rankings-page.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-entity-type-admin-service.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-public.php';

		/**
		 * The shortcode abstract class.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-shortcode.php';

		/**
		 * The Timeline shortcode.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-timeline-shortcode.php';

		/**
		 * The Navigator shortcode.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-navigator-shortcode.php';

		/**
		 * The Products Navigator shortcode.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-products-navigator-shortcode.php';

		/**
		 * The chord shortcode.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-chord-shortcode.php';

		/**
		 * The geomap shortcode.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-geomap-shortcode.php';

		/**
		 * The entity cloud shortcode.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-related-entities-cloud-shortcode.php';

		/**
		 * The entity glossary shortcode.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-alphabet-service.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-vocabulary-shortcode.php';

		/**
		 * Faceted Search shortcode.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-faceted-search-shortcode.php';

		/**
		 * The ShareThis service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-sharethis-service.php';

		/**
		 * The SEO service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-seo-service.php';

		/**
		 * The AMP service.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-amp-service.php';

		/** Widgets */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-widget.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-related-entities-cloud-widget.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-context-cards.php';

		/*
		 * Schema.org Services.
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/835
		 */
		if ( WL_ALL_ENTITY_TYPES ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/schemaorg/class-wordlift-schemaorg-sync-service.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/schemaorg/class-wordlift-schemaorg-property-service.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/schemaorg/class-wordlift-schemaorg-class-service.php';
			new Wordlift_Schemaorg_Sync_Service();
			$schemaorg_property_service = new Wordlift_Schemaorg_Property_Service();
			new Wordlift_Schemaorg_Class_Service();
		} else {
			$schemaorg_property_service = null;
		}

		$this->loader = new Wordlift_Loader();
		/**
		 * @since 3.30.0
		 */
		$this->features_registry = Features_Registry::get_instance();

		// Instantiate a global logger.
		global $wl_logger;
		$wl_logger = Wordlift_Log_Service::get_logger( 'WordLift' );

		// Load the `wl-api` end-point.
		new Wordlift_Http_Api();

		// Load the Install Service.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'install/class-wordlift-install-service.php';
		$this->install_service = new Wordlift_Install_Service();

		/** Services. */
		// Create the configuration service.
		$this->configuration_service = new Wordlift_Configuration_Service();
		$api_service                 = new Wordlift_Api_Service( $this->configuration_service );

		// Create an entity type service instance. It'll be later bound to the init action.
		$this->entity_post_type_service = new Wordlift_Entity_Post_Type_Service( Wordlift_Entity_Service::TYPE_NAME, $this->configuration_service->get_entity_base_path() );

		// Create an entity link service instance. It'll be later bound to the post_type_link and pre_get_posts actions.
		$this->entity_link_service = new Wordlift_Entity_Link_Service( $this->entity_post_type_service, $this->configuration_service->get_entity_base_path() );

		// Create an instance of the UI service.
		$this->ui_service = new Wordlift_UI_Service();

		// Create an instance of the Thumbnail service. Later it'll be hooked to post meta events.
		$this->thumbnail_service = new Wordlift_Thumbnail_Service();

		$this->sparql_service        = new Wordlift_Sparql_Service();
		$schema_url_property_service = new Wordlift_Schema_Url_Property_Service( $this->sparql_service );
		$this->notice_service        = new Wordlift_Notice_Service();
		$this->relation_service      = new Wordlift_Relation_Service();

		$entity_uri_cache_service = new Wordlift_File_Cache_Service( WL_TEMP_DIR . 'entity_uri/' );
		$this->entity_uri_service = new Wordlift_Cached_Entity_Uri_Service( $this->configuration_service, $entity_uri_cache_service );
		$this->entity_service     = new Wordlift_Entity_Service( $this->ui_service, $this->relation_service, $this->entity_uri_service );
		$this->user_service       = new Wordlift_User_Service( $this->sparql_service, $this->entity_service );

		// Instantiate the JSON-LD service.
		$property_getter = Wordlift_Property_Getter_Factory::create( $this->entity_service );

		/** Linked Data. */
		$this->storage_factory   = new Wordlift_Storage_Factory( $this->entity_service, $this->user_service, $property_getter );
		$this->rendition_factory = new Wordlift_Sparql_Tuple_Rendition_Factory( $this->entity_service );

		$this->schema_service = new Wordlift_Schema_Service( $this->storage_factory, $this->rendition_factory, $this->configuration_service );

		// Create a new instance of the Redirect service.
		$this->redirect_service    = new Wordlift_Redirect_Service( $this->entity_uri_service );
		$this->entity_type_service = new Wordlift_Entity_Type_Service( $this->schema_service );

		// Create a new instance of the Timeline service and Timeline shortcode.
		$this->timeline_service = new Wordlift_Timeline_Service( $this->entity_service, $this->entity_type_service );

		$this->entity_types_taxonomy_walker = new Wordlift_Entity_Types_Taxonomy_Walker();

		$this->topic_taxonomy_service        = new Wordlift_Topic_Taxonomy_Service();
		$this->entity_types_taxonomy_service = new Wordlift_Entity_Type_Taxonomy_Service();

		// Create an instance of the ShareThis service, later we hook it to the_content and the_excerpt filters.
		$this->sharethis_service = new Wordlift_ShareThis_Service();

		// Create an instance of the PrimaShop adapter.
		$this->primashop_adapter = new Wordlift_PrimaShop_Adapter();

		// Create an import service instance to hook later to WP's import function.
		$this->import_service = new Wordlift_Import_Service( $this->entity_post_type_service, $this->entity_service, $this->schema_service, $this->sparql_service, $this->configuration_service->get_dataset_uri() );

		$uri_service = new Wordlift_Uri_Service( $GLOBALS['wpdb'] );

		// Create the entity rating service.
		$this->rating_service = new Wordlift_Rating_Service( $this->entity_service, $this->entity_type_service, $this->notice_service );

		// Create entity list customization (wp-admin/edit.php).
		$this->entity_list_service = new Wordlift_Entity_List_Service( $this->rating_service );

		// Create a new instance of the Redirect service.
		$this->dashboard_service = new Wordlift_Dashboard_Service( $this->rating_service, $this->entity_service );

		// Create an instance of the Publisher Service and the AJAX Adapter.
		$this->publisher_service = new Wordlift_Publisher_Service( $this->configuration_service );
		$this->property_factory  = new Wordlift_Property_Factory( $schema_url_property_service );
		$this->property_factory->register( Wordlift_Schema_Url_Property_Service::META_KEY, $schema_url_property_service );

		$attachment_service = new Wordlift_Attachment_Service();

		// Instantiate the JSON-LD service.
		$property_getter                       = Wordlift_Property_Getter_Factory::create( $this->entity_service );
		$this->post_to_jsonld_converter        = new Wordlift_Post_To_Jsonld_Converter( $this->entity_type_service, $this->entity_service, $this->user_service, $attachment_service, $this->configuration_service );
		$this->entity_post_to_jsonld_converter = new Wordlift_Entity_Post_To_Jsonld_Converter( $this->entity_type_service, $this->entity_service, $this->user_service, $attachment_service, $property_getter, $schemaorg_property_service, $this->post_to_jsonld_converter );
		$this->postid_to_jsonld_converter      = new Wordlift_Postid_To_Jsonld_Converter( $this->entity_service, $this->entity_post_to_jsonld_converter, $this->post_to_jsonld_converter );
		$this->jsonld_website_converter        = new Wordlift_Website_Jsonld_Converter( $this->entity_type_service, $this->entity_service, $this->user_service, $attachment_service, $this->configuration_service );

		$jsonld_cache                            = new Ttl_Cache( 'jsonld', 86400 );
		$this->cached_postid_to_jsonld_converter = new Wordlift_Cached_Post_Converter( $this->postid_to_jsonld_converter, $this->configuration_service, $jsonld_cache );
		$this->jsonld_service                    = new Wordlift_Jsonld_Service( $this->entity_service, $this->cached_postid_to_jsonld_converter, $this->jsonld_website_converter );

		/*
		 * Load the `Wordlift_Term_JsonLd_Adapter`.
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/892
		 *
		 * @since 3.20.0
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wordlift-term-jsonld-adapter.php';
		$term_jsonld_adapter = new Wordlift_Term_JsonLd_Adapter( $this->entity_uri_service, $this->jsonld_service );
		$jsonld_service      = new Jsonld_Service(
			$this->jsonld_service,
			$term_jsonld_adapter,
			new Jsonld_User_Service( $this->user_service ) );
		new Jsonld_Endpoint( $jsonld_service, $this->entity_uri_service );

		// Prints the JSON-LD in the head.
		new Jsonld_Adapter( $this->jsonld_service );

		new Jsonld_By_Id_Endpoint( $this->jsonld_service, $this->entity_uri_service );

		$this->key_validation_service = new Wordlift_Key_Validation_Service( $this->configuration_service );
		$this->content_filter_service = new Wordlift_Content_Filter_Service( $this->entity_service, $this->configuration_service, $this->entity_uri_service );
		// Creating Faq Content filter service.
		$this->faq_content_filter_service = new Faq_Content_Filter();
		$this->relation_rebuild_service   = new Wordlift_Relation_Rebuild_Service( $this->content_filter_service, $this->entity_service );
		$this->sample_data_service        = new Wordlift_Sample_Data_Service( $this->entity_type_service, $this->configuration_service, $this->user_service );
		$this->sample_data_ajax_adapter   = new Wordlift_Sample_Data_Ajax_Adapter( $this->sample_data_service );
		$this->reference_rebuild_service  = new Wordlift_Reference_Rebuild_Service( $this->entity_service );

		$this->loader->add_action( 'enqueue_block_editor_assets', $this, 'add_wl_enabled_blocks' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this, 'add_wl_enabled_blocks' );

		/**
		 * Filter: wl_feature__enable__blocks.
		 *
		 * @param bool whether the blocks needed to be registered, defaults to true.
		 *
		 * @return bool
		 * @since 3.27.6
		 */
		if ( apply_filters( 'wl_feature__enable__blocks', true ) ) {
			// Initialize the short-codes.
			new Async_Template_Decorator( new Wordlift_Navigator_Shortcode() );
			new Wordlift_Chord_Shortcode();
			new Wordlift_Geomap_Shortcode();
			new Wordlift_Timeline_Shortcode();
			new Wordlift_Related_Entities_Cloud_Shortcode( $this->relation_service );
			new Wordlift_Vocabulary_Shortcode( $this->configuration_service );
			new Async_Template_Decorator( new Wordlift_Faceted_Search_Shortcode() );
		}

		new Wordlift_Products_Navigator_Shortcode();


		// Initialize the Context Cards Service
		$this->context_cards_service = new Wordlift_Context_Cards_Service();

		// Initialize the SEO service.
		new Wordlift_Seo_Service();

		// Initialize the AMP service.
		new Wordlift_AMP_Service( $this->jsonld_service );

		/** Services. */
		$this->google_analytics_export_service = new Wordlift_Google_Analytics_Export_Service();
		new Wordlift_Image_Service();

		/** Adapters. */
		$this->entity_type_adapter    = new Wordlift_Entity_Type_Adapter( $this->entity_type_service );
		$this->publisher_ajax_adapter = new Wordlift_Publisher_Ajax_Adapter( $this->publisher_service );
		$this->tinymce_adapter        = new Wordlift_Tinymce_Adapter( $this );
		//$this->faq_tinymce_adapter      = new Faq_Tinymce_Adapter();
		$this->relation_rebuild_adapter = new Wordlift_Relation_Rebuild_Adapter( $this->relation_rebuild_service );

		/*
		 * Exclude our public js from WP-Rocket.
		 *
		 * @since 3.19.4
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/842.
		 */
		new Wordlift_WpRocket_Adapter();

		// Create a Rebuild Service instance, which we'll later bound to an ajax call.
		$this->rebuild_service = new Wordlift_Rebuild_Service(
			$this->sparql_service,
			$uri_service
		);

		$that = $this;
		add_action( 'plugins_loaded', function () use ( $that ) {
			if ( ! apply_filters( 'wl_feature__enable__dataset-ng', false ) ) {
				new Wordlift_Linked_Data_Service( $that->entity_service, $that->entity_type_service, $that->schema_service, $that->sparql_service );
				new Wordlift_Sparql_Query_Async_Task();
				new Wordlift_Push_References_Async_Task();
			}
		} );

		/** WordPress Admin UI. */

		// UI elements.
		$this->input_element           = new Wordlift_Admin_Input_Element();
		$this->radio_input_element     = new Wordlift_Admin_Radio_Input_Element();
		$this->select2_element         = new Wordlift_Admin_Select2_Element();
		$this->language_select_element = new Wordlift_Admin_Language_Select_Element();
		$this->country_select_element  = new Wordlift_Admin_Country_Select_Element();
		$tabs_element                  = new Wordlift_Admin_Tabs_Element();
		$this->publisher_element       = new Wordlift_Admin_Publisher_Element( $this->configuration_service, $this->publisher_service, $tabs_element, $this->select2_element );
		$this->author_element          = new Wordlift_Admin_Author_Element( $this->publisher_service, $this->select2_element );

		$this->settings_page             = new Wordlift_Admin_Settings_Page( $this->configuration_service, $this->entity_service, $this->input_element, $this->language_select_element, $this->country_select_element, $this->publisher_element, $this->radio_input_element );
		$this->settings_page_action_link = new Wordlift_Admin_Settings_Page_Action_Link( $this->settings_page );

		$this->analytics_settings_page             = new Wordlift_Admin_Settings_Analytics_Page( $this->configuration_service, $this->input_element, $this->radio_input_element );
		$this->analytics_settings_page_action_link = new Wordlift_Admin_Settings_Analytics_Page_Action_Link( $this->analytics_settings_page );
		$this->analytics_connect                   = new Wordlift_Analytics_Connect();

		// Pages.
		/*
		 * Call the `wl_can_see_classification_box` filter to determine whether we can display the classification box.
		 *
		 * @since 3.20.3
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/914
		 */
		if ( apply_filters( 'wl_can_see_classification_box', true ) ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordlift-admin-post-edit-page.php';
			new Wordlift_Admin_Post_Edit_Page( $this );
		}
		new Wordlift_Entity_Type_Admin_Service();

		// create an instance of the entity type list admin page controller.
		$this->entity_type_admin_page = new Wordlift_Admin_Entity_Taxonomy_List_Page();

		// create an instance of the entity type setting admin page controller.
		$this->entity_type_settings_admin_page = new Wordlift_Admin_Entity_Type_Settings();

		/** Widgets */
		$this->related_entities_cloud_widget = new Wordlift_Related_Entities_Cloud_Widget();

		/* WordPress Admin. */
		$this->download_your_data_page = new Wordlift_Admin_Download_Your_Data_Page( $this->configuration_service );
		$this->status_page             = new Wordlift_Admin_Status_Page( $this->entity_service, $this->sparql_service );

		// Create an instance of the install wizard.
		$this->admin_setup = new Wordlift_Admin_Setup( $this->configuration_service, $this->key_validation_service, $this->entity_service, $this->language_select_element, $this->country_select_element );

		$this->category_taxonomy_service = new Wordlift_Category_Taxonomy_Service( $this->entity_post_type_service );

		// User Profile.
		new Wordlift_Admin_User_Profile_Page( $this->author_element, $this->user_service );

		$this->entity_page_service = new Wordlift_Entity_Page_Service();

		// Load the debug service if WP is in debug mode.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-debug-service.php';
			new Wordlift_Debug_Service( $this->entity_service, $uri_service );
		}

		// Remote Image Service.
		new Wordlift_Remote_Image_Service();

		/*
		 * Provides mappings between post types and entity types.
		 *
		 * @since 3.20.0
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/852.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordlift-batch-action.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/mapping/class-wordlift-mapping-service.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/mapping/class-wordlift-mapping-ajax-adapter.php';

		// Create an instance of the Mapping Service and assign it to the Ajax Adapter.
		new Wordlift_Mapping_Ajax_Adapter( new Wordlift_Mapping_Service( Wordlift_Entity_Type_Service::get_instance() ) );

		/*
		 * Batch Operations. They're similar to Batch Actions but do not require working on post types.
		 *
		 * Eventually Batch Actions will become Batch Operations.
		 *
		 * @since 3.20.0
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/batch/intf-wordlift-batch-operation.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/batch/class-wordlift-batch-operation-ajax-adapter.php';

		/*
		 * Add the Search Keywords taxonomy to manage the Search Keywords on WLS.
		 *
		 * @link https://github.com/insideout10/wordlift-plugin/issues/761
		 *
		 * @since 3.20.0
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/search-keywords/class-wordlift-search-keyword-taxonomy.php';
		new Wordlift_Search_Keyword_Taxonomy( $api_service );

		/*
		 * Load the Mappings JSON-LD post processing.
		 *
		 * @since 3.25.0
		 */

		$mappings_dbo           = new Mappings_DBO();
		$default_rule_validator = new Taxonomy_Rule_Validator();
		new Post_Type_Rule_Validator();
		// Taxonomy term rule validator for validating rules for term pages.
		new Taxonomy_Term_Rule_Validator();
		new Post_Taxonomy_Term_Rule_Validator();
		$rule_validators_registry = new Rule_Validators_Registry( $default_rule_validator );
		$rule_groups_validator    = new Rule_Groups_Validator( $rule_validators_registry );
		$mappings_validator       = new Mappings_Validator( $mappings_dbo, $rule_groups_validator );

		new Url_To_Entity_Transform_Function( $this->entity_uri_service );
		new Taxonomy_To_Terms_Transform_Function();
		new Post_Id_To_Entity_Transform_Function();
		$mappings_transform_functions_registry = new Mappings_Transform_Functions_Registry();

		/**
		 * @since 3.27.1
		 * Intiailize the acf group data formatter.
		 */
		new Acf_Group_Formatter();
		new Jsonld_Converter( $mappings_validator, $mappings_transform_functions_registry );

		/**
		 * @since 3.26.0
		 * Initialize the Faq JSON LD converter here - disabled.
		 */
		// new Faq_To_Jsonld_Converter();
		/*
		 * Use the Templates Ajax Endpoint to load HTML templates for the legacy Angular app via admin-ajax.php
		 * end-point.
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/834
		 * @since 3.24.4
		 */
		new Templates_Ajax_Endpoint();
		// Call this static method to register FAQ routes to rest api - disabled
		//Faq_Rest_Controller::register_routes();

		/*
		 * Create a singleton for the Analysis_Response_Ops_Factory.
		 */
		$entity_helper = new Entity_Helper( $this->entity_uri_service, $this->entity_service );
		new Analysis_Response_Ops_Factory(
			$this->entity_uri_service,
			$this->entity_service,
			$this->entity_type_service,
			$this->storage_factory->post_images(),
			$entity_helper
		);

		/** WL Autocomplete. */
		$autocomplete_service       = new All_Autocomplete_Service( array(
			new Local_Autocomplete_Service(),
			new Linked_Data_Autocomplete_Service( $this->configuration_service, $entity_helper, $this->entity_uri_service, $this->entity_service ),
		) );
		$this->autocomplete_adapter = new Wordlift_Autocomplete_Adapter( $autocomplete_service );

		/**
		 * @since 3.27.2
		 * Integrate the recipe maker jsonld & set recipe
		 * as default entity type to the wprm_recipe CPT.
		 */
		new Recipe_Maker_Post_Type_Hook();
		$recipe_maker_validation_service = new Recipe_Maker_Validation_Service();
		new Recipe_Maker_Jsonld_Hook( $attachment_service, $recipe_maker_validation_service );
		new Recipe_Maker_After_Get_Jsonld_Hook( $recipe_maker_validation_service );
		new Recipe_Maker_Warning( $recipe_maker_validation_service );
		new Yoast_Jsonld( $recipe_maker_validation_service );

		/**
		 * @since 3.27.4
		 * Add the faq duplicate markup hook.
		 */
		new Faq_Duplicate_Markup_Remover();
		/**
		 * @since 3.27.8
		 * @see https://github.com/insideout10/wordlift-plugin/issues/1248
		 */
		new Key_Validation_Notice( $this->key_validation_service, $this->configuration_service );
		/**
		 * @since 3.28.0
		 * @see https://github.com/insideout10/wordlift-plugin/issues?q=assignee%3Anaveen17797+is%3Aopen
		 */
		new Entity_No_Index_Flag();

		/**
		 * @since 3.29.0
		 * @see https://github.com/insideout10/wordlift-plugin/issues/1304
		 */
		new Entity_Rest_Service( $this->entity_type_service );

		/**
		 * Expand author in to references.
		 * @since 3.30.0
		 * @see https://github.com/insideout10/wordlift-plugin/issues/1318
		 */

		add_action( 'plugins_loaded', function () use ( $that ) {

			if ( apply_filters( 'wl_feature__enable__article-wrapper', false ) ) {
				new Jsonld_Article_Wrapper( Wordlift_Post_To_Jsonld_Converter::get_instance(), $that->cached_postid_to_jsonld_converter );
			}

			if ( apply_filters( 'wl_feature__enable__match-terms', false ) ) {
				$vocabulary_loader = new Vocabulary_Loader();
				$vocabulary_loader->init_vocabulary();
			}

		} );

		/**
		 * @since 3.30.0
		 * Add a checkbox to user option screen for wordlift admin.
		 */
		$wordlift_admin_checkbox = new Admin_User_Option();
		$wordlift_admin_checkbox->connect_hook();
		/**
		 * @since 3.31.0
		 * Init loader class for videoobject.
		 */
		$videoobject_loader = new Loader();
		$videoobject_loader->init_feature();
		/**
		 * @since 3.31.5
		 * Create configuration endpoint for webapp to configure.
		 */
		new Config( $this->admin_setup, $this->key_validation_service, $this->configuration_service );
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wordlift_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wordlift_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$that         = $this;
		$plugin_admin = new Wordlift_Admin(
			$this->get_plugin_name(),
			$this->get_version(),
			$this->configuration_service,
			$this->notice_service,
			$this->user_service
		);

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts', 11 );

		// Hook the init action to taxonomy services.
		$this->loader->add_action( 'init', $this->topic_taxonomy_service, 'init', 0 );
		$this->loader->add_action( 'init', $this->entity_types_taxonomy_service, 'init', 0 );

		// Hook the deleted_post_meta action to the Thumbnail service.
		$this->loader->add_action( 'deleted_post_meta', $this->thumbnail_service, 'deleted_post_meta', 10, 4 );

		// Hook the added_post_meta action to the Thumbnail service.
		$this->loader->add_action( 'added_post_meta', $this->thumbnail_service, 'added_or_updated_post_meta', 10, 4 );

		// Hook the updated_post_meta action to the Thumbnail service.
		$this->loader->add_action( 'updated_post_meta', $this->thumbnail_service, 'added_or_updated_post_meta', 10, 4 );

		// Hook the AJAX wl_timeline action to the Timeline service.
		$this->loader->add_action( 'wp_ajax_wl_timeline', $this->timeline_service, 'ajax_timeline' );

		// Register custom allowed redirect hosts.
		$this->loader->add_filter( 'allowed_redirect_hosts', $this->redirect_service, 'allowed_redirect_hosts' );
		// Hook the AJAX wordlift_redirect action to the Redirect service.
		$this->loader->add_action( 'wp_ajax_wordlift_redirect', $this->redirect_service, 'ajax_redirect' );

		/*
		 * The old dashboard is replaced with dashboard v2.
		 *
		 * The old dashboard service is still loaded because its functions are used.
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/879
		 *
		 * @since 3.20.0
		 */
		// Hook the AJAX wordlift_redirect action to the Redirect service.
		// $this->loader->add_action( 'wp_ajax_wordlift_get_stats', $this->dashboard_service, 'ajax_get_stats' );
		// Hook the AJAX wordlift_redirect action to the Redirect service.
		// $this->loader->add_action( 'wp_dashboard_setup', $this->dashboard_service, 'add_dashboard_widgets' );

		// Hook save_post to the entity service to update custom fields (such as alternate labels).
		// We have a priority of 9 because we want to be executed before data is sent to Redlink.
		$this->loader->add_action( 'save_post', $this->entity_service, 'save_post', 9, 3 );
		$this->loader->add_action( 'save_post', $this->rating_service, 'set_rating_for', 20, 1 );

		$this->loader->add_action( 'edit_form_before_permalink', $this->entity_service, 'edit_form_before_permalink', 10, 1 );
		$this->loader->add_action( 'in_admin_header', $this->rating_service, 'in_admin_header' );

		// Entity listing customization (wp-admin/edit.php)
		// Add custom columns.
		$this->loader->add_filter( 'manage_entity_posts_columns', $this->entity_list_service, 'register_custom_columns' );
		// no explicit entity as it prevents handling of other post types.
		$this->loader->add_filter( 'manage_posts_custom_column', $this->entity_list_service, 'render_custom_columns', 10, 2 );
		// Add 4W selection.
		$this->loader->add_action( 'restrict_manage_posts', $this->entity_list_service, 'restrict_manage_posts_classification_scope' );
		$this->loader->add_filter( 'posts_clauses', $this->entity_list_service, 'posts_clauses_classification_scope' );
		$this->loader->add_action( 'pre_get_posts', $this->entity_list_service, 'pre_get_posts' );
		$this->loader->add_action( 'load-edit.php', $this->entity_list_service, 'load_edit' );

		/*
		 * If `All Entity Types` is disable, use the radio button Walker.
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/835
		 */
		if ( ! WL_ALL_ENTITY_TYPES ) {
			$this->loader->add_filter( 'wp_terms_checklist_args', $this->entity_types_taxonomy_walker, 'terms_checklist_args' );
		}

		// Hook the PrimaShop adapter to <em>prima_metabox_entity_header_args</em> in order to add header support for
		// entities.
		$this->loader->add_filter( 'prima_metabox_entity_header_args', $this->primashop_adapter, 'prima_metabox_entity_header_args', 10, 2 );

		// Filter imported post meta.
		$this->loader->add_filter( 'wp_import_post_meta', $this->import_service, 'wp_import_post_meta', 10, 3 );

		// Notify the import service when an import starts and ends.
		$this->loader->add_action( 'import_start', $this->import_service, 'import_start', 10, 0 );
		$this->loader->add_action( 'import_end', $this->import_service, 'import_end', 10, 0 );

		// Hook the AJAX wl_rebuild action to the Rebuild Service.
		$this->loader->add_action( 'wp_ajax_wl_rebuild', $this->rebuild_service, 'rebuild' );
		$this->loader->add_action( 'wp_ajax_wl_rebuild_references', $this->reference_rebuild_service, 'rebuild' );

		/**
		 * Filter: wl_feature__enable__settings-download.
		 *
		 * @param bool whether the screens needed to be registered, defaults to true.
		 *
		 * @return bool
		 * @since 3.27.6
		 */
		$this->features_registry->register_feature_from_slug( 'settings-download', true, array(
			$this,
			'register_screens'
		) );


		// Hook the admin-ajax.php?action=wl_download_your_data&out=xyz links.
		$this->loader->add_action( 'wp_ajax_wl_download_your_data', $this->download_your_data_page, 'download_your_data', 10 );

		// Hook the AJAX wl_jsonld action to the JSON-LD service.
		$this->loader->add_action( 'wp_ajax_wl_jsonld', $this->jsonld_service, 'get' );
		$this->loader->add_action( 'admin_post_wl_jsonld', $this->jsonld_service, 'get' );
		$this->loader->add_action( 'admin_post_nopriv_wl_jsonld', $this->jsonld_service, 'get' );

		// Hook the AJAX wl_validate_key action to the Key Validation service.
		$this->loader->add_action( 'wp_ajax_wl_validate_key', $this->key_validation_service, 'validate_key' );

		// Hook the AJAX wl_update_country_options action to the countries.
		$this->loader->add_action( 'wp_ajax_wl_update_country_options', $this->country_select_element, 'get_options_html' );

		// Hook the `admin_init` function to the Admin Setup.
		$this->loader->add_action( 'admin_init', $this->admin_setup, 'admin_init' );

		// Hook the admin_init to the settings page.
		$this->loader->add_action( 'admin_init', $this->settings_page, 'admin_init' );
		$this->loader->add_action( 'admin_init', $this->analytics_settings_page, 'admin_init' );

		$this->loader->add_filter( 'admin_post_thumbnail_html', $this->publisher_service, 'add_featured_image_instruction' );

		// Hook the menu creation on the general wordlift menu creation.
		/**
		 * Filter: wl_feature__enable__screens.
		 *
		 * @param bool whether the screens needed to be registered, defaults to true.
		 *
		 * @return bool
		 * @since 3.27.6
		 *
		 * Since 3.30.0 this feature is registered using registry.
		 */
		add_action( 'plugins_loaded', function () use ( $that ) {
			if ( apply_filters( 'wl_feature__enable__settings-screen', true ) || Admin_User_Option::is_wordlift_admin() ) {
				add_action( 'wl_admin_menu', array( $that->settings_page, 'admin_menu' ), 10, 2 );
			}
		} );

		// Hook key update.
		$this->loader->add_action( 'pre_update_option_wl_general_settings', $this->configuration_service, 'maybe_update_dataset_uri', 10, 2 );
		$this->loader->add_action( 'update_option_wl_general_settings', $this->configuration_service, 'update_key', 10, 2 );

		// Add additional action links to the WordLift plugin in the plugins page.
		$this->loader->add_filter( 'plugin_action_links_wordlift/wordlift.php', $this->settings_page_action_link, 'action_links', 10, 1 );

		/*
		 * Remove the Analytics Settings link from the plugin page.
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/932
		 * @since 3.21.1
		 */
		// $this->loader->add_filter( 'plugin_action_links_wordlift/wordlift.php', $this->analytics_settings_page_action_link, 'action_links', 10, 1 );

		// Hook the AJAX `wl_publisher` action name.
		$this->loader->add_action( 'wp_ajax_wl_publisher', $this->publisher_ajax_adapter, 'publisher' );

		// Hook row actions for the entity type list admin.
		$this->loader->add_filter( 'wl_entity_type_row_actions', $this->entity_type_admin_page, 'wl_entity_type_row_actions', 10, 2 );

		/** Ajax actions. */
		$this->loader->add_action( 'wp_ajax_wl_google_analytics_export', $this->google_analytics_export_service, 'export' );

		// Hook capabilities manipulation to allow access to entity type admin
		// page  on WordPress versions before 4.7.
		global $wp_version;
		if ( version_compare( $wp_version, '4.7', '<' ) ) {
			$this->loader->add_filter( 'map_meta_cap', $this->entity_type_admin_page, 'enable_admin_access_pre_47', 10, 4 );
		}

		$this->loader->add_action( 'wl_async_wl_run_sparql_query', $this->sparql_service, 'run_sparql_query', 10, 1 );

		/** Adapters. */
		$this->loader->add_filter( 'mce_external_plugins', $this->tinymce_adapter, 'mce_external_plugins', 10, 1 );
		/**
		 * Disabling Faq temporarily.
		 * Load the tinymce editor button on the tool bar.
		 * @since 3.26.0
		 */
		//$this->loader->add_filter( 'tiny_mce_before_init', $this->faq_tinymce_adapter, 'register_custom_tags' );
		//$this->loader->add_filter( 'mce_buttons', $this->faq_tinymce_adapter, 'register_faq_toolbar_button', 10, 1 );
		//$this->loader->add_filter( 'mce_external_plugins', $this->faq_tinymce_adapter, 'register_faq_tinymce_plugin', 10, 1 );


		$this->loader->add_action( 'wp_ajax_wl_relation_rebuild_process_all', $this->relation_rebuild_adapter, 'process_all' );
		$this->loader->add_action( 'wp_ajax_wl_sample_data_create', $this->sample_data_ajax_adapter, 'create' );
		$this->loader->add_action( 'wp_ajax_wl_sample_data_delete', $this->sample_data_ajax_adapter, 'delete' );
		/**
		 * @since 3.26.0
		 */
		$excerpt_adapter = new Post_Excerpt_Meta_Box_Adapter();
		$this->loader->add_action( 'do_meta_boxes', $excerpt_adapter, 'replace_post_excerpt_meta_box' );
		// Adding Rest route for the post excerpt
		Post_Excerpt_Rest_Controller::register_routes();

		$this->loader->add_action( 'update_user_metadata', $this->user_service, 'update_user_metadata', 10, 5 );
		$this->loader->add_action( 'delete_user_metadata', $this->user_service, 'delete_user_metadata', 10, 5 );

		// Handle the autocomplete request.
		add_action( 'wp_ajax_wl_autocomplete', array(
			$this->autocomplete_adapter,
			'wl_autocomplete',
		) );
		add_action( 'wp_ajax_nopriv_wl_autocomplete', array(
			$this->autocomplete_adapter,
			'wl_autocomplete',
		) );

		// Hooks to restrict multisite super admin from manipulating entity types.
		if ( is_multisite() ) {
			$this->loader->add_filter( 'map_meta_cap', $this->entity_type_admin_page, 'restrict_super_admin', 10, 4 );
		}

		$deactivator_feedback = new Wordlift_Deactivator_Feedback( $this->configuration_service );

		add_action( 'admin_footer', array( $deactivator_feedback, 'render_feedback_popup' ) );
		add_action( 'admin_enqueue_scripts', array( $deactivator_feedback, 'enqueue_popup_scripts' ) );
		add_action( 'wp_ajax_wl_deactivation_feedback', array( $deactivator_feedback, 'wl_deactivation_feedback' ) );

		/**
		 * Always allow the `wordlift/classification` block.
		 *
		 * @since 3.23.0
		 */
		add_filter( 'allowed_block_types', function ( $value ) {

			if ( true === $value ) {
				return $value;
			}

			return array_merge( (array) $value, array( 'wordlift/classification' ) );
		}, PHP_INT_MAX );

		/**
		 * @since 3.27.7
		 * @see https://github.com/insideout10/wordlift-plugin/issues/1214
		 */
		new Top_Entities();
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wordlift_Public( $this->get_plugin_name(), $this->get_version() );

		// Register the entity post type.
		$this->loader->add_action( 'init', $this->entity_post_type_service, 'register' );

		// Bind the link generation and handling hooks to the entity link service.
		$this->loader->add_filter( 'post_type_link', $this->entity_link_service, 'post_type_link', 10, 4 );
		$this->loader->add_action( 'pre_get_posts', $this->entity_link_service, 'pre_get_posts', PHP_INT_MAX, 1 );
		// $this->loader->add_filter( 'wp_unique_post_slug_is_bad_flat_slug', $this->entity_link_service, 'wp_unique_post_slug_is_bad_flat_slug', 10, 3 );
		// $this->loader->add_filter( 'wp_unique_post_slug_is_bad_hierarchical_slug', $this->entity_link_service, 'wp_unique_post_slug_is_bad_hierarchical_slug', 10, 4 );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_enqueue_scripts', $this->context_cards_service, 'enqueue_scripts' );

		// Registering Faq_Content_Filter service used for removing faq question and answer tags from the html.
		$this->loader->add_filter( 'the_content', $this->faq_content_filter_service, 'remove_all_faq_question_and_answer_tags' );
		// Hook the content filter service to add entity links.
		if ( ! defined( 'WL_DISABLE_CONTENT_FILTER' ) || ! WL_DISABLE_CONTENT_FILTER ) {
			$this->loader->add_filter( 'the_content', $this->content_filter_service, 'the_content' );
		}

		// Hook the AJAX wl_timeline action to the Timeline service.
		$this->loader->add_action( 'wp_ajax_nopriv_wl_timeline', $this->timeline_service, 'ajax_timeline' );

		// Hook the ShareThis service.
		$this->loader->add_filter( 'the_content', $this->sharethis_service, 'the_content', 99 );
		$this->loader->add_filter( 'the_excerpt', $this->sharethis_service, 'the_excerpt', 99 );

		// Hook the AJAX wl_jsonld action to the JSON-LD service.
		$this->loader->add_action( 'wp_ajax_nopriv_wl_jsonld', $this->jsonld_service, 'get' );

		// Hook the `pre_get_posts` action to the `Wordlift_Category_Taxonomy_Service`
		// in order to tweak WP's `WP_Query` to include entities in queries related
		// to categories.
		$this->loader->add_action( 'pre_get_posts', $this->category_taxonomy_service, 'pre_get_posts', 10, 1 );

		/*
		 * Hook the `pre_get_posts` action to the `Wordlift_Entity_Page_Service`
		 * in order to tweak WP's `WP_Query` to show event related entities in reverse
		 * order of start time.
		 */
		$this->loader->add_action( 'pre_get_posts', $this->entity_page_service, 'pre_get_posts', 10, 1 );

		$this->loader->add_action( 'wl_async_wl_run_sparql_query', $this->sparql_service, 'run_sparql_query', 10, 1 );

		// This hook have to run before the rating service, as otherwise the post might not be a proper entity when rating is done.
		$this->loader->add_action( 'save_post', $this->entity_type_adapter, 'save_post', 9, 3 );

		// Analytics Script Frontend.
		if ( $this->configuration_service->is_analytics_enable() ) {
			$this->loader->add_action( 'wp_enqueue_scripts', $this->analytics_connect, 'enqueue_scripts', 10 );
		}

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Wordlift_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Load dependencies for WP-CLI.
	 *
	 * @throws Exception
	 * @since 3.18.0
	 */
	private function load_cli_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'cli/class-wordlift-push-reference-data-command.php';

		$push_reference_data_command = new Wordlift_Push_Reference_Data_Command( $this->relation_service, $this->entity_service, $this->sparql_service, $this->configuration_service, $this->entity_type_service );

		WP_CLI::add_command( 'wl references push', $push_reference_data_command );

	}

	/**
	 * Get the {@link \Wordlift_Dashboard_Service} to allow others to use its functions.
	 *
	 * @return \Wordlift_Dashboard_Service The {@link \Wordlift_Dashboard_Service} instance.
	 * @since 3.20.0
	 */
	public function get_dashboard_service() {

		return $this->dashboard_service;
	}

	public function add_wl_enabled_blocks() {
		/**
		 * Filter: wl_feature__enable__blocks.
		 *
		 * @param bool whether the blocks needed to be registered, defaults to true.
		 *
		 * @return bool
		 * @since 3.27.6
		 */

		wp_register_script( 'wl_enabled_blocks', false );

		$enabled_blocks = array();

		/**
		 * Filter name: wl_feature_enable__product_navigator
		 * @since 3.30.0
		 */
		if ( apply_filters( 'wl_feature_enable__product-navigator', true ) ) {
			$enabled_blocks[] = 'wordlift/products-navigator';
		}

		if ( apply_filters( 'wl_feature__enable__blocks', true ) ) {
			// To intimate JS
			$enabled_blocks = array_merge( $enabled_blocks, array(
				'wordlift/navigator',
				'wordlift/chord',
				'wordlift/geomap',
				'wordlift/timeline',
				'wordlift/cloud',
				'wordlift/vocabulary',
				'wordlift/faceted-search'
			) );
		}

		wp_localize_script( 'wl_enabled_blocks', 'wlEnabledBlocks', $enabled_blocks );
		wp_enqueue_script( 'wl_enabled_blocks' );
	}

	/**
	 * Register screens based on the filter.
	 */
	public function register_screens() {
		// Hook the menu to the Download Your Data page.
		if ( apply_filters( 'wl_feature__enable__settings-download', true ) ) {
			add_action( 'admin_menu', array( $this->download_your_data_page, 'admin_menu' ), 100, 0 );
		}
		add_action( 'admin_menu', array( $this->status_page, 'admin_menu' ), 100, 0 );
		add_action( 'admin_menu', array( $this->entity_type_settings_admin_page, 'admin_menu' ), 100, 0 );

	}

}
