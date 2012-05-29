<?php

/**
 * This class displays a geomap view of all the entities of the blog.
 */
class EntitiesGeomapView implements IShortCode {
	
	// the short-code for this view.
	const SHORT_CODE = 'entities.geomap';
	
	// the entities to display.
	private $entities;
	
	/**
	 * Creates a new instance of the class by passing the entities to display.
	 * @param unknown_type $entities
	 */
	function __construct(&$entities) {
		$this->entities = $entities;
	}
	
	/********************************************************************************
	 * IShortCode implementation													*
	 ********************************************************************************/
	
	public static function getShortCode() {
		return self::SHORT_CODE;
	}
	
	public static function doShortCode($atts, $content=null, $tag=null) {
		$entity_service = new EntityService();
		$entities = $entity_service->get_all_accepted_entities();

		$self = new self($entities);
		return $self->getContent();
	}
	
	/********************************************************************************/
	
	/**
	 * Renders the entities in a Map View and returns the rendered code.
	 * @param string $content
	 */
	public function getContent($content='') {
		
		$latitude = 0;
		$longitude = 0;
		$map_element_id = 'entities-map';
		$zoom_level = 1;
		
		return <<<EOD
		
		<div class="entities-map-view">
		<div id="$map_element_id"></div>
		</div>
		
		{$this->getScript($latitude,$longitude,$map_element_id,$zoom_level)}
		
EOD;
		
	}
	
	public function display() {
		echo $this->getContent();
	}

	private function getScript($latitude,$longitude,$map_element_id,$zoom_level = 10) {
		$latitude = htmlentities($latitude);
		$longitude = htmlentities($longitude);
		$map_element_id = htmlentities($map_element_id);
		$zoom_level = htmlentities($zoom_level);
	
		$icon_url = plugins_url('/images/1332499409_Internet.png', WORDLIFT_20_ROOT_PATH);
		$icon_url_width = 24;
		$icon_url_height = 24;
		$geo_rss_url = WORDLIFT_20_GEORSS;


		return <<<EOD
		
					<script type="text/javascript">
					jQuery(window).ready( function($){
					
						var longitude = '$longitude';
						var latitude  = '$latitude';
					
						var map = new OpenLayers.Map('$map_element_id');
				        var mapnik = new OpenLayers.Layer.OSM();
				        map.addLayer(mapnik);
				        var icon = new OpenLayers.Icon('$icon_url', new OpenLayers.Size($icon_url_width,$icon_url_height));
				        var geoRss = new OpenLayers.Layer.GeoRSS( 'GeoRSS', '$geo_rss_url', {'icon':icon});
			            map.addLayer(geoRss);
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

}
?>