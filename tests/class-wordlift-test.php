<?php
/**
 * Tests: WordLift Test Mockup.
 *
 * The {@link Wordlift_Test} class provides access to WordLift's services in order
 * to be able to test them.
 *
 * @since   3.10.0
 * @package Wordlift
 */

/**
 * Define the {@link Wordlift_Test} class.
 *
 * @since   3.10.0
 * @package Wordlift
 */
class Wordlift_Test extends Wordlift {

	/**
	 * {@link Wordlift} singleton instance.
	 *
	 * @since  3.10.0
	 * @access private
	 * @var \Wordlift $instance {@link Wordlift} singleton instance.
	 */
	private static $instance;

	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		parent::__construct();

		self::$instance = $this;

	}

	/**
	 * Get {@link Wordlift_Test} singleton instance.
	 *
	 * @since 3.10.0
	 *
	 * @return Wordlift_Test {@link Wordlift_Test} singleton instance.
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * A {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since 3.10.0
	 *
	 * @return Wordlift_Configuration_Service A {@link Wordlift_Configuration_Service} instance.
	 */
	public function get_configuration_service() {

		return $this->configuration_service;
	}

	/**
	 * Get the {@link Wordlift_Entity_Service} instance.
	 *
	 * @since 3.10.0
	 *
	 * @return \Wordlift_Entity_Service The {@link Wordlift_Entity_Service} instance.
	 */
	public function get_entity_service() {

		return $this->entity_service;
	}

	/**
	 * Get the {@link Wordlift_User_Service} instance.
	 *
	 * @since 3.10.0
	 *
	 * @return \Wordlift_User_Service The {@link Wordlift_User_Service} instance.
	 */
	public function get_user_service() {

		return $this->user_service;
	}

	/**
	 * Get the {@link Wordlift_Post_To_Jsonld_Converter} instance.
	 *
	 * @since 3.10.0
	 *
	 * @return \Wordlift_Post_To_Jsonld_Converter The {@link Wordlift_Post_To_Jsonld_Converter} instance.
	 */
	public function get_post_to_jsonld_converter() {

		return $this->post_to_jsonld_converter;
	}

	/**
	 * Get the {@link Wordlift_Entity_Post_To_Jsonld_Converter} instance.
	 *
	 * @since 3.10.0
	 *
	 * @return \Wordlift_Entity_Post_To_Jsonld_Converter The {@link Wordlift_Entity_Post_To_Jsonld_Converter} instance.
	 */
	public function get_entity_post_to_jsonld_converter() {

		return $this->entity_post_to_jsonld_converter;
	}

	/**
	 * Get the {@link Wordlift_Postid_To_Jsonld_Converter} instance.
	 *
	 * @since 3.10.0
	 *
	 * @return \Wordlift_Postid_To_Jsonld_Converter Get the {@link Wordlift_Postid_To_Jsonld_Converter} instance.
	 */
	public function get_postid_to_jsonld_converter() {

		return $this->postid_to_jsonld_converter;
	}

	/**
	 * Get the {@link Wordlift_Entity_Type_Service} instance.
	 *
	 * @since 3.10.0
	 *
	 * @return \Wordlift_Entity_Type_Service The {@link Wordlift_Entity_Type_Service} instance.
	 *
	 */
	public function get_entity_type_service() {

		return $this->entity_type_service;
	}

	/**
	 * Get the {@link Wordlift_Jsonld_Service} instance.
	 *
	 * @since 3.10.0
	 *
	 * @return \Wordlift_Jsonld_Service The {@link Wordlift_Jsonld_Service} instance.
	 */
	public function get_jsonld_service() {

		return $this->jsonld_service;
	}

	/**
	 * Get the {@link Wordlift_Admin_Input_Element} element renderer.
	 *
	 * @since 3.11.0
	 *
	 * @return \Wordlift_Admin_Input_Element The {@link Wordlift_Admin_Input_Element} element renderer.
	 */
	public function get_input_element() {

		return $this->input_element;
	}

	/**
	 * Get the {@link Wordlift_Admin_Language_Select_Element} element renderer.
	 *
	 * @since 3.11.0
	 *
	 * @return \Wordlift_Admin_Language_Select_Element The {@link Wordlift_Admin_Language_Select_Element} element renderer.
	 */
	public function get_language_select_element() {

		return $this->language_select_element;
	}

	/**
	 * Get the {@link Wordlift_Admin_Publisher_Element} element renderer.
	 *
	 * @since 3.11.0
	 *
	 * @return \Wordlift_Admin_Publisher_Element The {@link Wordlift_Admin_Publisher_Element} element renderer.
	 */
	public function get_publisher_element() {

		return $this->publisher_element;
	}

}
