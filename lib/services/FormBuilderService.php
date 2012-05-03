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
		
		// we want a case-insensitive look-up to avoid issues with WordLift importer, where usually keys are all lowercase.
		$meta_lower_case = array_change_key_case($meta);
		
		foreach ($type->properties as $property) {
			
			$name = HtmlService::htmlEncode($property->name);
			$description = HtmlService::htmlEncode($property->description);
			$type = $property->type;
			$field_name = HtmlService::htmlEncode($field_prefix.$name);
			$field_value = HtmlService::htmlEncode($meta_lower_case[strtolower($field_name)][0]);
			
			switch ($type) {
				case 'Text';
					echo '<div class="property">';
					echo '<label for="'.$field_name.'">' . $name . ': </label>';
					echo '<div class="description">' . $description . '</div>';
					
					if (true == $property->multiline)
						echo '<textarea name="'.$field_name.'">'.$field_value.'</textarea>';
                    else
						echo '<input name="'.$field_name.'" value="'.$field_value.'" type="text" />';
					
					echo '</div>';
					
					break;
					
				case 'URL';
					echo '<div class="property">';
					echo '<label for="'.$field_name.'">'.$name.': </label>';					
					echo '<div class="description">'.$description.'</div>';
					echo '<input name="'.$field_name.'" value="'.$field_value.'" type="text" />';
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
				
				case 'Date':
					
					echo <<<EOD
					
						<div class="property">
						<label for="$field_name">$name: </label>
						<div class="description">$description</div>
						<input name="$field_name" value="$field_value" type="text" />
						</div>
						
						<script type="text/javascript">
							jQuery(function($) {
								$( 'input[name=$field_name]' ).datepicker();
							});
						</script>

EOD;
					
					break;
											
				default:
					
					if (false == SchemaOrgFramework::isSchemaSupported($type)) {
					
						echo '<div class="property">';
						echo 'Type &quot;'.$type.'&quot; not supported for field &quot;'.$name.'&quot;.';
						echo '</div>';
						
						return;
					}

					echo <<<EOD
			
						<div class="property">
						<label for="$field_name">$name: </label>
						<div class="description">$description</div>
						<input name="$field_name" value="$field_value" type="text" />
						<select id="$field_name" name="$field_name" multiple="multiple" size="5">
						</select>
						<a href="javascript:jQuery('#$field_name option:selected').remove();">remove</a>
						</div>
					
						<script type="text/javascript">
							jQuery(function($) {
							
								$('input[name=$field_name]').autocomplete({
									source: function( request, response ) {
										$.ajax({
											url: 'admin-ajax.php?action=schema-org.posts',
											// '/wordlift/wp-content/plugins/wordlift/lib/externals/SchemaOrgFramework/api/http.php'
											dataType: "jsonp",
											data: {
												schema: '$type',
												name: request.term
											},
											success: function( data ) {
												response( $.map( data, function( item ) {
													return {
														label: item.name + ' (' + unescape(item['@type']) + ')',
														value: item.url
													}
												}));
											}
										});
									},
									minLength: 2,
									select: function( event, ui ) {
										if (0 === $('#$field_name option[value="' + ui.item.value + '"]').length) {
											$('#$field_name').append('<option value="' + ui.item.value + '">' + ui.item.label + '</option>');
										}

										event.target.value = '';
										return false; 
									},
									open: function() {
										$( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
									},
									close: function() {
										$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
									}
								});
							});
						</script>
					
EOD;
			
					
			}
		}
		
	}
	
}

?>