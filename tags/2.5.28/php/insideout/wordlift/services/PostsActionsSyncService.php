<?php

class WordLift_Services_PostsActionsSyncService
{

    public $logger;
    public $queryService;

    public function postDeleted($postId)
    {
        $this->logger->trace("The post [ postId :: $postId ] has been deleted.");
    } 

    public function postSaved($postId)
    {
        if (wp_is_post_revision($postId)) {
            return;
        }

        $post = get_post($postId);
        $status = &$post->post_status;
        $modified = &$post->post_modified_gmt;
        $this->logger->trace("The post [ postId :: $postId ][ status :: $status ][ modified :: $modified ] has been saved.");

        $data = array(
            "urn:wordpress:$postId" => array(
                "http://purl.org/dc/terms/accessRights" => array(
                    array(
                        WordLift_QueryService::VALUE_NAME => "?rights"
                    )
                ),
                "http://purl.org/dc/terms/modified" => array(
                    array(
                        WordLift_QueryService::VALUE_NAME => "?modified"
                    )
                )
            )
        );

        $graphURI = $this->queryService->defaultGraphURI;
        
        $this->queryService->query(
            $this->getQueryDelete($graphURI, $postId)
        );

        $this->logger->trace(
            $this->getQueryInsert($graphURI, $postId, $status, $modified)
        );
        $this->queryService->query(
            $this->getQueryInsert($graphURI, $postId, $status, $modified)
        );
    }

    private function getQueryDelete($graphURI, $postId)
    {

        return <<<EOF

        DELETE FROM <$graphURI> {
            <urn:wordpress:$postId> a ?type . 
            <urn:wordpress:$postId> <http://purl.org/dc/terms/accessRights> ?rights . 
            <urn:wordpress:$postId> <http://purl.org/dc/terms/modified> ?modified . 
        }
EOF;

    }

    private function getQueryInsert($graphURI, $postId, $status, $modified)
    {

        return <<<EOF

        INSERT INTO <$graphURI> {
            <urn:wordpress:$postId> a <http://http://purl.org/dc/dcmitype/Text> .
            <urn:wordpress:$postId> <http://purl.org/dc/terms/accessRights> "$status" . 
            <urn:wordpress:$postId> <http://purl.org/dc/terms/modified> "$modified" . 
        }
EOF;

    } 

}

?>