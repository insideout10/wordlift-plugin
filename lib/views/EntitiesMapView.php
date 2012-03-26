<?php

class EntitiesMapView {
	
	private $entities;
	
	function __construct(&$entities) {
		$this->entities = $entities;
	}
	
	public function display() {

?>

		<div class="entities-map-view">
			<div id="entities-map"></div>
		</div>
<?php

		$latitude = 0;
		$longitude = 0;
		$map_element_id = 'entities-map';
		$zoom_level = 1;

		$this->display_script($latitude,$longitude,$map_element_id,$zoom_level);

	}

	private function display_script($latitude,$longitude,$map_element_id,$zoom_level = 10) {
		$latitude = htmlentities($latitude);
		$longitude = htmlentities($longitude);
		$map_element_id = htmlentities($map_element_id);
		$zoom_level = htmlentities($zoom_level);
		
		$icon_url = plugins_url('/images/1332499409_Internet.png', WORDLIFT_20_ROOT_PATH);
		$icon_url_width = 24;
		$icon_url_height = 24;
		$geo_rss_url = plugins_url('/api/georss.php', WORDLIFT_20_ROOT_PATH);
		?>
			<script type="text/javascript">
				jQuery(window).ready( function($){
				
	<?php			echo 'var longitude = \''.$longitude.'\';';  ?>
	<?php			echo 'var latitude  = \''.$latitude.'\';'; 	?>
				
					var map = new OpenLayers.Map("<?php echo $map_element_id ?>");
			        var mapnik = new OpenLayers.Layer.OSM();
			        map.addLayer(mapnik);
			        var icon = new OpenLayers.Icon('<?php echo $icon_url ?>', new OpenLayers.Size(<?php echo $icon_url_width.','.$icon_url_height ?>));
			        var geoRss = new OpenLayers.Layer.GeoRSS( 'GeoRSS', '<?php echo $geo_rss_url ?>', {'icon':icon});
		            map.addLayer(geoRss);
			        map.setCenter(new OpenLayers.LonLat( longitude , latitude ) // Center of the map
			          .transform(
			            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
			            new OpenLayers.Projection("EPSG:900913") // to Spherical Mercator Projection
			          ), <?php echo $zoom_level ?>// Zoom level
			        );
			    });
			</script>
	<?php
		}
	
}

?>