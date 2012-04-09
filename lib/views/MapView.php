<?php

/**
 * Displays a fragment with a MapView with the specified parameters.
 */
class MapView implements IView {

	private $latitude;
	private $longitude;
	private $map_element_id;
	private $zoom_level;
	
	/**
	 * Creates an instance of the MapView with the specified parameters.
	 * @param float $latitude
	 * @param float $longitude
	 * @param string $map_element_id
	 * @param integer $zoom_level
	 */
	function __construct($latitude,$longitude,$map_element_id,$zoom_level = 10) {
		$this->latitude = $latitude;
		$this->longitude = $longitude;
		$this->map_element_id = $map_element_id;
		$this->zoom_level = $zoom_level;
	} 
	
	/**
	 * Renders the content for a MapView.
	 * @param string $content
	 */
	public function getContent($content=null) {
		$latitude = htmlentities($this->latitude);
		$longitude = htmlentities($this->longitude);
		$map_element_id = htmlentities($this->map_element_id);
		$zoom_level = htmlentities($this->zoom_level);

		return <<<EOD

				<script type="text/javascript">
					jQuery(window).ready( function($){
					
					var longitude = '$longitude';
					var latitude  = '$latitude';
					
						var map = new OpenLayers.Map('$map_element_id');
				        var mapnik = new OpenLayers.Layer.OSM();
				        map.addLayer(mapnik);
				        map.setCenter(new OpenLayers.LonLat( longitude , latitude ) // Center of the map
				          .transform(
				            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
				            new OpenLayers.Projection("EPSG:900913") // to Spherical Mercator Projection
				          ), $zoom_level // Zoom level
				        );
				    });
				</script>
EOD;

	}
	
	/**
	 * Prints out the rendering of this view.
	 */
	public function display() {
		echo $this->getContent();
	}
}

?>