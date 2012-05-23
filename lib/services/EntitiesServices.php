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

}

?>