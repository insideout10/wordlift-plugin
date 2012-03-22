<?php
/**
 * This class is responsible to show the meta-box when editing an entity in the admin area.
 */
class EntityMetaBox {

	private $logger;

	function __construct() {
		$this->logger 		= Logger::getLogger(__CLASS__);
	}
	
	/**
	 * Registers the meta-box handler.
	 */
	function register_meta_box_cb(){
		add_meta_box('entities-properties','Properties', array( $this, 'entities_properties_box'), WORDLIFT_20_ENTITY_CUSTOM_POST_TYPE);
	}

	/**
	 * Draws the meta-box.
	 */
	function entities_properties_box( $post ){

		$custom_fields 	= get_post_custom($post->ID);

		$type = TypeService::create($custom_fields[WORDLIFT_20_FIELD_SCHEMA_TYPE][0]);
		FormBuilderService::build_form_for_type($type,WORDLIFT_20_FIELD_PREFIX,$custom_fields);

	}

}

$entity_meta_box 	= new EntityMetaBox();

?>