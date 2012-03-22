<?php
require_once 'services/FormBuilderService.php';

class EntityBoxService {

	private $logger;

	function __construct() {
		$this->logger 		= Logger::getLogger(__CLASS__);
	}
	
	function register_meta_box_cb(){
		$this->logger->debug('register_meta_box_cb');

		add_meta_box('entities-properties','Properties', array( $this, 'entities_properties_box'), WORDLIFT_20_ENTITY_CUSTOM_POST_TYPE);
	}

	function entities_properties_box( $post ){

		$custom_fields 	= get_post_custom($post->ID);

// 		$labels 		= $this->get_entity_field($custom_fields, 'label');
// 		$type 			= $this->get_entity_field($custom_fields, 'type');
// 		$id 			= $this->get_entity_field($custom_fields, 'id');
// 		$slug 			= $this->get_entity_field($custom_fields, 'slug');
// 		$thumbnail		= $this->get_entity_field($custom_fields, 'thumbnail');
// 		$descriptions	= $this->get_entity_field($custom_fields, 'description');
// 		$longitude		= $this->get_entity_field($custom_fields, 'longitude');
// 		$latitude		= $this->get_entity_field($custom_fields, 'latitude');

// 		$this->print_input_text('ID (it\'s advised not to change this value)', 	'id', 			$id);
// 		$this->print_input_text('Label', 		'label',		$labels);
// 		$this->print_input_text('Type', 		'type', 		$type);
// 		// $this->print_input_text('slug', 		'slug', 		$slug);
// 		$this->print_input_text('Thumbnail', 	'thumbnail',	$thumbnail);
// 		$this->print_text_area('Descriptions', 	'description', 	$descriptions);
// 		$this->print_input_hidden(				'latitude',		$latitude);
// 		$this->print_input_hidden(				'longitude',	$longitude);

		if (true == $this->has_coordinates($custom_fields)) {
			add_meta_box('entity-map','Map', array( $this, 'entity_map'), WORDLIFT_20_ENTITY_CUSTOM_POST_TYPE);
		}
		
		
		$this->logger->debug('Getting form for type '.$custom_fields[WORDLIFT_20_FIELD_SCHEMA_TYPE][0].'.');
		$type = TypeService::create($custom_fields[WORDLIFT_20_FIELD_SCHEMA_TYPE][0]);
		FormBuilderService::build_form_for_type($type,WORDLIFT_20_FIELD_PREFIX,$custom_fields);
	}

	function has_coordinates(&$custom_fields) {
		$longitude		= $this->get_entity_field($custom_fields, 'longitude');
		$latitude		= $this->get_entity_field($custom_fields, 'latitude');		

		return ('' != $longitude[0] && '' != $latitude[0]);
	}

	function entity_map($post){

		$custom_fields 	= get_post_custom($post->ID);
		$longitude		= $this->get_entity_field($custom_fields, 'longitude');
		$latitude		= $this->get_entity_field($custom_fields, 'latitude');
?>

		<div id="entityMap" style="width: 100%; height: 200px;"></div>

		<script type="text/javascript">
<?php
	echo 'var longitude = '.$longitude[0].';';
	echo 'var latitude  = '.$latitude[0].';';
?>
			jQuery(window).ready( function($){

		        var map = new OpenLayers.Map("entityMap");
		        var mapnik = new OpenLayers.Layer.OSM();
		        map.addLayer(mapnik);
		        map.setCenter(new OpenLayers.LonLat( longitude , latitude ) // Center of the map
		          .transform(
		            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
		            new OpenLayers.Projection("EPSG:900913") // to Spherical Mercator Projection
		          ), 10 // Zoom level
		        );
		    });
		</script>

<?php
	}

	function get_entity_field(&$custom_fields, $field) {
		return $custom_fields[WORDLIFT_20_POST_META_ENTITY_PREFIX.$field];
	}

	function print_input_text($label, $key, $values) {
		$this->print_input($label, $key, $values, 'text');
	}

	function print_input_hidden($key, $values) {
		$this->print_input(NULL, $key, $values, 'hidden');
	}

	function print_input($label, $key, $values, $type) {

		if (0 === count($values)) return;

		foreach ($values as $value) {
			if (NULL != $label && '' != $label)
				echo '<label for="'.$key.'">'.$label.':</label>';

			echo '<input style="width: 100%;" type="'.$type.'" name="'.WORDLIFT_20_POST_META_ENTITY_PREFIX.$key.'" value="'.$value.'"/>';

			if (NULL != $label && '' != $label)
				echo '<br/>';
		}
	}


	function print_text_area($label, $key, $values) {

		if (0 === count($values)) return;

		foreach ($values as $value) {
			echo '<label for="'.$key.'">'.$label.':</label>';
			echo '<textarea style="width: 100%; height: 80px;" type="text" name="'.WORDLIFT_20_POST_META_ENTITY_PREFIX.$key.'">"'.$value.'"</textarea>';
			echo '<br/>';
		}
	}

}

$entity_box_service 	= new EntityBoxService();

?>