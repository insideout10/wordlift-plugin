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
                        WordLift_QueryService::VALUE_NAME => $status
                    )
                ),
                "http://purl.org/dc/terms/modified" => array(
                    array(
                        WordLift_QueryService::VALUE_NAME => $modified
                    )
                )
            )
        );

        $query = $this->queryService->createStatement($data, WordLift_QueryService::INSERT_COMMAND);
        $this->logger->trace($query);
    } 

}

?>