<?php

/**
 * @requires WordPressFramework
 */
class EntitiesServices {

	private $logger;

	function __construct() {
		$this->logger 		= Logger::getLogger(__CLASS__);
	}

    /**
     * @service ajax
     * @action wordlift.entities
     * @authentication none
     */
    public function entities( $id = null, $limit = -1, $offset = 0 ) {

        global $entity_service, $job_service;
        
        // return all the entities.
        if (null === $id) {

        	$entities 	= $entity_service->get_all($limit, $offset);
        	$entities_count = $entity_service->get_count();
        	return array('total' => $entities_count, 'entities' => $entities);
        }

        if (false === is_numeric($id)) {
        	$logger->warn('The entities.php end-point has been called with an invalid id [id:' . $id . '].');
        	return AjaxService::CALLBACK_RETURN_ERROR;
        }


        // return the entities for a specific post ID.
        $job 		= $job_service->get_job_by_post_id($id);
        $entities 	= $entity_service->get_entities_by_post_id($id);

        return array( 'job' => $job, 'entities' => $entities);
        
    }
    
    /**
     * @service ajax
     * @action wordlift.accept-entity
     * @authentication edit_posts
     */
    public function acceptEntity($post_id, $entity_id) {
        global $entity_service;

        if (false === is_numeric($post_id) || false === is_numeric($entity_id)) {
        	$this->logger->warn('The accept.php end-point has been called with an invalid post_id or entity_id [post_id:'.$post_id.'][entity_id:'.$entity_id.']');

        	header("HTTP/1.0 400 Bad Request");
        	return AjaxService::CALLBACK_RETURN_ERROR;
        }

        $entity_service->accept_entity_for_post($entity_id, $post_id);
    }
    
    /**
     * @service ajax
     * @action wordlift.reject-entity
     * @authentication edit_posts
     */
    public function rejectEntity($post_id, $entity_id) {
        global $entity_service;
        
        if (false == is_numeric($post_id) || false == is_numeric($entity_id)) {
        	$logger->warn('The '.__FILE__.' end-point has been called with an invalid post_id or entity_id [post_id:'.$post_id.'][entity_id:'.$entity_id.']');

        	return;
        }

        $entity_service->reject_entity_for_post($entity_id, $post_id);
    }

    /**
     * @service ajax
     * @action wordlift.georss
     */
    public function geoRss() {
        global $entity_service;
        
        echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";

        $url = get_permalink( get_page_by_path(WORDLIFT_20_ENTITIES_MAP_PAGE_NAME) );

echo <<<EOD
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:georss="http://www.georss.org/georss">
    <title>Entities</title>
    <subtitle>Geo-located Entities</subtitle>
    <link href="$url"/>
    <updated>2005-12-13T18:30:02Z</updated>
    <author>
    <name></name>
    <email></email>
    </author>
    <id>$url</id>
EOD;

        $entities = $entity_service->get_all_accepted_entities();

        foreach ($entities as $entity) {
        	$latitude = $entity->properties['latitude'][0];
        	$longitude = $entity->properties['longitude'][0];

            if (NULL == $latitude || NULL == $longitude)
             continue;

        	$title = htmlspecialchars($entity->text, ENT_QUOTES | ENT_XML1, 'UTF-8' );
        	$url = get_permalink($entity->post_id);
        	$summary = htmlspecialchars( substr( $entity->properties['description'][0], 0, 128), ENT_QUOTES | ENT_XML1, 'UTF-8' );

echo <<<EOD
    <entry>
        <title>$title</title>
        <link href="$url"/>
        <id>$url</id>
        <summary>$summary</summary>
        <georss:point>$latitude $longitude</georss:point>
    </entry>
EOD;
        }

        echo "</feed>\n";

        return AjaxService::CALLBACK_RETURN_NULL;
    }

}

?>