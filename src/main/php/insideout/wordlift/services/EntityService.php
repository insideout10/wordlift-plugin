<?php

/**
 * This class provide read/write access to the entities in this blog. 
 */
class WordLift_EntityService {

	// The logger instance.
	public $logger;

    public $dataStore;
    public $metaKey;
    public $postType;
    public $postStatus;

    public function getPosts( $postID ) {
        $this->logger->trace( "Getting entities for post ID [$postID]." );

        $posts = get_posts( array(
            "numberposts" => -1,
            "offset" => 0,
            "meta_key" => $this->metaKey,
            "meta_value" => $postID,
            "post_type" => $this->postType,
            "post_status" => $this->postStatus
        ));

        $this->logger->trace( "Found " . count($posts) . " entity post(s) for post ID [$postID]." );

        return $posts;
    }

    public function getEntities( $postID ) {

        if (NULL === $this->dataStore)
            throw new Exception( "The data-store hasn't been set. Check your configuration." );

        $posts = $this->getPosts( $postID );

        $entities = array();
        foreach ($posts as &$post) {
            $this->logger->trace( "Loading Entity from Entity Post ID [$post->ID]." );

            array_push( $entities,
                new SchemaOrg_Entity(
                    $post->ID,
                    NULL,
                    $this->dataStore)
            );

        }

        return $entities;
    }
}

?>