<?xml version="1.0" encoding="utf-8"?>
<?php
/**
 * This end-point is called to retrieve a GeoRSS version of the entities that have a latitude/longitude.  
 */
 
require_once('../wordlift.php');

$logger 	= Logger::getLogger(__FILE__);
$url = get_permalink( get_page_by_path(WORDLIFT_20_ENTITIES_MAP_PAGE_NAME) );

?>
<feed xmlns="http://www.w3.org/2005/Atom" 
  xmlns:georss="http://www.georss.org/georss">
  <title>Entities</title>
  <subtitle>Geo-located Entities</subtitle>
  <link href="<?php echo $url ?>"/>
  <updated>2005-12-13T18:30:02Z</updated>
  <author>
    <name></name>
    <email></email>
  </author>
  <id><?php echo url ?></id>
<?php

	$entities = $entity_service->get_all_accepted_entities();
	
	foreach ($entities as $entity) {
		$latitude = $entity->properties['geo-latitude'][0];
		$longitude = $entity->properties['geo-longitude'][0];
		
		if (NULL == $latitude || NULL == $longitude)
			continue;
		
		$title = htmlspecialchars($entity->text, ENT_QUOTES | ENT_XML1, 'UTF-8' );
		$url = get_permalink($entity->post_id);
		$summary = htmlspecialchars( substr( $entity->properties['description'][0], 0, 128), ENT_QUOTES | ENT_XML1, 'UTF-8' );
		
		$latitude = $entity->properties['geo-latitude'][0];
		$longitude = $entity->properties['geo-longitude'][0];
		
		echo '<entry>';
		echo '<title>'.$title.'</title>';
		echo '<link href="'.$url.'"/>';
		echo '<id>'.$url.'</id>';
// 		echo '<updated>2005-08-17T07:02:32Z</updated>';
		if ($summary) echo '<summary>'.$summary.'</summary>';
		echo '<georss:point>'.$latitude.' '.$longitude.'</georss:point>';
		echo '</entry>';
	}
	
?></feed>