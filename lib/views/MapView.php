<?php

class MapView {

	public function display($latitude,$longitude,$map_element_id,$zoom_level = 10) {
		$latitude = htmlentities($latitude);
		$longitude = htmlentities($longitude);
		$map_element_id = htmlentities($map_element_id);
		$zoom_level = htmlentities($zoom_level);
?>
		<script type="text/javascript">
			jQuery(window).ready( function($){
			
<?php			echo 'var longitude = \''.$longitude.'\';';  ?>
<?php			echo 'var latitude  = \''.$latitude.'\';'; 	?>
			
				var map = new OpenLayers.Map("<?php echo $map_element_id ?>");
		        var mapnik = new OpenLayers.Layer.OSM();
		        map.addLayer(mapnik);
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

$map_view = new MapView();
?>