<?php
require_once 'TypeService.php';

class FormBuilderService {
	
	public static function build_type_selection(&$types,$field_prefix) {

		echo '<div class="property">';
		echo '<label for="'.$field_prefix.'schema-type">Type: </label>';
	
		echo '<select name="'.$field_prefix.'schema-type">';
		
		foreach ($types as $type) {
			echo '<option>'.$type.'</option>';
		}
		
		echo '</select>';
		echo '</div>';
	
	}
	
	public static function build_form_for_type(&$type,$field_prefix,&$meta = NULL) {
		
// 		echo '<input type="hidden" name="'.WORDLIFT_20_FIELD_SCHEMA_TYPE.'" value="'.$meta[WORDLIFT_20_FIELD_SCHEMA_TYPE][0].'">';
		
		$type_selection_view = new TypeSelectionView($type->name);
		echo $type_selection_view->getContent();
		
		foreach ($type->properties as $property) {
			
			$name = htmlentities($property->name);
			$description = htmlentities($property->description);
			$type = $property->type;
			$field_name = htmlentities($field_prefix.$name);
			$field_value = htmlentities($meta[$field_name][0]);
			
			switch ($type) {
				case 'Text';
					echo '<div class="property">';
					echo '<label for="'.$field_name.'">'.$name.': </label>';
					
					if (true == $property->multiline) {
						echo '<textarea name="'.$field_name.'">'.$field_value.'</textarea>';
						echo '<div class="description">'.$description.'</div>';
					} else {
						echo '<input name="'.$field_name.'" value="'.$field_value.'" type="text" />';
						echo '<div class="description">'.$description.'</div>';
					}
					
					echo '</div>';
					
					break;
					
				case 'URL';
					echo '<div class="property">';
					echo '<label for="'.$field_name.'">'.$name.': </label>';					
					echo '<input name="'.$field_name.'" value="'.$field_value.'" type="text" />';
					echo '<div class="description">'.$description.'</div>';
					echo '</div>';
					
					break;
					
				case 'GeoCoordinates';
					echo '<div class="geo">';
					
					echo '<div class="properties">';
					
					echo '<div class="property">';
					
					$field_name = htmlentities($field_prefix.'geo-latitude');
					$latitude = htmlentities($meta[$field_name][0]);
					echo '<label for="'.$field_name.'">Latitude: </label>';
					echo '<input name="'.$field_name.'" value="'.$latitude.'" type="text" />';
					
					echo '</div>';
					echo '<div class="property">';
					
					$field_name = htmlentities($field_prefix.'geo-longitude');
					$longitude = htmlentities($meta[$field_name][0]);
					echo '<label for="'.$field_name.'">Longitude: </label>';
					echo '<input name="'.$field_name.'" value="'.$longitude.'" type="text" />';
					
					echo '</div>';
					echo '</div>';

					echo '<div class="map">';
					echo '<div id="entityMap"></div>';
					echo '</div>';
					
					echo '</div>';
?>
					<script type="text/javascript">
						jQuery(window).ready( function($){
			
<?php						echo 'var longitude = \''.$longitude.'\';';  ?>
<?php						echo 'var latitude  = \''.$latitude.'\';'; 	?>

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
					break;	
				
				default:
// 					echo '<div class="property">';
// 					echo 'Type &quot;'.$type.'&quot; not supported for field &quot;'.$name.'&quot;.';
// 					echo '</div>';					
			}
		}
		
	}
	
}

?>